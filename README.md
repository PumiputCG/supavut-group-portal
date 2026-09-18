# SUPAVUT GROUP Portal

**TH:** พอร์ทัลพนักงานรุ่นแรก — PHP ล้วนๆ ไม่มีเฟรมเวิร์ก และเป็นต้นทางของ SI MENU
**EN:** The first employee portal — plain PHP, no framework, and the direct ancestor of SI MENU.

`PHP 8` · `Tailwind CSS 4` · `MS SQL Server (ODBC)` · `Vanilla JavaScript`

---

## 🇹🇭 ภาษาไทย

### ที่มา

นี่คือเวอร์ชันแรกของพอร์ทัลพนักงาน เขียนด้วย PHP ล้วนไม่มีเฟรมเวิร์ก ตั้งใจให้เบาและ deploy ง่ายบน XAMPP ที่มีอยู่แล้ว ทำหน้าที่รวมลิงก์ระบบภายในและประกาศบริษัทไว้ที่เดียว

ภายหลังโปรเจคนี้ถูกพัฒนาต่อเป็น **SI MENU** ที่เพิ่มทะเบียนเอกสาร ระบบหลายภาษา และการส่งออกไฟล์ — แต่โครงสร้างพื้นฐานเริ่มจากที่นี่

### มีอะไรบ้าง

- **หน้าแรกรวมแอป** — ลิงก์ไปทุกระบบภายในพร้อมไอคอน
- **ประกาศบริษัท** — หน้าแสดงประกาศ หน้าจัดการสำหรับแอดมิน และดาวน์โหลดไฟล์แนบ
- **ล็อกอิน** — ตรวจสอบกับฐานข้อมูลพนักงานบน SQL Server
- **PWA** — มี `site.webmanifest` ให้ติดตั้งลงหน้าจอมือถือได้

### โครงสร้าง

```
index.php               → หน้าแรก รวมแอปทั้งหมด
login.php               → เข้าสู่ระบบ
conn.php                → เชื่อมฐานข้อมูล (gitignored)
nav.php / footer.php    → ส่วนประกอบที่ใช้ซ้ำ
announcements.php       → หน้าประกาศ
announcements_admin.php → หน้าจัดการประกาศ
announcement_view.php   → ดูประกาศรายชิ้น
announcement_download.php → ดาวน์โหลดไฟล์แนบ
announcement_helpers.php  → ฟังก์ชันช่วย
src/input.css → src/output.css  → Tailwind
```

### ติดตั้ง

```bash
npm install
cp conn.example.php conn.php   # ใส่ค่าฐานข้อมูลของคุณ
npm run build
```

---

## 🇬🇧 English

### Background

This is the first version of the employee portal — plain PHP, no framework, deliberately lightweight so it could drop straight onto the XAMPP server that already existed. It gathered internal system links and company announcements in one place.

It later grew into **SI MENU**, which added the document register, multilingual support, and file exports. But the foundation started here.

### What's in it

- **Application home** — every internal system, linked and iconed
- **Announcements** — a public view, an admin management page, and attachment downloads
- **Login** against the employee database on SQL Server
- **PWA support** via `site.webmanifest` for home-screen install

### Setup

```bash
npm install
cp conn.example.php conn.php   # fill in your own database values
npm run build
```

> ⚠️ `conn.php` holds database credentials and is gitignored.

### Note

Code only. Uploads and announcement data are excluded.
