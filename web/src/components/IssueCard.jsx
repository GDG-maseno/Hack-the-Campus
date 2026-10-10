import React from 'react';
import { timeAgo } from '../api.js';
import { Icon } from '../App.jsx';

const CATEGORY_META = {
  water:      { icon: 'water_drop',       label: 'Water',      cls: 'bg-secondary-container text-on-secondary-container' },
  electrical: { icon: 'bolt',             label: 'Electrical', cls: 'bg-tertiary-fixed text-on-tertiary-fixed' },
  ict:        { icon: 'desktop_windows',  label: 'ICT',        cls: 'bg-surface-container-high text-tertiary' },
  security:   { icon: 'security',         label: 'Security',   cls: 'bg-tertiary-fixed text-on-tertiary-fixed' },
  roads:      { icon: 'add_road',         label: 'Roads',      cls: 'bg-surface-container text-on-surface-variant' },
  sanitation: { icon: 'cleaning_services',label: 'Sanitation', cls: 'bg-surface-variant text-on-surface' },
  other:      { icon: 'build',            label: 'Other',      cls: 'bg-surface-container text-on-surface-variant' },
};

const URGENCY_META = {
  critical: { cls: 'bg-error-container text-on-error-container', dot: 'bg-error' },
  high:     { cls: 'bg-error-container/60 text-error',           dot: 'bg-error' },
  medium:   { cls: 'bg-surface-container text-on-surface',       dot: 'bg-secondary' },
  low:      { cls: 'bg-surface-container text-on-surface-variant',dot: 'bg-outline' },
};

const STATUS_META = {
  open:        { label: 'Open',        cls: 'bg-surface-container-high text-on-surface-variant', icon: null },
  in_progress: { label: 'In progress', cls: 'bg-secondary-container text-on-secondary-container', icon: 'refresh' },
  resolved:    { label: 'Resolved',    cls: 'bg-primary-fixed text-on-primary-fixed-variant',    icon: 'done_all' },
};

export default function IssueCard({ issue, busy, onUpvote, onOpen }) {
  const cat = CATEGORY_META[issue.category] ?? CATEGORY_META.other;
  const urg = URGENCY_META[issue.urgency]   ?? URGENCY_META.medium;
  const sta = STATUS_META[issue.status]     ?? STATUS_META.open;

  return (
    <article
      className="flex flex-col p-4 rounded-2xl bg-surface-container-lowest shadow-sm transition-all hover:shadow-md cursor-pointer"
      onClick={() => onOpen(issue.id)}
    >
      <div className="flex items-start gap-3">
        {/* Photo thumbnail */}
        <div className="relative w-20 h-20 rounded-xl overflow-hidden shrink-0 bg-surface-container shadow-inner">
          {issue.photo_url ? (
            <img src={issue.photo_url} alt={issue.title} className="w-full h-full object-cover" />
          ) : (
            <div className="w-full h-full flex items-center justify-center text-on-surface-variant/40">
              <Icon name={cat.icon} className="text-[28px]" />
            </div>
          )}
          {issue.photo_url && (
            <span className="absolute bottom-1 right-1 bg-on-surface/75 text-surface-container-lowest text-[10px] font-bold px-1 rounded flex items-center gap-0.5">
              <Icon name="photo_camera" className="text-[10px]" />
            </span>
          )}
          {issue.status === 'resolved' && (
            <span className="absolute bottom-1 right-1 bg-primary text-on-primary text-[10px] font-bold px-1 rounded flex items-center gap-0.5">
              <Icon name="check" className="text-[10px]" />
            </span>
          )}
        </div>

        {/* Issue summary */}
        <div className="flex-1 min-w-0">
          <div className="flex items-center justify-between gap-1 mb-1">
            <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-label-sm font-bold ${cat.cls}`}>
              <Icon name={cat.icon} className="text-[12px]" />
              {cat.label}
            </span>
            <span className="text-body-sm text-on-surface-variant shrink-0">{timeAgo(issue.created_at)}</span>
          </div>
          <h2 className="text-headline-sm font-semibold text-on-surface truncate tracking-tight">{issue.title}</h2>
          <div className="flex items-center gap-1 text-on-surface-variant mt-1">
            <Icon name="location_on" className="text-[15px] text-primary shrink-0" />
            <span className="text-body-sm truncate">{issue.location}</span>
          </div>
        </div>
      </div>

      {/* Card footer */}
      <div
        className="flex items-center justify-between mt-3 pt-3 bg-surface-container-low/50 -mx-4 -mb-4 px-4 py-2.5 rounded-b-2xl"
        onClick={(e) => e.stopPropagation()}
      >
        <div className="flex items-center gap-2">
          {/* Urgency badge */}
          <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-label-sm font-bold ${urg.cls}`}>
            <span className={`w-1.5 h-1.5 rounded-full ${urg.dot}`} />
            {issue.urgency.charAt(0).toUpperCase() + issue.urgency.slice(1)}
          </span>
          {/* Status badge */}
          <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-semibold ${sta.cls}`}>
            {sta.icon && <Icon name={sta.icon} className={`text-[12px] ${issue.status === 'in_progress' ? 'animate-spin' : ''}`} />}
            {sta.label}
          </span>
        </div>

        {/* Upvote pill */}
        <button
          type="button"
          disabled={busy}
          onClick={(e) => { e.stopPropagation(); onUpvote(issue.id); }}
          className={`flex items-center gap-1.5 px-3 py-1 rounded-full text-label-md font-semibold transition-all active:scale-95 shadow-sm disabled:opacity-50 ${
            issue.voted
              ? 'bg-primary-fixed text-on-primary-fixed-variant'
              : 'bg-surface-container hover:bg-primary-fixed text-on-surface'
          }`}
        >
          <Icon name="arrow_upward" className="text-[16px] text-primary" />
          <span>+{issue.upvotes}</span>
        </button>
      </div>
    </article>
  );
}
