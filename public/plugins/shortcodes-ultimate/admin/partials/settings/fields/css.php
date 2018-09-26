<?php defined( 'ABSPATH' ) or exit; ?>

<textarea name="<?php echo esc_attr( $data['id'] ); ?>" id="<?php echo esc_attr( $data['id'] ); ?>" cols="50" rows="15" class="large-text"><?php echo esc_textarea( get_option( $data['id'] ) ); ?></textarea>

<p class="description"><?php echo $data['description']; ?></p>

<h4 class="title"><?php _e( 'Available variables', 'shortcodes-ultimate' ); ?></h4>
<ul>
	<li><code>%home_url%</code> - <?php printf( '%s (%s)', __( 'the URL of the site home page', 'shortcodes-ultimate' ), __( 'with trailing slash', 'shortcodes-ultimate' ) ); ?></li>
	<li><code>%theme_url%</code> - <?php printf( '%s (%s)', __( 'the URL of the directory of the current theme', 'shortcodes-ultimate' ), __( 'with trailing slash', 'shortcodes-ultimate' ) ); ?></li>
	<li><code>%plugin_url%</code> - <?php printf( '%s (%s)', __( 'the URL of the directory of the plugin', 'shortcodes-ultimate' ), __( 'with trailing slash', 'shortcodes-ultimate' ) ); ?></li>
</ul>

<h4 class="title"><?php _e( 'More information', 'shortcodes-ultimate' ); ?></h4>

<ul class="ul-disc">
	<li><?php _e( 'See help tab at the top right corner of this page for more information.', 'shortcodes-ultimate' ); ?></li>
	<li><?php printf( __( 'Open %s file to see default styles.', 'shortcodes-ultimate' ), '<a href="' . $this->plugin_url . 'includes/css/shortcodes.css" target="_blank">shortcodes.css</a>' ); ?></li>
</ul>
