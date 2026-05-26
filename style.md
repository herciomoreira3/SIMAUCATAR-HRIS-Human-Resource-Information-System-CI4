# UI Design System & Style Guide (Next.js Inspired)

Dokumen ini berisi panduan gaya UI/UX untuk aplikasi ini dengan mengadopsi gaya modern yang bersih, minimalis, dan interaktif seperti yang sering ditemukan pada aplikasi Next.js (misalnya desain Vercel). Panduan ini dibuat sangat rinci agar mudah diimplementasikan oleh junior developer maupun AI agent.

## 1. Filosofi Desain
*   **Minimalis & Bersih:** Banyak *white space*, pembatas menggunakan garis tipis (*subtle borders*) daripada blok warna tebal.
*   **Interaktif:** Setiap elemen yang bisa diklik (tombol, link, ikon) harus memiliki efek *hover* dan *active* yang halus (transisi 150ms - 200ms).
*   **Feedback Visual:** Elemen seperti *dropdown* harus memiliki animasi masuk (masuk memudar/meluncur dari atas) dan *shadow* (bayangan) yang lembut untuk memberikan kedalaman (*depth*).
*   **Glassmorphism (Opsional/Aksen):** Penggunaan latar belakang agak transparan dengan efek *blur* pada *header* atau *dropdown* untuk kesan premium.

## 2. Palet Warna (CSS Variables)
Gunakan variabel CSS ini di akar (`:root`) agar konsisten di seluruh halaman.

```css
:root {
  /* Backgrounds */
  --bg-primary: #ffffff;      /* Latar belakang utama aplikasi */
  --bg-secondary: #f9fafb;    /* Latar belakang bagian tertentu/hover */
  --bg-hover: #f3f4f6;        /* Latar belakang saat elemen di-hover */
  --bg-dropdown: #ffffff;     /* Latar belakang dropdown menu */

  /* Text Colors */
  --text-primary: #111827;    /* Teks utama (Hitam pekat) */
  --text-secondary: #6b7280;  /* Teks sekunder/deskripsi (Abu-abu) */
  --text-muted: #9ca3af;      /* Teks tidak aktif/placeholder */

  /* Accents & States */
  --accent-color: #000000;    /* Warna utama brand (ala Vercel/Next.js: Hitam/Putih) */
  --accent-hover: #374151;
  --danger-color: #ef4444;    /* Warna untuk tombol logout/hapus */
  --danger-bg-hover: #fef2f2; /* Background hover tombol danger */
  
  /* Borders */
  --border-light: #e5e7eb;    /* Garis pemisah yang halus */
  
  /* Shadows */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-dropdown: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0, 0, 0, 0.05);
}
```

## 3. Tipografi
*   **Font Family:** Gunakan `Inter`, `Roboto`, atau standar *sans-serif* sistem.
*   **CSS:** `font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;`
*   **Ukuran:**
    *   Teks biasa: `14px`
    *   Judul/Header Menu: `16px` dengan `font-weight: 500` (Medium).

## 4. Animasi & Transisi
Setiap elemen interaktif wajib menyertakan transisi.

```css
/* Transisi Standar untuk hover tombol/ikon */
.transition-standard {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Animasi masuk untuk Dropdown (Fade in + Slide down) */
@keyframes dropdownEnter {
  from {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.dropdown-animate-in {
  animation: dropdownEnter 0.2s ease-out forwards;
}
```

---

## 5. Komponen Header (Fokus Utama Saat Ini)

Header harus terlihat premium, statis di atas (opsional *sticky*), dengan pinggiran bawah (*border-bottom*) yang halus.

### A. Struktur Umum Header
*   **Tinggi (Height):** Sekitar `64px`.
*   **Padding:** Kiri & Kanan `24px` (`padding: 0 24px;`).
*   **Layout:** Flexbox (`display: flex; justify-content: space-between; align-items: center;`).
*   **Border Bottom:** `border-bottom: 1px solid var(--border-light);`
*   **Background:** Putih pekat atau transparan dengan *backdrop-blur* (Glassmorphism).

### B. Ikon Lonceng (Notifikasi / Avizu)
*   **Tautan (Link):** Harus mengarah ke halaman `avizu` (misal: `/avizu` atau `<?= base_url('avizu') ?>`).
*   **Ikon:** Gunakan ikon lonceng modern (SVG atau library seperti Lucide/Heroicons).
*   **Styling & Hover:**
    *   Warna awal: `var(--text-secondary)`.
    *   Hover: Warna berubah menjadi `var(--text-primary)`, latar belakang ikon menjadi bulat abu-abu terang (`var(--bg-hover)`).
    *   Terdapat indikator titik merah (*badge*) jika ada notifikasi baru.

### C. Profil User (Avatar & Dropdown)
Komponen ini terdiri dari tombol *trigger* (avatar) dan menu *dropdown*.

**1. Tombol Trigger (Avatar)**
*   **Gambar:** Mengambil langsung dari foto akun *user* (*database*). Harus berbentuk bulat sempurna (`border-radius: 50%`).
*   **Ukuran:** `32px` x `32px` atau `36px` x `36px`.
*   **Object Fit:** `object-fit: cover;` agar gambar tidak gepeng.
*   **Efek Hover:** Muncul *ring* (cincin) abu-abu di sekitar avatar, atau kursor berubah menjadi pointer.

**2. Menu Dropdown**
*   **Posisi:** Absolut terhadap pembungkus (*wrapper*) avatar. Berada di pojok kanan bawah avatar (`right: 0; top: calc(100% + 8px);`).
*   **Bentuk:** Kotak bersudut melengkung (`border-radius: 8px;`), latar belakang putih (`var(--bg-dropdown)`).
*   **Bayangan (Shadow):** Menggunakan `var(--shadow-dropdown)` agar terlihat mengambang di atas konten lain.
*   **Isi Menu:**
    *   Setiap *item* (baris menu) memiliki padding: `8px 16px`.
    *   Layout item: Flexbox, `align-items: center; gap: 12px;` untuk spasi antara ikon dan teks.
    *   Teks ukuran `14px`.
*   **Hover Menu Item:**
    *   Saat kursor diarahkan, latar belakang baris berubah menjadi `var(--bg-hover)`.
    *   **Khusus Tombol Logout:** Teks dan ikon harus berwarna merah (`var(--danger-color)`), dan saat di-hover latar belakangnya menjadi merah sangat muda (`var(--danger-bg-hover)`).
*   **Ikon pada Menu:** Setiap menu (Profile, Settings, Logout) **wajib** berdampingan dengan ikon yang relevan dan gaya garis (outline style).

### Panduan Implementasi Cepat untuk Developer / AI:
1.  Bungkus bagian kanan header dengan kontainer `flex`, `align-items: center`, `gap: 20px`.
2.  Pasang ikon Lonceng di dalam tag `<a>` yang mengarah ke `avizu`.
3.  Buat `<div class="relative">` untuk membungkus Avatar.
4.  Tambahkan JavaScript sederhana untuk *toggle* (buka/tutup) *class* pada *dropdown* saat Avatar diklik, serta tutup *dropdown* jika klik di luar area (*click outside*).
5.  Gunakan animasi CSS `@keyframes` yang sudah disediakan di atas saat menampilkan *dropdown*.

---
*Catatan: Dokumen ini khusus untuk panduan gaya UI (Style Guide). Jangan mengubah file kode `header.php` sebelum diinstruksikan lebih lanjut.*
