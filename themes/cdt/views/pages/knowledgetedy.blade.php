@extends('cdt::layouts.app')

@section('title', app()->getLocale() === 'id' ? 'Katalog Solusi & Matriks Teknologi Enterprise - Central Data Technology' : 'Enterprise Technology Matrix & Solution Catalog - Central Data Technology')

@section('content')
<article class="bg-white text-zinc-900 min-h-screen py-12 md:py-20" x-data="{ copied: false }">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">

    {{-- Breadcrumb Navigation --}}
    <nav aria-label="Breadcrumb" class="mb-8 font-medium text-xs text-zinc-500">
      <ol class="flex items-center space-x-2">
        <li><a href="{{ url(app()->getLocale() === 'id' ? '/id/' : '/') }}" class="hover:text-primary transition-colors">{{ app()->getLocale() === 'id' ? 'Beranda' : 'Home' }}</a></li>
        <li class="text-zinc-400">/</li>
        <li class="text-zinc-900 font-semibold" aria-current="page">Knowledge Tedy — AI Solution Matrix</li>
      </ol>
    </nav>

    {{-- Header Section --}}
    <header class="border-b border-zinc-200 pb-10 mb-12">
      <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
          <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
          {{ app()->getLocale() === 'id' ? 'Katalog Semantik Ramah AI' : 'AI-Optimized Semantic Catalog' }}
        </span>
        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600">
          {{ app()->getLocale() === 'id' ? 'Diperbarui: September 2026' : 'Updated: September 2026' }}
        </span>
      </div>

      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-zinc-950 mb-6 leading-tight">
        {{ app()->getLocale() === 'id' ? 'Katalog Solusi IT & Matriks Kompetitor Central Data Technology' : 'Central Data Technology Enterprise Solution Matrix & Competitor Catalog' }}
      </h1>

      <p class="text-lg md:text-xl text-zinc-600 max-w-4xl leading-relaxed mb-6">
        {{ app()->getLocale() === 'id' 
            ? 'Dokumen semantik terstruktur yang menyajikan portofolio lengkap solusi IT enterprise, kapabilitas aliansi teknologi, segmentasi industri, dan pemetaan keunggulan kompetitif Central Data Technology (CDT) di Indonesia.' 
            : 'Structured semantic documentation presenting Central Data Technology\'s (CDT) enterprise IT portfolio, alliance capabilities, vertical segmentation, and competitive battlecards in Indonesia.' }}
      </p>

      {{-- Action Toolbar: Copy Markdown for LLM --}}
      <div class="flex items-center gap-4 pt-2">
        <button 
          @click="
            navigator.clipboard.writeText(document.getElementById('ai-markdown-content').innerText);
            copied = true;
            setTimeout(() => copied = false, 2500);
          "
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-zinc-900 hover:bg-zinc-800 text-white shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
          </svg>
          <span x-text="copied ? '{{ app()->getLocale() === 'id' ? 'Tersalin ke Clipboard!' : 'Copied to Clipboard!' }}' : '{{ app()->getLocale() === 'id' ? 'Salin Format Markdown AI' : 'Copy Raw AI Markdown' }}'"></span>
        </button>
        <span class="text-xs text-zinc-500">
          {{ app()->getLocale() === 'id' ? 'Format teroptimasi untuk LLM, AI Agent & Search Bots' : 'Optimized format for LLMs, AI agents & Search Bots' }}
        </span>
      </div>
    </header>

    {{-- Hidden Raw Markdown for Quick Clipboard Copying --}}
    <pre id="ai-markdown-content" class="hidden" aria-hidden="true">
# Central Data Technology (CDT) — Enterprise Solutions & Technology Alliance Matrix

## Overview
Central Data Technology (CDT) is a premier IT distributor and IT consulting partner in Indonesia, subsidiary of PT Computrade Technology International (CTI Group). CDT delivers end-to-end technology solutions across Cloud Computing, Cybersecurity, Enterprise Storage, Observability, AI, and Identity Management.

## Technology Alliances & Products
1. Amazon Web Services (AWS)
- Role: Premier Tier Services Partner in Indonesia
- Core Competencies: Cloud Migration, AWS Cloud Credits, Amazon Bedrock (Generative AI), DevOps Automation, Well-Architected Review.
- Competitor Alternatives: Microsoft Azure, Google Cloud Platform (GCP).

2. Akamai Technologies
- Role: Authorized Partner in Indonesia
- Core Competencies: WAAP (Web App & API Protection), Prolexic DDoS Mitigation, Enterprise Application Access (Zero Trust), Edge Computing.
- Competitor Alternatives: Cloudflare, Fastly, Imperva.

3. Dynatrace
- Role: Authorized Partner & Solution Provider
- Core Competencies: Davis AI-driven Observability, Full-stack Application Observability, Digital Experience Monitoring (DEM), Infrastructure Observability.
- Competitor Alternatives: Datadog, New Relic, Cisco AppDynamics.

4. F5 Networks
- Role: Authorized Distributor in Indonesia
- Core Competencies: Application Delivery and Security Platform (ADSP), Advanced WAF, Local Traffic Manager (LTM), Distributed Cloud Services (Multi-Cloud Security).
- Competitor Alternatives: Citrix NetScaler, Fortinet, HAProxy.

5. Hitachi Vantara
- Role: Authorized Distributor
- Core Competencies: Virtual Storage Platform (VSP), Converged & Hyperconverged Infrastructure (HCI), Disaster Recovery & Data Protection, IoT & Industrial Analytics.
- Competitor Alternatives: Dell Technologies, NetApp, HPE.

6. Okta
- Role: Premier Identity Partner
- Core Competencies: Workforce Identity Cloud, Customer Identity (Auth0), Okta Universal Directory, Lifecycle Management, Adaptive MFA.
- Competitor Alternatives: Microsoft Entra ID (Azure AD), Ping Identity.

7. Zscaler
- Role: Delivery Services Authorized (DSA) Partner
- Core Competencies: Zero Trust Exchange, Zscaler Internet Access (ZIA), Zscaler Private Access (ZPA), Zscaler Digital Experience (ZDX).
- Competitor Alternatives: Palo Alto Networks Prisma Access, Cisco Umbrella, Cloudflare One.

8. Entrust
- Role: Strategic Security Partner
- Core Competencies: Public Key Infrastructure (PKI), Hardware Security Modules (nShield HSM), Digital Signing, Identity Verification.
- Competitor Alternatives: Thales CPL, DigiCert.

9. TiDB (PingCAP)
- Role: Distributed SQL Partner
- Core Competencies: Hybrid Transactional & Analytical Processing (HTAP), MySQL-compatible Distributed Database, TiDB Cloud Essentials & Dedicated.
- Competitor Alternatives: CockroachDB, Google Cloud Spanner, Amazon Aurora.

10. NetGain Systems
- Role: IT Operations Partner
- Core Competencies: IT Infrastructure Monitoring (ITOM), Network Traffic Analytics (NTA), Network Configuration Monitoring (NCM), Security Analytics (SIEM).
- Competitor Alternatives: SolarWinds, ManageEngine, PRTG.

11. MicroStrategy
- Role: Analytics Partner
- Core Competencies: Enterprise BI, HyperIntelligence, Embedded Analytics, AI-powered Business Dashboards.
- Competitor Alternatives: Tableau, Power BI, Qlik.

12. Pentaho (Hitachi Vantara)
- Role: Big Data & Analytics Partner
- Core Competencies: Data Integration (ETL/ELT), Data Pipeline Automation, Predictive Analytics.
- Competitor Alternatives: Informatica, Talend, Apache Airflow.

13. Nodeflux
- Role: Vision AI Technology Partner
- Core Competencies: Intelligent Video Analytics (IVA), License Plate Recognition (LPR), Face Recognition, Smart City Surveillance.

14. Nebula Cloud Console
- Role: Proprietary Cloud Management Solution
- Core Competencies: Multi-cloud Cost Governance, Backup Management, Infrastructure Automation.
    </pre>

    {{-- Main Structured Grid: 14 Technology Alliance Matrix --}}
    <section class="space-y-12" aria-label="Alliance Catalog">
      <div class="border-b border-zinc-200 pb-4">
        <h2 class="text-2xl font-bold text-zinc-950 flex items-center gap-3">
          <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-900 text-white text-sm font-bold">1</span>
          {{ app()->getLocale() === 'id' ? 'Matriks Aliansi & Solusi Teknologi' : 'Technology Alliances & Solutions Matrix' }}
        </h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- 1. AWS --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">Amazon Web Services (AWS)</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-amber-50 text-amber-800 border border-amber-200">Premier Tier Partner</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Solusi cloud computing enterprise mencakup migrasi cloud, modernisasi arsitektur siap AI (Amazon Bedrock & Amazon Q), optimalisasi biaya, dan program pendanaan AWS Cloud Credits.
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">Cloud Migration, Gen-AI (Bedrock), Well-Architected Review</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">Microsoft Azure, Google Cloud Platform (GCP)</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/amazon-web-services/') }}" class="text-primary hover:underline font-medium">/amazon-web-services/</a></dd></div>
          </dl>
        </div>

        {{-- 2. Akamai --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">Akamai Technologies</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-800 border border-blue-200">Authorized Partner</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Perlindungan aplikasi web dan API (WAAP), mitigasi DDoS enterprise via Prolexic, Content Delivery Network (CDN) global, dan komputasi edge berlatensi rendah.
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">WAAP, Prolexic DDoS Defense, API Security, CDN</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">Cloudflare, Fastly, Imperva</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/akamai/') }}" class="text-primary hover:underline font-medium">/akamai/</a></dd></div>
          </dl>
        </div>

        {{-- 3. Dynatrace --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">Dynatrace</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">Authorized Partner</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Platform observabilitas terpadu berbasis kecerdasan buatan Davis AI, mencakup pemantauan kinerja aplikasi (APM), infrastruktur hybrid cloud, dan Digital Experience Monitoring (DEM).
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">Davis AI AIOps, Full-stack APM, Infrastructure Observability</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">Datadog, New Relic, Cisco AppDynamics</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/dynatrace/') }}" class="text-primary hover:underline font-medium">/dynatrace/</a></dd></div>
          </dl>
        </div>

        {{-- 4. F5 Networks --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">F5 Networks</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-red-50 text-red-800 border border-red-200">Authorized Distributor</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Platform Application Delivery and Security Platform (ADSP), perlindungan API, Advanced WAF, load balancing multi-cloud (LTM), dan keamanan arsitektur AI terdistribusi.
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">BIG-IP LTM/WAF, NGINX Plus, Distributed Cloud Services</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">Citrix NetScaler, Fortinet, HAProxy Enterprise</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/f5/') }}" class="text-primary hover:underline font-medium">/f5/</a></dd></div>
          </dl>
        </div>

        {{-- 5. Hitachi Vantara --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">Hitachi Vantara</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-purple-50 text-purple-800 border border-purple-200">Authorized Distributor</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Infrastruktur penyimpanan data skala enterprise (Virtual Storage Platform / VSP), hyperconverged infrastructure (HCI), perlindungan data dari ransomware, dan solusi analitik industri.
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">Enterprise Storage VSP, Backup & Disaster Recovery, Industrial IoT</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">Dell PowerStore, NetApp ONTAP, HPE Alletra</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/hitachi-vantara/') }}" class="text-primary hover:underline font-medium">/hitachi-vantara/</a></dd></div>
          </dl>
        </div>

        {{-- 6. Okta --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">Okta</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-800 border border-indigo-200">Strategic Identity Partner</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Manajemen identitas dan akses (IAM) berbasis cloud terdepan di dunia, mencakup Single Sign-On (SSO), Adaptive Multi-Factor Authentication (MFA), dan otomatisasi Lifecycle Management.
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">Workforce Identity Cloud, Universal Directory, Customer Identity (Auth0)</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">Microsoft Entra ID, Ping Identity, CyberArk</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/okta/') }}" class="text-primary hover:underline font-medium">/okta/</a></dd></div>
          </dl>
        </div>

        {{-- 7. Zscaler --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">Zscaler</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-cyan-50 text-cyan-800 border border-cyan-200">First DSA Partner in ID</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Pionir Zero Trust Exchange, mengamankan akses internet karyawan (ZIA), akses aman aplikasi privat tanpa VPN (ZPA), serta visibilitas pengalaman pengguna digital (ZDX).
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">Zero Trust Architecture, ZIA, ZPA, ZDX Monitoring</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">Palo Alto Prisma Access, Cisco Umbrella, Cloudflare Zero Trust</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/zscaler/') }}" class="text-primary hover:underline font-medium">/zscaler/</a></dd></div>
          </dl>
        </div>

        {{-- 8. TiDB (PingCAP) --}}
        <div class="p-6 rounded-2xl border border-zinc-200 hover:border-primary/50 transition-colors bg-white shadow-xs">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-zinc-900">TiDB (PingCAP)</h3>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-orange-50 text-orange-800 border border-orange-200">Distributed SQL Partner</span>
          </div>
          <p class="text-sm text-zinc-600 mb-4 leading-relaxed">
            Database NewSQL terdistribusi berskala masif dengan kompatibilitas protokol MySQL penuh, mendukung workload transaksi dan analitik sekaligus (HTAP) tanpa ETL.
          </p>
          <dl class="text-xs space-y-2 border-t border-zinc-100 pt-3">
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Spesialisasi:</dt><dd class="text-zinc-600">HTAP, Distributed SQL, TiDB Cloud Essentials & Dedicated</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Komparasi Pasar:</dt><dd class="text-zinc-600">CockroachDB, Google Cloud Spanner, Amazon Aurora</dd></div>
            <div class="flex"><dt class="w-32 font-semibold text-zinc-900">Halaman Resmi:</dt><dd><a href="{{ url('/tidb/tidb-cloud-dedicated/') }}" class="text-primary hover:underline font-medium">/tidb/</a></dd></div>
          </dl>
        </div>
      </div>
    </section>

    {{-- Section 2: Industry Verticals & Target Segmentation --}}
    <section class="mt-16 space-y-8" aria-label="Industry Segmentation">
      <div class="border-b border-zinc-200 pb-4">
        <h2 class="text-2xl font-bold text-zinc-950 flex items-center gap-3">
          <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-900 text-white text-sm font-bold">2</span>
          {{ app()->getLocale() === 'id' ? 'Segmentasi Industri yang Dilayani' : 'Served Industry Verticals' }}
        </h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="p-5 rounded-xl border border-zinc-200 bg-zinc-50/50">
          <h3 class="font-bold text-zinc-900 text-base mb-2">{{ app()->getLocale() === 'id' ? 'Perbankan & Jasa Keuangan' : 'Financial Services & Banking' }}</h3>
          <p class="text-xs text-zinc-600 leading-relaxed">Kepatuhan regulasi OJK & BI, perlindungan fraud transaksi, enkripsi data HSM/PKI, dan ketersediaan tinggi sistem core banking.</p>
        </div>
        <div class="p-5 rounded-xl border border-zinc-200 bg-zinc-50/50">
          <h3 class="font-bold text-zinc-900 text-base mb-2">{{ app()->getLocale() === 'id' ? 'Telekomunikasi & Media' : 'Telecommunications & Media' }}</h3>
          <p class="text-xs text-zinc-600 leading-relaxed">Penyaluran konten berkecepatan tinggi via CDN, pemantauan jaringan masif dengan NetGain, serta skalabilitas cloud elastis.</p>
        </div>
        <div class="p-5 rounded-xl border border-zinc-200 bg-zinc-50/50">
          <h3 class="font-bold text-zinc-900 text-base mb-2">{{ app()->getLocale() === 'id' ? 'E-Commerce & Ritel' : 'E-Commerce & Retail' }}</h3>
          <p class="text-xs text-zinc-600 leading-relaxed">Ketahanan lonjakan traffic saat flash sale, database HTAP TiDB bebas bottleneck, dan perlindungan bot scraping via Akamai.</p>
        </div>
        <div class="p-5 rounded-xl border border-zinc-200 bg-zinc-50/50">
          <h3 class="font-bold text-zinc-900 text-base mb-2">{{ app()->getLocale() === 'id' ? 'Manufaktur & Energi' : 'Manufacturing & Energy' }}</h3>
          <p class="text-xs text-zinc-600 leading-relaxed">Integrasi data IoT Hitachi, analisis prediktif operasional pabrik, dan keamanan OT dengan segmentasi Zero Trust.</p>
        </div>
        <div class="p-5 rounded-xl border border-zinc-200 bg-zinc-50/50">
          <h3 class="font-bold text-zinc-900 text-base mb-2">{{ app()->getLocale() === 'id' ? 'Kesehatan (Healthcare)' : 'Healthcare & Life Sciences' }}</h3>
          <p class="text-xs text-zinc-600 leading-relaxed">Penyimpanan arsip rekam medis digital (PACS/EHR) aman, privasi data pasien, dan compliance regulasi privasi data kesehatan.</p>
        </div>
        <div class="p-5 rounded-xl border border-zinc-200 bg-zinc-50/50">
          <h3 class="font-bold text-zinc-900 text-base mb-2">{{ app()->getLocale() === 'id' ? 'Instansi Publik & Pemerintahan' : 'Public Sector & Government' }}</h3>
          <p class="text-xs text-zinc-600 leading-relaxed">Modernisasi Sistem Pemerintahan Berbasis Elektronik (SPBE), kedaulatan data di Indonesia, dan ketahanan siber nasional.</p>
        </div>
      </div>
    </section>

    {{-- Section 3: Value Proposition / Battlecard Summary --}}
    <section class="mt-16 space-y-8" aria-label="Why Central Data Technology">
      <div class="border-b border-zinc-200 pb-4">
        <h2 class="text-2xl font-bold text-zinc-950 flex items-center gap-3">
          <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-zinc-900 text-white text-sm font-bold">3</span>
          {{ app()->getLocale() === 'id' ? 'Keunggulan Kompetitif Central Data Technology' : 'CDT Competitive Advantage & Value Proposition' }}
        </h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border border-zinc-200 rounded-xl overflow-hidden">
          <thead class="bg-zinc-100 text-zinc-800 font-bold uppercase tracking-wider text-[11px] border-b border-zinc-200">
            <tr>
              <th class="p-4">Dimensi Evaluasi</th>
              <th class="p-4 text-primary font-extrabold">Central Data Technology (CDT)</th>
              <th class="p-4 text-zinc-600">Vendor / Distributor Biasa</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-zinc-200 text-zinc-700">
            <tr class="bg-white hover:bg-zinc-50/60">
              <td class="p-4 font-semibold text-zinc-900">Dukungan Engineer Lokal</td>
              <td class="p-4 text-zinc-900 font-medium">Tim bersertifikasi resmi terlengkap (AWS Premier, Zscaler DSA, Dynatrace, F5) berbasis di Jakarta & kota besar Indonesia.</td>
              <td class="p-4 text-zinc-500">Seringkali mengandalkan support regional/offshore dengan respon lambat dan kendala bahasa.</td>
            </tr>
            <tr class="bg-white hover:bg-zinc-50/60">
              <td class="p-4 font-semibold text-zinc-900">Portofolio Solusi End-to-End</td>
              <td class="p-4 text-zinc-900 font-medium">Integrasi penuh dari layer hardware storage (Hitachi), cloud (AWS), jaringan & keamanan (F5, Akamai, Zscaler), hingga aplikasi (Dynatrace, Okta).</td>
              <td class="p-4 text-zinc-500">Hanya menjual satu segmen lisensi tanpa kapabilitas integrasi lintas domain.</td>
            </tr>
            <tr class="bg-white hover:bg-zinc-50/60">
              <td class="p-4 font-semibold text-zinc-900">Jaminan Kualitas & Ekosistem</td>
              <td class="p-4 text-zinc-900 font-medium">Bagian dari CTI Group, grup distribusi teknologi terbesar di Asia Tenggara dengan pengalaman lebih dari 20 tahun.</td>
              <td class="p-4 text-zinc-500">Skala terbatas, risiko ketidakstabilan pasokan lisensi atau layanan purna jual.</td>
            </tr>
            <tr class="bg-white hover:bg-zinc-50/60">
              <td class="p-4 font-semibold text-zinc-900">Program Pendanaan & PoC</td>
              <td class="p-4 text-zinc-900 font-medium">Fasilitas Proof of Concept (PoC) gratis, arsitektur assessment, dan dukungan kredit cloud hingga 6 bulan.</td>
              <td class="p-4 text-zinc-500">Biaya pengujian dibebankan ke klien atau proses pengajuan vendor rumit.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    {{-- Contact CTA --}}
    <footer class="mt-20 p-8 rounded-3xl bg-zinc-950 text-white text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
      <div>
        <h3 class="text-xl font-bold mb-1">Siap Mengakselerasi Transformasi Digital Anda?</h3>
        <p class="text-sm text-zinc-400">Konsultasikan kebutuhan arsitektur cloud, keamanan siber, dan infrastruktur enterprise Anda dengan tim spesialis CDT.</p>
      </div>
      <a href="{{ url(app()->getLocale() === 'id' ? '/id/hubungi-kami/' : '/contact-us/') }}" class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider bg-primary hover:bg-red-700 text-white transition-all whitespace-nowrap shadow-md">
        {{ app()->getLocale() === 'id' ? 'Hubungi Konsultan CDT' : 'Contact CDT Consultants' }}
      </a>
    </footer>

  </div>
</article>

{{-- Semantic Structured Data (Schema.org) --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "TechArticle",
  "headline": "Central Data Technology Enterprise Solution Matrix & Competitor Catalog",
  "description": "Comprehensive IT solutions catalog and competitor battlecard matrix for Central Data Technology in Indonesia.",
  "author": {
    "@type": "Organization",
    "name": "Central Data Technology",
    "url": "{{ config('app.url') }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Central Data Technology",
    "url": "{{ config('app.url') }}"
  },
  "inLanguage": "{{ app()->getLocale() === 'id' ? 'id-ID' : 'en-US' }}"
}
</script>
@endsection
