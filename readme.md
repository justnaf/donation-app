# Dokumentasi Aplikasi Donasi

## 1. Pendahuluan

Selamat datang di dokumentasi Aplikasi Donasi. Proyek ini dibangun sebagai platform penggalangan dana berbasis web yang modern, aman, dan mudah digunakan. Aplikasi ini memungkinkan admin untuk membuat dan mengelola berbagai program donasi, sementara publik (donatur) dapat melihat program dan memberikan donasi secara online melalui berbagai metode pembayaran.

**Teknologi Utama:**

- **Backend:** Laravel 12
- **Frontend:** Vue 3 (Composition API) & Inertia.js
- **Styling:** Tailwind CSS (dengan komponen UI terinspirasi dari shadcn-vue)
- **Otentikasi:** Laravel Breeze (dimodifikasi untuk login dengan username/email)
- **Hak Akses:** `spatie/laravel-permission`
- **Payment Gateway:** Midtrans

---

## 2. Instalasi & Setup

Berikut adalah langkah-langkah untuk menjalankan proyek ini di lingkungan lokal Anda.

1.  **Clone Repository**

    ```bash
    git clone [URL_REPOSITORY_ANDA]
    cd nama-proyek
    ```

2.  **Install Dependensi Backend**

    ```bash
    composer install
    ```

3.  **Konfigurasi Lingkungan**
    Salin file `.env.example` menjadi `.env`.

    ```bash
    cp .env.example .env
    ```

    Buat kunci aplikasi baru.

    ```bash
    php artisan key:generate
    ```

4.  **Edit File `.env`**
    Buka file `.env` dan konfigurasikan variabel berikut:

    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=root
    DB_PASSWORD=

    # Konfigurasi Kunci Midtrans (Gunakan kunci Sandbox untuk development)
    MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
    MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
    MIDTRANS_IS_PRODUCTION=false
    ```

5.  **Install Dependensi Frontend**

    ```bash
    npm install
    ```

6.  **Jalankan Migrasi & Seeder**
    Perintah ini akan membuat semua tabel database dan mengisi data awal (role, user admin, dll).

    ```bash
    php artisan migrate:fresh --seed
    ```

7.  **Jalankan Server Development**
    Buka dua terminal terpisah:

    - Untuk menjalankan server Vite (frontend): `npm run dev`
    - Untuk menjalankan server Laravel (backend): `php artisan serve`

8.  **Akses Aplikasi**
    Buka `http://localhost:8000` di browser Anda. Anda bisa login menggunakan akun admin yang telah dibuat oleh seeder:
    - **Login:** `admin` atau `admin@donasi.app`
    - **Password:** `password`

---

## 3. Struktur Database & Model

Berikut adalah penjelasan untuk setiap tabel utama dan model Eloquent yang digunakan.

#### `users` (Model: `User`)

- Menyimpan data pengguna, baik admin maupun donatur.
- **Kolom Penting**: `name`, `username` (unik), `email` (unik), `password`, `avatar` (path file).
- **Fitur Khusus**: Menggunakan `HasRoles` dari Spatie. Memiliki accessor `avatar_url` untuk menghasilkan URL avatar yang siap pakai.

#### `donation_categories` (Model: `DonationCategory`)

- Menyimpan kategori untuk program donasi (Pendidikan, Kemanusiaan, dll).
- **Kolom Penting**: `name`, `slug`, `description`.

#### `donation_programs` (Model: `DonationProgram`)

- Tabel inti yang menyimpan semua detail kampanye/program donasi.
- **Kolom Penting**: `donation_category_id` (relasi), `name`, `slug`, `poster_path`, `target_amount`, `short_description`, `content`, `start_date`, `end_date`, `status`.
- **Fitur Khusus**:
    - Menggunakan `SoftDeletes` untuk keamanan data.
    - `getRouteKeyName()` diatur ke `slug` untuk Route Model Binding.
    - `$appends` berisi `poster_url`, `collected_amount`, `progress_percentage` agar properti virtual ini selalu dikirim ke frontend.

#### `donations` (Model: `Donation`)

- Mencatat setiap transaksi donasi yang masuk.
- **Kolom Penting**: `order_id` (unik), `donation_program_id`, `user_id` (nullable), `donator_name`, `donator_email`, `amount`, `fee` (biaya transaksi), `message` (nullable), `is_anonymous`, `status` (`pending`, `paid`, `failed`), `payment_method`, `payment_details` (JSON).
- **Fitur Khusus**: Relasi `user_id` diatur `onDelete('set null')` untuk menjaga integritas data jika pengguna menghapus akunnya.

#### `program_updates` (Model: `ProgramUpdate`)

- Menyimpan catatan perkembangan atau "kabar terbaru" dari sebuah program.
- **Kolom Penting**: `donation_program_id`, `title`, `content`, `published_at`.

#### `fund_disbursements` (Model: `FundDisbursement`)

- Mencatat setiap pencairan dana yang dilakukan oleh admin untuk sebuah program.
- **Kolom Penting**: `donation_program_id`, `amount`, `description`, `disbursed_at`.

---

## 4. Sistem Peran & Izin (Spatie)

- **Roles**:
    1.  `admin`: Memiliki akses penuh ke semua fitur manajemen di dasbor admin.
    2.  `donatur`: Peran default untuk semua pengguna yang mendaftar.
- **Permissions**:
    - `manage programs`: CRUD untuk program donasi.
    - `manage donations`: Melihat semua transaksi.
    - `manage updates`: Mengelola kabar terbaru.
    - `manage disbursements`: Mengelola pencairan dana.
    - `manage users`: Mengelola pengguna (belum diimplementasikan sepenuhnya).
- Semua _permission_ di atas secara default diberikan ke _role_ `admin` melalui `RolesAndPermissionsSeeder`.

---

## 5. Alur Kerja & Fitur Utama

#### Alur Kerja Admin

1.  **Login**: Admin login melalui `/login` dan diarahkan ke `/admin/dashboard`.
2.  **Manajemen**: Dari dasbor admin, admin dapat mengakses:
    - **Kelola Program**: Melihat semua program dalam tabel, menambah, mengedit, dan menghapus (soft delete).
    - **Kelola Kategori**: CRUD untuk kategori donasi.
    - **Kelola Kabar Terbaru & Pencairan Dana**: Dari halaman edit program, admin dapat melihat daftar, menambah, mengedit, dan menghapus update perkembangan dan catatan pencairan dana.
    - **Lihat Donasi**: Dari daftar program, admin bisa melihat daftar transaksi donasi untuk setiap program, lengkap dengan filter.

#### Alur Kerja Donatur (Publik)

1.  **Melihat Program**: Pengguna mengunjungi halaman utama atau halaman `/programs` untuk melihat daftar program donasi yang aktif. Mereka bisa memfilter berdasarkan kategori.
2.  **Detail Program**: Pengguna mengklik sebuah program untuk melihat detailnya, termasuk cerita lengkap, kabar terbaru, penggunaan dana, dan pesan dari donatur lain.
3.  **Proses Donasi**:
    - Pengguna mengisi form donasi di sidebar (jumlah, nama, email, metode pembayaran).
    - Rincian biaya transaksi akan muncul secara dinamis.
    - Saat tombol "Lanjutkan Pembayaran" ditekan, `axios.post` mengirim data ke `DonationController@store`.
4.  **Pembayaran**:
    - Backend mengembalikan `redirect_url` dan `order_id`.
    - Frontend membuka `redirect_url` Midtrans di tab baru dan mengarahkan halaman saat ini ke halaman tunggu (`/donasi/status/{order_id}`).
5.  **Konfirmasi**:
    - Setelah pembayaran selesai, Midtrans mengirim _webhook_ ke `/api/midtrans-callback`.
    - `MidtransCallbackController` memverifikasi dan mengubah status donasi menjadi `paid`.
    - Halaman tunggu pengguna akan melakukan _polling_ dan otomatis menampilkan status "Berhasil" saat status di database berubah.

---

## 6. Struktur Frontend (Vue & Inertia)

- **Layouts**:
    - `PublicLayout.vue`: Digunakan oleh semua halaman publik. Berisi header, footer, dan bottom navbar yang responsif.
    - `AppLayout.vue`: Digunakan oleh semua halaman di dasbor admin. Berisi sidebar navigasi admin.
- **Pages**: Setiap halaman memiliki file `.vue` sendiri di dalam `resources/js/Pages`.
- **Partials**: Komponen-komponen yang dipakai ulang (seperti `ProgramForm.vue`, `UpdateForm.vue`, `DonationStatus.vue`) ditempatkan di dalam subdirektori `Partials` untuk menjaga kerapian.
- **Komponen UI**: Komponen dasar seperti `Button`, `Table`, `Select`, dll. ditempatkan di `resources/js/components/ui/` dan terinspirasi dari `shadcn-vue`.
