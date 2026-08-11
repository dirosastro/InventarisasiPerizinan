Saya ingin mengoptimalkan website berdasarkan hasil audit Google PageSpeed Insights.

Target:
- Performance minimal 90
- Accessibility minimal 95
- Best Practices minimal 95
- SEO tetap di atas 90

Berikut masalah yang ditemukan:

1. Performance Score: 76
2. Accessibility Score: 83
3. Best Practices Score: 78
4. SEO Score: 92

Lakukan optimasi berikut tanpa mengubah tampilan (UI) maupun alur bisnis aplikasi.

====================================
PERFORMANCE
====================================

1. Optimalkan Largest Contentful Paint (LCP)
- Percepat rendering elemen utama.
- Prioritaskan loading gambar hero/banner.
- Tambahkan preload pada resource penting.
- Hindari render-blocking CSS dan JavaScript.

2. Optimalkan gambar
- Konversi gambar ke WebP atau AVIF jika didukung.
- Gunakan responsive images.
- Tambahkan atribut width dan height.
- Terapkan lazy loading pada gambar yang berada di bawah viewport.
- Hindari gambar dengan resolusi berlebihan.

3. Optimalkan CSS
- Hapus unused CSS.
- Minify CSS.
- Inline critical CSS.
- Tunda pemuatan CSS yang tidak penting.

4. Optimalkan JavaScript
- Hapus unused JavaScript.
- Minify JavaScript.
- Gunakan defer atau async untuk script yang tidak kritikal.
- Kurangi ukuran bundle JavaScript.

5. Browser Caching
- Tambahkan Cache-Control untuk file statis.
- Tambahkan Expires Header.
- Optimalkan cache browser untuk CSS, JS, font, dan gambar.

6. Network Request
- Kurangi jumlah request HTTP.
- Kurangi dependency chain.
- Gabungkan file kecil bila memungkinkan.

7. Font
- Gunakan font-display: swap.
- Preload font yang digunakan.
- Hapus font yang tidak dipakai.

====================================
ACCESSIBILITY
====================================

1. Pastikan semua input memiliki label.
2. Tambahkan aria-label jika diperlukan.
3. Perbaiki kontras warna agar memenuhi standar WCAG AA.
4. Pastikan seluruh tombol memiliki nama yang dapat dibaca screen reader.
5. Tambahkan alt pada seluruh gambar.
6. Perbaiki struktur heading (H1-H6).
7. Pastikan elemen interaktif dapat diakses menggunakan keyboard.

====================================
BEST PRACTICES
====================================

1. Perbaiki seluruh Console Error.
2. Pastikan semua resource menggunakan HTTPS apabila tersedia.
3. Gunakan atribut rel="noopener noreferrer" pada link yang membuka tab baru.
4. Perbaiki ukuran dan rasio gambar.
5. Hapus library JavaScript yang sudah usang bila ada.
6. Terapkan security header yang sesuai.

====================================
SEO
====================================

1. Pastikan setiap halaman memiliki title unik.
2. Pastikan meta description tersedia.
3. Tambahkan alt image.
4. Perbaiki struktur heading.
5. Pastikan robots.txt dan sitemap.xml valid.
6. Perbaiki crawlability halaman.

====================================
GENERAL
====================================

- Jangan mengubah desain UI.
- Jangan mengubah warna.
- Jangan mengubah layout.
- Jangan mengubah alur bisnis.
- Jangan menghapus fitur yang sudah ada.
- Pertahankan kompatibilitas seluruh browser modern.
- Semua perubahan harus backward compatible.

====================================
OUTPUT
====================================

Berikan:

1. Daftar file yang diubah.
2. Penjelasan setiap perubahan.
3. Potongan kode sebelum dan sesudah.
4. Alasan teknis setiap optimasi.
5. Estimasi peningkatan skor PageSpeed.


Website ini dibangun menggunakan Laravel.

Lakukan optimasi sesuai best practice Laravel.

- Optimalkan Blade template.
- Gunakan Vite untuk asset.
- Minify CSS dan JavaScript.
- Optimalkan cache konfigurasi.
- Optimalkan route cache.
- Optimalkan view cache.
- Gunakan eager loading untuk query database.
- Kurangi query N+1.
- Optimalkan middleware.
- Gunakan queue untuk proses berat.