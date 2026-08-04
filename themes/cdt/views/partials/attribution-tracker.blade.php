{{--
    Global Attribution Tracker Partial
    Captures UTM parameters, Ad Click IDs (gclid, fbclid), Initial Landing Page, Initial Referrer,
    Device Type, Screen Resolution, Browser Language, Page View Count, and Time to Convert.
--}}
<script>
(function() {
    function setCookie(name, val, days) {
        var d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + "=" + encodeURIComponent(val) + ";expires=" + d.toUTCString() + ";path=/;SameSite=Lax";
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? decodeURIComponent(match[2]) : null;
    }

    try {
        var urlParams = new URLSearchParams(window.location.search);
        var utmSource = urlParams.get('utm_source');
        var utmMedium = urlParams.get('utm_medium');
        var utmCampaign = urlParams.get('utm_campaign');
        var utmTerm = urlParams.get('utm_term');
        var utmContent = urlParams.get('utm_content');
        var gclid = urlParams.get('gclid');
        var fbclid = urlParams.get('fbclid');

        var existingAttrStr = getCookie('cdt_attribution');
        var attrData = {};
        if (existingAttrStr) {
            try { attrData = JSON.parse(existingAttrStr); } catch(err) { attrData = {}; }
        }

        // Track page view count
        attrData.page_views_count = (attrData.page_views_count || 0) + 1;

        // Track Initial Landing Page, Initial Referrer, and First Visit Timestamp
        if (!attrData.initial_landing_page) {
            attrData.initial_landing_page = window.location.href;
            attrData.initial_referrer = document.referrer || 'Direct';
            attrData.first_visit_at = Math.floor(Date.now() / 1000);
        }

        // Technical & Device Context
        attrData.browser_language = navigator.language || navigator.userLanguage || '-';
        attrData.screen_resolution = (window.screen ? window.screen.width + 'x' + window.screen.height : '-');
        
        var w = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        attrData.device_type = w < 768 ? 'Mobile' : (w < 1024 ? 'Tablet' : 'Desktop');

        // Update UTM parameters & Ad Click IDs if present
        if (utmSource) attrData.utm_source = utmSource;
        if (utmMedium) attrData.utm_medium = utmMedium;
        if (utmCampaign) attrData.utm_campaign = utmCampaign;
        if (utmTerm) attrData.utm_term = utmTerm;
        if (utmContent) attrData.utm_content = utmContent;
        if (gclid) attrData.gclid = gclid;
        if (fbclid) attrData.fbclid = fbclid;

        // Legacy initialTrafficSource cookie compatibility
        var legacyCookie = getCookie('initialTrafficSource');
        if (legacyCookie) {
            legacyCookie.split('|').forEach(function(pair) {
                var kv = pair.split('=');
                if (kv.length === 2) {
                    var k = kv[0].trim();
                    var v = kv[1].trim();
                    if (k === 'utmcsr' && !attrData.utm_source) attrData.utm_source = v;
                    if (k === 'utmcmd' && !attrData.utm_medium) attrData.utm_medium = v;
                    if (k === 'utmccn' && !attrData.utm_campaign) attrData.utm_campaign = v;
                }
            });
        }

        setCookie('cdt_attribution', JSON.stringify(attrData), 30);
    } catch(e) {
        console.error('Attribution tracker error:', e);
    }
})();
</script>
