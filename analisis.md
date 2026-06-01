# Analisis Mendalam Sistem SIMAUCATAR

Tanggal audit: 2026-06-01
Lingkungan audit: local Windows, PHP spark serve, MySQL lokal, CodeIgniter 4
Tujuan dokumen: mencatat bug, risiko, inkonsistensi validasi, tombol/workflow yang belum lengkap, gap fitur HRIS, dan langkah optimalisasi sistem.

## 1. Ringkasan Eksekutif

Sistem sudah bisa berjalan lokal dan modul utamanya sudah terbentuk: login, dashboard administrator, master data departamentu/pozisaun/kategoria, funsionariu, prezensa, lisensa, salariu, avizu, sansaun, relatoriu, dan self-service funsionariu.

Namun kondisi saat ini belum siap untuk produksi karena ada beberapa risiko besar:

1. Autentikasi dan otorisasi belum kuat. Ada bug login akibat input bertipe email padahal username valid bisa berupa `admin`, validasi login sangat minimal, session tidak diregenerasi eksplisit saat login, dan akses role bisa ditembus lewat URL langsung pada beberapa halaman.
2. CSRF belum aktif secara global, sementara banyak aksi hapus/retira masih memakai route GET. Ini berbahaya karena perubahan data dapat dipicu dari link biasa.
3. Model dan controller punya side effect berat. Beberapa getter menjalankan `ALTER TABLE`/`CREATE TABLE`, dan `BaseController` otomatis menandai absen pada setiap request.
4. Struktur user ganda (`users/user_role` dan `utilizador/papel`) membuat relasi dan role tidak konsisten. Banyak data pegawai terhubung ke tabel `users`, sementara sebagian migration lama mengarah ke `utilizador`.
5. Validasi form belum konsisten antar halaman. Upload file belum divalidasi MIME/size/extension, salary payroll percaya pada hidden input dari browser, leave tidak mengecek overlap dengan benar, dan beberapa tombol hanya `href="#"`.
6. Database belum memiliki foreign key dan unique index yang cukup. Ini membuka risiko data yatim, duplikasi payroll, duplikasi akses menu, dan data role/user tidak sinkron.
7. Workflow HR masih kurang lengkap dibanding HRIS umum: belum ada leave balance, holiday calendar, shift/schedule, audit trail, approval bertingkat, payroll locking, payslip, password reset, 2FA, notifikasi yang bisa ditandai dibaca, document management, dan lifecycle pegawai.

Prioritas paling cepat:

1. Benahi login dan role guard.
2. Aktifkan CSRF dan ubah semua aksi hapus/retira menjadi POST/DELETE.
3. Hentikan auto absent dari `BaseController` dan pindahkan ke command/cron.
4. Satukan model user/role atau buat mapping yang jelas.
5. Tambahkan validasi server-side per modul dan constraint database.

## 2. Metodologi Audit

Audit dilakukan melalui:

1. Pembacaan struktur file dan route CodeIgniter.
2. Pemeriksaan controller, model, view, config, migration, dan seed.
3. Pemeriksaan database lokal yang sudah diimpor.
4. Smoke test browser pada halaman administrator dan funsionariu.
5. Pemeriksaan error console browser.
6. Pembandingan dengan referensi praktik baik CodeIgniter, OWASP, NIST, dan fitur HRIS/HCM umum.

Perintah/validasi yang sudah dilakukan:

1. `php -l` untuk file PHP di `app/**/*.php`: tidak ditemukan syntax error PHP.
2. `php spark routes`: route dapat dibaca dan memperlihatkan beberapa route mutasi data via GET.
3. Browser smoke test:
   - Login admin berhasil di `http://localhost:8080`.
   - Beberapa halaman admin terbuka.
   - Akses langsung ke beberapa halaman funsionariu dari session admin tidak selalu diblokir.
   - `funsionariu/perfil` sebagai admin menghasilkan HTTP 500 karena data pegawai null.
4. Database check:
   - Database memiliki 23 tabel.
   - Foreign key tidak terlihat aktif di database hasil import.
   - Ada kolom mojibake, misalnya `salariu_detallu.val¢r`.

## 3. Rujukan Praktik Baik yang Dipakai

Rujukan teknis:

1. CodeIgniter 4 Validation: https://codeigniter4.github.io/userguide/libraries/validation.html
2. CodeIgniter 4 Security/CSRF: https://codeigniter4.github.io/userguide/libraries/security.html
3. CodeIgniter 4 Filters: https://codeigniter4.github.io/userguide/incoming/filters.html
4. CodeIgniter 4 Uploaded Files: https://codeigniter4.github.io/userguide/libraries/uploaded_files.html
5. OWASP Authentication Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html
6. OWASP Session Management Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html
7. OWASP Cross-Site Request Forgery Prevention Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
8. OWASP File Upload Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html
9. OWASP Password Storage Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html
10. NIST SP 800-63B Digital Identity Guidelines: https://pages.nist.gov/800-63-4/sp800-63b.html

Rujukan fitur HRIS/HCM:

1. Oracle Fusion Cloud HCM overview: https://www.oracle.com/human-capital-management/
2. SAP SuccessFactors HCM suite: https://www.sap.com/products/hcm.html
3. BambooHR features: https://www.bamboohr.com/features
4. Workday Human Capital Management: https://www.workday.com/en-us/products/human-capital-management.html
5. SHRM HR technology/HRIS references: https://www.shrm.org/topics-tools/tools/toolkits/managing-human-resources-technology

Catatan: rujukan HRIS vendor bukan aturan wajib, tetapi dipakai sebagai pembanding fitur umum yang biasanya ada di sistem HR modern.

## 4. Peta Sistem Saat Ini

Framework dan runtime:

1. Framework: CodeIgniter 4.6.1.
2. PHP requirement di composer: `^8.1`.
3. Database lokal: MySQL, database `starterpanel`.
4. Entry local: `php spark serve --host 0.0.0.0 --port 8080`.

Modul utama:

1. `Auth`: login, logout, register.
2. `Administrador`: dashboard admin, master departamentu/pozisaun/kategoria, funsionariu, prezensa, lisensa, salariu, avizu, sansaun.
3. `Funsionariu`: dashboard pegawai, clock in/out, lisensa, salariu, perfil.
4. `Settings`: role, user, menu management, role access.
5. `Relatoriu`: laporan dan export PDF/CSV.
6. `ApplicationModel`: model besar yang menangani hampir semua query.
7. `RelatoriuModel`: query laporan.

Tabel penting:

1. User lama/template: `users`, `user_role`, `user_menu`, `user_submenu`, `user_access`.
2. User HRIS baru/Timor: `utilizador`, `papel`.
3. Pegawai/HR: `funsionariu`, `departamentu`, `pozisaun`, `kategoria`.
4. Attendance/leave/payroll: `prezensa`, `lisensa`, `salariu`, `salariu_detallu`, `subsidiu`.
5. Komunikasi/disiplin: `avizu`, `sansaun`, `tipu_sansaun`.

## 5. Temuan P0 - Kritis

### P0.1 Login memakai field email, tetapi kredensial valid memakai username

Lokasi:

1. `app/Views/pages/commons/login.php:66`
2. `app/Controllers/Auth.php:16-24`
3. `app/Models/ApplicationModel.php:105-139`

Masalah:

1. Input login bertipe `email`, tetapi akun valid seperti `admin` bukan email.
2. Placeholder juga menyebut email, sedangkan backend memanggil `getUser(username: $inputEmail)`.
3. Ini membuat user bingung dan bisa menyebabkan browser menolak submit username non-email.

Dampak:

1. User merasa tidak bisa login meskipun username/password benar.
2. Perilaku login tidak selaras antara UI dan backend.

Rekomendasi:

1. Ubah field menjadi `type="text"` dan label menjadi "Username atau Email".
2. Rename parameter controller dari `inputEmail` ke `login`.
3. Jika ingin email-only, ubah semua akun dan backend agar benar-benar memakai email.
4. Tambahkan validasi server-side: `required|min_length[3]|max_length[100]`.

### P0.2 Validasi login terlalu minimal

Lokasi:

1. `app/Controllers/Auth.php:16`
2. `app/Controllers/Auth.php:19-24`

Masalah:

1. Controller hanya memvalidasi `inputEmail => required`.
2. Password tidak divalidasi `required`.
3. Tidak ada cek status akun aktif/nonaktif.
4. Tidak ada rate limit, lockout, atau audit login gagal.
5. Password diberi `htmlspecialchars()` sebelum `password_verify()`. Jika password asli mengandung karakter seperti `&`, `<`, `>`, atau quote, hasil verifikasi bisa salah.

Dampak:

1. Login lebih mudah diserang brute-force.
2. Akun nonaktif tetap berpotensi login.
3. Password tertentu bisa gagal walaupun benar.

Rekomendasi:

1. Validasi login:
   - `login`: required, max length.
   - `password`: required, max length wajar.
2. Jangan pakai `htmlspecialchars()` untuk data password sebelum `password_verify()`.
3. Escape output di view, bukan mutate input autentikasi.
4. Tambahkan rate limiting per username dan IP.
5. Tambahkan kolom/logic `failed_attempts`, `locked_until`, `last_login_at`, `last_login_ip`.
6. Cek status akun sebelum membuat session.

### P0.3 Otorisasi role dapat dilewati pada direct URL

Lokasi:

1. `app/Filters/Authorization.php:29-60`
2. `app/Config/Routes.php:90-99`
3. `app/Controllers/Funsionariu.php`
4. `app/Views/pages/funsionariu/perfil.php:15`

Temuan smoke test:

1. Session admin dapat membuka `funsionariu/dashboard` dan menerima HTTP 200.
2. Session admin membuka `funsionariu/perfil` lalu aplikasi error HTTP 500.
3. Error terjadi karena data `funsionariu` null tetapi view tetap membaca array.

Masalah teknis:

1. Authorization filter mencari menu berdasarkan URL penuh atau segment pertama.
2. Jika menu tidak ditemukan, filter melakukan `return` dan request dibiarkan lewat.
3. Route protected yang tidak terdaftar di menu akhirnya menjadi boleh diakses.

Dampak:

1. Role isolation tidak aman.
2. Pegawai/admin dapat mengakses halaman yang tidak sesuai role jika tahu URL.
3. Halaman bisa crash saat session tidak memiliki `funsionariu_id`.

Rekomendasi:

1. Normalize path sebelum lookup: trim slash, lowercase jika perlu.
2. Untuk protected prefix seperti `administrador/*`, `users/*`, `funsionariu/*`, default harus deny jika menu/permission tidak ditemukan.
3. Tambahkan route guard eksplisit:
   - Admin routes hanya `role_name = administrador`.
   - Funsionariu routes hanya `role_name = funsionariu`.
4. Di controller funsionariu, validasi `session()->get('funsionariu_id')` dan data pegawai harus ada; jika tidak, redirect `blocked`.
5. Tambahkan integration test untuk akses silang role.

### P0.4 CSRF belum aktif global, sementara banyak aksi mutasi memakai GET

Lokasi:

1. `app/Config/Filters.php:75-80`
2. `app/Config/Routes.php:53,57,61,67,77,80,85,88`

Route mutasi via GET:

1. `administrador/departamentu/delete/(:num)`
2. `administrador/pozisaun/delete/(:num)`
3. `administrador/kategoria/delete/(:num)`
4. `administrador/funsionariu/delete/(:num)`
5. `administrador/subsidiu/delete/(:num)`
6. `administrador/avizu/delete/(:num)`
7. `administrador/sansaun/retira/(:num)`
8. `administrador/tipu_sansaun/delete/(:num)`

Masalah:

1. CSRF filter dikomentari.
2. Aksi hapus/retira berjalan lewat link GET.
3. Link GET dapat dipicu oleh klik, preview browser, crawler, atau halaman pihak ketiga.

Dampak:

1. Data penting bisa terhapus tanpa submit form yang sah.
2. Risiko CSRF tinggi.

Rekomendasi:

1. Aktifkan CSRF global untuk request mutasi.
2. Ubah semua aksi hapus/retira menjadi form POST dengan method spoofing `DELETE` atau `POST`.
3. Semua form harus menyertakan `csrf_field()`.
4. Gunakan modal konfirmasi yang submit form, bukan link GET.
5. Untuk API/AJAX, kirim token CSRF di header.

### P0.5 Auto absent berjalan pada setiap request

Lokasi:

1. `app/Controllers/BaseController.php:78-149`

Masalah:

1. `autoMarkAbsent()` dipanggil dari `BaseController::initController()`.
2. Artinya setiap request web dapat memicu proses menandai absen.
3. Proses ini membaca semua pegawai dan bisa menulis record `Falta`.
4. Pegawai yang sudah clock in tetapi belum clock out dapat diubah menjadi `Falta`.

Dampak:

1. Attendance bisa berubah hanya karena halaman dibuka.
2. Performa turun jika jumlah pegawai besar.
3. Sulit audit karena mutasi data terjadi dari request biasa.

Rekomendasi:

1. Pindahkan auto absent ke `spark command`, misalnya `attendance:mark-absent`.
2. Jalankan command lewat scheduler/cron sekali per hari setelah jam pulang.
3. Tambahkan lock agar command tidak berjalan ganda.
4. Buat status khusus untuk kasus clock-in tanpa clock-out, misalnya `Incomplete` atau `Sai La Iha`.
5. Simpan audit log kapan dan oleh proses apa attendance diubah.

### P0.6 Payroll mempercayai angka dari client

Lokasi:

1. `app/Controllers/Administrador.php:438-520`
2. `app/Views/pages/administrador/salariu.php`

Masalah:

1. `prosesaSalariu()` mengambil `salariu_baziku`, `total_subsidiu`, `total_deskontu`, `salariu_liquidu`, dan `sansaun_dedusaun` dari POST.
2. Nilai ini bisa diedit lewat browser devtools sebelum submit.
3. Perhitungan payroll harus server-authoritative.

Dampak:

1. Admin/user yang punya akses form bisa memalsukan nilai salary.
2. Laporan payroll tidak dapat dipercaya.

Rekomendasi:

1. Server harus mengambil gaji dasar dari `pozisaun`, subsidi dari tabel `subsidiu`, potongan dari `sansaun/prezensa`, lalu hitung ulang.
2. Hidden input hanya boleh membawa identifier, bukan nilai final.
3. Tambahkan unique index `salariu(funsionariu_id, fulan, tinan)`.
4. Tambahkan status payroll: `Draft`, `Processed`, `Locked`, `Paid`, `Cancelled`.
5. Tambahkan audit log payroll.

## 6. Temuan P1 - Tinggi

### P1.1 Struktur user ganda tidak konsisten

Lokasi:

1. `app/Models/ApplicationModel.php:105-139`
2. `app/Controllers/Administrador.php:184-197`
3. `app/Models/ApplicationModel.php:172-189`

Masalah:

1. Login mencoba tabel `users` dulu, lalu `utilizador`.
2. Pembuatan funsionariu baru memasukkan akun ke `users`.
3. Nama kolom `funsionariu.utilizador_id` memberi kesan relasi ke `utilizador`, tetapi praktik saat ini join ke `users`.
4. Role lama memakai `user_role`; role baru memakai `papel`.

Dampak:

1. Data user dan pegawai mudah tidak sinkron.
2. Foreign key sulit diterapkan.
3. Akses role bisa salah.
4. Import data lama dapat membuat pegawai login sebagai admin.

Rekomendasi:

1. Pilih satu sumber user/role:
   - Opsi A: pertahankan `users/user_role`, rename `funsionariu.utilizador_id` menjadi `user_id`.
   - Opsi B: migrasi penuh ke `utilizador/papel`, lalu ubah semua join dan controller.
2. Jangan pakai dua tabel login aktif tanpa mapping eksplisit.
3. Bersihkan data role pegawai.
4. Tambahkan unique index pada username.
5. Tambahkan foreign key setelah data dibersihkan.

### P1.2 Banyak pegawai terdata sebagai role administrator

Temuan database:

1. Banyak akun NID pegawai berada di tabel `users` dengan role `2`.
2. Role `2` adalah `administrador`.
3. Hanya sebagian kecil akun memakai role funsionariu.

Dampak:

1. Pegawai bisa mendapat akses admin.
2. Auth tidak mengisi `funsionariu_id` jika `role_name` bukan `funsionariu`.
3. Halaman self-service bisa rusak atau tidak dapat dipakai.

Rekomendasi:

1. Audit seluruh `users.role`.
2. Pastikan semua akun pegawai memakai role funsionariu.
3. Buat script koreksi:
   - Cocokkan `users.id` dengan `funsionariu.utilizador_id`.
   - Jika cocok dan bukan akun admin utama, set role menjadi funsionariu.
4. Tambahkan constraint agar akun pegawai tidak sembarang diberi role admin tanpa approval.

### P1.3 Runtime migration di model

Lokasi:

1. `app/Models/ApplicationModel.php:238-348`
2. `app/Models/ApplicationModel.php:593-627`

Contoh:

1. `getAvizu()` menjalankan `ALTER TABLE avizu ADD COLUMN data_remata`.
2. `getTipuSansaun()` menjalankan `CREATE TABLE IF NOT EXISTS`.
3. `getSansaun()` menjalankan `CREATE TABLE IF NOT EXISTS` dan `ALTER TABLE`.
4. `getAttendanceSettings()` menjalankan `CREATE TABLE IF NOT EXISTS` dan `ALTER TABLE`.
5. `getSubsidiu()` juga membuat tabel di runtime.

Masalah:

1. Getter seharusnya hanya membaca data.
2. Mutasi schema di request web dapat lambat, gagal, atau menyebabkan race condition.
3. Sulit deployment dan sulit audit versi schema.

Rekomendasi:

1. Pindahkan semua perubahan schema ke migration.
2. Jalankan `php spark migrate` saat setup/deploy.
3. Hilangkan query DDL dari model.
4. Tambahkan migration yang idempotent untuk kolom/tabel yang sudah muncul di runtime.

### P1.4 Database hampir tidak punya foreign key

Temuan:

1. Query `information_schema.referential_constraints` tidak menemukan foreign key aktif.
2. Banyak relasi penting hanya bergantung pada nilai ID manual.

Dampak:

1. Data yatim mudah terjadi.
2. Hapus data master dapat merusak histori.
3. Laporan bisa kehilangan nama departemen/pozisi/kategori.

Rekomendasi:

1. Tambahkan foreign key setelah data dibersihkan.
2. Gunakan `RESTRICT` untuk data master yang sudah dipakai.
3. Gunakan soft delete untuk pegawai, payroll, leave, attendance, dan sanksi.
4. Tambahkan index untuk kolom filter/report:
   - `prezensa(funsionariu_id, data_prezensa)`
   - `lisensa(funsionariu_id, data_hahu, data_remata, estadu_lisensa)`
   - `salariu(funsionariu_id, fulan, tinan)`
   - `sansaun(funsionariu_id, estadu_sansaun)`

### P1.5 Kolom payroll mojibake

Temuan database:

1. Kolom di `salariu_detallu` tampil sebagai `val¢r`.
2. Code memakai variasi `valór` atau hasil encoding lain.

Dampak:

1. Insert detail salary bisa gagal.
2. Export/report bisa rusak.
3. Query menjadi tidak portable dan sulit dirawat.

Rekomendasi:

1. Rename kolom menjadi ASCII: `valor`.
2. Normalisasi semua source code memakai `valor`.
3. Pastikan database dan koneksi memakai `utf8mb4`.
4. Tambahkan migration khusus rename kolom.

### P1.6 Query join salary payment status raw string

Lokasi:

1. `app/Models/ApplicationModel.php:606-621`

Masalah:

1. Join salary memakai concatenation:
   - `salariu.fulan = '.$fulan.' AND salariu.tinan = '.$tinan`
2. Meskipun UI mungkin angka, ini tetap pola raw SQL berisiko.

Dampak:

1. Risiko SQL injection jika input tidak dipaksa integer.
2. Bug query jika nilai kosong/non-numeric.

Rekomendasi:

1. Cast `fulan` dan `tinan` ke integer.
2. Pakai query builder conditions yang aman.
3. Validasi range bulan `1..12`, tahun `2000..2100`.

### P1.7 Dynamic menu dapat menulis controller, view, dan Routes.php dari input user

Lokasi:

1. `app/Controllers/Settings.php:207-249`
2. `app/Controllers/Settings.php:299-340`

Masalah:

1. `createMenu()` membuat file controller/view dan append route berdasarkan input.
2. Ini berisiko code/path injection.
3. Fitur ini cocok untuk developer, bukan admin runtime production.

Dampak:

1. Admin yang disusupi dapat membuat file PHP.
2. Route dapat rusak.
3. Deployment sulit dikontrol.

Rekomendasi:

1. Matikan fitur create file di production.
2. Menu management sebaiknya hanya mengatur metadata menu yang route-nya sudah ada.
3. Jika tetap dipakai, whitelist karakter URL, class name, dan folder.
4. Jangan append `Routes.php` di runtime; gunakan route dinamis yang aman.

### P1.8 Upload file belum divalidasi

Lokasi:

1. `app/Controllers/Administrador.php:214-248`
2. `app/Controllers/Funsionariu.php:212-215`

Masalah:

1. Upload hanya mengecek `isValid()` dan `hasMoved()`.
2. Tidak ada validasi extension, MIME, ukuran, dan tipe dokumen.
3. File disimpan di lokasi yang dapat diakses publik.

Dampak:

1. Risiko upload file berbahaya.
2. Storage cepat penuh.
3. Dokumen sensitif dapat diakses langsung jika URL diketahui.

Rekomendasi:

1. Validasi file dengan rules CodeIgniter:
   - `uploaded`
   - `max_size`
   - `is_image` untuk foto
   - `mime_in`
   - `ext_in`
2. Simpan dokumen sensitif di `writable/uploads`, bukan langsung public.
3. Buat route download yang memeriksa permission.
4. Hapus foto lama saat diganti.
5. Tambahkan scan/allowlist nama file.

## 7. Temuan P2 - Sedang

### P2.1 Remember me ada di UI tetapi tidak berfungsi

Lokasi:

1. `app/Views/pages/commons/login.php:74-75`
2. `app/Controllers/Auth.php`

Masalah:

1. Checkbox `rememberMe` tidak dibaca backend.
2. User mendapat ekspektasi session lebih lama, tetapi fitur tidak ada.

Rekomendasi:

1. Implement token remember-me yang aman:
   - selector dan validator token.
   - token disimpan hashed di database.
   - cookie `HttpOnly`, `Secure`, `SameSite=Lax/Strict`.
2. Atau hapus checkbox sampai fitur benar-benar dibuat.

### P2.2 Logout memakai GET

Lokasi:

1. `app/Config/Routes.php:11`
2. `app/Views/layouts/header.php:64`

Masalah:

1. Logout lewat GET.
2. Praktik lebih aman adalah POST dengan CSRF.

Rekomendasi:

1. Ubah logout menjadi POST.
2. Tambahkan form kecil untuk logout.
3. Regenerate/destroy session dengan benar.

### P2.3 Header memakai role ID salah untuk link avizu

Lokasi:

1. `app/Views/layouts/header.php:39`

Masalah:

1. Header menampilkan "View All Announcements" jika `session()->get('role') == 1`.
2. Role admin aktual tampaknya `2`.

Dampak:

1. UI admin bisa tidak menampilkan link yang seharusnya.
2. Role ID hardcoded rentan salah.

Rekomendasi:

1. Gunakan `session()->get('role_name') === 'administrador'`.
2. Hindari hardcoded role ID di view.

### P2.4 Error JavaScript global

Temuan browser:

1. `ReferenceError: $ is not defined` pada halaman login.
2. `TypeError: Cannot read properties of null (reading 'appendChild')` dari `public/assets/js/app.js`.

Lokasi terkait:

1. `app/Views/components/alerts.php:31-57`
2. `app/Views/layouts/main.php:22`
3. `public/assets/js/app.js`

Masalah:

1. Script jQuery/DataTables dipanggil sebelum library siap pada beberapa page.
2. Inisialisasi plugin di `app.js` tampaknya mencari elemen yang tidak selalu ada.

Dampak:

1. Script berikutnya bisa berhenti.
2. Modal, table, atau tombol AJAX bisa tidak berfungsi.

Rekomendasi:

1. Pastikan jQuery dimuat sebelum script yang memakai `$`.
2. Pindahkan inisialisasi DataTables ke layout setelah library.
3. Guard setiap plugin init:
   - hanya init jika element ada.
4. Pisahkan asset login dari asset dashboard.
5. Jalankan smoke test console setelah perbaikan.

### P2.5 Tombol profile belum berfungsi

Lokasi:

1. `app/Views/pages/funsionariu/perfil.php:21`
2. `app/Views/pages/funsionariu/perfil.php:97`
3. `app/Controllers/Funsionariu.php:159`

Masalah:

1. Tombol `Update Foto` masih `href="#"`.
2. Tombol `Konta Password Foun` masih `href="#"`.
3. Method `updatePerfil()` ada komentar saja dan tidak ada route aktif yang jelas.

Rekomendasi:

1. Buat route `POST funsionariu/perfil/update-foto`.
2. Buat route `POST funsionariu/perfil/update-password`.
3. Validasi password lama, password baru, confirm password.
4. Validasi upload foto.
5. Tampilkan modal form yang benar-benar submit.

### P2.6 Attendance tidak memakai toleransi dan status Tardi

Lokasi:

1. `app/Views/pages/administrador/prezensa.php:55`
2. `app/Controllers/Funsionariu.php:51-100`

Masalah:

1. `toleransia_minutu` disimpan/ada di config, tetapi view mengirim hidden value `0`.
2. `clockIn()` selalu membuat `Prezente` jika dalam range, tidak pernah `Tardi`.
3. Enum/status `Tardi` muncul di UI/laporan, tetapi logika belum memakainya.

Rekomendasi:

1. Tampilkan field toleransi di pengaturan attendance.
2. Hitung:
   - sebelum jam masuk sampai jam masuk + toleransi = `Prezente`.
   - setelah toleransi sampai jam batas masuk = `Tardi`.
3. Laporan harus menghitung `Tardi`.
4. Dashboard harus menampilkan `Tardi`.

### P2.7 Leave validation belum konsisten

Lokasi:

1. `app/Controllers/Funsionariu.php:173-216`
2. `app/Controllers/Administrador.php:339-374`
3. `app/Models/RelatoriuModel.php:57-63`

Masalah:

1. Komentar/pesan menyebut `Prezente/Tardi`, tetapi query hanya mengecek `Prezente`.
2. Tidak ada cek overlap dengan lisensi pending/approved yang sudah ada.
3. Approval leave bisa menimpa record attendance existing menjadi `Lisensa`.
4. Report lisensa hanya mengambil data yang start dan end berada di range, bukan overlap range.

Dampak:

1. Pegawai bisa mengajukan leave tumpang tindih.
2. Attendance historis bisa berubah tanpa kontrol.
3. Laporan leave tidak lengkap.

Rekomendasi:

1. Cek overlap:
   - `existing.data_hahu <= requested.data_remata`
   - `existing.data_remata >= requested.data_hahu`
2. Blok leave jika ada `Prezente` atau `Tardi`.
3. Saat approval, jangan overwrite attendance yang sudah punya jam masuk/keluar tanpa review.
4. Report leave gunakan overlap date range.
5. Tambahkan leave balance.

### P2.8 Avizu menghapus data expired saat dibaca

Lokasi:

1. `app/Models/ApplicationModel.php:238-252`

Masalah:

1. `getAvizu()` menghapus pengumuman expired secara otomatis.
2. Aksi read berubah menjadi delete.

Dampak:

1. Riwayat avizu hilang.
2. Audit komunikasi tidak ada.

Rekomendasi:

1. Tambahkan status `Aktivu`, `Expired`, `Archived`.
2. Filter default hanya tampilkan active.
3. Cleanup/archive lewat scheduled command jika diperlukan.

### P2.9 Sanksi memiliki risiko data dan logika

Lokasi:

1. `app/Controllers/Administrador.php:628-805`
2. `app/Views/pages/administrador/sansaun.php:90,288`
3. `app/Models/ApplicationModel.php:254-309`

Masalah:

1. Delete tipe sanksi tidak mengecek apakah tipe sudah digunakan.
2. Retira sanksi memakai GET.
3. Kategori sanksi memiliki mojibake di DB/source.
4. Auto-sanksi absensi memakai LIKE string yang rapuh.
5. Jika tipe sanksi tidak ditemukan, akses array `$tipu` bisa error.

Rekomendasi:

1. Gunakan foreign key dan restrict delete tipe yang sudah dipakai.
2. Retira via POST dengan CSRF.
3. Normalisasi enum kategori ke ASCII/stable code:
   - `general`
   - `salary_deduction`
   - `demotion`
4. Auto-sanksi harus punya tabel rule, bukan string LIKE.
5. Tambahkan approval dan audit.

### P2.10 Laporan belum mencakup semua status dan detail

Lokasi:

1. `app/Models/RelatoriuModel.php:27-63`
2. `app/Controllers/Relatoriu.php:116-243`

Masalah:

1. Rekap prezensa menghitung `Prezente`, `Falta`, `Lisensa`, tetapi tidak `Tardi`.
2. Report lisensa memakai range yang terlalu sempit.
3. Report salary belum menunjukkan detail komponen, potongan sanksi, status pembayaran, dan payroll lock.
4. CSV memakai header dengan karakter beraksen, sementara encoding project/database sedang bermasalah.

Rekomendasi:

1. Tambahkan kolom `Tardi`, `Incomplete`, `Holiday`, `Weekend` jika ada.
2. Report lisensa pakai overlap.
3. Salary report harus punya breakdown.
4. Export CSV pakai UTF-8 BOM atau header ASCII untuk kompatibilitas Excel.
5. Tambahkan filter departemen, posisi, status pegawai, dan rentang tanggal yang konsisten.

## 8. Temuan P3 - UI/UX, Maintainability, dan Kualitas

### P3.1 Banyak teks mojibake

Contoh:

1. `FunsionÃ¡riu`
2. `SalÃ¡riu`
3. `AbsÃ©nsia`
4. `val¢r`

Masalah:

1. File/source/database tidak konsisten encoding.
2. Teks Tetum/Portugis yang beraksen rusak.

Rekomendasi:

1. Standarkan encoding project ke UTF-8 tanpa BOM.
2. Standarkan database, table, dan connection ke `utf8mb4`.
3. Jalankan migration/data cleanup mojibake.
4. Hindari nama kolom beraksen. Gunakan ASCII untuk schema.

### P3.2 Footer berisi teks tidak profesional

Lokasi:

1. `app/Views/layouts/footer.php`

Masalah:

1. Footer menampilkan `RUMAH HANTU`.

Rekomendasi:

1. Ganti dengan nama aplikasi/lembaga.
2. Tambahkan versi aplikasi dan tahun.

### P3.3 ApplicationModel terlalu besar

Lokasi:

1. `app/Models/ApplicationModel.php`

Masalah:

1. Model ini mengurus menu, user, employee, attendance, leave, payroll, announcement, sanction, dan settings.
2. Sulit diuji dan rawan konflik perubahan.

Rekomendasi:

1. Pecah menjadi model/domain service:
   - `UserModel`
   - `EmployeeModel`
   - `AttendanceModel`
   - `LeaveModel`
   - `PayrollModel`
   - `AnnouncementModel`
   - `SanctionModel`
   - `MenuAccessModel`
2. Simpan query kompleks di service terpisah.

### P3.4 Belum ada automated test yang mewakili workflow HR

Masalah:

1. Tidak terlihat test untuk login, authorization, attendance, leave, payroll, dan report.

Rekomendasi test minimal:

1. Auth:
   - login username.
   - login email.
   - password salah.
   - akun nonaktif.
2. Authorization:
   - admin tidak boleh masuk self-service funsionariu.
   - funsionariu tidak boleh masuk admin.
3. Attendance:
   - clock in sebelum jam masuk.
   - clock in terlambat.
   - clock out.
   - duplicate clock in.
4. Leave:
   - date invalid.
   - overlap leave.
   - leave saat sudah present.
5. Payroll:
   - hitung ulang server-side.
   - duplicate payroll bulan sama ditolak.
6. Security:
   - delete GET ditolak.
   - CSRF wajib untuk POST/DELETE.

## 9. Audit Validasi Form per Modul

### 9.1 Login

Validasi saat ini:

1. `inputEmail` hanya required.
2. Password tidak required secara eksplisit.

Validasi yang disarankan:

1. `login`: `required|min_length[3]|max_length[100]`
2. `password`: `required|max_length[255]`
3. Rate limit: maksimum 5 percobaan gagal per 15 menit per akun/IP.
4. Pesan error jangan membedakan username tidak ada vs password salah.

### 9.2 Register

Lokasi:

1. `app/Controllers/Auth.php:77-108`

Masalah:

1. Validasi hanya email unique dan password match.
2. Belum jelas apakah fitur register publik memang dibutuhkan untuk HRIS.

Rekomendasi:

1. Jika sistem internal, matikan register publik.
2. Jika register tetap ada:
   - validasi nama lengkap.
   - validasi username/email.
   - password policy.
   - email verification atau admin approval.
3. Jangan auto-role administrator.

### 9.3 Master Departamentu/Pozisaun/Kategoria

Masalah umum:

1. Perlu validasi required, max length, unique name.
2. Salary harus numeric dan minimum 0.
3. Delete master data harus dicegah jika sudah dipakai pegawai.

Rekomendasi:

1. Tambahkan DB unique index pada nama master data.
2. Tambahkan server-side validation di create/update.
3. Gunakan soft-delete atau status aktif/nonaktif.

### 9.4 Funsionariu

Lokasi:

1. `app/Controllers/Administrador.php:177-284`

Masalah:

1. Upload foto belum divalidasi.
2. Password akun baru perlu policy.
3. Role user pegawai bisa salah.
4. NID harus unique di DB dan validasi controller.
5. Email/username harus unique di DB dan validasi controller.
6. Tidak ada status pegawai aktif/resign/suspended.

Rekomendasi validasi:

1. `nid`: required, max length, unique.
2. `naran_kompletu`: required, max length.
3. `departamentu_id`, `pozisaun_id`, `kategoria_id`: required, integer, exists.
4. `data_hahu_servisu`: required, valid_date, tidak terlalu jauh di masa depan.
5. `username`: required, unique.
6. `password`: required saat create, optional saat update, strong enough.
7. `foto_perfil`: max 2MB, image only.

### 9.5 Prezensa

Masalah:

1. Pengaturan jam/toleransi belum dipakai penuh.
2. Tidak ada konsep shift, holiday, weekend policy per departemen.
3. Koordinat/foto sudah ada di schema tetapi belum dipakai.

Rekomendasi:

1. Validasi jam masuk < jam batas masuk < jam pulang.
2. Validasi hari kerja.
3. Tambahkan shift schedule.
4. Tambahkan geofence opsional.
5. Tambahkan koreksi attendance oleh admin dengan alasan dan audit.

### 9.6 Lisensa

Masalah:

1. Date range invalid sebagian sudah dicek, tetapi overlap belum.
2. File upload belum divalidasi.
3. Tidak ada leave entitlement/balance.
4. Approval hanya satu tahap.

Rekomendasi:

1. Validasi tanggal mulai <= tanggal selesai.
2. Validasi overlap existing leave.
3. Validasi leave tidak berada pada hari libur/weekend jika kebijakan begitu.
4. Kurangi balance saat approved.
5. Tambahkan alasan reject.
6. Tambahkan approval history.

### 9.7 Salariu

Masalah:

1. Nilai payroll dari browser.
2. Tidak ada payroll locking.
3. Tidak ada unique DB constraint.
4. Detail salary terganggu kolom mojibake.

Rekomendasi:

1. Hitung payroll dari data server.
2. Simpan detail komponen:
   - basic salary.
   - fixed allowances.
   - attendance deduction.
   - sanction deduction.
   - tax/other deduction jika ada.
3. Tambahkan payslip.
4. Tambahkan approval payroll.
5. Tambahkan status dan locked period.

### 9.8 Avizu

Masalah:

1. Expired announcement dihapus saat dibaca.
2. Tidak ada target audience.
3. Tidak ada read receipt.

Rekomendasi:

1. Tambahkan `audience_type`: all, department, role, selected employees.
2. Tambahkan `published_at`, `expires_at`, `status`.
3. Tambahkan tabel `avizu_read`.
4. Jangan hapus otomatis.

### 9.9 Sansaun

Masalah:

1. Tipe sanksi bisa dihapus walau dipakai.
2. Status/kategori belum stabil.
3. Auto-sanksi absensi hardcoded.

Rekomendasi:

1. Rule engine sederhana untuk sanksi.
2. Approval workflow.
3. History perubahan posisi/gaji.
4. Retira sanksi harus butuh alasan.

### 9.10 Settings Role/User/Menu

Masalah:

1. Validasi role/user belum lengkap.
2. Menu management dapat menulis file.
3. `updateMenuCategory()` tidak punya where clause.

Lokasi:

1. `app/Models/ApplicationModel.php:54-56`

Dampak:

1. Jika method ini dipakai, semua kategori menu bisa berubah menjadi nama yang sama.

Rekomendasi:

1. Tambahkan where clause berdasarkan ID.
2. Validasi role name unique.
3. Cegah delete role yang masih dipakai user.
4. Tambahkan unique composite untuk `user_access`.
5. Audit semua perubahan role/access.

## 10. Audit Tombol dan Interaksi UI

Tombol/aksi yang perlu diperbaiki:

1. `Update Foto` di halaman profile funsionariu masih `href="#"`.
2. `Konta Password Foun` masih `href="#"`.
3. `Remember Me` di login belum ada backend.
4. Semua tombol `Hamos` yang mengarah ke route GET harus menjadi form DELETE.
5. `Retira Sansaun` memakai link GET dari JavaScript.
6. DataTable init perlu dicek karena error `$ is not defined`.
7. Modal salary harus tidak mengirim nilai final sebagai sumber kebenaran.
8. Header role check untuk avizu perlu diperbaiki.

Checklist smoke test UI setelah perbaikan:

1. Login username `admin` dari field text.
2. Login email jika didukung.
3. Logout POST.
4. Hapus departamentu/pozisaun/kategoria lewat DELETE dan CSRF.
5. Pegawai tidak bisa buka admin.
6. Admin tidak bisa buka self-service funsionariu.
7. Update foto profile benar-benar upload.
8. Update password membutuhkan password lama.
9. Clock in menghasilkan `Prezente` atau `Tardi` sesuai jam.
10. Leave overlap ditolak.
11. Payroll nilai tidak berubah walau hidden input dimanipulasi.
12. Console browser bersih dari error fatal.

## 11. Gap Fitur Dibanding HRIS/HCM Umum

Fitur yang biasanya ada di HRIS/HCM modern tetapi belum lengkap di sistem:

1. Employee master data lengkap:
   - status aktif/resign/suspended.
   - supervisor/manager.
   - struktur organisasi.
   - kontak darurat.
   - alamat, identitas, dokumen kontrak.
2. Employee self-service:
   - update data pribadi dengan approval.
   - download payslip.
   - lihat leave balance.
   - lihat attendance calendar.
   - notifikasi dan read status.
3. Time and attendance:
   - shift schedule.
   - holiday calendar.
   - overtime.
   - late/early leave rules.
   - attendance correction request.
   - geolocation/photo proof jika dibutuhkan.
4. Leave management:
   - leave type.
   - annual entitlement.
   - accrual.
   - carryover.
   - approval bertingkat.
   - attachment validation.
5. Payroll:
   - payroll period.
   - payroll lock.
   - payslip.
   - approval.
   - component master.
   - tax/deduction.
   - audit and reversal.
6. Discipline/sanction:
   - rule master.
   - approval.
   - evidence attachment.
   - appeal/retira reason.
   - link to payroll deduction.
7. Announcement:
   - audience targeting.
   - schedule publish/expire.
   - read receipt.
8. Reporting:
   - dashboard trend.
   - export by filter.
   - audit report.
   - payroll summary/detail.
   - attendance anomaly report.
9. Security:
   - password reset.
   - 2FA optional.
   - account lockout.
   - audit log.
   - role permission management with deny-by-default.
10. Administration:
   - backup/restore procedure.
   - import/export employees.
   - data retention.
   - environment config validation.

## 12. Roadmap Perbaikan

### Fase 1 - Stabilitas login dan keamanan dasar

Target: aplikasi aman dipakai lokal/internal.

1. Ubah login field menjadi username/email text.
2. Tambah validasi login dan hapus `htmlspecialchars()` pada password.
3. Tambah `session()->regenerate(true)` saat login sukses.
4. Cek status akun aktif.
5. Aktifkan CSRF.
6. Ubah delete/retira/logout menjadi POST/DELETE.
7. Perbaiki authorization filter deny-by-default.
8. Blok direct URL lintas role.
9. Perbaiki role pegawai yang masih admin.
10. Perbaiki error `funsionariu/perfil` jika data null.

### Fase 2 - Benahi data model dan validasi form

Target: data tidak mudah korup.

1. Pilih satu tabel user/role utama.
2. Tambahkan unique index:
   - `users.username`
   - `salariu(funsionariu_id, fulan, tinan)`
   - `user_access(role_id, menu_category_id, menu_id, submenu_id)` atau desain ulang permission.
3. Tambahkan foreign key setelah cleanup.
4. Pindahkan runtime DDL ke migration.
5. Normalisasi kolom `val¢r/valór` menjadi `valor`.
6. Tambah validasi upload file.
7. Perbaiki `updateMenuCategory()` where clause.
8. Tambah validation rules untuk semua create/update master.

### Fase 3 - Workflow HR inti

Target: attendance, leave, payroll benar secara bisnis.

1. Pindahkan auto absent ke scheduled command.
2. Implement `Tardi` dan toleransi.
3. Tambahkan status attendance `Incomplete`.
4. Tambahkan leave overlap check.
5. Tambahkan leave balance.
6. Hitung payroll server-side.
7. Tambahkan payroll period dan lock.
8. Tambahkan payslip.
9. Tambahkan audit log untuk approval, payroll, dan delete.

### Fase 4 - Fitur HRIS lengkap

Target: sistem mendekati HRIS operasional.

1. Employee document management.
2. Organization structure dan supervisor.
3. Approval bertingkat.
4. Holiday calendar dan shift.
5. Notification/read receipt.
6. Attendance correction request.
7. Import/export pegawai.
8. Advanced report dashboard.
9. Password reset dan optional 2FA.
10. Backup/restore procedure.

## 13. Rekomendasi Schema Tambahan

### 13.1 Audit log

Tabel: `audit_logs`

Kolom:

1. `id`
2. `actor_user_id`
3. `actor_role`
4. `action`
5. `entity_type`
6. `entity_id`
7. `old_values` JSON
8. `new_values` JSON
9. `ip_address`
10. `user_agent`
11. `created_at`

Dipakai untuk:

1. login sukses/gagal.
2. create/update/delete master data.
3. approval/reject leave.
4. process payroll.
5. retira sanksi.
6. change permission.

### 13.2 Leave balance

Tabel: `leave_balances`

Kolom:

1. `id`
2. `funsionariu_id`
3. `leave_type`
4. `year`
5. `entitlement_days`
6. `used_days`
7. `pending_days`
8. `remaining_days`
9. `created_at`
10. `updated_at`

### 13.3 Holiday calendar

Tabel: `holidays`

Kolom:

1. `id`
2. `date`
3. `name`
4. `is_recurring`
5. `created_at`
6. `updated_at`

### 13.4 Payroll period

Tabel: `payroll_periods`

Kolom:

1. `id`
2. `fulan`
3. `tinan`
4. `status`
5. `processed_by`
6. `processed_at`
7. `locked_by`
8. `locked_at`

Status:

1. `Draft`
2. `Processed`
3. `Locked`
4. `Paid`
5. `Cancelled`

### 13.5 Anunsiu/read receipt

Keputusan terbaru: tabel dan modul `notifications` tidak dipakai lagi sebagai fitur aktif. Pemberitahuan sistem dipusatkan ke Anunsiu/Avizu.

Jika read receipt Anunsiu dibutuhkan pada fase berikutnya, gunakan tabel `avizu_reads` dengan kolom:

1. `id`
2. `avizu_id`
3. `user_id`
4. `read_at`
5. `created_at`
6. `updated_at`

## 14. Contoh Aturan Validasi yang Disarankan

### Login

```php
$rules = [
    'login' => 'required|min_length[3]|max_length[100]',
    'password' => 'required|max_length[255]',
];
```

### Upload foto profile

```php
$rules = [
    'foto_perfil' => [
        'rules' => 'uploaded[foto_perfil]|is_image[foto_perfil]|mime_in[foto_perfil,image/jpg,image/jpeg,image/png]|max_size[foto_perfil,2048]',
    ],
];
```

### Leave request

```php
$rules = [
    'tipu_lisensa' => 'required|max_length[100]',
    'data_hahu' => 'required|valid_date[Y-m-d]',
    'data_remata' => 'required|valid_date[Y-m-d]',
    'razaun' => 'required|min_length[5]|max_length[1000]',
];
```

Validasi tambahan setelah rules:

1. `data_remata >= data_hahu`.
2. Tidak overlap dengan leave pending/approved.
3. Tidak ada attendance `Prezente` atau `Tardi` pada rentang tanggal.
4. Balance mencukupi.

### Payroll process

Input yang boleh dipercaya:

1. `funsionariu_id`
2. `fulan`
3. `tinan`

Nilai yang harus dihitung server:

1. `salariu_baziku`
2. `total_subsidiu`
3. `total_deskontu`
4. `sansaun_dedusaun`
5. `salariu_liquidu`

## 15. Daftar Backlog Teknis

### Security

1. Aktifkan CSRF global.
2. Ubah route mutasi GET menjadi POST/DELETE.
3. Deny-by-default pada Authorization filter.
4. Session regenerate saat login.
5. Logout POST.
6. Rate limit login.
7. Account lockout.
8. Status akun aktif/nonaktif.
9. Password reset.
10. Optional 2FA.
11. Audit log.

### Database

1. Hapus runtime DDL dari model.
2. Migration untuk semua tabel/kolom yang sekarang dibuat runtime.
3. Normalisasi encoding ke UTF-8/utf8mb4.
4. Rename kolom mojibake `val¢r` ke `valor`.
5. Unique index username.
6. Unique index payroll period per employee.
7. Foreign key setelah cleanup.
8. Index report.
9. Soft delete untuk data penting.
10. Seed role default yang konsisten.

### Auth/User

1. Satukan `users/user_role` dan `utilizador/papel`.
2. Perbaiki role pegawai.
3. Hapus register publik jika tidak dibutuhkan.
4. Tambah change password.
5. Tambah profile update.

### Attendance

1. Command auto absent.
2. Implement `Tardi`.
3. Implement `Incomplete`.
4. Shift/holiday calendar.
5. Attendance correction request.
6. Geolocation/photo proof opsional.

### Leave

1. Leave balance.
2. Overlap validation.
3. Approval history.
4. Reject reason.
5. Attachment validation.
6. Leave calendar.

### Payroll

1. Server-side calculation.
2. Payroll lock.
3. Payslip.
4. Component breakdown.
5. Approval/reversal.
6. Report detail.

### UI/UX

1. Perbaiki JavaScript load order.
2. Guard plugin init jika element tidak ada.
3. Hapus tombol placeholder atau implement backend.
4. Perbaiki teks mojibake.
5. Ganti footer.
6. Konsisten bahasa UI.
7. Tampilkan empty state yang jelas.
8. Tampilkan validation error per field.

## 16. Risiko Jika Dibiarkan

1. User tidak bisa login karena mismatch email/username.
2. Pegawai bisa mendapat akses administrator karena role data salah.
3. Halaman self-service bisa diakses role yang salah dan menghasilkan error 500.
4. Data dapat terhapus lewat link GET.
5. Attendance berubah otomatis saat halaman dibuka.
6. Payroll bisa dimanipulasi dari browser.
7. Upload file berbahaya bisa masuk ke server.
8. Database semakin sulit diperbaiki karena runtime DDL dan user table ganda.
9. Laporan tidak akurat karena status tidak dihitung lengkap.
10. Sistem sulit diaudit karena belum ada audit log.

## 17. Quick Wins yang Paling Cepat Dikerjakan

1. Ubah login input `type=email` menjadi `type=text`.
2. Tambahkan validasi password required di login.
3. Tambahkan session regenerate saat login sukses.
4. Aktifkan CSRF.
5. Ubah logout menjadi POST.
6. Ubah semua delete link menjadi form POST/DELETE.
7. Perbaiki Authorization filter agar unknown protected route diblokir.
8. Tambahkan null guard di semua method/view funsionariu.
9. Perbaiki role pegawai yang salah di database.
10. Matikan/hapus `rememberMe` sampai backend dibuat.
11. Perbaiki JS load order untuk jQuery/DataTables.
12. Rename kolom salary detail ke `valor`.
13. Pindahkan DDL dari model ke migration.
14. Ubah auto absent menjadi command.
15. Tambahkan validasi upload file.

## 18. Kesimpulan

Sistem ini sudah punya fondasi modul HR yang cukup luas, tetapi perlu distabilkan sebelum dikembangkan lebih jauh. Masalah paling mendesak bukan menambah fitur baru, melainkan membuat workflow dasar menjadi benar dan aman: login, role guard, CSRF, validasi form, integritas database, attendance, leave, dan payroll.

Setelah fondasi ini rapi, fitur tambahan seperti leave balance, shift, payroll lock, payslip, audit log, notification, document management, dan approval bertingkat akan jauh lebih mudah ditambahkan tanpa memperbesar utang teknis.

Urutan terbaik:

1. Security/auth/authorization.
2. Data model dan migration.
3. Validasi form dan upload.
4. Attendance/leave/payroll correctness.
5. HRIS feature expansion.

## 19. PRD Tambahan - Panduan Eksekusi Pengembangan SIMAUCATAR

Bagian ini adalah Product Requirements Document (PRD) yang ditempatkan dalam file yang sama agar audit, rancangan fitur, dan langkah implementasi dapat dieksekusi dari satu dokumen.

### 19.1 Tujuan Produk

SIMAUCATAR diarahkan menjadi sistem HRIS internal yang membantu administrasi pegawai, absensi, cuti/lisensi, payroll, sanksi, pengumuman, dan laporan dengan workflow yang aman, konsisten, dan dapat diaudit.

Tujuan utama:

1. Mengurangi pekerjaan manual HR/admin.
2. Membuat data pegawai, absensi, lisensi, dan payroll lebih akurat.
3. Memberikan self-service untuk pegawai.
4. Menjaga akses data sesuai role.
5. Menyediakan laporan yang bisa dipakai untuk pengambilan keputusan.
6. Membuat setiap aksi penting dapat ditelusuri melalui audit log.

### 19.2 Sasaran Pengguna

Role utama:

1. Super administrator
   - Mengatur user, role, permission, konfigurasi sistem, backup, dan audit.
2. Administrator HR
   - Mengelola data pegawai, master data, absensi, lisensi, payroll, sanksi, pengumuman, dan laporan.
3. Funsionariu/Pegawai
   - Melakukan absensi, melihat profil, mengajukan lisensi, melihat salary/payslip, membaca pengumuman, dan menerima notifikasi.
4. Manager/Supervisor (fitur tambahan)
   - Menyetujui lisensi, memeriksa attendance tim, memberi rekomendasi sanksi, dan melihat laporan tim.
5. Auditor/Read-only (fitur tambahan)
   - Melihat laporan dan audit log tanpa dapat mengubah data.

### 19.3 Prinsip Produk

1. Deny by default untuk akses protected.
2. Semua perubahan data penting harus lewat POST/PUT/PATCH/DELETE dengan CSRF.
3. Semua perhitungan penting harus dilakukan server-side.
4. Semua aksi penting harus memiliki audit log.
5. UI boleh sederhana, tetapi workflow harus jelas dan tidak menipu user.
6. Data historis tidak boleh dihapus sembarangan.
7. Master data yang sudah dipakai harus di-nonaktifkan, bukan dihapus permanen.
8. Schema database harus stabil melalui migration, bukan dibuat runtime di model.
9. Validasi form harus konsisten di frontend dan backend, tetapi backend tetap sumber kebenaran.
10. Bahasa UI harus konsisten dan encoding harus benar.

### 19.4 Scope Versi

#### Versi 1.1 - Stabilization Release

Fokus:

1. Perbaikan login.
2. Perbaikan authorization.
3. CSRF dan route mutasi aman.
4. Validasi form utama.
5. Perbaikan role pegawai.
6. Perbaikan bug profile, JS, dan payroll basic.
7. Hapus side effect berbahaya dari model/controller.

Output:

1. Sistem bisa dipakai lokal/internal tanpa error utama.
2. Login jelas dan dapat dipahami.
3. Role admin dan funsionariu tidak bocor.
4. Delete/retira/logout tidak lagi via GET.
5. Attendance tidak berubah hanya karena halaman dibuka.

#### Versi 1.2 - Core HR Workflow Release

Fokus:

1. Attendance lengkap: Tardi, Incomplete, schedule, command auto absent.
2. Leave management: overlap check, approval/reject reason, leave balance.
3. Payroll: server-side calculation, payroll period, payroll lock, payslip.
4. Audit log.
5. Notification basic.

Output:

1. Workflow absensi, lisensi, dan payroll menjadi reliable.
2. Semua aksi HR penting punya jejak audit.
3. Pegawai bisa melihat data dirinya dengan lebih lengkap.

#### Versi 1.3 - HRIS Expansion Release

Fokus:

1. Supervisor/manager workflow.
2. Approval bertingkat.
3. Document management.
4. Holiday calendar dan shift.
5. Import/export pegawai.
6. Dashboard dan report lanjutan.
7. Backup/restore procedure.

Output:

1. Sistem lebih dekat ke HRIS operasional lengkap.
2. Admin dapat mengelola data besar dengan lebih efisien.
3. Laporan dapat dipakai untuk audit dan evaluasi.

## 20. PRD Modul Security, Auth, dan Access Control

### 20.1 Problem Statement

Saat ini login, session, dan role guard belum cukup kuat. Ada mismatch antara email/username, validasi minimal, akses role bisa ditembus pada route tertentu, CSRF belum aktif, dan banyak route mutasi menggunakan GET.

### 20.2 Goal

Membuat akses sistem aman, jelas, dan konsisten untuk semua role.

### 20.3 User Stories

1. Sebagai user, saya ingin login memakai username atau email agar saya tidak bingung dengan field login.
2. Sebagai admin, saya ingin akun nonaktif tidak bisa login agar akses pegawai yang sudah keluar dapat ditutup.
3. Sebagai sistem, saya ingin membatasi percobaan login gagal agar brute force bisa dicegah.
4. Sebagai admin, saya ingin pegawai tidak bisa membuka halaman admin walaupun tahu URL.
5. Sebagai pegawai, saya ingin hanya melihat halaman self-service saya sendiri.
6. Sebagai auditor, saya ingin melihat histori login dan perubahan permission.

### 20.4 Functional Requirements

Auth:

1. Field login menerima username atau email.
2. Password wajib diisi.
3. Password tidak dimodifikasi dengan `htmlspecialchars()` sebelum verifikasi.
4. Setelah login sukses, session harus diregenerasi.
5. Akun harus memiliki status aktif.
6. Login gagal dicatat dalam audit/security log.
7. Rate limiting diterapkan per IP dan login identifier.
8. Pesan error login harus generik.
9. Logout memakai POST dan CSRF.
10. Register publik dinonaktifkan jika sistem hanya internal.

Access control:

1. Protected route harus deny-by-default.
2. Role admin hanya dapat membuka prefix `administrador/*` dan `users/*`.
3. Role funsionariu hanya dapat membuka prefix `funsionariu/*`.
4. Super admin dapat membuka settings/security/audit.
5. Unknown protected route harus redirect ke `blocked` atau 403.
6. Menu visibility tidak boleh menjadi satu-satunya access control.
7. Permission check harus dilakukan di server.

Session:

1. Session cookie harus `HttpOnly`.
2. Session cookie harus `SameSite=Lax` atau `Strict`.
3. Jika HTTPS dipakai, cookie harus `Secure`.
4. Session timeout harus jelas.
5. Session harus dihancurkan saat logout.

### 20.5 Acceptance Criteria

1. User bisa login dengan `admin` jika username valid.
2. User bisa login dengan email jika email/username email valid.
3. Password kosong ditolak.
4. Akun nonaktif ditolak.
5. 5 kali login gagal dalam window tertentu memicu delay/lockout.
6. Admin membuka `funsionariu/dashboard` mendapat 403/blocked.
7. Funsionariu membuka `administrador/dashboard` mendapat 403/blocked.
8. Logout GET ditolak.
9. Delete GET ditolak.
10. Semua form mutasi menyertakan CSRF.

### 20.6 Data Requirements

Kolom yang disarankan pada tabel user utama:

1. `id`
2. `username`
3. `email`
4. `password_hash`
5. `role_id`
6. `status`
7. `failed_login_count`
8. `locked_until`
9. `last_login_at`
10. `last_login_ip`
11. `password_changed_at`
12. `created_at`
13. `updated_at`

Tabel tambahan:

1. `login_attempts`
2. `audit_logs`
3. `remember_tokens` jika remember me benar-benar dibuat.

### 20.7 Implementation Notes

Urutan implementasi:

1. Putuskan tabel user utama.
2. Buat migration penambahan kolom status/security.
3. Perbaiki login view.
4. Perbaiki `Auth::index()`.
5. Perbaiki `Authorization` filter.
6. Aktifkan CSRF.
7. Ubah route mutasi.
8. Tambahkan test auth/access.

## 21. PRD Modul Employee Master Data

### 21.1 Problem Statement

Data pegawai sudah ada tetapi belum lengkap sebagai HRIS. Belum ada status pegawai, supervisor, dokumen, riwayat posisi, dan data lifecycle pegawai.

### 21.2 Goal

Membuat data pegawai menjadi pusat data HR yang konsisten dan dapat dipakai oleh attendance, leave, payroll, sanction, dan report.

### 21.3 User Stories

1. Sebagai admin HR, saya ingin menambah pegawai lengkap dengan akun login agar pegawai bisa memakai self-service.
2. Sebagai admin HR, saya ingin mengubah status pegawai menjadi aktif/nonaktif/resign agar workflow attendance dan payroll menyesuaikan.
3. Sebagai admin HR, saya ingin melihat riwayat posisi pegawai agar perubahan jabatan dapat diaudit.
4. Sebagai pegawai, saya ingin melihat profil saya sendiri.
5. Sebagai pegawai, saya ingin mengajukan perubahan data pribadi.
6. Sebagai supervisor, saya ingin melihat daftar bawahan.

### 21.4 Functional Requirements

Data pegawai:

1. NID wajib unique.
2. Nama lengkap wajib.
3. Departemen, posisi, kategori wajib.
4. Tanggal mulai kerja wajib.
5. Status pegawai wajib:
   - `active`
   - `inactive`
   - `resigned`
   - `suspended`
6. Supervisor opsional pada fase awal.
7. Foto profile opsional dengan validasi image.
8. Data kontak:
   - email
   - nomor telepon
   - alamat
   - kontak darurat.
9. Dokumen pegawai dapat diupload pada modul document management.

Riwayat:

1. Perubahan posisi disimpan ke `employee_position_histories`.
2. Perubahan status disimpan ke `employee_status_histories`.
3. Perubahan salary base akibat posisi harus tercatat.

Self-service:

1. Pegawai dapat melihat profil.
2. Pegawai dapat mengajukan update data tertentu.
3. Update data penting menunggu approval admin.

### 21.5 Acceptance Criteria

1. Admin tidak bisa menyimpan pegawai tanpa NID.
2. Admin tidak bisa menyimpan NID duplikat.
3. Admin tidak bisa memilih departemen/posisi/kategori yang tidak ada.
4. Upload foto selain jpg/png ditolak.
5. Pegawai nonaktif tidak muncul dalam daftar payroll aktif.
6. Pegawai resign tidak bisa clock in.
7. Perubahan posisi menghasilkan history.
8. Pegawai hanya bisa melihat profil sendiri.

### 21.6 Data Requirements

Tambahan kolom `funsionariu`:

1. `status`
2. `supervisor_id`
3. `email`
4. `phone`
5. `address`
6. `emergency_contact_name`
7. `emergency_contact_phone`
8. `created_at`
9. `updated_at`
10. `deleted_at`

Tabel baru:

1. `employee_position_histories`
2. `employee_status_histories`
3. `employee_profile_change_requests`
4. `employee_documents`

### 21.7 Implementation Notes

1. Tambahkan migration status pegawai.
2. Perbaiki form create/update funsionariu.
3. Tambahkan validasi lengkap.
4. Tambahkan soft delete.
5. Perbaiki delete pegawai menjadi deactivate/resign, bukan hard delete default.
6. Tambahkan history saat posisi/status berubah.

## 22. PRD Modul Attendance

### 22.1 Problem Statement

Attendance sudah memiliki clock in/out, tetapi toleransi belum dipakai, status `Tardi` belum dihitung, auto absent berjalan di request biasa, dan belum ada schedule/holiday.

### 22.2 Goal

Membuat attendance akurat, bisa diaudit, dan sesuai aturan kerja.

### 22.3 User Stories

1. Sebagai pegawai, saya ingin clock in dan clock out agar kehadiran saya tercatat.
2. Sebagai pegawai, saya ingin tahu status saya hari ini: belum absen, hadir, terlambat, atau sudah pulang.
3. Sebagai admin HR, saya ingin mengatur jam kerja dan toleransi.
4. Sebagai admin HR, saya ingin sistem menandai absen setelah hari kerja berakhir.
5. Sebagai admin HR, saya ingin mengoreksi attendance dengan alasan.
6. Sebagai supervisor, saya ingin melihat attendance bawahan.

### 22.4 Functional Requirements

Clock in:

1. Hanya pegawai aktif yang bisa clock in.
2. Tidak boleh clock in dua kali pada tanggal yang sama.
3. Jika clock in <= jam masuk + toleransi, status `Prezente`.
4. Jika clock in > jam masuk + toleransi dan <= batas masuk, status `Tardi`.
5. Jika lewat batas masuk, clock in ditolak atau dicatat sebagai terlambat berat sesuai policy.
6. Jika hari libur/weekend, sistem mengikuti konfigurasi.

Clock out:

1. Hanya bisa clock out jika sudah clock in.
2. Tidak boleh clock out dua kali.
3. Jika belum clock out setelah jam pulang dan command auto absent berjalan, status bisa `Incomplete`, bukan langsung `Falta`.

Auto absent:

1. Tidak boleh berjalan di `BaseController`.
2. Harus berjalan melalui command terjadwal.
3. Hanya menandai pegawai aktif yang tidak memiliki attendance dan tidak sedang leave approved.
4. Hari libur/weekend tidak ditandai `Falta`.

Correction:

1. Admin dapat membuat koreksi attendance.
2. Koreksi wajib memiliki alasan.
3. Koreksi masuk audit log.
4. Pegawai bisa mengajukan correction request pada fase lanjutan.

### 22.5 Acceptance Criteria

1. Jam kerja 08:00, toleransi 15 menit:
   - clock in 08:10 = `Prezente`.
   - clock in 08:20 = `Tardi`.
2. Pegawai tidak bisa clock in dua kali.
3. Pegawai tidak bisa clock out tanpa clock in.
4. Auto absent command tidak berjalan saat membuka halaman.
5. Pegawai approved leave tidak ditandai `Falta`.
6. Report menghitung `Tardi`.
7. Semua koreksi attendance masuk audit log.

### 22.6 Data Requirements

Tambahan status attendance:

1. `Prezente`
2. `Tardi`
3. `Falta`
4. `Lisensa`
5. `Incomplete`
6. `Holiday`
7. `Weekend`

Tabel baru:

1. `attendance_settings`
2. `attendance_corrections`
3. `attendance_correction_requests`
4. `work_schedules`
5. `holidays`

### 22.7 Implementation Notes

1. Buat command `attendance:mark-absent`.
2. Tambahkan migration enum/status baru.
3. Perbaiki `clockIn()` agar memakai toleransi.
4. Perbaiki `clockOut()` agar validasi lebih jelas.
5. Tambahkan report status `Tardi` dan `Incomplete`.
6. Tambahkan halaman setting jam kerja.

## 23. PRD Modul Leave/Lisensa

### 23.1 Problem Statement

Lisensa sudah bisa diajukan dan disetujui, tetapi belum memiliki balance, overlap validation, approval history, reject reason, dan attachment validation.

### 23.2 Goal

Membuat leave management yang akurat, tidak tumpang tindih, dan terhubung dengan attendance serta payroll.

### 23.3 User Stories

1. Sebagai pegawai, saya ingin mengajukan lisensi dengan tanggal, tipe, alasan, dan dokumen pendukung.
2. Sebagai pegawai, saya ingin melihat status pengajuan saya.
3. Sebagai admin/supervisor, saya ingin approve/reject pengajuan dengan alasan.
4. Sebagai admin HR, saya ingin sistem mengurangi leave balance setelah approved.
5. Sebagai admin HR, saya ingin leave approved otomatis mempengaruhi attendance.

### 23.4 Functional Requirements

Leave request:

1. Pegawai aktif dapat mengajukan leave.
2. Tanggal mulai wajib <= tanggal selesai.
3. Tidak boleh overlap dengan leave pending/approved.
4. Tidak boleh diajukan untuk tanggal yang sudah `Prezente` atau `Tardi`.
5. File pendukung divalidasi tipe dan ukuran.
6. Tipe leave dapat menentukan apakah balance dikurangi.

Approval:

1. Admin/supervisor dapat approve/reject.
2. Reject wajib alasan.
3. Approval menyimpan siapa dan kapan.
4. Setelah approved, attendance tanggal terkait dibuat/diupdate menjadi `Lisensa` hanya jika belum ada attendance present/tardy.
5. Jika ada konflik attendance, sistem menampilkan error dan meminta review manual.

Balance:

1. Leave balance dihitung per pegawai, tipe, dan tahun.
2. Pending leave mengurangi `pending_days`.
3. Approved leave mengurangi `remaining_days`.
4. Rejected/cancelled mengembalikan pending.

### 23.5 Acceptance Criteria

1. Pengajuan tanggal akhir sebelum tanggal awal ditolak.
2. Pengajuan overlap dengan pending/approved ditolak.
3. Pengajuan pada hari yang sudah `Prezente` ditolak.
4. File `.exe` ditolak.
5. Reject tanpa alasan ditolak.
6. Approved leave mengubah attendance kosong menjadi `Lisensa`.
7. Approved leave tidak menimpa attendance yang sudah punya jam masuk.
8. Balance berkurang sesuai jumlah hari kerja.

### 23.6 Data Requirements

Tabel baru:

1. `leave_types`
2. `leave_balances`
3. `leave_approval_histories`
4. `leave_attachments`

Tambahan kolom `lisensa`:

1. `leave_type_id`
2. `approved_by`
3. `approved_at`
4. `rejected_by`
5. `rejected_at`
6. `reject_reason`
7. `cancelled_at`

### 23.7 Implementation Notes

1. Buat master leave type.
2. Buat migration leave balance.
3. Tambahkan overlap query.
4. Tambahkan approval history.
5. Tambahkan validasi upload.
6. Tambahkan report leave balance.

## 24. PRD Modul Payroll/Salariu

### 24.1 Problem Statement

Payroll saat ini masih mempercayai nilai dari form browser, belum memiliki period lock, belum memiliki payslip, dan detail payroll terganggu kolom mojibake.

### 24.2 Goal

Membuat payroll yang dihitung server-side, dapat diaudit, dan menghasilkan payslip pegawai.

### 24.3 User Stories

1. Sebagai admin HR, saya ingin memproses payroll per bulan dan tahun.
2. Sebagai admin HR, saya ingin melihat breakdown gaji setiap pegawai.
3. Sebagai admin HR, saya ingin mengunci payroll setelah diverifikasi.
4. Sebagai pegawai, saya ingin melihat dan mengunduh payslip saya.
5. Sebagai auditor, saya ingin melihat siapa yang memproses payroll dan kapan.

### 24.4 Functional Requirements

Payroll calculation:

1. Input hanya `funsionariu_id`, `fulan`, dan `tinan`.
2. Gaji dasar diambil dari posisi pegawai.
3. Subsidi diambil dari master subsidi.
4. Potongan attendance dihitung dari aturan.
5. Potongan sanksi dihitung dari sanksi aktif.
6. Salary net dihitung server-side.
7. Semua komponen disimpan di `salariu_detallu`.

Payroll period:

1. Payroll diproses per periode bulan/tahun.
2. Periode dapat memiliki status:
   - `Draft`
   - `Processed`
   - `Locked`
   - `Paid`
   - `Cancelled`
3. Periode locked tidak boleh diubah kecuali reversal oleh role khusus.

Payslip:

1. Pegawai dapat melihat payslip miliknya.
2. Admin dapat melihat semua payslip.
3. Payslip menunjukkan:
   - gaji dasar.
   - subsidi.
   - potongan.
   - sanksi.
   - total net.
   - tanggal proses.
4. Payslip dapat diunduh PDF.

### 24.5 Acceptance Criteria

1. Manipulasi hidden input salary tidak mengubah hasil payroll.
2. Payroll pegawai yang sama pada bulan/tahun sama tidak bisa diproses dua kali.
3. Periode locked menolak update/delete payroll.
4. Payslip pegawai A tidak bisa dilihat pegawai B.
5. Detail salary tersimpan dengan kolom `valor`.
6. Payroll process masuk audit log.
7. Report payroll sama dengan data payroll yang tersimpan.

### 24.6 Data Requirements

Tabel baru:

1. `payroll_periods`
2. `payroll_approvals`
3. `payroll_reversals`

Perbaikan tabel:

1. Rename `salariu_detallu.val¢r` atau variasi `valór` menjadi `valor`.
2. Tambahkan unique index `salariu(funsionariu_id, fulan, tinan)`.
3. Tambahkan `payroll_period_id` pada `salariu`.
4. Tambahkan `processed_by`, `processed_at`.
5. Tambahkan `locked_at`, `locked_by` pada period.

### 24.7 Implementation Notes

1. Normalisasi kolom `valor`.
2. Tambahkan service `PayrollService`.
3. Pindahkan perhitungan dari view/controller ke service.
4. Tambahkan migration unique index.
5. Tambahkan halaman payroll period.
6. Tambahkan payslip view/PDF.
7. Tambahkan test payroll tampering.

## 25. PRD Modul Sanction/Sansaun

### 25.1 Problem Statement

Sanksi sudah ada tetapi rule, kategori, approval, dan relasi payroll belum stabil. Retira masih memakai GET dan tipe sanksi bisa berisiko dihapus walau dipakai.

### 25.2 Goal

Membuat sanksi sebagai workflow disiplin yang aman, dapat diaudit, dan terhubung dengan payroll jika sanksi berupa potongan.

### 25.3 User Stories

1. Sebagai admin HR, saya ingin membuat tipe sanksi.
2. Sebagai admin HR, saya ingin memberi sanksi kepada pegawai dengan alasan.
3. Sebagai admin HR, saya ingin sanksi potongan salary otomatis masuk payroll.
4. Sebagai admin HR, saya ingin menarik/retira sanksi dengan alasan.
5. Sebagai auditor, saya ingin melihat history sanksi.

### 25.4 Functional Requirements

Tipe sanksi:

1. Kategori memakai kode stabil:
   - `general`
   - `salary_deduction`
   - `demotion`
2. Tipe sanksi yang sudah dipakai tidak boleh dihapus.
3. Tipe sanksi dapat dinonaktifkan.

Sanksi:

1. Sanksi wajib punya pegawai, tipe, tanggal, alasan.
2. Jika kategori salary deduction, nilai/percent harus jelas.
3. Jika kategori demotion, posisi baru wajib.
4. Retira sanksi wajib alasan.
5. Retira sanksi via POST dengan CSRF.
6. Semua perubahan status masuk audit log.

Auto sanction:

1. Rule auto sanction disimpan di tabel, bukan hardcoded.
2. Rule dapat berdasarkan jumlah `Falta`, `Tardi`, atau kombinasi.
3. Sistem tidak membuat duplikasi sanksi untuk periode yang sama.

### 25.5 Acceptance Criteria

1. Delete tipe sanksi yang sudah dipakai ditolak.
2. Retira via GET ditolak.
3. Retira tanpa alasan ditolak.
4. Salary deduction muncul di payroll.
5. Demotion membuat history posisi.
6. Auto sanction tidak membuat duplikasi.

### 25.6 Data Requirements

Tabel baru:

1. `sanction_rules`
2. `sanction_histories`
3. `sanction_attachments`

Tambahan kolom:

1. `tipu_sansaun.code`
2. `tipu_sansaun.is_active`
3. `sansaun.retira_reason`
4. `sansaun.retira_by`
5. `sansaun.retira_at`

## 26. PRD Modul Announcement/Avizu dan Notification

### 26.1 Problem Statement

Avizu sudah ada tetapi expired data dihapus saat dibaca, belum ada target audience, dan belum ada read receipt/notifikasi.

### 26.2 Goal

Membuat pengumuman internal yang dapat ditargetkan, dijadwalkan, dan diketahui apakah sudah dibaca.

### 26.3 User Stories

1. Sebagai admin HR, saya ingin membuat pengumuman untuk semua pegawai.
2. Sebagai admin HR, saya ingin membuat pengumuman hanya untuk departemen tertentu.
3. Sebagai pegawai, saya ingin menerima notifikasi pengumuman baru.
4. Sebagai admin HR, saya ingin melihat siapa yang sudah membaca pengumuman.

### 26.4 Functional Requirements

Avizu:

1. Pengumuman memiliki title, content, status, publish date, expire date.
2. Status:
   - `Draft`
   - `Published`
   - `Expired`
   - `Archived`
3. Audience:
   - all
   - role
   - department
   - selected employees
4. Expired tidak dihapus otomatis.
5. Admin dapat archive.

Notification:

1. User menerima notifikasi saat avizu published.
2. Notifikasi dapat ditandai dibaca.
3. Header menampilkan jumlah unread.
4. Pegawai hanya melihat avizu yang sesuai audience.

### 26.5 Acceptance Criteria

1. Avizu expired tidak hilang dari database.
2. Pegawai departemen A tidak melihat pengumuman khusus departemen B.
3. Membuka avizu menandai read receipt.
4. Header unread count berkurang setelah dibaca.
5. Admin bisa melihat daftar pembaca.

### 26.6 Data Requirements

Tabel baru:

1. `avizu_audiences`
2. `avizu_reads`
3. Tidak memakai tabel `notifications`; kebutuhan pemberitahuan masuk ke Anunsiu.

Tambahan kolom `avizu`:

1. `status`
2. `published_at`
3. `expires_at`
4. `created_by`

## 27. PRD Modul Reporting dan Dashboard

### 27.1 Problem Statement

Laporan sudah ada, tetapi belum mencakup semua status, belum punya drill-down, dan belum punya report audit/operasional lengkap.

### 27.2 Goal

Menyediakan laporan yang akurat, dapat difilter, dan siap export.

### 27.3 User Stories

1. Sebagai admin HR, saya ingin melihat rekap pegawai per departemen.
2. Sebagai admin HR, saya ingin melihat attendance bulanan dengan status lengkap.
3. Sebagai admin HR, saya ingin melihat leave balance dan leave usage.
4. Sebagai admin HR, saya ingin export payroll summary dan detail.
5. Sebagai auditor, saya ingin melihat audit log dan perubahan data penting.

### 27.4 Functional Requirements

Report pegawai:

1. Filter departemen, posisi, kategori, status.
2. Export CSV/PDF.
3. Kolom status pegawai.

Report attendance:

1. Hitung `Prezente`, `Tardi`, `Falta`, `Lisensa`, `Incomplete`.
2. Filter date range.
3. Filter departemen.
4. Drill-down per pegawai.

Report leave:

1. Gunakan overlap date range.
2. Tampilkan status pending/approved/rejected.
3. Tampilkan leave balance.

Report payroll:

1. Summary per bulan/tahun.
2. Detail komponen payroll.
3. Status payroll period.
4. Export payslip.

Report audit:

1. Filter actor, action, entity, date range.
2. Lihat old/new values.
3. Export untuk auditor.

### 27.5 Acceptance Criteria

1. Attendance report menghitung `Tardi`.
2. Leave yang overlap range muncul di report.
3. Payroll report sama dengan payroll tersimpan.
4. CSV terbuka benar di Excel.
5. User tanpa permission tidak bisa export report.

## 28. PRD Modul Document Management

### 28.1 Problem Statement

Upload saat ini terbatas dan belum aman. HRIS biasanya membutuhkan penyimpanan dokumen pegawai seperti kontrak, ID, sertifikat, dan dokumen leave.

### 28.2 Goal

Menyediakan penyimpanan dokumen pegawai yang aman, tervalidasi, dan memiliki kontrol akses.

### 28.3 User Stories

1. Sebagai admin HR, saya ingin mengupload dokumen pegawai.
2. Sebagai pegawai, saya ingin melihat dokumen tertentu milik saya.
3. Sebagai admin HR, saya ingin memberi kategori dokumen.
4. Sebagai auditor, saya ingin melihat histori upload/delete dokumen.

### 28.4 Functional Requirements

1. Dokumen disimpan di `writable/uploads`, bukan public langsung.
2. Download dokumen lewat controller yang mengecek permission.
3. File type dibatasi:
   - pdf
   - jpg
   - jpeg
   - png
4. Ukuran file dibatasi.
5. Dokumen punya kategori.
6. Dokumen punya visibility:
   - admin_only
   - employee_visible
7. Delete dokumen soft delete.
8. Semua upload/delete masuk audit log.

### 28.5 Acceptance Criteria

1. File `.php` ditolak.
2. Pegawai A tidak bisa download dokumen pegawai B.
3. Admin bisa melihat semua dokumen.
4. Dokumen admin_only tidak terlihat pegawai.
5. Download URL langsung tanpa permission ditolak.

## 29. PRD Modul Import/Export Data

### 29.1 Problem Statement

Admin kemungkinan perlu memasukkan banyak pegawai sekaligus dan mengekspor data untuk kebutuhan eksternal.

### 29.2 Goal

Menyediakan import/export yang aman, tervalidasi, dan dapat rollback jika gagal.

### 29.3 User Stories

1. Sebagai admin HR, saya ingin import pegawai dari CSV/Excel.
2. Sebagai admin HR, saya ingin melihat preview error sebelum data disimpan.
3. Sebagai admin HR, saya ingin export data pegawai dengan filter.

### 29.4 Functional Requirements

Import:

1. Upload CSV/XLSX.
2. Preview data sebelum simpan.
3. Validasi NID unique.
4. Validasi departemen/posisi/kategori exists.
5. Validasi role pegawai.
6. Jika ada error, tampilkan baris dan pesan.
7. Simpan dalam transaction.

Export:

1. Export pegawai CSV/XLSX.
2. Export attendance CSV/XLSX.
3. Export payroll CSV/XLSX/PDF.
4. Export memperhatikan permission.

### 29.5 Acceptance Criteria

1. Import dengan NID duplikat ditolak pada baris yang benar.
2. Import tidak menyimpan sebagian data jika transaction gagal.
3. Export hanya berisi data sesuai filter.
4. Export pegawai tidak membocorkan password/hash.

## 30. PRD Modul Backup, Restore, dan Maintenance

### 30.1 Problem Statement

Sistem HR menyimpan data penting. Perlu backup dan prosedur pemulihan agar data tidak hilang.

### 30.2 Goal

Menyediakan prosedur backup/restore yang jelas untuk environment lokal maupun production.

### 30.3 Functional Requirements

1. Admin/super admin dapat melihat status backup terakhir.
2. Backup database dapat dijalankan manual oleh role khusus.
3. Backup otomatis dijadwalkan.
4. Restore hanya dapat dilakukan super admin.
5. Restore harus butuh konfirmasi berlapis.
6. Log backup/restore masuk audit log.
7. File upload penting ikut strategi backup.

### 30.4 Acceptance Criteria

1. Backup menghasilkan file yang dapat diverifikasi.
2. Restore tidak bisa dijalankan oleh admin biasa.
3. Backup failure memberi notifikasi.
4. Dokumentasi restore tersedia.

## 31. PRD UI/UX dan Bahasa

### 31.1 Problem Statement

UI sudah terbentuk, tetapi beberapa tombol belum berfungsi, teks mojibake, bahasa campur, dan error JavaScript muncul.

### 31.2 Goal

Membuat UI konsisten, jelas, dan bebas dari error fatal.

### 31.3 Requirements

1. Semua tombol yang terlihat harus memiliki fungsi.
2. Jika fitur belum ada, tombol disembunyikan.
3. Form menampilkan error per field.
4. Empty state jelas.
5. Loading state untuk aksi submit panjang.
6. Confirmation modal untuk aksi berisiko.
7. Bahasa dipilih konsisten:
   - Tetum, atau
   - Indonesia, atau
   - bilingual dengan i18n.
8. Encoding harus UTF-8.
9. Tidak ada mojibake pada teks UI.
10. Browser console tidak memiliki error fatal pada halaman utama.

### 31.4 Acceptance Criteria

1. Tidak ada tombol `href="#"` kecuali trigger yang ditangani JavaScript.
2. Semua form gagal validasi menampilkan pesan field.
3. Halaman login, dashboard, employee, attendance, leave, payroll, report bebas dari error console fatal.
4. Teks aksen tampil benar atau dinormalisasi ke ASCII.

## 32. Technical Execution Plan

### 32.1 Urutan Eksekusi Aman

Jangan langsung menambahkan semua fitur besar sebelum fondasi aman. Urutan yang disarankan:

1. Backup database dan file.
2. Buat branch kerja.
3. Tambahkan test minimal auth/access.
4. Perbaiki login.
5. Perbaiki role data.
6. Aktifkan CSRF.
7. Ubah route mutasi GET menjadi POST/DELETE.
8. Perbaiki authorization deny-by-default.
9. Pindahkan runtime DDL ke migration.
10. Normalisasi kolom payroll `valor`.
11. Pindahkan auto absent ke command.
12. Tambahkan validasi upload.
13. Perbaiki payroll server-side.
14. Tambahkan audit log.
15. Mulai fitur leave balance, shift, payslip, dan Anunsiu/read receipt.

### 32.2 Definition of Ready

Sebuah task siap dikerjakan jika:

1. Tujuan jelas.
2. File/modul terdampak diketahui.
3. Data/schema yang diperlukan jelas.
4. Acceptance criteria jelas.
5. Risiko migration diketahui.
6. Cara test diketahui.

### 32.3 Definition of Done

Sebuah task dianggap selesai jika:

1. Kode selesai.
2. Migration selesai jika ada perubahan schema.
3. Validasi backend selesai.
4. UI menampilkan pesan error/sukses.
5. Test manual minimal lulus.
6. Tidak ada error console fatal untuk halaman terkait.
7. Tidak ada route mutasi via GET.
8. Audit log ditulis untuk aksi penting.
9. Dokumentasi singkat diperbarui jika perlu.

### 32.4 Migration Policy

Aturan migration:

1. Semua perubahan schema harus lewat migration.
2. Jangan menjalankan DDL di model getter.
3. Migration harus bisa dijalankan di database bersih.
4. Migration data cleanup harus diuji di copy database.
5. Untuk rename kolom mojibake, buat backup dulu.
6. Tambahkan index setelah data bersih.
7. Tambahkan foreign key setelah data yatim dibersihkan.

### 32.5 Testing Matrix

Auth:

1. Login username benar.
2. Login email benar.
3. Password salah.
4. Password kosong.
5. Akun nonaktif.
6. Lockout.
7. Logout POST.

Authorization:

1. Admin akses admin OK.
2. Admin akses funsionariu blocked.
3. Funsionariu akses self-service OK.
4. Funsionariu akses admin blocked.
5. Guest akses protected redirect login.

CSRF:

1. POST tanpa CSRF ditolak.
2. DELETE tanpa CSRF ditolak.
3. Form valid dengan CSRF sukses.

Employee:

1. Create valid.
2. Create NID duplikat ditolak.
3. Upload foto invalid ditolak.
4. Deactivate pegawai.

Attendance:

1. Clock in present.
2. Clock in tardy.
3. Clock out.
4. Duplicate clock in.
5. Auto absent command.
6. Leave approved tidak jadi falta.

Leave:

1. Request valid.
2. Date invalid.
3. Overlap ditolak.
4. Approved.
5. Rejected dengan alasan.
6. Attachment invalid ditolak.

Payroll:

1. Process payroll valid.
2. Duplicate payroll ditolak.
3. Hidden input tamper tidak mempengaruhi hasil.
4. Period lock menolak update.
5. Payslip hanya owner/admin.

Report:

1. Attendance status lengkap.
2. Leave overlap muncul.
3. Payroll summary benar.
4. CSV/PDF export.

## 33. Backlog Eksekusi Prioritas

### Sprint 1 - Login dan Access Guard

Task:

1. Ubah login field ke username/email.
2. Validasi password required.
3. Hapus `htmlspecialchars()` dari password verify.
4. Tambah session regenerate.
5. Tambah status akun.
6. Perbaiki authorization deny-by-default.
7. Perbaiki direct URL role admin/funsionariu.
8. Tambah null guard self-service.

Acceptance:

1. Login admin berhasil.
2. Login pegawai berhasil.
3. Cross-role URL blocked.
4. `funsionariu/perfil` tidak error 500 saat role salah.

### Sprint 2 - CSRF dan Mutasi Aman

Task:

1. Aktifkan CSRF.
2. Ubah delete departamentu ke DELETE.
3. Ubah delete pozisaun ke DELETE.
4. Ubah delete kategoria ke DELETE.
5. Ubah delete funsionariu ke DELETE/deactivate.
6. Ubah delete subsidiu ke DELETE.
7. Ubah delete avizu ke DELETE/archive.
8. Ubah retira sansaun ke POST.
9. Ubah logout ke POST.

Acceptance:

1. Semua link hapus GET hilang.
2. POST/DELETE tanpa CSRF ditolak.
3. UI masih bisa hapus lewat form valid.

### Sprint 3 - Database Cleanup

Task:

1. Tentukan tabel user utama.
2. Cleanup role pegawai.
3. Rename `salariu_detallu` kolom nilai menjadi `valor`.
4. Tambahkan unique `users.username`.
5. Tambahkan unique `salariu(funsionariu_id, fulan, tinan)`.
6. Pindahkan runtime DDL ke migration.
7. Tambahkan index report.

Acceptance:

1. `php spark migrate` berhasil.
2. Salary detail insert berhasil.
3. Duplicate payroll ditolak DB.
4. Tidak ada DDL di getter model.

### Sprint 4 - Attendance Core

Task:

1. Buat command auto absent.
2. Hapus auto absent dari `BaseController`.
3. Implement toleransi.
4. Implement status `Tardi`.
5. Implement status `Incomplete`.
6. Update report attendance.

Acceptance:

1. Membuka halaman tidak menulis attendance otomatis.
2. Clock in terlambat menghasilkan `Tardi`.
3. Report menghitung `Tardi`.

### Sprint 5 - Leave Core

Task:

1. Tambah leave overlap validation.
2. Tambah attachment validation.
3. Tambah reject reason.
4. Tambah approval history.
5. Tambah leave balance awal.
6. Update report leave overlap.

Acceptance:

1. Leave overlap ditolak.
2. Approved leave mengurangi balance.
3. Reject wajib alasan.
4. Report leave range benar.

### Sprint 6 - Payroll Core

Task:

1. Buat `PayrollService`.
2. Hitung payroll server-side.
3. Tambah payroll period.
4. Tambah payroll lock.
5. Tambah payslip view.
6. Tambah payroll audit log.

Acceptance:

1. Hidden input tampering tidak mempengaruhi payroll.
2. Period locked tidak bisa diubah.
3. Pegawai bisa melihat payslip sendiri.

### Sprint 7 - Audit, Anunsiu, dan Document

Task:

1. Buat audit log service.
2. Audit login, role access, payroll, leave, sanksi.
3. Satukan kebutuhan notification ke Anunsiu.
4. Buat avizu read receipt jika diperlukan.
5. Buat document upload aman.

Acceptance:

1. Aksi penting muncul di audit log.
2. Avizu unread count bekerja.
3. Dokumen protected tidak bisa diakses langsung.

## 34. Non-Functional Requirements

Security:

1. CSRF aktif.
2. Password hash memakai `password_hash()`.
3. Tidak menyimpan password plain text.
4. Session aman.
5. Upload file allowlist.
6. Protected download.
7. Deny-by-default authorization.

Performance:

1. Dashboard admin load < 3 detik untuk data normal.
2. Report besar memakai pagination/filter.
3. Index database untuk query report.
4. Tidak ada DDL runtime di request web.

Reliability:

1. Payroll process memakai transaction.
2. Leave approval memakai transaction.
3. Import memakai transaction.
4. Scheduled command idempotent.

Maintainability:

1. Model dipisah per domain.
2. Business logic kompleks pindah ke service.
3. Validation rules reusable.
4. Migration rapi.
5. Test minimal untuk workflow utama.

Usability:

1. Pesan error jelas.
2. UI tidak menampilkan fitur yang belum siap.
3. Konfirmasi aksi berisiko.
4. Bahasa konsisten.

## 35. RACI Implementasi

Owner produk:

1. Menentukan prioritas fitur.
2. Menyetujui workflow bisnis.
3. Menyetujui istilah bahasa UI.

Developer:

1. Implementasi kode.
2. Migration database.
3. Unit/feature test.
4. Perbaikan bug.

Admin HR:

1. Validasi workflow pegawai, attendance, leave, payroll.
2. Menentukan aturan toleransi, leave balance, dan sanksi.
3. UAT.

Auditor/Super admin:

1. Validasi audit log.
2. Validasi permission.
3. Validasi report.

## 36. Pertanyaan Bisnis yang Perlu Diputuskan

Auth:

1. Apakah login utama memakai username, email, atau keduanya?
2. Apakah register publik perlu ada?
3. Berapa lama session aktif?
4. Apakah butuh 2FA?

Attendance:

1. Jam kerja resmi berapa?
2. Toleransi keterlambatan berapa menit?
3. Apakah Sabtu/Minggu hari kerja?
4. Apakah pegawai boleh clock in saat terlambat melewati batas?
5. Apakah perlu lokasi GPS?

Leave:

1. Tipe leave apa saja?
2. Berapa jatah annual leave?
3. Apakah weekend/hari libur dihitung leave?
4. Approval oleh admin saja atau supervisor dulu?

Payroll:

1. Komponen salary apa saja?
2. Potongan absensi dihitung bagaimana?
3. Potongan sanksi maksimal berapa persen?
4. Kapan payroll dianggap locked?
5. Apakah perlu pajak?

Sanksi:

1. Berapa kali `Falta` memicu sanksi?
2. Apakah `Tardi` juga memicu sanksi?
3. Siapa yang boleh retira sanksi?

Report:

1. Format laporan resmi apa?
2. Apakah perlu tanda tangan digital/manual di PDF?
3. Siapa yang boleh export data salary?

## 37. Checklist Sebelum Go-Live

Security:

1. CSRF aktif.
2. Route mutasi GET hilang.
3. Role guard lulus test.
4. Login rate limit aktif.
5. Akun default password lemah diganti.
6. Error development dimatikan di production.

Database:

1. Backup tersedia.
2. Migration sukses.
3. Foreign key/index penting aktif.
4. Tidak ada kolom mojibake.
5. Data role pegawai benar.

Workflow:

1. Create/update pegawai sukses.
2. Clock in/out sukses.
3. Auto absent command sukses.
4. Leave request/approve/reject sukses.
5. Payroll process/payslip sukses.
6. Report export sukses.

UI:

1. Tidak ada tombol mati.
2. Tidak ada error console fatal.
3. Teks tidak mojibake.
4. Form menampilkan error field.

Operational:

1. Backup restore diuji.
2. Admin tahu cara reset password.
3. Scheduler/cron berjalan.
4. Log dan audit dapat dibaca.

## 38. Lampiran - Format Task Siap Eksekusi

Gunakan format ini saat mengambil item dari dokumen untuk dikerjakan:

```md
### Task: [Nama task]

Prioritas: P0/P1/P2/P3
Modul: Auth/Attendance/Leave/Payroll/etc
Tujuan:
- ...

File terdampak:
- ...

Perubahan:
- ...

Migration:
- Ya/Tidak

Acceptance criteria:
- ...

Test manual:
- ...

Risiko:
- ...
```

Contoh task:

```md
### Task: Perbaiki Login Username/Email

Prioritas: P0
Modul: Auth
Tujuan:
- User bisa login memakai username atau email.
- UI tidak lagi membatasi input harus email.

File terdampak:
- app/Views/pages/commons/login.php
- app/Controllers/Auth.php
- app/Models/ApplicationModel.php

Perubahan:
- Ganti input type email menjadi text.
- Rename label menjadi Username atau Email.
- Validasi login dan password required.
- Jangan mutate password sebelum password_verify.
- Regenerate session saat login sukses.

Migration:
- Tidak, kecuali menambah status akun.

Acceptance criteria:
- admin/admin123 bisa login.
- admin@gmail.com/admin123 bisa login jika akun ada.
- password kosong ditolak.
- password salah menampilkan pesan generik.

Test manual:
- Submit kosong.
- Login username benar.
- Login email benar.
- Login password salah.

Risiko:
- Perlu memastikan semua akun memakai username/email unik.
```

## 39. Penutup PRD

Dengan tambahan PRD ini, `analisis.md` sekarang dapat dipakai sebagai satu dokumen kerja:

1. Bagian 1-18 menjelaskan audit dan masalah.
2. Bagian 19-31 menjelaskan kebutuhan produk dan fitur tambahan.
3. Bagian 32-38 menjelaskan urutan eksekusi, definition of done, testing matrix, sprint backlog, checklist go-live, dan format task.

Rekomendasi eksekusi tetap dimulai dari fondasi keamanan dan konsistensi data sebelum menambah fitur besar. Jika langsung menambah fitur tanpa memperbaiki login, authorization, CSRF, migration, dan payroll calculation, risiko bug akan makin besar.

## 40. Status Eksekusi Implementasi - 2026-06-01

Bagian ini mencatat implementasi yang sudah dieksekusi ke kode lokal agar dokumen analisis tetap selaras dengan kondisi repository.

### 40.1 Fondasi login dan akses

Status: selesai untuk fondasi lokal.

Perubahan:

1. Login menerima username atau email.
2. Password tidak lagi dimutasi dengan `htmlspecialchars()` sebelum `password_verify()`.
3. Validasi login diperketat: login wajib diisi, password wajib diisi, panjang input dibatasi.
4. Session diregenerasi setelah login sukses.
5. Akun inactive/disabled/blocked ditolak.
6. Login gagal memakai pesan generik.
7. Counter gagal login dan `locked_until` ditambahkan; akun dikunci sementara setelah terlalu banyak kegagalan.
8. Login sukses/gagal/logout masuk ke `audit_logs`.
9. Role `administrador` dan `funsionariu` diarahkan ke dashboard masing-masing.
10. Direct URL admin/pegawai dijaga oleh filter authorization.
11. Akun admin utama dinormalisasi di tabel `users`: username `admin`, email `admin@gmail.com`, password lokal `admin123`.

Verifikasi:

1. `admin/admin123` berhasil masuk ke `http://localhost:8080/administrador/dashboard`.
2. `admin@gmail.com/admin123` berhasil masuk ke `http://localhost:8080/administrador/dashboard`.
3. GET `/logout` tidak lagi tersedia dan mengembalikan 404.
4. Akses admin ke area `funsionariu` diarahkan ke halaman blocked.

### 40.2 CSRF dan route mutasi aman

Status: selesai untuk route utama.

Perubahan:

1. CSRF global diaktifkan.
2. Semua form POST view memiliki `csrf_field()`.
3. Logout berubah menjadi POST.
4. Route delete master data, pegawai, subsidi, avizu, dan tipe sansaun berubah dari GET menjadi DELETE via form `_method`.
5. Route retira sansaun berubah dari GET menjadi POST.
6. Ajax global membaca token CSRF dari meta tag.

Verifikasi:

1. Script audit form mengembalikan: `OK: semua POST form view punya csrf_field()`.
2. `php spark routes` menampilkan logout sebagai POST dan aksi delete sebagai DELETE.

### 40.3 Database hardening

Status: selesai untuk fondasi migrasi lokal.

Perubahan:

1. Migration `HardeningFoundation` ditambahkan.
2. Kolom keamanan user ditambahkan: `email`, `status`, `failed_login_count`, `locked_until`, `last_login_at`, `last_login_ip`, `password_changed_at`.
3. Status pegawai dan user diselaraskan.
4. Tabel operasional dibuat: `audit_logs`, `employee_documents`, `leave_balances`, `payroll_periods`; tabel `notifications` tidak lagi dipakai sebagai modul aktif.
5. Struktur payroll diperbaiki dari kolom mojibake ke `valor`.
6. Enum presensi diperluas dengan `Tardi`, `Incomplete`, `Holiday`, `Weekend`.
7. Index unik payroll per pegawai/bulan/tahun ditambahkan.
8. Role pegawai yang sebelumnya salah sebagai admin diperbaiki menjadi `funsionariu`.

Verifikasi:

1. `php spark migrate:status` menunjukkan semua migration utama sudah migrated.
2. Tabel operasional baru ditemukan di database.
3. Komposisi role lokal: 1 administrator, 1 Developer, 20 funsionariu.

### 40.4 Attendance

Status: selesai untuk workflow inti.

Perubahan:

1. Auto absent tidak lagi berjalan di setiap request.
2. Command baru: `php spark attendance:mark-absent [YYYY-MM-DD]`.
3. Command tersebut membuat status `Falta` hanya untuk pegawai yang benar-benar tidak punya presensi dan tidak sedang leave approved.
4. Clock-in memakai toleransi dan menghasilkan `Prezente` atau `Tardi`.
5. Clock-out memvalidasi window waktu.
6. Presensi tanpa clock-out ditandai `Incomplete`, bukan otomatis `Falta`.
7. Setting toleransi tampil di halaman admin.

Verifikasi:

1. `php spark attendance:mark-absent` berjalan sukses.
2. Output lokal: `Attendance checked for 2026-06-01. Falta created: 0. Incomplete marked: 0.`

### 40.5 Leave/Lisensa

Status: selesai untuk workflow inti.

Perubahan:

1. Pegawai hanya dapat melihat dan mengajukan lisensa miliknya sendiri.
2. Form lisensa memvalidasi tipe, tanggal, alasan, dan file pendukung.
3. Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.
4. Leave overlap dengan leave pending/approved ditolak.
5. Leave untuk tanggal yang sudah `Prezente` atau `Tardi` ditolak.
6. Admin wajib memberi komentar ketika reject.
7. Approval leave membuat/memperbarui record presensi `Lisensa` tanpa menimpa hari yang sudah `Prezente`/`Tardi`.
8. Perubahan status leave masuk audit log dan mengirim notifikasi ke pegawai.

### 40.6 Payroll/Salariu

Status: selesai untuk workflow inti dan slip detail.

Perubahan:

1. Payroll tidak lagi mempercayai angka total dari client.
2. Server menghitung ulang salary base dari posisi, subsidi dari DB, potongan manual, dan potongan sansaun aktif.
3. Payroll duplicate per pegawai/bulan/tahun ditolak.
4. Detail payroll disimpan ke `salariu_detallu.valor`.
5. Sanksi potongan gaji otomatis mengurangi outstanding dan menutup status sanksi jika lunas.
6. Admin dan pegawai dapat membuka modal detail slip gaji.
7. Payroll sukses masuk audit log dan mengirim notifikasi ke pegawai.

### 40.7 Sansaun

Status: selesai untuk risiko utama.

Perubahan:

1. Retira sansaun memakai POST dan CSRF.
2. Retira menyimpan reason, actor, dan timestamp.
3. Tipe sansaun yang sudah dipakai tidak bisa dihapus.
4. Tipe sanksi dipastikan ada sebelum sanksi dibuat.

### 40.8 Anunsiu

Status: selesai untuk versi saat ini. Modul notifikasi terpisah dibatalkan karena kebutuhan pemberitahuan diarahkan ke menu Anunsiu/Avizu yang sudah ada.

Perubahan:

1. Avizu expired tidak lagi dihapus ketika dibaca.
2. Header menampilkan Anunsiu saja, tanpa halaman notifikasi terpisah.
3. Output judul/konten di header dan halaman Anunsiu sudah di-escape.
4. Route, controller, view, dan link `notifikasaun` dihapus dari aplikasi.

Backlog lanjutan:

1. Targeting anunsiu per role/departemen.
2. Read receipt anunsiu jika nanti diperlukan.

### 40.9 UI, tombol, dan JavaScript

Status: selesai untuk bug yang ditemukan saat audit lokal.

Perubahan:

1. Tombol update foto profil pegawai membuka modal dan menyimpan upload.
2. Tombol ubah password profil pegawai membuka modal dan memvalidasi password lama.
3. Tombol detail salary pegawai dan admin sekarang membuka modal breakdown.
4. DataTables tidak diinisialisasi ulang jika sudah aktif.
5. Header dropdown diberi null guard.
6. `public/assets/js/app.js` diberi guard agar inisialisasi Notyf tidak error saat `document.body` belum tersedia.
7. App JS diberi cache-busting `filemtime()`.
8. Footer teks tidak profesional dihapus.

Verifikasi browser:

1. Halaman salary admin terbuka.
2. Modal detail salary dapat dibuka.
3. Form salary memiliki input CSRF.

### 40.10 Dynamic menu hardening

Status: selesai untuk risiko utama.

Perubahan:

1. Create menu runtime tidak lagi membuat file controller/view.
2. Create menu runtime tidak lagi menulis langsung ke `Routes.php`.
3. Menu baru hanya menyimpan metadata; route/controller harus ditambahkan melalui kode/migration.

### 40.11 Reporting

Status: selesai untuk penambahan status presensi utama.

Perubahan:

1. Rekap presensi mencakup `Tardi` dan `Incomplete`.
2. Export CSV/PDF presensi mencakup `Tardi` dan `Incomplete`.
3. Query laporan lisensa memakai logika overlap tanggal, bukan hanya tanggal mulai.

### 40.12 Document Management

Status: selesai untuk versi inti.

Perubahan:

1. Route admin `administrador/documentu` ditambahkan.
2. Admin dapat upload dokumen pegawai ke `uploads/documentu`.
3. File yang diterima dibatasi ke PDF/JPG/PNG dan maksimal 5MB.
4. Admin dapat memilih visibilitas `admin_only` atau `employee_visible`.
5. Delete dokumen memakai soft delete via `deleted_at`.
6. Upload/delete dokumen masuk audit log.
7. Jika dokumen terlihat untuk pegawai, pegawai mengaksesnya melalui menu dokumen pegawai; notifikasi terpisah sudah tidak digunakan.
8. Route pegawai `funsionariu/dokumentu` ditambahkan untuk melihat dokumen yang visible.
9. Menu `Dokumentu` ditambahkan ke sidebar admin dan pegawai melalui migration.

### 40.13 Command dan cara menjalankan

Jalankan server lokal:

```bash
php spark serve
```

Jika shell tidak mengenali `php`, gunakan path lokal:

```bash
C:\php\php.exe spark serve
```

Jalankan migration:

```bash
C:\php\php.exe spark migrate
```

Jalankan auto absent manual:

```bash
C:\php\php.exe spark attendance:mark-absent
```

Login admin lokal yang sudah diverifikasi:

```text
Username: admin
Password: admin123
```

Alternatif username admin:

```text
Username: admin@gmail.com
Password: admin123
```

### 40.14 Verifikasi teknis terakhir

Perintah yang sudah dijalankan:

```bash
C:\php\php.exe -l app/**/*.php
C:\php\php.exe spark routes
C:\php\php.exe spark migrate:status
C:\php\php.exe spark attendance:mark-absent
node --check public/assets/js/app.js
```

Hasil:

1. Semua file PHP di `app` lolos syntax check.
2. Route logout/delete/retira sudah memakai method aman.
3. Migration status sudah lengkap.
4. Command presensi berjalan.
5. JavaScript bundle lolos syntax check.
6. Login admin berhasil.
7. Audit log mencatat login sukses dan login gagal.
8. Route document management tampil di `php spark routes`.
9. Halaman admin document management terbuka setelah login.

### 40.15 Status backlog setelah eksekusi lanjutan

Backlog operasional yang sebelumnya tersisa sudah dieksekusi pada 2026-06-01:

1. Halaman penuh untuk `audit_logs`: selesai melalui route `administrador/audit`, controller `Administrador::audit`, view `pages/administrador/audit.php`, dan menu admin.
2. Notifikasi terpisah sudah digabung ke Anunsiu: controller/view/route `notifikasaun` dihapus, dropdown header hanya menampilkan Anunsiu, dan menu `administrador/avizu` diberi label `Anunsiu`.
3. Leave balance otomatis per tahun: selesai melalui helper `ApplicationModel::ensureLeaveBalance`, `getLeaveBalances`, `recalculateLeaveBalance`, halaman admin `administrador/lisensa/balansu`, validasi sisa cuti pada pengajuan pegawai, dan recalculation saat approval.
4. Holiday calendar: selesai melalui tabel `holidays`, halaman `administrador/feriadu`, integrasi block clock-in pada hari libur, dan command `attendance:mark-absent` yang melewati hari libur.
5. Preview dokumen inline dan kategori dokumen konfigurabel: selesai melalui tabel `document_categories`, form kategori admin, dropdown kategori upload, preview modal admin/pegawai, dan menu document management.
6. Import/export pegawai: import CSV dan template selesai melalui `administrador/funsionariu/import` dan `administrador/funsionariu/template`; export pegawai tetap memakai modul laporan yang sudah ada.
7. Backup/restore dari UI: selesai melalui halaman `administrador/maintenance`, create backup SQL, download backup, upload restore SQL, dan audit log backup/restore.
8. Password reset admin untuk akun pegawai: selesai melalui tombol reset pada halaman pegawai dan route `administrador/funsionariu/reset-password/{id}`.
9. Perbaikan encoding/mojibake: source aplikasi di `app` dan `public` sudah dicek tidak mengandung marker mojibake utama (`Ã`, `Â`, `�`, `val¢`, `valór`); kolom payroll memakai `valor`. Catatan mojibake lama tetap ada di dokumen ini sebagai histori audit.
10. Automated test: selesai melalui `tests/unit/HrisImplementationTest.php`, mencakup hitung hari cuti, route aman, route backlog operasional, keberadaan view baru, guard workflow leave/payroll/document/holiday, CSRF form, dan marker mojibake.

### 40.16 Verifikasi eksekusi lanjutan

Perintah yang dijalankan pada 2026-06-01:

```bash
C:\php\php.exe spark migrate
C:\php\php.exe spark migrate:status
C:\php\php.exe spark routes
C:\php\php.exe -l app/**/*.php
node --check public/assets/js/app.js
C:\php\php.exe vendor\phpunit\phpunit\phpunit --no-coverage
```

Hasil verifikasi:

1. Migration `2026-06-01-000004_OperationalCompletion` berhasil masuk.
2. Tabel `holidays`, `document_categories`, `leave_balances`, dan `payroll_periods` tersedia.
3. Semua route backlog operasional tampil di `php spark routes`.
4. Semua POST form view memiliki `csrf_field()`.
5. Semua 123 file PHP di folder `app` lolos syntax check.
6. JavaScript `public/assets/js/app.js` lolos syntax check.
7. Full PHPUnit suite lokal lolos: `12 tests, 40 assertions`.
8. Smoke test browser lokal berhasil membuka halaman `administrador/audit`, `administrador/feriadu`, `administrador/lisensa/balansu`, `administrador/maintenance`, `administrador/salariu`, `administrador/funsionariu`, `administrador/documentu`, dan `administrador/avizu` tanpa error aplikasi.
9. Login admin tetap valid dengan username `admin` dan password `admin123`.

Status akhir: backlog implementasi dari analisis ini sudah ditutup untuk kebutuhan menjalankan sistem lokal dan workflow HRIS inti yang dibahas. Item yang masih dapat menjadi fase berikutnya bukan bug blocker, melainkan peningkatan enterprise seperti approval bertingkat lebih kompleks, shift multi-jadwal, 2FA, dan deployment production hardening.

### 40.17 Update penggabungan Notifikasaun ke Anunsiu

Status: selesai pada 2026-06-01 sesuai keputusan terbaru.

Perubahan:

1. Halaman dan controller `Notifikasaun` dihapus karena pemberitahuan sistem dipusatkan ke Anunsiu.
2. Route `notifikasaun`, `notifikasaun/read/{id}`, dan `notifikasaun/read-all` dihapus.
3. Header tidak lagi membaca tabel `notifications`; badge dan dropdown hanya menghitung data Anunsiu/Avizu.
4. Aksi leave approval, proses salariu, dan upload dokumen tidak lagi membuat notifikasi personal yang terpisah.
5. Menu database `administrador/avizu` diperbarui menjadi label `Anunsiu` melalui migration `2026-06-01-000006_MergeNotificationsIntoAnunsiu`.
6. Tabel legacy `notifications` dihapus melalui migration `2026-06-01-000007_DropLegacyNotificationsTable`.
7. View Anunsiu memakai label Tetun `Anunsiu`, validasi panjang input, serta escaping untuk judul/konten agar aman saat dipakai sebagai pusat pemberitahuan.
8. PHPUnit diperbarui untuk memastikan modul notifikasi terpisah tidak muncul lagi dan header mengarah ke `administrador/avizu`.
