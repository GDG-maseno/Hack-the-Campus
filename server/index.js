import { ANTHROPIC_API_KEY, FIXMASENO_MODEL, ADMIN_KEY, PORT, TRIAGE_BACKEND, DB_PATH, UPLOADS_DIR, isRealKey } from './config.js';
import { openDb } from './db.js';
import { createApp } from './app.js';
import Anthropic from '@anthropic-ai/sdk';
import fs from 'node:fs';

// Ensure uploads dir exists
fs.mkdirSync(UPLOADS_DIR, { recursive: true });

const db = openDb(DB_PATH);

let anthropicClient = null;
if (isRealKey(ANTHROPIC_API_KEY)) {
  anthropicClient = new Anthropic({ apiKey: ANTHROPIC_API_KEY, timeout: 40_000, maxRetries: 1 });
}

const app = createApp({
  db,
  uploadsDir: UPLOADS_DIR,
  adminKey: ADMIN_KEY,
  triageBackend: TRIAGE_BACKEND,
  anthropicClient,
  model: FIXMASENO_MODEL,
});

app.listen(PORT, () => {
  console.log(`FixMaseno server running on http://localhost:${PORT}`);
  console.log(`Triage backend: ${TRIAGE_BACKEND} | Model: ${FIXMASENO_MODEL}`);
});
