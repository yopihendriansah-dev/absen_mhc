# Product Requirements Document (PRD)

## Sistem Pendaftaran dan Absensi Event Metal Health Community (MHC)

**Status:** Draft untuk validasi  
**Versi:** 0.1  
**Platform:** Web  
**Teknologi rencana:** Laravel 12, Filament 5, Blade, Spatie Media Library, Gmail SMTP  

---

## 1. Ringkasan Produk

Aplikasi ini digunakan oleh Metal Health Community (MHC) untuk mengelola pendaftaran peserta, pengiriman undangan, dan absensi pada berbagai event komunitas.

Admin dapat membuat banyak event, mengelola data peserta pada masing-masing event, mengirim undangan melalui Gmail secara manual, serta mencatat kehadiran peserta menggunakan QR Code.

Satu aplikasi dapat digunakan berulang kali untuk event yang berbeda tanpa mencampur data antar-event.

## 2. Latar Belakang

Proses pendaftaran dan absensi event komunitas perlu dikelola dalam satu sistem agar:

- Peserta dapat mendaftar melalui halaman web yang lebih rapi daripada formulir manual.
- Admin memiliki daftar peserta yang terpusat.
- Undangan dan QR Code dapat dikirim secara terkontrol oleh admin.
- Kehadiran peserta dapat dicatat dengan cepat di lokasi acara.
- Data pendaftaran dan kehadiran dapat digunakan sebagai arsip serta laporan.

## 3. Tujuan Produk

### Tujuan utama

- Menyediakan halaman pendaftaran publik untuk setiap event.
- Menyediakan dashboard admin untuk mengelola event dan peserta.
- Mengirim undangan berisi QR Code melalui email.
- Mencatat kehadiran peserta saat event berlangsung.
- Mendukung banyak event dengan data yang terpisah.

### Indikator keberhasilan MVP

- Admin dapat membuat dan menerbitkan event.
- Peserta dapat menyelesaikan pendaftaran melalui halaman publik.
- Admin dapat mengirim undangan ke peserta tertentu.
- Peserta menerima email dengan informasi event dan QR Code.
- Petugas dapat memvalidasi QR Code dan mencatat check-in.
- Admin dapat melihat serta mengekspor daftar peserta dan kehadiran.

## 4. Target Pengguna

### Peserta

Peserta event yang membutuhkan proses pendaftaran sederhana dan menerima undangan digital.

### Admin MHC

Pengelola event yang membuat event, mengelola peserta, dan mengirim undangan.

### Petugas check-in

Admin atau anggota panitia yang memvalidasi QR Code di lokasi acara.

Untuk MVP, admin dan petugas check-in dapat menggunakan akun dashboard yang sama.

## 5. Ruang Lingkup MVP

### Termasuk

- Manajemen banyak event.
- Upload poster event menggunakan Spatie Media Library.
- Halaman publik daftar event.
- Halaman detail dan pendaftaran event.
- Penyimpanan data peserta.
- Pencegahan pendaftaran ganda pada event yang sama.
- Pengiriman email undangan secara manual.
- QR Code unik untuk setiap pendaftaran.
- Check-in menggunakan QR Code.
- Check-in manual sebagai cadangan.
- Dashboard statistik dasar.
- Export data peserta dan kehadiran.
- Status event dan status pengiriman email.

### Tidak termasuk dalam MVP

- Pengiriman email menggunakan queue.
- Notifikasi WhatsApp otomatis.
- Pembayaran tiket.
- Login atau akun khusus peserta.
- Sertifikat otomatis.
- Integrasi Google Calendar.
- Multi-tenant atau organisasi MHC yang kompleks.
- Pengumpulan informasi diagnosis atau riwayat kesehatan mental.

## 6. User Flow

### 6.1 Alur admin membuat event

1. Admin login ke dashboard.
2. Admin membuka menu Event.
3. Admin membuat event baru.
4. Admin mengisi nama, tanggal, waktu, deskripsi, lokasi, dan link Google Maps.
5. Admin mengunggah poster event.
6. Admin menyimpan event sebagai draft atau menerbitkannya.
7. Sistem menyediakan link pendaftaran berdasarkan slug event.

### 6.2 Alur peserta mendaftar

1. Peserta membuka link event.
2. Peserta melihat poster dan informasi event.
3. Peserta mengisi formulir pendaftaran.
4. Sistem memvalidasi data.
5. Sistem memeriksa apakah email sudah terdaftar pada event yang sama.
6. Sistem menyimpan data pendaftaran.
7. Sistem menampilkan halaman berhasil mendaftar dengan ucapan terima kasih.
8. Jika event memiliki link grup WhatsApp, sistem menampilkan ajakan dan tombol untuk bergabung ke grup tersebut.
9. Peserta menunggu undangan dikirim oleh admin.

### 6.3 Alur admin mengirim undangan

1. Admin membuka daftar peserta.
2. Admin memilih peserta.
3. Admin menekan tombol Kirim Undangan.
4. Sistem membuat atau mengambil QR Code peserta.
5. Sistem mengirim email melalui Gmail SMTP.
6. Sistem menyimpan status pengiriman.
7. Admin dapat mengirim ulang jika email gagal atau diperlukan.

### 6.4 Alur check-in

1. Petugas membuka halaman check-in.
2. Petugas memindai QR Code peserta.
3. Sistem memvalidasi kode registrasi.
4. Sistem memastikan peserta terdaftar pada event yang sesuai.
5. Sistem memastikan peserta belum check-in.
6. Sistem menyimpan waktu dan petugas check-in.
7. Sistem menampilkan hasil check-in.

## 7. Kebutuhan Fungsional

### FR-01 — Manajemen event

Admin harus dapat membuat, melihat, mengubah, menerbitkan, menutup, dan menghapus event sesuai kebutuhan operasional.

Data event:

- Nama event.
- Slug.
- Tanggal event.
- Jam mulai.
- Jam selesai.
- Deskripsi.
- Nama lokasi.
- Alamat atau keterangan lokasi.
- Link Google Maps.
- Link grup WhatsApp event (opsional).
- Poster event.
- Kapasitas maksimal peserta atau unlimited.
- Status event.

### FR-02 — Poster event

- Poster diunggah melalui Filament.
- File dikelola menggunakan Spatie Media Library.
- Format yang diizinkan: JPG, JPEG, PNG, dan WEBP.
- Ukuran file dibatasi oleh validasi upload.
- Satu event memiliki satu poster utama.
- Poster dapat diganti atau dihapus oleh admin.

### FR-03 — Halaman pendaftaran publik

- Halaman dibuat custom menggunakan Blade, terpisah dari dashboard Filament.
- Tampilan menggunakan pendekatan mobile-first.
- Hanya event berstatus published yang dapat diakses untuk pendaftaran.
- Halaman menampilkan poster, deskripsi, tanggal, waktu, lokasi, dan Google Maps.
- Link grup WhatsApp tidak wajib ditampilkan di halaman publik dan minimal tersedia melalui email undangan.
- Halaman memiliki formulir pendaftaran.
- Setelah event ditutup, formulir tidak dapat menerima pendaftaran baru.
- Jika event memiliki kapasitas terbatas, pendaftaran ditutup otomatis ketika kapasitas tercapai.
- Jika event disetel unlimited, pendaftaran tidak dibatasi jumlah peserta.

### FR-04 — Data peserta

Data yang dikumpulkan:

#### Wajib

- Nama lengkap.
- Email.
- Nomor WhatsApp.
- Jenis kelamin: Laki-laki atau Perempuan.
- Persetujuan penggunaan data.

#### Opsional

- Kota atau domisili.
- Asal komunitas atau instansi.
- Catatan tambahan.
- Sumber informasi event.

Sistem tidak mengumpulkan data diagnosis, riwayat medis, NIK, atau data sensitif lain yang tidak diperlukan untuk operasional event.

### FR-05 — Validasi pendaftaran

- Email harus memiliki format valid.
- Field wajib tidak boleh kosong.
- Jenis kelamin wajib dipilih menggunakan pilihan yang tersedia pada form.
- Persetujuan penggunaan data harus dicentang.
- Email yang sama tidak dapat mendaftar dua kali pada event yang sama.
- Peserta tetap dapat mendaftar pada event lain menggunakan email yang sama.
- Peserta tidak dapat membatalkan pendaftaran sendiri.

Validasi event:

- Link grup WhatsApp bersifat opsional.
- Jika diisi, link grup WhatsApp harus memiliki format URL yang valid.

### FR-06 — Pengiriman undangan

- Admin dapat mengirim undangan dari daftar peserta.
- Admin dapat mengirim ulang undangan.
- Pengiriman dilakukan satu per satu oleh admin dari action pada data peserta.
- MVP tidak menyediakan bulk email atau pengiriman massal otomatis.
- Sistem menggunakan akun Gmail yang diintegrasikan ke Laravel Mail melalui Gmail SMTP.
- Kredensial pengiriman menggunakan Gmail App Password, bukan password utama akun Gmail.
- Alamat pengirim menggunakan akun Gmail yang sudah dikonfigurasi oleh admin.
- Pengiriman dilakukan synchronous tanpa queue untuk MVP.
- Email berisi informasi event, lokasi, Google Maps, dan QR Code.
- Jika event memiliki link grup WhatsApp, link tersebut ikut dicantumkan pada email undangan.
- Sistem menyimpan status `pending`, `sent`, atau `failed`.
- Sistem menyimpan waktu pengiriman dan jumlah percobaan pengiriman.
- Error pengiriman disimpan agar dapat ditelusuri admin.

### Konfigurasi Gmail

Laravel akan menggunakan konfigurasi mail pada environment aplikasi. Nilai sensitif tidak boleh ditulis langsung di source code.

Konfigurasi yang dibutuhkan meliputi:

- Mailer: SMTP.
- Host SMTP Gmail.
- Port SMTP Gmail.
- Username akun Gmail pengirim.
- App Password Gmail.
- Encryption TLS.
- Nama pengirim, misalnya `MHC Community`.

Persyaratan akun Gmail:

- Two-Step Verification diaktifkan.
- App Password dibuat khusus untuk aplikasi Laravel.
- App Password disimpan di file environment/server secret.
- Password utama akun Gmail tidak digunakan oleh aplikasi.

Pengiriman email dilakukan ketika admin menekan action kirim pada dashboard. Karena MVP tidak menggunakan queue, admin perlu menunggu hasil pengiriman dan sistem harus menampilkan notifikasi berhasil atau gagal.

### FR-07 — QR Code

- Setiap pendaftaran memiliki kode unik.
- QR Code berisi token atau kode registrasi, bukan data pribadi langsung.
- QR Code dilampirkan atau ditampilkan pada email undangan.
- Kode tidak dapat digunakan untuk check-in lebih dari satu kali.

### FR-08 — Check-in

- Check-in hanya dapat dilakukan oleh admin yang sudah login.
- Check-in dilakukan melalui halaman web custom khusus di luar halaman Filament.
- Halaman custom check-in memiliki fitur pemindaian QR Code menggunakan kamera perangkat.
- Halaman custom check-in tidak dapat diakses oleh pengguna yang belum login.
- Admin dapat membuka halaman check-in dari dashboard Filament.
- Sistem memvalidasi QR Code yang masuk.
- Sistem memeriksa event dan status pendaftaran.
- Sistem mencatat waktu check-in.
- Sistem mencatat user yang melakukan check-in.
- Sistem menolak check-in ganda.
- Admin dapat melakukan check-in manual berdasarkan nama, email, atau kode registrasi.

### FR-09 — Dashboard dan laporan

Admin dapat melihat:

- Total event.
- Total peserta per event.
- Total email terkirim.
- Total peserta yang sudah check-in.
- Daftar peserta yang belum check-in.

Admin dapat mengekspor:

- Daftar peserta berdasarkan event.
- Daftar kehadiran berdasarkan event.

## 8. Rancangan Data dan Relasi

```text
Event
 └── hasMany Registrations

Registration
 ├── belongsTo Event
 └── hasOne Attendance

Attendance
 ├── belongsTo Registration
 └── belongsTo User
```

### Entitas `Event`

- `id`
- `name`
- `slug`
- `event_date`
- `start_time`
- `end_time`
- `description`
- `location_name`
- `location_address`
- `google_maps_url`
- `whatsapp_group_url`
- `capacity_type`
- `capacity`
- `status`
- timestamps

Poster disimpan sebagai media pada collection `event-posters`, bukan sebagai path poster biasa di tabel event.

### Entitas `Registration`

- `id`
- `event_id`
- `registration_code`
- `name`
- `email`
- `phone`
- `gender`
- `city`
- `organization`
- `notes`
- `referral_source`
- `data_consent_at`
- `invitation_status`
- `invitation_sent_at`
- `invitation_send_count`
- `last_invitation_error`
- timestamps

### Entitas `Attendance`

- `id`
- `registration_id`
- `checked_in_by`
- `checked_in_at`
- `check_in_method`
- `notes`
- timestamps

## 9. Status Sistem

### Status event

```text
draft       Event belum ditampilkan kepada publik.
published   Event dapat dilihat dan menerima pendaftaran.
closed      Pendaftaran event ditutup.
completed   Event telah selesai.
cancelled   Event dibatalkan.
```

### Status undangan

```text
pending     Belum pernah berhasil dikirim.
sent        Berhasil dikirim.
failed      Pengiriman terakhir gagal.
```

### Kapasitas event

```text
limited     Event memiliki jumlah peserta maksimum.
unlimited   Event tidak memiliki batas jumlah peserta.
```

Jika `capacity_type` bernilai `limited`, kolom `capacity` wajib diisi dengan angka lebih besar dari nol. Jika bernilai `unlimited`, kolom `capacity` boleh kosong.

## 10. Kebutuhan Non-Fungsional

### Keamanan

- Dashboard hanya dapat diakses admin yang sudah login.
- Token QR Code dibuat secara acak dan tidak mudah ditebak.
- Data peserta tidak ditampilkan melalui URL publik.
- Form publik menggunakan validasi dan perlindungan CSRF Laravel.
- File poster divalidasi berdasarkan tipe dan ukuran.
- Aksi admin penting dicatat di log aplikasi.

### Privasi

- Sistem hanya mengumpulkan data yang diperlukan untuk event.
- Data peserta hanya digunakan untuk pendaftaran, undangan, komunikasi event, dan absensi.
- Persetujuan penggunaan data wajib disimpan bersama waktu persetujuan.
- Informasi sensitif terkait kesehatan mental tidak dikumpulkan pada MVP.

### Performa

- Halaman publik dan formulir pendaftaran dibuat mobile-first serta dapat digunakan dengan nyaman melalui perangkat mobile.
- Input form menggunakan tipe yang sesuai pada perangkat mobile, seperti email dan nomor telepon.
- Pengiriman email synchronous ditampilkan dengan loading state.
- Daftar peserta menggunakan pagination.
- QR Code dapat dibaca dengan kamera ponsel.
- Halaman custom check-in dioptimalkan untuk digunakan melalui ponsel admin atau petugas.

### Penyimpanan file

- Spatie Media Library digunakan untuk poster event.
- Disk awal menggunakan local public storage.
- Struktur harus memungkinkan migrasi ke S3 atau Cloudflare R2 di masa mendatang.

## 11. Kriteria Penerimaan MVP

### Event

- Admin dapat membuat event dengan seluruh informasi wajib.
- Admin dapat mengunggah dan mengganti poster.
- Event published memiliki link pendaftaran yang dapat dibagikan.
- Event closed tidak menerima pendaftaran baru.

### Pendaftaran

- Peserta dapat mendaftar melalui perangkat mobile.
- Data wajib divalidasi.
- Email ganda pada event yang sama ditolak.
- Peserta melihat halaman sukses setelah pendaftaran berhasil.
- Halaman sukses menampilkan ucapan terima kasih.
- Jika tersedia, halaman sukses menampilkan ajakan dan link grup WhatsApp event.

### Email

- Admin dapat mengirim undangan dari halaman peserta.
- Email berisi informasi event yang benar.
- Email berisi QR Code peserta.
- Jika tersedia, email berisi link grup WhatsApp event.
- Status pengiriman tersimpan.
- Admin dapat mengirim ulang undangan.

### Check-in

- QR Code valid dapat digunakan untuk check-in.
- QR Code yang sama ditolak pada pemindaian kedua.
- Kode tidak valid menampilkan pesan error.
- Check-in manual tersedia sebagai fallback.

### Laporan

- Admin dapat melihat jumlah pendaftar dan jumlah hadir.
- Admin dapat melihat daftar peserta berdasarkan event.
- Admin dapat mengekspor peserta dan kehadiran.

## 12. Rencana Implementasi

### Fase 1 — Fondasi

- Konfigurasi environment.
- Install dan konfigurasi Spatie Media Library.
- Membuat model, migration, dan relasi event.
- Membuat Event Resource Filament.

### Fase 2 — Pendaftaran

- Membuat model dan migration registration.
- Membuat halaman publik event.
- Membuat formulir pendaftaran.
- Menambahkan validasi dan pencegahan duplikasi.

### Fase 3 — Undangan

- Konfigurasi Gmail SMTP.
- Membuat QR Code.
- Membuat template email Blade.
- Membuat action kirim dan kirim ulang undangan.

### Fase 4 — Absensi

- Membuat model dan migration attendance.
- Membuat halaman scan QR Code.
- Membuat check-in manual.
- Menambahkan pencegahan check-in ganda.

### Fase 5 — Laporan dan pengujian

- Dashboard statistik.
- Export data.
- Pengujian alur pendaftaran.
- Pengujian email.
- Pengujian QR Code dan check-in.
- Pengujian permission admin.

## 13. Hal yang Perlu Divalidasi

Sebelum implementasi dimulai, keputusan berikut perlu dikonfirmasi:

1. Satu email hanya dapat mendaftar satu kali pada event yang sama. Email yang sama boleh mendaftar pada event berbeda.
2. Peserta tidak dapat membatalkan pendaftaran sendiri.
3. Check-in hanya dilakukan oleh admin yang sudah login melalui halaman custom terpisah dari Filament.
4. Event memiliki dua pilihan kapasitas: terbatas dengan jumlah maksimum atau unlimited.
5. Undangan email dikirim satu per satu oleh admin.
6. Data peserta tidak dihapus setelah event selesai dan disimpan sebagai data histori.
7. Link grup WhatsApp event bersifat opsional dan dapat diisi atau dikosongkan oleh admin.

## 14. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| Gmail membatasi pengiriman email | Batasi pengiriman dan sediakan fitur kirim ulang manual |
| Email masuk spam | Gunakan SMTP dan subject/template email yang konsisten |
| QR Code sulit dipindai | Sediakan check-in manual sebagai cadangan pada halaman custom check-in |
| Admin menekan tombol kirim berkali-kali | Gunakan loading state dan simpan riwayat pengiriman |
| Poster terlalu besar | Validasi ukuran file dan buat conversion thumbnail |
| Data peserta tidak sengaja tersebar | Batasi akses dashboard dan jangan masukkan data pribadi ke QR Code |

## 15. Definisi Selesai MVP

MVP dianggap selesai apabila admin dapat membuat event dengan kapasitas terbatas atau unlimited, peserta dapat mendaftar sesuai aturan satu email per event, admin dapat mengirim undangan satu per satu berisi QR Code, dan admin yang sudah login dapat mencatat kehadiran peserta melalui halaman custom check-in atau check-in manual.

Seluruh data peserta dan kehadiran harus terhubung dengan event masing-masing serta dapat dilihat dari dashboard Filament.
