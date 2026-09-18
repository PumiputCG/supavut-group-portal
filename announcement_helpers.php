<?php
define('ANNOUNCEMENT_ADMIN_USERNAME', 'admin');
define('ANNOUNCEMENT_ADMIN_PASSWORD', '000000');
define('ANNOUNCEMENT_DATA_DIR', dirname(__FILE__) . '/data');
define('ANNOUNCEMENT_DATA_FILE', ANNOUNCEMENT_DATA_DIR . '/announcements.json');
define('ANNOUNCEMENT_UPLOAD_DIR', dirname(__FILE__) . '/uploads/announcements');
define('ANNOUNCEMENT_COVER_DIR', ANNOUNCEMENT_UPLOAD_DIR . '/covers');
define('ANNOUNCEMENT_MAX_FILE_SIZE', 100 * 1024 * 1024);

function announcement_start_session()
{
  if (function_exists('session_status')) {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    return;
  }

  if (session_id() === '') {
    session_start();
  }
}

function announcement_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function announcement_field($announcement, $key)
{
  return isset($announcement[$key]) ? $announcement[$key] : '';
}

function announcement_ensure_storage()
{
  if (!is_dir(ANNOUNCEMENT_DATA_DIR)) {
    mkdir(ANNOUNCEMENT_DATA_DIR, 0775, true);
  }

  if (!is_dir(ANNOUNCEMENT_UPLOAD_DIR)) {
    mkdir(ANNOUNCEMENT_UPLOAD_DIR, 0775, true);
  }

  if (!is_dir(ANNOUNCEMENT_COVER_DIR)) {
    mkdir(ANNOUNCEMENT_COVER_DIR, 0775, true);
  }

  if (!file_exists(ANNOUNCEMENT_DATA_FILE)) {
    file_put_contents(ANNOUNCEMENT_DATA_FILE, "[]");
  }
}

function announcement_json_encode($data)
{
  if (defined('JSON_PRETTY_PRINT') && defined('JSON_UNESCAPED_UNICODE')) {
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }

  return json_encode($data);
}

function announcement_read_all()
{
  announcement_ensure_storage();
  $raw = file_get_contents(ANNOUNCEMENT_DATA_FILE);
  $items = json_decode($raw, true);

  if (!is_array($items)) {
    return array();
  }

  usort($items, 'announcement_sort_latest');
  return $items;
}

function announcement_sort_latest($a, $b)
{
  $aPinned = announcement_is_pinned($a) ? 1 : 0;
  $bPinned = announcement_is_pinned($b) ? 1 : 0;

  if ($aPinned !== $bPinned) {
    return ($aPinned < $bPinned) ? 1 : -1;
  }

  $aTime = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
  $bTime = isset($b['created_at']) ? strtotime($b['created_at']) : 0;

  if ($aTime === $bTime) {
    return 0;
  }

  return ($aTime < $bTime) ? 1 : -1;
}

function announcement_is_pinned($announcement)
{
  if (!isset($announcement['pinned'])) {
    return false;
  }

  return $announcement['pinned'] === true || $announcement['pinned'] === 1 || $announcement['pinned'] === '1';
}

function announcement_write_all($items)
{
  announcement_ensure_storage();
  $encoded = announcement_json_encode(array_values($items));

  if ($encoded === false) {
    return false;
  }

  return file_put_contents(ANNOUNCEMENT_DATA_FILE, $encoded, LOCK_EX) !== false;
}

function announcement_allowed_extensions()
{
  return array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp');
}

function announcement_safe_id()
{
  if (function_exists('random_bytes')) {
    return date('YmdHis') . bin2hex(random_bytes(6));
  }

  return date('YmdHis') . mt_rand(100000, 999999);
}

function announcement_format_file_size($bytes)
{
  $bytes = (int) $bytes;
  if ($bytes >= 1048576) {
    return number_format($bytes / 1048576, 1) . ' MB';
  }

  if ($bytes >= 1024) {
    return number_format($bytes / 1024, 1) . ' KB';
  }

  return $bytes . ' B';
}

function announcement_gallery_images()
{
  $galleryDir = dirname(__FILE__) . '/img/gallery';
  $galleryImages = array();

  if (!is_dir($galleryDir)) {
    return $galleryImages;
  }

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

  natcasesort($filtered);
  foreach ($filtered as $file) {
    $galleryImages[] = 'img/gallery/' . rawurlencode($file);
  }

  return array_values($galleryImages);
}

function announcement_get_attachments($announcement)
{
  if (isset($announcement['attachments']) && is_array($announcement['attachments'])) {
    return array_values($announcement['attachments']);
  }

  if (isset($announcement['attachment']) && is_array($announcement['attachment'])) {
    return array($announcement['attachment']);
  }

  return array();
}

function announcement_file_kind($attachment)
{
  $extension = '';
  if (isset($attachment['extension'])) {
    $extension = strtolower((string) $attachment['extension']);
  } elseif (isset($attachment['original_name'])) {
    $extension = strtolower(pathinfo($attachment['original_name'], PATHINFO_EXTENSION));
  } elseif (isset($attachment['stored_name'])) {
    $extension = strtolower(pathinfo($attachment['stored_name'], PATHINFO_EXTENSION));
  }

  if (in_array($extension, array('jpg', 'jpeg', 'png', 'webp'))) {
    return 'image';
  }

  if ($extension === 'pdf') {
    return 'pdf';
  }

  if (in_array($extension, array('doc', 'docx'))) {
    return 'word';
  }

  if (in_array($extension, array('xls', 'xlsx'))) {
    return 'excel';
  }

  if (in_array($extension, array('ppt', 'pptx'))) {
    return 'powerpoint';
  }

  return 'document';
}

function announcement_file_short_label($kind)
{
  $labels = array(
    'image' => 'IMG',
    'pdf' => 'PDF',
    'word' => 'DOC',
    'excel' => 'XLS',
    'powerpoint' => 'PPT',
    'document' => 'FILE'
  );

  return isset($labels[$kind]) ? $labels[$kind] : 'FILE';
}

function announcement_file_icon_svg($kind)
{
  $icons = array(
    'pdf' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" fill="currentColor" fill-opacity="0.14"/><path d="M14 2.5v3.2A1.3 1.3 0 0 0 15.3 7h3.2" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><text x="12" y="16.6" text-anchor="middle" font-size="6.6" font-weight="800" fill="currentColor" font-family="Arial, sans-serif">PDF</text></svg>',
    'word' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" fill="currentColor" fill-opacity="0.14"/><path d="M14 2.5v3.2A1.3 1.3 0 0 0 15.3 7h3.2" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><text x="12" y="16.6" text-anchor="middle" font-size="6.6" font-weight="800" fill="currentColor" font-family="Arial, sans-serif">DOC</text></svg>',
    'excel' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" fill="currentColor" fill-opacity="0.14"/><path d="M14 2.5v3.2A1.3 1.3 0 0 0 15.3 7h3.2" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><text x="12" y="16.6" text-anchor="middle" font-size="6.6" font-weight="800" fill="currentColor" font-family="Arial, sans-serif">XLS</text></svg>',
    'powerpoint' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" fill="currentColor" fill-opacity="0.14"/><path d="M14 2.5v3.2A1.3 1.3 0 0 0 15.3 7h3.2" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><text x="12" y="16.6" text-anchor="middle" font-size="6.6" font-weight="800" fill="currentColor" font-family="Arial, sans-serif">PPT</text></svg>',
    'image' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3.5" y="4.5" width="17" height="15" rx="2" fill="currentColor" fill-opacity="0.14" stroke="currentColor" stroke-width="1.4"/><circle cx="8.5" cy="9.5" r="1.6" fill="currentColor"/><path d="m4 17 5-5 3.5 3.5L17 11l3.5 3.5" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" stroke-linecap="round"/></svg>',
    'document' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 2.5h7l4 4V20a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 20V4A1.5 1.5 0 0 1 7 2.5Z" fill="currentColor" fill-opacity="0.14" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M14 2.5v3.2A1.3 1.3 0 0 0 15.3 7h3.2" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M9 13h6M9 16.3h4.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>'
  );

  return isset($icons[$kind]) ? $icons[$kind] : $icons['document'];
}

function announcement_file_icon_url($kind)
{
  $urls = array(
    'pdf' => 'img/file-icons/pdf.png',
    'word' => 'img/file-icons/word.webp',
    'excel' => 'img/file-icons/excel.jpg',
    'powerpoint' => 'img/file-icons/powerpoint.jpg',
    'image' => 'img/file-icons/image.png'
  );

  return isset($urls[$kind]) ? $urls[$kind] : '';
}

function announcement_attachment_url($announcementId, $attachment)
{
  $fileId = isset($attachment['id']) ? $attachment['id'] : (isset($attachment['stored_name']) ? $attachment['stored_name'] : '');
  return 'announcement_download.php?id=' . rawurlencode($announcementId) . '&file=' . rawurlencode($fileId);
}

function announcement_date_day($date)
{
  $time = strtotime($date);
  if (!$time) {
    return '';
  }

  return date('d', $time);
}

function announcement_date_month_short_th($date)
{
  $time = strtotime($date);
  if (!$time) {
    return '';
  }

  $months = array('', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.');
  return $months[(int) date('n', $time)];
}

function announcement_is_new($announcement)
{
  if (!isset($announcement['created_at'])) {
    return false;
  }

  $time = strtotime($announcement['created_at']);
  if (!$time) {
    return false;
  }

  return (time() - $time) < 86400;
}

function announcement_normalize_files($files)
{
  if (!isset($files) || !isset($files['name'])) {
    return array();
  }

  if (!is_array($files['name'])) {
    return array($files);
  }

  $normalized = array();
  $count = count($files['name']);
  for ($i = 0; $i < $count; $i++) {
    $normalized[] = array(
      'name' => isset($files['name'][$i]) ? $files['name'][$i] : '',
      'type' => isset($files['type'][$i]) ? $files['type'][$i] : '',
      'tmp_name' => isset($files['tmp_name'][$i]) ? $files['tmp_name'][$i] : '',
      'error' => isset($files['error'][$i]) ? $files['error'][$i] : UPLOAD_ERR_NO_FILE,
      'size' => isset($files['size'][$i]) ? $files['size'][$i] : 0
    );
  }

  return $normalized;
}

function announcement_delete_uploaded_files($attachments)
{
  foreach ($attachments as $attachment) {
    if (!isset($attachment['stored_name'])) {
      continue;
    }

    $filePath = ANNOUNCEMENT_UPLOAD_DIR . '/' . basename($attachment['stored_name']);
    if (is_file($filePath)) {
      unlink($filePath);
    }
  }
}

function announcement_store_uploaded_file($file, &$error)
{
  $error = '';

  if (!isset($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
    return null;
  }

  if ($file['error'] !== UPLOAD_ERR_OK) {
    $error = 'อัปโหลดไฟล์ไม่สำเร็จ';
    return false;
  }

  if ($file['size'] > ANNOUNCEMENT_MAX_FILE_SIZE) {
    $error = 'ไฟล์ต้องมีขนาดไม่เกิน 100MB';
    return false;
  }

  $originalName = basename($file['name']);
  $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
  if (!in_array($extension, announcement_allowed_extensions())) {
    $error = 'รองรับเฉพาะไฟล์ PDF, Office และรูปภาพ';
    return false;
  }

  announcement_ensure_storage();
  $storedName = announcement_safe_id() . '.' . $extension;
  $targetPath = ANNOUNCEMENT_UPLOAD_DIR . '/' . $storedName;

  if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    $error = 'บันทึกไฟล์แนบไม่สำเร็จ';
    return false;
  }

  return array(
    'id' => announcement_safe_id(),
    'original_name' => $originalName,
    'stored_name' => $storedName,
    'size' => (int) $file['size'],
    'extension' => $extension
  );
}

function announcement_upload_file($file, &$error)
{
  return announcement_store_uploaded_file($file, $error);
}

function announcement_store_cover_image($file, &$error)
{
  $error = '';

  if (!isset($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
    return null;
  }

  if ($file['error'] !== UPLOAD_ERR_OK) {
    $error = 'อัปโหลดภาพหน้าปกไม่สำเร็จ';
    return false;
  }

  if ($file['size'] > ANNOUNCEMENT_MAX_FILE_SIZE) {
    $error = 'ภาพหน้าปกต้องมีขนาดไม่เกิน 100MB';
    return false;
  }

  $originalName = basename($file['name']);
  $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
  if (!in_array($extension, array('jpg', 'jpeg', 'png', 'webp'))) {
    $error = 'ภาพหน้าปกรองรับเฉพาะไฟล์ JPG, PNG หรือ WEBP';
    return false;
  }

  announcement_ensure_storage();
  $storedName = announcement_safe_id() . '.' . $extension;
  $targetPath = ANNOUNCEMENT_COVER_DIR . '/' . $storedName;

  if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    $error = 'บันทึกภาพหน้าปกไม่สำเร็จ';
    return false;
  }

  return array(
    'original_name' => $originalName,
    'stored_name' => $storedName,
    'extension' => $extension
  );
}

function announcement_cover_url($announcement)
{
  if (!isset($announcement['cover_image']) || !is_array($announcement['cover_image'])) {
    return '';
  }

  $cover = $announcement['cover_image'];
  if (!isset($cover['stored_name']) || $cover['stored_name'] === '') {
    return '';
  }

  return 'uploads/announcements/covers/' . rawurlencode($cover['stored_name']);
}

function announcement_delete_cover_image($announcement)
{
  if (!isset($announcement['cover_image']) || !is_array($announcement['cover_image'])) {
    return;
  }

  $cover = $announcement['cover_image'];
  if (!isset($cover['stored_name'])) {
    return;
  }

  $filePath = ANNOUNCEMENT_COVER_DIR . '/' . basename($cover['stored_name']);
  if (is_file($filePath)) {
    unlink($filePath);
  }
}

function announcement_upload_files($files, &$errors)
{
  $errors = array();
  $attachments = array();
  $normalizedFiles = announcement_normalize_files($files);

  foreach ($normalizedFiles as $file) {
    $uploadError = '';
    $attachment = announcement_store_uploaded_file($file, $uploadError);

    if ($attachment === false) {
      if ($uploadError !== '') {
        $errors[] = $uploadError;
      }
      announcement_delete_uploaded_files($attachments);
      return false;
    }

    if ($attachment !== null) {
      $attachments[] = $attachment;
    }
  }

  return $attachments;
}

function announcement_create($title, $body, $files, $coverFile, $translations, &$errors, $pinned = false)
{
  $errors = array();
  $title = trim($title);
  $body = trim($body);
  $titleEn = isset($translations['en']['title']) ? trim($translations['en']['title']) : '';
  $bodyEn = isset($translations['en']['body']) ? trim($translations['en']['body']) : '';
  $titleMy = isset($translations['my']['title']) ? trim($translations['my']['title']) : '';
  $bodyMy = isset($translations['my']['body']) ? trim($translations['my']['body']) : '';

  if ($title === '') {
    $errors[] = 'กรุณากรอกหัวข้อประกาศ';
  }

  if ($body === '') {
    $errors[] = 'กรุณากรอกรายละเอียดประกาศ';
  }

  if (count($errors) > 0) {
    return false;
  }

  $uploadErrors = array();
  $attachments = announcement_upload_files($files, $uploadErrors);
  if ($attachments === false) {
    $errors = array_merge($errors, $uploadErrors);
  }

  $coverError = '';
  $coverImage = announcement_store_cover_image($coverFile, $coverError);
  if ($coverImage === false) {
    $errors[] = $coverError;
  }

  if (count($errors) > 0) {
    if (is_array($attachments)) {
      announcement_delete_uploaded_files($attachments);
    }
    return false;
  }

  $items = announcement_read_all();
  array_unshift($items, array(
    'id' => announcement_safe_id(),
    'title' => $title,
    'body' => $body,
    'title_en' => $titleEn,
    'body_en' => $bodyEn,
    'title_my' => $titleMy,
    'body_my' => $bodyMy,
    'attachment' => count($attachments) > 0 ? $attachments[0] : null,
    'attachments' => $attachments,
    'cover_image' => $coverImage,
    'pinned' => $pinned ? true : false,
    'created_at' => date('Y-m-d H:i:s')
  ));

  if (!announcement_write_all($items)) {
    announcement_delete_uploaded_files($attachments);
    if ($coverImage) {
      announcement_delete_cover_image(array('cover_image' => $coverImage));
    }
    $errors[] = 'บันทึกประกาศไม่สำเร็จ';
    return false;
  }

  return true;
}

function announcement_attachment_identifier($attachment)
{
  if (isset($attachment['id']) && trim((string) $attachment['id']) !== '') {
    return (string) $attachment['id'];
  }

  if (isset($attachment['stored_name']) && trim((string) $attachment['stored_name']) !== '') {
    return (string) $attachment['stored_name'];
  }

  return '';
}

function announcement_remaining_attachments($attachments, $removeIds, &$removed)
{
  $remaining = array();
  $removed = array();
  $removeMap = array();

  if (!is_array($removeIds)) {
    $removeIds = array($removeIds);
  }

  foreach ($removeIds as $removeId) {
    $key = trim((string) $removeId);
    if ($key !== '') {
      $removeMap[$key] = true;
    }
  }

  foreach ($attachments as $attachment) {
    $attachmentId = announcement_attachment_identifier($attachment);
    if ($attachmentId !== '' && isset($removeMap[$attachmentId])) {
      $removed[] = $attachment;
      continue;
    }

    $remaining[] = $attachment;
  }

  return $remaining;
}

function announcement_update($id, $title, $body, $files, $coverFile, $translations, $removeAttachmentIds, $removeCover, &$errors, $pinned = false)
{
  $errors = array();
  $id = trim((string) $id);
  $title = trim($title);
  $body = trim($body);
  $titleEn = isset($translations['en']['title']) ? trim($translations['en']['title']) : '';
  $bodyEn = isset($translations['en']['body']) ? trim($translations['en']['body']) : '';
  $titleMy = isset($translations['my']['title']) ? trim($translations['my']['title']) : '';
  $bodyMy = isset($translations['my']['body']) ? trim($translations['my']['body']) : '';

  if ($id === '') {
    $errors[] = 'ไม่พบประกาศที่ต้องการแก้ไข';
  }

  if ($title === '') {
    $errors[] = 'กรุณากรอกหัวข้อประกาศ';
  }

  if ($body === '') {
    $errors[] = 'กรุณากรอกรายละเอียดประกาศ';
  }

  if (count($errors) > 0) {
    return false;
  }

  $items = announcement_read_all();
  $foundIndex = -1;
  $oldItem = null;

  foreach ($items as $index => $item) {
    if (isset($item['id']) && $item['id'] === $id) {
      $foundIndex = $index;
      $oldItem = $item;
      break;
    }
  }

  if ($foundIndex < 0 || !is_array($oldItem)) {
    $errors[] = 'ไม่พบประกาศที่ต้องการแก้ไข';
    return false;
  }

  $removedAttachments = array();
  $remainingAttachments = announcement_remaining_attachments(announcement_get_attachments($oldItem), $removeAttachmentIds, $removedAttachments);

  $uploadErrors = array();
  $newAttachments = announcement_upload_files($files, $uploadErrors);
  if ($newAttachments === false) {
    $errors = array_merge($errors, $uploadErrors);
    return false;
  }

  $coverError = '';
  $newCoverImage = announcement_store_cover_image($coverFile, $coverError);
  if ($newCoverImage === false) {
    announcement_delete_uploaded_files($newAttachments);
    $errors[] = $coverError;
    return false;
  }

  $oldCoverImage = isset($oldItem['cover_image']) && is_array($oldItem['cover_image']) ? $oldItem['cover_image'] : null;
  $coverImage = $oldCoverImage;
  $deleteOldCover = false;

  if ($newCoverImage !== null) {
    $coverImage = $newCoverImage;
    $deleteOldCover = $oldCoverImage !== null;
  } elseif ($removeCover && $oldCoverImage !== null) {
    $coverImage = null;
    $deleteOldCover = true;
  }

  $attachments = array_merge($remainingAttachments, $newAttachments);
  $updatedItem = $oldItem;
  $updatedItem['title'] = $title;
  $updatedItem['body'] = $body;
  $updatedItem['title_en'] = $titleEn;
  $updatedItem['body_en'] = $bodyEn;
  $updatedItem['title_my'] = $titleMy;
  $updatedItem['body_my'] = $bodyMy;
  $updatedItem['attachment'] = count($attachments) > 0 ? $attachments[0] : null;
  $updatedItem['attachments'] = $attachments;
  $updatedItem['cover_image'] = $coverImage;
  $updatedItem['pinned'] = $pinned ? true : false;
  $updatedItem['created_at'] = isset($oldItem['created_at']) ? $oldItem['created_at'] : date('Y-m-d H:i:s');
  $updatedItem['updated_at'] = date('Y-m-d H:i:s');

  $items[$foundIndex] = $updatedItem;

  if (!announcement_write_all($items)) {
    announcement_delete_uploaded_files($newAttachments);
    if ($newCoverImage !== null) {
      announcement_delete_cover_image(array('cover_image' => $newCoverImage));
    }
    $errors[] = 'อัปเดตประกาศไม่สำเร็จ';
    return false;
  }

  if (count($removedAttachments) > 0) {
    announcement_delete_uploaded_files($removedAttachments);
  }

  if ($deleteOldCover && $oldCoverImage !== null) {
    announcement_delete_cover_image(array('cover_image' => $oldCoverImage));
  }

  return true;
}

function announcement_set_pinned($id, $pinned, &$errors)
{
  $errors = array();
  $id = trim((string) $id);

  if ($id === '') {
    $errors[] = 'ไม่พบประกาศที่ต้องการปักหมุด';
    return false;
  }

  $items = announcement_read_all();
  $found = false;

  foreach ($items as $index => $item) {
    if (isset($item['id']) && $item['id'] === $id) {
      $items[$index]['pinned'] = $pinned ? true : false;
      $items[$index]['updated_at'] = date('Y-m-d H:i:s');
      $found = true;
      break;
    }
  }

  if (!$found) {
    $errors[] = 'ไม่พบประกาศที่ต้องการปักหมุด';
    return false;
  }

  if (!announcement_write_all($items)) {
    $errors[] = 'อัปเดตสถานะปักหมุดไม่สำเร็จ';
    return false;
  }

  return true;
}

function announcement_delete($id)
{
  $items = announcement_read_all();
  $nextItems = array();
  $deleted = false;

  foreach ($items as $item) {
    if (isset($item['id']) && $item['id'] === $id) {
      announcement_delete_uploaded_files(announcement_get_attachments($item));
      announcement_delete_cover_image($item);
      $deleted = true;
      continue;
    }

    $nextItems[] = $item;
  }

  if ($deleted) {
    return announcement_write_all($nextItems);
  }

  return false;
}

function announcement_is_admin()
{
  announcement_start_session();
  return isset($_SESSION['announcement_admin']) && $_SESSION['announcement_admin'] === true;
}

function announcement_login($username, $password)
{
  announcement_start_session();
  if ($username === ANNOUNCEMENT_ADMIN_USERNAME && $password === ANNOUNCEMENT_ADMIN_PASSWORD) {
    $_SESSION['announcement_admin'] = true;
    session_regenerate_id(true);
    return true;
  }

  return false;
}

function announcement_logout()
{
  announcement_start_session();
  unset($_SESSION['announcement_admin']);
}

function announcement_csrf_token()
{
  announcement_start_session();
  if (!isset($_SESSION['announcement_csrf'])) {
    $_SESSION['announcement_csrf'] = announcement_safe_id();
  }

  return $_SESSION['announcement_csrf'];
}

function announcement_check_csrf($token)
{
  announcement_start_session();
  return isset($_SESSION['announcement_csrf']) && $token === $_SESSION['announcement_csrf'];
}

function announcement_format_date($date)
{
  $time = strtotime($date);
  if (!$time) {
    return '';
  }

  return date('d/m/Y H:i', $time);
}

function announcement_localized_title($announcement, $lang)
{
  if ($lang === 'en' && isset($announcement['title_en']) && trim((string) $announcement['title_en']) !== '') {
    return $announcement['title_en'];
  }

  if ($lang === 'my' && isset($announcement['title_my']) && trim((string) $announcement['title_my']) !== '') {
    return $announcement['title_my'];
  }

  return isset($announcement['title']) ? $announcement['title'] : '';
}

function announcement_localized_body($announcement, $lang)
{
  if ($lang === 'en' && isset($announcement['body_en']) && trim((string) $announcement['body_en']) !== '') {
    return $announcement['body_en'];
  }

  if ($lang === 'my' && isset($announcement['body_my']) && trim((string) $announcement['body_my']) !== '') {
    return $announcement['body_my'];
  }

  return isset($announcement['body']) ? $announcement['body'] : '';
}
?>
