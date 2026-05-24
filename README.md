# Study-Study (PHP) – Penjelasan Website

Website **StudyTrack** adalah aplikasi untuk **guru dan siswa** yang membantu mengelola **tantangan (challenge)** mingguan, memantau **progress**, serta menyimpan **riwayat penilaian**.

## Fitur Utama
### Untuk Guru
- Melihat daftar tantangan: `/teacher/challenges`
- Membuat tantangan baru
- Mengedit tantangan
- Melihat daftar submisi siswa untuk setiap tantangan
- Memberi nilai dan feedback

### Untuk Siswa
- Mengerjakan tantangan dari halaman take/kerjakan: `/challenge`
- Melihat progress pengerjaan: `/progress`
- Melihat history penilaian: `/history`

## Perubahan yang Pernah Dibuat (Ringkas)
### 1) Pengaturan tampilan heading
Beberapa halaman pernah disesuaikan agar gaya judul (heading utama) konsisten. Pada akhirnya, **halaman History siswa** dikembalikan ke format awal seperti sebelumnya setelah permintaan pengguna.

### 2) Catatan fitur lampiran (attachment)
Di kode terdapat dukungan pengunggahan file (`attachment`) untuk:
- tantangan (pada sisi guru)
- jawaban/submisi (pada sisi siswa)

Jika kebutuhan Anda adalah “tidak boleh ada attachment sama sekali”, maka pengunggahan perlu dinonaktifkan juga di **backend/controller** serta dari **UI (input/link)**. (Saat ini, fitur attachment masih terlihat dipakai di beberapa controller dan view.)

## Hal penting yang sebaiknya diuji (sebelum rilis)
### Web UI
- Halaman guru:
  - `/teacher/challenges`
  - `/teacher/challenges/{id}/submissions`
  - `/teacher/challenges/{id}/submissions/{submissionId}/grade`
- Halaman siswa:
  - `/challenge` (mengerjakan dan submit)
  - `/progress`
  - `/history`

### Backend
- Validasi alur submit:
  - Pastikan data submisi tersimpan benar
- Jika attachment harus dinonaktifkan:
  - Uji submit dengan form yang berisi file (pastikan file tidak diterima)

## File yang relevan
- `app/controllers/TeacherController.php` (logika guru)
- `app/controllers/StudentController.php` (logika siswa)
- `app/views/teacher/challenges.php`
- `app/views/teacher/submissions.php`
- `app/views/teacher/grade_submission.php`
- `app/views/home/take.php`
- `app/views/home/progress.php`
- `app/views/home/history.php`

## Cara Menjalankan
1. Pastikan Anda memakai server PHP + MySQL (mis. Laragon).
2. Jalankan project ini di folder web server.
3. Pastikan database sudah terhubung (lihat `app/config/db.php`).
4. Buka aplikasi di browser sesuai URL lokal Anda.
