<?php
/**
 * Author: ExactMetrics team
 * Copyright 2018 ExactMetrics team
 * Author URI: https://exactmetrics.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<amp-analytics config="https://www.googletagmanager.com/amp.json?id=<?php echo $data['containerid']; ?>&gtm.url=SOURCE_URL" data-credentials="include">
	<script type="application/json">
<?php echo $data['json']; ?>
	</script>
</amp-analytics>