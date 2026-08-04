{{-- Google Site Kit HTML Code Injection --}}
@if(setting('gsk_enabled', true))
    {{-- Google Analytics 4 (GA4) --}}
    @if($ga4Id = setting('gsk_ga4_tag_id'))
        <!-- Google Analytics (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4Id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $ga4Id }}');
        </script>
    @endif

    {{-- Google Tag Manager (GTM) --}}
    @if($gtmId = setting('gsk_gtm_id'))
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtmId }}');</script>
        <!-- End Google Tag Manager -->
    @endif

    {{-- Google Ads --}}
    @if($adsId = setting('gsk_ads_id'))
        <!-- Google Ads -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $adsId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $adsId }}');
        </script>
    @endif
@endif
