import { test } from 'node:test';
import assert from 'node:assert/strict';
import { makeApp, req } from './helpers/index.js';
import { openDb } from '../db.js';
import os from 'node:os';

function seedIssue(db) {
  const now = '2025-01-15T10:00:00Z';
  const info = db.prepare(`
    INSERT INTO issues (title, summary, original_text, location, category, urgency, department, language, confidence, status, upvotes, photo, ai_source, created_at, updated_at)
    VALUES ('Existing pipe issue', 'Pipe leaking at Hostel B.', 'maji inatoka', 'Hostel B', 'water', 'high',
            'Estates & Maintenance (Water & Plumbing)', 'Swahili', 'high', 'open', 3, null, 'seed', ?, ?)
  `).run(now, now);
  return Number(info.lastInsertRowid);
}

function makeClientWithDuplicate(duplicateId) {
  return {
    messages: {
      create: async () => ({
        content: [{
          type: 'tool_use', name: 'log_campus_issue',
          input: {
            is_valid_report: true, title: 'Duplicate issue', summary: 'Same problem.',
            category: 'water', urgency: 'high', language: 'English',
            duplicate_of: duplicateId, confidence: 'high', ack_message: 'Already reported.',
          },
        }],
      }),
    },
  };
}

test('duplicate merge: upvotes incremented, returns 200 duplicate:true', async () => {
  const db = openDb(':memory:');
  const existingId = seedIssue(db);
  const { app } = makeApp({
    db,
    triageBackend: 'api',
    anthropicClient: makeClientWithDuplicate(existingId),
    uploadsDir: os.tmpdir(),
  });

  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'maji inatoka mob sana bomba imepasuka' },
    headers: { 'x-voter-id': 'voter-dup-1' },
  });
  assert.equal(res.status, 200);
  assert.equal(res.body.duplicate, true);
  assert.equal(res.body.issue.upvotes, 4);
});

test('one count per voter: second report from same voter does not double-count', async () => {
  const db = openDb(':memory:');
  const existingId = seedIssue(db);
  const { app } = makeApp({
    db,
    triageBackend: 'api',
    anthropicClient: makeClientWithDuplicate(existingId),
    uploadsDir: os.tmpdir(),
  });

  await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'maji inatoka mob sana bomba imepasuka' },
    headers: { 'x-voter-id': 'voter-same' },
  });

  const res2 = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'maji inatoka mob sana bomba imepasuka' },
    headers: { 'x-voter-id': 'voter-same' },
  });
  assert.equal(res2.status, 200);
  // upvotes: 3 seed + 1 first vote = 4; second vote from same voter ignored
  assert.equal(res2.body.issue.upvotes, 4);
});

test('candidates are unresolved + same location only', async () => {
  const db = openDb(':memory:');
  const now = '2025-01-15T10:00:00Z';
  // Resolved issue at same location — should NOT be a candidate
  db.prepare(`
    INSERT INTO issues (title, summary, original_text, location, category, urgency, department, language, confidence, status, upvotes, photo, ai_source, created_at, updated_at, resolved_at)
    VALUES ('Resolved issue', 'Was fixed.', '', 'Hostel B', 'water', 'low',
            'Estates & Maintenance (Water & Plumbing)', 'English', 'high', 'resolved', 1, null, 'seed', ?, ?, ?)
  `).run(now, now, now);
  // Open issue at different location — should NOT be a candidate
  db.prepare(`
    INSERT INTO issues (title, summary, original_text, location, category, urgency, department, language, confidence, status, upvotes, photo, ai_source, created_at, updated_at)
    VALUES ('Other location issue', 'Different place.', '', 'Library', 'water', 'medium',
            'Estates & Maintenance (Water & Plumbing)', 'English', 'high', 'open', 1, null, 'seed', ?, ?)
  `).run(now, now);

  let capturedSystem = null;
  const capturingClient = {
    messages: {
      create: async (opts) => {
        capturedSystem = opts.system;
        return {
          content: [{
            type: 'tool_use', name: 'log_campus_issue',
            input: {
              is_valid_report: true, title: 'New issue', summary: 'New.',
              category: 'water', urgency: 'medium', language: 'English',
              duplicate_of: null, confidence: 'medium', ack_message: 'Thanks.',
            },
          }],
        };
      },
    },
  };

  const { app } = makeApp({ db, triageBackend: 'api', anthropicClient: capturingClient, uploadsDir: os.tmpdir() });
  await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside' },
  });

  assert.ok(!capturedSystem.includes('Resolved issue'), 'resolved issues should not be candidates');
  assert.ok(!capturedSystem.includes('Other location issue'), 'different location should not be candidate');
});

test('hallucinated duplicate id ignored → new issue created', async () => {
  const db = openDb(':memory:');
  const { app } = makeApp({
    db,
    triageBackend: 'api',
    anthropicClient: makeClientWithDuplicate(99999),
    uploadsDir: os.tmpdir(),
  });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside badly' },
    headers: { 'x-voter-id': 'voter-halluc' },
  });
  assert.equal(res.status, 201);
  assert.equal(res.body.duplicate, false);
});
