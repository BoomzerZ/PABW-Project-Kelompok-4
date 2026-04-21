# Panduan Instalasi Projek PABW Marketplace AI

Projek ini adalah marketplace *gaming gear* yang terintegrasi dengan AI Chatbot lokal (Ollama + Qwen 2.5). Berikut adalah langkah-langkah lengkap untuk menjalankan projek ini di komputer lokal Anda.

---

## 1. Prasyarat (*Prerequisites*)

Pastikan komputer Anda sudah terinstal *software* berikut:
* **PHP** (minimal versi 8.2)
* **Composer** (untuk dependensi Laravel)
* **Node.js & NPM** (untuk dependensi Vue.js/Vite)
* **Git**
* **Ollama** (untuk menjalankan model AI secara lokal)
* **SQLite** (biasanya sudah termasuk dalam instalasi PHP)

---

## 2. Setup AI (Ollama)

Projek ini menggunakan model AI Qwen 2.5 secara lokal.

1.  Download dan instal Ollama dari [ollama.com](https://ollama.com).
2.  Buka terminal atau *command prompt*, lalu jalankan perintah berikut untuk mengunduh model Qwen 2.5:
    ```bash
    ollama pull qwen2.5
    ```
3.  Pastikan Ollama berjalan di *background* (biasanya dapat diakses melalui `http://localhost:11434`).

---

## 3. Setup Projek (Backend & Frontend)

### Langkah 1: Clone Repositori
Buka terminal dan jalankan perintah berikut:
```bash
git clone [https://github.com/fienda87/pabw-marketplace-ai.git](https://github.com/fienda87/pabw-marketplace-ai.git)
cd pabw-marketplace-ai
