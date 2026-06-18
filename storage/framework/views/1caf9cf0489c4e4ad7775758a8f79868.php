<?php $marketing = app(\App\Services\MarketingService::class)->all(); ?>

<?php if($marketing['gtm_id']): ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo e($marketing['gtm_id']); ?>');</script>
<?php endif; ?>
<?php if($marketing['ga_measurement_id']): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($marketing['ga_measurement_id']); ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo e($marketing['ga_measurement_id']); ?>');</script>
<?php endif; ?>
<?php if($marketing['facebook_pixel_id']): ?>
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?php echo e($marketing['facebook_pixel_id']); ?>');</script>
<?php endif; ?>
<?php if($marketing['tiktok_pixel_id']): ?>
<script>!function(w,d,t){w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};ttq.load('<?php echo e($marketing['tiktok_pixel_id']); ?>');}(window,document,'ttq');</script>
<?php endif; ?>
<script>window.__MARKETING__={fb_pixel:'<?php echo e($marketing['facebook_pixel_id'] ?? ''); ?>',ga_id:'<?php echo e($marketing['ga_measurement_id'] ?? ''); ?>',gtm_id:'<?php echo e($marketing['gtm_id'] ?? ''); ?>',tiktok_id:'<?php echo e($marketing['tiktok_pixel_id'] ?? ''); ?>'};</script>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/components/marketing-pixels.blade.php ENDPATH**/ ?>