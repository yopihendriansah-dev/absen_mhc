# Implementation Plan

## Sistem Pendaftaran dan Absensi Event MHC

Dokumen ini menjadi panduan eksekusi teknis berdasarkan [PRD](PRD.md).

**Status:** Draft eksekusi  
**Stack:** Laravel 12, Filament 5, Blade, Gmail SMTP, Spatie Media Library  
**Strategi:** Selesaikan fondasi dan satu alur end-to-end terlebih dahulu, lalu lanjutkan fitur pendukung.

---

## 1. Prinsip Implementasi

- Halaman publik dibuat custom menggunakan Blade dan mobile-first.
- Dashboard manajemen dibuat menggunakan Filament 5.
- Setiap data peserta selalu terhubung ke satu event melalui `event_id`.
- Email dikirim satu per satu secara manual oleh admin.
- Email dikirim synchronous melalui Gmail SMTP tanpa queue pada MVP.
- Poster event dikelola menggunakan Spatie Media Library.
- Check-in dilakukan admin yang sudah login melalui halaman custom di luar Filament.
- Data peserta dan kehadiran tidak dihapus otomatis setelah event selesai.
- Setiap fase harus menghasilkan fitur yang dapat diuji sebelum lanjut ke fase berikutnya.

## 2. Urutan Fase

```text
Fase 0  Persiapan dan baseline
   ↓
Fase 1  Package, storage, dan konfigurasi
   ↓
Fase 2  Event dan poster
   ↓
Fase 3  Halaman publik dan pendaftaran
   ↓
Fase 4  Dashboard peserta
   ↓
Fase 5  Email Gmail dan QR Code
   ↓
Fase 6  Custom check-in
   ↓
Fase 7  Statistik, export, dan keamanan
   ↓
Fase 8  Testing, deployment, dan handover
```

---

## 3. Fase 0 — Persiapan dan Baseline

### Tujuan

Memastikan project Laravel 12 dan Filament 5 dapat dijalankan sebelum perubahan domain dimulai.

### Langkah

1. Periksa status project dan perubahan lokal yang sudah ada.
2. Pastikan `.env` tersedia dan `APP_KEY` sudah dibuat.
3. Pastikan database development dapat digunakan.
4. Jalankan migration yang tersedia.
5. Jalankan aplikasi Laravel dan Vite.
6. Pastikan login Filament dapat digunakan.
7. Jalankan test bawaan sebagai baseline.

### Output

- Project dapat dijalankan.
- Admin dapat login.
- Baseline test tercatat.

### Validasi selesai

- Halaman publik dapat dibuka.
- Panel admin dapat dibuka.
- Tidak ada error konfigurasi awal.

---

## 4. Fase 1 — Package, Storage, dan Konfigurasi

### Tujuan

Menyiapkan kebutuhan teknis untuk media, QR Code, email, dan environment.

### Langkah

1. Install package Spatie Laravel Media Library.
2. Install plugin integrasi Spatie Media Library untuk Filament versi yang kompatibel.
3. Jalankan migration package media library.
4. Publish konfigurasi yang diperlukan.
5. Konfigurasi filesystem dan jalankan `storage:link`.
6. Tentukan disk media development sebagai local public storage.
7. Siapkan package QR Code yang kompatibel dengan Laravel 12.
8. Tambahkan konfigurasi environment untuk Gmail SMTP.
9. Pastikan semua kredensial disimpan di `.env`, bukan source code.
10. Tambahkan `.env.example` tanpa nilai rahasia.

### Konfigurasi Gmail yang dibutuhkan

- Alamat Gmail pengirim.
- Gmail SMTP host dan port.
- TLS encryption.
- Gmail App Password.
- Nama pengirim `MHC Community`.

Gmail App Password dibuat setelah Two-Step Verification akun Gmail aktif. Password utama Gmail tidak digunakan oleh aplikasi.

### Output

- Upload media dapat digunakan.
- Storage publik berfungsi.
- QR Code dapat dibuat pada test atau prototype kecil.
- Konfigurasi mail siap diuji.

---

## 5. Fase 2 — Event dan Poster

### Tujuan

Membangun sumber data event yang menjadi induk seluruh pendaftaran.

### Langkah database

1. Buat migration tabel `events`.
2. Tambahkan kolom:
   - `name`
   - `slug` unique
   - `event_date`
   - `start_time`
   - `end_time`
   - `description`
   - `location_name`
   - `location_address`
   - `google_maps_url`
   - `whatsapp_group_url` nullable
   - `capacity_type` dengan nilai `limited` atau `unlimited`
   - `capacity` nullable
   - `status`
   - timestamps
3. Tambahkan index pada `slug`, `event_date`, dan `status` jika diperlukan.
4. Buat model `Event` dan enum atau constant untuk status serta kapasitas.
5. Tambahkan cast tanggal dan waktu.
6. Tambahkan validasi bahwa `capacity` wajib lebih besar dari nol saat tipe kapasitas `limited`.

### Langkah model media

1. Implementasikan kontrak media pada `Event`.
2. Gunakan trait media library.
3. Daftarkan collection `event-posters`.
4. Tambahkan conversion thumbnail jika dibutuhkan.
5. Pastikan media lama dapat diganti tanpa menampilkan poster yang salah.

### Langkah Filament

1. Buat Event Resource.
2. Buat form event dengan section informasi dasar, waktu, lokasi, kapasitas, dan poster.
3. Tambahkan conditional field untuk kapasitas.
4. Buat table event dengan status, tanggal, kapasitas, dan jumlah peserta.
5. Tambahkan action publish, close, complete, dan cancel sesuai kebutuhan.
6. Tambahkan link untuk membuka halaman publik event.

### Validasi selesai

- Admin dapat membuat draft event.
- Admin dapat upload dan mengganti poster.
- Event published memiliki slug unik.
- Event dengan kapasitas limited menolak nilai kosong atau nol.
- Event unlimited tidak membutuhkan nilai kapasitas.

---

## 6. Fase 3 — Halaman Publik dan Pendaftaran

### Tujuan

Membuat alur peserta dari melihat event sampai berhasil mendaftar.

### Langkah database

1. Buat migration tabel `registrations`.
2. Tambahkan kolom:
   - `event_id` foreign key
   - `registration_code` unique
   - `name`
   - `email`
   - `phone`
   - `gender`
   - `city` nullable
   - `organization` nullable
   - `notes` nullable
   - `referral_source` nullable
   - `data_consent_at`
   - `invitation_status`
   - `invitation_sent_at` nullable
   - `invitation_send_count`
   - `last_invitation_error` nullable
   - timestamps
3. Tambahkan unique constraint gabungan `event_id` dan `email`.
4. Tambahkan index untuk pencarian nama, email, kode, dan event.

### Langkah model dan service

1. Buat model `Registration`.
2. Definisikan relasi `Event hasMany Registration`.
3. Buat generator registration code yang aman dan unik.
4. Buat `RegistrationService` untuk proses pendaftaran.
5. Pastikan service memvalidasi kapasitas sebelum menyimpan.
6. Hitung peserta yang sudah terdaftar berdasarkan registration aktif.
7. Tolak pendaftaran jika event closed, cancelled, completed, atau kapasitas penuh.
8. Normalisasi email dan nomor WhatsApp sebelum disimpan.

### Langkah halaman Blade

1. Buat route daftar event menggunakan slug.
2. Buat halaman daftar event.
3. Buat halaman detail event dan form pendaftaran.
4. Gunakan layout mobile-first.
5. Gunakan input email dan telephone yang sesuai mobile.
6. Tambahkan field wajib:
   - Nama lengkap
   - Email
   - Nomor WhatsApp
   - Jenis kelamin: Laki-laki atau Perempuan
   - Persetujuan penggunaan data
7. Tambahkan field opsional:
   - Kota atau domisili
   - Asal komunitas atau instansi
   - Catatan tambahan
   - Sumber informasi event
8. Tampilkan error validasi di dekat field terkait.
9. Tambahkan proteksi CSRF dan rate limiting pada submit form.
10. Setelah berhasil, arahkan ke halaman sukses berdasarkan registration code.

### Halaman sukses

Halaman sukses menampilkan:

- Ucapan terima kasih.
- Nama peserta.
- Nama event.
- Informasi bahwa undangan email dikirim manual oleh admin.
- Ajakan masuk grup WhatsApp.
- Tombol grup WhatsApp jika URL tersedia.
- Pesan alternatif jika event tidak memiliki grup WhatsApp.

### Validasi selesai

- Peserta dapat mendaftar melalui HP.
- Email yang sama ditolak pada event yang sama.
- Email yang sama dapat mendaftar pada event berbeda.
- Jenis kelamin wajib dipilih.
- Event unlimited tidak berhenti karena jumlah peserta.
- Event limited berhenti menerima pendaftaran saat kapasitas tercapai.
- Halaman sukses tampil sesuai kondisi link grup WhatsApp.

---

## 7. Fase 4 — Dashboard Peserta

### Tujuan

Memberikan admin kemampuan melihat dan mengelola pendaftaran tanpa mengubah alur publik.

### Langkah

1. Buat Registration Resource Filament.
2. Tampilkan nama, email, WhatsApp, jenis kelamin, event, status undangan, dan status hadir.
3. Tambahkan filter event.
4. Tambahkan filter status undangan.
5. Tambahkan pencarian nama, email, nomor WhatsApp, dan kode registrasi.
6. Buat halaman detail registration.
7. Tambahkan action kirim undangan.
8. Tambahkan action kirim ulang undangan.
9. Tampilkan riwayat ringkas pengiriman email.
10. Jangan menyediakan action pembatalan mandiri untuk peserta.

### Validasi selesai

- Admin dapat melihat peserta berdasarkan event.
- Admin dapat melihat informasi peserta secara lengkap.
- Peserta dari event berbeda tidak tercampur.
- Action email hanya tersedia untuk admin yang memiliki akses.

---

## 8. Fase 5 — Gmail SMTP dan QR Code

### Tujuan

Mengirim undangan satu per satu dengan QR Code yang terhubung ke peserta dan event.

### Langkah email

1. Konfigurasi Laravel Mail menggunakan Gmail SMTP.
2. Buat Mail class `EventInvitationMail`.
3. Buat template email Blade responsive.
4. Sertakan nama peserta dan detail event.
5. Sertakan tanggal, waktu, lokasi, dan Google Maps.
6. Sertakan link grup WhatsApp jika tersedia.
7. Sertakan QR Code.
8. Tampilkan kode registrasi sebagai fallback.
9. Buat service `InvitationService`.
10. Kirim email secara synchronous saat action admin dipanggil.
11. Tangkap exception pengiriman.
12. Simpan status `sent` atau `failed` serta pesan error.
13. Tambah satu ke `invitation_send_count` setiap percobaan.
14. Tambahkan loading state dan confirmation pada tombol Filament.

### Langkah QR Code

1. Buat token atau gunakan `registration_code` yang aman.
2. Jangan memasukkan data pribadi langsung ke QR Code.
3. Buat QR Code dari token tersebut.
4. Pastikan token dapat dicari ke satu registration.
5. Uji QR Code pada beberapa perangkat mobile.

### Validasi selesai

- Admin dapat mengirim undangan satu peserta.
- Email masuk ke inbox test Gmail.
- QR Code terbaca dan mengarah ke token valid.
- Link WhatsApp tampil jika tersedia.
- Pengiriman gagal menghasilkan notifikasi dan status `failed`.
- Admin dapat mengirim ulang.
- Tidak ada queue worker yang dibutuhkan untuk MVP.

---

## 9. Fase 6 — Custom Check-in

### Tujuan

Menyediakan halaman scan QR Code yang mobile-first, terpisah dari UI Filament, tetapi tetap terlindungi oleh login admin.

### Langkah database

1. Buat migration tabel `attendances`.
2. Tambahkan kolom:
   - `registration_id` unique
   - `checked_in_by` foreign key ke users
   - `checked_in_at`
   - `check_in_method`
   - `notes` nullable
   - timestamps
3. Buat model `Attendance` dan relasinya.

### Langkah halaman custom

1. Buat route check-in yang memakai middleware autentikasi admin.
2. Buat layout Blade custom mobile-first.
3. Tambahkan akses kamera melalui library scanner QR yang sesuai.
4. Sediakan input manual kode registrasi.
5. Kirim token ke endpoint check-in.
6. Validasi registration dan event.
7. Tolak registration yang tidak valid.
8. Tolak check-in ganda.
9. Simpan waktu check-in dan user admin.
10. Tampilkan hasil sukses atau error dengan jelas.
11. Tambahkan link dari dashboard Filament menuju halaman custom.

### Fallback manual

Admin dapat mencari peserta berdasarkan:

- Nama.
- Email.
- Nomor WhatsApp.
- Registration code.

### Validasi selesai

- Pengguna yang belum login tidak dapat membuka halaman check-in.
- Admin dapat memindai QR Code dari HP.
- QR Code valid membuat attendance.
- QR Code yang sudah digunakan ditolak.
- Token invalid menampilkan error.
- Check-in manual berfungsi.
- Halaman tidak bergantung pada tampilan Filament.

---

## 10. Fase 7 — Statistik, Export, dan Keamanan

### Statistik dashboard

Tambahkan widget atau query untuk menampilkan:

- Total event.
- Total peserta per event.
- Total undangan terkirim.
- Total peserta hadir.
- Total peserta belum hadir.
- Kapasitas tersisa untuk event limited.

### Export

1. Tambahkan export peserta berdasarkan event.
2. Tambahkan export kehadiran berdasarkan event.
3. Sertakan kolom waktu check-in dan admin check-in.
4. Pastikan export hanya dapat dilakukan admin berwenang.

### Keamanan dan privasi

1. Terapkan authorization pada Resource dan action Filament.
2. Gunakan CSRF pada form publik.
3. Tambahkan rate limiting pada submit pendaftaran dan endpoint check-in.
4. Jangan memasukkan email atau nomor WhatsApp ke QR Code.
5. Validasi URL Google Maps dan WhatsApp.
6. Validasi ukuran serta mime type poster.
7. Jangan menyimpan kredensial Gmail di repository.
8. Simpan data peserta sebagai histori sesuai keputusan produk.
9. Sediakan mekanisme backup database dan media.

---

## 11. Fase 8 — Testing

### Unit test

- Pembuatan slug event.
- Pembuatan registration code.
- Validasi kapasitas event.
- Event unlimited tidak membatasi pendaftaran.
- Email duplikat pada event yang sama ditolak.
- Email yang sama pada event berbeda diterima.
- Jenis kelamin wajib dan hanya menerima dua nilai.
- Pembuatan token QR Code.

### Feature test

- Event published dapat dibuka melalui slug.
- Event draft tidak tersedia untuk pendaftaran publik.
- Form pendaftaran berhasil disimpan.
- Halaman sukses menampilkan ajakan WhatsApp bila URL tersedia.
- Halaman sukses tidak menampilkan tombol WhatsApp bila URL kosong.
- Event penuh menolak pendaftaran.
- Admin dapat memicu pengiriman undangan.
- Status email berubah sesuai hasil pengiriman.
- Check-in hanya bisa dilakukan user login.
- Check-in valid berhasil dibuat.
- Check-in ganda ditolak.
- Check-in manual berhasil.

### Manual QA

- Uji tampilan pada mobile kecil dan desktop.
- Uji form dengan koneksi lambat.
- Uji email Gmail pada inbox dan spam.
- Uji link Google Maps dan WhatsApp.
- Uji kamera pada Android dan iPhone jika tersedia.
- Uji poster dengan format dan ukuran berbeda.
- Uji event limited, unlimited, closed, completed, dan cancelled.

---

## 12. Checklist Deployment

### Environment

- `APP_ENV` dan `APP_DEBUG` sesuai environment.
- `APP_URL` sudah benar.
- Database production tersedia.
- `APP_KEY` aman.
- Konfigurasi Gmail SMTP sudah diisi menggunakan App Password.
- Storage link sudah dibuat.
- Disk media production sudah ditentukan.

### Database dan file

- Jalankan migration production.
- Pastikan folder media memiliki permission yang benar.
- Atur backup database.
- Atur backup media.
- Pastikan log aplikasi dapat dipantau.

### Smoke test production

1. Login admin.
2. Buat event test.
3. Upload poster.
4. Buka halaman pendaftaran dari HP.
5. Daftar dengan email test.
6. Kirim undangan.
7. Scan QR Code.
8. Pastikan attendance tersimpan.
9. Hapus atau tandai data test sesuai kebijakan sebelum event berjalan.

---

## 13. Definition of Done

Implementasi dianggap selesai apabila:

- Semua fitur dalam scope MVP pada PRD tersedia.
- Halaman publik menggunakan Blade dan mobile-first.
- Event dapat memiliki kapasitas limited atau unlimited.
- Poster dikelola menggunakan Spatie Media Library.
- Peserta hanya dapat mendaftar sekali pada event yang sama.
- Peserta menerima ucapan terima kasih dan ajakan WhatsApp setelah mendaftar jika link tersedia.
- Admin dapat mengirim email undangan satu per satu melalui Gmail SMTP.
- Email berisi QR Code dan link WhatsApp bila tersedia.
- Check-in berjalan pada halaman custom yang hanya dapat diakses admin login.
- Data peserta dan attendance tersimpan sebagai histori.
- Test utama lulus.
- Dokumentasi konfigurasi lokal dan production tersedia.
