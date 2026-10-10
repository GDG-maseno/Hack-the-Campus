# FixMaseno — Campus Issue Reporter

AI-powered campus maintenance reporting for Maseno University.

## Quick Start (Windows / PowerShell)

```powershell
# 1. Install all dependencies (backend + frontend)
npm run setup

# 2. Seed sample data
npm run seed

# 3. Build the frontend
npm run build

# 4. Start the server
npm start
```

Open http://localhost:3000

## Development

```powershell
npm run dev   # hot-reload backend + Vite frontend
```

## Demo mode (no API key needed)

If you have the Claude Code CLI installed and logged in (`claude --version`), set `TRIAGE_BACKEND=cli` in `.env`.
The CLI uses your logged-in Pro/Max account. Laptop-only, slower — for demos not production.

## Environment

Copy `.env.example` to `.env` and fill in:

| Variable | Default | Notes |
|---|---|---|
| `ANTHROPIC_API_KEY` | — | Real key enables API triage |
| `FIXMASENO_MODEL` | `claude-sonnet-5-5` | Any vision-capable Claude model |
| `ADMIN_KEY` | `maseno-admin` | **Change before sharing a public link** |
| `TRIAGE_BACKEND` | `auto` | `api` / `cli` / `auto` |
| `PORT` | `3000` | |

## Tests

```powershell
npm test
```

Uses `node:test` (built-in). No Jest. Runs against an in-memory DB with a fake Claude client.

## Notes

- Never commit `.env` or `*.db`
- Frontend is React + Vite in `web/` — run `npm run build` after changes
- The fallback keyword triage ensures a failed AI call never returns a 500
- Escape all user text in the UI
- Icons are inline SVG in `web/src/icons.jsx` (exported as `Icon` from `App.jsx`).
  There is no icon-font/CDN request, so a blocked or offline CDN can never render
  icon identifiers such as `location_on` as literal text.

## Deploying

The Express server serves the built SPA itself (`web/dist`) plus `/api` and
`/uploads`, so a single Node process is the whole app.

```powershell
npm ci
npm --prefix web ci
npm run build          # must run on the host: web/dist is what gets served
npm start              # honours PORT
```

Host requirements:

- **Node 20+** and a build step (`npm run build`) in the deploy pipeline —
  without it `/` serves the "front end hasn't been built yet" placeholder.
- **Persistent disk** for `fixmaseno.db` and `uploads/` (SQLite + photo files).
  On an ephemeral filesystem records and photos are lost on every redeploy.
- Set `ADMIN_KEY` to a strong value; `config.js` warns when it is left at the
  default. `ANTHROPIC_API_KEY` is only needed for `TRIAGE_BACKEND=api` — the
  CLI backend is laptop-only, so a server deploy should use `api` or `fallback`.
- Set `TRIAGE_BACKEND` explicitly (`api` or `fallback`). On a host without the
  `claude` CLI, `auto` resolves to `fallback` anyway, but being explicit keeps
  `/api/health` honest.

