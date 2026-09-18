<?php
require_once dirname(__FILE__) . '/announcement_helpers.php';
announcement_start_session();

$message = '';
$errors = array();
$postedAction = '';

if (isset($_SESSION['announcement_flash'])) {
  $message = $_SESSION['announcement_flash'];
  unset($_SESSION['announcement_flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? $_POST['action'] : '';
  $postedAction = $action;

  if ($action === 'login') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (announcement_login($username, $password)) {
      header('Location: announcements_admin.php');
      exit;
    }

    $errors[] = 'Username หรือ Password ไม่ถูกต้อง';
  } elseif ($action === 'logout') {
    announcement_logout();
    header('Location: announcements_admin.php');
    exit;
  } elseif (!announcement_check_csrf(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
    $errors[] = 'Session หมดอายุ กรุณาลองใหม่';
  } elseif (announcement_is_admin() && $action === 'create') {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $body = isset($_POST['body']) ? $_POST['body'] : '';
    $files = isset($_FILES['attachments']) ? $_FILES['attachments'] : null;
    $coverFile = isset($_FILES['cover_image']) ? $_FILES['cover_image'] : null;
    $pinned = isset($_POST['pinned']) && $_POST['pinned'] === '1';
    $translations = array(
      'en' => array(
        'title' => isset($_POST['title_en']) ? $_POST['title_en'] : '',
        'body' => isset($_POST['body_en']) ? $_POST['body_en'] : ''
      ),
      'my' => array(
        'title' => isset($_POST['title_my']) ? $_POST['title_my'] : '',
        'body' => isset($_POST['body_my']) ? $_POST['body_my'] : ''
      )
    );

    if (announcement_create($title, $body, $files, $coverFile, $translations, $errors, $pinned)) {
      $_SESSION['announcement_flash'] = 'บันทึกประกาศเรียบร้อยแล้ว';
      header('Location: announcements_admin.php');
      exit;
    }
  } elseif (announcement_is_admin() && $action === 'update') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $body = isset($_POST['body']) ? $_POST['body'] : '';
    $files = isset($_FILES['attachments']) ? $_FILES['attachments'] : null;
    $coverFile = isset($_FILES['cover_image']) ? $_FILES['cover_image'] : null;
    $removeAttachmentIds = isset($_POST['remove_attachments']) ? $_POST['remove_attachments'] : array();
    $removeCover = isset($_POST['remove_cover']) && $_POST['remove_cover'] === '1';
    $pinned = isset($_POST['pinned']) && $_POST['pinned'] === '1';
    $translations = array(
      'en' => array(
        'title' => isset($_POST['title_en']) ? $_POST['title_en'] : '',
        'body' => isset($_POST['body_en']) ? $_POST['body_en'] : ''
      ),
      'my' => array(
        'title' => isset($_POST['title_my']) ? $_POST['title_my'] : '',
        'body' => isset($_POST['body_my']) ? $_POST['body_my'] : ''
      )
    );

    if (announcement_update($id, $title, $body, $files, $coverFile, $translations, $removeAttachmentIds, $removeCover, $errors, $pinned)) {
      $_SESSION['announcement_flash'] = 'อัปเดตประกาศเรียบร้อยแล้ว';
      header('Location: announcements_admin.php');
      exit;
    }
  } elseif (announcement_is_admin() && $action === 'pin') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $pinned = isset($_POST['pinned']) && $_POST['pinned'] === '1';
    if (announcement_set_pinned($id, $pinned, $errors)) {
      $_SESSION['announcement_flash'] = $pinned ? 'ปักหมุดประกาศแล้ว' : 'ถอดหมุดประกาศแล้ว';
      header('Location: announcements_admin.php');
      exit;
    }
  } elseif (announcement_is_admin() && $action === 'delete') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if (announcement_delete($id)) {
      $_SESSION['announcement_flash'] = 'ลบประกาศเรียบร้อยแล้ว';
      header('Location: announcements_admin.php');
      exit;
    } else {
      $errors[] = 'ลบประกาศไม่สำเร็จ';
    }
  }
}

$isAdmin = announcement_is_admin();
$announcements = announcement_read_all();
$csrfToken = announcement_csrf_token();
$galleryImages = announcement_gallery_images();
$bgImage = count($galleryImages) > 0 ? $galleryImages[0] : '';
$showCreateForm = $isAdmin && $postedAction === 'create' && count($errors) > 0;
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcement Admin | SUPAVUT GROUP</title>
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
      --success: #008f55;
      --success-soft: #e8fff4;
      --danger: #dc2626;
      --danger-soft: #fff1f2;
      --radius: 12px;
      --page-x: clamp(14px, 1.6vw, 28px);
      --page-y: clamp(14px, 1.6vw, 28px);
      --paper: #fffdf4;
      --paper-line: rgba(141, 43, 58, 0.09);
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
        radial-gradient(circle at top left, rgba(79, 70, 229, 0.11), transparent 32rem),
        linear-gradient(180deg, #e8f0fb 0%, #f5f8fc 46%, #edf4fb 100%);
      color: var(--ink);
      font-family: "Inter", "Noto Sans Thai", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      -webkit-font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
    }

    h1,
    h2,
    p {
      margin-top: 0;
    }

    button,
    input,
    textarea {
      font: inherit;
    }

    button,
    a {
      -webkit-tap-highlight-color: transparent;
    }

    button:focus-visible,
    a:focus-visible,
    input:focus-visible,
    textarea:focus-visible {
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
      opacity: 0.3;
      filter: saturate(0.92) contrast(0.96);
      transition: opacity 700ms ease;
    }

    .ambient-bg::after {
      content: "";
      position: absolute;
      inset: 0;
      background:
        linear-gradient(180deg, rgba(232, 240, 251, 0.34), rgba(247, 249, 252, 0.76)),
        linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
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
      border-bottom: 1px solid rgba(212, 223, 239, 0.94);
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

    .language-trigger,
    .topbar-link,
    .btn,
    .link-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      min-height: 44px;
      padding: 0 14px;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: var(--surface);
      color: var(--ink);
      font-weight: 700;
      font-size: 0.9rem;
      text-decoration: none;
      cursor: pointer;
      transition: background 180ms ease, border-color 180ms ease, color 180ms ease;
    }

    .language-trigger {
      min-height: 50px;
    }

    .language-trigger:hover,
    .language-trigger[aria-expanded="true"],
    .topbar-link:hover,
    .link-btn.ghost:hover,
    .btn.ghost:hover {
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

    .btn.primary {
      border-color: var(--accent);
      background: var(--accent);
      color: #ffffff;
    }

    .btn.primary:hover {
      background: #4338ca;
    }

    .btn.danger {
      min-height: 36px;
      padding: 0 12px;
      border-color: rgba(220, 38, 38, 0.22);
      background: var(--danger-soft);
      color: var(--danger);
      font-size: 0.82rem;
    }

    .btn.danger:hover {
      background: #ffe4e6;
    }

    .logout-icon-btn {
      width: 44px;
      min-width: 44px;
      min-height: 44px;
      padding: 0;
      border: 0;
      border-radius: 8px;
      background: transparent;
      color: var(--danger);
      box-shadow: none;
    }

    .logout-icon-btn:hover {
      background: transparent;
      color: #b91c1c;
      transform: translateY(-1px);
    }

    .btn.secondary {
      min-height: 36px;
      padding: 0 12px;
      border-color: rgba(79, 70, 229, 0.18);
      background: rgba(255, 255, 255, 0.82);
      color: var(--accent);
      font-size: 0.82rem;
    }

    .btn.secondary:hover {
      background: var(--accent-soft);
      border-color: rgba(79, 70, 229, 0.32);
    }

    .btn.compact {
      min-height: 36px;
      padding: 0 12px;
      font-size: 0.82rem;
    }

    .btn.full {
      width: 100%;
    }

    .message-stack {
      width: min(100%, 920px);
      margin: 18px auto 0;
    }

    .notice,
    .error {
      padding: 12px 14px;
      border-radius: 12px;
      margin-bottom: 12px;
      font-weight: 700;
      font-size: 0.9rem;
    }

    .notice {
      border: 1px solid rgba(0, 143, 85, 0.18);
      background: var(--success-soft);
      color: var(--success);
    }

    .error {
      border: 1px solid rgba(220, 38, 38, 0.16);
      background: var(--danger-soft);
      color: var(--danger);
    }

    .login-wrap {
      width: min(100%, 1180px);
      min-height: calc(100vh - 158px);
      margin: 0 auto;
      display: grid;
      place-items: center;
      padding: 28px 0;
    }

    .login-card {
      position: relative;
      width: min(100%, 430px);
      padding: 28px;
      border: 1px solid rgba(228, 233, 241, 0.92);
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.97);
      box-shadow: 0 8px 18px rgba(16, 23, 42, 0.06);
      text-align: center;
    }

    .login-back-link {
      display: flex;
      align-items: center;
      gap: 6px;
      width: max-content;
      margin: 0 0 18px;
      color: var(--muted);
      font-size: 0.86rem;
      font-weight: 800;
      text-decoration: none;
    }

    .login-back-link:hover {
      color: var(--accent);
    }

    .login-card h2 {
      margin: 0 0 8px;
      font-size: 1.26rem;
      font-weight: 900;
    }

    .login-card .subtitle {
      margin: 0 auto 22px;
      max-width: 32ch;
      color: var(--muted);
      font-size: 0.9rem;
      line-height: 1.6;
    }

    .login-form {
      display: grid;
      gap: 14px;
      text-align: left;
    }

    .admin-workspace {
      width: min(100%, 1480px);
      margin: 12px auto 0;
    }

    .admin-toolbar {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 16px;
      margin-bottom: 14px;
      padding: 0;
    }

    .admin-heading {
      min-width: 0;
    }

    .admin-heading h2 {
      margin: 0 0 4px;
      font-size: 1.12rem;
      font-weight: 900;
      text-wrap: balance;
    }

    .admin-heading p {
      margin: 0;
      color: var(--muted);
      font-size: 0.88rem;
      line-height: 1.5;
    }

    .add-toggle {
      flex: 0 0 auto;
      min-width: 150px;
    }

    label {
      display: block;
      margin-bottom: 7px;
      color: var(--ink);
      font-weight: 800;
      font-size: 0.85rem;
    }

    input,
    textarea {
      width: 100%;
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 11px 12px;
      background: #ffffff;
      color: var(--ink);
      transition: border-color 160ms ease, box-shadow 160ms ease;
    }

    input:focus,
    textarea:focus {
      outline: 0;
      border-color: rgba(79, 70, 229, 0.5);
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    textarea {
      min-height: 130px;
      resize: vertical;
    }

    .field {
      margin-bottom: 16px;
    }

    .field-help {
      margin: 6px 0 0;
      color: var(--faint);
      font-size: 0.76rem;
      line-height: 1.5;
    }

    input[type="checkbox"] {
      width: auto;
      min-width: 18px;
      min-height: 18px;
      padding: 0;
      accent-color: var(--accent);
    }

    .field-row,
    .form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
    }

    .post-card .form-grid {
      grid-template-columns: 1fr;
      gap: 12px;
    }

    .form-tabs {
      display: inline-flex;
      gap: 4px;
      margin: 0 0 16px;
      padding: 4px;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: var(--surface-soft);
    }

    .form-tab {
      min-width: 54px;
      min-height: 34px;
      border: 0;
      border-radius: 999px;
      background: transparent;
      color: var(--muted);
      font-size: 0.78rem;
      font-weight: 900;
      letter-spacing: 0.04em;
      cursor: pointer;
    }

    .form-tab:hover,
    .form-tab.is-active {
      background: var(--surface);
      color: var(--accent);
      box-shadow: 0 4px 12px rgba(16, 23, 42, 0.08);
    }

    .form-panel {
      display: none;
    }

    .form-panel.is-active {
      display: block;
    }

    .post-board {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 18px;
      align-items: start;
    }

    .post-card {
      position: relative;
      width: 100%;
      display: block;
      min-height: 238px;
      padding: 20px 18px 18px;
      border: 1px solid rgba(210, 195, 154, 0.74);
      border-radius: 4px;
      background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.68), rgba(255, 255, 255, 0.08)),
        repeating-linear-gradient(to bottom, transparent 0, transparent 30px, var(--paper-line) 31px),
        var(--paper);
      box-shadow:
        0 1px 0 rgba(255, 255, 255, 0.84) inset,
        0 14px 28px rgba(71, 85, 105, 0.14);
    }

    .post-card::before {
      content: "";
      position: absolute;
      top: 12px;
      left: 50%;
      width: 44px;
      height: 10px;
      border-radius: 999px;
      background: rgba(148, 163, 184, 0.22);
      transform: translateX(-50%);
      box-shadow: 0 1px 4px rgba(71, 85, 105, 0.12);
    }

    .post-card::after {
      content: "";
      position: absolute;
      right: -1px;
      bottom: -1px;
      width: 34px;
      height: 34px;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0) 50%, rgba(226, 212, 170, 0.68) 51%);
      border-bottom-right-radius: 4px;
      pointer-events: none;
    }

    .post-card.tone-1 {
      --paper: #f6fbff;
      --paper-line: rgba(37, 99, 235, 0.08);
    }

    .post-card.tone-2 {
      --paper: #f7fff5;
      --paper-line: rgba(22, 163, 74, 0.08);
    }

    .post-card.tone-3 {
      --paper: #fff7f7;
      --paper-line: rgba(220, 38, 38, 0.08);
    }

    .post-card.is-create {
      display: none;
      grid-column: auto / span 2;
      min-height: 0;
      --paper: #ffffff;
      --paper-line: rgba(79, 70, 229, 0.07);
    }

    .post-card.is-create.is-open {
      display: block;
    }

    .post-card.is-new {
      border-color: rgba(239, 68, 68, 0.3);
    }

    .post-card.is-pinned {
      border-color: rgba(202, 138, 4, 0.45);
    }

    .post-card.is-hidden-page {
      display: none;
    }

    .pin-form {
      position: absolute;
      top: 6px;
      right: 7px;
      z-index: 3;
      margin: 0;
    }

    .pin-btn {
      width: 46px;
      height: 46px;
      display: inline-grid;
      place-items: center;
      border: 0;
      background: transparent;
      color: var(--faint);
      cursor: pointer;
      transition: transform 160ms ease, color 160ms ease;
    }

    .pin-btn svg {
      width: 28px;
      height: 28px;
    }

    .pin-btn:hover {
      transform: translateY(-1px);
      color: #a16207;
    }

    .pin-btn.is-pinned {
      color: #a16207;
      transform: rotate(-12deg);
    }

    .pin-btn.is-pinned:hover {
      transform: rotate(-12deg) translateY(-1px);
    }

    .post-body {
      min-width: 0;
    }

    .post-meta {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 6px;
    }

    .post-date {
      color: var(--faint);
      font-size: 0.76rem;
      font-weight: 700;
    }

    .new-badge {
      display: inline-flex;
      align-items: center;
      min-height: 20px;
      padding: 0 8px;
      border-radius: 999px;
      background: #fee2e2;
      color: #b91c1c;
      font-size: 0.66rem;
      font-weight: 800;
      text-transform: uppercase;
    }

    .post-title {
      margin: 0 0 4px;
      font-size: 0.98rem;
      font-weight: 900;
      line-height: 1.35;
      text-wrap: pretty;
    }

    .post-snippet {
      display: -webkit-box;
      margin: 0;
      overflow: hidden;
      color: var(--muted);
      font-size: 0.84rem;
      line-height: 1.5;
      text-overflow: ellipsis;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
    }

    .post-tags {
      display: flex;
      gap: 6px;
      margin-top: 8px;
      flex-wrap: wrap;
    }

    .post-tag {
      display: inline-flex;
      align-items: center;
      padding: 3px 8px;
      border-radius: 999px;
      background: var(--surface-soft);
      color: var(--faint);
      font-size: 0.7rem;
      font-weight: 700;
    }

    .post-tag.pinned-tag {
      background: #fffbeb;
      color: #92400e;
    }

    .post-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 14px;
      position: relative;
      z-index: 1;
    }

    .post-actions form {
      margin: 0;
    }

    .post-edit {
      display: none;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px dashed rgba(132, 144, 165, 0.46);
    }

    .post-edit.is-open {
      display: block;
    }

    .post-edit-title {
      margin: 0 0 12px;
      font-size: 0.9rem;
      font-weight: 900;
    }

    .asset-list {
      display: grid;
      gap: 8px;
    }

    .asset-row {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
      padding: 9px 10px;
      border: 1px solid var(--line);
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.72);
      cursor: pointer;
      margin-bottom: 0;
    }

    .asset-row span {
      min-width: 0;
      overflow: hidden;
      color: var(--muted);
      font-size: 0.8rem;
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .pin-field {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0 0 14px;
      padding: 10px 12px;
      border: 1px solid rgba(202, 138, 4, 0.22);
      border-radius: 10px;
      background: rgba(255, 251, 235, 0.72);
      color: #78350f;
      cursor: pointer;
    }

    .pin-field input {
      width: 17px;
      height: 17px;
      accent-color: #ca8a04;
    }

    .pin-field span {
      font-size: 0.84rem;
      font-weight: 800;
    }

    .asset-icon {
      width: 30px;
      height: 30px;
      flex: 0 0 auto;
      object-fit: contain;
      border-radius: 8px;
      background: #ffffff;
    }

    .asset-fallback {
      width: 30px;
      height: 30px;
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      background: var(--accent-soft);
      color: var(--accent);
      font-size: 0.62rem;
      font-weight: 900;
    }

    .cover-preview {
      width: 100%;
      max-height: 130px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid var(--line);
      display: block;
      margin-bottom: 8px;
    }

    .empty {
      padding: 24px;
      border: 1px dashed rgba(148, 163, 184, 0.34);
      border-radius: 14px;
      background: var(--surface-soft);
      color: var(--muted);
      text-align: center;
    }

    .post-pagination {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      justify-content: center;
      gap: 10px;
      width: fit-content;
      max-width: 100%;
      margin: clamp(22px, 2.4vw, 34px) auto clamp(44px, 5vw, 80px);
      padding: 0;
    }

    .post-pagination:empty {
      display: none;
      margin: 0;
      padding: 0;
      border: 0;
      box-shadow: none;
    }

    .post-page-btn {
      min-width: 40px;
      min-height: 40px;
      border: 1px solid rgba(79, 70, 229, 0.2);
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.86);
      color: var(--muted);
      font-weight: 900;
      cursor: pointer;
      transition: background 160ms ease, border-color 160ms ease, color 160ms ease, transform 160ms ease;
    }

    .post-page-btn:hover,
    .post-page-btn.is-active {
      border-color: var(--accent);
      background: var(--accent);
      color: #ffffff;
    }

    .post-page-btn:hover {
      transform: translateY(-1px);
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

    @media (max-width: 920px) {
      .portal-topbar {
        grid-template-columns: minmax(0, 1fr) auto;
      }

      .top-actions {
        grid-column: 2;
      }

      .topbar-center {
        display: none;
      }

      .admin-toolbar {
        align-items: flex-start;
        flex-direction: column;
      }

      .add-toggle {
        width: 100%;
      }

      .post-board {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 640px) {
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

      .field-row,
      .form-grid {
        grid-template-columns: 1fr;
      }

      .post-board {
        grid-template-columns: 1fr;
      }

      .post-pagination {
        width: 100%;
        margin-bottom: 52px;
      }

      .post-page-btn {
        min-width: 42px;
        min-height: 42px;
      }

      .post-card.is-create {
        grid-column: span 1;
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

<body>
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
        <?php if ($isAdmin) { ?>
          <form method="post" style="margin:0;">
            <input type="hidden" name="action" value="logout">
            <button class="btn logout-icon-btn" type="submit" aria-label="ออกจากระบบ" title="ออกจากระบบ">
              <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M10 6V5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6a2 2 0 0 1-2-2v-1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                <path d="M15 12H4m0 0 3.5-3.5M4 12l3.5 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </form>
        <?php } ?>
      </div>
    </header>

    <div class="message-stack">
      <?php if ($message !== '') { ?>
        <div class="notice"><?php echo announcement_h($message); ?></div>
      <?php } ?>

      <?php foreach ($errors as $error) { ?>
        <div class="error"><?php echo announcement_h($error); ?></div>
      <?php } ?>
    </div>

    <?php if (!$isAdmin) { ?>
      <section class="login-wrap">
        <div class="login-card">
          <a class="login-back-link" href="index.php">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
            <span data-i18n="backHome">กลับหน้าแรก</span>
          </a>
          <h2 data-i18n="loginTitle">เข้าสู่ระบบผู้ดูแลประกาศ</h2>
          <p class="subtitle" data-i18n="loginSubtitle">ใช้บัญชีผู้ดูแลเพื่อสร้างและดูแลประกาศภายใน</p>
          <form method="post" class="login-form">
            <input type="hidden" name="action" value="login">
            <div class="field">
              <label for="username">Username</label>
              <input id="username" name="username" type="text" autocomplete="username" required autofocus>
            </div>
            <div class="field">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="btn primary" type="submit" data-i18n="loginButton">เข้าสู่ระบบ</button>
          </form>
        </div>
      </section>
    <?php } else { ?>
      <section class="admin-workspace">
        <div class="admin-toolbar">
          <button id="toggleCreatePost" class="btn primary add-toggle" type="button" aria-expanded="<?php echo $showCreateForm ? 'true' : 'false'; ?>" aria-controls="createPostCard" data-i18n="addAnnouncement">เพิ่มประกาศ</button>
        </div>

        <div class="post-board">
          <article id="createPostCard" class="post-card is-create<?php echo $showCreateForm ? ' is-open' : ''; ?>">
            <h3 class="post-title" data-i18n="createTitle">สร้างประกาศใหม่</h3>
            <form method="post" enctype="multipart/form-data" data-announcement-form>
              <input type="hidden" name="action" value="create">
              <input type="hidden" name="csrf" value="<?php echo announcement_h($csrfToken); ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ANNOUNCEMENT_MAX_FILE_SIZE; ?>">

              <div class="form-tabs" role="tablist" aria-label="Announcement language">
                <button class="form-tab is-active" type="button" role="tab" aria-selected="true" data-form-tab="th">TH</button>
                <button class="form-tab" type="button" role="tab" aria-selected="false" data-form-tab="en">EN</button>
                <button class="form-tab" type="button" role="tab" aria-selected="false" data-form-tab="my">MY</button>
              </div>

              <div class="form-panel is-active" data-form-panel="th">
                <div class="field">
                  <label for="title" data-i18n="titleLabel">หัวข้อประกาศ</label>
                  <input id="title" name="title" type="text" maxlength="160" placeholder="เช่น แจ้งวันหยุดประจำปี">
                </div>
                <div class="field">
                  <label for="body" data-i18n="bodyLabel">รายละเอียด</label>
                  <textarea id="body" name="body" placeholder="พิมพ์รายละเอียดประกาศสำหรับพนักงาน..."></textarea>
                </div>
              </div>

              <div class="form-panel" data-form-panel="en">
                <div class="field">
                  <label for="title_en" data-i18n="titleLabelEn">Announcement title</label>
                  <input id="title_en" name="title_en" type="text" maxlength="160" placeholder="Optional English title">
                </div>
                <div class="field">
                  <label for="body_en" data-i18n="bodyLabelEn">Details</label>
                  <textarea id="body_en" name="body_en" placeholder="Optional English body"></textarea>
                </div>
              </div>

              <div class="form-panel" data-form-panel="my">
                <div class="field">
                  <label for="title_my" data-i18n="titleLabelMy">ကြေညာချက်ခေါင်းစဉ်</label>
                  <input id="title_my" name="title_my" type="text" maxlength="160" placeholder="Optional Burmese title">
                </div>
                <div class="field">
                  <label for="body_my" data-i18n="bodyLabelMy">အသေးစိတ်</label>
                  <textarea id="body_my" name="body_my" placeholder="Optional Burmese body"></textarea>
                </div>
              </div>

              <div class="form-grid">
                <div class="field">
                  <label for="cover_image" data-i18n="coverLabel">ภาพหน้าปก</label>
                  <input id="cover_image" name="cover_image" type="file" accept=".jpg,.jpeg,.png,.webp">
                  <p class="field-help" data-i18n="coverHelp">รูปที่จะแสดงเป็นหน้าปกข่าวบนหน้าแรกและหน้าอ่านข่าว</p>
                </div>

                <div class="field">
                  <label for="attachments" data-i18n="attachLabel">แนบเอกสาร</label>
                  <input id="attachments" name="attachments[]" type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp">
                  <p class="field-help"><?php echo announcement_h(announcement_format_file_size(ANNOUNCEMENT_MAX_FILE_SIZE)); ?> / file</p>
                </div>
              </div>

              <label class="pin-field">
                <input type="checkbox" name="pinned" value="1">
                <span data-i18n="pinField">ปักหมุดให้แสดงบนหน้าประกาศข่าว</span>
              </label>

              <button class="btn primary full" type="submit" data-i18n="saveButton">บันทึกประกาศ</button>
            </form>
          </article>

          <?php if (count($announcements) === 0) { ?>
            <div class="empty" data-i18n="empty">ยังไม่มีประกาศจาก HR</div>
          <?php } ?>

          <?php foreach ($announcements as $postIndex => $announcement) { ?>
            <?php
              $announcementId = isset($announcement['id']) ? $announcement['id'] : '';
              $safeId = preg_replace('/[^A-Za-z0-9_]/', '_', $announcementId);
              $attachments = announcement_get_attachments($announcement);
              $isNew = announcement_is_new($announcement);
              $coverUrl = announcement_cover_url($announcement);
              $hasCover = $coverUrl !== '';
              $hasEn = isset($announcement['title_en']) && trim((string) $announcement['title_en']) !== '';
              $hasMy = isset($announcement['title_my']) && trim((string) $announcement['title_my']) !== '';
              $toneClass = 'tone-' . ($postIndex % 4);
              $titleTh = announcement_localized_title($announcement, 'th');
              $titleEn = announcement_localized_title($announcement, 'en');
              $titleMy = announcement_localized_title($announcement, 'my');
              $bodyTh = announcement_localized_body($announcement, 'th');
              $bodyEn = announcement_localized_body($announcement, 'en');
              $bodyMy = announcement_localized_body($announcement, 'my');
              $isPinned = announcement_is_pinned($announcement);
            ?>
            <article class="post-card <?php echo announcement_h($toneClass); ?><?php echo $isNew ? ' is-new' : ''; ?><?php echo $isPinned ? ' is-pinned' : ''; ?>" data-post-card data-news-card data-title-th="<?php echo announcement_h($titleTh); ?>" data-title-en="<?php echo announcement_h($titleEn); ?>" data-title-my="<?php echo announcement_h($titleMy); ?>" data-body-th="<?php echo announcement_h($bodyTh); ?>" data-body-en="<?php echo announcement_h($bodyEn); ?>" data-body-my="<?php echo announcement_h($bodyMy); ?>">
              <form class="pin-form" method="post">
                <input type="hidden" name="action" value="pin">
                <input type="hidden" name="csrf" value="<?php echo announcement_h($csrfToken); ?>">
                <input type="hidden" name="id" value="<?php echo announcement_h($announcementId); ?>">
                <input type="hidden" name="pinned" value="<?php echo $isPinned ? '0' : '1'; ?>">
                <button class="pin-btn<?php echo $isPinned ? ' is-pinned' : ''; ?>" type="submit" aria-label="<?php echo $isPinned ? 'ถอดหมุด' : 'ปักหมุด'; ?>" title="<?php echo $isPinned ? 'ถอดหมุด' : 'ปักหมุด'; ?>">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 4 5 5-4.2 4.2.7 5.3-1.1 1.1-5.3-5.4L5.8 18.5 5 17.7l4.3-4.3L4 8.1 5.1 7l5.3.7L15 4Z" fill="currentColor" fill-opacity="0.16"/><path d="m15 4 5 5-4.2 4.2.7 5.3-1.1 1.1-5.3-5.4L5.8 18.5 5 17.7l4.3-4.3L4 8.1 5.1 7l5.3.7L15 4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                </button>
              </form>
              <div class="post-body">
                <div class="post-meta">
                  <span class="post-date"><?php echo announcement_h(announcement_format_date($announcement['created_at'])); ?></span>
                  <?php if ($isNew) { ?>
                    <span class="new-badge">New</span>
                  <?php } ?>
                </div>
                <h3 class="post-title" data-card-title><?php echo announcement_h($titleTh); ?></h3>
                <p class="post-snippet" data-card-body><?php echo announcement_h($bodyTh); ?></p>
                <div class="post-tags">
                  <?php if ($isPinned) { ?>
                    <span class="post-tag pinned-tag" data-i18n="pinnedTag">ปักหมุด</span>
                  <?php } ?>
                  <?php if ($hasCover) { ?>
                    <span class="post-tag" data-i18n="coverTag">มีภาพหน้าปก</span>
                  <?php } ?>
                  <?php if (count($attachments) > 0) { ?>
                    <span class="post-tag" data-file-tag data-count="<?php echo count($attachments); ?>"><?php echo count($attachments); ?> files</span>
                  <?php } ?>
                  <?php if ($hasEn) { ?>
                    <span class="post-tag">EN</span>
                  <?php } ?>
                  <?php if ($hasMy) { ?>
                    <span class="post-tag">MY</span>
                  <?php } ?>
                </div>
              </div>

              <div class="post-actions">
                <button class="btn secondary compact" type="button" data-edit-toggle aria-expanded="false" aria-controls="edit_<?php echo announcement_h($safeId); ?>" data-i18n="editButton">แก้ไข</button>
                <form method="post" onsubmit="return confirm(t('deleteConfirm'));">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="csrf" value="<?php echo announcement_h($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo announcement_h($announcementId); ?>">
                  <button class="btn danger" type="submit" data-i18n="deleteButton">ลบ</button>
                </form>
              </div>

              <div id="edit_<?php echo announcement_h($safeId); ?>" class="post-edit">
                <h4 class="post-edit-title" data-i18n="editTitle">แก้ไขประกาศ</h4>
                <form method="post" enctype="multipart/form-data" data-announcement-form>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="csrf" value="<?php echo announcement_h($csrfToken); ?>">
                  <input type="hidden" name="id" value="<?php echo announcement_h($announcementId); ?>">
                  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ANNOUNCEMENT_MAX_FILE_SIZE; ?>">

                  <div class="form-tabs" role="tablist" aria-label="Announcement language">
                    <button class="form-tab is-active" type="button" role="tab" aria-selected="true" data-form-tab="th">TH</button>
                    <button class="form-tab" type="button" role="tab" aria-selected="false" data-form-tab="en">EN</button>
                    <button class="form-tab" type="button" role="tab" aria-selected="false" data-form-tab="my">MY</button>
                  </div>

                  <div class="form-panel is-active" data-form-panel="th">
                    <div class="field">
                      <label for="edit_title_<?php echo announcement_h($safeId); ?>" data-i18n="titleLabel">หัวข้อประกาศ</label>
                      <input id="edit_title_<?php echo announcement_h($safeId); ?>" name="title" type="text" maxlength="160" value="<?php echo announcement_h(announcement_field($announcement, 'title')); ?>">
                    </div>
                    <div class="field">
                      <label for="edit_body_<?php echo announcement_h($safeId); ?>" data-i18n="bodyLabel">รายละเอียด</label>
                      <textarea id="edit_body_<?php echo announcement_h($safeId); ?>" name="body"><?php echo announcement_h(announcement_field($announcement, 'body')); ?></textarea>
                    </div>
                  </div>

                  <div class="form-panel" data-form-panel="en">
                    <div class="field">
                      <label for="edit_title_en_<?php echo announcement_h($safeId); ?>" data-i18n="titleLabelEn">Announcement title</label>
                      <input id="edit_title_en_<?php echo announcement_h($safeId); ?>" name="title_en" type="text" maxlength="160" value="<?php echo announcement_h(announcement_field($announcement, 'title_en')); ?>">
                    </div>
                    <div class="field">
                      <label for="edit_body_en_<?php echo announcement_h($safeId); ?>" data-i18n="bodyLabelEn">Details</label>
                      <textarea id="edit_body_en_<?php echo announcement_h($safeId); ?>" name="body_en"><?php echo announcement_h(announcement_field($announcement, 'body_en')); ?></textarea>
                    </div>
                  </div>

                  <div class="form-panel" data-form-panel="my">
                    <div class="field">
                      <label for="edit_title_my_<?php echo announcement_h($safeId); ?>" data-i18n="titleLabelMy">ကြေညာချက်ခေါင်းစဉ်</label>
                      <input id="edit_title_my_<?php echo announcement_h($safeId); ?>" name="title_my" type="text" maxlength="160" value="<?php echo announcement_h(announcement_field($announcement, 'title_my')); ?>">
                    </div>
                    <div class="field">
                      <label for="edit_body_my_<?php echo announcement_h($safeId); ?>" data-i18n="bodyLabelMy">အသေးစိတ်</label>
                      <textarea id="edit_body_my_<?php echo announcement_h($safeId); ?>" name="body_my"><?php echo announcement_h(announcement_field($announcement, 'body_my')); ?></textarea>
                    </div>
                  </div>

                  <?php if ($hasCover) { ?>
                    <div class="field">
                      <label data-i18n="currentCoverLabel">ภาพหน้าปกเดิม</label>
                      <img class="cover-preview" src="<?php echo announcement_h($coverUrl); ?>" alt="">
                      <label class="asset-row">
                        <input type="checkbox" name="remove_cover" value="1">
                        <span data-i18n="removeCoverLabel">ลบภาพหน้าปกเดิม</span>
                      </label>
                    </div>
                  <?php } ?>

                  <?php if (count($attachments) > 0) { ?>
                    <div class="field">
                      <label data-i18n="currentFilesLabel">ไฟล์แนบเดิม</label>
                      <div class="asset-list">
                        <?php foreach ($attachments as $attachment) { ?>
                          <?php
                            $attachmentId = announcement_attachment_identifier($attachment);
                            $fileKind = announcement_file_kind($attachment);
                            $fileIconUrl = announcement_file_icon_url($fileKind);
                            $fileLabel = isset($attachment['original_name']) ? $attachment['original_name'] : $attachmentId;
                          ?>
                          <label class="asset-row">
                            <input type="checkbox" name="remove_attachments[]" value="<?php echo announcement_h($attachmentId); ?>">
                            <?php if ($fileIconUrl !== '') { ?>
                              <img class="asset-icon" src="<?php echo announcement_h($fileIconUrl); ?>" alt="">
                            <?php } else { ?>
                              <span class="asset-fallback"><?php echo announcement_h(announcement_file_short_label($fileKind)); ?></span>
                            <?php } ?>
                            <span><?php echo announcement_h($fileLabel); ?></span>
                          </label>
                        <?php } ?>
                      </div>
                      <p class="field-help" data-i18n="removeFileHelp">ติ๊กไฟล์ที่ต้องการลบ แล้วกดอัปเดตประกาศ</p>
                    </div>
                  <?php } ?>

                  <div class="form-grid">
                    <div class="field">
                      <label for="edit_cover_<?php echo announcement_h($safeId); ?>" data-i18n="newCoverLabel">เปลี่ยนภาพหน้าปก</label>
                      <input id="edit_cover_<?php echo announcement_h($safeId); ?>" name="cover_image" type="file" accept=".jpg,.jpeg,.png,.webp">
                      <p class="field-help" data-i18n="newCoverHelp">เลือกไฟล์ใหม่เมื่ออยากเปลี่ยนภาพหน้าปก</p>
                    </div>

                    <div class="field">
                      <label for="edit_attachments_<?php echo announcement_h($safeId); ?>" data-i18n="addFilesLabel">แนบไฟล์เพิ่ม</label>
                      <input id="edit_attachments_<?php echo announcement_h($safeId); ?>" name="attachments[]" type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp">
                      <p class="field-help"><?php echo announcement_h(announcement_format_file_size(ANNOUNCEMENT_MAX_FILE_SIZE)); ?> / file</p>
                    </div>
                  </div>

                  <label class="pin-field">
                    <input type="checkbox" name="pinned" value="1"<?php echo $isPinned ? ' checked' : ''; ?>>
                    <span data-i18n="pinField">ปักหมุดให้แสดงบนหน้าประกาศข่าว</span>
                  </label>

                  <button class="btn primary full" type="submit" data-i18n="updateButton">อัปเดตประกาศ</button>
                </form>
              </div>
            </article>
          <?php } ?>
        </div>
        <div id="postPagination" class="post-pagination" aria-label="Announcement pages"></div>
      </section>
    <?php } ?>

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
    const galleryImages = <?php echo json_encode($galleryImages); ?>;
    const ambientPhoto = document.getElementById('ambientPhoto');
    const languageTrigger = document.getElementById('languageTrigger');
    const languagePanel = document.getElementById('languagePanel');
    const languageCode = document.getElementById('languageCode');
    const languageStorageKey = 'simenu_language';
    const supportedLanguages = ['th', 'en', 'my'];
    let currentLang = getStoredLanguage();

    const toggleCreatePost = document.getElementById('toggleCreatePost');
    const createPostCard = document.getElementById('createPostCard');
    const postBoard = document.querySelector('.post-board');
    const postPagination = document.getElementById('postPagination');
    let currentPostPage = 1;

    if (postBoard && createPostCard) {
      postBoard.appendChild(createPostCard);
    }

    const translations = {
      th: {
        backHome: 'กลับหน้าแรก',
        loginTitle: 'เข้าสู่ระบบผู้ดูแลประกาศ',
        loginSubtitle: 'ใช้บัญชีผู้ดูแลเพื่อสร้างและดูแลประกาศภายใน',
        loginButton: 'เข้าสู่ระบบ',
        addAnnouncement: 'เพิ่มประกาศ',
        closeCreate: 'ปิดฟอร์ม',
        createTitle: 'สร้างประกาศใหม่',
        editTitle: 'แก้ไขประกาศ',
        titleLabel: 'หัวข้อประกาศ',
        bodyLabel: 'รายละเอียด',
        titleLabelEn: 'หัวข้อประกาศภาษาอังกฤษ',
        bodyLabelEn: 'รายละเอียดภาษาอังกฤษ',
        titleLabelMy: 'หัวข้อประกาศภาษาพม่า',
        bodyLabelMy: 'รายละเอียดภาษาพม่า',
        coverLabel: 'ภาพหน้าปก',
        coverHelp: 'รูปที่จะแสดงเป็นหน้าปกข่าวบนหน้าแรกและหน้าอ่านข่าว',
        attachLabel: 'แนบเอกสาร',
        pinField: 'ปักหมุดให้แสดงบนหน้าประกาศข่าว',
        pinnedTag: 'ปักหมุด',
        saveButton: 'บันทึกประกาศ',
        empty: 'ยังไม่มีประกาศจาก HR',
        coverTag: 'มีภาพหน้าปก',
        editButton: 'แก้ไข',
        hideEdit: 'ปิดแก้ไข',
        deleteButton: 'ลบ',
        currentCoverLabel: 'ภาพหน้าปกเดิม',
        removeCoverLabel: 'ลบภาพหน้าปกเดิม',
        currentFilesLabel: 'ไฟล์แนบเดิม',
        removeFileHelp: 'ติ๊กไฟล์ที่ต้องการลบ แล้วกดอัปเดตประกาศ',
        newCoverLabel: 'เปลี่ยนภาพหน้าปก',
        newCoverHelp: 'เลือกไฟล์ใหม่เมื่ออยากเปลี่ยนภาพหน้าปก',
        addFilesLabel: 'แนบไฟล์เพิ่ม',
        updateButton: 'อัปเดตประกาศ',
        fileSingular: 'ไฟล์',
        filePlural: 'ไฟล์',
        deleteConfirm: 'ยืนยันลบประกาศนี้?'
      },
      en: {
        backHome: 'Back to home',
        loginTitle: 'Announcement Admin Login',
        loginSubtitle: 'Sign in to create and manage HR announcements.',
        loginButton: 'Sign in',
        addAnnouncement: 'Add announcement',
        closeCreate: 'Close form',
        createTitle: 'Create announcement',
        editTitle: 'Edit announcement',
        titleLabel: 'Announcement title',
        bodyLabel: 'Details',
        titleLabelEn: 'English title',
        bodyLabelEn: 'English details',
        titleLabelMy: 'Burmese title',
        bodyLabelMy: 'Burmese details',
        coverLabel: 'Cover image',
        coverHelp: 'Image shown as the news cover on the home and reading pages',
        attachLabel: 'Attachments',
        pinField: 'Pin this announcement on the news page',
        pinnedTag: 'Pinned',
        saveButton: 'Save announcement',
        empty: 'No HR announcements yet',
        coverTag: 'Cover image',
        editButton: 'Edit',
        hideEdit: 'Close edit',
        deleteButton: 'Delete',
        currentCoverLabel: 'Current cover image',
        removeCoverLabel: 'Remove current cover image',
        currentFilesLabel: 'Current attachments',
        removeFileHelp: 'Select files to remove, then update the announcement.',
        newCoverLabel: 'Replace cover image',
        newCoverHelp: 'Choose a new file only when replacing the cover image.',
        addFilesLabel: 'Add attachments',
        updateButton: 'Update announcement',
        fileSingular: 'file',
        filePlural: 'files',
        deleteConfirm: 'Delete this announcement?'
      },
      my: {
        backHome: 'ပင်မစာမျက်နှာသို့',
        loginTitle: 'ကြေညာချက် Admin Login',
        loginSubtitle: 'HR ကြေညာချက်များ ဖန်တီးရန်နှင့် စီမံရန် ဝင်ရောက်ပါ။',
        loginButton: 'ဝင်ရန်',
        addAnnouncement: 'ကြေညာချက်ထည့်ရန်',
        closeCreate: 'ဖောင်ပိတ်ရန်',
        createTitle: 'ကြေညာချက်အသစ် ဖန်တီးရန်',
        editTitle: 'ကြေညာချက်ပြင်ရန်',
        titleLabel: 'ကြေညာချက်ခေါင်းစဉ်',
        bodyLabel: 'အသေးစိတ်',
        titleLabelEn: 'အင်္ဂလိပ်ခေါင်းစဉ်',
        bodyLabelEn: 'အင်္ဂလိပ်အသေးစိတ်',
        titleLabelMy: 'မြန်မာခေါင်းစဉ်',
        bodyLabelMy: 'မြန်မာအသေးစိတ်',
        coverLabel: 'Cover ပုံ',
        coverHelp: 'Home နှင့် News page တွင်ပြမည့် ပုံ',
        attachLabel: 'ပူးတွဲဖိုင်များ',
        pinField: 'News page တွင် ပင်ထိုးပြရန်',
        pinnedTag: 'Pinned',
        saveButton: 'သိမ်းရန်',
        empty: 'HR ကြေညာချက်မရှိသေးပါ',
        coverTag: 'Cover ပုံရှိသည်',
        editButton: 'ပြင်ရန်',
        hideEdit: 'ပြင်ဆင်မှု ပိတ်ရန်',
        deleteButton: 'ဖျက်ရန်',
        currentCoverLabel: 'လက်ရှိ Cover ပုံ',
        removeCoverLabel: 'လက်ရှိ Cover ပုံကို ဖျက်ရန်',
        currentFilesLabel: 'လက်ရှိ ပူးတွဲဖိုင်များ',
        removeFileHelp: 'ဖျက်လိုသောဖိုင်ကို ရွေးပြီး ကြေညာချက်ကို အပ်ဒိတ်လုပ်ပါ။',
        newCoverLabel: 'Cover ပုံ ပြောင်းရန်',
        newCoverHelp: 'Cover ပုံပြောင်းရန် ဖိုင်အသစ်ကို ရွေးပါ။',
        addFilesLabel: 'ဖိုင်ထပ်ထည့်ရန်',
        updateButton: 'ကြေညာချက် အပ်ဒိတ်လုပ်ရန်',
        fileSingular: 'ဖိုင်',
        filePlural: 'ဖိုင်',
        deleteConfirm: 'ဤကြေညာချက်ကို ဖျက်မည်လား?'
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

    window.t = t;

    function updateCreateToggleLabel() {
      if (!toggleCreatePost || !createPostCard) {
        return;
      }
      const isOpen = createPostCard.classList.contains('is-open');
      toggleCreatePost.textContent = t(isOpen ? 'closeCreate' : 'addAnnouncement');
      toggleCreatePost.setAttribute('aria-expanded', String(isOpen));
    }

    function updateEditToggleLabels() {
      document.querySelectorAll('[data-edit-toggle]').forEach((button) => {
        const isOpen = button.getAttribute('aria-expanded') === 'true';
        button.textContent = t(isOpen ? 'hideEdit' : 'editButton');
      });
    }

    function updateFileTags() {
      document.querySelectorAll('[data-file-tag]').forEach((node) => {
        const count = Number(node.dataset.count || 0);
        const label = count === 1 ? t('fileSingular') : t('filePlural');
        node.textContent = `${count} ${label}`;
      });
    }

    function updateAnnouncementCards() {
      const suffix = currentLang === 'en' ? 'en' : currentLang === 'my' ? 'my' : 'th';
      document.querySelectorAll('[data-news-card]').forEach((card) => {
        const title = card.dataset[`title${suffix.charAt(0).toUpperCase()}${suffix.slice(1)}`] || card.dataset.titleTh || '';
        const body = card.dataset[`body${suffix.charAt(0).toUpperCase()}${suffix.slice(1)}`] || card.dataset.bodyTh || '';
        const titleNode = card.querySelector('[data-card-title]');
        const bodyNode = card.querySelector('[data-card-body]');

        if (titleNode) {
          titleNode.textContent = title;
        }

        if (bodyNode) {
          bodyNode.textContent = body;
        }
      });
    }

    function postPageForIndex(index, createOpen) {
      if (!createOpen) {
        return Math.floor(index / 12) + 1;
      }

      if (index < 10) {
        return 1;
      }

      return Math.floor((index - 10) / 12) + 2;
    }

    function postTotalPages(count, createOpen) {
      if (count <= 0) {
        return 1;
      }

      if (!createOpen) {
        return Math.max(1, Math.ceil(count / 12));
      }

      if (count <= 10) {
        return 1;
      }

      return Math.ceil((count - 10) / 12) + 1;
    }

    function renderPostPagination() {
      const postCards = Array.from(document.querySelectorAll('[data-post-card]'));
      const createOpen = createPostCard && createPostCard.classList.contains('is-open');
      const totalPages = postTotalPages(postCards.length, createOpen);

      if (currentPostPage > totalPages) {
        currentPostPage = totalPages;
      }

      if (currentPostPage < 1) {
        currentPostPage = 1;
      }

      postCards.forEach((card, index) => {
        card.classList.toggle('is-hidden-page', postPageForIndex(index, createOpen) !== currentPostPage);
      });

      if (createPostCard) {
        createPostCard.classList.toggle('is-hidden-page', createOpen && currentPostPage !== 1);
      }

      if (!postPagination) {
        return;
      }

      if (totalPages <= 1) {
        postPagination.innerHTML = '';
        return;
      }

      let html = '';
      for (let page = 1; page <= totalPages; page += 1) {
        const activeClass = page === currentPostPage ? ' is-active' : '';
        html += `<button class="post-page-btn${activeClass}" type="button" data-post-page="${page}">${page}</button>`;
      }
      postPagination.innerHTML = html;
    }

    function setLanguageMenu(open) {
      if (!languagePanel || !languageTrigger) {
        return;
      }
      languagePanel.classList.toggle('is-open', open);
      languageTrigger.setAttribute('aria-expanded', String(open));
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
      updateCreateToggleLabel();
      updateEditToggleLabels();
      updateFileTags();
      updateAnnouncementCards();

      if (shouldSave) {
        saveStoredLanguage(lang);
      }
    }

    function setFormTab(form, lang) {
      form.querySelectorAll('[data-form-tab]').forEach((button) => {
        const isActive = button.dataset.formTab === lang;
        button.classList.toggle('is-active', isActive);
        button.setAttribute('aria-selected', String(isActive));
      });

      form.querySelectorAll('[data-form-panel]').forEach((panel) => {
        panel.classList.toggle('is-active', panel.dataset.formPanel === lang);
      });
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

    document.querySelectorAll('[data-announcement-form]').forEach((form) => {
      form.querySelectorAll('[data-form-tab]').forEach((button) => {
        button.addEventListener('click', () => {
          setFormTab(form, button.dataset.formTab);
        });
      });
    });

    if (toggleCreatePost && createPostCard) {
      toggleCreatePost.addEventListener('click', () => {
        const willOpen = !createPostCard.classList.contains('is-open');
        createPostCard.classList.toggle('is-open', willOpen);
        currentPostPage = 1;
        updateCreateToggleLabel();
        renderPostPagination();

        if (willOpen) {
          const firstField = createPostCard.querySelector('input[name="title"]');
          if (firstField) {
            firstField.focus();
          }
        }
      });
    }

    if (postPagination) {
      postPagination.addEventListener('click', (event) => {
        const button = event.target.closest('[data-post-page]');
        if (!button) {
          return;
        }
        currentPostPage = Number(button.dataset.postPage);
        renderPostPagination();
      });
    }

    document.querySelectorAll('[data-edit-toggle]').forEach((button) => {
      button.addEventListener('click', () => {
        const panel = document.getElementById(button.getAttribute('aria-controls'));
        if (!panel) {
          return;
        }

        const willOpen = !panel.classList.contains('is-open');
        panel.classList.toggle('is-open', willOpen);
        button.setAttribute('aria-expanded', String(willOpen));
        updateEditToggleLabels();

        if (willOpen) {
          const firstField = panel.querySelector('input[name="title"]');
          if (firstField) {
            firstField.focus();
          }
        }
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
    renderPostPagination();

    if (ambientPhoto && galleryImages.length > 1) {
      let heroIndex = 0;
      setInterval(() => {
        heroIndex = (heroIndex + 1) % galleryImages.length;
        ambientPhoto.style.opacity = '0';
        setTimeout(() => {
          ambientPhoto.src = galleryImages[heroIndex];
          ambientPhoto.style.opacity = '0.18';
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
