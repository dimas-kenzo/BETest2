🧮 Laravel Transaction API — Backend Developer Test
📌 Deskripsi

Proyek ini adalah RESTful API sederhana menggunakan Laravel, yang berfungsi untuk mengelola data transaksi pembelian dan penjualan barang.
API ini mencakup operasi CRUD (Create, Read, Update, Delete), validasi stok agar tidak minus, serta sample data untuk pengujian awal.

⚙️ Fitur Utama

Tambah Transaksi (POST /api/transactions)
Menambahkan data transaksi baru (Pembelian atau Penjualan).
Validasi: stok tidak boleh minus untuk transaksi Penjualan.

Update Transaksi (PUT /api/transactions/{id})
Mengubah data transaksi dan memperbarui stok secara otomatis.

Hapus Transaksi (DELETE /api/transactions/{id})
Menghapus transaksi dan mengembalikan efeknya terhadap stok.
Validasi: stok tidak boleh minus setelah penghapusan.

List Transaksi (GET /api/transactions)
Menampilkan seluruh data transaksi berdasarkan urutan tanggal.

Seeder Data Awal
Berisi 3 sample data: data awal, data sisipan, dan data akhir.


🚀 Cara Menjalankan Project

1️⃣ Clone Repository
git clone <url-repo-anda>
cd BETest

2️⃣ Install Dependency
composer install

3️⃣ Buat file .env

Salin dari .env.example

cp .env.example .env


Kemudian sesuaikan koneksi database:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=betest_db
DB_USERNAME=root
DB_PASSWORD=

4️⃣ Generate key
php artisan key:generate

5️⃣ Jalankan Migrasi & Seeder
php artisan migrate:fresh --seed


Seeder akan membuat:
Data stok awal (stock.qty = 25)
3 data transaksi:
Pembelian (qty 10)
Penjualan (qty 5)
Pembelian (qty 20)

6️⃣ Jalankan Server
php artisan serve


Akses API di:
👉 http://127.0.0.1:8000/api/transactions

🧠 Contoh Penggunaan API
🔹 GET /api/transactions

Response:

[
  {
    "id": 1,
    "date": "2024-10-01",
    "qty": 10,
    "price": 100,
    "type": "Pembelian",
    "description": "Data awal"
  },
  ...
]

🔹 POST /api/transactions

Request Body:

{
  "date": "2024-10-29",
  "qty": 5,
  "price": 200,
  "type": "Penjualan",
  "description": "Penjualan barang A"
}


Response:

{
  "message": "Transaksi berhasil ditambahkan"
}

🔹 PUT /api/transactions/{id}

Request Body:

{
  "date": "2024-10-30",
  "qty": 8,
  "price": 250,
  "type": "Pembelian",
  "description": "Update data transaksi"
}


Response:

{
  "message": "Transaksi berhasil diperbarui"
}

🔹 DELETE /api/transactions/{id}

Response:

{
  "message": "Transaksi berhasil dihapus"
}

🔐 Validasi
Kondisi	Aksi
qty <= 0	Ditolak
type bukan Pembelian atau Penjualan	Ditolak
stok < qty pada Penjualan	Ditolak
Stok hasil operasi negatif	Ditolak

📂 Struktur Folder Utama
app/
 ├── Http/
 │    ├── Controllers/
 │    │    └── TransactionController.php
 │    └── Requests/
 │         └── TransactionRequest.php
 ├── Models/
 │    └── Transaction.php
database/
 ├── migrations/
 └── seeders/
      └── TransactionSeeder.php
routes/
 └── api.php

💬 Catatan Akhir

Semua operasi menggunakan transaksi database (DB::transaction) untuk menjaga konsistensi stok.

API ini mendemonstrasikan penguasaan Eloquent ORM, Form Request Validation, dan Database Transaction di Laravel.

✨ Dibuat oleh

Dimas Kenzo - Backend Developer Test
