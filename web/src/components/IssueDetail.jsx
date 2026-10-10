import React, { useCallback, useEffect, useState } from 'react';
import { api, timeAgo } from '../api.js';
import { Icon } from '../App.jsx';

const CATEGORY_META = {
  water:      { icon: 'plumbing',           label: 'Water & Plumbing' },
  electrical: { icon: 'electrical_services', label: 'Electrical' },
  ict:        { icon: 'desktop_windows',    label: 'ICT' },
  security:   { icon: 'security',           label: 'Security' },
  roads:      { icon: 'add_road',           label: 'Roads & Grounds' },
  sanitation: { icon: 'cleaning_services',  label: 'Sanitation' },
  other:      { icon: 'build',              label: 'General' },
};

const URGENCY_META = {
  critical: { cls: 'bg-error-container text-on-error-container', dot: 'bg-error', ping: true },
  high:     { cls: 'bg-error-container/60 text-error',           dot: 'bg-error', ping: false },
  medium:   { cls: 'bg-surface-container text-on-surface',       dot: 'bg-secondary', ping: false },
  low:      { cls: 'bg-surface-container text-on-surface-variant',dot: 'bg-outline', ping: false },
};

const STATUS_META = {
  open:        { label: 'Open',        cls: 'bg-surface-container-highest text-secondary' },
  in_progress: { label: 'In progress', cls: 'bg-surface-container-highest text-secondary' },
  resolved:    { label: 'Resolved',    cls: 'bg-primary-fixed text-on-primary-fixed-variant' },
};

const STATUSES = [
  { value: 'open',        label: 'Mark Open' },
  { value: 'in_progress', label: 'In Progress' },
  { value: 'resolved',    label: 'Mark Resolved' },
];

export default function IssueDetail({ id, isStaff, onBack }) {
  const [issue,   setIssue]   = useState(null);
  const [loading, setLoading] = useState(true);
  const [error,   setError]   = useState('');
  const [busy,    setBusy]    = useState(false);
  const [saving,  setSaving]  = useState(false);

  const load = useCallback(async () => {
    setLoading(true); setError('');
    try { setIssue(await api.issue(id)); }
    catch (err) { setError(err.message); }
    finally { setLoading(false); }
  }, [id]);

  useEffect(() => { load(); }, [load]);

  async function toggleUpvote() {
    if (!issue) return;
    setBusy(true);
    try {
      const res = await api.upvote(issue.id);
      setIssue((prev) => ({ ...prev, upvotes: res.upvotes, voted: res.voted }));
    } catch (err) { setError(err.message); }
    finally { setBusy(false); }
  }

  async function changeStatus(next) {
    if (!issue || next === issue.status) return;
    setSaving(true); setError('');
    try {
      const updated = await api.setStatus(issue.id, next);
      setIssue((prev) => ({ ...prev, ...updated }));
    } catch (err) { setError(err.message); }
    finally { setSaving(false); }
  }

  if (loading) {
    return (
      <div className="flex flex-col items-center justify-center py-24 gap-3 text-on-surface-variant">
        <Icon name="refresh" className="text-[36px] animate-spin text-primary" />
        <span className="text-body-md">Loading issue…</span>
      </div>
    );
  }

  if (!issue) {
    return (
      <div className="flex flex-col space-y-4 pt-4">
        <div className="p-3 rounded-xl bg-error-container text-on-error-container text-body-sm">{error || 'Issue not found'}</div>
        <button type="button" onClick={onBack} className="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors py-1">
          <Icon name="chevron_left" className="text-[20px]" />
          <span className="text-label-lg font-semibold">Back to Issues</span>
        </button>
      </div>
    );
  }

  const cat = CATEGORY_META[issue.category] ?? CATEGORY_META.other;
  const urg = URGENCY_META[issue.urgency]   ?? URGENCY_META.medium;
  const sta = STATUS_META[issue.status]     ?? STATUS_META.open;

  return (
    <div className="flex flex-col w-full pb-8 space-y-4 pt-1">
      {/* Back + admin badge */}
      <div className="flex items-center justify-between pt-1">
        <button type="button" onClick={onBack} className="flex items-center gap-1 text-on-surface-variant hover:text-primary transition-colors py-1">
          <Icon name="chevron_left" className="text-[20px]" />
          <span className="text-label-lg font-semibold">Back to Issues</span>
        </button>
        {isStaff && (
          <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface-variant shadow-sm">
            <Icon name="shield" className="text-[14px] text-primary" fill />
            <span className="text-label-sm font-bold tracking-wide uppercase">Admin View</span>
          </div>
        )}
      </div>

      {/* Error */}
      {error && (
        <div className="p-3 rounded-xl bg-error-container text-on-error-container text-body-sm flex items-center justify-between gap-2">
          <span>{error}</span>
          <button type="button" onClick={() => setError('')}><Icon name="close" className="text-[16px]" /></button>
        </div>
      )}

      {/* Hero photo */}
      {issue.photo_url && (
        <div className="relative w-full rounded-2xl overflow-hidden shadow-md bg-surface-container">
          <img src={issue.photo_url} alt={`Photo for ${issue.title}`} className="w-full h-52 sm:h-72 max-h-[50vh] object-cover" />
          <div className="absolute inset-0 bg-gradient-to-t from-on-surface/60 via-transparent to-transparent pointer-events-none" />
          <div className="absolute bottom-3 left-3 right-3 flex items-center justify-between text-on-tertiary">
            <span className="flex items-center gap-1.5 bg-on-surface/70 backdrop-blur-md px-3 py-1 rounded-full text-label-sm font-bold">
              <Icon name="photo_camera" className="text-[14px]" />
              Photo Evidence
            </span>
          </div>
        </div>
      )}

      {/* Title + location */}
      <div className="flex flex-col space-y-2">
        <h1 className="text-headline-xl-mobile font-bold text-on-surface tracking-tight">{issue.title}</h1>
        <div className="flex items-start gap-1.5 text-on-surface-variant">
          <Icon name="location_on" className="text-[18px] text-primary mt-0.5 shrink-0" fill />
          <p className="text-body-md">{issue.location}</p>
        </div>
      </div>

      {/* Status / urgency / category chips */}
      <div className="flex flex-wrap items-center gap-2">
        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-on-surface">
          <Icon name={cat.icon} className="text-[16px] text-primary" />
          <span className="text-label-md font-semibold">{cat.label}</span>
        </div>
        <div className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full ${urg.cls}`}>
          {urg.ping ? (
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75" />
              <span className="relative inline-flex rounded-full h-2 w-2 bg-error" />
            </span>
          ) : (
            <span className={`w-2 h-2 rounded-full ${urg.dot}`} />
          )}
          <span className="text-label-md font-bold">{issue.urgency.charAt(0).toUpperCase() + issue.urgency.slice(1)}</span>
        </div>
        <div className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full ${sta.cls}`}>
          <span className="w-2 h-2 rounded-full bg-secondary" />
          <span className="text-label-md font-semibold">{sta.label}</span>
        </div>
      </div>

      {/* Upvotes + time */}
      <div className="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-surface-container-low shadow-sm">
        <button
          type="button"
          disabled={busy}
          onClick={toggleUpvote}
          className={`flex items-center gap-1.5 transition-all disabled:opacity-50 ${issue.voted ? 'text-primary' : 'text-on-surface-variant hover:text-primary'}`}
        >
          <Icon name="arrow_upward" className="text-[20px]" fill={issue.voted} />
          <span className="text-label-lg font-bold">+{issue.upvotes} student upvote{issue.upvotes !== 1 ? 's' : ''}</span>
        </button>
        <div className="flex items-center gap-1 text-on-surface-variant">
          <Icon name="schedule" className="text-[16px]" />
          <span className="text-label-md font-semibold">Reported {timeAgo(issue.created_at)}</span>
        </div>
      </div>

      {/* AI Summary */}
      <div className="p-4 rounded-xl bg-surface-container shadow-sm flex flex-col space-y-2">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-1.5 text-primary">
            <Icon name="smart_toy" className="text-[20px]" />
            <span className="text-label-lg font-bold">AI Summary</span>
          </div>
          <span className="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed text-label-sm font-bold">
            {issue.confidence} confidence
          </span>
        </div>
        <p className="text-body-md text-on-surface leading-relaxed">{issue.summary}</p>
      </div>

      {/* Department */}
      <div className="p-4 rounded-xl bg-surface-container-low shadow-sm flex flex-col space-y-2">
        <div className="flex items-center justify-between">
          <span className="text-label-md font-bold uppercase tracking-wider text-on-surface-variant">Routed to</span>
          <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container text-label-sm font-bold">
            <Icon name="send" className="text-[14px]" />
            Dispatched
          </span>
        </div>
        <div className="flex items-start gap-3 pt-1">
          <div className="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shrink-0">
            <Icon name="engineering" className="text-[22px]" />
          </div>
          <div className="flex flex-col">
            <span className="text-headline-sm font-semibold text-on-surface">{issue.department}</span>
            <p className="text-body-md text-on-surface-variant mt-0.5">
              Triage: {issue.ai_source} · {issue.language}
            </p>
          </div>
        </div>
      </div>

      {/* Original report */}
      {issue.original_text && (
        <div className="p-4 rounded-xl bg-surface-container-high shadow-sm flex flex-col space-y-2">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-1 text-on-surface-variant">
              <Icon name="record_voice_over" className="text-[18px]" />
              <span className="text-label-md font-semibold">Original Student Report</span>
            </div>
            <span className="px-2.5 py-0.5 rounded-full bg-surface-container-lowest text-on-surface text-label-sm font-semibold shadow-sm">
              {issue.language}
            </span>
          </div>
          <blockquote className="italic text-body-lg text-on-surface leading-relaxed">
            «{issue.original_text}»
          </blockquote>
        </div>
      )}

      {/* Timestamps */}
      <div className="flex flex-wrap gap-x-4 gap-y-1 text-body-sm text-on-surface-variant px-1">
        <span>Reported: {timeAgo(issue.created_at)}</span>
        <span>Updated: {timeAgo(issue.updated_at)}</span>
        {issue.resolved_at && <span className="text-primary font-semibold">Resolved: {timeAgo(issue.resolved_at)}</span>}
      </div>

      {/* Staff admin actions */}
      {isStaff && (
        <div className="p-4 rounded-xl bg-surface-container-lowest shadow-md flex flex-col space-y-3.5">
          <div className="flex items-center justify-between pb-1">
            <span className="text-headline-sm font-semibold text-on-surface">Admin Actions</span>
            <span className="text-label-sm text-primary font-bold flex items-center gap-1">
              <Icon name="lock_reset" className="text-[14px]" />
              Authorized Only
            </span>
          </div>

          <div className="flex flex-col space-y-1.5">
            <label className="text-label-md font-semibold text-on-surface-variant" htmlFor="status-select">
              Current Work Order Status
            </label>
            <div className="relative w-full">
              <select
                id="status-select"
                value={issue.status}
                onChange={(e) => changeStatus(e.target.value)}
                disabled={saving}
                className="w-full h-12 pl-3.5 pr-10 rounded-xl bg-surface-container text-on-surface text-label-lg font-semibold appearance-none focus:outline-none focus:bg-surface-container-high cursor-pointer transition-colors disabled:opacity-50"
              >
                <option value="open">Open</option>
                <option value="in_progress">In progress</option>
                <option value="resolved">Resolved</option>
              </select>
              <Icon name="expand_more" className="absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-[20px]" />
            </div>
          </div>

          <button
            type="button"
            disabled={saving}
            onClick={() => changeStatus('resolved')}
            className="w-full border border-emerald-600 text-emerald-800 bg-emerald-50 hover:bg-emerald-100 font-semibold py-3.5 rounded-xl text-center min-h-[48px] flex items-center justify-center gap-2 active:scale-[0.98] transition-transform disabled:opacity-50"
          >
            <Icon name="task_alt" className="text-[20px] text-emerald-800" />
            <span className="text-label-lg font-semibold">
              {saving ? 'Saving…' : issue.status === 'resolved' ? 'Already Resolved' : 'Mark as Resolved'}
            </span>
          </button>
        </div>
      )}
    </div>
  );
}
