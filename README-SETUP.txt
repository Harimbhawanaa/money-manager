MONEY MANAGER (Laravel) - PAKET LENGKAP TAHAP 2-10

ISI: migration, model, factory, seeder, routes, controller, view Blade, Blade component,
     JS (chart + kalkulator). Tinggal timpa ke project Laravel hasil tahap 1.

CARA PAKAI
1. Extract zip, copy SEMUA folder (app, database, resources, routes) ke ROOT project money-manager
   (sejajar composer.json). Pilih merge / replace saat ditanya.
   File bawaan yang akan ditimpa: routes/web.php, database/seeders/DatabaseSeeder.php, resources/js/app.js
2. Install Chart.js:
      npm install chart.js
3. Buat tabel + isi data dummy (kategori default + transaksi 12 bulan + budget bulan ini):
      php artisan migrate:fresh --seed
4. Jalankan:
      composer run dev
   Buka http://localhost:8000

HALAMAN
   /              Dashboard (ringkasan bulan ini, peringatan budget, transaksi terbaru)
   /transactions  Record: total pemasukan, pengeluaran, sisa uang / MINUS, filter bulan-tahun-kategori-tipe, CRUD
   /chart         Chart bulanan/tahunan (bar pemasukan vs pengeluaran + doughnut per kategori)
   /budgets       Budget bulanan per kategori + progress bar
   /categories    Kelola kategori
   Kalkulator     Tombol di pojok kanan bawah semua halaman ("Pakai hasil" mengisi kolom nominal)

PEMETAAN MATERI
   Instalasi/Struktur Folder : seluruh struktur project
   Routing                   : routes/web.php
   Controller                : app/Http/Controllers/*
   Views / View Data / Mengirim data ke view : resources/views/*, compact() di controller
   Assets                    : resources/js/app.js, calculator.js (Vite + Chart.js)
   Tailwind                  : semua class Tailwind di view
   Blade Component           : resources/views/components/*
   Model / Eloquent ORM      : app/Models/* (relasi hasMany/belongsTo, scope query di controller)
   Database dan Migration    : database/migrations/*
   Model Factories           : database/factories/*, dipakai di DatabaseSeeder

CATATAN
- Kalau error "Vite manifest not found": pastikan npm run dev jalan (composer run dev sudah menjalankannya).
- Kalau sebelumnya sudah sempat bikin model/migration sendiri, hapus dulu migration lama yang bentrok
  (categories/transactions/budgets), lalu jalankan migrate:fresh --seed.
- Reset data kapan saja: php artisan migrate:fresh --seed
