# Implementation Plan: Integrasi Kouventa Website ke CTI CMS (Laravel 13)

**Project:** Kouventa Website  
**CMS Path:** `C:\laragon\www\kouventa`  
**Branch:** `project/kouventa`  
**Local Domain:** `https://kouventa.devs`  
**Acuan Desain:** `c:\Users\fredo\Documents\data gilang\webdev\altia dev\kouventa-website\clean-html`  

---

## 1. Modul 1: CPT & MetaBoxes
* **Blog / Insight**: Menggunakan core plugin `posts` (`App\Models\Post`) sesuai **Rule 8 (CPT vs Blog Plugin)**. *Dilarang membuat CPT baru untuk blog.*
* **Fail-Safe Plugin Check**: Mengikuti **Rule 15 (Plugin Status Fail-Safe Rule)**, template `insight.blade.php` wajib memeriksa `is_plugin_active('posts')` sebelum merender collection artikel.
* **Sections & Blocks**: Halaman-halaman landing page menggunakan Page Blocks / Structured JSON metadata pada `pages.meta` agar mudah dikelola melalui admin panel tanpa memerlukan CPT berlebih.

---

## 2. Modul 2: Daftar Halaman / Pages Database (`Page` Model)
Semua halaman akan dijadikan **Dedicated Pages** dengan routing bersih:

| Halaman | Slug URL | Template Blade | Keterangan |
|---|---|---|---|
| **Homepage** | `/` | `pages/home.blade.php` | Dual-track Hero, Team Selector, Industry Showcase, Security |
| **Product AI** | `/product/ai` | `pages/product-ai.blade.php` | Enterprise Wikipedia, Task Force Agents, Governance |
| **Product Customer Engagement** | `/product/customer-engagement` | `pages/product-ce.blade.php` | WhatsApp Business API, Omnichannel Inbox, Auto Routing |
| **Solution AI** | `/solutions/ai` | `pages/solution-ai.blade.php` | Solusi 6 Departemen (Support, Sales, Ops, HR, Finance, Legal) |
| **Solution Customer Engagement** | `/solutions/customer-engagement` | `pages/solution-ce.blade.php` | Solusi Omnichannel Customer Experience |
| **Pricing** | `/pricing` | `pages/pricing.blade.php` | Pricing Tiers & WhatsApp Cost Calculator (Alpine.js) |
| **About Us** | `/about` | `pages/about.blade.php` | Milestones & Sejarah Evolusi Kouventa |
| **Insight (Blog)** | `/insight` | `pages/insight.blade.php` | Thought Leadership & Artikel Blog (`plugins/posts/`) |
| **Book a Demo** | `/book-demo` | `pages/book-demo.blade.php` | Form Konsultasi Enterprise (Slot Form Studio) |

---

## 3. Modul 3: Form Studio Engine & Fields
Sesuai **Rule 3 & 4 (Form Translation & Compliance)**:
* **Deklarasi Slot Tema (`theme.json`)**:
  ```json
  "form_placeholders": [
    {
      "key": "book_demo_form",
      "label": "Book a Demo Form",
      "description": "Form konsultasi enterprise pada halaman Book a Demo"
    }
  ]
  ```
* **Field Form Studio (`Form` & `FormField` Model)**:
  1. `full_name` (Text, Required, Label EN: "Full Name" / ID: "Nama Lengkap")
  2. `work_email` (Email, Required, Label EN: "Work Email" / ID: "Email Kantor")
  3. `phone_whatsapp` (Tel, Required, Label EN: "Phone / WhatsApp" / ID: "Nomor WhatsApp")
  4. `company_name` (Text, Required, Label EN: "Company Name" / ID: "Nama Perusahaan")
  5. `team_size` (Select: "1-10", "11-50", "51-200", "201-500", "500+", Label: "Company Size")
  6. `interest` (Select: "Artificial Intelligence", "Customer Engagement", "Both", Label: "Primary Interest")
  7. `notes` (Textarea, Optional, Label EN: "Tell us about your use case" / ID: "Kebutuhan Anda")
* **Render & Submission**:
  * Dirender via template partial `themes/kouventa/views/partials/tailwind-form.blade.php`.
  * Action submit otomatis mengarah ke endpoint `route('forms.submit', $form->slug)`.
  * Data terkirim otomatis masuk ke tabel `form_entries` dan dapat dilihat di admin panel `/ctrlpanel/forms`.

---

## 4. Modul 4: Settings & String Translations (`t()`)
* **955 Pasang String Bilingual**: Telah diekstrak bersih dari prototipe ke `clean-html/translations/dictionary.json`.
* **Mekanisme Pemuatan**:
  * Dibuatkan seeder / command `php artisan kouventa:import-translations` untuk memasukkan seluruh string ke tabel `string_translations` dengan group `kouventa`.
  * Di dalam Blade views, teks dipanggil menggunakan helper CMS standar: `{{ t('kouventa.' . $key, $fallback) }}`.
* **Theme Settings (`settings` table)**:
  * WhatsApp Business Hotline number
  * Social Media Links (LinkedIn, Instagram)
  * Default Meta Title & Description per locale (`en`, `id`)

---

## 5. Modul 5: SEO Meta & Breadcrumbs
* Mengintegrasikan komponen native CMS:
  * `<x-seo-meta :model="$page" />` di `<head>` pada `layouts/app.blade.php`.
  * `<x-seo-breadcrumbs />` pada sub-halaman (`Product`, `Solution`, `About`, `Insight`).
* **Canonical URL & Multi-locale alternate tags**:
  * `<link rel="alternate" hreflang="en" href="..." />`
  * `<link rel="alternate" hreflang="id" href="..." />`
* **Social Graph Tags**: OpenGraph dan Twitter Card dengan logo Kouventa dan image preview per halaman.

---

## 6. Modul 6: Local Vendor Assets & MediaService Pipeline
* **Mandatory Local Asset Rule (Rule 10 - No External CDN)**:
  * **Fonts**: 7 varian Plus Jakarta Sans WOFF2 disimpan lokal di `themes/kouventa/assets/fonts/` dan dimuat via CSS `@font-face`.
  * **Vendor Scripts**:
    * **Alpine.js v3**: Disimpan lokal di `themes/kouventa/assets/js/alpine.min.js` untuk mengontrol dropdown navigasi dan kalkulator WhatsApp.
* **Asset Media Pipeline (Rule 9 & Helper Rule)**:
  * **Static Assets (Logo & Ikon)**: Disimpan di `themes/kouventa/assets/images/` dan dipanggil di Blade menggunakan helper `{{ theme_asset('images/kouventa-logo.png') }}`.
  * **Content Images (Mockup & Foto Tim)**: Diimport via `php artisan media:import` atau diproses melalui `MediaService`, terdaftar di tabel `media`, otomatis dikonversi ke WebP responsif (`sm`, `md`, `lg`, `xl`), dan dipanggil menggunakan `<x-image>`.
* **Theme Publishing**:
  * Eksekusi `php artisan theme:publish kouventa` untuk mempublikasikan aset ke `public/themes/kouventa/assets/`.

---

## 7. Tahapan Eksekusi Bertahap

- [ ] **Fase 1: Scaffold Tema Kouventa**
  - Buat folder `themes/kouventa/` lengkap dengan `theme.json` dan `KouventaThemeServiceProvider.php`.
  - Salin aset lokal (`images/`, `fonts/`, dan `alpine.min.js`) ke `themes/kouventa/assets/`.
  - Terbitkan aset dengan `php artisan theme:publish kouventa`.

- [ ] **Fase 2: Layout Global & Partials (Header & Footer)**
  - Buat `views/layouts/app.blade.php` (responsive canvas, font setup, SEO meta, flash message).
  - Buat `views/partials/header.blade.php` dengan Alpine.js untuk dropdown menu navigasi dan locale switcher.
  - Buat `views/partials/footer.blade.php`.

- [ ] **Fase 3: Konversi 9 Halaman Blade (Zero-Approximation Pixel-Perfect)**
  - Konversi `home.blade.php` (tab tim dinamis).
  - Konversi `product-ai.blade.php` & `product-ce.blade.php`.
  - Konversi `solution-ai.blade.php` & `solution-ce.blade.php`.
  - Konversi `pricing.blade.php` + buat widget kalkulator WhatsApp Alpine.js.
  - Konversi `about.blade.php`.
  - Konversi `insight.blade.php` dengan fail-safe check `is_plugin_active('posts')`.
  - Konversi `book-demo.blade.php` dan buat renderer `partials/tailwind-form.blade.php`.

- [ ] **Fase 4: Database Seeding & Form Studio Setup**
  - Seeding 9 Halaman ke tabel `pages`.
  - Seeding form "Book a Demo" ke tabel `forms` dan `form_fields`.
  - Seeding 955 string terjemahan ke tabel `string_translations`.

- [ ] **Fase 5: Verifikasi & Audit Kualitas**
  - Mandatory Partials Audit (Header, Body, Footer) ter-diff 1-to-1 dengan file acuan.
  - Verifikasi responsivitas di desktop (1440px canvas) dan mobile (<1024px).
  - Verifikasi fungsionalitas pengiriman form & perhitungan kalkulator WhatsApp di `https://kouventa.devs`.
