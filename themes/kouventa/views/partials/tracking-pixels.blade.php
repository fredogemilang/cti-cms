{{-- Kouventa Production Marketing Pixels (Meta Pixel + Ahrefs Analytics) --}}
@if(str_contains((string) request()->getHost(), 'kouventa.ai'))
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '7313138518763661');
fbq('track', 'PageView');
</script>
<!-- End Meta Pixel Code -->
<!-- Ahrefs Analytics -->
<script src="https://analytics.ahrefs.com/analytics.js" data-key="6xEJdqjGjCM/oWAa/5jrgQ" async></script>
@endif
