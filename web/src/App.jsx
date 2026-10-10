import React, { useCallback, useEffect, useState } from 'react';
import { api, getAdminKey } from './api.js';
import ReportPage from './components/ReportPage.jsx';
import Dashboard from './components/Dashboard.jsx';
import IssueDetail from './components/IssueDetail.jsx';
import StaffSignIn from './components/StaffSignIn.jsx';
// Icons are inline SVG (see ./icons.jsx) — no icon-font/CDN dependency.
import { Icon } from './icons.jsx';

// ── Hash routing ──────────────────────────────────────────────────────────────
function parseHash() {
  const raw = window.location.hash.replace(/^#\/?/, '');
  const parts = raw.split('/').filter(Boolean);
  if (parts[0] === 'issues') return { name: 'dashboard' };
  if (parts[0] === 'issue' && parts[1]) return { name: 'issue', id: parts[1] };
  if (parts[0] === 'staff') return { name: 'staff' };
  return { name: 'report' };
}

function navigate(path) { window.location.hash = path; }

export { Icon, navigate };

// Height of the fixed header (4rem) plus the device safe-area inset, so page
// content is never hidden behind the header or the fixed bottom navigation.
const HEADER_OFFSET = 'calc(4rem + env(safe-area-inset-top, 0px))';
const NAV_OFFSET = 'calc(4.5rem + env(safe-area-inset-bottom, 0px))';

// ── App ───────────────────────────────────────────────────────────────────────
export default function App() {
  const [route, setRoute] = useState(parseHash);
  const [isStaff, setIsStaff] = useState(() => !!getAdminKey());

  useEffect(() => {
    const onHash = () => { setRoute(parseHash()); window.scrollTo(0, 0); };
    window.addEventListener('hashchange', onHash);
    if (!window.location.hash) window.location.hash = '#/';
    return () => window.removeEventListener('hashchange', onHash);
  }, []);

  const handleSignedIn  = useCallback(() => setIsStaff(true),  []);
  const handleSignedOut = useCallback(() => setIsStaff(false), []);

  const isReport    = route.name === 'report';
  const isDashboard = route.name === 'dashboard' || route.name === 'issue';

  return (
    <div className="bg-surface min-h-screen flex flex-col overflow-x-hidden">
      {/* ── Header ── */}
      <header className="fixed top-0 inset-x-0 z-50 pt-safe bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div className="h-16 px-gutter flex items-center justify-between gap-3">
          <button
            type="button"
            onClick={() => navigate('#/')}
            className="flex items-center gap-space-sm min-w-0 flex-1 text-left rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50"
            aria-label="FixMaseno home"
          >
            <div className="w-9 h-9 shrink-0 rounded-xl bg-primary flex items-center justify-center text-on-primary shadow-[0_2px_6px_rgba(0,100,55,0.2)]">
              <Icon name="account_balance" className="text-[20px]" />
            </div>
            <div className="flex flex-col min-w-0">
              <span className="text-headline-sm font-semibold text-on-surface tracking-tight leading-none truncate">FixMaseno</span>
              <span className="text-label-sm font-bold text-primary tracking-wide leading-none mt-space-xs truncate">MASENO UNIVERSITY</span>
            </div>
          </button>

          <button
            type="button"
            onClick={() => navigate('#/staff')}
            aria-label={isStaff ? 'Staff console — signed in' : 'Staff sign in'}
            title={isStaff ? 'Staff console' : 'Staff sign in'}
            aria-current={route.name === 'staff' ? 'page' : undefined}
            className="w-9 h-9 shrink-0 rounded-full bg-primary flex items-center justify-center text-on-primary transition-transform active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50"
          >
            <Icon name={isStaff ? 'shield' : 'person'} className="text-[18px]" fill={isStaff} />
          </button>
        </div>
      </header>

      {/* ── Main content ── */}
      <main
        className="flex-1 w-full bg-surface px-gutter flex flex-col"
        style={{ paddingTop: HEADER_OFFSET, paddingBottom: NAV_OFFSET }}
      >
        {route.name === 'report' && (
          <ReportPage onOpenIssue={(id) => navigate(`#/issue/${id}`)} />
        )}
        {route.name === 'dashboard' && (
          <Dashboard onOpenIssue={(id) => navigate(`#/issue/${id}`)} isStaff={isStaff} />
        )}
        {route.name === 'issue' && (
          <IssueDetail
            id={route.id}
            isStaff={isStaff}
            onBack={() => navigate('#/issues')}
          />
        )}
        {route.name === 'staff' && (
          <StaffSignIn
            isStaff={isStaff}
            onSignedIn={handleSignedIn}
            onSignedOut={handleSignedOut}
          />
        )}
      </main>

      {/* ── Bottom nav ── */}
      <nav className="fixed bottom-0 inset-x-0 z-50 pb-safe bg-surface/85 backdrop-blur-xl shadow-[0_-2px_12px_rgba(0,0,0,0.05)]">
        <div className="h-16 px-gutter flex items-center justify-around">
          <button
            type="button"
            onClick={() => navigate('#/')}
            aria-current={isReport ? 'page' : undefined}
            className={`flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-space-xs px-space-md rounded-xl transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 ${
              isReport ? 'text-primary-container font-semibold' : 'text-on-surface-variant hover:text-on-surface'
            }`}
          >
            <Icon name="add_a_photo" className="text-[26px]" />
            <span className="text-label-md font-semibold mt-0.5">Report</span>
          </button>

          <button
            type="button"
            onClick={() => navigate('#/issues')}
            aria-current={isDashboard ? 'page' : undefined}
            className={`flex flex-col items-center justify-center min-w-[56px] min-h-[48px] py-space-xs px-space-md rounded-xl transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 ${
              isDashboard ? 'text-primary-container font-semibold' : 'text-on-surface-variant hover:text-on-surface'
            }`}
          >
            <Icon name="fact_check" className="text-[26px]" />
            <span className="text-label-md font-semibold mt-0.5">Issues</span>
          </button>
        </div>
      </nav>
    </div>
  );
}
