# Panduan Instalasi Projek PABW Marketplace AI

Projek ini adalah marketplace gaming gear yang terintegrasi dengan AI Chatbot lokal (Ollama + Qwen 2.5). Berikut adalah langkah-langkah lengkap untuk menjalankan projek ini di komputer lokal Anda.

## 1. Prasyarat (Prerequisites)
Pastikan komputer Anda sudah terinstal software berikut:
- **PHP** (minimal versi 8.2)
- **Composer** (untuk dependensi Laravel)
- **Node.js & NPM** (untuk dependensi Vue.js/Vite)
- **Git**
- **Ollama** (untuk menjalankan model AI secara lokal)
- **SQLite** (sudah termasuk dalam PHP biasanya)

---

## 2. Setup AI (Ollama)
Projek ini menggunakan model AI Qwen 2.5 secara lokal.
1. Download dan instal Ollama di [ollama.com](https://ollama.com).
2. Jalankan terminal/command prompt, lalu jalankan perintah berikut untuk mengunduh model Qwen 2.5:
   ```bash
   ollama pull qwen2.5
   ```
3. Pastikan Ollama berjalan di background (biasanya di `http://localhost:11434`).

---

## 3. Setup Projek (Backend & Frontend)

### Langkah 1: Clone Repositori
Buka terminal dan jalankan:
```bash
git clone https://github.com/fienda87/pabw-marketplace-ai.git
cd pabw-marketplace-ai
```

### Langkah 2: Setup Backend (Laravel)
1. Instal dependensi PHP:
   ```bash
   composer install
   ```
2. Duplikat file `.env`:
   ```bash
   cp .env.example .env
   ```
3. Generate kunci aplikasi:
   ```bash
   php artisan key:generate
   ```
4. Konfigurasi Database:
   - Secara default projek ini menggunakan **SQLite**.
   - Di file `.env`, pastikan pengaturannya seperti ini:
     ```env
     DB_CONNECTION=sqlite
     # Hapus atau kosongkan baris DB_DATABASE lainnya
     ```
   - Buat file database kosong (khusus Linux/Mac): `touch database/database.sqlite`. Untuk Windows, buat file baru bernama `database.sqlite` di folder `database`.
5. Jalankan Migrasi dan Seeding (Data Awal):
   ```bash
   php artisan migrate --seed
   ```
   *Ini akan membuat tabel dan mengisi data produk gaming gear awal serta akun Admin.*

### Langkah 3: Setup Frontend (Vue.js)
1. Instal dependensi JavaScript:
   ```bash
   npm install
   ```
2. Build atau jalankan compiler aset:
   ```bash
   npm run dev
   ```

---

## 4. Cara Menjalankan Aplikasi
Anda perlu menjalankan dua perintah ini di dua terminal yang berbeda:

**Terminal 1 (Backend):**
```bash
php artisan serve
```
Aplikasi akan berjalan di `http://127.0.0.1:8000`.

**Terminal 2 (Frontend/Vite):**
```bash
npm run dev
```

---

## 5. Akun Akses (Default)
Setelah berhasil dijalankan, Anda bisa login menggunakan akun berikut:

**Akun Admin:**
- **Email:** `admin@pabw.com`
- **Password:** `password`

**Akun User Biasa:**
- Silakan daftar melalui halaman **Register**.

---

## 6. Fitur AI Chat
- Pastikan aplikasi Ollama sedang berjalan.
- Anda bisa langsung menuju menu **Chat AI** di sidebar kiri untuk berkonsultasi mengenai gaming gear.
- AI akan memberikan rekomendasi berdasarkan stok produk yang ada di database Anda.

---
**Catatan:** Jika ada kendala koneksi ke Ollama, pastikan URL di file `.env` sudah benar atau biarkan default ke `localhost`.
