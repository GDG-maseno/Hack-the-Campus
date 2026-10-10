import React, { useEffect, useState } from 'react';
import { api, setAdminKey, verifyAdminKey } from '../api.js';
import { Icon } from '../App.jsx';

export default function StaffSignIn({ isStaff, onSignedIn, onSignedOut }) {
  const [key,             setKey]             = useState('');
  const [error,           setError]           = useState('');
  const [busy,            setBusy]            = useState(false);
  const [insights,        setInsights]        = useState(null);
  const [insightsError,   setInsightsError]   = useState('');
  const [loadingInsights, setLoadingInsights] = useState(false);

  useEffect(() => { setInsights(null); }, [isStaff]);

  async function submit(e) {
    e.preventDefault();
    if (!key.trim()) return;
    setBusy(true); setError('');
    try {
      await verifyAdminKey(key.trim());
      setAdminKey(key.trim());
      setKey('');
      onSignedIn();
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  function signOut() { setAdminKey(''); setInsights(null); onSignedOut(); }

  async function loadInsights() {
    setLoadingInsights(true); setInsightsError('');
    try { setInsights(await api.insights()); }
    catch (err) { setInsightsError(err.message); }
    finally { setLoadingInsights(false); }
  }

  if (!isStaff) {
    return (
      <div className="flex flex-col w-full pb-6 space-y-5 pt-4">
        <div className="flex flex-col space-y-1">
          <div className="flex items-center space-x-2">
            <span className="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-primary-container text-on-primary shadow-sm">
              <Icon name="shield" className="text-[18px]" />
            </span>
            <h1 className="text-headline-lg font-bold text-on-surface tracking-tight">Staff Sign-in</h1>
          </div>
          <p className="text-body-md text-on-surface-variant pl-9">Enter the staff key to update issue status and view the AI briefing.</p>
        </div>

        {error && (
          <div className="p-3 rounded-xl bg-error-container text-on-error-container text-body-sm flex items-center justify-between gap-2">
            <span>{error}</span>
            <button type="button" onClick={() => setError('')}><Icon name="close" className="text-[16px]" /></button>
          </div>
        )}

        <form onSubmit={submit} className="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex flex-col space-y-4">
          <div className="flex flex-col space-y-1.5">
            <label className="text-label-lg font-semibold text-on-surface flex items-center gap-1.5" htmlFor="admin-key">
              <Icon name="key" className="text-[18px] text-primary" />
              Staff key
            </label>
            <input
              id="admin-key"
              type="password"
              value={key}
              onChange={(e) => setKey(e.target.value)}
              autoComplete="current-password"
              required
              className="w-full h-12 bg-surface-container-low text-on-surface text-body-md rounded-xl px-3.5 focus:outline-none focus:bg-surface-container-lowest shadow-sm transition-colors"
              placeholder="Enter staff key…"
            />
          </div>
          <button
            type="submit"
            disabled={busy || !key.trim()}
            className="w-full h-12 bg-primary-container hover:bg-primary active:scale-[0.99] text-on-primary text-headline-sm font-semibold rounded-xl shadow-md flex items-center justify-center gap-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Icon name={busy ? 'refresh' : 'login'} className={`text-[20px] ${busy ? 'animate-spin' : ''}`} />
            {busy ? 'Checking…' : 'Sign in'}
          </button>
        </form>
      </div>
    );
  }

  return (
    <div className="flex flex-col w-full pb-8 space-y-5 pt-1">
      {/* Header */}
      <div className="flex flex-wrap items-center justify-between gap-2">
        <div className="flex flex-col space-y-1">
          <div className="flex items-center space-x-2">
            <span className="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-primary-container text-on-primary shadow-sm">
              <Icon name="shield" className="text-[18px]" fill />
            </span>
            <h1 className="text-headline-lg font-bold text-on-surface tracking-tight">Staff Console</h1>
          </div>
          <p className="text-body-md text-on-surface-variant pl-9">Signed in · session only</p>
        </div>
        <button
          type="button"
          onClick={signOut}
          className="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-outline/40 bg-surface-container-low text-on-surface text-label-lg font-semibold hover:bg-surface-container transition-colors"
        >
          <Icon name="logout" className="text-[18px]" />
          Sign out
        </button>
      </div>

      {/* Insights */}
      <div className="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex flex-col space-y-3">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-1.5 text-primary">
            <Icon name="smart_toy" className="text-[20px]" />
            <span className="text-label-lg font-bold">AI Operations Briefing</span>
          </div>
          <button
            type="button"
            onClick={loadInsights}
            disabled={loadingInsights}
            className="flex items-center gap-1 px-3 py-1.5 rounded-xl bg-primary-container text-on-primary text-label-md font-semibold hover:bg-primary transition-colors disabled:opacity-50"
          >
            <Icon name={loadingInsights ? 'refresh' : 'auto_awesome'} className={`text-[16px] ${loadingInsights ? 'animate-spin' : ''}`} />
            {loadingInsights ? 'Generating…' : insights ? 'Refresh' : 'Generate'}
          </button>
        </div>

        {insightsError && (
          <div className="p-3 rounded-xl bg-error-container text-on-error-container text-body-sm flex items-center justify-between gap-2">
            <span>{insightsError}</span>
            <button type="button" onClick={() => setInsightsError('')}><Icon name="close" className="text-[16px]" /></button>
          </div>
        )}

        {insights ? (
          <div className="space-y-4">
            <p className="text-body-md text-on-surface leading-relaxed">{insights.briefing}</p>

            <div className="grid grid-cols-3 gap-2.5">
              {[
                { label: 'Open',     value: insights.open,          cls: 'text-error' },
                { label: 'Critical', value: insights.critical_open, cls: 'text-error' },
                { label: 'Avg resolve', value: insights.avg_hours_to_resolve == null ? '—' : `${Math.round(insights.avg_hours_to_resolve)}h`, cls: 'text-primary' },
              ].map(({ label, value, cls }) => (
                <div key={label} className="flex flex-col p-3 rounded-2xl bg-surface-container shadow-sm">
                  <span className={`text-headline-lg font-bold tracking-tight ${cls}`}>{value}</span>
                  <span className="text-label-sm font-bold text-on-surface-variant mt-0.5">{label}</span>
                </div>
              ))}
            </div>

            {insights.hotspots?.length > 0 && (
              <div className="flex flex-col space-y-2">
                <span className="text-label-md font-bold text-on-surface-variant uppercase tracking-wider">Hotspot Locations</span>
                {insights.hotspots.map((h) => (
                  <div key={h.location} className="flex items-center justify-between px-3 py-2 rounded-xl bg-surface-container-low">
                    <div className="flex items-center gap-2 text-on-surface">
                      <Icon name="location_on" className="text-[16px] text-primary shrink-0" />
                      <span className="text-body-md truncate">{h.location}</span>
                    </div>
                    <span className="text-label-md font-bold text-on-surface-variant shrink-0">{h.count} open</span>
                  </div>
                ))}
              </div>
            )}

            {insights.unresolved_by_category?.length > 0 && (
              <div className="flex flex-col space-y-2">
                <span className="text-label-md font-bold text-on-surface-variant uppercase tracking-wider">Unresolved by Category</span>
                {insights.unresolved_by_category.map((c) => (
                  <div key={c.category} className="flex items-center justify-between px-3 py-2 rounded-xl bg-surface-container-low">
                    <span className="text-body-md text-on-surface capitalize">{c.category}</span>
                    <span className="text-label-md font-bold text-on-surface-variant">{c.count}</span>
                  </div>
                ))}
              </div>
            )}
          </div>
        ) : !loadingInsights && (
          <p className="text-body-md text-on-surface-variant">
            Generate a short briefing written by Claude from today's numbers.
          </p>
        )}
      </div>
    </div>
  );
}
