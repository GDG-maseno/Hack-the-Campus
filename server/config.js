import 'dotenv/config';
import { execSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

const ANTHROPIC_API_KEY = process.env.ANTHROPIC_API_KEY ?? '';
const FIXMASENO_MODEL = process.env.FIXMASENO_MODEL ?? 'claude-sonnet-5-5';
const ADMIN_KEY = process.env.ADMIN_KEY ?? 'maseno-admin';
const PORT = parseInt(process.env.PORT ?? '3000', 10);
const TRIAGE_BACKEND_ENV = process.env.TRIAGE_BACKEND ?? 'auto';

if (ADMIN_KEY === 'maseno-admin') {
  console.warn('[WARN] ADMIN_KEY is set to the default value. Change it before sharing a public link.');
}

function isRealKey(key) {
  return !!(key && key.length > 10 && key !== 'sk-ant-...');
}

function claudeCliOnPath() {
  try {
    execSync('claude --version', { stdio: 'ignore', timeout: 5000 });
    return true;
  } catch {
    return false;
  }
}

function resolveTriageBackend() {
  if (TRIAGE_BACKEND_ENV === 'api') return 'api';
  if (TRIAGE_BACKEND_ENV === 'cli') return 'cli';
  // auto
  if (isRealKey(ANTHROPIC_API_KEY)) return 'api';
  if (claudeCliOnPath()) return 'cli';
  return 'fallback';
}

const TRIAGE_BACKEND = resolveTriageBackend();

const DB_PATH = path.join(__dirname, '..', 'fixmaseno.db');
const UPLOADS_DIR = path.join(__dirname, '..', 'uploads');

export { ANTHROPIC_API_KEY, FIXMASENO_MODEL, ADMIN_KEY, PORT, TRIAGE_BACKEND, DB_PATH, UPLOADS_DIR, isRealKey };
