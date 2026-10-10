# FixMaseno — Project Rules

## Layout
- `package.json` — root, backend + scripts
- `server/` — index.js, app.js, db.js, config.js, triage.js, seed.js
- `server/tests/` — *.test.js (node:test), helpers/index.js
- `web/` — React + Vite frontend (Phase 2)
- `uploads/` — git-ignored photo storage

## Rules
- Run `npm test` after every change
- The fallback keyword triage must always work — a failed AI call must never return a 500
- Escape all user text in the UI
- Never commit `.env` or `*.db`
- Frontend is React + Vite in `web/` — needs `npm run build` after changes
- Demo mode: no API key needed, uses the logged-in Claude Code CLI (`TRIAGE_BACKEND=cli`); laptop-only, slower, for demos not production
- JavaScript only (ESM, `"type":"module"`), Node.js + npm — no Python, no TypeScript
- Stack: express@4, better-sqlite3, multer, dotenv, @anthropic-ai/sdk; dev: concurrently
- Tests use node:test + node:assert (no Jest), in-memory DB, fake Claude client, fake spawner
