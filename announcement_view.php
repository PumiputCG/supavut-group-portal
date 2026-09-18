<?php
require_once dirname(__FILE__) . '/announcement_helpers.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$announcements = announcement_read_all();
$match = null;

foreach ($announcements as $item) {
  if (isset($item['id']) && $item['id'] === $id) {
    $match = $item;
    break;
  }
}

if (!$match && function_exists('http_response_code')) {
  http_response_code(404);
}

$attachments = $match ? announcement_get_attachments($match) : array();
$coverUrl = $match ? announcement_cover_url($match) : '';
$isNew = $match ? announcement_is_new($match) : false;
$pageTitle = $match ? $match['title'] . ' | SUPAVUT GROUP' : 'ไม่พบประกาศ | SUPAVUT GROUP';
$galleryImages = announcement_gallery_images();
$backgroundImages = count($galleryImages) > 0 ? $galleryImages : ($coverUrl !== '' ? array($coverUrl) : array());
$bgImage = count($backgroundImages) > 0 ? $backgroundImages[0] : '';
$useCoverBackground = $bgImage !== '';
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo announcement_h($pageTitle); ?></title>
  <link rel="icon" type="image/png" href="./img/3si.png">
  <link rel="apple-touch-icon" href="./img/pwa/icon-192.png">
  <link rel="manifest" href="./site.webmanifest">
  <meta name="theme-color" content="#ffffff">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+Thai:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
      --news-wine: #8d2b3a;
      --news-gold: #d9c374;
      --news-gold-soft: #f8f1d7;
      --radius: 12px;
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
        radial-gradient(circle at top left, rgba(79, 70, 229, 0.10), transparent 30rem),
        linear-gradient(180deg, #e6eef9 0%, #f5f8fc 52%, #eaf1fb 100%);
      color: var(--ink);
      font-family: "Inter", "Noto Sans Thai", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      -webkit-font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
    }

    button,
    input {
      font: inherit;
    }

    button,
    a {
      -webkit-tap-highlight-color: transparent;
    }

    button:focus-visible,
    a:focus-visible {
      outline: 3px solid rgba(79, 70, 229, 0.26);
      outline-offset: 3px;
    }

    .ambient-bg {
      position: fixed;
      inset: 0;
      z-index: -2;
      pointer-events: none;
      overflow: hidden;
      background: #e9f0fb;
    }

    .ambient-bg img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
      object-position: center;
      opacity: 0.34;
      filter: blur(4px) saturate(0.9) contrast(0.96);
      transform: scale(1.018);
      transition: opacity 700ms ease;
    }

    .ambient-bg::after {
      content: "";
      position: absolute;
      inset: 0;
      background:
        linear-gradient(180deg, rgba(232, 240, 251, 0.34), rgba(247, 249, 252, 0.74)),
        linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
    }

    .has-cover-bg .ambient-bg img {
      opacity: 0.36;
    }

    .page-shell {
      width: 100%;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      margin: 0;
      padding: 0 var(--page-x) 0;
    }

    .portal-topbar {
      display: grid;
      grid-template-columns: minmax(220px, 300px) minmax(0, 1fr) auto;
      align-items: center;
      gap: 20px;
      min-height: 74px;
      margin: 0 calc(var(--page-x) * -1);
      padding: 10px var(--page-x);
      border: 0;
      border-bottom: 1px solid rgba(228, 233, 241, 0.96);
      border-radius: 0;
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 8px 18px rgba(16, 23, 42, 0.06);
      position: relative;
      z-index: 5;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 13px;
      min-width: 0;
      color: inherit;
      text-decoration: none;
    }

    .brand-logo {
      width: 54px;
      height: 54px;
      flex: 0 0 auto;
      object-fit: contain;
    }

    .brand-title {
      display: grid;
      gap: 1px;
      margin: 0;
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
      text-align: center;
      text-transform: uppercase;
    }

    .topbar-center {
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 0;
    }

    .topbar-spacer {
      min-width: 0;
    }

    .topbar-link {
      display: inline-flex;
      align-items: center;
      min-height: 42px;
      padding: 0 14px;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: var(--surface);
      color: var(--muted);
      font-size: 0.88rem;
      font-weight: 700;
      text-decoration: none;
    }

    .topbar-link:hover {
      border-color: rgba(79, 70, 229, 0.28);
      background: var(--accent-soft);
      color: var(--accent);
    }

    .top-actions {
      grid-column: 3;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
    }

    .language-menu {
      position: relative;
    }

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
      font-weight: 700;
      font-size: 0.9rem;
      cursor: pointer;
      transition: background 180ms ease, border-color 180ms ease, color 180ms ease;
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
      z-index: 20;
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

    .content-shell {
      width: min(100%, 920px);
      margin: 24px auto clamp(24px, 3vw, 40px);
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 16px;
      color: var(--muted);
      font-size: 0.9rem;
      font-weight: 700;
      text-decoration: none;
    }

    .back-link:hover {
      color: var(--news-wine);
    }

    .news-article {
      overflow: hidden;
      border: 1px solid rgba(228, 233, 241, 0.92);
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 8px 18px rgba(16, 23, 42, 0.06);
    }

    .news-cover {
      width: 100%;
      aspect-ratio: 16 / 8;
      overflow: hidden;
      background: #f1f5f9;
    }

    .news-cover img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .news-body-wrap {
      padding: clamp(22px, 4vw, 40px);
    }

    .news-meta {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 14px;
    }

    .news-date {
      color: var(--muted);
      font-size: 0.86rem;
      font-weight: 700;
    }

    .news-new-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      min-height: 22px;
      padding: 0 9px;
      border-radius: 999px;
      background: var(--news-gold-soft);
      color: var(--news-wine);
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
    }

    .news-title {
      margin: 0 0 18px;
      color: var(--ink);
      font-size: clamp(1.5rem, 3vw, 2.1rem);
      font-weight: 900;
      line-height: 1.28;
      text-wrap: balance;
    }

    .news-text {
      margin: 0;
      color: #334155;
      font-size: 1rem;
      line-height: 1.85;
      tab-size: 2;
      white-space: pre-wrap;
      overflow-wrap: anywhere;
    }

    .news-files {
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px solid var(--line);
    }

    .news-files h2 {
      margin: 0 0 14px;
      color: var(--ink);
      font-size: 0.9rem;
      font-weight: 800;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    .news-file-list {
      display: grid;
      gap: 10px;
    }

    .news-file {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
      padding: 12px 14px;
      border: 1px solid var(--line);
      border-radius: 12px;
      background: #f8fafc;
      color: var(--ink);
      text-decoration: none;
      transition: border-color 160ms ease, background 160ms ease;
    }

    .news-file:hover {
      border-color: rgba(141, 43, 58, 0.28);
      background: #ffffff;
    }

    .file-icon {
      display: inline-grid;
      place-items: center;
      width: 40px;
      height: 40px;
      flex: 0 0 auto;
      border-radius: 10px;
      background: #ffffff;
      border: 1px solid var(--line);
    }

    .file-icon svg,
    .file-icon img {
      width: 26px;
      height: 26px;
      object-fit: contain;
    }

    .file-info {
      display: grid;
      min-width: 0;
      gap: 2px;
    }

    .file-info strong,
    .file-info span {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .file-info strong {
      font-size: 0.9rem;
      font-weight: 800;
    }

    .file-info span {
      color: var(--faint);
      font-size: 0.76rem;
      font-weight: 600;
    }

    .empty-state {
      padding: 40px 24px;
      border: 1px dashed rgba(148, 163, 184, 0.4);
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.95);
      color: var(--muted);
      text-align: center;
      line-height: 1.6;
    }

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

    .brand-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      min-height: 96px;
      margin: auto calc(var(--page-x) * -1) 0;
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

    .footer-brand {
      display: inline-flex;
      align-items: center;
      justify-content: flex-end;
      gap: 16px;
      min-width: 0;
    }

    .footer-brand-name {
      color: var(--ink);
      font-size: 0.9rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      white-space: nowrap;
    }

    @media (max-width: 760px) {
      .portal-topbar {
        grid-template-columns: 1fr auto;
      }

      .top-actions {
        grid-column: 2;
      }

      .topbar-center {
        display: none;
      }

      .brand-logo {
        width: 46px;
        height: 46px;
      }

      .brand-title-main {
        font-size: 1.32rem;
      }

      .brand-title-sub {
        font-size: 0.68rem;
      }

      .news-cover {
        aspect-ratio: 16 / 10;
      }
    }

    @media (max-width: 480px) {
      .page-shell {
        --page-x: 14px;
      }

      .portal-topbar {
        border-radius: 0;
      }

      .language-trigger {
        min-height: 44px;
        padding: 0 10px;
      }

      .brand-footer {
        align-items: flex-start;
        flex-direction: column;
      }

      .footer-brand {
        width: 100%;
        justify-content: space-between;
      }

      .footer-brand-name {
        white-space: normal;
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
      animation: aiChatPop 200ms ease;
    }

    @keyframes aiChatPop {
      from { opacity: 0; transform: translateY(8px) scale(0.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
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
      font: inherit;
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
  </style>
</head>

<body class="<?php echo $useCoverBackground ? 'has-cover-bg' : ''; ?>">
  <div class="ambient-bg" aria-hidden="true">
    <?php if ($bgImage !== '') { ?>
      <img id="ambientPhoto" src="<?php echo announcement_h($bgImage); ?>" alt="">
    <?php } ?>
  </div>

  <main class="page-shell">
    <header class="portal-topbar">
      <a class="brand" href="index.php" aria-label="Supavut Group">
        <img class="brand-logo" src="./img/3si.png" alt="Supavut Group logo">
        <h1 class="brand-title">
          <span class="brand-title-main">SUPAVUT</span>
          <span class="brand-title-sub">GROUP</span>
        </h1>
      </a>

      <div class="topbar-spacer" aria-hidden="true"></div>

      <div class="top-actions">
        <div class="language-menu" aria-label="Language switcher">
          <button id="languageTrigger" class="language-trigger" type="button" aria-label="Change language" aria-expanded="false" aria-controls="languagePanel">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.2-2.4 3.4-5.3 3.4-9S14.2 5.4 12 3m0 18c-2.2-2.4-3.4-5.3-3.4-9S9.8 5.4 12 3M3.6 9h16.8M3.6 15h16.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span class="lang-code" id="languageCode">TH</span>
            <svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
          </button>
          <div id="languagePanel" class="language-panel">
            <button class="lang-btn is-active" type="button" data-lang="th"><span>ไทย</span><span class="lang-code">TH</span></button>
            <button class="lang-btn" type="button" data-lang="en"><span>English</span><span class="lang-code">EN</span></button>
            <button class="lang-btn" type="button" data-lang="my"><span>မြန်မာ</span><span class="lang-code">MY</span></button>
          </div>
        </div>
      </div>
    </header>

    <section class="content-shell">
      <a class="back-link" href="index.php">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
        <span data-i18n="backLinkText">กลับหน้าแรก</span>
      </a>

      <?php if (!$match) { ?>
        <div class="empty-state" data-i18n="notFound">ไม่พบประกาศนี้ หรือประกาศถูกลบไปแล้ว</div>
      <?php } else { ?>
        <article class="news-article" data-news-link
          data-title-th="<?php echo announcement_h($match['title']); ?>"
          data-title-en="<?php echo announcement_h(announcement_field($match, 'title_en')); ?>"
          data-title-my="<?php echo announcement_h(announcement_field($match, 'title_my')); ?>"
          data-body-th="<?php echo announcement_h($match['body']); ?>"
          data-body-en="<?php echo announcement_h(announcement_field($match, 'body_en')); ?>"
          data-body-my="<?php echo announcement_h(announcement_field($match, 'body_my')); ?>">
          <?php if ($coverUrl !== '') { ?>
            <div class="news-cover">
              <img src="<?php echo announcement_h($coverUrl); ?>" alt="<?php echo announcement_h($match['title']); ?>">
            </div>
          <?php } ?>
          <div class="news-body-wrap">
            <div class="news-meta">
              <span class="news-date"><?php echo announcement_h(announcement_format_date($match['created_at'])); ?></span>
              <?php if ($isNew) { ?>
                <span class="news-new-badge" data-i18n="newBadge">New</span>
              <?php } ?>
            </div>
            <h2 class="news-title"><?php echo announcement_h($match['title']); ?></h2>
            <p class="news-text"><?php echo announcement_h($match['body']); ?></p>

            <?php if (count($attachments) > 0) { ?>
              <div class="news-files">
                <h2 data-i18n="attachmentsHeading">แนบเอกสาร</h2>
                <div class="news-file-list">
                  <?php foreach ($attachments as $attachment) { ?>
                    <?php
                      $fileKind = announcement_file_kind($attachment);
                      $fileIconUrl = announcement_file_icon_url($fileKind);
                      $fileSize = isset($attachment['size']) ? announcement_format_file_size($attachment['size']) : '';
                    ?>
                    <a class="news-file" href="<?php echo announcement_h(announcement_attachment_url($match['id'], $attachment)); ?>" target="_blank" rel="noopener noreferrer">
                      <span class="file-icon">
                        <?php if ($fileIconUrl !== '') { ?>
                          <img src="<?php echo announcement_h($fileIconUrl); ?>" alt="">
                        <?php } else { ?>
                          <?php echo announcement_file_icon_svg($fileKind); ?>
                        <?php } ?>
                      </span>
                      <span class="file-info">
                        <strong><?php echo announcement_h($attachment['original_name']); ?></strong>
                        <span><?php echo announcement_h($fileSize); ?></span>
                      </span>
                    </a>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          </div>
        </article>
      <?php } ?>
    </section>

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
      <div class="footer-brand">
        <span class="footer-brand-name">SUPAVUT INDUSTRY 2029</span>
      </div>
    </footer>
  </main>

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

  <script>
    'use strict';
    const galleryImages = <?php echo json_encode($backgroundImages); ?>;
    const ambientPhoto = document.getElementById('ambientPhoto');
    const languageTrigger = document.getElementById('languageTrigger');
    const languagePanel = document.getElementById('languagePanel');
    const languageCode = document.getElementById('languageCode');
    const languageStorageKey = 'simenu_language';
    const supportedLanguages = ['th', 'en', 'my'];
    let currentLang = getStoredLanguage();

    const translations = {
      th: {
        homeLink: 'หน้าแรก',
        backLinkText: 'กลับหน้าแรก',
        notFound: 'ไม่พบประกาศนี้ หรือประกาศถูกลบไปแล้ว',
        newBadge: 'ใหม่',
        attachmentsHeading: 'แนบเอกสาร'
      },
      en: {
        homeLink: 'Home',
        backLinkText: 'Back to home',
        notFound: 'This announcement was not found or has been removed.',
        newBadge: 'New',
        attachmentsHeading: 'Attachments'
      },
      my: {
        homeLink: 'ပင်မစာမျက်နှာ',
        backLinkText: 'ပင်မစာမျက်နှာသို့',
        notFound: 'ဤကြေညာချက်ကို ရှာမတွေ့ပါ သို့မဟုတ် ဖျက်ပြီးပါပြီ။',
        newBadge: 'အသစ်',
        attachmentsHeading: 'ပူးတွဲစာရွက်စာတမ်း'
      }
    };

    function t(key) {
      return (translations[currentLang] && translations[currentLang][key]) || translations.th[key] || key;
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

    function setLanguageMenu(open) {
      if (!languagePanel || !languageTrigger) {
        return;
      }
      languagePanel.classList.toggle('is-open', open);
      languageTrigger.setAttribute('aria-expanded', String(open));
    }

    function applyNewsTranslation() {
      document.querySelectorAll('[data-news-link]').forEach((card) => {
        const titleEl = card.querySelector('.news-title');
        const bodyEl = card.querySelector('.news-text');
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

    function setLanguage(lang, shouldSave = true) {
      lang = normalizeLanguage(lang);
      currentLang = lang;
      document.documentElement.lang = lang === 'my' ? 'my' : lang;
      if (languageCode) {
        languageCode.textContent = lang.toUpperCase();
      }
      document.querySelectorAll('[data-i18n]').forEach((node) => {
        node.textContent = t(node.dataset.i18n);
      });
      document.querySelectorAll('.lang-btn').forEach((button) => {
        button.classList.toggle('is-active', button.dataset.lang === lang);
      });
      applyNewsTranslation();

      if (shouldSave) {
        saveStoredLanguage(lang);
      }
    }

    if (languageTrigger) {
      languageTrigger.addEventListener('click', () => {
        setLanguageMenu(!languagePanel.classList.contains('is-open'));
      });
    }

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
      }
    });

    window.addEventListener('storage', (event) => {
      if (event.key === languageStorageKey) {
        setLanguage(event.newValue || 'th', false);
      }
    });

    setLanguage(currentLang, false);

    if (ambientPhoto && galleryImages.length > 1) {
      let heroIndex = 0;
      setInterval(() => {
        heroIndex = (heroIndex + 1) % galleryImages.length;
        ambientPhoto.style.opacity = '0';
        setTimeout(() => {
          ambientPhoto.src = galleryImages[heroIndex];
          ambientPhoto.style.opacity = '0.24';
        }, 450);
      }, 4500);
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
