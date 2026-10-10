import { test } from 'node:test';
import assert from 'node:assert/strict';
import { run } from '../triage.js';
import { makeApp, req, FAKE_JPEG } from './helpers/index.js';
import os from 'node:os';

// ── Fallback triage ──────────────────────────────────────────────────────────

test('fallback: Swahili burst pipe → water/critical', async () => {
  const { result } = await run(
    'Bomba imepasuka mob sana maji inafurika nje',
    'Hostel B', null, null, [],
    { triageBackend: 'fallback' }
  );
  assert.equal(result.category, 'water');
  assert.equal(result.urgency, 'critical');
  assert.equal(result.source ?? result.confidence, result.confidence); // confidence present
});

test('fallback: English PC issue → ict', async () => {
  const { result } = await run(
    'The computer in lab 5 will not boot at all',
    'Lab 5', null, null, [],
    { triageBackend: 'fallback' }
  );
  assert.equal(result.category, 'ict');
});

test('fallback: short text → is_valid_report false', async () => {
  const { result } = await run('hi', 'Hostel A', null, null, [], { triageBackend: 'fallback' });
  assert.equal(result.is_valid_report, false);
});

// ── API path ─────────────────────────────────────────────────────────────────

test('api path: issue created from tool call', async () => {
  const { app } = makeApp({ triageBackend: 'api', uploadsDir: os.tmpdir() });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside badly' },
    headers: { 'x-voter-id': 'voter-api-1' },
  });
  assert.equal(res.status, 201);
  assert.equal(res.body.issue.category, 'water');
  assert.equal(res.body.ai, 'claude');
});

test('api path: image sent as first base64 block', async () => {
  let capturedContent = null;
  const capturingClient = {
    messages: {
      create: async (opts) => {
        capturedContent = opts.messages[0].content;
        return {
          content: [{
            type: 'tool_use',
            name: 'log_campus_issue',
            input: {
              is_valid_report: true, title: 'Photo issue', summary: 'Issue with photo.',
              category: 'electrical', urgency: 'high', language: 'English',
              duplicate_of: null, confidence: 'high', ack_message: 'Received.',
            },
          }],
        };
      },
    },
  };
  const { app } = makeApp({ triageBackend: 'api', anthropicClient: capturingClient, uploadsDir: os.tmpdir() });
  await req(app, 'POST', '/api/report', {
    multipart: {
      location: 'Lecture Halls',
      description: 'exposed wiring on the socket',
      photo: { buffer: FAKE_JPEG, filename: 'photo.jpg', contentType: 'image/jpeg' },
    },
  });
  assert.ok(capturedContent, 'content was captured');
  assert.equal(capturedContent[0].type, 'image', 'first block should be image');
  assert.equal(capturedContent[1].type, 'text');
});

test('api path: forced tool_choice', async () => {
  let capturedOpts = null;
  const capturingClient = {
    messages: {
      create: async (opts) => {
        capturedOpts = opts;
        return {
          content: [{
            type: 'tool_use', name: 'log_campus_issue',
            input: {
              is_valid_report: true, title: 'T', summary: 'S', category: 'other',
              urgency: 'low', language: 'English', duplicate_of: null, confidence: 'low', ack_message: 'Ok.',
            },
          }],
        };
      },
    },
  };
  const { app } = makeApp({ triageBackend: 'api', anthropicClient: capturingClient, uploadsDir: os.tmpdir() });
  await req(app, 'POST', '/api/report', {
    multipart: { location: 'Library', description: 'something is broken here today' },
  });
  assert.deepEqual(capturedOpts.tool_choice, { type: 'tool', name: 'log_campus_issue' });
});

test('api path: text wrapped in <<< >>> markers', async () => {
  let capturedText = null;
  const capturingClient = {
    messages: {
      create: async (opts) => {
        capturedText = opts.messages[0].content.find(b => b.type === 'text')?.text ?? '';
        return {
          content: [{
            type: 'tool_use', name: 'log_campus_issue',
            input: {
              is_valid_report: true, title: 'T', summary: 'S', category: 'other',
              urgency: 'low', language: 'English', duplicate_of: null, confidence: 'low', ack_message: 'Ok.',
            },
          }],
        };
      },
    },
  };
  const { app } = makeApp({ triageBackend: 'api', anthropicClient: capturingClient, uploadsDir: os.tmpdir() });
  await req(app, 'POST', '/api/report', {
    multipart: { location: 'Library', description: 'pipe is leaking water badly' },
  });
  assert.ok(capturedText.includes('<<<'), 'text should be wrapped in <<<');
  assert.ok(capturedText.includes('>>>'), 'text should be wrapped in >>>');
});

// ── CLI path ─────────────────────────────────────────────────────────────────

test('cli path: issue created from spawner output', async () => {
  const { app } = makeApp({ triageBackend: 'cli', uploadsDir: os.tmpdir() });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'School of Computing', description: 'projector has no signal at all' },
    headers: { 'x-voter-id': 'voter-cli-1' },
  });
  assert.equal(res.status, 201);
  assert.equal(res.body.ai, 'claude-cli');
});

test('cli junk output → fallback not 500', async () => {
  const junkSpawner = async () => ({ stdout: 'not json at all !!!', stderr: '' });
  const { app } = makeApp({ triageBackend: 'cli', spawner: junkSpawner, uploadsDir: os.tmpdir() });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside' },
  });
  // Should fall through to fallback, not 500
  assert.ok(res.status !== 500, `Expected non-500, got ${res.status}`);
});

test('cli non-zero exit → fallback not 500', async () => {
  const failSpawner = async () => { const e = new Error('exit 1'); e.code = 1; throw e; };
  const { app } = makeApp({ triageBackend: 'cli', spawner: failSpawner, uploadsDir: os.tmpdir() });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside' },
  });
  assert.ok(res.status !== 500);
});

// ── Enum coercion ─────────────────────────────────────────────────────────────

test('bad enums coerced to valid values', async () => {
  const badClient = {
    messages: {
      create: async () => ({
        content: [{
          type: 'tool_use', name: 'log_campus_issue',
          input: {
            is_valid_report: true, title: 'Bad enums', summary: 'Test.',
            category: 'INVALID_CAT', urgency: 'INVALID_URG',
            language: 'INVALID_LANG', duplicate_of: null,
            confidence: 'INVALID_CONF', ack_message: 'Ok.',
          },
        }],
      }),
    },
  };
  const { app } = makeApp({ triageBackend: 'api', anthropicClient: badClient, uploadsDir: os.tmpdir() });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Library', description: 'something is broken here today' },
  });
  assert.equal(res.status, 201);
  assert.equal(res.body.issue.category, 'other');
  assert.equal(res.body.issue.urgency, 'medium');
});

// ── API failure → fallback ────────────────────────────────────────────────────

test('API failure → fallback not 500', async () => {
  const failClient = {
    messages: { create: async () => { throw new Error('Network error'); } },
  };
  const { app } = makeApp({ triageBackend: 'api', anthropicClient: failClient, uploadsDir: os.tmpdir() });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Hostel B', description: 'water is leaking from the pipe outside badly' },
  });
  assert.ok(res.status !== 500, `Expected non-500, got ${res.status}`);
});

// ── Backend auto-selection ────────────────────────────────────────────────────

test('auto: placeholder key counts as no key → not api', async () => {
  const { isRealKey } = await import('../config.js');
  assert.equal(isRealKey('sk-ant-...'), false);
  assert.equal(isRealKey(''), false);
  assert.equal(isRealKey(undefined), false);
  assert.equal(isRealKey('sk-ant-realkey1234567890'), true);
});

// ── Invalid report → 422, nothing stored ─────────────────────────────────────

test('invalid report → 422 and nothing stored', async () => {
  const rejectClient = {
    messages: {
      create: async () => ({
        content: [{
          type: 'tool_use', name: 'log_campus_issue',
          input: {
            is_valid_report: false,
            rejection_reason: 'Hii si tatizo la campus.',
            title: '', summary: '', category: 'other', urgency: 'low',
            language: 'Swahili', duplicate_of: null, confidence: 'low', ack_message: '',
          },
        }],
      }),
    },
  };
  const { app, db } = makeApp({ triageBackend: 'api', anthropicClient: rejectClient, uploadsDir: os.tmpdir() });
  const res = await req(app, 'POST', '/api/report', {
    multipart: { location: 'Library', description: 'haha just a selfie lol' },
  });
  assert.equal(res.status, 422);
  assert.equal(res.body.rejected, true);
  const count = db.prepare('SELECT COUNT(*) as n FROM issues').get().n;
  assert.equal(count, 0);
});
