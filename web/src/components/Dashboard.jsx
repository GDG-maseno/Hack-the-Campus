import React, { useCallback, useEffect, useState } from 'react';
import { api } from '../api.js';
import IssueCard from './IssueCard.jsx';
import { Icon } from '../App.jsx';

const CATEGORIES = ['water', 'electrical', 'ict', 'security', 'roads', 'sanitation'];
const CAT_LABELS  = { water: 'Water', electrical: 'Electrical', ict: 'ICT', security: 'Security', roads: 'Roads', sanitation: 'Sanitation' };

export default function Dashboard({ onOpenIssue }) {
  const [stats,    setStats]    = useState(null);
  const [issues,   setIssues]   = useState([]);
  const [category, setCategory] = useState('all');
  const [status,   setStatus]   = useState('all');
  const [loading,  setLoading]  = useState(true);
  const [error,    setError]    = useState('');
  const [busyId,   setBusyId]   = useState(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const [s, list] = await Promise.all([
        api.stats(),
        api.issues({ category, status, sort: 'priority' }),
      ]);
      setStats(s);
      setIssues(Array.isArray(list) ? list : []);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [category, status]);

  useEffect(() => { load(); }, [load]);

  async function toggleUpvote(id) {
    setBusyId(id);
    try {
      const res = await api.upvote(id);
      setIssues((prev) => prev.map((i) => i.id === id ? { ...i, upvotes: res.upvotes, voted: res.voted } : i));
    } catch (err) {
      setError(err.message);
    } finally {
      setBusyId(null);
    }
  }

  return (
    <div className="flex flex-col w-full pb-16">
      {/* Title row */}
      <div className="flex flex-col mb-space-md pt-4">
        <div className="flex items-center justify-between">
          <div className="flex flex-col">
            <h1 className="text-headline-xl-mobile font-bold text-on-surface tracking-tight">Campus Issues</h1>
            <p className="text-body-md text-on-surface-variant mt-0.5">Live Maseno University maintenance feed</p>
          </div>
          <div className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-fixed text-on-primary-fixed text-label-sm font-bold shadow-sm">
            <span className="w-2 h-2 rounded-full bg-primary animate-pulse" />
            <span>LIVE FEED</span>
          </div>
        </div>
      </div>

      {/* Stats row */}
      {stats && (
        <div className="grid grid-cols-3 gap-2.5 mb-space-lg">
          <div className="flex flex-col p-3 rounded-2xl bg-surface-container shadow-sm">
            <div className="flex items-center justify-between mb-1">
              <span className="text-label-sm font-bold text-on-surface-variant">Open</span>
              <span className="w-2.5 h-2.5 rounded-full bg-error" />
            </div>
            <span className="text-headline-lg font-bold text-on-surface tracking-tight">{stats.open}</span>
            <span className="text-body-sm text-error font-semibold mt-0.5">+{stats.new_today} today</span>
          </div>
          <div className="flex flex-col p-3 rounded-2xl bg-surface-container shadow-sm">
            <div className="flex items-center justify-between mb-1">
              <span className="text-label-sm font-bold text-on-surface-variant">In progress</span>
              <span className="w-2.5 h-2.5 rounded-full bg-secondary" />
            </div>
            <span className="text-headline-lg font-bold text-on-surface tracking-tight">{stats.in_progress}</span>
            <span className="text-body-sm text-secondary font-semibold mt-0.5">Active work</span>
          </div>
          <div className="flex flex-col p-3 rounded-2xl bg-surface-container shadow-sm">
            <div className="flex items-center justify-between mb-1">
              <span className="text-label-sm font-bold text-on-surface-variant truncate">Resolved</span>
              <Icon name="check_circle" className="text-[16px] text-primary" fill />
            </div>
            <span className="text-headline-lg font-bold text-on-surface tracking-tight">{stats.resolved_week}</span>
            <span className="text-body-sm text-primary font-semibold mt-0.5">This week</span>
          </div>
        </div>
      )}

      {/* Error */}
      {error && (
        <div className="mb-space-md p-3 rounded-xl bg-error-container text-on-error-container text-body-sm flex items-center justify-between gap-2">
          <span>{error}</span>
          <button type="button" onClick={() => setError('')} className="shrink-0">
            <Icon name="close" className="text-[16px]" />
          </button>
        </div>
      )}

      {/* Filters */}
      <div className="flex flex-col gap-2.5 mb-space-lg">
        {/* Category chips */}
        <div className="flex items-center gap-2 overflow-x-auto pb-1 -mx-gutter px-gutter no-scrollbar">
          {['all', ...CATEGORIES].map((cat) => (
            <button
              key={cat}
              type="button"
              onClick={() => setCategory(cat)}
              className={`px-4 py-2 rounded-full text-label-md font-semibold transition-all shrink-0 shadow-sm ${
                category === cat
                  ? 'bg-primary-container text-on-primary'
                  : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'
              }`}
            >
              {cat === 'all' ? 'All' : CAT_LABELS[cat]}
            </button>
          ))}
        </div>

        {/* Status chips */}
        <div className="flex items-center gap-2">
          {[['all', 'Status: All'], ['open', 'Open'], ['in_progress', 'In progress'], ['resolved', 'Resolved']].map(([val, label]) => (
            <button
              key={val}
              type="button"
              onClick={() => setStatus(val)}
              className={`flex-1 py-1.5 px-3 rounded-full text-center text-label-sm font-semibold transition-all shadow-sm ${
                status === val
                  ? 'bg-surface-container-highest text-on-surface'
                  : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'
              }`}
            >
              {label}
            </button>
          ))}
        </div>
      </div>

      {/* Issue cards */}
      {loading && issues.length === 0 ? (
        <div className="flex flex-col items-center justify-center py-16 text-on-surface-variant gap-3">
          <Icon name="refresh" className="text-[32px] animate-spin text-primary" />
          <span className="text-body-md">Loading issues…</span>
        </div>
      ) : issues.length === 0 ? (
        <div className="flex flex-col items-center justify-center py-16 text-on-surface-variant gap-3">
          <Icon name="inbox" className="text-[40px]" />
          <span className="text-body-md">No issues match these filters.</span>
        </div>
      ) : (
        <div className="flex flex-col gap-3.5">
          {issues.map((issue) => (
            <IssueCard
              key={issue.id}
              issue={issue}
              busy={busyId === issue.id}
              onUpvote={toggleUpvote}
              onOpen={onOpenIssue}
            />
          ))}
        </div>
      )}

      {/* FAB */}
      <aside className="fixed right-5 bottom-20 z-40">
        <a
          href="#/"
          aria-label="Report new campus issue"
          className="flex items-center justify-center w-14 h-14 rounded-full bg-primary text-on-primary shadow-[0_4px_16px_rgba(0,100,55,0.4)] transition-transform duration-200 hover:scale-105 active:scale-95"
        >
          <Icon name="add" className="text-[28px]" />
        </a>
      </aside>
    </div>
  );
}
