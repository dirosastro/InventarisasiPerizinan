Lakukan security hardening pada aplikasi web ini berdasarkan OWASP Top 10 dan OWASP Web Security Testing Guide (WSTG).

Jangan mengubah tampilan (UI), alur bisnis, atau fungsi aplikasi.

Lakukan pemeriksaan dan perbaikan berikut:

1. Authentication
- Verifikasi password menggunakan algoritma hash yang kuat.
- Lindungi dari brute force (rate limiting/lockout).
- Regenerasi session setelah login.
- Pastikan logout menghapus session.

2. Authorization
- Verifikasi kontrol akses di seluruh endpoint.
- Pastikan tidak ada Broken Access Control.
- Pastikan pengguna hanya dapat mengakses resource sesuai haknya.

3. Session Management
- Cookie menggunakan HttpOnly, Secure (jika HTTPS), dan SameSite.
- Terapkan timeout session jika sesuai kebutuhan.

4. Input Validation
- Validasi seluruh input di sisi server.
- Sanitasi output untuk mencegah XSS.
- Gunakan prepared statements atau ORM.
- Hindari query SQL yang dibangun dari input pengguna.

5. File Upload
- Validasi tipe file dan ukuran.
- Acak nama file.
- Simpan file di lokasi yang aman.
- Pastikan file yang diunggah tidak dapat dieksekusi sebagai skrip.

6. Security Headers
- Tambahkan Content-Security-Policy.
- Tambahkan X-Frame-Options.
- Tambahkan X-Content-Type-Options.
- Tambahkan Referrer-Policy.
- Tambahkan Permissions-Policy.
- Tambahkan HSTS jika aplikasi menggunakan HTTPS.

7. Error Handling
- Jangan tampilkan stack trace atau informasi sensitif kepada pengguna.
- Catat error secara aman di log.

8. Logging
- Catat login, logout, perubahan data penting, dan kejadian keamanan.

9. Dependency
- Identifikasi library/dependency yang sudah usang atau memiliki kerentanan yang diketahui.
- Rekomendasikan pembaruan yang aman.

Output:
- Daftar temuan.
- Tingkat risiko (Critical, High, Medium, Low).
- File yang diubah.
- Penjelasan setiap perubahan.
- Potongan kode sebelum dan sesudah.
- Alasan teknis setiap perbaikan.