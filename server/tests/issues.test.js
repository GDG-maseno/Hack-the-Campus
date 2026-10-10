import { test } from 'node:test';
import assert from 'node:assert/strict';
import { makeApp, req } from './helpers/index.js';

function seedIssues(db) {
  const now = '2025-01-15T10:00:00Z';
  const rows = [
    { title: 'Critical water', category: 'water', urgency: 'critical', status: 'open', upvotes: 5, location: 'Hostel B' },
    { title: 'Low ict', category: 'ict', urgency: 'low', status: 'open', upvotes: 1, location: 'Lab 5' },
    { title: 'High security', category: 'security', urgency: 'high', status: 'open', upvotes: 3, location: 'Library' },
    { title: 'Resolved roads', category: 'roads', urgency: 'medium', status: 'resolved', upvotes: 2, location: 'Main Gate' },
    { title: 'Medium sanitation', category: 'sanitation', urgency: 'medium', status: 'in_progress', upvotes: 4, location: 'Cafeteria' },
  ];
  const stmt = db.prepare(`
    INSERT INTO issues (title, summary, original_text, location, category, urgency, department, language, confidence, status, upvotes, photo, ai_source, created_at, updated_at)
    VALUES (@title, 'Summary.', '', @location, @category, @urgency, 'Estates & Maintenance (General)', 'English', 'high', @status, @upvotes, null, 'seed', '${now}', '${now}')
  `);
  for (const r of rows) stmt.run(r);
}

test('GET /api/issues returns list', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const res = await req(app, 'GET', '/api/issues');
  assert.equal(res.status, 200);
  assert.ok(Array.isArray(res.body));
  assert.equal(res.body.length, 5);
});

test('priority sort: critical before low, resolved last', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const res = await req(app, 'GET', '/api/issues?sort=priority');
  assert.equal(res.status, 200);
  const titles = res.body.map(i => i.title);
  assert.equal(titles[0], 'Critical water');
  assert.equal(titles[titles.length - 1], 'Resolved roads');
});

test('filter by category', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const res = await req(app, 'GET', '/api/issues?category=water');
  assert.equal(res.status, 200);
  assert.equal(res.body.length, 1);
  assert.equal(res.body[0].category, 'water');
});

test('filter by status', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const res = await req(app, 'GET', '/api/issues?status=resolved');
  assert.equal(res.status, 200);
  assert.equal(res.body.length, 1);
  assert.equal(res.body[0].status, 'resolved');
});

test('sort=new returns newest first', async () => {
  const { app, db } = makeApp();
  const t1 = '2025-01-14T08:00:00Z';
  const t2 = '2025-01-15T09:00:00Z';
  db.prepare(`INSERT INTO issues (title,summary,original_text,location,category,urgency,department,language,confidence,status,upvotes,photo,ai_source,created_at,updated_at) VALUES ('Old','S','','L','other','low','Estates & Maintenance (General)','English','low','open',1,null,'seed',?,?)`).run(t1, t1);
  db.prepare(`INSERT INTO issues (title,summary,original_text,location,category,urgency,department,language,confidence,status,upvotes,photo,ai_source,created_at,updated_at) VALUES ('New','S','','L','other','low','Estates & Maintenance (General)','English','low','open',1,null,'seed',?,?)`).run(t2, t2);
  const res = await req(app, 'GET', '/api/issues?sort=new');
  assert.equal(res.body[0].title, 'New');
});

test('each issue has photo_url and voted fields', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const res = await req(app, 'GET', '/api/issues', { headers: { 'x-voter-id': 'v1' } });
  assert.ok('photo_url' in res.body[0]);
  assert.ok('voted' in res.body[0]);
});

test('upvote toggle: vote then unvote', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const issueId = db.prepare('SELECT id FROM issues LIMIT 1').get().id;

  const r1 = await req(app, 'POST', `/api/issues/${issueId}/upvote`, {
    headers: { 'x-voter-id': 'voter-toggle' },
  });
  assert.equal(r1.status, 200);
  assert.equal(r1.body.voted, true);
  const upvotesAfterVote = r1.body.upvotes;

  const r2 = await req(app, 'POST', `/api/issues/${issueId}/upvote`, {
    headers: { 'x-voter-id': 'voter-toggle' },
  });
  assert.equal(r2.status, 200);
  assert.equal(r2.body.voted, false);
  assert.equal(r2.body.upvotes, upvotesAfterVote - 1);
});

test('upvote without voter id → 400', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const issueId = db.prepare('SELECT id FROM issues LIMIT 1').get().id;
  const res = await req(app, 'POST', `/api/issues/${issueId}/upvote`);
  assert.equal(res.status, 400);
});

test('voted flag reflects voter state', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const issueId = db.prepare('SELECT id FROM issues LIMIT 1').get().id;

  await req(app, 'POST', `/api/issues/${issueId}/upvote`, {
    headers: { 'x-voter-id': 'voter-flag' },
  });

  const res = await req(app, 'GET', '/api/issues', { headers: { 'x-voter-id': 'voter-flag' } });
  const issue = res.body.find(i => i.id === issueId);
  assert.equal(issue.voted, true);
});

test('GET /api/stats returns correct counts', async () => {
  const { app, db } = makeApp();
  seedIssues(db);
  const res = await req(app, 'GET', '/api/stats');
  assert.equal(res.status, 200);
  assert.equal(res.body.open, 3);
  assert.equal(res.body.in_progress, 1);
  assert.ok(typeof res.body.resolved_week === 'number');
  assert.ok(typeof res.body.new_today === 'number');
});
