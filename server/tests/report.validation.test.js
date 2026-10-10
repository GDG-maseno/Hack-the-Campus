import { test } from 'node:test';
import assert from 'node:assert/strict';
import { makeApp, req, FAKE_JPEG } from './helpers/index.js';
import os from 'node:os';

test('missing location → 400', async () => {
  const { app } = makeApp();
  const res = await req(app, 'POST', '/api/report', {
    multipart: { description: 'something broken here' },
  });
  assert.equal(res.status, 400);
  assert.ok(res.body.error);
});

test('nothing to triage (no photo, short text) → 400', async () => {
  const { app } = makeApp();
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'hi' },
  });
  assert.equal(res.status, 400);
  assert.ok(res.body.error);
});

test('non-image upload → 400', async () => {
  const { app } = makeApp({ uploadsDir: os.tmpdir() });
  const fakePdf = Buffer.from('%PDF-1.4 fake content');
  const res = await req(app, 'POST', '/api/report', {
    multipart: {
      location: 'Library',
      description: 'broken thing',
      photo: { buffer: fakePdf, filename: 'doc.pdf', contentType: 'application/pdf' },
    },
  });
  assert.equal(res.status, 400);
  assert.match(res.body.error, /JPEG|PNG|WebP/i);
});

test('location too long → 400', async () => {
  const { app } = makeApp();
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'A'.repeat(61), description: 'something is broken here badly' },
  });
  assert.equal(res.status, 400);
});

test('description too long → 400', async () => {
  const { app } = makeApp();
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Lab 5', description: 'x'.repeat(1001) },
  });
  assert.equal(res.status, 400);
});

test('rate limit: 9th request in a minute → 429', async () => {
  const { app } = makeApp();
  // Send 8 valid requests (they will hit fallback triage)
  for (let i = 0; i < 8; i++) {
    await req(app, 'POST', '/api/report', {
      multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside' },
      headers: { 'x-forwarded-for': '10.0.0.1' },
    });
  }
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside' },
    headers: { 'x-forwarded-for': '10.0.0.1' },
  });
  assert.equal(res.status, 429);
});
