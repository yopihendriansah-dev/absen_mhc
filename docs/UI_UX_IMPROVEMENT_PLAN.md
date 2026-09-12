# UI/UX Improvement Plan

## Halaman Detail Event MHC

**Status:** Draft untuk validasi  
**Tujuan:** Membuat halaman event terasa lebih profesional, informatif, dan mendorong peserta menyelesaikan pendaftaran.

---

## 1. Prinsip Desain

- Informasi penting harus terlihat tanpa scroll panjang.
- Tombol pendaftaran harus menjadi aksi utama.
- Poster menjadi elemen visual pendukung, bukan mengambil seluruh perhatian.
- Konten event harus mudah dipindai melalui heading, metadata, dan paragraf pendek.
- Tampilan tetap mobile-first.
- Warna dan komponen mengikuti identitas logo MHC: navy, teal, mint, dan putih.
- Tidak menggunakan emoticon; icon menggunakan Blade Icons.

## 2. Target Pengalaman Pengguna

Peserta yang membuka link event harus dapat memahami hal berikut dalam beberapa detik:

1. Event apa yang akan diikuti.
2. Kapan event berlangsung.
3. Di mana event berlangsung.
4. Apakah masih tersedia tempat.
5. Bagaimana cara mendaftar.

## 3. Rencana Struktur Halaman

```text
Header brand MHC
        ↓
Breadcrumb kembali ke daftar event
        ↓
Hero event
 ├── Poster portrait
 └── Detail utama
     ├── Kategori/label event
     ├── Nama event
     ├── Tanggal
     ├── Waktu
     ├── Lokasi
     ├── Status kapasitas
     └── Tombol Daftar Event
        ↓
Deskripsi lengkap
        ↓
Informasi lokasi dan Google Maps
        ↓
CTA pendaftaran sticky/floating
        ↓
Modal formulir pendaftaran
```

## 4. Perbaikan Visual

### 4.1 Hero event

- Pertahankan layout dua kolom pada desktop.
- Poster menggunakan rasio portrait 3:4.
- Batasi lebar poster sekitar 280–320px.
- Gunakan `object-contain` agar poster tidak terpotong.
- Hilangkan frame warna yang tidak berasal dari poster.
- Gunakan radius dan shadow yang halus.

### 4.2 Hierarki informasi

Urutan visual:

1. Label atau kategori event.
2. Nama event.
3. Tanggal dan waktu.
4. Lokasi.
5. Kapasitas atau status pendaftaran.
6. Tombol daftar.

Judul event harus menjadi elemen terbesar setelah poster. Metadata menggunakan Blade Icons yang konsisten untuk jam, lokasi, kalender, dan peserta.

### 4.3 Palet warna

```text
Navy utama       #104B68
Teal utama       #159B83
Mint background  #E8F8F3
Page background  #F7FBFA
Border           #D7EEE8
Text sekunder    #5D7D86
```

Warna teal digunakan untuk CTA dan link. Warna navy digunakan untuk judul. Warna mint digunakan sebagai aksen, bukan sebagai frame poster.

## 5. Perbaikan Konten

### 5.1 Informasi kapasitas

Untuk event terbatas, tampilkan:

- Kapasitas maksimum.
- Jumlah kursi tersisa jika tersedia.
- Status `Pendaftaran penuh` jika kapasitas tercapai.

Untuk event unlimited, tampilkan label `Pendaftaran terbuka` tanpa angka kapasitas.

## 6. Perbaikan CTA Pendaftaran

### Desktop

- Tombol daftar tampil di hero dan tetap tersedia sebagai CTA sticky ketika pengguna membaca deskripsi panjang.
- Gunakan label `Daftar event` saat tersedia.
- Gunakan label `Pendaftaran penuh` saat kapasitas tercapai.
- Tombol disabled harus memiliki kontras dan status visual yang jelas.

### Mobile

- Tombol daftar dapat dibuat sticky di bagian bawah viewport.
- Sticky CTA tidak boleh menutupi isi atau tombol penting lainnya.
- Modal pendaftaran menggunakan lebar hampir penuh layar.
- Modal memiliki header tetap dan area isi yang dapat di-scroll.

## 7. Modal Pendaftaran

- Judul modal menampilkan nama event.
- Field wajib diberi tanda yang konsisten.
- Field disusun dalam satu kolom pada mobile.
- Field opsional ditempatkan setelah field wajib.
- Tombol submit selalu terlihat di bagian akhir form.
- Saat validasi gagal, modal otomatis terbuka kembali.
- Fokus keyboard diarahkan ke field pertama.
- Modal dapat ditutup dengan tombol Blade Icon `x-mark` dan klik backdrop.
- Setelah berhasil, peserta diarahkan ke halaman sukses.

## 8. Halaman Sukses

Halaman sukses menampilkan:

- Icon check dari Blade Icons.
- Ucapan terima kasih.
- Nama peserta.
- Nama event.
- Informasi bahwa undangan QR Code akan dikirim admin.
- Kode registrasi.
- Tombol grup WhatsApp jika tersedia.

Link grup WhatsApp tidak ditampilkan pada halaman detail sebelum peserta mendaftar.

## 9. Responsive Behavior

### Desktop

- Hero dua kolom.
- Poster portrait tetap berukuran terkendali.
- CTA berada di area utama.
- Deskripsi dapat dibaca tanpa membuat hero terlalu panjang.

### Tablet

- Dua kolom dipertahankan jika lebar cukup.
- Poster dan detail menggunakan proporsi yang seimbang.

### Mobile

- Poster tampil lebih dahulu.
- Detail event tampil di bawah poster.
- CTA full width.
- Modal hampir full-screen.
- Metadata tetap mudah dipindai.

## 10. Accessibility

- Semua gambar memiliki alt text yang informatif.
- Semua icon dekoratif diberi `aria-hidden="true"` jika tidak membawa informasi.
- Tombol memiliki label teks, bukan hanya icon.
- Warna teks memenuhi kontras yang cukup.
- Semua field memiliki label yang terhubung dengan input.
- Modal dapat digunakan dengan keyboard.
- Status kapasitas tidak hanya dibedakan melalui warna.

## 11. Perbaikan Admin

Tambahkan pada Event Resource:

- Preview event sebelum publish.
- Preview poster dengan rasio portrait.
- Field kategori event.
- Preview link pendaftaran.
- Tombol copy link.
- Indikator kapasitas tersisa.

Admin harus dapat melihat tampilan event yang akan dilihat peserta sebelum event diterbitkan.

## 12. Tahapan Implementasi

### Tahap 1 — Penyempurnaan visual

- Rapikan hero event.
- Tambahkan metadata icons.
- Konsistenkan spacing, border, dan shadow.
- Pastikan poster tanpa frame tambahan.

### Tahap 2 — Penyempurnaan konten

- Tambahkan kategori event.
- Rapikan deskripsi lengkap.
- Tambahkan status kapasitas.

### Tahap 3 — Peningkatan konversi

- Tambahkan CTA sticky desktop.
- Tambahkan sticky CTA mobile.
- Sempurnakan modal dan validasi form.

### Tahap 4 — Preview admin

- Tambahkan preview event.
- Tambahkan copy link.
- Tambahkan preview poster dan deskripsi lengkap.

### Tahap 5 — QA visual

- Uji desktop lebar.
- Uji laptop standar.
- Uji tablet.
- Uji mobile kecil.
- Uji keyboard dan screen reader dasar.
- Uji event dengan poster kosong.
- Uji event dengan deskripsi panjang.
- Uji event unlimited dan event penuh.

## 13. Definition of Done

Perbaikan UI/UX dianggap selesai apabila:

- Informasi utama event terlihat tanpa scroll panjang.
- CTA daftar terlihat jelas dan mudah dijangkau.
- Poster portrait tetap proporsional.
- Deskripsi panjang tidak mengganggu hero.
- Status kapasitas dapat dipahami tanpa bergantung pada warna.
- Modal pendaftaran nyaman digunakan di desktop dan mobile.
- Halaman sukses memiliki alur WhatsApp yang jelas.
- Tidak ada emoticon pada UI.
- Icon UI menggunakan Blade Icons.
- Tampilan lolos QA pada desktop dan mobile.
