import React from 'react';

// ── Inline SVG icon set ───────────────────────────────────────────────────────
// The Stitch export used Material Symbols ligatures loaded from the Google Fonts
// CDN (`<span class="material-symbols-outlined">location_on</span>`). When that
// CDN is unreachable the ligature names render as literal text, which is the
// defect in the review screenshot. These are equivalent 24×24 line icons drawn
// inline, so they render identically offline and can never leak a ligature name.
//
// Sizing: every icon is width/height 1em, so the existing `text-[20px]` utility
// classes in the components keep controlling the rendered size.
// Colour: stroke/fill use currentColor, so Tailwind text-* classes apply.

const P = ({ d, ...rest }) => <path d={d} {...rest} />;

const ICONS = {
  // ── Navigation / chrome ──
  account_balance: (
    <>
      <P d="M3 10 12 4l9 6" />
      <P d="M5 10v9M9.5 10v9M14.5 10v9M19 10v9" />
      <P d="M2.5 21h19" />
    </>
  ),
  person: (
    <>
      <circle cx="12" cy="8" r="3.6" />
      <P d="M5 20a7 7 0 0 1 14 0" />
    </>
  ),
  add_a_photo: (
    <>
      <P d="M3 9a2 2 0 0 1 2-2h1.8L8.3 5h6l1.5 2H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
      <circle cx="11.5" cy="13" r="3.2" />
      <P d="M20.5 3v4M18.5 5h4" />
    </>
  ),
  fact_check: (
    <>
      <P d="M9 4H7a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2" />
      <P d="M9 3.5h6v2.5H9z" />
      <P d="M8.5 12l1.8 1.8L14 10" />
      <P d="M8.5 17.5h7" />
    </>
  ),
  chevron_left: <P d="M15 5l-7 7 7 7" />,
  expand_more: <P d="M6 9.5l6 6 6-6" />,
  close: (
    <>
      <P d="M6 6l12 12" />
      <P d="M18 6L6 18" />
    </>
  ),
  add: <P d="M12 5v14M5 12h14" />,
  refresh: (
    <>
      <P d="M20.5 12a8.5 8.5 0 1 1-2.5-6" />
      <P d="M20.5 4.5v5h-5" />
    </>
  ),
  open_in_new: (
    <>
      <P d="M14 3.5h6.5V10" />
      <P d="M20.5 3.5 12 12" />
      <P d="M17.5 14v4.5a2 2 0 0 1-2 2h-10a2 2 0 0 1-2-2v-10a2 2 0 0 1 2-2H9" />
    </>
  ),
  logout: (
    <>
      <P d="M16 17l5-5-5-5" />
      <P d="M21 12H9" />
      <P d="M13 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8" />
    </>
  ),
  login: (
    <>
      <P d="M14 12H3" />
      <P d="M10 8l4 4-4 4" />
      <P d="M11 3.5h6a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2h-6" />
    </>
  ),

  // ── Status / marks ──
  check: <P d="M5 13l4.5 4.5L19 7" />,
  check_circle: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <P d="M8.2 12.3l2.7 2.7 5-5.2" />
    </>
  ),
  task_alt: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <P d="M8.2 12.3l2.7 2.7 5-5.2" />
    </>
  ),
  done_all: (
    <>
      <P d="M2.5 12.8 5.5 16l6.5-6.8" />
      <P d="M10.5 15.2 12 16.8l8-8.4" />
    </>
  ),
  verified: (
    <>
      <P d="M12 2.8l2.3 1.6 2.8-.3 1.1 2.6 2.4 1.4-.6 2.8.6 2.8-2.4 1.4-1.1 2.6-2.8-.3L12 21.2l-2.3-1.6-2.8.3-1.1-2.6-2.4-1.4.6-2.8-.6-2.8 2.4-1.4 1.1-2.6 2.8.3z" />
      <P d="M8.6 12.2l2.4 2.4 4.6-4.8" />
    </>
  ),
  schedule: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <P d="M12 7v5.3l3.4 2" />
    </>
  ),
  lock_reset: (
    <>
      <P d="M5.5 11h13a1.5 1.5 0 0 1 1.5 1.5v6a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 18.5v-6A1.5 1.5 0 0 1 5.5 11z" />
      <P d="M8 11V8a4 4 0 0 1 7.5-1.9" />
      <P d="M12 15v1.8" />
    </>
  ),
  key: (
    <>
      <circle cx="8" cy="15.5" r="3.4" />
      <P d="M10.4 13.1 19 4.5" />
      <P d="M16.5 3.5 20.5 7.5" />
      <P d="M14 6 16.5 8.5" />
    </>
  ),
  inbox: (
    <>
      <P d="M4 13.5 6.4 5.4A2 2 0 0 1 8.3 4h7.4a2 2 0 0 1 1.9 1.4L20 13.5v4.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z" />
      <P d="M4 13.5h4l1.2 2.5h5.6L16 13.5h4" />
    </>
  ),

  // ── Security / staff ──
  shield: <P d="M12 3l7 2.8v5.6c0 4.4-2.9 7.5-7 9.1-4.1-1.6-7-4.7-7-9.1V5.8z" />,
  security: (
    <>
      <P d="M12 3l7 2.8v5.6c0 4.4-2.9 7.5-7 9.1-4.1-1.6-7-4.7-7-9.1V5.8z" />
      <P d="M8.8 12l2.3 2.3 4.1-4.4" />
    </>
  ),

  // ── Media ──
  photo_camera: (
    <>
      <P d="M3 9a2 2 0 0 1 2-2h1.8L8.3 5h7.4l1.5 2H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
      <circle cx="12" cy="13" r="3.4" />
    </>
  ),
  photo_library: (
    <>
      <P d="M8 3.5h11a1.5 1.5 0 0 1 1.5 1.5v11" />
      <P d="M5 7h11a1.5 1.5 0 0 1 1.5 1.5v11A1.5 1.5 0 0 1 16 21H5a1.5 1.5 0 0 1-1.5-1.5v-11A1.5 1.5 0 0 1 5 7z" />
      <P d="M6.5 17.5l3.2-4 2.4 3 1.6-1.8 2.3 2.8" />
    </>
  ),

  // ── Location / language ──
  location_on: (
    <>
      <P d="M12 21s6.8-6.2 6.8-11A6.8 6.8 0 0 0 5.2 10c0 4.8 6.8 11 6.8 11z" />
      <circle cx="12" cy="10" r="2.6" />
    </>
  ),
  language: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <P d="M3.6 12h16.8" />
      <P d="M12 3.4a13 13 0 0 1 0 17.2" />
      <P d="M12 3.4a13 13 0 0 0 0 17.2" />
    </>
  ),
  translate: (
    <>
      <P d="M3.5 6.5h9" />
      <P d="M8 4.5v2" />
      <P d="M11 6.5c-.8 4.2-3.6 7.6-7 9" />
      <P d="M4 6.5c.6 3.6 3.4 6.8 6.8 8.2" />
      <P d="M12.8 20.5l3.7-9.5 3.7 9.5" />
      <P d="M14.3 17h4.4" />
    </>
  ),

  // ── Writing / reporting ──
  edit_note: (
    <>
      <P d="M12 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-6" />
      <P d="M17.4 3.6a1.9 1.9 0 0 1 2.7 2.7L12.6 13.8 9 14.7l.9-3.6z" />
    </>
  ),
  record_voice_over: (
    <>
      <circle cx="9.5" cy="8" r="3.2" />
      <P d="M3.5 19.5a6 6 0 0 1 12 0" />
      <P d="M17.5 9.5a4.5 4.5 0 0 1 0 5" />
      <P d="M20 7a8 8 0 0 1 0 10" />
    </>
  ),
  summarize: (
    <>
      <P d="M4 7h16" />
      <P d="M4 12h16" />
      <P d="M4 17h9" />
    </>
  ),
  campaign: (
    <>
      <P d="M4 10.2v3.6h3.2L17 18.6V5.4L7.2 10.2z" />
      <P d="M19.5 9.5a3.6 3.6 0 0 1 0 5" />
      <P d="M7.2 13.8v4.4a1.6 1.6 0 0 0 3.2 0" />
    </>
  ),
  send: (
    <>
      <P d="M21 3.5 11 13.6" />
      <P d="M21 3.5 14.6 20.5l-3.6-6.9-6.9-3.6z" />
    </>
  ),

  // ── AI / triage ──
  auto_awesome: (
    <>
      <P d="M10.5 3.5 12 8l4.5 1.5L12 11l-1.5 4.5L9 11 4.5 9.5 9 8z" />
      <P d="M17.5 13.5l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8z" />
    </>
  ),
  smart_toy: (
    <>
      <P d="M12 3.5v3" />
      <P d="M6 6.5h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2z" />
      <circle cx="9.5" cy="12" r="1.2" fill="currentColor" stroke="none" />
      <circle cx="14.5" cy="12" r="1.2" fill="currentColor" stroke="none" />
      <P d="M9 16h6" />
    </>
  ),
  merge: (
    <>
      <P d="M6.5 5.5v6.2a3.6 3.6 0 0 0 3.6 3.6h8" />
      <P d="M14.5 11.6l3.9 3.9-3.9 3.9" />
      <circle cx="6.5" cy="4" r="1.8" />
      <circle cx="6.5" cy="20.5" r="1.8" />
    </>
  ),
  forklift: (
    <>
      <P d="M6.5 3.5v11" />
      <P d="M9 6.5h5v7H9z" />
      <P d="M9 13.5h9.5" />
      <circle cx="6" cy="18.5" r="2" />
      <circle cx="14.5" cy="18.5" r="2" />
    </>
  ),
  engineering: (
    <>
      <P d="M2.5 15.5h19" />
      <P d="M5.5 15.5a6.5 6.5 0 0 1 13 0" />
      <P d="M11 9.2V6.5h2v2.7" />
      <P d="M13.5 18.5v2M10.5 18.5v2" />
    </>
  ),

  // ── Category glyphs ──
  water_drop: <P d="M12 3.2s6.2 6.4 6.2 10.3A6.2 6.2 0 0 1 5.8 13.5C5.8 9.6 12 3.2 12 3.2z" />,
  bolt: <P d="M13.4 2.5 5.6 13.4h5.2l-1.2 8.1 7.8-10.9h-5.2z" />,
  desktop_windows: (
    <>
      <P d="M4 5h16a1.5 1.5 0 0 1 1.5 1.5v9A1.5 1.5 0 0 1 20 17H4a1.5 1.5 0 0 1-1.5-1.5v-9A1.5 1.5 0 0 1 4 5z" />
      <P d="M9 20.5h6" />
      <P d="M12 17v3.5" />
    </>
  ),
  electrical_services: (
    <>
      <P d="M13 3.5 6.5 12h4.3l-1 8L17 10.8h-4.5z" />
      <P d="M20.5 5.5v13" />
    </>
  ),
  add_road: (
    <>
      <P d="M6 3.5 3.5 20.5" />
      <P d="M18 3.5l2.5 17" />
      <P d="M12 4v3.5M12 11v3M12 18v3" />
    </>
  ),
  cleaning_services: (
    <>
      <P d="M14.5 3.5l6 6" />
      <P d="M12.8 8.2 6.4 14.6a2 2 0 0 0 0 2.8l.2.2a2 2 0 0 0 2.8 0l6.4-6.4z" />
      <P d="M3.5 20.5h11" />
      <P d="M6.5 20.5 5 17" />
      <P d="M12 20.5 13.5 17" />
    </>
  ),
  build: (
    <P d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.5-3.5a6 6 0 0 1-7.9 7.9l-6.6 6.6a2.1 2.1 0 0 1-3-3l6.6-6.6a6 6 0 0 1 7.9-7.9z" />
  ),
  plumbing: (
    <>
      <P d="M4.5 20.5 11 14" />
      <P d="M9.4 11.2a4.6 4.6 0 0 1 6.5-6.5l3.2 3.2-3.9 3.9z" />
      <P d="M14.5 12.5 11.8 15.2" />
    </>
  ),
  help: (
    <>
      <circle cx="12" cy="12" r="8.6" />
      <P d="M9.6 9.4a2.5 2.5 0 1 1 3.4 2.3c-.7.3-1 .8-1 1.5v.4" />
      <circle cx="12" cy="16.6" r="0.9" fill="currentColor" stroke="none" />
    </>
  ),
};

// Names that read better as a solid glyph when a component asks for `fill`.
const FILLED = {
  arrow_upward: <path d="M12 4.5 19 12h-4.2v7.5h-5.6V12H5z" fill="currentColor" stroke="none" />,
};

const ARROW_UP_STROKE = (
  <>
    <P d="M12 20V5" />
    <P d="M6 11l6-6 6 6" />
  </>
);

ICONS.arrow_upward = ARROW_UP_STROKE;

/**
 * Renders an inline SVG icon. Unknown names fall back to a help glyph so the
 * UI can never display a raw Material ligature identifier.
 */
export function Icon({ name, className = '', fill = false, title }) {
  const body = fill && FILLED[name] ? FILLED[name] : ICONS[name] || ICONS.help;
  const strokeWidth = fill && !FILLED[name] ? 2.3 : 1.8;

  return (
    <svg
      viewBox="0 0 24 24"
      width="1em"
      height="1em"
      className={`inline-block shrink-0 align-middle ${className}`}
      fill="none"
      stroke="currentColor"
      strokeWidth={strokeWidth}
      strokeLinecap="round"
      strokeLinejoin="round"
      role={title ? 'img' : undefined}
      aria-label={title || undefined}
      aria-hidden={title ? undefined : 'true'}
      focusable="false"
    >
      {title ? <title>{title}</title> : null}
      {body}
    </svg>
  );
}

export { ICONS };
