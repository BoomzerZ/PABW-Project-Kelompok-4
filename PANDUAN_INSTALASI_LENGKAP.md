# 🎮 Panduan Instalasi Lengkap — PABW Marketplace AI
> **Stack:** Laravel 11 · Vue.js 3 · SQLite · Ollama + Qwen 2.5
> 
> Panduan ini mencakup **semua langkah** dari nol sampai AI chatbot berjalan sempurna terintegrasi dengan sistem marketplace.

---

## 📋 Daftar Isi

1. [Cek Spesifikasi & Prasyarat](#1-cek-spesifikasi--prasyarat)
2. [Install Tools Dasar](#2-install-tools-dasar)
3. [Install & Setup Ollama](#3-install--setup-ollama)
4. [Download Model Qwen 2.5](#4-download-model-qwen-25)
5. [Verifikasi Ollama Berjalan](#5-verifikasi-ollama-berjalan)
6. [Clone & Setup Project](#6-clone--setup-project)
7. [Konfigurasi Backend Laravel](#7-konfigurasi-backend-laravel)
8. [Konfigurasi Frontend Vue.js](#8-konfigurasi-frontend-vuejs)
9. [Integrasi Ollama ke Laravel (.env)](#9-integrasi-ollama-ke-laravel-env)
10. [Jalankan Semua Layanan](#10-jalankan-semua-layanan)
11. [Verifikasi Integrasi AI](#11-verifikasi-integrasi-ai)
12. [Troubleshooting](#12-troubleshooting)
13. [Cheat Sheet Perintah Harian](#13-cheat-sheet-perintah-harian)

---

## 1. Cek Spesifikasi & Prasyarat

Sebelum mulai, pastikan spesifikasi komputer/laptop kamu memenuhi kebutuhan minimum.

### Spesifikasi Minimum untuk Qwen 2.5

| Model | RAM | Storage | GPU (Opsional) | Rekomendasi |
|---|---|---|---|---|
| `qwen2.5:0.5b` | 4 GB | ~1 GB | Tidak perlu | Testing ringan |
| `qwen2.5:1.5b` | 6 GB | ~1.5 GB | Tidak perlu | Dev lokal ringan |
| `qwen2.5:7b` | **8–16 GB** | ~5 GB | 6 GB VRAM | ✅ **Recommended** |
| `qwen2.5:14b` | 16–32 GB | ~9 GB | 10 GB VRAM | Respons lebih pintar |

> 💡 **Tips:** Untuk tugas kuliah, `qwen2.5:7b` sudah lebih dari cukup. Kalau RAM kamu pas-pasan (8 GB), pakai `qwen2.5:1.5b` dulu.

### Tools yang Wajib Terinstall

| Tool | Versi Minimum | Cek dengan |
|---|---|---|
| PHP | 8.2+ | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 18+ | `node -v` |
| NPM | 9+ | `npm -v` |
| Git | terbaru | `git --version` |
| Ollama | terbaru | `ollama -v` |

---

## 2. Install Tools Dasar

### PHP 8.2+

**Windows:**
Download dari [windows.php.net/download](https://windows.php.net/download/) pilih versi **Non Thread Safe (NTS) x64**.
Tambahkan ke PATH sistem.

**macOS:**
```bash
brew install php
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-sqlite3 php8.2-curl unzip -y
```

---

### Composer

**Windows / macOS / Linux:**
```bash
# Download installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Pindah ke PATH (Linux/macOS)
sudo mv composer.phar /usr/local/bin/composer
```

Atau download langsung dari [getcomposer.org](https://getcomposer.org/download/).

---

### Node.js & NPM

Download dari [nodejs.org](https://nodejs.org/) pilih versi **LTS**.

Atau via terminal:
```bash
# macOS
brew install node

# Linux (Ubuntu)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
```

---

### Git

**Windows:** Download dari [git-scm.com](https://git-scm.com/)

**macOS:**
```bash
brew install git
```

**Linux:**
```bash
sudo apt install git -y
```

---

## 3. Install & Setup Ollama

Ollama adalah runtime yang memungkinkan kamu menjalankan LLM secara lokal di komputer sendiri — tanpa internet, tanpa biaya API.

### Install Ollama

**Windows:**
1. Kunjungi [ollama.com](https://ollama.com)
2. Klik **Download for Windows**
3. Jalankan installer `.exe`
4. Ollama otomatis berjalan di background setelah install

**macOS:**
```bash
brew install ollama
```
Atau download dari [ollama.com](https://ollama.com) → **Download for Mac**.

**Linux:**
```bash
curl -fsSL https://ollama.com/install.sh | sh
```

---

### Jalankan Ollama Service

**Windows:** Ollama otomatis berjalan di system tray setelah install. Cek icon-nya di pojok kanan bawah taskbar.

**macOS:**
```bash
ollama serve
```
> Biarkan terminal ini tetap terbuka, atau jalankan sebagai background process.

**Linux:**
```bash
# Jalankan sebagai service (otomatis start saat boot)
sudo systemctl enable ollama
sudo systemctl start ollama

# Atau jalankan manual
ollama serve
```

---

## 4. Download Model Qwen 2.5

Setelah Ollama berjalan, download model Qwen 2.5. Buka terminal **baru** dan jalankan:

### Pilih Salah Satu Sesuai Spesifikasi

```bash
# Pilihan 1: Ringan (RAM 4–6 GB) — respons lebih sederhana
ollama pull qwen2.5:1.5b

# Pilihan 2: Recommended (RAM 8–16 GB) — respons lebih baik
ollama pull qwen2.5:7b

# Pilihan 3: Terbaik (RAM 16 GB+) — respons paling pintar
ollama pull qwen2.5:14b
```

> ⏳ **Proses download** bisa memakan waktu tergantung koneksi internet dan ukuran model. `qwen2.5:7b` sekitar 4–5 GB.

Pantau progress download di terminal. Tunggu sampai selesai 100%.

---

## 5. Verifikasi Ollama Berjalan

Setelah download selesai, tes model berjalan dengan benar:

### Test via Terminal
```bash
ollama run qwen2.5:7b "Halo, kamu siapa?"
```
Jika berhasil, model akan membalas pertanyaan tersebut.

### Test via HTTP (cara yang sama dengan Laravel)
Buka browser atau gunakan `curl`:

```bash
curl http://localhost:11434/api/generate -d '{
  "model": "qwen2.5:7b",
  "prompt": "Halo, kamu siapa?",
  "stream": false
}'
```

**Respons yang diharapkan:**
```json
{
  "model": "qwen2.5:7b",
  "response": "Halo! Saya adalah asisten AI...",
  "done": true
}
```

Kalau muncul respons seperti di atas, berarti **Ollama siap diintegrasikan!** ✅

---

## 6. Clone & Setup Project

```bash
# Clone repository
git clone https://github.com/fienda87/pabw-marketplace-ai.git

# Masuk ke folder project
cd pabw-marketplace-ai
```

---

## 7. Konfigurasi Backend Laravel

### Langkah 1 — Install Dependensi PHP
```bash
composer install
```

### Langkah 2 — Buat File .env
```bash
# Linux / macOS
cp .env.example .env

# Windows (Command Prompt)
copy .env.example .env
```

### Langkah 3 — Generate App Key
```bash
php artisan key:generate
```

### Langkah 4 — Setup Database SQLite

**Linux / macOS:**
```bash
touch database/database.sqlite
```

**Windows:** Buat file baru bernama `database.sqlite` (tanpa ekstensi lain) di dalam folder `database/`.

Pastikan isi `.env` untuk database seperti ini:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

> 💡 Untuk path absolut di Windows contohnya: `C:\xampp\htdocs\pabw-marketplace-ai\database\database.sqlite`
> 
> Untuk Linux/macOS: `/home/username/pabw-marketplace-ai/database/database.sqlite`

Atau cara paling mudah — biarkan Laravel auto-detect dengan hanya menulis:
```env
DB_CONNECTION=sqlite
```
Dan hapus/kosongkan baris `DB_DATABASE`, `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`.

### Langkah 5 — Migrasi & Seed Database
```bash
php artisan migrate --seed
```

Perintah ini akan:
- Membuat semua tabel (users, products, categories, orders, carts, coupons, dll)
- Mengisi data awal: produk gaming gear, kategori, akun admin, dan kupon demo

---

## 8. Konfigurasi Frontend Vue.js

### Install Dependensi JavaScript
```bash
npm install
```

---

## 9. Integrasi Ollama ke Laravel (.env)

Ini bagian **paling penting** agar AI chatbot terhubung ke sistem marketplace.

Buka file `.env` dan tambahkan / pastikan konfigurasi berikut ada:

```env
# ============================================
# KONFIGURASI OLLAMA AI
# ============================================

# URL Ollama (default localhost)
OLLAMA_URL=http://localhost:11434

# Model yang digunakan — sesuaikan dengan yang kamu download
OLLAMA_MODEL=qwen2.5:7b

# Timeout request ke Ollama (detik) — naikkan jika respons lambat
OLLAMA_TIMEOUT=120
```

> ⚠️ **Penting:** Pastikan nama model di `OLLAMA_MODEL` **sama persis** dengan yang kamu pull. 
> Contoh: kalau kamu pull `qwen2.5:1.5b`, maka isi `OLLAMA_MODEL=qwen2.5:1.5b`.

### Cek Konfigurasi di Laravel

Setelah edit `.env`, clear cache konfigurasi:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 10. Jalankan Semua Layanan

Kamu perlu membuka **3 terminal** secara bersamaan:

### Terminal 1 — Ollama (AI Engine)
```bash
ollama serve
```
> Biarkan berjalan terus. Jika sudah berjalan di background (Windows/macOS), lewati ini.

### Terminal 2 — Backend Laravel
```bash
php artisan serve
```
Laravel akan berjalan di: **http://127.0.0.1:8000**

### Terminal 3 — Frontend Vite (Vue.js)
```bash
npm run dev
```
Frontend akan berjalan di: **http://127.0.0.1:5173** (atau port lain yang ditampilkan)

---

### Akun Login Default

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@pabw.com` | `password` |
| **User** | Daftar via Register | — |

### Kupon Demo yang Tersedia
| Kode | Keterangan |
|---|---|
| `GAMER20` | Diskon 20% |
| `50KOFF` | Potongan Rp 50.000 |
| `EXPIRED` | Testing error expired |

---

## 11. Verifikasi Integrasi AI

Setelah semua layanan berjalan, lakukan pengecekan berikut:

### Cek 1 — Health Check Ollama via API Laravel
Buka browser dan akses:
```
http://127.0.0.1:8000/api/health/ollama
```

**Respons sukses:**
```json
{
  "status": "ok",
  "message": "Ollama is reachable"
}
```

**Jika gagal:** Pastikan Ollama sudah berjalan dan URL di `.env` sudah benar.

---

### Cek 2 — Test Chat API via curl / Postman
```bash
curl -X POST http://127.0.0.1:8000/api/chat \
  -H "Content-Type: application/json" \
  -d '{"message": "Rekomendasikan headset gaming untuk saya"}'
```

**Respons sukses:**
```json
{
  "reply": "Tentu! Berdasarkan produk yang tersedia di toko kami, saya merekomendasikan..."
}
```

---

### Cek 3 — Test via UI Langsung
1. Buka browser → `http://127.0.0.1:5173`
2. Klik menu **Chat AI** di sidebar kiri
3. Ketik pesan seperti: *"Headset gaming terbaik di bawah 500 ribu?"*
4. Tunggu beberapa detik — AI akan merespons berdasarkan produk di database kamu

✅ Jika AI merespons dengan rekomendasi produk yang relevan dari database, integrasi **berhasil sempurna!**

---

## 12. Troubleshooting

### ❌ Error: `Connection refused` ke Ollama

**Penyebab:** Ollama belum berjalan.

**Solusi:**
```bash
# Jalankan Ollama
ollama serve

# Atau cek apakah sudah berjalan
curl http://localhost:11434
```

---

### ❌ Error: `model not found`

**Penyebab:** Model yang ditulis di `.env` belum di-pull atau nama salah.

**Solusi:**
```bash
# Cek model yang sudah terinstall
ollama list

# Pull ulang model
ollama pull qwen2.5:7b
```

Pastikan nama di `OLLAMA_MODEL` di `.env` **sama persis** dengan output `ollama list`.

---

### ❌ Error: `php artisan migrate` gagal

**Penyebab:** File `database.sqlite` belum ada.

**Solusi:**
```bash
# Linux/macOS
touch database/database.sqlite

# Lalu jalankan ulang
php artisan migrate --seed
```

---

### ❌ Respons AI sangat lambat

**Penyebab:** Model terlalu besar untuk spesifikasi komputer.

**Solusi:** Ganti ke model yang lebih kecil:
```env
# Di file .env
OLLAMA_MODEL=qwen2.5:1.5b
```
Lalu pull model tersebut jika belum:
```bash
ollama pull qwen2.5:1.5b
php artisan config:clear
```

---

### ❌ Frontend tidak bisa akses API Laravel (CORS error)

**Penyebab:** Konfigurasi CORS Laravel belum mengizinkan origin Vite.

**Solusi:** Pastikan di `config/cors.php`:
```php
'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],
```

Atau set `'allowed_origins' => ['*']` untuk development lokal.

---

### ❌ `npm run dev` error

**Penyebab:** Dependensi belum terinstall atau versi Node.js terlalu lama.

**Solusi:**
```bash
# Hapus node_modules dan install ulang
rm -rf node_modules package-lock.json
npm install
npm run dev
```

---

## 13. Cheat Sheet Perintah Harian

Simpan ini untuk referensi sehari-hari saat development:

```bash
# ==========================================
# MEMULAI DEVELOPMENT (jalankan setiap hari)
# ==========================================

# 1. Pastikan Ollama jalan (jika belum otomatis)
ollama serve

# 2. Backend Laravel
php artisan serve

# 3. Frontend Vue.js
npm run dev

# ==========================================
# UTILITAS LARAVEL
# ==========================================

# Reset database dari awal
php artisan migrate:fresh --seed

# Clear semua cache
php artisan optimize:clear

# Lihat semua route API
php artisan route:list --path=api

# ==========================================
# UTILITAS OLLAMA
# ==========================================

# Lihat model yang terinstall
ollama list

# Test model langsung di terminal
ollama run qwen2.5:7b

# Hapus model (hemat storage)
ollama rm qwen2.5:7b

# Cek status Ollama
curl http://localhost:11434
```

---

## ✅ Checklist Final

Centang semua sebelum demo atau presentasi:

- [ ] Ollama berjalan (`curl http://localhost:11434` → OK)
- [ ] Model Qwen 2.5 sudah ter-download (`ollama list`)
- [ ] `OLLAMA_MODEL` di `.env` sesuai nama model yang diinstall
- [ ] `php artisan migrate --seed` berhasil
- [ ] `php artisan serve` berjalan di port 8000
- [ ] `npm run dev` berjalan di port 5173
- [ ] `/api/health/ollama` mengembalikan status `ok`
- [ ] Chat AI di frontend merespons dengan benar
- [ ] Login admin berhasil (`admin@pabw.com` / `password`)

---

> 📌 **Dikembangkan untuk:** Tugas Mata Kuliah PABW  
> 🔗 **Repository:** https://github.com/fienda87/pabw-marketplace-ai  
> 🤖 **AI Engine:** Ollama + Qwen 2.5 (Local LLM)
