<?php
/**
 * Author: ExactMetrics team
 * Copyright 2018 ExactMetrics team
 * Author URI: https://exactmetrics.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<script>
var gadwpDnt = false;
var gadwpProperty = '<?php echo $data['uaid']?>';
var gadwpDntFollow = <?php echo $data['gaDntOptout'] ? 'true' : 'false'?>;
var gadwpOptout = <?php echo $data['gaOptout'] ? 'true' : 'false'?>;
var disableStr = 'ga-disable-' + gadwpProperty;
if(gadwpDntFollow && (window.doNotTrack === "1" || navigator.doNotTrack === "1" || navigator.doNotTrack === "yes" || navigator.msDoNotTrack === "1")) {
	gadwpDnt = true;
}
if (gadwpDnt || (document.cookie.indexOf(disableStr + '=true') > -1 && gadwpOptout)) {
	window[disableStr] = true;
}
function gaOptout() {
	var expDate = new Date;
	expDate.setFullYear(expDate.getFullYear( ) + 10);
	document.cookie = disableStr + '=true; expires=' + expDate.toGMTString( ) + '; path=/';
	window[disableStr] = true;
}
</script>
