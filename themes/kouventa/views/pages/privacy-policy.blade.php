@extends('kouventa::layouts.app')

@php
    $isIdLocale = app()->getLocale() === 'id';
    $pageTitle = $isIdLocale ? 'Kebijakan Privasi' : 'Privacy Policy';
    $heroBadge = $isIdLocale ? 'KEAMANAN & PRIVASI' : 'SECURITY & PRIVACY';
    $heroSubtitle = $isIdLocale
        ? 'Komitmen PT Helios Informatika Nusantara dalam melindungi dan mengelola data pribadi Anda pada platform Kouventa dan WhatsApp Business.'
        : 'Our commitment at PT Helios Informatika Nusantara to safeguarding your personal data across Kouventa and WhatsApp Business solutions.';
    $lastUpdated = $isIdLocale ? 'Terakhir diperbarui: 24 Oktober 2026' : 'Last updated: October 24, 2026';
@endphp

@push('styles')
<style>
    .kv-legal-hero {
        background: #071C33;
        color: #FFFFFF;
        position: relative;
        overflow: hidden;
    }
    .kv-legal-hero-glow {
        position: absolute;
        right: -240px;
        top: -200px;
        width: 900px;
        height: 900px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,0.07);
        pointer-events: none;
    }
    .kv-legal-hero-inner {
        padding: 80px 80px 140px;
        max-width: 1280px;
        margin: 0 auto;
        width: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
    }
    .kv-legal-wrap {
        max-width: 1280px;
        margin: -60px auto 120px;
        padding: 0 80px;
        position: relative;
        z-index: 2;
        box-sizing: border-box;
    }
    .kv-legal-grid {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 36px;
        align-items: start;
    }
    .kv-legal-sidebar {
        position: sticky;
        top: 96px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .kv-legal-toc-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid #E2E8F0;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(7,28,51,0.05);
    }
    .kv-legal-toc-title {
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748B;
        margin-bottom: 14px;
        display: block;
    }
    .kv-legal-toc-nav {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .kv-legal-toc-link {
        font-size: 13.5px;
        line-height: 1.45;
        color: #4A5866;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: block;
    }
    .kv-legal-toc-link:hover,
    .kv-legal-toc-link.active {
        color: #071C33;
        background: #F1F5F9;
        font-weight: 600;
        padding-left: 14px;
        border-left: 3px solid #2BD46A;
    }
    .kv-legal-badge-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid #E2E8F0;
        padding: 20px 24px;
        box-shadow: 0 10px 30px rgba(7,28,51,0.05);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .kv-legal-contact-card {
        background: linear-gradient(135deg, #071C33 0%, #0E294B 100%);
        color: #FFFFFF;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 14px 34px rgba(7,28,51,0.12);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .kv-legal-card-body {
        background: #FFFFFF;
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        padding: 48px 56px;
        box-shadow: 0 18px 45px rgba(7,28,51,0.05);
    }
    .kv-legal-prose {
        font-size: 15px;
        line-height: 1.75;
        color: #334155;
    }
    .kv-legal-prose h2 {
        font-size: 24px;
        font-weight: 800;
        color: #071C33;
        letter-spacing: -0.02em;
        margin: 40px 0 16px;
        padding-top: 24px;
        border-top: 1px solid #F1F5F9;
        scroll-margin-top: 110px;
    }
    .kv-legal-prose h2:first-of-type {
        margin-top: 0;
        padding-top: 0;
        border-top: none;
    }
    .kv-legal-prose h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0F172A;
        margin: 24px 0 12px;
        scroll-margin-top: 110px;
    }
    .kv-legal-prose p {
        margin: 0 0 16px;
    }
    .kv-legal-prose ul,
    .kv-legal-prose ol {
        margin: 0 0 20px;
        padding-left: 24px;
    }
    .kv-legal-prose li {
        margin-bottom: 8px;
    }
    .kv-legal-prose li::marker {
        color: #2BD46A;
    }
    .kv-legal-prose a {
        color: #13803F;
        text-decoration: underline;
        text-underline-offset: 3px;
        font-weight: 500;
    }
    .kv-legal-prose a:hover {
        color: #0F5132;
    }
    .kv-legal-callout {
        background: #F0FDF4;
        border-left: 4px solid #2BD46A;
        padding: 16px 20px;
        border-radius: 8px;
        margin: 24px 0;
        color: #166534;
        font-size: 14.5px;
    }
    @media (max-width: 1024px) {
        .kv-legal-hero-inner {
            padding: 60px 24px 110px;
        }
        .kv-legal-wrap {
            padding: 0 24px;
            margin-top: -40px;
        }
        .kv-legal-grid {
            grid-template-columns: 1fr;
            gap: 28px;
        }
        .kv-legal-sidebar {
            position: static;
        }
        .kv-legal-card-body {
            padding: 32px 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="kv-canvas-wrap">
    <!-- ============ HERO SECTION ============ -->
    <section class="kv-legal-hero">
        <div class="kv-legal-hero-glow"></div>
        <div class="kv-legal-hero-inner">
            <!-- Mandatory Rule 2: Component Breadcrumb -->
            <x-seo-breadcrumbs :entity="$page" class="text-white/70 mb-2" />

            <span style="font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; color: #93A4B6">
                {{ $heroBadge }}
            </span>
            <h1 style="margin: 0; font-size: 52px; line-height: 1.1; font-weight: 800; letter-spacing: -0.03em">
                {{ $pageTitle }}
            </h1>
            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #93A4B6; max-width: 780px">
                {{ $heroSubtitle }}
            </p>
            <div style="display: flex; align-items: center; gap: 12px; margin-top: 4px; font-size: 13px; color: #64748B">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #2BD46A"></span>
                <span>{{ $lastUpdated }}</span>
            </div>
        </div>
    </section>

    <!-- ============ MAIN CONTENT AREA ============ -->
    <section class="kv-legal-wrap">
        <div class="kv-legal-grid">
            <!-- Sticky Sidebar: Table of Contents -->
            <aside class="kv-legal-sidebar">
                <div class="kv-legal-toc-card">
                    <span class="kv-legal-toc-title">{{ $isIdLocale ? 'Daftar Isi' : 'Table of Contents' }}</span>
                    <nav class="kv-legal-toc-nav" id="legal-toc">
                        @if($isIdLocale)
                            <a href="#pendahuluan" class="kv-legal-toc-link">1. Pendahuluan</a>
                            <a href="#komunikasi-whatsapp" class="kv-legal-toc-link">2. WhatsApp Business & Kouventa</a>
                            <a href="#data-dikumpulkan" class="kv-legal-toc-link">3. Data yang Dikumpulkan</a>
                            <a href="#metode-pengumpulan" class="kv-legal-toc-link">4. Metode Pengumpulan Data</a>
                            <a href="#tujuan-penggunaan" class="kv-legal-toc-link">5. Tujuan Penggunaan</a>
                            <a href="#keamanan-data" class="kv-legal-toc-link">6. Keamanan & Enkripsi Data</a>
                            <a href="#berbagi-data" class="kv-legal-toc-link">7. Pembagian Data Pihak Ketiga</a>
                            <a href="#transfer-lintas-negara" class="kv-legal-toc-link">8. Transfer Data Lintas Negara</a>
                            <a href="#hak-anda" class="kv-legal-toc-link">9. Hak-Hak Anda (PDP)</a>
                            <a href="#retensi-data" class="kv-legal-toc-link">10. Retensi & Penyimpanan</a>
                            <a href="#perubahan-kebijakan" class="kv-legal-toc-link">11. Perubahan Kebijakan</a>
                            <a href="#hubungi-kami" class="kv-legal-toc-link">12. Hubungi Kami</a>
                        @else
                            <a href="#introduction" class="kv-legal-toc-link">1. Introduction</a>
                            <a href="#data-collected" class="kv-legal-toc-link">2. Personal Data We Collect</a>
                            <a href="#collection-methods" class="kv-legal-toc-link">3. Collection Methods</a>
                            <a href="#purposes" class="kv-legal-toc-link">4. Purpose of Processing</a>
                            <a href="#data-sharing" class="kv-legal-toc-link">5. Data Sharing & Disclosure</a>
                            <a href="#cross-border" class="kv-legal-toc-link">6. Cross-Border Transfers</a>
                            <a href="#security" class="kv-legal-toc-link">7. Security & Protection</a>
                            <a href="#retention" class="kv-legal-toc-link">8. Storage & Retention</a>
                            <a href="#modifications" class="kv-legal-toc-link">9. Policy Modifications</a>
                            <a href="#your-rights" class="kv-legal-toc-link">10. Your Privacy Rights</a>
                            <a href="#third-party" class="kv-legal-toc-link">11. Social & Third-Party</a>
                            <a href="#contact" class="kv-legal-toc-link">12. Contacting Us</a>
                        @endif
                    </nav>
                </div>

                <!-- Compliance & Certification Card -->
                <div class="kv-legal-badge-card">
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #64748B">
                        {{ $isIdLocale ? 'STANDAR KEPATUHAN' : 'COMPLIANCE STANDARDS' }}
                    </span>
                    <div style="display: flex; gap: 14px; align-items: center">
                        <img src="{{ theme_asset('images/badge-iso-27001.webp') }}?v=20261026" alt="{{ t('kouventa.Sertifikasi Keamanan Informasi ISO/IEC 27001 Resmi Kouventa', 'Sertifikasi Keamanan Informasi ISO/IEC 27001 Resmi Kouventa') }}" style="height: 48px; width: 48px; object-fit: contain">
                        <img src="{{ theme_asset('images/badge-komdigi.webp') }}?v=20261026" alt="{{ t('kouventa.Tanda Daftar Penyelenggara Sistem Elektronik Resmi Kementerian Komunikasi dan Digital (Komdigi)', 'Tanda Daftar Penyelenggara Sistem Elektronik Resmi Kementerian Komunikasi dan Digital (Komdigi)') }}" style="height: 48px; width: 48px; object-fit: contain">
                    </div>
                    <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #64748B">
                        {{ $isIdLocale ? 'Sistem Manajemen Keamanan Informasi ISO/IEC 27001 terakreditasi CBQA Global & Terdaftar Resmi di Komdigi.' : 'ISO/IEC 27001 Information Security Management accredited by CBQA Global & registered with Komdigi.' }}
                    </p>
                </div>

                <!-- Contact & Assistance Card -->
                <div class="kv-legal-contact-card">
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: #2BD46A">
                        {{ $isIdLocale ? 'PERLINDUNGAN DATA' : 'DATA PROTECTION' }}
                    </span>
                    <h4 style="margin: 0; font-size: 17px; font-weight: 700; line-height: 1.3">
                        {{ $isIdLocale ? 'Ingin mengelola data Anda?' : 'Want to manage your personal data?' }}
                    </h4>
                    <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #B9C6D3">
                        {{ $isIdLocale ? 'Hubungi Data Protection Officer kami untuk permintaan akses, perbaikan, atau penghapusan data.' : 'Contact our Data Protection Officer for inquiries, access requests, or data erasure.' }}
                    </p>
                    <a href="mailto:kouventa@helios.id" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px; border-radius: 10px; background: #2BD46A; color: #071C33; font-weight: 700; font-size: 13.5px; text-decoration: none; transition: opacity 0.2s">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                        <span>kouventa@helios.id</span>
                    </a>
                </div>
            </aside>

            <!-- Main Legal Document Body -->
            <main class="kv-legal-card-body">
                <article class="kv-legal-prose">
                    @if($isIdLocale)
                        <!-- ==================== BAHASA INDONESIA ==================== -->
                        <h2 id="pendahuluan">1. Pendahuluan & Komitmen Privasi</h2>
                        <p>Di <strong>PT HELIOS INFORMATIKA NUSANTARA</strong> (anggota CTI Group) beserta afiliasinya, kami berkomitmen penuh untuk menjaga privasi, integritas, dan keamanan data pribadi Anda saat Anda berinteraksi dengan kami melalui situs web, WhatsApp Business, dan seluruh ekosistem produk Kouventa.</p>
                        <p>Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, memproses, menyimpan, dan melindungi data pribadi Anda sesuai dengan <strong>Undang-Undang Republik Indonesia No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong> serta standar internasional keamanan data ISO/IEC 27001.</p>

                        <div class="kv-legal-callout">
                            <strong>Ringkasan Komitmen:</strong> Kami tidak pernah menjual data pribadi Anda kepada pihak ketiga mana pun. Seluruh pemrosesan data dilakukan semata-mata untuk penyediaan layanan enterprise yang andal, aman, dan mematuhi regulasi yang berlaku.
                        </div>

                        <h2 id="komunikasi-whatsapp">2. Komunikasi melalui WhatsApp Business & Kouventa</h2>
                        <p>Kami menggunakan <strong>Kouventa</strong>—sebuah platform Customer Engagement dan Enterprise AI terintegrasi resmi berbasis Meta WhatsApp Business API—untuk memperlancar dan meningkatkan kualitas komunikasi dengan klien dan prospek.</p>
                        <p>Saluran WhatsApp Business memungkinkan kami untuk mengelola pertanyaan secara efisien, memberikan respons interaktif tepat waktu, dan memfasilitasi demo produk. Penggunaan saluran komunikasi ini sepenuhnya bersifat sukarela; Anda tetap dapat menghubungi kami melalui formulir web, telepon kantor, atau email resmi kami.</p>

                        <h2 id="data-dikumpulkan">3. Data Pribadi yang Kami Kumpulkan</h2>
                        <p>Saat Anda berinteraksi dengan situs web atau layanan Kouventa, kami dapat mengumpulkan kategori data berikut:</p>
                        <ul>
                            <li><strong>Identitas & Kontak Pribadi:</strong> Nama lengkap, alamat email korporat, nomor telepon/WhatsApp, nama perusahaan, jabatan/posisi, dan departemen yang Anda cantumkan pada formulir pendaftaran, book demo, atau pesan WhatsApp.</li>
                            <li><strong>Konten Percakapan & Pertanyaan:</strong> Seluruh pesan teks, lampiran dokumen, atau pertanyaan yang dikirimkan saat berdiskusi dengan tim kami atau bot cerdas Kouventa.</li>
                            <li><strong>Informasi Teknis & Perangkat:</strong> Jenis browser, sistem operasi, alamat IP, preferensi bahasa, dan data log teknis yang dikumpulkan secara otomatis saat Anda menavigasi situs web kami.</li>
                            <li><strong>Data Analitik & Penggunaan:</strong> Metrik interaksi, durasi kunjungan halaman, tautan yang diklik, dan status penerimaan pesan.</li>
                        </ul>

                        <h2 id="metode-pengumpulan">4. Metode Pengumpulan Data</h2>
                        <p>Kami memperoleh data pribadi melalui saluran-saluran berikut:</p>
                        <ul>
                            <li><strong>Komunikasi Sukarela:</strong> Data yang Anda berikan secara sadar saat mengisi formulir <em>Book a Demo</em>, konsultasi harga, berlangganan wawasan (newsletter), atau mengirimkan pesan WhatsApp.</li>
                            <li><strong>Proses Otomatis & Kuki:</strong> Melalui kuki (cookies) dan tag analitik lokal untuk memantau performa situs dan memfasilitasi interaksi antarmuka yang cepat dan responsif.</li>
                            <li><strong>Alat Dukungan Bisnis Resmi:</strong> Integrasi dengan platform CRM dan API resmi seperti Meta WhatsApp Business Cloud API.</li>
                        </ul>

                        <h2 id="tujuan-penggunaan">5. Tujuan Penggunaan Data Pribadi Anda</h2>
                        <p>Data pribadi yang dikumpulkan digunakan semata-mata untuk:</p>
                        <ol>
                            <li><strong>Menanggapi Pertanyaan & Kebutuhan Klien:</strong> Memberikan dukungan teknis, demonstrasi produk Kouventa, dan konsultasi arsitektur enterprise.</li>
                            <li><strong>Meningkatkan Kualitas Layanan:</strong> Menganalisis efektivitas alur interaksi dan menyempurnakan solusi kecerdasan buatan (Gen-AI dan Agentic AI).</li>
                            <li><strong>Keamanan & Pencegahan Penipuan:</strong> Memverifikasi identitas, mendeteksi aktivitas mencurigakan, dan menjaga keandalan jaringan infrastruktur kami.</li>
                            <li><strong>Pemberitahuan & Edukasi Produk:</strong> Mengirimkan informasi pembaruan fitur, studi kasus industri, atau undangan seminar teknologi dengan opsi berhenti berlangganan (opt-out) kapan saja.</li>
                        </ol>

                        <h2 id="keamanan-data">6. Keamanan & Enkripsi Data</h2>
                        <p>Keamanan informasi adalah pilar utama kami. PT Helios Informatika Nusantara telah menerapkan Sistem Manajemen Keamanan Informasi bersertifikasi <strong>ISO/IEC 27001</strong> dan terdaftar secara resmi di Kementerian Komunikasi dan Digital (Kominfo).</p>
                        <p>Langkah-langkah pengamanan mencakup:</p>
                        <ul>
                            <li><strong>Enkripsi End-to-End:</strong> Pesan yang dikirimkan melalui saluran WhatsApp dienkripsi dari ujung ke ujung oleh protokol WhatsApp.</li>
                            <li><strong>Enkripsi In-Transit & At-Rest:</strong> Seluruh transmisi data dienkripsi dengan protokol TLS 1.3 dan data tersimpan dienkripsi dengan standar AES-256.</li>
                            <li><strong>Kontrol Akses Ketat:</strong> Akses ke basis data hanya diberikan kepada personel berwenang berdasarkan prinsip hak akses terendah (least-privilege).</li>
                            <li><strong>Uji Penetrasi & Audit Rutin:</strong> Pemindaian kerentanan berkala untuk memastikan perlindungan dari potensi ancaman siber.</li>
                        </ul>

                        <h2 id="berbagi-data">7. Pembagian Data kepada Pihak Ketiga</h2>
                        <p>Kami hanya membagikan data pribadi Anda kepada pihak ketiga dalam batas-batas yang sangat ketat dan diatur oleh perjanjian kerahasiaan:</p>
                        <ul>
                            <li><strong>Penyedia Infrastruktur & Hosting:</strong> Mitra penyedia pusat data bereputasi tinggi yang mematuhi standar privasi ketat.</li>
                            <li><strong>Meta Platforms, Inc.:</strong> Sebagai penyedia protokol WhatsApp Business API untuk pengiriman pesan komunikasi.</li>
                            <li><strong>Afiliasi CTI Group:</strong> Entitas anak perusahaan dalam grup untuk penyediaan solusi teknologi terpadu.</li>
                            <li><strong>Kepatuhan Hukum:</strong> Ketika diwajibkan oleh penetapan pengadilan, proses penyidikan resmi aparat penegak hukum, atau peraturan perundang-undangan di Indonesia.</li>
                        </ul>

                        <h2 id="transfer-lintas-negara">8. Transfer Data Lintas Negara</h2>
                        <p>Dalam menjalankan layanan berbasis cloud dan integrasi API global, beberapa data mungkin diproses melalui server yang berlokasi di luar wilayah Indonesia. Kami memastikan setiap transfer data lintas batas memenuhi persyaratan UU PDP, termasuk penerapan klausul kontrak standar (Standard Contractual Clauses) yang menjamin tingkat pelindungan data setara.</p>

                        <h2 id="hak-anda">9. Hak-Hak Anda Berdasarkan UU PDP</h2>
                        <p>Sesuai dengan regulasi pelindungan data pribadi yang berlaku, Anda memiliki hak-hak hukum sebagai berikut:</p>
                        <ul>
                            <li><strong>Hak Akses:</strong> Meminta konfirmasi dan salinan data pribadi Anda yang kami proses.</li>
                            <li><strong>Hak Pembaruan/Koreksi:</strong> Memperbaiki data pribadi yang tidak akurat, tidak lengkap, atau sudah kedaluwarsa.</li>
                            <li><strong>Hak Penghapusan (Erasure):</strong> Meminta penghapusan data pribadi Anda dari sistem kami bila tujuan pemrosesan telah berakhir.</li>
                            <li><strong>Hak Penarikan Persetujuan:</strong> Menarik kembali persetujuan yang sebelumnya telah Anda berikan untuk tujuan pemasaran.</li>
                        </ul>

                        <h2 id="retensi-data">10. Retensi & Penyimpanan Data</h2>
                        <p>Kami menyimpan data pribadi Anda selama periode yang diperlukan untuk memenuhi tujuan pengumpulan, penyediaan layanan, atau penyelesaian kewajiban hukum, perpajakan, dan pelaporan keuangan di Indonesia. Setelah periode retensi berakhir, data akan dimusnahkan secara aman dan permanen.</p>

                        <h2 id="perubahan-kebijakan">11. Perubahan pada Kebijakan Privasi Ini</h2>
                        <p>PT Helios Informatika Nusantara berhak memperbarui Kebijakan Privasi ini dari waktu ke waktu guna menyesuaikan dengan perkembangan hukum atau penyempurnaan layanan. Setiap perubahan akan diumumkan secara transparan di halaman ini dengan tanggal pembaruan terkini.</p>

                        <h2 id="hubungi-kami">12. Hubungi Kami</h2>
                        <p>Untuk pertanyaan, pengajuan hak privasi, atau permintaan terkait data Anda, hubungi tim kami melalui kontak resmi:</p>
                        <p>
                            <strong>PT Helios Informatika Nusantara (CTI Group)</strong><br>
                            Centennial Tower, Lantai 12<br>
                            Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta 12930, Indonesia<br>
                            Telepon: +62 21 8062 2220<br>
                            WhatsApp: <a href="https://wa.me/628118496889" target="_blank" rel="noopener">+62 8118 4968 89</a><br>
                            Email: <a href="mailto:kouventa@helios.id">kouventa@helios.id</a>
                        </p>
                    @else
                        <!-- ==================== ENGLISH ==================== -->
                        <h2 id="introduction">1. Introduction & Overview</h2>
                        <p>At <strong>PT Helios Informatika Nusantara</strong> (a member of CTI Group) and its affiliates, ensuring the privacy and security of your personal data is of utmost importance to us. As you navigate through our websites (<a href="https://kouventa.ai" target="_blank" rel="noopener">https://kouventa.ai</a> and <a href="https://www.helios.id" target="_blank" rel="noopener">https://www.helios.id</a>), we strive to create a safe, transparent, and trustworthy environment for all users.</p>
                        <p>This Privacy Policy establishes the terms governing our handling of personal information across our website, products, and WhatsApp Business customer engagement platforms. By accessing our services, you acknowledge that you have reviewed, understood, and consented to the practices described herein.</p>

                        <div class="kv-legal-callout">
                            <strong>Our Privacy Commitment:</strong> We never sell your personal information to third parties. All data collected is utilized strictly to provide, secure, and enhance enterprise communication and AI solutions.
                        </div>

                        <h2 id="data-collected">2. Personal Data We Collect</h2>
                        <p>When utilizing or engaging with our platform and services, we may collect and process several categories of information:</p>
                        <ul>
                            <li><strong>Identity & Contact Information:</strong> Name, professional email address, telephone/WhatsApp number, company name, job position, and department submitted through demo requests or contact forms.</li>
                            <li><strong>Communication & Inquiry Details:</strong> Contents of messages, questions, and inquiries exchanged with our sales, technical specialists, or automated assistant.</li>
                            <li><strong>Technical Information:</strong> Browser type, operating system, device identifiers, IP address, and referring URLs collected automatically when interacting with our digital interfaces.</li>
                            <li><strong>Usage & Interaction Data:</strong> Pages visited, features explored, click patterns, and status of notification deliveries.</li>
                        </ul>

                        <h2 id="collection-methods">3. Collection Methods</h2>
                        <p>We collect personal information through the following touchpoints:</p>
                        <ul>
                            <li><strong>Direct Interactions:</strong> When you voluntarily submit personal details to schedule a product demo, download whitepapers, or chat with our team via WhatsApp.</li>
                            <li><strong>Automated Technologies:</strong> Local cookies and session identifiers designed to provide optimal navigation experience, assess site performance, and protect against bot abuse.</li>
                            <li><strong>Enterprise Service Connectors:</strong> Direct integration with authorized platforms such as the Meta WhatsApp Business Cloud API.</li>
                        </ul>

                        <h2 id="purposes">4. Purpose of Processing</h2>
                        <p>We process your personal information for the following legitimate business purposes:</p>
                        <ol>
                            <li><strong>Responding to Inquiries:</strong> Providing tailored consultations, product pricing, and live software demonstrations.</li>
                            <li><strong>Service Enhancement:</strong> Analyzing interaction patterns to continuously refine our conversational AI models and user workflows.</li>
                            <li><strong>Security & Threat Prevention:</strong> Ensuring network resilience, mitigating unauthorized access, and verifying communication integrity.</li>
                            <li><strong>Product Announcements & Updates:</strong> Sharing relevant industry insights and product release notes, with simple opt-out mechanisms available in every communication.</li>
                        </ol>

                        <h2 id="data-sharing">5. Data Sharing & Disclosure</h2>
                        <p>We disclose information only to vetted third-party service providers acting on our behalf under strict data protection and non-disclosure obligations:</p>
                        <ul>
                            <li><strong>Cloud & Hosting Infrastructure:</strong> Tier-3/4 enterprise data centers maintaining robust physical and logical isolation.</li>
                            <li><strong>Meta Platforms, Inc.:</strong> As the underlying provider of the WhatsApp Business API infrastructure.</li>
                            <li><strong>CTI Group Affiliates:</strong> Group companies collaborating to provide comprehensive digital solutions.</li>
                            <li><strong>Statutory & Legal Requirements:</strong> When compelled by applicable laws, court orders, or official regulatory directives.</li>
                        </ul>

                        <h2 id="cross-border">6. Cross-Border Transfers</h2>
                        <p>In operating enterprise cloud architectures, data may be transferred to or processed on systems located outside your home jurisdiction. We implement recognized transfer safeguards, including Standard Contractual Clauses, to ensure an equivalent level of security and privacy protection.</p>

                        <h2 id="security">7. Security & Protection</h2>
                        <p>We maintain stringent administrative, physical, and technical safeguards in alignment with <strong>ISO/IEC 27001</strong> certification standards and regulatory compliance requirements:</p>
                        <ul>
                            <li><strong>End-to-End Encryption:</strong> Messages transmitted through WhatsApp are end-to-end encrypted by WhatsApp's Signal protocol.</li>
                            <li><strong>In-Transit & At-Rest Encryption:</strong> Standard TLS 1.3 for network transmissions and AES-256 for persistent database storage.</li>
                            <li><strong>Role-Based Access Control:</strong> Strict least-privilege policies and mandatory multi-factor authentication (MFA).</li>
                            <li><strong>Continuous Vulnerability Auditing:</strong> Regular penetration testing and code audits to safeguard against evolving threats.</li>
                        </ul>

                        <h2 id="retention">8. Storage & Retention</h2>
                        <p>We retain personal information only for as long as necessary to fulfill the operational purposes for which it was gathered, or to comply with statutory accounting, auditing, and Indonesian data retention requirements. Upon expiry of the retention duration, records are irreversibly anonymized or destroyed.</p>

                        <h2 id="modifications">9. Policy Modifications</h2>
                        <p>PT Helios Informatika Nusantara reserves the right to revise this Privacy Policy periodically. Significant changes will be prominently announced on this page with an updated revision date.</p>

                        <h2 id="your-rights">10. Your Privacy Rights</h2>
                        <p>Depending on your jurisdiction, you possess specific legal rights regarding your personal data:</p>
                        <ul>
                            <li>The right to request access to the personal data we maintain about you.</li>
                            <li>The right to rectify inaccurate or incomplete records.</li>
                            <li>The right to request deletion (erasure) of your personal data where grounds permit.</li>
                            <li>The right to withdraw marketing consent at any time without adverse effect.</li>
                        </ul>

                        <h2 id="third-party">11. Social & Third-Party Services</h2>
                        <p>Our website may include links to external social media platforms (LinkedIn, YouTube, Instagram). Any information collected by those third parties is governed exclusively by their respective privacy statements.</p>

                        <h2 id="contact">12. Contacting Us</h2>
                        <p>If you have any questions, concerns, or requests regarding this Privacy Policy, please get in touch with our team:</p>
                        <p>
                            <strong>PT Helios Informatika Nusantara (CTI Group)</strong><br>
                            Centennial Tower, 12th Floor<br>
                            Jl. Jend. Gatot Subroto Kav. 24-25, Jakarta 12930, Indonesia<br>
                            Phone: +62 21 8062 2220<br>
                            WhatsApp: <a href="https://wa.me/628118496889" target="_blank" rel="noopener">+62 8118 4968 89</a><br>
                            Email: <a href="mailto:kouventa@helios.id">kouventa@helios.id</a>
                        </p>
                    @endif
                </article>
            </main>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.kv-legal-toc-link');
        const sections = Array.from(links).map(link => {
            const targetId = link.getAttribute('href').replace('#', '');
            return document.getElementById(targetId);
        }).filter(Boolean);

        function onScroll() {
            const scrollPos = window.scrollY + 140;
            let current = null;
            sections.forEach(section => {
                if (section.offsetTop <= scrollPos) {
                    current = section;
                }
            });

            links.forEach(link => {
                link.classList.remove('active');
                if (current && link.getAttribute('href') === '#' + current.id) {
                    link.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    });
</script>
@endpush