<?php
/**
 * Author: ExactMetrics team
 * Copyright 2018 ExactMetrics team
 * Author URI: https://exactmetrics.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php if ( 0 == $data['ga_with_gtag'] ):?>
<!-- BEGIN ExactMetrics v<?php echo GADWP_CURRENT_VERSION; ?> Universal Analytics - https://exactmetrics.com/ -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','<?php echo $data['tracking_script_path']?>','ga');
<?php echo $data['trackingcode']?>
</script>
<!-- END ExactMetrics Universal Analytics -->
<?php else:?>
<!-- BEGIN ExactMetrics v<?php echo GADWP_CURRENT_VERSION; ?> Global Site Tag - https://exactmetrics.com/ -->
<script async src="<?php echo $data['tracking_script_path']?>?id=<?php echo $data['uaid']?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
<?php echo $data['trackingcode']?>

  if (window.performance) {
    var timeSincePageLoad = Math.round(performance.now());
    gtag('event', 'timing_complete', {
      'name': 'load',
      'value': timeSincePageLoad,
      'event_category': 'JS Dependencies'
    });
  }
</script>
<!-- END ExactMetrics Global Site Tag -->
<?php endif;?>