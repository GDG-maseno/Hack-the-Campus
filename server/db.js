import Database from 'better-sqlite3';
import path from 'node:path';

export function openDb(dbPath) {
  const db = new Database(dbPath);
  db.pragma('journal_mode = WAL');
  db.pragma('busy_timeout = 5000');

  db.exec(`
    CREATE TABLE IF NOT EXISTS issues (
      id          INTEGER PRIMARY KEY AUTOINCREMENT,
      title       TEXT NOT NULL,
      summary     TEXT NOT NULL,
      original_text TEXT,
      location    TEXT NOT NULL,
      category    TEXT NOT NULL,
      urgency     TEXT NOT NULL,
      department  TEXT NOT NULL,
      language    TEXT NOT NULL,
      confidence  TEXT NOT NULL,
      status      TEXT NOT NULL DEFAULT 'open',
      upvotes     INTEGER NOT NULL DEFAULT 1,
      photo       TEXT,
      ai_source   TEXT,
      created_at  TEXT NOT NULL,
      updated_at  TEXT NOT NULL,
      resolved_at TEXT
    );

    CREATE TABLE IF NOT EXISTS votes (
      issue_id  INTEGER NOT NULL,
      voter_id  TEXT NOT NULL,
      PRIMARY KEY (issue_id, voter_id)
    );
  `);

  return db;
}
