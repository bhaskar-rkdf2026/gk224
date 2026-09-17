# 🚀 GK224.COM - Complete Server Deployment Guide

Yeh guide aapko GK224.COM application ko kisi bhi live web hosting server (cPanel / Hostinger / GoDaddy / Namecheap / VPS / Apache) par upload aur setup karne ke step-by-step instructions deti hai.

---

## 📋 Server Requirements
* **PHP Version:** PHP 7.4, 8.0, 8.1, 8.2 ya 8.3+
* **Web Server:** Apache / LiteSpeed / Nginx
* **Extensions:** `pdo`, `pdo_mysql`, `session`, `json`, `mod_rewrite` (cPanel me default enabled hoti hain)
* **Database (Optional):** MySQL / MariaDB

---

## 🛠️ Step-by-Step Server Upload Guide

### Step 1: Files Ko ZIP Karein
1. Apne computer par `e:/gk224/` folder ke andar jaayein.
2. Saari files aur folders ko select karein:
   * `assets/`
   * `includes/`
   * `api/`
   * `index.php`
   * `login.php`
   * `learn.php`
   * `earn.php`
   * `travel.php`
   * `profile.php`
   * `referral.php`
   * `reffral.php`
   * `scratchpad.php`
   * `.htaccess`
   * `schema.sql`
3. Right click karke **Compress to ZIP** (`gk224.zip`) bana lein.

---

### Step 2: cPanel / Hosting File Manager Par Upload Karein
1. Apne Hosting cPanel me Login karein.
2. **File Manager** open karein aur **`public_html`** (ya jahan domain point hai) me jaayein.
3. **Upload** button par click karke `gk224.zip` file upload karein.
4. Upload hone ke baad ZIP file par right click karke **Extract** kar dein.

---

### Step 3: (Optional) MySQL Database Setup Karein
Agar aap server-side database use karna chahte hain:
1. cPanel me **MySQL Databases** me jaakar ek naya Database banayein (e.g. `u12345_gk224`).
2. Ek naya Database User banayein aur use Database me **All Privileges** ke sath add karein.
3. cPanel me **phpMyAdmin** open karein.
4. Apne database par click karein aur upar **Import** tab me jaakar `schema.sql` file choose karke **Go** par click kar dein.
5. `includes/config.php` file edit karein aur database details enter karein:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_db_username');
   define('DB_PASS', 'your_db_password');
   define('DB_NAME', 'your_db_name');
   ```

*(Note: Agar aap bina database ke bhi run karenge toh application client-side localStorage state engine ke sath 100% smoothly chalegi!)*

---

### Step 4: Test Karein
Apna domain browser me open karein:
* `https://yourdomain.com/` (Pay Dashboard)
* `https://yourdomain.com/login.php` (Login / Signup)
* `https://yourdomain.com/learn.php` (Learning Hub)
* `https://yourdomain.com/earn.php` (Earn Hub)
* `https://yourdomain.com/travel.php` (Travel Deals)
* `https://yourdomain.com/scratchpad.php` (Lucky Scratchpad)
* `https://yourdomain.com/referral.php` (Referral Contest)

---

## 🔒 Security Features Included
* **`.htaccess`**: Automatic HTTPS, clean URLs, XSS protection, anti-clickjacking headers, and blocking sensitive files.
* **Gzip & Caching**: Automatic deflate compression for fast 60fps mobile loading.
* **Fallback Architecture**: Server offline hone par bhi application bina error ke offline localStorage mode me chalegi.
