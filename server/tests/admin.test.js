import { test } from 'node:test';
import assert from 'node:assert/strict';
import { makeApp, req } from './helpers/index.js';

function seedOne(db) {
  const now = '2025-01-15T10:00:00Z';
  const info = db.prepare(`
    INSERT INTO issues (title, summary, original_text, location, category, urgency, department, language, confidence, status, upvotes, photo, ai_source, created_at, updated_at)
    VALUES ('Test issue', 'Summary.', '', 'Hostel B', 'water', 'high', 'Estates & Maintenance (Water & Plumbing)', 'English', 'high', 'open', 1, null, 'seed', ?, ?)
  `).run(now, now);
  return info.lastInsertRowid;
}

test('POST /api/admin/check with correct key → 200', async () => {
  const { app } = makeApp({ adminKey: 'secret-key' });
  const res = await req(app, 'POST', '/api/admin/check', {
    headers: { 'x-admin-key': 'secret-key' },
  });
  assert.equal(res.status, 200);
  assert.equal(res.body.ok, true);
});

test('POST /api/admin/check with wrong key → 401', async () => {
  const { app } = makeApp({ adminKey: 'secret-key' });
  const res = await req(app, 'POST', '/api/admin/check', {
    headers: { 'x-admin-key': 'wrong-key' },
  });
  assert.equal(res.status, 401);
});

test('PATCH /api/issues/:id/status without admin key → 401', async () => {
  const { app, db } = makeApp();
  const id = seedOne(db);
  const res = await req(app, 'PATCH', `/api/issues/${id}/status`, {
    body: { status: 'in_progress' },
  });
  assert.equal(res.status, 401);
});

test('PATCH /api/issues/:id/status with admin key → 200', async () => {
  const { app, db } = makeApp({ adminKey: 'admin-test' });
  const id = seedOne(db);
  const res = await req(app, 'PATCH', `/api/issues/${id}/status`, {
    body: { status: 'in_progress' },
    headers: { 'x-admin-key': 'admin-test' },
  });
  assert.equal(res.status, 200);
  assert.equal(res.body.status, 'in_progress');
});

test('resolved_at set when status → resolved', async () => {
  const { app, db } = makeApp({ adminKey: 'admin-test' });
  const id = seedOne(db);
  await req(app, 'PATCH', `/api/issues/${id}/status`, {
    body: { status: 'resolved' },
    headers: { 'x-admin-key': 'admin-test' },
  });
  const issue = db.prepare('SELECT resolved_at FROM issues WHERE id = ?').get(id);
  assert.ok(issue.resolved_at, 'resolved_at should be set');
});

test('resolved_at cleared when status → open', async () => {
  const { app, db } = makeApp({ adminKey: 'admin-test' });
  const id = seedOne(db);
  // First resolve
  await req(app, 'PATCH', `/api/issues/${id}/status`, {
    body: { status: 'resolved' },
    headers: { 'x-admin-key': 'admin-test' },
  });
  // Then reopen
  await req(app, 'PATCH', `/api/issues/${id}/status`, {
    body: { status: 'open' },
    headers: { 'x-admin-key': 'admin-test' },
  });
  const issue = db.prepare('SELECT resolved_at FROM issues WHERE id = ?').get(id);
  assert.equal(issue.resolved_at, null);
});

test('PATCH invalid status → 400', async () => {
  const { app, db } = makeApp({ adminKey: 'admin-test' });
  const id = seedOne(db);
  const res = await req(app, 'PATCH', `/api/issues/${id}/status`, {
    body: { status: 'flying' },
    headers: { 'x-admin-key': 'admin-test' },
  });
  assert.equal(res.status, 400);
});

test('GET /api/insights without admin key → 401', async () => {
  const { app } = makeApp();
  const res = await req(app, 'GET', '/api/insights');
  assert.equal(res.status, 401);
});

test('GET /api/insights with admin key → 200 with briefing', async () => {
  const { app, db } = makeApp({ adminKey: 'admin-test' });
  seedOne(db);
  const res = await req(app, 'GET', '/api/insights', {
    headers: { 'x-admin-key': 'admin-test' },
  });
  assert.equal(res.status, 200);
  assert.ok(typeof res.body.briefing === 'string');
  assert.ok(res.body.briefing.length > 0);
  assert.ok(typeof res.body.open === 'number');
});
