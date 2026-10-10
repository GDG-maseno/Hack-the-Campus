import React, { useState } from 'react';
import { api, timeAgo } from '../api.js';
import { Icon } from '../App.jsx';

const CAMPUS_LOCATIONS = [
  'Hostel A', 'Hostel B', 'Hostel C', 'Main Gate', 'Library',
  'Cafeteria', 'Lecture Halls', 'School of Computing', 'Lab 5',
  'Administration Block', 'Sports Ground', 'Bus Park',
];

const CATEGORY_META = {
  water:      { icon: 'plumbing',          label: 'Water & Plumbing' },
  electrical: { icon: 'electrical_services',label: 'Electrical' },
  ict:        { icon: 'desktop_windows',   label: 'ICT' },
  security:   { icon: 'security',          label: 'Security' },
  roads:      { icon: 'add_road',          label: 'Roads & Grounds' },
  sanitation: { icon: 'cleaning_services', label: 'Sanitation' },
  other:      { icon: 'build',             label: 'General' },
};

const URGENCY_META = {
  critical: { cls: 'bg-error-container text-on-error-container', dot: 'bg-error' },
  high:     { cls: 'bg-error-container/60 text-error',           dot: 'bg-error' },
  medium:   { cls: 'bg-surface-container text-on-surface',       dot: 'bg-secondary' },
  low:      { cls: 'bg-surface-container text-on-surface-variant',dot: 'bg-outline' },
};

const AI_LABELS = { claude: 'Claude AI', 'claude-cli': 'Claude (CLI)', fallback: 'Keyword triage', seed: 'Seed data' };

// ── AI Triage Result Card ─────────────────────────────────────────────────────
function TriageResultCard({ data, onReset, onOpenIssue }) {
  const issue = data.issue || {};
  const cat = CATEGORY_META[issue.category] ?? CATEGORY_META.other;
  const urg = URGENCY_META[issue.urgency]   ?? URGENCY_META.medium;

  return (
    <div className="flex flex-col w-full pb-6 space-y-5 pt-4">
      {/* Header */}
      <div className="flex flex-col space-y-1">
        <div className="flex items-center space-x-2">
          <span className="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-primary-container text-on-primary shadow-sm">
            <Icon name={data.duplicate ? 'merge' : 'campaign'} className="text-[18px]" />
          </span>
          <h1 className="text-headline-lg font-bold text-on-surface tracking-tight">
            {data.duplicate ? 'Duplicate merged' : 'Issue logged'}
          </h1>
        </div>
        <p className="text-body-md text-on-surface-variant pl-9">{data.message}</p>
      </div>

      {/* AI Triage Result Card */}
      <div className="bg-surface-container-lowest rounded-2xl p-4 shadow-sm overflow-hidden space-y-3.5">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <div className="w-7 h-7 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
              <Icon name="auto_awesome" className="text-[18px]" fill />
            </div>
            <span className="text-headline-sm font-semibold text-on-surface tracking-tight">AI Triage Result</span>
          </div>
          <span className="flex items-center gap-1 px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface text-label-sm font-bold">
            <span className="w-2 h-2 rounded-full bg-primary animate-pulse" />
            {AI_LABELS[data.ai] || data.ai}
          </span>
        </div>

        {/* Badges */}
        <div className="flex flex-wrap items-center gap-2">
          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-container text-on-primary">
            <Icon name={cat.icon} className="text-[16px]" />
            <span className="text-label-md font-semibold">{cat.label}</span>
          </div>
          <div className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full ${urg.cls}`}>
            <span className={`w-2 h-2 rounded-full ${urg.dot}`} />
            <span className="text-label-md font-bold uppercase tracking-wider">{issue.urgency}</span>
          </div>
        </div>

        {/* Department */}
        <div className="flex items-start gap-2.5 p-3 rounded-xl bg-surface-container-low">
          <Icon name="forklift" className="text-[20px] text-primary mt-0.5" />
          <div className="flex flex-col min-w-0">
            <span className="text-label-sm font-bold text-on-surface-variant uppercase tracking-wider">Department Assignment</span>
            <span className="text-headline-sm font-semibold text-on-surface truncate">{issue.department}</span>
          </div>
        </div>

        {/* Summary */}
        <div className="flex items-start space-x-2.5 px-1">
          <Icon name="summarize" className="text-[18px] text-on-surface-variant mt-0.5" />
          <p className="text-body-md text-on-surface leading-snug">{issue.summary}</p>
        </div>

        {/* Language */}
        <div className="flex items-center justify-between pt-1">
          <div className="flex items-center gap-1.5 text-on-surface-variant">
            <Icon name="language" className="text-[16px] text-secondary" />
            <span className="text-label-md font-semibold">Detected: {issue.language} · {issue.confidence} confidence</span>
          </div>
          <span className="text-label-sm text-primary flex items-center gap-0.5 font-bold">
            <Icon name="done_all" className="text-[14px]" />
            Verified
          </span>
        </div>
      </div>

      {/* Actions */}
      <div className="flex gap-3">
        <button
          type="button"
          onClick={() => onOpenIssue(issue.id)}
          className="flex-1 h-12 bg-primary-container hover:bg-primary active:scale-[0.99] text-on-primary text-headline-sm font-semibold rounded-xl shadow-md flex items-center justify-center gap-2 transition-all"
        >
          <Icon name="open_in_new" className="text-[20px]" />
          View issue
        </button>
        <button
          type="button"
          onClick={onReset}
          className="flex-1 h-12 border border-outline/40 bg-surface-container-low text-on-surface text-headline-sm font-semibold rounded-xl flex items-center justify-center gap-2 transition-all hover:bg-surface-container active:scale-[0.99]"
        >
          <Icon name="add" className="text-[20px]" />
          Report another
        </button>
      </div>

      {/* Guarantee pill */}
      <div className="flex items-center justify-center space-x-2 py-1 text-center text-on-surface-variant">
        <Icon name="security" className="text-[16px] text-primary" />
        <span className="text-body-sm">Reports are shared immediately with on-duty technicians.</span>
      </div>
    </div>
  );
}

// ── Report Form ───────────────────────────────────────────────────────────────
export default function ReportPage({ onOpenIssue }) {
  const [location,       setLocation]       = useState('');
  const [customLocation, setCustomLocation] = useState('');
  const [description,    setDescription]    = useState('');
  const [photo,          setPhoto]          = useState(null);
  const [preview,        setPreview]        = useState(null);
  const [busy,           setBusy]           = useState(false);
  const [error,          setError]          = useState('');
  const [result,         setResult]         = useState(null);

  const effectiveLocation = (location === '__other' ? customLocation : location).trim();
  const canSubmit = effectiveLocation.length > 0 && (!!photo || description.trim().length >= 5) && !busy;

  function pickPhoto(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    if (file.size > 8 * 1024 * 1024) { setError('Photo must be 8 MB or smaller.'); return; }
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
      setError('Photo must be a JPEG, PNG, or WebP image.'); return;
    }
    setError('');
    setPhoto(file);
    setPreview(URL.createObjectURL(file));
  }

  function removePhoto() {
    if (preview) URL.revokeObjectURL(preview);
    setPreview(null);
    setPhoto(null);
  }

  function reset() {
    setResult(null); setError(''); setDescription('');
    removePhoto();
  }

  async function submit(e) {
    e.preventDefault();
    if (!canSubmit) return;
    setBusy(true); setError('');
    try {
      const data = await api.report({ location: effectiveLocation, description, photo });
      setResult(data);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  if (result) {
    return <TriageResultCard data={result} onReset={reset} onOpenIssue={onOpenIssue} />;
  }

  return (
    <div className="flex flex-col w-full pb-6 space-y-4 pt-1">
      {/* Title */}
      <div className="flex flex-col space-y-1">
        <div className="flex items-center space-x-2">
          <span className="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-primary-container text-on-primary shadow-sm">
            <Icon name="campaign" className="text-[18px]" />
          </span>
          <h1 className="text-headline-lg font-bold text-on-surface tracking-tight">Report an issue</h1>
        </div>
        <p className="text-body-md text-on-surface-variant pl-9">Snap it. Describe it. We'll route it.</p>
      </div>

      {/* Error */}
      {error && (
        <div className="p-3 rounded-xl bg-error-container text-on-error-container text-body-sm flex items-center justify-between gap-2">
          <span>{error}</span>
          <button type="button" onClick={() => setError('')}><Icon name="close" className="text-[16px]" /></button>
        </div>
      )}

      {/* Form card */}
      <form onSubmit={submit} className="bg-surface-container-lowest rounded-2xl p-4 shadow-sm flex flex-col space-y-4">

        {/* Upload zone */}
        <div className="relative group cursor-pointer transition-all duration-200 rounded-2xl p-5 text-center bg-surface-container-low hover:bg-surface-container flex flex-col items-center justify-center overflow-hidden">
          <input
            type="file"
            accept="image/jpeg,image/png,image/webp"
            onChange={pickPhoto}
            aria-label="Upload photo"
            className="absolute inset-0 opacity-0 cursor-pointer z-20"
          />
          {preview ? (
            <div className="relative w-full h-40 sm:h-52 max-h-[45vh] rounded-xl overflow-hidden shadow-inner">
              <img src={preview} alt="Selected photo preview" className="w-full h-full object-cover" />
              <div className="absolute bottom-2 left-2 bg-on-surface/80 backdrop-blur-md px-2.5 py-1 rounded-full flex items-center gap-1.5 text-surface">
                <Icon name="verified" className="text-[14px] text-primary-fixed" />
                <span className="text-label-sm font-bold">{photo ? `${(photo.size / 1024 / 1024).toFixed(1)} MB` : 'Photo attached'}</span>
              </div>
              <button
                type="button"
                onClick={(e) => { e.stopPropagation(); removePhoto(); }}
                aria-label="Remove photo"
                title="Remove photo"
                className="absolute top-2 right-2 w-9 h-9 rounded-full bg-surface-container-lowest/90 backdrop-blur-md flex items-center justify-center text-on-surface shadow-md hover:bg-surface-container-lowest z-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50"
              >
                <Icon name="close" className="text-[18px]" />
              </button>
            </div>
          ) : (
            <div className="flex flex-col items-center space-y-2 py-3">
              <div className="w-14 h-14 rounded-2xl bg-surface-container-high flex items-center justify-center text-primary transition-transform group-active:scale-95 shadow-sm">
                <Icon name="photo_camera" className="text-[32px]" />
              </div>
              <div className="flex flex-col space-y-0.5">
                <span className="text-headline-sm font-semibold text-on-surface">Tap to take or upload a photo</span>
                <span className="text-body-sm text-on-surface-variant flex items-center justify-center gap-1">
                  <Icon name="photo_library" className="text-[14px]" />
                  Camera snap or choose from gallery (max 8 MB)
                </span>
              </div>
            </div>
          )}
        </div>

        {/* Location */}
        <div className="flex flex-col space-y-1.5">
          <label className="text-label-lg font-semibold text-on-surface flex items-center gap-1.5" htmlFor="locationSelect">
            <Icon name="location_on" className="text-[18px] text-primary" />
            Location
          </label>
          <div className="relative w-full">
            <select
              id="locationSelect"
              value={location}
              onChange={(e) => setLocation(e.target.value)}
              required
              className="w-full h-12 appearance-none bg-surface-container-low text-on-surface text-body-md rounded-xl px-3.5 pr-10 focus:outline-none focus:bg-surface-container-lowest shadow-sm transition-colors"
            >
              <option value="">Select a location…</option>
              {CAMPUS_LOCATIONS.map((loc) => <option key={loc} value={loc}>{loc}</option>)}
              <option value="__other">Other (type below)</option>
            </select>
            <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-on-surface-variant">
              <Icon name="expand_more" className="text-[20px]" />
            </div>
          </div>
          {location === '__other' && (
            <input
              className="w-full h-12 bg-surface-container-low text-on-surface text-body-md rounded-xl px-3.5 focus:outline-none focus:bg-surface-container-lowest shadow-sm transition-colors"
              placeholder="e.g. Behind School of Education"
              maxLength={60}
              value={customLocation}
              onChange={(e) => setCustomLocation(e.target.value)}
              aria-label="Custom location"
            />
          )}
        </div>

        {/* Description */}
        <div className="flex flex-col space-y-1.5">
          <div className="flex items-start justify-between gap-2">
            <label className="text-label-lg font-semibold text-on-surface flex items-center gap-1.5 min-w-0" htmlFor="issueDescription">
              <Icon name="edit_note" className="text-[18px] text-primary" />
              Describe the problem
            </label>
            <span className="shrink-0 text-label-sm font-bold text-primary bg-on-primary-container px-2 py-0.5 rounded-full">
              AI Auto-Detect Active
            </span>
          </div>
          <textarea
            id="issueDescription"
            className="w-full bg-surface-container-low text-on-surface text-body-md rounded-xl p-3.5 focus:outline-none focus:bg-surface-container-lowest shadow-sm resize-none transition-colors"
            rows={4}
            maxLength={1000}
            placeholder='Describe the problem (English, Swahili or Sheng)'
            value={description}
            onChange={(e) => setDescription(e.target.value)}
          />
          <div className="flex items-center justify-between">
            <p className="text-body-sm text-on-surface-variant flex items-center gap-1 pt-0.5">
              <Icon name="translate" className="text-[14px] text-secondary" />
              Feel free to write in English, Kiswahili, or Sheng.
            </p>
            <span className="text-body-sm text-on-surface-variant">{description.length}/1000</span>
          </div>
        </div>

        {/* Submit */}
        <button
          type="submit"
          disabled={!canSubmit}
          className="w-full h-12 bg-primary-container hover:bg-primary active:scale-[0.99] text-on-primary text-headline-sm font-semibold rounded-xl shadow-md flex items-center justify-center space-x-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {busy ? (
            <>
              <Icon name="refresh" className="text-[20px] animate-spin" />
              <span>Transmitting to Estates…</span>
            </>
          ) : (
            <>
              <Icon name="send" className="text-[20px]" />
              <span>Submit Report</span>
            </>
          )}
        </button>
      </form>

      {/* Guarantee pill */}
      <div className="flex items-center justify-center space-x-2 py-1 text-center text-on-surface-variant">
        <Icon name="security" className="text-[16px] text-primary" />
        <span className="text-body-sm">Reports are encrypted &amp; shared immediately with on-duty technicians.</span>
      </div>
    </div>
  );
}
