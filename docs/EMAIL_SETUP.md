# Konfigurasi Email Gmail MHC

Pengiriman undangan dilakukan manual oleh admin melalui action **Kirim undangan** pada Resource Peserta. Sistem tidak menggunakan queue untuk MVP.

## 1. Siapkan Gmail

Gunakan akun Gmail khusus komunitas. Aktifkan verifikasi 2 langkah, lalu buat **App Password** untuk aplikasi ini. Password aplikasi digunakan sebagai `MAIL_PASSWORD`, bukan password Gmail utama.

## 2. Isi `.env`

Salin konfigurasi berikut dan ganti nilai username serta app password:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alamat-gmail-komunitas@gmail.com
MAIL_PASSWORD=app-password-16-karakter
MAIL_FROM_ADDRESS="alamat-gmail-komunitas@gmail.com"
MAIL_FROM_NAME="MHC Community"
```

`MAIL_FROM_ADDRESS` sebaiknya sama dengan `MAIL_USERNAME` agar Gmail tidak menolak atau mengubah alamat pengirim.

## 3. Bersihkan konfigurasi cache

Setelah mengubah `.env`, jalankan:

```bash
php artisan optimize:clear
```

## 4. Alur pengiriman

1. Peserta mengisi form publik.
2. Admin membuka Resource Peserta.
3. Admin memilih action **Kirim undangan**.
4. Laravel mengirim email Gmail secara langsung.
5. Email berisi detail event, link grup WhatsApp jika tersedia, QR Code inline, dan attachment QR Code.
6. Status undangan berubah menjadi **Terkirim** atau **Gagal**.

Jangan menyimpan app password di repository atau membagikannya melalui chat.
