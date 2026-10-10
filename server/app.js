import express from 'express';
import multer from 'multer';
import path from 'node:path';
import fs from 'node:fs';
import fsp from 'node:fs/promises';
import { randomUUID } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import { run as triageRun, insightsNarrative } from './triage.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// ── Departments map ──────────────────────────────────────────────────────────
/* prettier-ignore */
const DEPARTMENTS = {
  water:      'Estates & Maintenance (Water & Plumbing)',
  electrical: 'Estates & Maintenance (Electrical)',
  ict:        'ICT Services',
  security:   'Security Services',
  roads:      'Estates & Maintenance (Roads & Grounds)',
  sanitation: 'Cleaning & Sanitation Services',
  other:      'Estates & Maintenance (General)',
};

const URGENCY_ORDER = { critical: 4, high: 3, medium: 2, low: 1 };

// ── Magic bytes image detection ──────────────────────────────────────────────
function detectImageType(buf) {
  if (!buf || buf.length < 4) return null;
  if (buf[0] === 0xFF && buf[1] === 0xD8 && buf[2] === 0xFF) return 'image/jpeg';
  if (buf[0] === 0x89 && buf[1] === 0x50 && buf[2] === 0x4E && buf[3] === 0x47) return 'image/png';
  if (buf[0] === 0x52 && buf[1] === 0x49 && buf[2] === 0x46 && buf[3] === 0x46) return 'image/webp';
  return null;
}

// ── Rate limiter (in-memory, per IP) ────────────────────────────────────────
function makeRateLimiter(maxPerMin = 8) {
  const map = new Map();
  return (req, res, next) => {
    const ip = req.ip;
    const now = Date.now();
    const entry = map.get(ip) ?? { count: 0, reset: now + 60_000 };
    if (now > entry.reset) { entry.count = 0; entry.reset = now + 60_000; }
    entry.count++;
    map.set(ip, entry);
    if (entry.count > maxPerMin) return res.status(429).json({ error: 'Too many requests. Please wait a minute.' });
    next();
  };
}

// ── Insights cache ───────────────────────────────────────────────────────────
let insightsCache = null;
let insightsCacheAt = 0;
const INSIGHTS_TTL = 90_000;

export function clearInsightsCache() { insightsCacheAt = 0; }

// ── App factory ──────────────────────────────────────────────────────────────
export function createApp({
  db,
  uploadsDir,
  adminKey,
  triageBackend = 'fallback',
  anthropicClient = null,
  model = 'claude-sonnet-5-5',
  spawner = null,
  clock = () => new Date(),
} = {}) {
  const app = express();
  app.set('trust proxy', true);
  app.use(express.json());

  // ── Multer ────────────────────────────────────────────────────────────────
  const upload = multer({
    storage: multer.memoryStorage(),
    limits: { fileSize: 8 * 1024 * 1024 },
  });

  // ── Static uploads ────────────────────────────────────────────────────────
  app.use('/uploads', express.static(uploadsDir));

  // ── Health ────────────────────────────────────────────────────────────────
  app.get('/api/health', (req, res) => {
    res.json({
      ok: true,
      ai: triageBackend !== 'fallback',
      backend: triageBackend,
      model,
    });
  });

  // ── Admin check ───────────────────────────────────────────────────────────
  app.post('/api/admin/check', (req, res) => {
    if (req.headers['x-admin-key'] !== adminKey) return res.status(401).json({ error: 'Unauthorized' });
    res.json({ ok: true });
  });

  // ── Middleware: require admin ─────────────────────────────────────────────
  function requireAdmin(req, res, next) {
    if (req.headers['x-admin-key'] !== adminKey) return res.status(401).json({ error: 'Unauthorized' });
    next();
  }

  // ── POST /api/report ──────────────────────────────────────────────────────
  const rateLimiter = makeRateLimiter(8);

  app.post('/api/report', rateLimiter, upload.single('photo'), async (req, res) => {
    try {
      const location = (req.body.location ?? '').trim();
      const description = (req.body.description ?? '').trim();
      const voterId = req.headers['x-voter-id'] ?? null;

      // Validate
      if (!location) return res.status(400).json({ error: 'location is required' });
      if (location.length > 60) return res.status(400).json({ error: 'location must be 60 chars or less' });
      if (description.length > 1000) return res.status(400).json({ error: 'description must be 1000 chars or less' });

      let imageBuffer = null;
      let mediaType = null;

      if (req.file) {
        mediaType = detectImageType(req.file.buffer);
        if (!mediaType) return res.status(400).json({ error: 'Photo must be a JPEG, PNG, or WebP image' });
        imageBuffer = req.file.buffer;
      }

      if (!imageBuffer && description.length < 5) {
        return res.status(400).json({ error: 'Please provide a photo or at least 5 characters of description' });
      }

      // Load duplicate candidates (up to 15 unresolved at same location)
      const candidates = db.prepare(
        `SELECT id, title, summary, category FROM issues WHERE location = ? AND status != 'resolved' LIMIT 15`
      ).all(location);

      // Triage
      const { result, source } = await triageRun(
        description, location, imageBuffer, mediaType, candidates,
        { triageBackend, anthropicClient, model, spawner }
      );

      // Invalid report
      if (!result.is_valid_report) {
        return res.status(422).json({
          error: result.rejection_reason || 'This does not appear to be a valid campus issue report.',
          rejected: true,
        });
      }

      const now = clock();
      const nowIso = toIso(now);
      const department = DEPARTMENTS[result.category] ?? DEPARTMENTS.other;

      // Save photo to disk
      let photoFilename = null;
      if (imageBuffer) {
        const ext = mediaType === 'image/png' ? 'png' : mediaType === 'image/webp' ? 'webp' : 'jpg';
        photoFilename = `${randomUUID()}.${ext}`;
        await fsp.writeFile(path.join(uploadsDir, photoFilename), imageBuffer);
      }

      // Duplicate?
      if (result.duplicate_of) {
        const existing = db.prepare('SELECT * FROM issues WHERE id = ?').get(result.duplicate_of);
        if (existing) {
          // Add vote (INSERT OR IGNORE)
          let voted = false;
          if (voterId) {
            const info = db.prepare('INSERT OR IGNORE INTO votes (issue_id, voter_id) VALUES (?, ?)').run(existing.id, voterId);
            if (info.changes > 0) {
              db.prepare('UPDATE issues SET upvotes = upvotes + 1, updated_at = ? WHERE id = ?').run(nowIso, existing.id);
              voted = true;
            }
          }
          // Attach photo if existing has none
          if (photoFilename && !existing.photo) {
            db.prepare('UPDATE issues SET photo = ?, updated_at = ? WHERE id = ?').run(photoFilename, nowIso, existing.id);
          }
          const updated = db.prepare('SELECT * FROM issues WHERE id = ?').get(existing.id);
          return res.status(200).json({
            duplicate: true,
            ai: source,
            message: result.ack_message,
            issue: formatIssue(updated, voterId, db),
          });
        }
      }

      // Insert new issue
      const info = db.prepare(`
        INSERT INTO issues (title, summary, original_text, location, category, urgency, department, language, confidence, status, upvotes, photo, ai_source, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', 1, ?, ?, ?, ?)
      `).run(
        result.title, result.summary, description, location,
        result.category, result.urgency, department,
        result.language, result.confidence,
        photoFilename, source, nowIso, nowIso
      );

      const issueId = info.lastInsertRowid;

      // Record reporter's vote
      if (voterId) {
        db.prepare('INSERT OR IGNORE INTO votes (issue_id, voter_id) VALUES (?, ?)').run(issueId, voterId);
      }

      const issue = db.prepare('SELECT * FROM issues WHERE id = ?').get(issueId);
      return res.status(201).json({
        duplicate: false,
        ai: source,
        message: result.ack_message,
        issue: formatIssue(issue, voterId, db),
      });

    } catch (err) {
      console.error('[POST /api/report]', err);
      res.status(500).json({ error: 'Internal server error' });
    }
  });

  // ── GET /api/issues ───────────────────────────────────────────────────────
  app.get('/api/issues', (req, res) => {
    const { category = 'all', status = 'all', sort = 'priority' } = req.query;
    const voterId = req.headers['x-voter-id'] ?? null;

    let where = '1=1';
    const params = [];
    if (category !== 'all') { where += ' AND category = ?'; params.push(category); }
    if (status !== 'all')   { where += ' AND status = ?';   params.push(status); }

    let orderBy;
    if (sort === 'new') {
      orderBy = 'created_at DESC';
    } else {
      // priority: unresolved first, then urgency desc, then upvotes desc, then newest
      orderBy = `CASE status WHEN 'resolved' THEN 1 ELSE 0 END ASC,
        CASE urgency WHEN 'critical' THEN 4 WHEN 'high' THEN 3 WHEN 'medium' THEN 2 ELSE 1 END DESC,
        upvotes DESC, created_at DESC`;
    }

    const issues = db.prepare(`SELECT * FROM issues WHERE ${where} ORDER BY ${orderBy} LIMIT 200`).all(...params);
    res.json(issues.map(i => formatIssue(i, voterId, db)));
  });

  // ── GET /api/issues/:id ───────────────────────────────────────────────────
  app.get('/api/issues/:id', (req, res) => {
    const voterId = req.headers['x-voter-id'] ?? null;
    const issue = db.prepare('SELECT * FROM issues WHERE id = ?').get(req.params.id);
    if (!issue) return res.status(404).json({ error: 'Issue not found' });
    res.json(formatIssue(issue, voterId, db));
  });

  // ── GET /api/stats ────────────────────────────────────────────────────────
  app.get('/api/stats', (req, res) => {
    const now = clock();
    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
    const dayAgo  = new Date(now.getTime() - 24 * 60 * 60 * 1000);

    const open        = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE status = 'open'`).get().n;
    const in_progress = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE status = 'in_progress'`).get().n;
    const resolved_week = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE status = 'resolved' AND resolved_at >= ?`).get(toIso(weekAgo)).n;
    const new_today   = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE created_at >= ?`).get(toIso(dayAgo)).n;

    res.json({ open, in_progress, resolved_week, new_today });
  });

  // ── POST /api/issues/:id/upvote ───────────────────────────────────────────
  app.post('/api/issues/:id/upvote', (req, res) => {
    const voterId = req.headers['x-voter-id'];
    if (!voterId) return res.status(400).json({ error: 'X-Voter-Id header is required' });

    const issue = db.prepare('SELECT * FROM issues WHERE id = ?').get(req.params.id);
    if (!issue) return res.status(404).json({ error: 'Issue not found' });

    const existing = db.prepare('SELECT 1 FROM votes WHERE issue_id = ? AND voter_id = ?').get(issue.id, voterId);
    const now = toIso(clock());

    if (existing) {
      // Toggle off — remove vote, decrement (never below 0)
      db.prepare('DELETE FROM votes WHERE issue_id = ? AND voter_id = ?').run(issue.id, voterId);
      db.prepare('UPDATE issues SET upvotes = MAX(0, upvotes - 1), updated_at = ? WHERE id = ?').run(now, issue.id);
    } else {
      db.prepare('INSERT OR IGNORE INTO votes (issue_id, voter_id) VALUES (?, ?)').run(issue.id, voterId);
      db.prepare('UPDATE issues SET upvotes = upvotes + 1, updated_at = ? WHERE id = ?').run(now, issue.id);
    }

    const updated = db.prepare('SELECT upvotes FROM issues WHERE id = ?').get(issue.id);
    res.json({ upvotes: updated.upvotes, voted: !existing });
  });

  // ── PATCH /api/issues/:id/status ──────────────────────────────────────────
  app.patch('/api/issues/:id/status', requireAdmin, (req, res) => {
    const { status } = req.body;
    const VALID = ['open', 'in_progress', 'resolved'];
    if (!VALID.includes(status)) return res.status(400).json({ error: 'Invalid status' });

    const issue = db.prepare('SELECT * FROM issues WHERE id = ?').get(req.params.id);
    if (!issue) return res.status(404).json({ error: 'Issue not found' });

    const now = toIso(clock());
    const resolved_at = status === 'resolved' ? now : null;

    db.prepare('UPDATE issues SET status = ?, resolved_at = ?, updated_at = ? WHERE id = ?')
      .run(status, resolved_at, now, issue.id);

    clearInsightsCache();

    const updated = db.prepare('SELECT * FROM issues WHERE id = ?').get(issue.id);
    res.json(formatIssue(updated, null, db));
  });

  // ── GET /api/insights ─────────────────────────────────────────────────────
  app.get('/api/insights', requireAdmin, async (req, res) => {
    try {
      const now = clock();
      if (insightsCache && (now.getTime() - insightsCacheAt) < INSIGHTS_TTL) {
        return res.json(insightsCache);
      }

      const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

      const open        = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE status = 'open'`).get().n;
      const in_progress = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE status = 'in_progress'`).get().n;
      const resolved_week = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE status = 'resolved' AND resolved_at >= ?`).get(toIso(weekAgo)).n;
      const critical_open = db.prepare(`SELECT COUNT(*) as n FROM issues WHERE status != 'resolved' AND urgency = 'critical'`).get().n;

      // avg hours to resolve
      const resolved = db.prepare(`SELECT created_at, resolved_at FROM issues WHERE status = 'resolved' AND resolved_at IS NOT NULL`).all();
      let avg_hours_to_resolve = null;
      if (resolved.length > 0) {
        const totalMs = resolved.reduce((sum, r) => sum + (new Date(r.resolved_at) - new Date(r.created_at)), 0);
        avg_hours_to_resolve = totalMs / resolved.length / 3_600_000;
      }

      // Top 3 hotspot locations by unresolved count
      const hotspots = db.prepare(`
        SELECT location, COUNT(*) as count FROM issues WHERE status != 'resolved'
        GROUP BY location ORDER BY count DESC LIMIT 3
      `).all();

      // Unresolved by category
      const byCategory = db.prepare(`
        SELECT category, COUNT(*) as count FROM issues WHERE status != 'resolved'
        GROUP BY category ORDER BY count DESC
      `).all();

      const stats = { open, in_progress, resolved_week, avg_hours_to_resolve, hotspots, unresolved_by_category: byCategory, critical_open };

      const briefing = await insightsNarrative(stats, { triageBackend, anthropicClient, model, spawner });

      insightsCache = { ...stats, briefing };
      insightsCacheAt = now.getTime();

      res.json(insightsCache);
    } catch (err) {
      console.error('[GET /api/insights]', err);
      res.status(500).json({ error: 'Internal server error' });
    }
  });

  // ── Serve frontend or placeholder ─────────────────────────────────────────
  const webDist = path.join(__dirname, '..', 'web', 'dist');

  if (fs.existsSync(webDist) && fs.existsSync(path.join(webDist, 'index.html'))) {
    app.use(express.static(webDist));
    app.get(/^(?!\/api|\/uploads).*/, (req, res) => {
      res.sendFile(path.join(webDist, 'index.html'));
    });
  } else {
    app.get(/^(?!\/api|\/uploads).*/, (req, res) => {
      res.status(200).send(`<!DOCTYPE html><html><head><title>FixMaseno</title></head><body>
        <h1>FixMaseno</h1><p>The front end hasn't been built yet. Run <code>npm run build</code> first.</p>
        <p>API is running at <a href="/api/health">/api/health</a></p>
      </body></html>`);
    });
  }

  return app;
}

// ── Helpers ──────────────────────────────────────────────────────────────────

function toIso(d) {
  return d.toISOString().replace(/\.\d{3}Z$/, 'Z');
}

function formatIssue(issue, voterId, db) {
  const voted = voterId
    ? !!db.prepare('SELECT 1 FROM votes WHERE issue_id = ? AND voter_id = ?').get(issue.id, voterId)
    : false;
  return {
    ...issue,
    photo_url: issue.photo ? `/uploads/${issue.photo}` : null,
    voted,
  };
}
