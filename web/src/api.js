// ── FixMaseno frontend API wrapper ───────────────────────────────────────────
// Talks to the Express backend. All errors are surfaced as thrown Errors whose
// message comes from the server's {error} JSON body (never invented).

const API = '/api';
const VOTER_KEY = 'fixmaseno.voterId';
const ADMIN_KEY = 'fixmaseno.adminKey';

// ── Identity helpers ─────────────────────────────────────────────────────────

export function getVoterId() {
  try {
    let id = localStorage.getItem(VOTER_KEY);
    if (!id) {
      id =
        (crypto.randomUUID && crypto.randomUUID()) ||
        `v-${Date.now()}-${Math.random().toString(36).slice(2)}`;
      localStorage.setItem(VOTER_KEY, id);
    }
    return id;
  } catch {
    return `v-${Date.now()}-${Math.random().toString(36).slice(2)}`;
  }
}

export function getAdminKey() {
  try {
    return sessionStorage.getItem(ADMIN_KEY) || '';
  } catch {
    return '';
  }
}

export function setAdminKey(key) {
  try {
    if (key) sessionStorage.setItem(ADMIN_KEY, key);
    else sessionStorage.removeItem(ADMIN_KEY);
  } catch {
    /* storage unavailable */
  }
}

// ── Core fetch helper ────────────────────────────────────────────────────────

async function request(path, { method = 'GET', body, admin = false, voter = true } = {}) {
  const headers = {};
  if (voter) headers['X-Voter-Id'] = getVoterId();
  if (admin) headers['X-Admin-Key'] = getAdminKey();

  let payload = body;
  if (body && !(body instanceof FormData)) {
    headers['Content-Type'] = 'application/json';
    payload = JSON.stringify(body);
  }

  let res;
  try {
    res = await fetch(`${API}${path}`, { method, headers, body: payload });
  } catch {
    throw new Error('Cannot reach the server. Is it running?');
  }

  const text = await res.text();
  let data = null;
  if (text) {
    try {
      data = JSON.parse(text);
    } catch {
      data = null;
    }
  }

  if (!res.ok) {
    const err = new Error((data && data.error) || `Request failed (${res.status})`);
    err.status = res.status;
    err.rejected = !!(data && data.rejected);
    throw err;
  }
  return data;
}

// ── Endpoints ────────────────────────────────────────────────────────────────

export const api = {
  health: () => request('/health', { voter: false }),

  /** multipart: location (required), description, photo (optional) */
  report: ({ location, description, photo }) => {
    const fd = new FormData();
    fd.append('location', location);
    fd.append('description', description || '');
    if (photo) fd.append('photo', photo);
    return request('/report', { method: 'POST', body: fd });
  },

  issues: ({ category = 'all', status = 'all', sort = 'priority' } = {}) =>
    request(`/issues?category=${encodeURIComponent(category)}&status=${encodeURIComponent(status)}&sort=${encodeURIComponent(sort)}`),

  issue: (id) => request(`/issues/${encodeURIComponent(id)}`),

  stats: () => request('/stats', { voter: false }),

  upvote: (id) => request(`/issues/${encodeURIComponent(id)}/upvote`, { method: 'POST' }),

  /** Verifies the key already stored in sessionStorage. */
  checkAdmin: () => request('/admin/check', { method: 'POST', admin: true, voter: false }),

  setStatus: (id, status) =>
    request(`/issues/${encodeURIComponent(id)}/status`, { method: 'PATCH', admin: true, voter: false, body: { status } }),

  insights: () => request('/insights', { admin: true, voter: false }),
};

/** Verify an admin key directly (used by the sign-in form before storing it). */
export async function verifyAdminKey(key) {
  let res;
  try {
    res = await fetch(`${API}/admin/check`, { method: 'POST', headers: { 'X-Admin-Key': key } });
  } catch {
    throw new Error('Cannot reach the server. Is it running?');
  }
  const data = await res.json().catch(() => null);
  if (!res.ok) throw new Error((data && data.error) || 'Invalid staff key');
  return true;
}

// ── Presentation helpers (pure) ──────────────────────────────────────────────

export const CATEGORIES = ['water', 'electrical', 'ict', 'security', 'roads', 'sanitation', 'other'];
export const STATUSES = ['open', 'in_progress', 'resolved'];
export const URGENCIES = ['critical', 'high', 'medium', 'low'];

export const CATEGORY_LABELS = {
  water: 'Water',
  electrical: 'Electrical',
  ict: 'ICT',
  security: 'Security',
  roads: 'Roads',
  sanitation: 'Sanitation',
  other: 'Other',
};

export const URGENCY_STYLES = {
  critical: 'bg-red-500/15 text-red-300 border border-red-500/30',
  high: 'bg-orange-500/15 text-orange-300 border border-orange-500/30',
  medium: 'bg-amber-500/15 text-amber-200 border border-amber-500/30',
  low: 'bg-slate-500/15 text-slate-300 border border-slate-500/30',
};

export const STATUS_STYLES = {
  open: 'bg-sky-500/15 text-sky-300 border border-sky-500/30',
  in_progress: 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30',
  resolved: 'bg-mseno-500/15 text-mseno-400 border border-mseno-500/30',
};

export const STATUS_LABELS = {
  open: 'Open',
  in_progress: 'In progress',
  resolved: 'Resolved',
};

export function timeAgo(iso) {
  if (!iso) return '';
  const then = new Date(iso).getTime();
  if (Number.isNaN(then)) return '';
  const secs = Math.max(0, Math.floor((Date.now() - then) / 1000));
  if (secs < 60) return 'just now';
  const mins = Math.floor(secs / 60);
  if (mins < 60) return `${mins}m ago`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours}h ago`;
  const days = Math.floor(hours / 24);
  if (days < 30) return `${days}d ago`;
  return new Date(iso).toLocaleDateString();
}

export function ticketNumber(id) {
  return `FIX-${String(id).padStart(4, '0')}`;
}
