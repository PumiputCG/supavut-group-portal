<?php
require_once dirname(__FILE__) . '/announcement_helpers.php';
$announcements = announcement_read_all();

// ── สแกนรูปจากโฟลเดอร์ img/gallery (วางไฟล์ชื่อ 1, 2, 3, ... แล้วขึ้นเองทั้ง gallery และ hero) ──
// เขียนแบบรองรับ PHP 5.2 (เซิร์ฟบริษัทใช้ PHP 5.2.6)
$galleryDir = dirname(__FILE__) . '/img/gallery';
$galleryImages = array();
if (is_dir($galleryDir)) {
  $allowedExt = array('jpg', 'jpeg', 'png', 'webp', 'gif');
  $filtered = array();
  $files = scandir($galleryDir);
  if ($files !== false) {
    foreach ($files as $file) {
      $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      if (in_array($ext, $allowedExt)) {
        $filtered[] = $file;
      }
    }
  }
  natcasesort($filtered); // เรียงตามเลขแบบธรรมชาติ 1,2,...,9,10,11
  foreach ($filtered as $file) {
    $galleryImages[] = 'img/gallery/' . rawurlencode($file);
  }
  $galleryImages = array_values($galleryImages);
}
$heroImage = (count($galleryImages) > 0) ? $galleryImages[0] : 'img/gallery/1.jpg';
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SUPAVUT GROUP</title>
  <link href="./src/output.css?v=<?php echo time(); ?>" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/3si.png">
  <link rel="apple-touch-icon" href="./img/pwa/icon-192.png">
  <link rel="manifest" href="./site.webmanifest">
  <meta name="theme-color" content="#ffffff">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<style>
  :root {
    --bg: #eef2f7;
    --surface: #ffffff;
    --surface-soft: #f7f9fc;
    --ink: #10172a;
    --muted: #53617a;
    --faint: #8490a5;
    --line: #e4e9f1;
    --line-strong: #cbd4e2;
    --accent: #4f46e5;
    --accent-soft: #eef2ff;
    --success: #00a85a;
    --warning: #f59e0b;
    --danger: #dc2626;
    --news-wine: #8d2b3a;
    --news-wine-dark: #6f1f2c;
    --news-gold: #d9c374;
    --news-gold-soft: #f8f1d7;
    --shadow: 0 14px 42px rgba(16, 23, 42, 0.07);
    --radius: 12px;
    --zone: #eaf3fe;
    --zone-line: #d4e6fa;
    --page-x: clamp(14px, 1.6vw, 28px);
    --page-y: clamp(14px, 1.6vw, 28px);
  }

  * {
    box-sizing: border-box;
  }

  html {
    scroll-behavior: smooth;
  }

  body {
    min-height: 100vh;
    margin: 0;
    background:
      linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(238, 242, 247, 0.96)),
      var(--bg);
    color: var(--ink);
    font-family: "Inter", "Noto Sans Thai", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
  }

  button,
  input,
  select,
  textarea {
    font: inherit;
  }

  button,
  a {
    -webkit-tap-highlight-color: transparent;
  }

  button:focus-visible,
  a:focus-visible,
  input:focus-visible {
    outline: 3px solid rgba(79, 70, 229, 0.26);
    outline-offset: 3px;
  }

  .page-shell {
    width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 0 var(--page-x) var(--page-y);
    background: rgba(255, 255, 255, 0.92);
  }

  /* ── Topbar ─────────────────────────────────────── */
  .topbar {
    display: grid;
    grid-template-columns: minmax(220px, 300px) minmax(280px, 1fr) auto;
    align-items: center;
    gap: 20px;
    min-height: 74px;
    margin: 0 calc(var(--page-x) * -1);
    padding: 10px var(--page-x);
    border-bottom: 1px solid rgba(228, 233, 241, 0.96);
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 8px 18px rgba(16, 23, 42, 0.06);
    position: relative;
    z-index: 5;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 13px;
  }

  .brand-logo {
    width: 54px;
    height: 54px;
    flex: 0 0 auto;
    object-fit: contain;
  }

  .brand-text {
    min-width: 0;
  }

  .brand-title {
    margin: 0;
    display: grid;
    gap: 1px;
    line-height: 1;
  }

  .brand-title-main {
    color: #064ba6;
    font-size: 1.65rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .brand-title-sub {
    color: #168642;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.44em;
    text-transform: uppercase;
    text-align: center;
  }

  .search-wrap {
    position: relative;
    max-width: 1040px;
    width: 100%;
    margin: 0 auto;
  }

  .search-wrap > svg {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    color: var(--muted);
    pointer-events: none;
  }

  .search-input {
    width: 100%;
    min-height: 50px;
    padding: 0 58px 0 52px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface);
    color: var(--ink);
    box-shadow: 0 12px 32px rgba(16, 23, 42, 0.07);
    transition: border-color 180ms ease, box-shadow 180ms ease;
  }

  .search-input::placeholder {
    color: #94a0b5;
  }

  .search-input:hover,
  .search-input:focus {
    border-color: var(--line-strong);
    box-shadow: 0 16px 38px rgba(16, 23, 42, 0.1);
  }

  .search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    width: 36px;
    height: 36px;
    transform: translateY(-50%);
    display: none;
    place-items: center;
    border: 0;
    border-radius: 50%;
    background: var(--accent-soft);
    color: var(--accent);
    cursor: pointer;
  }

  .search-clear.is-visible {
    display: grid;
  }

  .top-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    justify-self: end;
  }

  .announcement-admin-btn,
  .language-trigger {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 50px;
    padding: 0 14px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface);
    color: var(--ink);
    font-weight: 600;
    font-size: 0.9rem;
    box-shadow: 0 10px 24px rgba(16, 23, 42, 0.06);
    cursor: pointer;
    transition: background 180ms ease, border-color 180ms ease, color 180ms ease;
  }

  .announcement-admin-btn {
    width: 44px;
    min-height: 44px;
    justify-content: center;
    padding: 0;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: var(--muted);
    text-decoration: none;
    box-shadow: none;
  }

  .announcement-admin-btn:hover {
    background: transparent;
    color: var(--accent);
    transform: translateY(-1px);
  }

  .language-menu {
    position: relative;
  }

  .language-trigger:hover,
  .language-trigger[aria-expanded="true"] {
    background: var(--accent-soft);
    border-color: rgba(79, 70, 229, 0.26);
    color: var(--accent);
  }

  .language-trigger .lang-code {
    letter-spacing: 0.04em;
  }

  .language-trigger .chev {
    transition: transform 180ms ease;
  }

  .language-trigger[aria-expanded="true"] .chev {
    transform: rotate(180deg);
  }

  .language-panel {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    z-index: 10;
    min-width: 180px;
    display: none;
    padding: 6px;
    border: 1px solid var(--line);
    border-radius: 14px;
    background: var(--surface);
    box-shadow: 0 18px 38px rgba(16, 23, 42, 0.14);
  }

  .language-panel.is-open {
    display: grid;
    gap: 3px;
  }

  .lang-btn {
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border: 0;
    border-radius: 9px;
    background: transparent;
    color: var(--muted);
    padding: 0 12px;
    font-weight: 600;
    cursor: pointer;
    text-align: left;
  }

  .lang-btn:hover,
  .lang-btn.is-active {
    background: var(--accent-soft);
    color: var(--accent);
  }

  .lang-btn .lang-code {
    font-size: 0.78rem;
    color: var(--faint);
  }

  .lang-btn.is-active .lang-code {
    color: var(--accent);
  }

  /* ── Dashboard layout ───────────────────────────── */
  .dashboard-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 18px;
    margin-top: 20px;
  }

  .main-panel {
    display: grid;
    align-content: start;
    gap: 18px;
  }

  .widget,
  .hero-panel,
  .app-card {
    border: 1px solid var(--line);
    background: var(--surface);
    border-radius: var(--radius);
  }

  /* ── Hero ───────────────────────────────────────── */
  .hero-panel {
    position: relative;
    min-height: 260px;
    display: flex;
    align-items: center;
    padding: clamp(22px, 2.2vw, 36px);
    overflow: hidden;
    background: linear-gradient(180deg, #fbfcfe, #f6f8fc);
  }

  .hero-copy {
    position: relative;
    z-index: 1;
    max-width: 460px;
  }

  .welcome {
    display: inline-flex;
    margin-bottom: 12px;
    color: var(--accent);
    font-weight: 600;
    font-size: 0.88rem;
  }

  .hero-title {
    margin: 0;
    max-width: 380px;
    font-size: clamp(1.5rem, 2.4vw, 2.1rem);
    line-height: 1.18;
    font-weight: 700;
    letter-spacing: -0.03em;
    text-wrap: balance;
  }

  .hero-text {
    margin: 14px 0 0;
    color: var(--muted);
    font-size: 0.92rem;
    line-height: 1.6;
    white-space: pre-line;
    max-width: 42ch;
  }

  /* ── Hero visual: company photo fading in behind the copy ── */
  .hero-visual {
    position: absolute;
    inset: 0;
    overflow: hidden;
  }

  .hero-photo {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center right;
    transition: opacity 600ms ease;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 50%);
    mask-image: linear-gradient(to right, transparent 0%, #000 50%);
  }

  .hero-visual::after {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(to right, #f6f8fc 0%, rgba(246, 248, 252, 0.55) 34%, rgba(246, 248, 252, 0) 60%);
  }

  /* ── Applications ───────────────────────────────── */
  .app-header {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 14px;
    margin: 4px 2px 0;
  }

  .section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: -0.02em;
  }

  .category-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .tab-btn {
    min-height: 34px;
    padding: 0 14px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface);
    color: var(--muted);
    font-size: 0.84rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 160ms ease, color 160ms ease, border-color 160ms ease;
  }

  .tab-btn:hover {
    border-color: var(--line-strong);
    color: var(--ink);
  }

  .tab-btn.is-active {
    border-color: var(--accent);
    background: var(--accent);
    color: #fff;
  }

  .dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.1);
  }

  .app-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
    margin-top: 16px;
  }

  .app-card {
    position: relative;
    min-height: 112px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 14px;
    text-decoration: none;
    color: inherit;
    overflow: hidden;
    transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
  }

  .app-card:hover {
    transform: translateY(-4px);
    border-color: var(--line-strong);
    box-shadow: 0 18px 36px rgba(16, 23, 42, 0.1);
  }

  .app-card.is-disabled {
    cursor: default;
  }

  .app-card.is-disabled:hover {
    transform: none;
  }

  .app-card.is-hidden {
    display: none;
  }

  .app-icon {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    background: var(--surface-soft);
    border: 1px solid #edf1f7;
    border-radius: 11px;
  }

  .app-icon img {
    max-width: 26px;
    max-height: 26px;
    object-fit: contain;
  }

  .app-name {
    margin: 0;
    color: var(--ink);
    font-size: 0.86rem;
    font-weight: 700;
    line-height: 1.28;
  }

  .app-desc {
    display: -webkit-box;
    margin: 2px 0 0;
    overflow: hidden;
    color: var(--muted);
    font-size: 0.72rem;
    line-height: 1.4;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
  }

  .open-arrow {
    position: absolute;
    right: 12px;
    bottom: 12px;
    min-width: 16px;
    display: inline-grid;
    place-items: center;
    color: var(--muted);
    font-size: 1.2rem;
    font-weight: 400;
    line-height: 1;
    transition: color 180ms ease, transform 180ms ease;
  }

  .app-card:hover .open-arrow {
    color: var(--ink);
    transform: translateX(3px);
  }

  .empty-state {
    display: none;
    margin-top: 16px;
    padding: 16px;
    border: 1px dashed var(--line-strong);
    border-radius: var(--radius);
    background: var(--surface-soft);
    color: var(--muted);
    text-align: center;
  }

  .empty-state.is-visible {
    display: block;
  }

  .app-footer {
    display: flex;
    justify-content: center;
    margin-top: 22px;
  }

  .view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 0;
    background: transparent;
    color: var(--accent);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
  }

  .view-all-link:hover {
    text-decoration: underline;
  }

  /* ── Sidebar ────────────────────────────────────── */
  .side-stack {
    display: grid;
    gap: 18px;
    align-content: start;
    padding: 18px;
    background: var(--zone);
    border: 1px solid var(--zone-line);
    border-radius: 20px;
  }

  .widget {
    padding: 16px;
  }

  .mini-widgets-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .widget-compact {
    padding: 12px;
  }

  .widget-compact .clock {
    margin-bottom: 8px;
    padding-bottom: 8px;
  }

  .widget-compact .clock-time {
    font-size: 1.15rem;
  }

  .widget-compact .widget-head {
    margin-bottom: 8px;
  }

  .widget-compact .widget-title {
    font-size: 0.82rem;
  }

  .widget-compact .widget-link {
    font-size: 0.74rem;
  }

  .widget-compact .calendar-nav {
    margin-bottom: 6px;
  }

  .widget-compact .calendar-grid {
    gap: 2px;
    font-size: 0.68rem;
  }

  .widget-compact .calendar-grid span {
    min-height: 22px;
  }

  .widget-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
  }

  .widget-title {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0;
    font-size: 0.94rem;
    font-weight: 700;
  }

  .mini-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--success);
  }

  .mini-dot.blue {
    background: #0f7df2;
  }

  .mini-dot.purple {
    background: #a855f7;
  }

  .mini-dot.orange {
    background: var(--warning);
  }

  .widget-link {
    display: inline-flex;
    align-items: center;
    border: 0;
    background: transparent;
    color: var(--accent);
    font-size: 0.82rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
  }

  .announcement-widget {
    overflow: hidden;
    background:
      linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 247, 241, 0.72)),
      var(--surface);
  }

  .announcement-widget .widget-head {
    align-items: flex-start;
    margin-bottom: 12px;
  }

  .announcement-heading {
    display: grid;
    gap: 4px;
  }

  .announcement-title-line {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .news-orb {
    width: 17px;
    height: 17px;
    flex: 0 0 auto;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--news-wine), var(--news-gold));
  }

  .announcement-widget-title {
    margin: 0;
    color: var(--ink);
    font-size: 1.02rem;
    font-weight: 800;
    letter-spacing: 0.14em;
  }

  .announcement-heading-sub {
    margin: 0 0 0 27px;
    color: var(--muted);
    font-size: 0.76rem;
    line-height: 1.45;
  }

  .announcement-widget .widget-link {
    color: var(--news-wine);
    font-weight: 800;
  }

  .announcement-list {
    display: grid;
    gap: 10px;
  }

  .announcement-widget-full .announcement-widget-title {
    font-size: 1.1rem;
  }

  .announcement-grid-9 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    align-content: start;
  }

  .announcement-carousel {
    position: relative;
    padding: 0 30px;
  }

  .announcement-carousel.no-nav {
    padding: 0;
  }

  .widget-carousel-arrow {
    position: absolute;
    top: 50%;
    left: 0;
    z-index: 2;
    width: 24px;
    height: 30px;
    display: grid;
    place-items: center;
    border: 0;
    background: transparent;
    color: var(--faint);
    cursor: pointer;
    transform: translateY(-50%);
    transition: color 160ms ease;
  }

  .widget-carousel-arrow.right {
    left: auto;
    right: 0;
  }

  .widget-carousel-arrow:hover:not(:disabled) {
    color: var(--news-wine);
  }

  .widget-carousel-arrow:disabled {
    opacity: 0.35;
    cursor: default;
  }

  .widget-carousel-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 14px;
  }

  .carousel-dot {
    width: 8px;
    height: 8px;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: var(--line-strong);
    cursor: pointer;
    transition: background 160ms ease, transform 160ms ease;
  }

  .carousel-dot.is-active {
    background: var(--news-wine);
    transform: scale(1.25);
  }

  .announcement-tile {
    position: relative;
    display: grid;
    align-content: start;
    gap: 6px;
    padding: 12px 42px 12px 12px;
    border: 1px solid rgba(141, 43, 58, 0.12);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.6);
    color: inherit;
    text-decoration: none;
    transition: border-color 160ms ease, background 160ms ease, transform 160ms ease;
  }

  .announcement-tile:hover {
    border-color: rgba(141, 43, 58, 0.3);
    background: #ffffff;
    transform: translateY(-2px);
  }

  .announcement-tile.is-pinned {
    border-color: rgba(202, 138, 4, 0.36);
    background: #fffdf5;
    animation: pinnedGlow 2.6s ease-in-out infinite;
  }

  @keyframes pinnedGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(202, 138, 4, 0.22); }
    50% { box-shadow: 0 0 0 5px rgba(202, 138, 4, 0.1); }
  }

  .announcement-tile.is-hidden {
    display: none;
  }

  .announcement-list.is-moving-next {
    animation: announcementWidgetNext 220ms cubic-bezier(0.22, 1, 0.36, 1);
  }

  .announcement-list.is-moving-prev {
    animation: announcementWidgetPrev 220ms cubic-bezier(0.22, 1, 0.36, 1);
  }

  @keyframes announcementWidgetNext {
    from {
      opacity: 0.42;
      transform: translateX(18px);
    }

    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes announcementWidgetPrev {
    from {
      opacity: 0.42;
      transform: translateX(-18px);
    }

    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .announcement-pin {
    position: absolute;
    top: 8px;
    right: 9px;
    z-index: 2;
    width: 34px;
    height: 34px;
    display: inline-grid;
    place-items: center;
    border: 0;
    background: transparent;
    color: #a16207;
    transform: rotate(-12deg);
    animation: pinWiggle 2.6s ease-in-out infinite;
  }

  @keyframes pinWiggle {
    0%, 80%, 100% { transform: rotate(-12deg); }
    88% { transform: rotate(-22deg); }
    94% { transform: rotate(-4deg); }
  }

  .announcement-pin svg {
    width: 23px;
    height: 23px;
  }

  .announcement-tile .announcement-title {
    display: -webkit-box;
    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
  }

  .announcement-tile .announcement-text {
    -webkit-line-clamp: 3;
  }

  .announcement-item {
    position: relative;
    min-width: 0;
  }

  .announcement-feature {
    display: grid;
    min-width: 0;
    gap: 8px;
  }

  .announcement-feature-media,
  .announcement-card-media {
    position: relative;
    overflow: hidden;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    border-radius: 8px;
    background: #f1f5f9;
  }

  .announcement-feature-media {
    aspect-ratio: 21 / 8;
  }

  .announcement-card-media {
    aspect-ratio: 16 / 10;
  }

  .announcement-feature-media img,
  .announcement-card-media img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
  }

  .announcement-media-fallback {
    display: grid;
    place-items: center;
    width: 100%;
    height: 100%;
    min-height: 120px;
    background: linear-gradient(135deg, #f8fafc, #f3ead4);
    color: var(--news-wine);
    font-size: 0.76rem;
    font-weight: 900;
    letter-spacing: 0.12em;
  }

  .announcement-date-card,
  .announcement-date-stack {
    display: grid;
    place-items: center;
    align-content: center;
    width: 46px;
    min-height: 46px;
    background: var(--news-wine);
    color: #ffffff;
  }

  .announcement-date-card {
    position: absolute;
    top: 0;
    left: 0;
  }

  .announcement-date-card strong,
  .announcement-date-stack strong {
    color: var(--news-gold);
    font-size: 1.2rem;
    font-weight: 900;
    line-height: 1;
  }

  .announcement-date-card span,
  .announcement-date-stack span {
    font-size: 0.72rem;
    font-weight: 800;
    line-height: 1.2;
  }

  .announcement-feature-body {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    min-width: 0;
    gap: 6px;
  }

  .announcement-row {
    display: grid;
    grid-template-columns: 46px minmax(0, 1fr);
    gap: 10px;
    padding-top: 10px;
    border-top: 1px solid rgba(141, 43, 58, 0.16);
  }

  .announcement-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    max-width: 100%;
    gap: 8px;
  }

  .announcement-date {
    color: var(--muted);
    font-size: 0.76rem;
    font-weight: 700;
  }

  .announcement-label {
    display: inline-flex;
    align-items: center;
    min-height: 22px;
    padding: 0 7px;
    border: 1px solid rgba(141, 43, 58, 0.35);
    color: var(--news-wine);
    font-size: 0.66rem;
    font-weight: 900;
    letter-spacing: 0.02em;
  }

  .announcement-new-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    min-height: 22px;
    padding: 0 8px;
    border-radius: 999px;
    background: var(--news-gold-soft);
    color: var(--news-wine-dark);
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
  }

  .announcement-new-badge::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--news-wine);
    box-shadow: 0 0 0 4px rgba(141, 43, 58, 0.12);
  }

  .announcement-title {
    margin: 0;
    color: var(--news-wine);
    font-size: 0.92rem;
    font-weight: 800;
    line-height: 1.32;
  }

  .announcement-text {
    display: -webkit-box;
    margin: 0;
    overflow: hidden;
    color: var(--muted);
    font-size: 0.76rem;
    line-height: 1.5;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
  }


  .announcement-empty {
    display: grid;
    gap: 6px;
    margin: 0;
    padding: 16px;
    border: 1px dashed rgba(148, 163, 184, 0.38);
    border-radius: 8px;
    background: #f8fafc;
    color: var(--muted);
    font-size: 0.84rem;
    line-height: 1.5;
  }

  .announcement-admin-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 12px;
    min-height: 34px;
    padding: 0 12px;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--surface);
    color: var(--faint);
    font-size: 0.76rem;
    font-weight: 600;
    text-decoration: none;
  }

  .announcement-admin-link:hover {
    border-color: rgba(141, 43, 58, 0.24);
    color: var(--news-wine);
  }

  .clock {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
    padding: 2px 2px 12px;
    border-bottom: 1px solid var(--line);
  }

  .clock-time {
    font-size: 1.55rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    font-variant-numeric: tabular-nums;
    color: var(--ink);
  }

  .clock-date {
    font-size: 0.72rem;
    line-height: 1.35;
    text-align: right;
    color: var(--muted);
  }

  .calendar-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .icon-btn {
    width: 28px;
    height: 28px;
    display: grid;
    place-items: center;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: var(--muted);
    cursor: pointer;
  }

  .icon-btn:hover {
    background: var(--surface-soft);
    color: var(--ink);
  }

  .month-label {
    font-weight: 700;
    font-size: 0.94rem;
  }

  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    text-align: center;
    color: var(--muted);
    font-size: 0.76rem;
  }

  .calendar-grid span {
    min-height: 28px;
    display: grid;
    place-items: center;
    border-radius: 50%;
  }

  .calendar-grid .weekday {
    color: var(--faint);
    font-weight: 600;
    font-size: 0.72rem;
  }

  .calendar-grid .muted-day {
    color: #c2cad8;
  }

  .calendar-grid .today {
    background: var(--accent);
    color: #fff;
    font-weight: 700;
  }

  .weather-row {
    display: grid;
    justify-items: center;
    gap: 8px;
    align-items: center;
    min-height: 148px;
    padding: 2px 0 4px;
    text-align: center;
  }

  .weather-icon {
    position: relative;
    width: 64px;
    height: 50px;
    overflow: visible;
  }

  .weather-widget .weather-icon {
    width: 118px;
    height: 92px;
    display: block;
    margin: 0 auto;
    isolation: isolate;
  }

  .weather-scene-glow {
    position: absolute;
    inset: 7px 10px 1px;
    z-index: -1;
    border-radius: 999px;
    background:
      radial-gradient(circle at 36% 30%, rgba(251, 191, 36, 0.26), transparent 34%),
      radial-gradient(circle at 72% 64%, rgba(79, 70, 229, 0.14), transparent 44%),
      linear-gradient(135deg, rgba(234, 242, 255, 0.95), rgba(255, 255, 255, 0.2));
    filter: blur(0.2px);
    animation: weatherGlowShift 5.8s ease-in-out infinite;
  }

  .sun {
    position: absolute;
    left: 4px;
    top: 0;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #fbbf24;
    box-shadow: 0 0 0 8px rgba(251, 191, 36, 0.18);
    animation: sunGlow 3.2s ease-in-out infinite;
  }

  .weather-widget .sun {
    left: 20px;
    top: 8px;
    width: 52px;
    height: 52px;
    box-shadow: 0 0 0 10px rgba(251, 191, 36, 0.18);
  }

  .cloud {
    position: absolute;
    left: 16px;
    bottom: 0;
    width: 46px;
    height: 24px;
    border-radius: 999px;
    background: #eef3f9;
    border: 1px solid #d8e0ec;
    box-shadow: 0 10px 18px rgba(16, 23, 42, 0.08);
    animation: cloudDrift 6s ease-in-out infinite;
  }

  .weather-widget .cloud {
    left: 34px;
    bottom: 15px;
    width: 72px;
    height: 36px;
    border-color: rgba(203, 213, 225, 0.88);
    box-shadow: 0 16px 26px rgba(16, 23, 42, 0.1);
  }

  .weather-widget .cloud::before,
  .weather-widget .cloud::after {
    content: "";
    position: absolute;
    bottom: 11px;
    border-radius: 50%;
    background: inherit;
    border: inherit;
    border-bottom: 0;
  }

  .weather-widget .cloud::before {
    left: 11px;
    width: 31px;
    height: 31px;
  }

  .weather-widget .cloud::after {
    right: 12px;
    width: 24px;
    height: 24px;
  }

  .weather-widget .weather-icon.wx-icon--clear .cloud {
    opacity: 0.36;
  }

  .weather-widget .weather-icon.wx-icon--cloudy .sun {
    opacity: 0.82;
  }

  .weather-widget .weather-icon.wx-icon--rain .weather-wind,
  .weather-widget .weather-icon.wx-icon--storm .weather-wind {
    background: linear-gradient(90deg, transparent, rgba(91, 143, 214, 0.42), transparent);
  }

  .weather-wind {
    position: absolute;
    left: 8px;
    width: 34px;
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, transparent, rgba(79, 70, 229, 0.32), transparent);
    opacity: 0;
    animation: weatherWind 4.4s ease-in-out infinite;
  }

  .weather-wind.one {
    top: 60px;
  }

  .weather-wind.two {
    top: 72px;
    width: 48px;
    animation-delay: 1.2s;
  }

  @keyframes sunGlow {
    0%, 100% { box-shadow: 0 0 0 8px rgba(251, 191, 36, 0.18); transform: scale(1); }
    50% { box-shadow: 0 0 0 13px rgba(251, 191, 36, 0.1); transform: scale(1.05); }
  }

  @keyframes weatherGlowShift {
    0%, 100% { opacity: 0.78; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.04); }
  }

  @keyframes cloudDrift {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(4px); }
  }

  @keyframes weatherWind {
    0%, 22%, 100% { opacity: 0; transform: translateX(-10px); }
    42%, 68% { opacity: 0.9; transform: translateX(22px); }
  }

  /* ── Weather condition state (rain / storm / snow) ── */
  .wx-icon--rain .sun,
  .wx-icon--storm .sun,
  .wx-icon--snow .sun {
    opacity: 0.55;
    filter: saturate(0.7);
  }

  .wx-icon--rain .cloud,
  .wx-icon--storm .cloud {
    background: #d7e2ee;
    border-color: #becbdb;
  }

  .rain-drop {
    position: absolute;
    bottom: -6px;
    width: 2px;
    height: 9px;
    border-radius: 999px;
    background: #5b8fd6;
    opacity: 0;
    z-index: 2;
    animation: rainFall 1.1s linear infinite;
  }

  @keyframes rainFall {
    0% { transform: translateY(-2px); opacity: 0; }
    30% { opacity: 0.9; }
    100% { transform: translateY(14px); opacity: 0; }
  }

  .snow-flake {
    position: absolute;
    bottom: -2px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid #d8e0ec;
    opacity: 0;
    z-index: 2;
    animation: snowFall 2.4s ease-in infinite;
  }

  @keyframes snowFall {
    0% { transform: translateY(-2px) translateX(0); opacity: 0; }
    20% { opacity: 0.95; }
    100% { transform: translateY(16px) translateX(3px); opacity: 0; }
  }

  .lightning-bolt {
    position: absolute;
    right: 2px;
    bottom: -4px;
    z-index: 3;
    font-size: 14px;
    line-height: 1;
    animation: lightningFlash 2.6s ease-in-out infinite;
  }

  @keyframes lightningFlash {
    0%, 88%, 100% { opacity: 0; transform: scale(0.9); }
    90%, 96% { opacity: 1; transform: scale(1.05); }
  }

  .weather-temp {
    margin: 0;
    font-size: 2rem;
    font-weight: 900;
    letter-spacing: -0.03em;
    line-height: 1;
  }

  .weather-copy {
    display: grid;
    justify-items: center;
    gap: 6px;
    min-width: 0;
  }

  .weather-text {
    margin: 0;
    color: var(--muted);
    font-size: 0.82rem;
    line-height: 1.45;
    text-wrap: balance;
  }

  .weather-widget {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .forecast {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-top: 12px;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    text-align: center;
  }

  .forecast > div {
    min-width: 0;
    min-height: 56px;
    display: grid;
    align-content: center;
    gap: 4px;
    padding: 6px 4px;
    border-radius: 9px;
    background: #f8fafc;
  }

  .forecast-day {
    color: var(--muted);
    font-size: 0.78rem;
    font-weight: 800;
    line-height: 1.1;
  }

  .forecast-temps {
    display: inline-flex;
    align-items: baseline;
    justify-content: center;
    gap: 3px;
    margin-top: 0;
    font-size: 0.88rem;
    line-height: 1;
  }

  .forecast-high {
    color: var(--ink);
    font-size: 0.95rem;
    font-weight: 900;
  }

  .forecast-low {
    color: var(--faint);
    margin-left: 0;
    font-size: 0.78rem;
    font-weight: 800;
  }

  /* ── Contact strip ──────────────────────────────── */
  .contact-action {
    min-width: 58px;
    display: inline-grid;
    justify-items: center;
    gap: 5px;
    text-decoration: none;
    color: var(--muted);
    cursor: pointer;
    transition: color 180ms ease, transform 180ms ease;
  }

  .contact-icon {
    width: 28px;
    height: 28px;
    display: grid;
    place-items: center;
    color: currentColor;
    transition: transform 180ms ease;
  }

  .contact-action:hover {
    color: var(--accent);
    transform: translateY(-2px);
  }

  .contact-action:hover .contact-icon {
    transform: scale(1.04);
  }

  .contact-caption {
    color: currentColor;
    font-size: 0.68rem;
    font-weight: 600;
    line-height: 1;
  }

  /* ── Brand footer ───────────────────────────────── */
  .brand-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    min-height: 96px;
    margin: 24px calc(var(--page-x) * -1) 0;
    padding: 18px var(--page-x);
    border-top: 1px solid var(--line);
    background: rgba(255, 255, 255, 0.96);
  }

  .footer-contact {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 160px;
  }

  .footer-brand-name {
    color: var(--ink);
    font-size: 0.9rem;
    font-weight: 700;
    letter-spacing: 0.1em;
  }

  /* ── Modal ──────────────────────────────────────── */
  .modal {
    position: fixed;
    inset: 0;
    z-index: 50;
    display: none;
    place-items: center;
    padding: 20px;
    background: rgba(16, 23, 42, 0.45);
    -webkit-backdrop-filter: blur(3px);
    backdrop-filter: blur(3px);
  }

  .modal.is-open {
    display: grid;
  }

  .modal-card {
    width: min(100%, 580px);
    max-height: calc(100vh - 40px);
    overflow: auto;
    padding: clamp(20px, 2.4vw, 28px);
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 22px;
    box-shadow: 0 30px 70px rgba(16, 23, 42, 0.32);
    animation: modalPop 200ms ease;
  }

  @keyframes modalPop {
    from { opacity: 0; transform: translateY(14px) scale(0.98); }
    to { opacity: 1; transform: none; }
  }

  .modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
  }

  .modal-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    font-size: 1.18rem;
    font-weight: 700;
  }

  .modal-close {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border: 1px solid var(--line);
    border-radius: 50%;
    background: var(--surface);
    color: var(--muted);
    cursor: pointer;
    transition: background 180ms ease, color 180ms ease;
  }

  .modal-close:hover {
    background: var(--surface-soft);
    color: var(--ink);
  }

  .announcement-modal .modal-card {
    width: min(100%, 980px);
    padding: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .announcement-modal-shell {
    padding: clamp(22px, 3vw, 34px);
    overflow-y: auto;
    flex: 1;
    min-height: 0;
  }

  .announcement-modal-headline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 24px;
  }

  .announcement-modal-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 0 0 auto;
  }

  .announcement-display-title {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 0;
    color: var(--ink);
    font-size: clamp(1.45rem, 3vw, 2rem);
    font-weight: 900;
    letter-spacing: 0.16em;
    line-height: 1.2;
  }

  .announcement-modal-subtitle {
    margin: 8px 0 0 31px;
    color: var(--muted);
    font-size: 0.9rem;
    line-height: 1.6;
  }

  .announcement-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    margin-top: 24px;
  }

  .pagination-btn {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border: 1px solid var(--line);
    border-radius: 50%;
    background: var(--surface);
    color: var(--muted);
    cursor: pointer;
    transition: background 160ms ease, color 160ms ease, border-color 160ms ease;
  }

  .pagination-btn:hover:not(:disabled) {
    border-color: rgba(141, 43, 58, 0.3);
    color: var(--news-wine);
  }

  .pagination-btn:disabled {
    opacity: 0.4;
    cursor: default;
  }

  .pagination-numbers {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .pagination-number {
    min-width: 32px;
    height: 32px;
    padding: 0 6px;
    display: inline-grid;
    place-items: center;
    border: 1px solid var(--line);
    border-radius: 8px;
    background: var(--surface);
    color: var(--muted);
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 160ms ease, color 160ms ease, border-color 160ms ease;
  }

  .pagination-number:hover {
    border-color: rgba(141, 43, 58, 0.3);
    color: var(--news-wine);
  }

  .pagination-number.is-active {
    border-color: var(--news-wine);
    background: var(--news-wine);
    color: #ffffff;
  }

  .announcement-modal-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 28px 22px;
  }

  .announcement-modal-item {
    position: relative;
    display: grid;
    align-content: start;
    gap: 11px;
    min-width: 0;
    padding: 14px;
    border: 1px solid var(--line);
    border-radius: 14px;
    background: #ffffff;
    color: inherit;
    text-decoration: none;
    transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
  }

  .announcement-modal-item.is-pinned {
    border-color: rgba(202, 138, 4, 0.4);
    background: #fffdf5;
    animation: pinnedGlow 2.6s ease-in-out infinite;
  }

  .announcement-modal-item .announcement-pin {
    top: 12px;
    right: 12px;
  }

  .announcement-modal-item:hover {
    border-color: rgba(141, 43, 58, 0.3);
    box-shadow: 0 10px 24px rgba(16, 23, 42, 0.08);
    transform: translateY(-3px);
  }

  .announcement-modal-item.is-new {
    color: inherit;
  }

  .announcement-modal-item.is-hidden {
    display: none;
  }

  .announcement-modal-title {
    margin: 0;
    color: var(--news-wine);
    font-size: 1.04rem;
    font-weight: 900;
    line-height: 1.38;
    text-wrap: pretty;
  }

  .announcement-modal-text {
    display: -webkit-box;
    margin: 0;
    overflow: hidden;
    color: var(--muted);
    font-size: 0.86rem;
    line-height: 1.65;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    white-space: pre-line;
  }

  /* Large calendar */
  .calendar-grid.large {
    gap: 8px;
    font-size: 0.98rem;
  }

  .calendar-grid.large span {
    min-height: 52px;
    border-radius: 14px;
  }

  .calendar-grid.large .weekday {
    min-height: 28px;
    font-size: 0.82rem;
  }

  .calendar-grid.large span:not(.weekday):not(.muted-day):hover {
    background: var(--surface-soft);
  }

  /* Weather modal */
  .wx-hero {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border-radius: 18px;
    background: linear-gradient(135deg, #eaf2ff, #f6f9ff);
    border: 1px solid var(--line);
  }

  .wx-hero-icon {
    position: relative;
    width: 90px;
    height: 70px;
    flex: 0 0 auto;
  }

  .wx-hero-icon .sun {
    width: 46px;
    height: 46px;
  }

  .wx-hero-icon .cloud {
    width: 62px;
    height: 32px;
    left: 22px;
  }

  .wx-hero-icon .rain-drop {
    width: 3px;
    height: 13px;
  }

  .wx-hero-icon .snow-flake {
    width: 6px;
    height: 6px;
  }

  .wx-hero-icon .lightning-bolt {
    font-size: 20px;
  }

  .wx-hero-temp {
    margin: 0;
    font-size: 2.8rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.03em;
  }

  .wx-hero-temp span {
    font-size: 1.2rem;
    color: var(--muted);
    font-weight: 600;
  }

  .wx-hero-sub {
    margin: 8px 0 0;
    color: var(--muted);
    font-size: 0.92rem;
  }

  .wx-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 16px;
  }

  .wx-stat {
    display: grid;
    gap: 4px;
    padding: 12px;
    text-align: center;
    border: 1px solid var(--line);
    border-radius: 14px;
    background: var(--surface-soft);
  }

  .wx-stat span {
    color: var(--muted);
    font-size: 0.72rem;
  }

  .wx-stat strong {
    font-size: 1rem;
  }

  .wx-forecast-title {
    margin: 20px 0 10px;
    font-size: 0.95rem;
    font-weight: 700;
  }

  .wx-forecast {
    display: grid;
    gap: 6px;
  }

  .wx-fc-row {
    display: grid;
    grid-template-columns: 1fr auto auto;
    align-items: center;
    gap: 14px;
    padding: 10px 14px;
    border: 1px solid var(--line);
    border-radius: 12px;
  }

  .wx-fc-row.today {
    border-color: rgba(79, 70, 229, 0.4);
    background: var(--accent-soft);
  }

  .wx-fc-day {
    font-weight: 600;
    font-size: 0.9rem;
  }

  .wx-fc-cond {
    color: var(--muted);
    font-size: 0.82rem;
  }

  .wx-fc-temp {
    font-size: 0.9rem;
    font-weight: 600;
    min-width: 78px;
    text-align: right;
  }

  .wx-fc-temp .low {
    color: var(--faint);
    margin-left: 6px;
    font-weight: 500;
  }

  .wx-note {
    margin: 16px 0 0;
    color: var(--faint);
    font-size: 0.74rem;
  }

  @media (max-width: 540px) {
    .wx-stats {
      grid-template-columns: repeat(2, 1fr);
    }

    .calendar-grid.large span {
      min-height: 42px;
    }
  }

  /* ── AI Assistant (AssistiveTouch style) ────────── */
  .ai-fab {
    position: fixed;
    right: 24px;
    bottom: 28px;
    z-index: 60;
    width: 74px;
    height: 74px;
    display: grid;
    place-items: center;
    border: 0;
    border-radius: 50%;
    background: #fff;
    color: #fff;
    box-shadow: 0 14px 30px rgba(16, 23, 42, 0.18);
    cursor: grab;
    touch-action: none;
    transition: transform 160ms ease, box-shadow 160ms ease;
  }

  .ai-fab-logo {
    width: 62px;
    height: 62px;
    display: block;
    object-fit: contain;
  }

  .ai-fab::before {
    content: "";
    position: absolute;
    inset: -5px;
    border-radius: 50%;
    border: 2px solid rgba(239, 98, 33, 0.28);
    animation: aiPulse 2.4s ease-out infinite;
  }

  @keyframes aiPulse {
    0% { transform: scale(0.9); opacity: 0.7; }
    100% { transform: scale(1.35); opacity: 0; }
  }

  .ai-fab:hover {
    transform: scale(1.06);
    box-shadow: 0 18px 38px rgba(16, 23, 42, 0.24);
  }

  .ai-fab.dragging {
    cursor: grabbing;
    transform: scale(1.1);
  }

  .ai-fab.dragging::before {
    animation: none;
    opacity: 0;
  }

  .ai-chat {
    position: fixed;
    right: 24px;
    bottom: 96px;
    z-index: 60;
    width: min(346px, calc(100vw - 32px));
    height: min(470px, calc(100vh - 130px));
    display: none;
    flex-direction: column;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 20px;
    box-shadow: 0 28px 64px rgba(16, 23, 42, 0.3);
  }

  .ai-chat.is-open {
    display: flex;
    animation: modalPop 200ms ease;
  }

  .ai-chat-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
  }

  .ai-chat-title {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .ai-chat-avatar {
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #fff;
  }

  .ai-logo,
  .ai-msg-logo {
    width: 84%;
    height: 84%;
    display: block;
    object-fit: contain;
  }

  .ai-chat-title strong {
    display: block;
    font-size: 1.1rem;
  }

  .ai-chat-status {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.7rem;
    opacity: 0.9;
  }

  .ai-chat-status::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #fde047;
  }

  .ai-chat-close {
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.18);
    color: #fff;
    cursor: pointer;
  }

  .ai-chat-close:hover {
    background: rgba(255, 255, 255, 0.32);
  }

  .ai-chat-body {
    flex: 1;
    overflow: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
    background: var(--surface-soft);
  }

  .ai-msg {
    display: flex;
    align-items: flex-end;
    gap: 8px;
  }

  .ai-msg.user {
    flex-direction: row-reverse;
  }

  .ai-msg-ava {
    width: 28px;
    height: 28px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #ede9fe;
    font-size: 0.84rem;
  }

  .ai-msg-ava.bot-logo {
    background: #fff;
    border: 1px solid var(--line);
  }

  .ai-bubble {
    max-width: 78%;
    padding: 10px 12px;
    border-radius: 14px;
    font-size: 0.84rem;
    line-height: 1.55;
    background: var(--surface);
    border: 1px solid var(--line);
    color: var(--ink);
  }

  .ai-msg.user .ai-bubble {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
  }

  .ai-chat-input {
    display: flex;
    gap: 8px;
    padding: 12px;
    border-top: 1px solid var(--line);
    background: var(--surface);
  }

  .ai-chat-input input {
    flex: 1;
    min-height: 42px;
    padding: 0 14px;
    border: 1px solid var(--line);
    border-radius: 999px;
    background: var(--surface-soft);
    color: var(--ink);
  }

  .ai-chat-input button {
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    display: grid;
    place-items: center;
    border: 0;
    border-radius: 50%;
    background: var(--accent);
    color: #fff;
    cursor: pointer;
    transition: background 160ms ease;
  }

  .ai-chat-input button:hover {
    background: #4338ca;
  }

  /* ── Toast ──────────────────────────────────────── */
  .toast {
    position: fixed;
    left: 24px;
    bottom: 24px;
    z-index: 20;
    max-width: min(360px, calc(100vw - 36px));
    padding: 14px 16px;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: var(--ink);
    color: #fff;
    box-shadow: 0 18px 36px rgba(16, 23, 42, 0.22);
    opacity: 0;
    transform: translateY(14px);
    pointer-events: none;
    transition: opacity 180ms ease, transform 180ms ease;
  }

  .toast.is-visible {
    opacity: 1;
    transform: translateY(0);
  }

  /* ── Responsive ─────────────────────────────────── */
  @media (max-width: 1320px) {
    .dashboard-grid {
      grid-template-columns: 1fr;
    }

    .side-stack {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 1080px) {
    .topbar {
      grid-template-columns: 1fr auto;
      grid-template-areas:
        "brand lang"
        "search search";
      row-gap: 14px;
    }

    .brand {
      grid-area: brand;
    }

    .search-wrap {
      grid-area: search;
    }

    .top-actions {
      grid-area: lang;
    }

    .hero-photo {
      -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 66%);
      mask-image: linear-gradient(to right, transparent 0%, #000 66%);
    }

    .hero-visual::after {
      background: linear-gradient(to right, #f6f8fc 0%, rgba(246, 248, 252, 0.7) 46%, rgba(246, 248, 252, 0) 72%);
    }

    .announcement-modal-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 720px) {
    .page-shell {
      min-height: 100vh;
      margin: 0;
      border-radius: 0;
      --page-x: 18px;
      padding: 0 var(--page-x) 18px;
    }

    .topbar {
      grid-template-columns: minmax(0, 1fr) auto;
      column-gap: 10px;
      row-gap: 12px;
    }

    .top-actions {
      gap: 6px;
    }

    .announcement-admin-btn,
    .language-trigger {
      min-height: 44px;
    }

    .announcement-admin-btn {
      width: 44px;
    }

    .language-trigger {
      padding: 0 10px;
    }

    .app-grid {
      grid-template-columns: repeat(auto-fill, minmax(112px, 1fr));
      gap: 10px;
    }

    .mini-widgets-row {
      grid-template-columns: 1fr;
    }

    .side-stack {
      grid-template-columns: 1fr;
    }

    .announcement-modal-headline {
      flex-direction: column;
    }

    .announcement-modal-actions {
      width: 100%;
      justify-content: space-between;
    }

    .announcement-modal-grid {
      grid-template-columns: 1fr;
    }

    .announcement-widget-title,
    .announcement-display-title {
      letter-spacing: 0.08em;
    }

    .announcement-row,
    .announcement-grid-9 {
      grid-template-columns: 1fr;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
      scroll-behavior: auto !important;
      transition-duration: 0.01ms !important;
      animation-duration: 0.01ms !important;
      animation-iteration-count: 1 !important;
    }
  }
</style>

<body>
  <main class="page-shell">
    <header class="topbar" aria-label="SI Intranet header">
      <div class="brand">
        <img class="brand-logo" src="img/3si.png" alt="Supavut Group logo">
        <div class="brand-text">
          <h1 class="brand-title" aria-label="Supavut Group">
            <span class="brand-title-main">SUPAVUT</span>
            <span class="brand-title-sub">GROUP</span>
          </h1>
        </div>
      </div>

      <div class="search-wrap">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        </svg>
        <input id="searchInput" class="search-input" type="search" autocomplete="off" data-i18n-placeholder="searchPlaceholder" placeholder="Search apps, services, documents..." aria-label="Search applications">
        <button id="clearSearch" class="search-clear" type="button" aria-label="Clear search">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
        </button>
      </div>

      <div class="top-actions">
        <a class="announcement-admin-btn" href="announcements_admin.php" aria-label="HR Announcement Admin" title="HR Announcement Admin">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3.2 5 5.9v5.3c0 4.6 2.98 8.72 7 9.9 4.02-1.18 7-5.3 7-9.9V5.9L12 3.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
            <path d="M12 10.4a1.7 1.7 0 1 0 0 3.4 1.7 1.7 0 0 0 0-3.4Z" stroke="currentColor" stroke-width="1.7" />
            <path d="M12 13.8v2.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
          </svg>
        </a>
        <div class="language-menu" aria-label="Language switcher">
          <button id="languageTrigger" class="language-trigger" type="button" aria-label="Change language" aria-expanded="false" aria-controls="languagePanel">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.2-2.4 3.4-5.3 3.4-9S14.2 5.4 12 3m0 18c-2.2-2.4-3.4-5.3-3.4-9S9.8 5.4 12 3M3.6 9h16.8M3.6 15h16.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="lang-code" id="languageCode">TH</span>
            <svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
          </button>
          <div id="languagePanel" class="language-panel">
            <button class="lang-btn" type="button" data-lang="en"><span>English</span><span class="lang-code">EN</span></button>
            <button class="lang-btn is-active" type="button" data-lang="th"><span>ไทย</span><span class="lang-code">TH</span></button>
            <button class="lang-btn" type="button" data-lang="my"><span>မြန်မာ</span><span class="lang-code">MY</span></button>
          </div>
        </div>
      </div>
    </header>

    <div class="dashboard-grid">
      <section class="main-panel" aria-label="Main intranet content">
        <div class="hero-panel">
          <div class="hero-copy">
            <span class="welcome" data-i18n="welcome">ยินดีต้อนรับ</span>
            <h2 class="hero-title" data-i18n="heroTitle">ศูนย์กลางสำหรับระบบงานและบริการภายใน</h2>
            <p class="hero-text" data-i18n="heroText">พื้นที่กลางสำหรับเชื่อมต่อระบบงาน<br>และบริการภายในของ Supavut Industry</p>
          </div>

          <div class="hero-visual" aria-hidden="true">
            <img id="heroPhoto" class="hero-photo" src="<?php echo htmlspecialchars($heroImage, ENT_QUOTES); ?>" alt="">
          </div>
        </div>

        <div>
          <div id="applications" class="app-header">
            <h2 class="section-title"><span class="dot"></span><span data-i18n="applications">Applications</span></h2>
            <div id="categoryTabs" class="category-tabs" aria-label="หมวดหมู่ระบบงาน"></div>
          </div>

          <div id="appGrid" class="app-grid" aria-live="polite"></div>
          <div id="emptyState" class="empty-state" data-i18n="emptyState">ไม่พบระบบที่ค้นหา</div>

          <div class="app-footer">
            <button id="toggleApps" class="view-all-link" type="button">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
              </svg>
              <span data-i18n="viewAll">View all applications</span>
            </button>
          </div>
        </div>
      </section>

      <aside class="side-stack" aria-label="Intranet sidebar">
        <section class="widget announcement-widget announcement-widget-full" aria-labelledby="announcementTitle">
          <div class="widget-head">
            <div class="announcement-heading">
              <div class="announcement-title-line">
                <span class="news-orb" aria-hidden="true"></span>
                <h2 id="announcementTitle" class="announcement-widget-title" data-i18n="announcementTitle">ANNOUNCEMENT</h2>
              </div>
              <p class="announcement-heading-sub" data-i18n="announcementSub">ประกาศและเอกสารจาก HR</p>
            </div>
            <button class="widget-link" type="button" data-action="announcements" data-i18n="viewAllNews">ดูทั้งหมด</button>
          </div>

          <?php if (count($announcements) === 0) { ?>
            <p class="announcement-empty" data-i18n="announcementEmpty">ยังไม่มีประกาศจาก HR</p>
          <?php } else { ?>
            <?php
              $widgetPerPage = 6;
              $widgetTotalPages = max(1, ceil(count($announcements) / $widgetPerPage));
            ?>
            <div class="announcement-carousel<?php echo $widgetTotalPages > 1 ? '' : ' no-nav'; ?>">
              <?php if ($widgetTotalPages > 1) { ?>
                <button id="announcementWidgetPrev" class="widget-carousel-arrow left" type="button" aria-label="Previous page">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
              <?php } ?>
              <div id="announcementWidgetGrid" class="announcement-list announcement-grid-9" data-per-page="<?php echo $widgetPerPage; ?>">
                <?php foreach ($announcements as $widgetIndex => $announcement) { ?>
                  <?php
                    $announcementIsNew = announcement_is_new($announcement);
                    $announcementIsPinned = announcement_is_pinned($announcement);
                    $widgetPage = intval($widgetIndex / $widgetPerPage) + 1;
                  ?>
                  <a class="announcement-item announcement-tile<?php echo $announcementIsNew ? ' is-new' : ''; ?><?php echo $announcementIsPinned ? ' is-pinned' : ''; ?>" data-page="<?php echo $widgetPage; ?>" href="announcement_view.php?id=<?php echo urlencode($announcement['id']); ?>" data-news-link
                    data-title-th="<?php echo announcement_h($announcement['title']); ?>"
                    data-title-en="<?php echo announcement_h(announcement_field($announcement, 'title_en')); ?>"
                    data-title-my="<?php echo announcement_h(announcement_field($announcement, 'title_my')); ?>"
                    data-body-th="<?php echo announcement_h($announcement['body']); ?>"
                    data-body-en="<?php echo announcement_h(announcement_field($announcement, 'body_en')); ?>"
                    data-body-my="<?php echo announcement_h(announcement_field($announcement, 'body_my')); ?>">
                    <?php if ($announcementIsPinned) { ?>
                      <span class="announcement-pin" aria-label="ปักหมุด">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 4 5 5-4.2 4.2.7 5.3-1.1 1.1-5.3-5.4L5.8 18.5 5 17.7l4.3-4.3L4 8.1 5.1 7l5.3.7L15 4Z" fill="currentColor" fill-opacity="0.16"/><path d="m15 4 5 5-4.2 4.2.7 5.3-1.1 1.1-5.3-5.4L5.8 18.5 5 17.7l4.3-4.3L4 8.1 5.1 7l5.3.7L15 4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                      </span>
                    <?php } ?>
                    <div class="announcement-meta">
                      <span class="announcement-date"><?php echo announcement_h(announcement_format_date($announcement['created_at'])); ?></span>
                      <?php if ($announcementIsNew) { ?>
                        <span class="announcement-new-badge" data-i18n="announcementNew">New</span>
                      <?php } ?>
                    </div>
                    <h3 class="announcement-title"><?php echo announcement_h($announcement['title']); ?></h3>
                    <p class="announcement-text"><?php echo announcement_h($announcement['body']); ?></p>
                  </a>
                <?php } ?>
              </div>
              <?php if ($widgetTotalPages > 1) { ?>
                <button id="announcementWidgetNext" class="widget-carousel-arrow right" type="button" aria-label="Next page">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </button>
              <?php } ?>
            </div>
            <?php if ($widgetTotalPages > 1) { ?>
              <div id="announcementWidgetDots" class="widget-carousel-dots"></div>
            <?php } ?>
          <?php } ?>
        </section>

        <div class="mini-widgets-row">
          <section class="widget widget-compact" aria-labelledby="calendarTitle">
            <div class="clock" aria-label="เวลาปัจจุบัน">
              <span id="clockTime" class="clock-time">--:--:--</span>
              <span id="clockDate" class="clock-date"></span>
            </div>
            <div class="widget-head">
              <h2 id="calendarTitle" class="widget-title"><span class="mini-dot"></span><span data-i18n="calendar">Calendar</span></h2>
              <button class="widget-link" type="button" data-action="calendar" data-i18n="viewCalendar">ดูปฏิทินเต็ม</button>
            </div>
            <div class="calendar-nav">
              <button id="prevMonth" class="icon-btn" type="button" aria-label="Previous month">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
              </button>
              <span id="monthLabel" class="month-label"></span>
              <button id="nextMonth" class="icon-btn" type="button" aria-label="Next month">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
              </button>
            </div>
            <div id="calendarGrid" class="calendar-grid" aria-label="Calendar"></div>
          </section>

          <section class="widget widget-compact weather-widget" aria-labelledby="weatherTitle">
            <div class="widget-head">
              <h2 id="weatherTitle" class="widget-title"><span class="mini-dot blue"></span><span data-i18n="weather">Weather</span></h2>
              <button class="widget-link" type="button" data-action="weather" data-i18n="viewWeather">ดูแบบเต็ม</button>
            </div>
            <div class="weather-row">
              <div id="wxIcon" class="weather-icon" aria-hidden="true">
                <span class="weather-scene-glow"></span>
                <span class="sun"></span>
                <span class="cloud"></span>
                <span class="weather-wind one"></span>
                <span class="weather-wind two"></span>
              </div>
              <div class="weather-copy">
                <p class="weather-temp" id="wxTemp">32°C</p>
                <p class="weather-text"><span data-i18n="bangkokOffice">Bangkok Office</span><br><span id="wxCond">มีเมฆบางส่วน</span></p>
              </div>
            </div>
            <div class="forecast" id="wxMiniForecast">
              <div><div class="forecast-day">Fri</div><div class="forecast-temps"><span class="forecast-high">33°</span><span class="forecast-low">25°</span></div></div>
              <div><div class="forecast-day">Sat</div><div class="forecast-temps"><span class="forecast-high">32°</span><span class="forecast-low">25°</span></div></div>
              <div><div class="forecast-day">Sun</div><div class="forecast-temps"><span class="forecast-high">31°</span><span class="forecast-low">24°</span></div></div>
            </div>
          </section>
        </div>
      </aside>
    </div>

    <footer class="brand-footer" aria-label="Company footer">
      <div class="footer-contact" aria-label="Contact">
        <a class="contact-action" href="mailto:hr.manager@supavut.com" aria-label="Email HR" title="hr.manager@supavut.com">
          <span class="contact-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16v12H4V6Zm0 1 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
          </span>
          <span class="contact-caption">Email</span>
        </a>
        <a class="contact-action" href="mailto:Pumiput.it@supavut.com" aria-label="Contact Admin" title="Pumiput.it@supavut.com">
          <span class="contact-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
          </span>
          <span class="contact-caption">Contact</span>
        </a>
      </div>
      <span class="footer-brand-name">SUPAVUT INDUSTRY 2029</span>
    </footer>
  </main>

  <!-- ── Announcements modal ───────────────────────── -->
  <div id="announcementsModal" class="modal announcement-modal" role="dialog" aria-modal="true" aria-labelledby="announcementsModalTitle">
    <div class="modal-card">
      <div class="announcement-modal-shell">
        <div class="announcement-modal-headline">
          <div>
            <h2 id="announcementsModalTitle" class="announcement-display-title"><span class="news-orb" aria-hidden="true"></span><span data-i18n="announcementTitle">ANNOUNCEMENT</span></h2>
            <p class="announcement-modal-subtitle" data-i18n="announcementModalSub">ประกาศ ข่าวสาร และเอกสารจาก HR สำหรับพนักงาน Supavut Group</p>
          </div>
          <div class="announcement-modal-actions">
            <button class="modal-close" type="button" data-close-modal aria-label="ปิด">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
            </button>
          </div>
        </div>

        <?php if (count($announcements) === 0) { ?>
          <p class="announcement-empty" data-i18n="announcementEmpty">ยังไม่มีประกาศจาก HR</p>
        <?php } else { ?>
          <?php $perPage = 9; ?>
          <div id="announcementModalGrid" class="announcement-modal-grid" data-per-page="<?php echo $perPage; ?>">
            <?php foreach ($announcements as $modalIndex => $announcement) { ?>
              <?php
                $modalCoverUrl = announcement_cover_url($announcement);
                $modalIsNew = announcement_is_new($announcement);
                $modalIsPinned = announcement_is_pinned($announcement);
                $modalPage = intval($modalIndex / $perPage) + 1;
              ?>
              <a class="announcement-modal-item<?php echo $modalIsNew ? ' is-new' : ''; ?><?php echo $modalIsPinned ? ' is-pinned' : ''; ?>" data-page="<?php echo $modalPage; ?>" href="announcement_view.php?id=<?php echo urlencode($announcement['id']); ?>" data-news-link
                data-title-th="<?php echo announcement_h($announcement['title']); ?>"
                data-title-en="<?php echo announcement_h(announcement_field($announcement, 'title_en')); ?>"
                data-title-my="<?php echo announcement_h(announcement_field($announcement, 'title_my')); ?>"
                data-body-th="<?php echo announcement_h($announcement['body']); ?>"
                data-body-en="<?php echo announcement_h(announcement_field($announcement, 'body_en')); ?>"
                data-body-my="<?php echo announcement_h(announcement_field($announcement, 'body_my')); ?>">
                <?php if ($modalIsPinned) { ?>
                  <span class="announcement-pin" aria-label="ปักหมุด">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 4 5 5-4.2 4.2.7 5.3-1.1 1.1-5.3-5.4L5.8 18.5 5 17.7l4.3-4.3L4 8.1 5.1 7l5.3.7L15 4Z" fill="currentColor" fill-opacity="0.16"/><path d="m15 4 5 5-4.2 4.2.7 5.3-1.1 1.1-5.3-5.4L5.8 18.5 5 17.7l4.3-4.3L4 8.1 5.1 7l5.3.7L15 4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                  </span>
                <?php } ?>
                <div class="announcement-card-media">
                  <?php if ($modalCoverUrl !== '') { ?>
                    <img src="<?php echo announcement_h($modalCoverUrl); ?>" alt="<?php echo announcement_h($announcement['title']); ?>" loading="lazy">
                  <?php } else { ?>
                    <div class="announcement-media-fallback" data-i18n="announcementNoCover">NEWS</div>
                  <?php } ?>
                  <div class="announcement-date-card" aria-label="<?php echo announcement_h(announcement_format_date($announcement['created_at'])); ?>">
                    <strong><?php echo announcement_h(announcement_date_day($announcement['created_at'])); ?></strong>
                    <span><?php echo announcement_h(announcement_date_month_short_th($announcement['created_at'])); ?></span>
                  </div>
                </div>
                <div class="announcement-meta">
                  <span class="announcement-date"><?php echo announcement_h(announcement_format_date($announcement['created_at'])); ?></span>
                  <?php if ($modalIsNew) { ?>
                    <span class="announcement-new-badge" data-i18n="announcementNew">New</span>
                  <?php } ?>
                </div>
                <h3 class="announcement-modal-title"><?php echo announcement_h($announcement['title']); ?></h3>
                <p class="announcement-modal-text"><?php echo announcement_h($announcement['body']); ?></p>
              </a>
            <?php } ?>
          </div>

          <?php if (count($announcements) > $perPage) { ?>
            <div class="announcement-pagination" id="announcementPagination">
              <button id="announcementPrevPage" class="pagination-btn" type="button" aria-label="Previous page">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
              </button>
              <div id="announcementPageNumbers" class="pagination-numbers"></div>
              <button id="announcementNextPage" class="pagination-btn" type="button" aria-label="Next page">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
              </button>
            </div>
          <?php } ?>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- ── Calendar modal ─────────────────────────────── -->
  <div id="calendarModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="calendarModalTitle">
    <div class="modal-card">
      <div class="modal-head">
        <h2 id="calendarModalTitle" class="modal-title"><span class="mini-dot"></span><span data-i18n="calendar">ปฏิทิน</span></h2>
        <button class="modal-close" type="button" data-close-modal aria-label="ปิด">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
        </button>
      </div>
      <div class="calendar-nav">
        <button id="prevMonthLg" class="icon-btn" type="button" aria-label="เดือนก่อนหน้า">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </button>
        <span id="monthLabelLarge" class="month-label"></span>
        <button id="nextMonthLg" class="icon-btn" type="button" aria-label="เดือนถัดไป">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </button>
      </div>
      <div id="calendarGridLarge" class="calendar-grid large" aria-label="ปฏิทินแบบเต็ม"></div>
    </div>
  </div>

  <!-- ── Weather modal ──────────────────────────────── -->
  <div id="weatherModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="weatherModalTitle">
    <div class="modal-card">
      <div class="modal-head">
        <h2 id="weatherModalTitle" class="modal-title"><span class="mini-dot blue"></span><span data-i18n="weather">สภาพอากาศ</span></h2>
        <button class="modal-close" type="button" data-close-modal aria-label="ปิด">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
        </button>
      </div>

      <div class="wx-hero">
        <div id="wxHeroIcon" class="wx-hero-icon" aria-hidden="true">
          <span class="sun"></span>
          <span class="cloud"></span>
        </div>
        <div>
          <p class="wx-hero-temp" id="wxModalTemp">32<span>°C</span></p>
          <p class="wx-hero-sub" id="wxModalSub">สำนักงานกรุงเทพ · มีเมฆบางส่วน</p>
        </div>
      </div>

      <div class="wx-stats">
        <div class="wx-stat"><span data-i18n="feelsLike">รู้สึกเหมือน</span><strong id="wxFeels">35°</strong></div>
        <div class="wx-stat"><span data-i18n="humidity">ความชื้น</span><strong id="wxHumidity">62%</strong></div>
        <div class="wx-stat"><span data-i18n="windLabel">ลม</span><strong id="wxWind">12 กม./ชม.</strong></div>
        <div class="wx-stat"><span data-i18n="uvIndex">ดัชนี UV</span><strong id="wxUv">8</strong></div>
      </div>

      <h3 class="wx-forecast-title" data-i18n="forecast7Day">พยากรณ์ 7 วัน</h3>
      <div class="wx-forecast" id="wxForecast"></div>

      <p class="wx-note" id="wxNote" data-i18n="weatherNote">* ข้อมูลจริงจาก Open-Meteo · กรุงเทพมหานคร</p>
    </div>
  </div>

  <!-- ── AI Assistant (draggable) ───────────────────── -->
  <button id="aiFab" class="ai-fab" type="button" aria-label="ผู้ช่วย AI">
    <img class="ai-fab-logo" src="img/si5.jpg" alt="">
  </button>

  <div id="aiChat" class="ai-chat" role="dialog" aria-label="ผู้ช่วย AI">
    <div class="ai-chat-head">
      <div class="ai-chat-title">
        <span class="ai-chat-avatar">
          <img class="ai-logo" src="img/si5.jpg" alt="">
        </span>
        <div>
          <strong>SI Assistant</strong>
          <span class="ai-chat-status">กำลังพัฒนา</span>
        </div>
      </div>
      <button class="ai-chat-close" id="aiChatClose" type="button" aria-label="ปิด">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
      </button>
    </div>
    <div class="ai-chat-body" id="aiChatBody">
      <div class="ai-msg bot">
        <span class="ai-msg-ava bot-logo"><img class="ai-msg-logo" src="img/si5.jpg" alt=""></span>
        <div class="ai-bubble">สวัสดีครับ ผมคือผู้ช่วย AI ของ Supavut Group<br>ฟีเจอร์แชทนี้กำลังอยู่ในช่วงพัฒนา เร็วๆ นี้จะพร้อมให้คุยได้จริงครับ</div>
      </div>
    </div>
    <form class="ai-chat-input" id="aiChatForm">
      <input id="aiChatText" type="text" placeholder="พิมพ์ข้อความ..." autocomplete="off">
      <button type="submit" aria-label="ส่ง">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" /></svg>
      </button>
    </form>
  </div>

  <div id="toast" class="toast" role="status" aria-live="polite"></div>

  <script>
    'use strict';

    const translations = {
      en: {
        gateway: 'Application Gateway',
        searchPlaceholder: 'Search apps, services, documents...',
        welcome: 'Welcome',
        heroTitle: 'Your central hub for work and productivity',
        heroText: 'A central space for connecting Supavut Industry internal systems and services.',
        applications: 'Applications',
        calendar: 'Calendar',
        weather: 'Weather',
        viewCalendar: 'View full calendar',
        viewWeather: 'View full',
        sampleData: 'Sample data',
        bangkokOffice: 'Bangkok Office',
        partlyCloudy: 'Partly cloudy',
        feelsLike: 'Feels like',
        humidity: 'Humidity',
        windLabel: 'Wind',
        uvIndex: 'UV Index',
        forecast7Day: '7-day forecast',
        weatherNote: '* Live data from Open-Meteo · Bangkok',
        windUnit: 'km/h',
        todayLabel: 'Today',
        tomorrowLabel: 'Tomorrow',
        dayAfterLabel: 'Day after',
        unknownWeather: 'Unknown weather',
        emailHr: 'Email HR',
        emailIt: 'Email IT',
        contactAdmin: 'Contact Admin',
        systemStatus: 'System Status',
        operational: 'All systems operational',
        maintenance: 'Maintenance',
        noMaintenance: 'No upcoming maintenance',
        lastUpdated: 'Last Updated',
        viewAll: 'View all applications',
        showLess: 'Show less',
        emptyState: 'No applications matched your search.',
        opening: 'Opening',
        comingSoon: 'System link is being prepared',
        copied: 'Copied to clipboard',
        unavailableCopy: 'Contact copied',
        calendarToast: 'Calendar preview is ready. Full calendar can connect to company calendar later.',
        noSearch: 'Search cleared',
        announcementTitle: 'ANNOUNCEMENT',
        announcementSub: 'News and documents from HR',
        announcementModalSub: 'Announcements, news and documents from HR for Supavut Group staff',
        viewAllNews: 'View all',
        announcementEmpty: 'No announcements from HR yet',
        announcementNew: 'New',
        announcementNoCover: 'NEWS'
      },
      th: {
        gateway: 'Application Gateway',
        searchPlaceholder: 'ค้นหาระบบ บริการ หรือเอกสาร...',
        welcome: 'ยินดีต้อนรับ',
        heroTitle: 'ศูนย์กลางสำหรับระบบงานและบริการภายใน',
        heroText: 'พื้นที่กลางสำหรับเชื่อมต่อระบบงาน\nและบริการภายในของ Supavut Industry',
        applications: 'ระบบงาน',
        calendar: 'ปฏิทิน',
        weather: 'สภาพอากาศ',
        viewCalendar: 'ดูปฏิทินเต็ม',
        viewWeather: 'ดูแบบเต็ม',
        sampleData: 'ข้อมูลตัวอย่าง',
        bangkokOffice: 'สำนักงานกรุงเทพ',
        partlyCloudy: 'มีเมฆบางส่วน',
        feelsLike: 'รู้สึกเหมือน',
        humidity: 'ความชื้น',
        windLabel: 'ลม',
        uvIndex: 'ดัชนี UV',
        forecast7Day: 'พยากรณ์ 7 วัน',
        weatherNote: '* ข้อมูลจริงจาก Open-Meteo · กรุงเทพมหานคร',
        windUnit: 'กม./ชม.',
        todayLabel: 'วันนี้',
        tomorrowLabel: 'พรุ่งนี้',
        dayAfterLabel: 'มะรืนนี้',
        unknownWeather: 'ไม่ทราบสภาพอากาศ',
        emailHr: 'อีเมล HR',
        emailIt: 'อีเมล IT',
        contactAdmin: 'ติดต่อ Admin',
        systemStatus: 'สถานะระบบ',
        operational: 'ทุกระบบพร้อมใช้งาน',
        maintenance: 'การบำรุงรักษา',
        noMaintenance: 'ไม่มีแผนบำรุงรักษา',
        lastUpdated: 'อัปเดตล่าสุด',
        viewAll: 'ดูระบบทั้งหมด',
        showLess: 'แสดงน้อยลง',
        emptyState: 'ไม่พบระบบที่ค้นหา',
        opening: 'กำลังเปิด',
        comingSoon: 'กำลังเตรียมลิงก์ระบบ',
        copied: 'คัดลอกไปยังคลิปบอร์ดแล้ว',
        unavailableCopy: 'คัดลอกข้อมูลติดต่อแล้ว',
        calendarToast: 'ปฏิทินตัวอย่างพร้อมใช้งาน และสามารถเชื่อมกับปฏิทินบริษัทภายหลังได้',
        noSearch: 'ล้างการค้นหาแล้ว',
        announcementTitle: 'ประกาศ',
        announcementSub: 'ประกาศและเอกสารจาก HR',
        announcementModalSub: 'ประกาศ ข่าวสาร และเอกสารจาก HR สำหรับพนักงาน Supavut Group',
        viewAllNews: 'ดูทั้งหมด',
        announcementEmpty: 'ยังไม่มีประกาศจาก HR',
        announcementNew: 'ใหม่',
        announcementNoCover: 'ข่าว'
      },
      my: {
        gateway: 'အက်ပ်လီကေးရှင်း ဂိတ်ဝေး',
        searchPlaceholder: 'အက်ပ်များ၊ ဝန်ဆောင်မှုများ၊ စာရွက်စာတမ်းများ ရှာရန်...',
        welcome: 'ကြိုဆိုပါတယ်',
        heroTitle: 'အလုပ်နှင့် ဝန်ဆောင်မှုများအတွက် ဗဟိုနေရာ',
        heroText: 'Supavut Industry ၏ အတွင်းပိုင်းလုပ်ငန်းစနစ်များနှင့် ဝန်ဆောင်မှုများကို ချိတ်ဆက်ပေးသည့် ဗဟိုနေရာ။',
        applications: 'အက်ပ်များ',
        calendar: 'ပြက္ခဒိန်',
        weather: 'ရာသီဥတု',
        viewCalendar: 'ပြက္ခဒိန်အပြည့်ကြည့်ရန်',
        viewWeather: 'အပြည့်ကြည့်ရန်',
        sampleData: 'နမူနာဒေတာ',
        bangkokOffice: 'ဘန်ကောက်ရုံး',
        partlyCloudy: 'တိမ်အနည်းငယ်',
        feelsLike: 'ခံစားရသည့်အပူချိန်',
        humidity: 'စိုထိုင်းဆ',
        windLabel: 'လေ',
        uvIndex: 'UV Index',
        forecast7Day: '7 ရက် ရာသီဥတုခန့်မှန်းချက်',
        weatherNote: '* Open-Meteo မှ တိုက်ရိုက်ဒေတာ · ဘန်ကောက်',
        windUnit: 'ကီလိုမီတာ/နာရီ',
        todayLabel: 'ယနေ့',
        tomorrowLabel: 'မနက်ဖြန်',
        dayAfterLabel: 'သဘက်',
        unknownWeather: 'ရာသီဥတု မသိရပါ',
        emailHr: 'HR အီးမေးလ်',
        emailIt: 'IT အီးမေးလ်',
        contactAdmin: 'Admin ဆက်သွယ်ရန်',
        systemStatus: 'စနစ်အခြေအနေ',
        operational: 'စနစ်အားလုံး အသုံးပြုနိုင်သည်',
        maintenance: 'ထိန်းသိမ်းမှု',
        noMaintenance: 'ထိန်းသိမ်းမှုအစီအစဉ် မရှိပါ',
        lastUpdated: 'နောက်ဆုံးအပ်ဒိတ်',
        viewAll: 'အက်ပ်အားလုံးကြည့်ရန်',
        showLess: 'လျှော့ပြရန်',
        emptyState: 'ရှာဖွေမှုနှင့်ကိုက်ညီသော အက်ပ် မတွေ့ပါ',
        opening: 'ဖွင့်နေသည်',
        comingSoon: 'System link is being prepared',
        copied: 'Clipboard သို့ ကူးယူပြီးပါပြီ',
        unavailableCopy: 'ဆက်သွယ်ရန်အချက်အလက် ကူးယူပြီးပါပြီ',
        calendarToast: 'နမူနာပြက္ခဒိန် အသင့်ဖြစ်သည်။ နောက်ပိုင်း company calendar နှင့် ချိတ်ဆက်နိုင်သည်။',
        noSearch: 'ရှာဖွေမှုကို ရှင်းလင်းပြီးပါပြီ',
        announcementTitle: 'ကြေညာချက်',
        announcementSub: 'HR မှ သတင်းနှင့် စာရွက်စာတမ်းများ',
        announcementModalSub: 'Supavut Group ဝန်ထမ်းများအတွက် HR မှ ကြေညာချက်၊ သတင်းနှင့် စာရွက်စာတမ်းများ',
        viewAllNews: 'အားလုံးကြည့်ရန်',
        announcementEmpty: 'HR မှ ကြေညာချက် မရှိသေးပါ',
        announcementNew: 'အသစ်',
        announcementNoCover: 'သတင်း'
      }
    };

    const appTranslations = {
      omnex: {
        en: ['Omnex (Admin)', 'System for OMNEX administration and management'],
        th: ['Omnex (Admin)', 'ระบบจัดการและดูแลข้อมูล OMNEX'],
        my: ['Omnex (Admin)', 'OMNEX စီမံခန့်ခွဲရေးစနစ်']
      },
      meeting: {
        en: ['Meeting Room System', 'Manage and book meeting rooms and facilities'],
        th: ['Meeting Room System', 'จองห้องประชุมและจัดการอุปกรณ์ส่วนกลาง'],
        my: ['Meeting Room System', 'အစည်းအဝေးခန်းနှင့် facility များကို ဘွတ်ကင်လုပ်ရန်']
      },
      car: {
        en: ['Car System', 'Company car reservation and fleet management system'],
        th: ['Car System', 'ระบบจองรถบริษัทและจัดการการใช้งานรถ'],
        my: ['Car System', 'ကုမ္ပဏီကား ဘွတ်ကင်နှင့် fleet စီမံခန့်ခွဲမှု']
      },
      ticket: {
        en: ['Ticket', 'IT and service desk support system'],
        th: ['Ticket', 'ระบบเปิดตั๋วเพื่อขอรับบริการหรือแจ้งปัญหา'],
        my: ['Ticket', 'IT နှင့် service desk support စနစ်']
      },
      ot: {
        en: ['E-Leaver', 'Online leave request and approval system'],
        th: ['E-Leaver', 'ระบบลาออนไลน์และขออนุมัติการลา'],
        my: ['E-Leaver', 'Online leave request and approval system']
      },
      memo: {
        en: ['Memo Online', 'Create, send and track internal memos'],
        th: ['Memo Online', 'สร้าง ส่ง และติดตามเอกสารอนุมัติภายใน'],
        my: ['Memo Online', 'အတွင်းပိုင်း memo များ ဖန်တီး၊ ပို့ပြီး ခြေရာခံရန်']
      },
      pr: {
        en: ['Request PR', 'Purchase request submission and tracking'],
        th: ['Request PR', 'ระบบขอเปิด PR และติดตามคำขอจัดซื้อ'],
        my: ['Request PR', 'ဝယ်ယူမှုတောင်းဆိုချက် တင်ပြီး ခြေရာခံရန်']
      },
      printer: {
        en: ['Request Printer', 'Request and manage printer or equipment support'],
        th: ['Request Printer', 'ระบบขอใช้งานและจัดการงานเครื่องพิมพ์'],
        my: ['Request Printer', 'Printer သို့မဟုတ် equipment support တောင်းဆိုရန်']
      },
      it: {
        en: ['IT Service', 'Request IT support and operational assistance'],
        th: ['IT Service', 'แจ้งงานและขอรับบริการจากฝ่าย IT'],
        my: ['IT Service', 'IT support နှင့် operational assistance တောင်းဆိုရန်']
      },
      visitor: {
        en: ['Visitor', 'Visitor registration and appointment support'],
        th: ['Visitor', 'ลงทะเบียนและจัดการข้อมูลผู้มาติดต่อ'],
        my: ['Visitor', 'ဧည့်သည်စာရင်းသွင်းခြင်းနှင့် appointment support']
      },
      repair: {
        en: ['Machine Repair (MT)', 'Machine repair request sent to MT team'],
        th: ['Machine Repair (MT)', 'แจ้งซ่อมเครื่องจักรและส่งงานถึงทีม MT'],
        my: ['Machine Repair (MT)', 'စက်ပြင်တောင်းဆိုချက်ကို MT team သို့ ပို့ရန်']
      },
      she: {
        en: ['SHE Repair', 'Safety repair request for general, vehicle and workplace issues'],
        th: ['SHE Repair', 'แจ้งซ่อมด้านความปลอดภัย รถ และพื้นที่ทำงาน'],
        my: ['SHE Repair', 'Safety, vehicle နှင့် workplace ပြဿနာများအတွက် repair request']
      },
      supavutAssessment: {
        en: ['Supavut Assessment', 'Employee assessment system'],
        th: ['Supavut Assessment', 'ระบบประเมินพนักงานของ Supavut'],
        my: ['Supavut Assessment', 'Employee assessment system']
      },
      penaltyBonus: {
        en: ['Supavut Penalty', 'Penalty and bonus management system'],
        th: ['Supavut Penalty', 'ระบบจัดการบทลงโทษและโบนัส'],
        my: ['Supavut Penalty', 'Penalty and bonus management system']
      },
      okrKpi: {
        en: ['OKR - KPI System', 'Objective and performance tracking system'],
        th: ['OKR - KPI System', 'ระบบติดตามเป้าหมายและตัวชี้วัดผลงาน'],
        my: ['OKR - KPI System', 'Objective and performance tracking system']
      }
    };

    const apps = [
      { id: 'omnex', category: 'admin', image: 'img/omnax_admin.png', url: 'http://192.168.7.12/EwQIMS/Common/EwIMSNew/homepage/Index' },
      { id: 'meeting', category: 'booking', image: 'img/meeting.png', url: 'http://192.168.5.6/meeting/' },
      { id: 'car', category: 'booking', image: 'img/car.png', url: 'http://192.168.5.6/vehicle/' },
      { id: 'ticket', category: 'support', image: 'img/ticket.png', url: 'http://192.168.5.6/logistic/index2.php' },
      { id: 'ot', category: 'hr', image: 'img/business-plus.png', url: 'https://e-leave.supavut.com/Login/Login.aspx' },
      { id: 'supavutAssessment', category: 'hr', image: 'img/supavut-assessment-logo.png', url: 'http://192.168.7.12:8080/Supavut_Assessment/public/index.php' },
      { id: 'penaltyBonus', category: 'hr', image: 'img/supavut-penalty-logo.png', url: 'http://192.168.7.12:8080/Supavut_penalty&bonus/public/index.php' },
      { id: 'okrKpi', category: 'hr', image: 'img/okr-kpi-logo.png', url: 'http://192.168.7.12:8080/okr-kpi-system/public/index.php' },
      { id: 'memo', category: 'admin', image: 'img/memo.png', url: 'http://192.168.5.7/Docusign/login.php' },
      { id: 'pr', category: 'request', image: 'img/buy.png', url: 'http://192.168.5.7/RequestPR/login.php' },
      { id: 'printer', category: 'request', image: 'img/printer2.png', url: 'http://192.168.5.7/printer_/login.php' },
      { id: 'it', category: 'support', image: 'img/it_service.png', url: 'http://192.168.5.7/ITService/login.php' },
      { id: 'visitor', category: 'booking', image: 'img/vs.png', url: 'http://192.168.5.7/visitor/login.php' },
      { id: 'repair', category: 'safety', image: 'img/repair.png', url: 'http://192.168.5.7/MT_repair/index.php' },
      { id: 'she', category: 'safety', image: 'img/she_repair.png', url: 'http://192.168.5.7/SHE_Repair/index.php' }
    ];

    const categories = [
      { key: 'all', th: 'ทั้งหมด', en: 'All', my: 'အားလုံး' },
      { key: 'hr', th: 'งานบุคคล', en: 'HR', my: 'HR' },
      { key: 'booking', th: 'จอง/นัดหมาย', en: 'Booking', my: 'ဘွတ်ကင်' },
      { key: 'request', th: 'คำขอ/จัดซื้อ', en: 'Requests', my: 'တောင်းဆိုမှု' },
      { key: 'support', th: 'IT & สนับสนุน', en: 'IT & Support', my: 'IT & ပံ့ပိုးမှု' },
      { key: 'safety', th: 'ศูนย์รวมการแจ้งซ๋อม', en: 'Repair Center', my: 'Repair Center' },
      { key: 'admin', th: 'เอกสาร/จัดการ', en: 'Docs & Admin', my: 'စာရွက်စာတမ်း' }
    ];

    const galleryImages = <?php echo json_encode($galleryImages); ?>;
    const localeMap = { en: 'en-US', th: 'th-TH', my: 'my-MM' };
    const html = document.documentElement;
    const appGrid = document.getElementById('appGrid');
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const languageTrigger = document.getElementById('languageTrigger');
    const languagePanel = document.getElementById('languagePanel');
    const languageCode = document.getElementById('languageCode');
    const emptyState = document.getElementById('emptyState');
    const toggleApps = document.getElementById('toggleApps');
    const announcementModalGrid = document.getElementById('announcementModalGrid');
    const announcementPrevPage = document.getElementById('announcementPrevPage');
    const announcementNextPage = document.getElementById('announcementNextPage');
    const announcementPageNumbers = document.getElementById('announcementPageNumbers');
    const announcementWidgetGrid = document.getElementById('announcementWidgetGrid');
    const announcementWidgetPrev = document.getElementById('announcementWidgetPrev');
    const announcementWidgetNext = document.getElementById('announcementWidgetNext');
    const announcementWidgetDots = document.getElementById('announcementWidgetDots');
    const categoryTabs = document.getElementById('categoryTabs');
    const toast = document.getElementById('toast');
    const monthLabel = document.getElementById('monthLabel');
    const calendarGrid = document.getElementById('calendarGrid');
    const clockTime = document.getElementById('clockTime');
    const clockDate = document.getElementById('clockDate');
    const monthLabelLarge = document.getElementById('monthLabelLarge');
    const calendarGridLarge = document.getElementById('calendarGridLarge');
    const wxForecast = document.getElementById('wxForecast');
    const wxTemp = document.getElementById('wxTemp');
    const wxCond = document.getElementById('wxCond');
    const wxMiniForecast = document.getElementById('wxMiniForecast');
    const wxModalTemp = document.getElementById('wxModalTemp');
    const wxModalSub = document.getElementById('wxModalSub');
    const wxFeels = document.getElementById('wxFeels');
    const wxHumidity = document.getElementById('wxHumidity');
    const wxWind = document.getElementById('wxWind');
    const wxUv = document.getElementById('wxUv');
    const wxIcon = document.getElementById('wxIcon');
    const wxHeroIcon = document.getElementById('wxHeroIcon');
    const heroPhoto = document.getElementById('heroPhoto');

    const languageStorageKey = 'simenu_language';
    const supportedLanguages = ['th', 'en', 'my'];
    let currentLang = getStoredLanguage();
    let showAllApps = false;
    let activeCategory = 'all';
    let calendarDate = new Date();
    let toastTimer = null;
    let announcementPage = 1;
    let announcementWidgetPage = 1;
    let announcementWidgetSlideTimer = null;

    function t(key) {
      return translations[currentLang][key] || translations.en[key] || key;
    }

    function normalizeLanguage(lang) {
      return supportedLanguages.includes(lang) ? lang : 'th';
    }

    function getStoredLanguage() {
      try {
        return normalizeLanguage(localStorage.getItem(languageStorageKey) || 'th');
      } catch (error) {
        return 'th';
      }
    }

    function saveStoredLanguage(lang) {
      try {
        localStorage.setItem(languageStorageKey, normalizeLanguage(lang));
      } catch (error) {
        // Keep the page usable if browser storage is blocked.
      }
    }

    function showToast(message) {
      clearTimeout(toastTimer);
      toast.textContent = message;
      toast.classList.add('is-visible');
      toastTimer = setTimeout(() => {
        toast.classList.remove('is-visible');
      }, 2600);
    }

    function setLanguageMenu(open) {
      languagePanel.classList.toggle('is-open', open);
      languageTrigger.setAttribute('aria-expanded', String(open));
    }

    function setLanguage(lang, shouldSave = true) {
      lang = normalizeLanguage(lang);
      currentLang = lang;
      html.lang = lang === 'my' ? 'my' : lang;

      document.querySelectorAll('[data-i18n]').forEach((node) => {
        node.textContent = t(node.dataset.i18n);
      });

      document.querySelectorAll('[data-i18n-placeholder]').forEach((node) => {
        node.setAttribute('placeholder', t(node.dataset.i18nPlaceholder));
      });

      document.querySelectorAll('.lang-btn').forEach((button) => {
        button.classList.toggle('is-active', button.dataset.lang === lang);
      });

      if (languageCode) {
        languageCode.textContent = lang.toUpperCase();
      }

      renderCategories();
      renderApps();
      renderCalendar();
      renderAnnouncementPage();
      applyNewsTranslations();
      updateClock();
      if (weatherData) {
        renderWeather();
      } else {
        forecastList = getFallbackForecast();
        if (wxCond) {
          wxCond.textContent = wmoInfo(2)[0];
        }
        if (wxModalSub) {
          wxModalSub.textContent = `${t('bangkokOffice')} · ${wmoInfo(2)[0]}`;
        }
        if (wxWind) {
          wxWind.textContent = `12 ${t('windUnit')}`;
        }
        renderForecast();
        renderMiniForecast();
      }

      if (shouldSave) {
        saveStoredLanguage(lang);
      }
    }

    function categoryLabel(category) {
      return category[currentLang] || category.en;
    }

    function renderCategories() {
      if (!categoryTabs) {
        return;
      }
      categoryTabs.innerHTML = categories.map((category) => {
        const activeClass = category.key === activeCategory ? ' is-active' : '';
        return `<button class="tab-btn${activeClass}" type="button" data-category="${category.key}">${categoryLabel(category)}</button>`;
      }).join('');
    }

    function getFilteredApps() {
      const query = searchInput.value.trim().toLowerCase();

      return apps.filter((app) => {
        const copy = appTranslations[app.id][currentLang] || appTranslations[app.id].en;
        const haystack = `${copy[0]} ${copy[1]}`.toLowerCase();
        const categoryMatch = activeCategory === 'all' || app.category === activeCategory;
        const searchMatch = query === '' || haystack.includes(query);
        return categoryMatch && searchMatch;
      });
    }

    function renderApps() {
      const filteredApps = getFilteredApps();
      const visibleApps = showAllApps ? filteredApps : filteredApps.slice(0, 10);

      appGrid.innerHTML = visibleApps.map((app) => {
        const copy = appTranslations[app.id][currentLang] || appTranslations[app.id].en;
        const href = app.url || '#';
        const cardState = app.url ? '' : ' is-disabled';
        const linkAttrs = app.url ? 'target="_blank" rel="noopener noreferrer"' : 'aria-disabled="true"';

        return `
          <a class="app-card${cardState}" href="${href}" ${linkAttrs} data-app-id="${app.id}" data-app-name="${copy[0]}" data-app-url="${app.url}">
            <div class="app-icon">
              <img src="${app.image}" alt="${copy[0]} icon" loading="lazy">
            </div>
            <div>
              <h3 class="app-name">${copy[0]}</h3>
              <p class="app-desc">${copy[1]}</p>
            </div>
            <span class="open-arrow" aria-hidden="true">&rsaquo;</span>
          </a>
        `;
      }).join('');

      emptyState.classList.toggle('is-visible', filteredApps.length === 0);
      toggleApps.style.display = filteredApps.length > 10 ? 'inline-flex' : 'none';
      toggleApps.querySelector('span').textContent = showAllApps ? t('showLess') : t('viewAll');
    }

    function renderCalendar() {
      const year = calendarDate.getFullYear();
      const month = calendarDate.getMonth();
      const today = new Date();
      const firstDay = new Date(year, month, 1);
      const startDay = (firstDay.getDay() + 6) % 7; // Monday-first
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const previousMonthDays = new Date(year, month, 0).getDate();
      const locale = localeMap[currentLang] || 'en-US';
      const weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

      const monthText = new Intl.DateTimeFormat(locale, {
        month: 'long',
        year: 'numeric'
      }).format(calendarDate);
      monthLabel.textContent = monthText;
      if (monthLabelLarge) {
        monthLabelLarge.textContent = monthText;
      }

      const cells = [];

      weekdays.forEach((day) => {
        cells.push(`<span class="weekday">${day}</span>`);
      });

      for (let i = startDay - 1; i >= 0; i -= 1) {
        cells.push(`<span class="muted-day">${previousMonthDays - i}</span>`);
      }

      for (let day = 1; day <= daysInMonth; day += 1) {
        const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
        cells.push(`<span class="${isToday ? 'today' : ''}">${day}</span>`);
      }

      while (cells.length < 49) {
        cells.push(`<span class="muted-day">${cells.length - (7 + startDay + daysInMonth) + 1}</span>`);
      }

      const markup = cells.join('');
      calendarGrid.innerHTML = markup;
      if (calendarGridLarge) {
        calendarGridLarge.innerHTML = markup;
      }
    }

    // WMO weather codes → emoji (ไม่ขึ้นกับภาษา)
    const weatherEmoji = {
      0: '☀️', 1: '🌤️', 2: '⛅', 3: '☁️',
      45: '🌫️', 48: '🌫️',
      51: '🌦️', 53: '🌦️', 55: '🌦️',
      61: '🌧️', 63: '🌧️', 65: '🌧️',
      66: '🌧️', 67: '🌧️',
      71: '🌨️', 73: '🌨️', 75: '❄️', 77: '🌨️',
      80: '🌦️', 81: '🌧️', 82: '⛈️',
      85: '🌨️', 86: '❄️',
      95: '⛈️', 96: '⛈️', 99: '⛈️'
    };

    // WMO weather codes → ข้อความตามภาษา
    const weatherConditions = {
      en: {
        0: 'Clear sky', 1: 'Mostly clear', 2: 'Partly cloudy', 3: 'Cloudy',
        45: 'Fog', 48: 'Freezing fog',
        51: 'Light drizzle', 53: 'Drizzle', 55: 'Heavy drizzle',
        61: 'Light rain', 63: 'Moderate rain', 65: 'Heavy rain',
        66: 'Freezing rain', 67: 'Heavy freezing rain',
        71: 'Light snow', 73: 'Snow', 75: 'Heavy snow', 77: 'Snow grains',
        80: 'Light showers', 81: 'Showers', 82: 'Heavy showers',
        85: 'Snow showers', 86: 'Heavy snow showers',
        95: 'Thunderstorm', 96: 'Thunderstorm with hail', 99: 'Severe thunderstorm'
      },
      th: {
        0: 'ท้องฟ้าแจ่มใส', 1: 'ส่วนใหญ่แจ่มใส', 2: 'มีเมฆบางส่วน', 3: 'เมฆมาก',
        45: 'หมอก', 48: 'หมอกแข็งตัว',
        51: 'ฝนปรอยเล็กน้อย', 53: 'ฝนปรอย', 55: 'ฝนปรอยหนัก',
        61: 'ฝนเล็กน้อย', 63: 'ฝนปานกลาง', 65: 'ฝนหนัก',
        66: 'ฝนเยือกแข็ง', 67: 'ฝนเยือกแข็งหนัก',
        71: 'หิมะเล็กน้อย', 73: 'หิมะ', 75: 'หิมะหนัก', 77: 'เม็ดหิมะ',
        80: 'ฝนซู่เล็กน้อย', 81: 'ฝนซู่', 82: 'ฝนซู่หนัก',
        85: 'หิมะซู่', 86: 'หิมะซู่หนัก',
        95: 'ฝนฟ้าคะนอง', 96: 'ฝนฟ้าคะนองมีลูกเห็บ', 99: 'ฝนฟ้าคะนองรุนแรง'
      },
      my: {
        0: 'ကောင်းကင်ကြည်လင်', 1: 'အများစုကြည်လင်', 2: 'တိမ်အနည်းငယ်', 3: 'တိမ်များ',
        45: 'မြူခိုး', 48: 'အေးခဲမြူခိုး',
        51: 'ဖွဲဖွဲမိုးအနည်းငယ်', 53: 'ဖွဲဖွဲမိုး', 55: 'ဖွဲဖွဲမိုးပြင်း',
        61: 'မိုးအနည်းငယ်', 63: 'မိုးအလယ်အလတ်', 65: 'မိုးသည်းထန်',
        66: 'အေးခဲမိုး', 67: 'အေးခဲမိုးပြင်း',
        71: 'နှင်းအနည်းငယ်', 73: 'နှင်း', 75: 'နှင်းထူ', 77: 'နှင်းမှုန့်',
        80: 'မိုးရေဖွဲနှင်းအနည်းငယ်', 81: 'မိုးရေဖွဲ', 82: 'မိုးရေဖွဲပြင်း',
        85: 'နှင်းရွာသွန်း', 86: 'နှင်းရွာသွန်းပြင်း',
        95: 'မိုးကြိုးပစ်', 96: 'မိုးကြိုးပစ်နှင့်မိုးသီး', 99: 'မိုးကြိုးပစ်ပြင်းထန်'
      }
    };

    function wmoInfo(code) {
      const conditions = weatherConditions[currentLang] || weatherConditions.en;
      const text = conditions[code] || t('unknownWeather');
      const emoji = weatherEmoji[code] || '🌡️';
      return [text, emoji];
    }

    function weatherCategory(code) {
      if ([95, 96, 99].includes(code)) return 'storm';
      if ([71, 73, 75, 77, 85, 86].includes(code)) return 'snow';
      if ([51, 53, 55, 61, 63, 65, 66, 67, 80, 81, 82].includes(code)) return 'rain';
      if ([0, 1].includes(code)) return 'clear';
      return 'cloudy';
    }

    function renderWeatherIcon(container, code) {
      if (!container) {
        return;
      }
      const category = weatherCategory(code);
      container.classList.remove('wx-icon--clear', 'wx-icon--cloudy', 'wx-icon--rain', 'wx-icon--storm', 'wx-icon--snow');
      container.classList.add(`wx-icon--${category}`);
      container.querySelectorAll('.rain-drop, .snow-flake, .lightning-bolt').forEach((el) => el.remove());

      if (category === 'rain' || category === 'storm') {
        [18, 30, 42].forEach((left, i) => {
          const drop = document.createElement('span');
          drop.className = 'rain-drop';
          drop.style.left = `${left}%`;
          drop.style.animationDelay = `${i * 0.25}s`;
          container.appendChild(drop);
        });
      }

      if (category === 'snow') {
        [16, 30, 44].forEach((left, i) => {
          const flake = document.createElement('span');
          flake.className = 'snow-flake';
          flake.style.left = `${left}%`;
          flake.style.animationDelay = `${i * 0.5}s`;
          container.appendChild(flake);
        });
      }

      if (category === 'storm') {
        const bolt = document.createElement('span');
        bolt.className = 'lightning-bolt';
        bolt.textContent = '⚡';
        container.appendChild(bolt);
      }
    }

    let weatherData = null;

    // ค่าตัวอย่าง (ใช้ก่อน API ตอบ / กรณีเชื่อมต่อไม่ได้)
    function getFallbackForecast() {
      return [
        { day: t('todayLabel'), cond: wmoInfo(2)[0], high: 32, low: 25, today: true },
        { day: t('tomorrowLabel'), cond: wmoInfo(0)[0], high: 34, low: 26 },
        { day: t('dayAfterLabel'), cond: wmoInfo(2)[0], high: 33, low: 25 }
      ];
    }

    let forecastList = getFallbackForecast();

    function updateClock() {
      if (!clockTime) {
        return;
      }
      const now = new Date();
      const locale = localeMap[currentLang] || 'en-US';
      clockTime.textContent = now.toLocaleTimeString(locale, {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      });
      if (clockDate) {
        clockDate.textContent = new Intl.DateTimeFormat(locale, {
          weekday: 'long',
          day: 'numeric',
          month: 'short',
          year: 'numeric'
        }).format(now);
      }
    }

    function renderForecast() {
      if (!wxForecast) {
        return;
      }
      wxForecast.innerHTML = forecastList.map((item) => `
        <div class="wx-fc-row${item.today ? ' today' : ''}">
          <span class="wx-fc-day">${item.day}</span>
          <span class="wx-fc-cond">${item.cond}</span>
          <span class="wx-fc-temp">${item.high}°<span class="low">${item.low}°</span></span>
        </div>
      `).join('');
    }

    function renderMiniForecast() {
      if (!wxMiniForecast) {
        return;
      }
      const days = forecastList.slice(1, 4);
      if (!days.length) {
        return;
      }
      wxMiniForecast.innerHTML = days.map((item) => `
        <div>
          <div class="forecast-day">${item.short || item.day}</div>
          <div class="forecast-temps"><span class="forecast-high">${item.high}°</span><span class="forecast-low">${item.low}°</span></div>
        </div>
      `).join('');
    }

    function renderWeather() {
      if (!weatherData) {
        return;
      }
      const cur = weatherData.current;
      const daily = weatherData.daily;
      const locale = localeMap[currentLang] || 'th-TH';
      const [condText] = wmoInfo(cur.weather_code);

      if (wxTemp) wxTemp.textContent = `${Math.round(cur.temperature_2m)}°C`;
      if (wxCond) wxCond.textContent = condText;
      if (wxModalTemp) wxModalTemp.innerHTML = `${Math.round(cur.temperature_2m)}<span>°C</span>`;
      if (wxModalSub) wxModalSub.textContent = `${t('bangkokOffice')} · ${condText}`;
      if (wxFeels) wxFeels.textContent = `${Math.round(cur.apparent_temperature)}°`;
      if (wxHumidity) wxHumidity.textContent = `${cur.relative_humidity_2m}%`;
      if (wxWind) wxWind.textContent = `${Math.round(cur.wind_speed_10m)} ${t('windUnit')}`;
      if (wxUv && daily.uv_index_max) wxUv.textContent = `${Math.round(daily.uv_index_max[0])}`;
      renderWeatherIcon(wxIcon, cur.weather_code);
      renderWeatherIcon(wxHeroIcon, cur.weather_code);

      forecastList = daily.time.map((iso, i) => {
        const date = new Date(`${iso}T00:00:00`);
        const [cond] = wmoInfo(daily.weather_code[i]);
        return {
          day: i === 0 ? t('todayLabel') : new Intl.DateTimeFormat(locale, { weekday: 'long' }).format(date),
          short: i === 0 ? t('todayLabel') : new Intl.DateTimeFormat(locale, { weekday: 'short' }).format(date),
          cond,
          high: Math.round(daily.temperature_2m_max[i]),
          low: Math.round(daily.temperature_2m_min[i]),
          today: i === 0
        };
      });

      renderForecast();
      renderMiniForecast();
    }

    async function fetchWeather() {
      const url = 'https://api.open-meteo.com/v1/forecast?latitude=13.7563&longitude=100.5018'
        + '&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m'
        + '&daily=weather_code,temperature_2m_max,temperature_2m_min,uv_index_max'
        + '&timezone=Asia%2FBangkok&forecast_days=7';
      try {
        const res = await fetch(url);
        if (!res.ok) {
          throw new Error('weather request failed');
        }
        weatherData = await res.json();
        renderWeather();
      } catch (error) {
        // เชื่อมต่อไม่ได้ → คงค่าตัวอย่างไว้
      }
    }

    function applyNewsTranslations() {
      document.querySelectorAll('[data-news-link]').forEach((card) => {
        const titleEl = card.querySelector('h3');
        const bodyEl = card.querySelector('p');
        const titleTh = card.dataset.titleTh || '';
        const bodyTh = card.dataset.bodyTh || '';
        const titleLang = currentLang === 'en' ? card.dataset.titleEn : (currentLang === 'my' ? card.dataset.titleMy : '');
        const bodyLang = currentLang === 'en' ? card.dataset.bodyEn : (currentLang === 'my' ? card.dataset.bodyMy : '');
        if (titleEl) {
          titleEl.textContent = titleLang && titleLang.trim() !== '' ? titleLang : titleTh;
        }
        if (bodyEl) {
          bodyEl.textContent = bodyLang && bodyLang.trim() !== '' ? bodyLang : bodyTh;
        }
      });
    }

    function renderAnnouncementPage() {
      if (!announcementModalGrid) {
        return;
      }
      const items = Array.from(announcementModalGrid.querySelectorAll('.announcement-modal-item'));
      if (items.length === 0) {
        return;
      }
      const totalPages = items.reduce((max, item) => Math.max(max, Number(item.dataset.page)), 1);
      if (announcementPage > totalPages) {
        announcementPage = totalPages;
      }
      if (announcementPage < 1) {
        announcementPage = 1;
      }
      items.forEach((item) => {
        item.classList.toggle('is-hidden', Number(item.dataset.page) !== announcementPage);
      });
      if (announcementPageNumbers) {
        let numbersHtml = '';
        for (let page = 1; page <= totalPages; page++) {
          const activeClass = page === announcementPage ? ' is-active' : '';
          numbersHtml += `<button class="pagination-number${activeClass}" type="button" data-page-number="${page}">${page}</button>`;
        }
        announcementPageNumbers.innerHTML = numbersHtml;
      }
      if (announcementPrevPage) {
        announcementPrevPage.disabled = announcementPage <= 1;
      }
      if (announcementNextPage) {
        announcementNextPage.disabled = announcementPage >= totalPages;
      }
    }

    function animateAnnouncementWidget(direction) {
      if (!announcementWidgetGrid || direction === 0) {
        return;
      }

      window.clearTimeout(announcementWidgetSlideTimer);
      announcementWidgetGrid.classList.remove('is-moving-next', 'is-moving-prev');
      announcementWidgetGrid.offsetWidth;
      announcementWidgetGrid.classList.add(direction > 0 ? 'is-moving-next' : 'is-moving-prev');

      announcementWidgetSlideTimer = window.setTimeout(() => {
        announcementWidgetGrid.classList.remove('is-moving-next', 'is-moving-prev');
      }, 240);
    }

    function renderAnnouncementWidgetPage(direction = 0) {
      if (!announcementWidgetGrid) {
        return;
      }
      const items = Array.from(announcementWidgetGrid.querySelectorAll('.announcement-tile'));
      if (items.length === 0) {
        return;
      }
      const totalPages = items.reduce((max, item) => Math.max(max, Number(item.dataset.page)), 1);
      if (announcementWidgetPage > totalPages) {
        announcementWidgetPage = totalPages;
      }
      if (announcementWidgetPage < 1) {
        announcementWidgetPage = 1;
      }
      items.forEach((item) => {
        item.classList.toggle('is-hidden', Number(item.dataset.page) !== announcementWidgetPage);
      });
      if (announcementWidgetPrev) {
        announcementWidgetPrev.disabled = announcementWidgetPage <= 1;
      }
      if (announcementWidgetNext) {
        announcementWidgetNext.disabled = announcementWidgetPage >= totalPages;
      }
      if (announcementWidgetDots) {
        let dotsHtml = '';
        for (let page = 1; page <= totalPages; page++) {
          const activeClass = page === announcementWidgetPage ? ' is-active' : '';
          dotsHtml += `<button class="carousel-dot${activeClass}" type="button" data-widget-page="${page}" aria-label="Page ${page}"></button>`;
        }
        announcementWidgetDots.innerHTML = dotsHtml;
      }

      animateAnnouncementWidget(direction);
    }

    function openModal(id) {
      const modal = document.getElementById(id);
      if (!modal) {
        return;
      }
      modal.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }

    function closeModals() {
      document.querySelectorAll('.modal.is-open').forEach((modal) => {
        modal.classList.remove('is-open');
      });
      document.body.style.overflow = '';
    }

    function openApp(event) {
      const card = event.target.closest('.app-card');
      if (!card) {
        return;
      }

      event.preventDefault();
      const appName = card.dataset.appName;
      if (!card.dataset.appUrl) {
        showToast(`${appName}: ${t('comingSoon')}`);
        return;
      }

      showToast(`${t('opening')} ${appName}`);
      window.open(card.href, '_blank', 'noopener,noreferrer');
    }

    function bindEvents() {
      languageTrigger.addEventListener('click', () => {
        setLanguageMenu(!languagePanel.classList.contains('is-open'));
      });

      document.querySelectorAll('.lang-btn').forEach((button) => {
        button.addEventListener('click', () => {
          setLanguage(button.dataset.lang);
          setLanguageMenu(false);
        });
      });

      document.addEventListener('click', (event) => {
        if (!event.target.closest('.language-menu')) {
          setLanguageMenu(false);
        }
      });

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          setLanguageMenu(false);
          closeModals();
        }
      });

      document.querySelectorAll('.modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
          if (event.target === modal || event.target.closest('[data-close-modal]')) {
            closeModals();
          }
        });
      });

      searchInput.addEventListener('input', () => {
        clearSearch.classList.toggle('is-visible', searchInput.value.trim() !== '');
        showAllApps = false;
        renderApps();
      });

      clearSearch.addEventListener('click', () => {
        searchInput.value = '';
        clearSearch.classList.remove('is-visible');
        renderApps();
        showToast(t('noSearch'));
        searchInput.focus();
      });

      toggleApps.addEventListener('click', () => {
        showAllApps = !showAllApps;
        renderApps();
      });

      if (categoryTabs) {
        categoryTabs.addEventListener('click', (event) => {
          const button = event.target.closest('[data-category]');
          if (!button) {
            return;
          }
          activeCategory = button.dataset.category;
          showAllApps = true;
          renderCategories();
          renderApps();
        });
      }

      appGrid.addEventListener('click', openApp);

      document.querySelectorAll('[data-action="calendar"]').forEach((button) => {
        button.addEventListener('click', () => openModal('calendarModal'));
      });

      document.querySelectorAll('[data-action="weather"]').forEach((button) => {
        button.addEventListener('click', () => openModal('weatherModal'));
      });

      document.querySelectorAll('[data-action="announcements"]').forEach((button) => {
        button.addEventListener('click', () => {
          announcementPage = 1;
          renderAnnouncementPage();
          openModal('announcementsModal');
        });
      });

      function shiftMonth(delta) {
        calendarDate = new Date(calendarDate.getFullYear(), calendarDate.getMonth() + delta, 1);
        renderCalendar();
      }

      document.getElementById('prevMonth').addEventListener('click', () => shiftMonth(-1));
      document.getElementById('nextMonth').addEventListener('click', () => shiftMonth(1));
      document.getElementById('prevMonthLg').addEventListener('click', () => shiftMonth(-1));
      document.getElementById('nextMonthLg').addEventListener('click', () => shiftMonth(1));

      if (announcementPrevPage) {
        announcementPrevPage.addEventListener('click', () => {
          announcementPage -= 1;
          renderAnnouncementPage();
        });
      }

      if (announcementNextPage) {
        announcementNextPage.addEventListener('click', () => {
          announcementPage += 1;
          renderAnnouncementPage();
        });
      }

      if (announcementPageNumbers) {
        announcementPageNumbers.addEventListener('click', (event) => {
          const button = event.target.closest('[data-page-number]');
          if (!button) {
            return;
          }
          announcementPage = Number(button.dataset.pageNumber);
          renderAnnouncementPage();
        });
      }

      if (announcementWidgetPrev) {
        announcementWidgetPrev.addEventListener('click', () => {
          announcementWidgetPage -= 1;
          renderAnnouncementWidgetPage(-1);
        });
      }

      if (announcementWidgetNext) {
        announcementWidgetNext.addEventListener('click', () => {
          announcementWidgetPage += 1;
          renderAnnouncementWidgetPage(1);
        });
      }

      if (announcementWidgetDots) {
        announcementWidgetDots.addEventListener('click', (event) => {
          const button = event.target.closest('[data-widget-page]');
          if (!button) {
            return;
          }
          const nextPage = Number(button.dataset.widgetPage);
          const direction = nextPage > announcementWidgetPage ? 1 : nextPage < announcementWidgetPage ? -1 : 0;
          announcementWidgetPage = nextPage;
          renderAnnouncementWidgetPage(direction);
        });
      }
    }

    window.addEventListener('storage', (event) => {
      if (event.key === languageStorageKey) {
        setLanguage(event.newValue || 'th', false);
      }
    });

    bindEvents();
    renderAnnouncementPage();
    renderAnnouncementWidgetPage();
    renderForecast();
    renderMiniForecast();
    renderWeatherIcon(wxIcon, 2);
    renderWeatherIcon(wxHeroIcon, 2);
    setLanguage(currentLang, false);
    updateClock();
    setInterval(updateClock, 1000);

    // สภาพอากาศจริง (Open-Meteo) + รีเฟรชทุก 10 นาที
    fetchWeather();
    setInterval(fetchWeather, 600000);

    // ภาพ hero เลื่อนอัตโนมัติทุก 3 วินาที
    if (heroPhoto && galleryImages.length > 1) {
      let heroIndex = 0;
      setInterval(() => {
        heroIndex = (heroIndex + 1) % galleryImages.length;
        const nextSrc = galleryImages[heroIndex];
        heroPhoto.style.opacity = '0';
        setTimeout(() => {
          heroPhoto.src = nextSrc;
          heroPhoto.style.opacity = '1';
        }, 600);
      }, 3000);
    }

    // ── AI Assistant: draggable floating button + mini chat ──
    (function aiAssistant() {
      const fab = document.getElementById('aiFab');
      const chat = document.getElementById('aiChat');
      const closeBtn = document.getElementById('aiChatClose');
      const form = document.getElementById('aiChatForm');
      const input = document.getElementById('aiChatText');
      const chatBody = document.getElementById('aiChatBody');

      if (!fab || !chat) {
        return;
      }

      let dragging = false;
      let moved = false;
      let startX = 0;
      let startY = 0;
      let originLeft = 0;
      let originTop = 0;

      function placeChat() {
        const rect = fab.getBoundingClientRect();
        const w = chat.offsetWidth;
        const h = chat.offsetHeight;
        let left = rect.right - w;
        let top = rect.top - h - 12;
        if (top < 12) {
          top = rect.bottom + 12;
        }
        left = Math.max(12, Math.min(left, window.innerWidth - w - 12));
        top = Math.max(12, Math.min(top, window.innerHeight - h - 12));
        chat.style.left = `${left}px`;
        chat.style.top = `${top}px`;
        chat.style.right = 'auto';
        chat.style.bottom = 'auto';
      }

      function openChat() {
        chat.classList.add('is-open');
        placeChat();
        if (input) {
          input.focus();
        }
      }

      function closeChat() {
        chat.classList.remove('is-open');
      }

      function toggleChat() {
        if (chat.classList.contains('is-open')) {
          closeChat();
        } else {
          openChat();
        }
      }

      function appendMessage(message, who) {
        const wrap = document.createElement('div');
        wrap.className = `ai-msg ${who}`;
        const avatar = who === 'user'
          ? '<span class="ai-msg-ava">🙂</span>'
          : '<span class="ai-msg-ava bot-logo"><img class="ai-msg-logo" src="img/si5.jpg" alt=""></span>';
        wrap.innerHTML = `${avatar}<div class="ai-bubble"></div>`;
        wrap.querySelector('.ai-bubble').textContent = message;
        chatBody.appendChild(wrap);
        chatBody.scrollTop = chatBody.scrollHeight;
      }

      fab.addEventListener('pointerdown', (event) => {
        dragging = true;
        moved = false;
        const rect = fab.getBoundingClientRect();
        originLeft = rect.left;
        originTop = rect.top;
        startX = event.clientX;
        startY = event.clientY;
        fab.classList.add('dragging');
        fab.setPointerCapture(event.pointerId);
      });

      fab.addEventListener('pointermove', (event) => {
        if (!dragging) {
          return;
        }
        const dx = event.clientX - startX;
        const dy = event.clientY - startY;
        if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
          moved = true;
        }
        let left = originLeft + dx;
        let top = originTop + dy;
        left = Math.max(8, Math.min(left, window.innerWidth - fab.offsetWidth - 8));
        top = Math.max(8, Math.min(top, window.innerHeight - fab.offsetHeight - 8));
        fab.style.left = `${left}px`;
        fab.style.top = `${top}px`;
        fab.style.right = 'auto';
        fab.style.bottom = 'auto';
      });

      fab.addEventListener('pointerup', (event) => {
        dragging = false;
        fab.classList.remove('dragging');
        try {
          fab.releasePointerCapture(event.pointerId);
        } catch (error) {
          // pointer already released
        }
        if (!moved) {
          toggleChat();
        } else if (chat.classList.contains('is-open')) {
          placeChat();
        }
      });

      if (closeBtn) {
        closeBtn.addEventListener('click', closeChat);
      }

      if (form) {
        form.addEventListener('submit', (event) => {
          event.preventDefault();
          const value = (input.value || '').trim();
          if (!value) {
            return;
          }
          appendMessage(value, 'user');
          input.value = '';
          setTimeout(() => {
            appendMessage('ขออภัยครับ ฟีเจอร์แชท AI ยังอยู่ในช่วงพัฒนา 🛠️ เร็วๆ นี้จะพร้อมให้บริการครับ', 'bot');
          }, 500);
        });
      }

      window.addEventListener('resize', () => {
        if (chat.classList.contains('is-open')) {
          placeChat();
        }
      });
    })();
  </script>
</body>

</html>
