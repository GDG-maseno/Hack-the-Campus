import { createApp } from '../../app.js';
import { openDb } from '../../db.js';

export function makeApp(overrides = {}) {
  const db = openDb(':memory:');

  // Fake Anthropic client — returns a valid tool_use block
  const fakeAnthropicClient = {
    messages: {
      create: async (opts) => {
        // If tools present → tool_use response
        if (opts.tools && opts.tools.length > 0) {
          return {
            content: [{
              type: 'tool_use',
              name: 'log_campus_issue',
              input: overrides.triageResult ?? {
                is_valid_report: true,
                title: 'Test issue',
                summary: 'A test issue for automated testing.',
                category: 'water',
                urgency: 'medium',
                language: 'English',
                duplicate_of: null,
                confidence: 'high',
                ack_message: 'Thank you for your report.',
              },
            }],
          };
        }
        // Insights narrative
        return { content: [{ type: 'text', text: 'Three sentence briefing for tests.' }] };
      },
    },
  };

  // Fake spawner — simulates claude CLI returning JSON
  const fakeSpawner = async (_exe, _args, opts) => {
    const result = overrides.cliResult ?? {
      is_valid_report: true,
      title: 'CLI test issue',
      summary: 'CLI triage result.',
      category: 'ict',
      urgency: 'low',
      language: 'English',
      duplicate_of: null,
      confidence: 'low',
      ack_message: 'Got it.',
    };
    return { stdout: JSON.stringify(result), stderr: '' };
  };

  const effectiveDb = overrides.db ?? db;

  const app = createApp({
    db: effectiveDb,
    uploadsDir: overrides.uploadsDir ?? ':memory:',
    adminKey: overrides.adminKey ?? 'test-admin',
    triageBackend: overrides.triageBackend ?? 'fallback',
    anthropicClient: overrides.anthropicClient ?? fakeAnthropicClient,
    model: 'claude-test',
    spawner: overrides.spawner ?? fakeSpawner,
    clock: overrides.clock ?? (() => new Date('2025-01-15T10:00:00Z')),
  });

  return { app, db: effectiveDb };
}

// Minimal HTTP request helper (no external deps)
export async function req(app, method, url, { body, headers = {}, multipart } = {}) {
  const { default: http } = await import('node:http');

  return new Promise((resolve, reject) => {
    const server = http.createServer(app);
    server.listen(0, '127.0.0.1', () => {
      const port = server.address().port;
      let postData;
      const reqHeaders = { ...headers };

      if (multipart) {
        const boundary = '----TestBoundary' + Date.now();
        reqHeaders['content-type'] = `multipart/form-data; boundary=${boundary}`;
        const parts = [];
        for (const [k, v] of Object.entries(multipart)) {
          if (v && v.buffer) {
            parts.push(
              `--${boundary}\r\nContent-Disposition: form-data; name="${k}"; filename="${v.filename}"\r\nContent-Type: ${v.contentType}\r\n\r\n`
            );
            parts.push(v.buffer);
            parts.push('\r\n');
          } else {
            parts.push(`--${boundary}\r\nContent-Disposition: form-data; name="${k}"\r\n\r\n${v}\r\n`);
          }
        }
        parts.push(`--${boundary}--\r\n`);
        postData = Buffer.concat(parts.map(p => Buffer.isBuffer(p) ? p : Buffer.from(p)));
        reqHeaders['content-length'] = postData.length;
      } else if (body) {
        postData = JSON.stringify(body);
        reqHeaders['content-type'] = 'application/json';
        reqHeaders['content-length'] = Buffer.byteLength(postData);
      }

      const options = { hostname: '127.0.0.1', port, path: url, method, headers: reqHeaders };
      const request = http.request(options, (response) => {
        let data = '';
        response.on('data', chunk => { data += chunk; });
        response.on('end', () => {
          server.close();
          try { resolve({ status: response.statusCode, body: JSON.parse(data) }); }
          catch { resolve({ status: response.statusCode, body: data }); }
        });
      });
      request.on('error', (e) => { server.close(); reject(e); });
      if (postData) request.write(postData);
      request.end();
    });
  });
}

// JPEG magic bytes (minimal 4-byte header)
export const FAKE_JPEG = Buffer.from([0xFF, 0xD8, 0xFF, 0xE0, ...Array(16).fill(0)]);
export const FAKE_PNG  = Buffer.from([0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A, ...Array(16).fill(0)]);
