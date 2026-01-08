# Presence App — Starterkit Laravel + Vue

Starterkit ini menggabungkan Laravel (backend) dengan Vue (frontend) menggunakan Vite + Inertia. Cocok untuk membangun aplikasi SPA dengan fitur autentikasi, store (Pinia), dan alur pengembangan modern.

**Fitur utama**
- Laravel 10 (API & server-side)
- Inertia + Vue 3 (komponen single-file)
- Vite untuk bundling dan HMR
- Pinia untuk state management (opsional)
- Laravel Fortify untuk autentikasi
- Testing dengan Pest / PHPUnit

**Persyaratan**
- PHP 8.1+
- Composer
- Node.js 18+ dan npm/yarn
- MySQL / Postgres / SQLite

**Instalasi cepat**
1. Clone repo:

```bash
git clone <repo-url>
cd presence_app
```

2. Pasang dependensi PHP dan Node:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

3. Konfigurasi environment (`.env`) — atur koneksi database, mail, dsb.

4. Jalankan migrasi dan seeder (opsional):

```bash
php artisan migrate --seed
```

5. Jalankan server pengembangan:

```bash
# Jalankan backend
php artisan serve

# Jalankan Vite (frontend)
npm run dev
```

Frontend tersedia di http://localhost:5173 (Vite) dan backend di http://127.0.0.1:8000

**Build produksi**

```bash
npm run build
php artisan optimize
```

**Testing**

```bash
php artisan test
# atau
./vendor/bin/pest
```

**Struktur penting**
- `app/Models/User.php` — model user
- `app/Http/Controllers` — controller backend
- `resources/js` — sumber Vue/TS, komponen, pages, routes
- `resources/css` — stylesheet (Tailwind/vanilla)
- `routes/web.php` — route web + Inertia

**Tips & catatan**
- Starterkit menggunakan Fortify untuk otentikasi; cek `app/Actions/Fortify` dan `Providers/FortifyServiceProvider.php`.
- Jika ingin menggunakan Pinia: sudah ditambahkan sebagai dependensi (jalankan `npm install`).

**Kontribusi**
- Silakan buka issue atau kirim pull request.

**Lisensi**
- Lisensi sesuai `composer.json` / `package.json` (periksa file untuk detail).

Jika Anda ingin saya menambahkan instruksi deployment atau CI, beri tahu saya.
