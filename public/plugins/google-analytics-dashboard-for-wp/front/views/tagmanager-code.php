<?php
/**
 * Author: ExactMetrics team
 * Copyright 2018 ExactMetrics team
 * Author URI: https://exactmetrics.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<!-- BEGIN GADWP v<?php echo GADWP_CURRENT_VERSION; ?> Tag Manager - https://exactmetrics.com/ -->
<script>
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push(<?php echo $data['vars']; ?>);
</script>

<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','<?php echo $data['containerid']; ?>');
</script>
<!-- END GADWP Tag Manager -->

