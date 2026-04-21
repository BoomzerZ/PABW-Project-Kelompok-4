### Langkah 2: Setup Backend (Laravel)
1.  **Instal dependensi PHP:**
    ```bash
    composer install
    ```
2.  **Duplikat file `.env`:**
    ```bash
    cp .env.example .env
    ```
3.  **Generate kunci aplikasi:**
    ```bash
    php artisan key:generate
    ```
4.  **Konfigurasi Database:**
    Secara *default* projek ini menggunakan SQLite. Buka file `.env` dan pastikan pengaturannya seperti ini:
    ```env
    DB_CONNECTION=sqlite
    # Hapus atau kosongkan baris DB_DATABASE lainnya
    ```
5.  **Buat file database kosong:**
    * **Linux/Mac:** Jalankan `touch database/database.sqlite` di terminal.
    * **Windows:** Buat file baru bernama `database.sqlite` secara manual di dalam folder `database`.
6.  **Jalankan Migrasi dan Seeding (Data Awal):**
    ```bash
    php artisan migrate --seed
    ```
    *Perintah ini akan membuat tabel dan mengisi data produk gaming gear awal serta akun Admin.*

### Langkah 3: Setup Frontend (Vue.js)
1.  **Instal dependensi JavaScript:**
    ```bash
    npm install
    ```
2.  **Build atau jalankan compiler aset:**
    ```bash
    npm run dev
    ```

---

## 4. Cara Menjalankan Aplikasi

Anda perlu menjalankan dua perintah di bawah ini pada **dua terminal yang berbeda**:

**Terminal 1 (Backend - Laravel):**
```bash
php artisan serve

##
Aplikasi akan berjalan di http://127.0.0.1:8000.

Terminal 2 (Frontend - Vite):

Bash
npm run dev
5. Akun Akses (Default)
Setelah aplikasi berhasil dijalankan, Anda bisa login menggunakan akun berikut:

Akun Admin:

Email: admin@pabw.com

Password: password

Akun User Biasa:

Silakan buat akun baru dengan mendaftar melalui halaman Register.

6. Fitur AI Chat
Pastikan aplikasi Ollama sedang berjalan di komputer Anda.

Anda bisa langsung menuju menu Chat AI di sidebar sebelah kiri untuk berkonsultasi mengenai gaming gear.

AI akan memberikan rekomendasi berdasarkan stok produk yang ada di database Anda.

Catatan: Jika ada kendala koneksi ke Ollama, pastikan URL di file .env sudah benar atau biarkan default mengarah ke localhost.
