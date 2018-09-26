<?php defined( 'ABSPATH' ) or exit; ?>

<div id="su_admin_settings" class="wrap su-admin-settings wp-clearfix">

	<h1><?php $this->the_page_title(); ?></h1>

	<?php settings_errors(); ?>

	<form action="options.php" method="post">

		<?php settings_fields( 'shortcodes-ultimate' ); ?>
		<?php do_settings_sections( 'shortcodes-ultimate-settings' ); ?>
		<?php submit_button(); ?>

	</form>

</div>
