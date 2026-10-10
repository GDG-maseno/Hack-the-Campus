import { test } from 'node:test';
import assert from 'node:assert/strict';
import { makeApp, req } from './helpers/index.js';

test('GET / returns 200 (frontend placeholder)', async () => {
  const { app } = makeApp();
  const res = await req(app, 'GET', '/');
  assert.equal(res.status, 200);
});

test('GET /api/health returns ok', async () => {
  const { app } = makeApp();
  const res = await req(app, 'GET', '/api/health');
  assert.equal(res.status, 200);
  assert.equal(res.body.ok, true);
  assert.ok('backend' in res.body);
  assert.ok('model' in res.body);
});

test('GET /api/health with api backend reports ai:true', async () => {
  const { app } = makeApp({ triageBackend: 'api' });
  const res = await req(app, 'GET', '/api/health');
  assert.equal(res.body.ai, true);
  assert.equal(res.body.backend, 'api');
});

test('GET /api/health with fallback backend reports ai:false', async () => {
  const { app } = makeApp({ triageBackend: 'fallback' });
  const res = await req(app, 'GET', '/api/health');
  assert.equal(res.body.ai, false);
});

test('GET /api/issues/:id not found → 404', async () => {
  const { app } = makeApp();
  const res = await req(app, 'GET', '/api/issues/99999');
  assert.equal(res.status, 404);
});

test('non-api path returns 200', async () => {
  const { app } = makeApp();
  const res = await req(app, 'GET', '/some/random/path');
  assert.equal(res.status, 200);
});
