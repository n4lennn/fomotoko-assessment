# Fomotoko Assessment — Fullstack Developer

## Task 1: Online Store API

REST API untuk simulasi online store dengan fitur flash sale dan penanganan race condition.

### Tech Stack
- PHP / Laravel 11
- MySQL

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Sesuaikan konfigurasi database di file .env
php artisan migrate --seed
php artisan serve
```

### API Endpoints

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | /api/products | Ambil semua produk |
| GET | /api/products/{id} | Ambil detail produk |
| POST | /api/products | Tambah produk baru |
| PATCH | /api/products/{id} | Update produk |
| GET | /api/orders | Ambil semua order |
| GET | /api/orders/{id} | Ambil detail order |
| POST | /api/orders | Buat order baru |

### Cara Kerja Race Condition Handling

Saat flash sale, banyak pembeli bisa memesan produk yang sama secara bersamaan.
Untuk mencegah stok menjadi negatif, setiap order diproses di dalam **DB Transaction**
dan menggunakan **Pessimistic Locking** (`lockForUpdate`) sehingga hanya satu proses
yang bisa mengubah stok dalam satu waktu.

### Jalankan Test

```bash
php artisan test --filter=FlashSaleRaceConditionTest
```

---

## Task 2: Hidden Item

Program CLI untuk menemukan kemungkinan lokasi item tersembunyi di dalam grid.

Pemain bergerak dari posisi awal X dengan urutan: naik → kanan → turun.
Program akan menampilkan koordinat yang kemungkinan besar menjadi lokasi item,
beserta visualisasi grid-nya.

### Cara Pakai

```bash
php hidden_item.php <A> <B> <C>

# A = jumlah langkah ke atas
# B = jumlah langkah ke kanan  
# C = jumlah langkah ke bawah

# Contoh:
php hidden_item.php 2 3 1
```