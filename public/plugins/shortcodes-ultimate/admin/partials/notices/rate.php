<?php defined( 'ABSPATH' ) or exit; ?>

<div class="notice notice-info shortcodes-ultimate-notice-rate">

	<?php echo get_avatar( 'ano.vladimir@gmail.com', 60, '', __( 'Vladimir Anokhin', 'shortcodes-ultimate' ) ); ?>

	<div class="shortcodes-ultimate-notice-rate-content">

		<p><?php _e( 'Hello', 'shortcodes-ultimate' ); ?>,</p>
		<p><?php _e( 'my name is Vladimir Anokhin. I am the developer of the plugin Shortcodes Ultimate.<br>If you like this plugin, please write few words about it at wordpress.org or twitter. Your opinion will help other people to find this useful plugin much faster.', 'shortcodes-ultimate' ); ?></p>
		<p><?php _e( 'Thank you!', 'shortcodes-ultimate' ); ?></p>

		<p class="shortcodes-ultimate-notice-rate-actions">
			<a href="<?php echo $this->get_dismiss_link(); ?>" class="button button-primary" onclick="window.open('https://wordpress.org/support/plugin/shortcodes-ultimate/reviews/?rate=5&filter=5#new-post');"><?php _e( 'Rate plugin', 'shortcodes-ultimate' ); ?></a>
			<a href="<?php echo $this->get_dismiss_link( true ); ?>"><?php _e( 'Remind me later', 'shortcodes-ultimate' ); ?></a>
			<a href="<?php echo $this->get_dismiss_link(); ?>" class="shortcodes-ultimate-notice-rate-dismiss"><?php _e( 'Dismiss', 'shortcodes-ultimate' ); ?></a>
		</p>

	</div>

</div>

<style>
	.shortcodes-ultimate-notice-rate {
		position: relative;
		padding: 15px 20px;
	}
	.shortcodes-ultimate-notice-rate .avatar {
		position: absolute;
		left: 20px;
		top: 20px;
		border-radius: 50%;
	}
	.shortcodes-ultimate-notice-rate-content {
		margin-left: 80px;
	}
	p.shortcodes-ultimate-notice-rate-actions {
		margin-top: 15px;
	}
	p.shortcodes-ultimate-notice-rate-actions a {
		vertical-align: middle !important;
	}
	p.shortcodes-ultimate-notice-rate-actions a + a {
		margin-left: 20px;
	}
	.shortcodes-ultimate-notice-rate-dismiss {
		position: absolute;
		top: 10px;
		right: 10px;
		padding: 10px 15px 10px 21px;
		font-size: 13px;
		line-height: 1.23076923;
		text-decoration: none;
	}
	.shortcodes-ultimate-notice-rate-dismiss:before {
		position: absolute;
		top: 8px;
		left: 0;
		margin: 0;
		-webkit-transition: all .1s ease-in-out;
		transition: all .1s ease-in-out;
		background: 0 0;
		color: #b4b9be;
		content: "\f153";
		display: block;
		font: 400 16px / 20px dashicons;
		height: 20px;
		text-align: center;
		width: 20px;
	}
	.shortcodes-ultimate-notice-rate-dismiss:hover:before {
		color: #c00;
	}
</style>
