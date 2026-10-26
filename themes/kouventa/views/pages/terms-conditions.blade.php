@extends('kouventa::layouts.app')

@php
    $isIdLocale = app()->getLocale() === 'id';
    $pageTitle = $isIdLocale ? 'Syarat & Ketentuan' : 'Terms & Conditions';
    $heroBadge = $isIdLocale ? 'HUKUM & KEPATUHAN' : 'LEGAL & COMPLIANCE';
    $heroSubtitle = $isIdLocale
        ? 'Ketentuan dan aturan penggunaan situs web serta layanan Kouventa (PT Helios Informatika Nusantara).'
        : 'Rules, guidelines, and legal agreements governing the use of Kouventa and Helios Informatika Nusantara platforms.';
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
                            <a href="#kuki" class="kv-legal-toc-link">2. Kuki (Cookies)</a>
                            <a href="#lisensi" class="kv-legal-toc-link">3. Lisensi & Komentar</a>
                            <a href="#penautan" class="kv-legal-toc-link">4. Hyperlink ke Konten Kami</a>
                            <a href="#iframes" class="kv-legal-toc-link">5. Penggunaan iFrames</a>
                            <a href="#tanggung-jawab" class="kv-legal-toc-link">6. Tanggung Jawab Konten</a>
                            <a href="#hak-reservasi" class="kv-legal-toc-link">7. Hak Reservasi & Perubahan</a>
                            <a href="#penghapusan-tautan" class="kv-legal-toc-link">8. Penghapusan Tautan</a>
                            <a href="#penafian" class="kv-legal-toc-link">9. Penafian (Disclaimer)</a>
                            <a href="#kontak" class="kv-legal-toc-link">10. Kontak & Bantuan</a>
                        @else
                            <a href="#introduction" class="kv-legal-toc-link">1. Introduction</a>
                            <a href="#cookies" class="kv-legal-toc-link">2. Cookies</a>
                            <a href="#license" class="kv-legal-toc-link">3. License & Comments</a>
                            <a href="#hyperlinking" class="kv-legal-toc-link">4. Hyperlinking to Our Content</a>
                            <a href="#iframes" class="kv-legal-toc-link">5. iFrames</a>
                            <a href="#content-liability" class="kv-legal-toc-link">6. Content Liability</a>
                            <a href="#reservation" class="kv-legal-toc-link">7. Reservation of Rights</a>
                            <a href="#removal-of-links" class="kv-legal-toc-link">8. Removal of Links</a>
                            <a href="#disclaimer" class="kv-legal-toc-link">9. Disclaimer</a>
                            <a href="#contact" class="kv-legal-toc-link">10. Contact Us</a>
                        @endif
                    </nav>
                </div>

                <!-- Contact & Assistance Card -->
                <div class="kv-legal-contact-card">
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: #2BD46A">
                        {{ $isIdLocale ? 'BANTUAN HUKUM' : 'LEGAL INQUIRIES' }}
                    </span>
                    <h4 style="margin: 0; font-size: 17px; font-weight: 700; line-height: 1.3">
                        {{ $isIdLocale ? 'Ada pertanyaan tentang ketentuan kami?' : 'Have questions about these terms?' }}
                    </h4>
                    <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #B9C6D3">
                        {{ $isIdLocale ? 'Tim legal Helios dan Kouventa siap membantu menjawab pertanyaan Anda.' : 'Our legal and support team is here to assist with any clarification.' }}
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
                        <h2 id="pendahuluan">1. Pendahuluan</h2>
                        <p>Selamat datang di <strong>Helios Informatika Nusantara</strong>! Ketentuan penggunaan ini menguraikan aturan dan ketentuan untuk penggunaan Situs Web Helios Informatika Nusantara dan platform Kouventa, yang terletak di <a href="https://kouventa.ai" target="_blank" rel="noopener">https://kouventa.ai</a> dan <a href="https://www.helios.id" target="_blank" rel="noopener">https://www.helios.id</a>.</p>
                        <p>Dengan mengakses situs web ini, kami menganggap Anda menerima ketentuan penggunaan ini. Jangan terus menggunakan layanan atau situs web Helios Informatika Nusantara jika Anda tidak setuju untuk mematuhi semua ketentuan yang disebutkan pada halaman ini.</p>
                        
                        <div class="kv-legal-callout">
                            <strong>Definisi dan Istilah:</strong> Istilah berikut berlaku untuk Ketentuan Penggunaan, Pernyataan Privasi, dan Pemberitahuan Penafian ini: <em>"Klien"</em>, <em>"Anda"</em>, dan <em>"Milik Anda"</em> merujuk kepada Anda, orang yang mengakses situs web ini dan mematuhi ketentuan perusahaan. <em>"Perusahaan"</em>, <em>"Kami Sendiri"</em>, <em>"Kami"</em>, dan <em>"Milik Kami"</em> merujuk kepada PT Helios Informatika Nusantara beserta lini produk Kouventa. <em>"Pihak"</em>, <em>"Para Pihak"</em>, atau <em>"Kami"</em> merujuk pada Klien dan kami sendiri secara bersama-sama.
                        </div>

                        <p>Semua istilah merujuk pada penawaran, penerimaan, dan pertimbangan pembayaran yang diperlukan untuk menjalankan proses bantuan kami kepada Klien dengan cara yang paling tepat untuk tujuan yang jelas yaitu memenuhi kebutuhan Klien sehubungan dengan penyediaan layanan yang dinyatakan oleh Perusahaan, sesuai dengan dan tunduk pada hukum yang berlaku di Republik Indonesia.</p>

                        <h2 id="kuki">2. Kuki (Cookies)</h2>
                        <p>Kami menggunakan kuki (cookies). Dengan mengakses situs web Helios Informatika Nusantara dan Kouventa, Anda setuju untuk menggunakan kuki sesuai dengan <a href="{{ localized_url('/privacy-policy') }}">Kebijakan Privasi</a> kami.</p>
                        <p>Sebagian besar situs web interaktif modern menggunakan kuki untuk memungkinkan kami mengambil detail pengguna untuk setiap kunjungan. Kuki digunakan oleh situs web kami untuk mengaktifkan fungsionalitas area tertentu agar memudahkan orang yang mengunjungi situs web kami. Beberapa mitra afiliasi dan analitik kami mungkin juga menggunakan kuki untuk mengukur kinerja dan keamanan platform.</p>

                        <h2 id="lisensi">3. Lisensi & Hak Kekayaan Intelektual</h2>
                        <p>Kecuali dinyatakan lain, PT Helios Informatika Nusantara dan/atau pemberi lisensinya memiliki hak kekayaan intelektual atas semua materi di situs web ini. Semua hak kekayaan intelektual dilindungi oleh undang-undang yang berlaku. Anda dapat mengaksesnya untuk penggunaan pribadi dan bisnis internal Anda dengan tunduk pada batasan yang ditetapkan dalam ketentuan penggunaan ini.</p>
                        <p><strong>Anda dilarang keras untuk:</strong></p>
                        <ul>
                            <li>Menerbitkan ulang materi dari Helios Informatika Nusantara atau Kouventa tanpa izin tertulis sebelumnya.</li>
                            <li>Menjual, menyewakan, atau mensublisensikan materi yang bersumber dari situs web ini.</li>
                            <li>Mereproduksi, menduplikat, atau menyalin materi dari situs web ini untuk tujuan komersial tidak sah.</li>
                            <li>Mendistribusikan ulang konten tanpa atribusi resmi dan persetujuan tertulis dari manajemen PT Helios Informatika Nusantara.</li>
                        </ul>

                        <h3 id="komentar">Komentar dan Interaksi Pengguna</h3>
                        <p>Bagian tertentu dari situs web ini dapat menawarkan kesempatan bagi pengguna untuk berinteraksi, memposting tanggapan, atau bertukar informasi. Helios Informatika Nusantara tidak menyaring, mengedit, menerbitkan, atau meninjau komentar sebelum kehadirannya di situs web secara menyeluruh.</p>
                        <p>Komentar sepenuhnya mencerminkan pandangan dan pendapat orang yang mempostingnya, dan bukan pandangan resmi PT Helios Informatika Nusantara. Sejauh diizinkan oleh undang-undang yang berlaku, Helios Informatika Nusantara tidak bertanggung jawab atas kerugian atau pengeluaran yang timbul sebagai akibat dari komentar yang ditampilkan.</p>
                        <p>Helios berhak memantau semua komentar dan interaksi serta menghapus konten apa pun yang dapat dianggap tidak pantas, menyinggung, atau melanggar Ketentuan Penggunaan ini.</p>

                        <h2 id="penautan">4. Hyperlink ke Konten Kami</h2>
                        <p>Organisasi-organisasi berikut dapat menautkan (hyperlink) ke Situs Web kami tanpa persetujuan tertulis sebelumnya:</p>
                        <ul>
                            <li>Badan pemerintah dan lembaga regulasi resmi;</li>
                            <li>Mesin pencari (Search Engines seperti Google, Bing);</li>
                            <li>Organisasi berita dan publikasi terakreditasi;</li>
                            <li>Distributor direktori online terverifikasi;</li>
                            <li>Bisnis terakreditasi di seluruh ekosistem teknologi dan mitra resmi.</li>
                        </ul>
                        <p>Organisasi-organisasi ini dapat menautkan ke halaman depan kami selama tautan tersebut: (a) tidak menipu dengan cara apa pun; (b) tidak secara keliru menyiratkan sponsor, dukungan, atau persetujuan dari pihak yang menautkan; dan (c) sesuai dengan konteks situs pihak yang menautkan.</p>

                        <h2 id="iframes">5. Penggunaan iFrames</h2>
                        <p>Tanpa persetujuan tertulis dan izin resmi sebelumnya, Anda tidak boleh membuat bingkai (frames/iFrames) di sekitar halaman web kami yang mengubah presentasi visual, tata letak, atau tampilan situs web Kouventa dan Helios Informatika Nusantara dengan cara apa pun.</p>

                        <h2 id="tanggung-jawab">6. Tanggung Jawab Konten</h2>
                        <p>Kami tidak bertanggung jawab atas konten apa pun yang muncul di situs web pihak ketiga yang menautkan ke kami. Anda setuju untuk melindungi dan membela kami terhadap semua klaim yang timbul dari situs web Anda. Tidak boleh ada tautan yang muncul di situs web mana pun yang dapat ditafsirkan sebagai fitnah, cabul, melanggar hak cipta, atau melanggar hak pihak ketiga mana pun.</p>

                        <h2 id="hak-reservasi">7. Hak Reservasi & Pembaruan Kebijakan</h2>
                        <p>Kami berhak sewaktu-waktu meminta Anda untuk menghapus semua tautan atau tautan tertentu ke Situs Web kami. Anda setuju untuk segera menghapus semua tautan ke Situs Web kami setelah menerima permintaan resmi dari kami. Kami juga berhak mengubah syarat dan ketentuan ini beserta kebijakan penautannya kapan saja.</p>

                        <h2 id="penghapusan-tautan">8. Penghapusan Tautan dari Situs Kami</h2>
                        <p>Jika Anda menemukan tautan di Situs Web kami yang menyinggung atau bermasalah karena alasan apa pun, Anda bebas untuk menghubungi dan memberi tahu kami kapan saja. Kami akan mempertimbangkan setiap permintaan untuk menghapus tautan, namun kami tidak berkewajiban untuk melakukannya atau merespons secara langsung.</p>

                        <h2 id="penafian">9. Penafian (Disclaimer)</h2>
                        <p>Sejauh hukum yang berlaku memperbolehkannya, kami mengecualikan semua pernyataan, jaminan, dan ketentuan yang terkait dengan situs web kami dan penggunaannya. Informasi dan layanan di situs web ini disediakan sebagaimana adanya ("as is").</p>
                        <p>Tidak ada hal dalam penafian ini yang akan:</p>
                        <ul>
                            <li>Membatasi atau mengecualikan tanggung jawab kami atau Anda atas cedera pribadi akibat kelalaian berat;</li>
                            <li>Membatasi atau mengecualikan tanggung jawab atas penipuan atau penyesatan yang disengaja;</li>
                            <li>Membatasi tanggung jawab kami atau Anda dengan cara apa pun yang tidak diizinkan oleh hukum Indonesia yang berlaku.</li>
                        </ul>

                        <h2 id="kontak">10. Kontak & Informasi Perusahaan</h2>
                        <p>Jika Anda memiliki pertanyaan tentang Ketentuan Penggunaan ini, silakan hubungi tim kami:</p>
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
                        <h2 id="introduction">1. Introduction</h2>
                        <p>Welcome to <strong>Helios Informatika Nusantara</strong>! These terms and conditions outline the rules and regulations for the use of Helios Informatika Nusantara's Website and Kouventa platform, located at <a href="https://kouventa.ai" target="_blank" rel="noopener">https://kouventa.ai</a> and <a href="https://www.helios.id" target="_blank" rel="noopener">https://www.helios.id</a>.</p>
                        <p>By accessing this website we assume you accept these terms and conditions. Do not continue to use Helios Informatika Nusantara or Kouventa if you do not agree to take all of the terms and conditions stated on this page.</p>

                        <div class="kv-legal-callout">
                            <strong>Terminology:</strong> The following terminology applies to these Terms and Conditions, Privacy Statement, and Disclaimer Notice: <em>"Client"</em>, <em>"You"</em>, and <em>"Your"</em> refers to you, the person accessing this website and compliant with the Company's terms and conditions. <em>"The Company"</em>, <em>"Ourselves"</em>, <em>"We"</em>, <em>"Our"</em>, and <em>"Us"</em> refers to PT Helios Informatika Nusantara and Kouventa. <em>"Party"</em>, <em>"Parties"</em>, or <em>"Us"</em> refers to both the Client and ourselves.
                        </div>

                        <p>All terms refer to the offer, acceptance, and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client's needs in respect of the provision of the Company's stated services, in accordance with and subject to prevailing Indonesian laws.</p>

                        <h2 id="cookies">2. Cookies</h2>
                        <p>We employ the use of cookies. By accessing Helios Informatika Nusantara and Kouventa, you agree to use cookies in agreement with the <a href="{{ localized_url('/privacy-policy') }}">Privacy Policy</a>.</p>
                        <p>Most interactive websites use cookies to let us retrieve the user's details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate, analytics, and advertising partners may also use cookies.</p>

                        <h2 id="license">3. License & Intellectual Property</h2>
                        <p>Unless otherwise stated, PT Helios Informatika Nusantara and/or its licensors own the intellectual property rights for all material on Helios Informatika Nusantara and Kouventa. All intellectual property rights are reserved. You may access this from the website for your own personal and internal business use subjected to restrictions set in these terms and conditions.</p>
                        <p><strong>You must not:</strong></p>
                        <ul>
                            <li>Republish material from Helios Informatika Nusantara or Kouventa without prior written consent;</li>
                            <li>Sell, rent, or sub-license material from this website;</li>
                            <li>Reproduce, duplicate, or copy material from this website for commercial redistribution;</li>
                            <li>Redistribute content from this website without explicit authorization.</li>
                        </ul>

                        <h3 id="comments">User Comments & Participation</h3>
                        <p>Parts of this website offer an opportunity for users to post and exchange opinions and information. PT Helios Informatika Nusantara does not filter, edit, publish, or review Comments prior to their presence on the website.</p>
                        <p>Comments do not reflect the views and opinions of PT Helios Informatika Nusantara, its agents, or affiliates. Comments reflect the views and opinions of the person who posts them. To the extent permitted by applicable laws, Helios shall not be liable for the Comments or for any liability, damages, or expenses caused as a result of any use of or posting of Comments on this website.</p>
                        <p>Helios reserves the right to monitor all Comments and to remove any Comments which can be considered inappropriate, offensive, or causes a breach of these Terms and Conditions.</p>

                        <h2 id="hyperlinking">4. Hyperlinking to Our Content</h2>
                        <p>The following organizations may link to our Website without prior written approval:</p>
                        <ul>
                            <li>Government agencies;</li>
                            <li>Search engines;</li>
                            <li>News organizations;</li>
                            <li>Online directory distributors in the same manner as they hyperlink to other listed businesses; and</li>
                            <li>System-wide Accredited Businesses except soliciting non-profit organizations and charity fundraising groups.</li>
                        </ul>
                        <p>These organizations may link to our home page or publications so long as the link: (a) is not in any way deceptive; (b) does not falsely imply sponsorship, endorsement, or approval of the linking party and its products or services; and (c) fits within the context of the linking party's site.</p>

                        <h2 id="iframes">5. iFrames</h2>
                        <p>Without prior approval and written permission, you may not create frames around our Webpages that alter in any way the visual presentation or appearance of our Website.</p>

                        <h2 id="content-liability">6. Content Liability</h2>
                        <p>We shall not be held responsible for any content that appears on your Website. You agree to protect and defend us against all claims arising on your Website. No link(s) should appear on any Website that may be interpreted as libelous, obscene, or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of any third-party rights.</p>

                        <h2 id="reservation">7. Reservation of Rights</h2>
                        <p>We reserve the right to request that you remove all links or any particular link to our Website. You approve to immediately remove all links to our Website upon request. We also reserve the right to amend these terms and conditions and its linking policy at any time. By continuously linking to our Website, you agree to be bound to and follow these linking terms and conditions.</p>

                        <h2 id="removal-of-links">8. Removal of Links from Our Website</h2>
                        <p>If you find any link on our Website that is offensive for any reason, you are free to contact and inform us at any moment. We will consider requests to remove links but we are not obligated to do so or to respond to you directly.</p>

                        <h2 id="disclaimer">9. Disclaimer</h2>
                        <p>To the maximum extent permitted by applicable law, we exclude all representations, warranties, and conditions relating to our website and the use of this website. Nothing in this disclaimer will:</p>
                        <ul>
                            <li>Limit or exclude our or your liability for death or personal injury;</li>
                            <li>Limit or exclude our or your liability for fraud or fraudulent misrepresentation;</li>
                            <li>Limit any of our or your liabilities in any way that is not permitted under applicable law; or</li>
                            <li>Exclude any of our or your liabilities that may not be excluded under applicable law.</li>
                        </ul>

                        <h2 id="contact">10. Contact Information</h2>
                        <p>If you have any questions or feedback regarding our Terms & Conditions, please contact us:</p>
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