<?php

class SiteOrigin_Settings_About_Page {

	function __construct(){
		add_action( 'load-themes.php', array( $this, 'activation_admin_notice' ) );
		add_action( 'admin_menu', array( $this, 'add_theme_page' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	static function single(){
		static $single;
		if( empty( $single ) ) {
			$single = new self();
		}
		return $single;
	}

	public function activation_admin_notice() {
		global $pagenow;

		if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'about_page_notice' ), 99 );
		}
	}

	function about_page_notice(){
		$theme = wp_get_theme( get_template() );

		?>
		<div class="updated notice is-dismissible">
			<p>
				<?php echo esc_html( sprintf( __( 'Thanks for choosing %s!', 'siteorigin-corp' ), $theme->get( 'Name' ) ) ); ?>
				<?php
				printf(
					esc_html__( 'You can learn more about it %1$shere%2$s, or head straight to the %3$scustomizer%4$s to start setting it up.', 'siteorigin-corp' ),
					'<a href="' . admin_url( 'themes.php?page=siteorigin-theme-about' ) . '">',
					'</a>',
					'<a href="' . admin_url( 'customize.php' ) . '">',
					'</a>'
				); ?>
			</p>
			<p>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=siteorigin-theme-about' ) ); ?>" class="button-primary">
					<?php echo esc_html( sprintf( __( 'Learn About %s', 'siteorigin-corp' ), $theme->get( 'Name' ) ) ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	function add_theme_page( ){
		$theme = wp_get_theme( get_template() );
		$theme_name = $theme->get( 'Name' );

		add_theme_page(
			sprintf( __( 'About %s', 'siteorigin-corp' ), $theme_name ),
			sprintf( __( 'About %s', 'siteorigin-corp' ), $theme_name ),
			'edit_theme_options',
			'siteorigin-theme-about',
			array( $this, 'display_about_page' )
		);
	}

	function enqueue_scripts( $prefix ) {
		if( $prefix !== 'appearance_page_siteorigin-theme-about' ) return;

		wp_enqueue_script(
			'siteorigin-settings-about',
			get_template_directory_uri() . '/inc/settings/js/about' . SITEORIGIN_THEME_JS_PREFIX . '.js',
			array( 'jquery' ),
			SITEORIGIN_THEME_VERSION
		);

		wp_enqueue_style(
			'siteorigin-settings-about',
			get_template_directory_uri() . '/inc/settings/css/about.css',
			array( ),
			SITEORIGIN_THEME_VERSION
		);
	}

	function get_share_link( $network ) {
		$theme = wp_get_theme( get_template() );
		$share_url = false;

		switch( $network ) {
			case 'google_plus' :
				$share_url = add_query_arg( array(
					'url' => urlencode( $theme->get( 'ThemeURI' ) )
				), 'https://plus.google.com/share' );
				break;

			case 'twitter' :
				$share_url = add_query_arg( array(
					'status' => urlencode(
						$theme->get( 'Name' ) .
						' - ' .
						__( 'Free #WordPress Theme from @SiteOrigin', 'siteorigin-corp' ) . ' - ' .
						$theme->get( 'ThemeURI' )
					)
				), 'https://twitter.com/home' );
				break;

			case 'facebook' :
				$share_url = add_query_arg( array(
					'u' => urlencode( $theme->get( 'ThemeURI' ) )
				), 'https://www.facebook.com/sharer/sharer.php' );
		}

		return $share_url;
	}

	function display_about_page(){

		$theme = wp_get_theme( get_template() );
		$about = apply_filters( 'siteorigin_about_page', array(
			'title' => sprintf( __( 'About %s', 'siteorigin-corp' ), $theme->get( 'Name' ) ),
			'sections' => array(),
			'title_image' => false,
			'title_image_2x' => false,
			'version' => $theme->get( 'Version' ),
			'description' => $theme->get( 'Description' ),
			'video_thumbnail' => array(
				$theme->get_screenshot()
			),
			'video_url' => add_query_arg( 'autoplay', 1, $theme->get( 'ThemeURI' ) ),
			'video_description' => false,
			'newsletter_url' => 'https://siteorigin.com/#newsletter',
			'tour_url' => '',
			'documentation_url' => '',
			'premium_url' => SiteOrigin_Settings::get_premium_url( 'theme' ),
			'review_url' => sprintf( 'https://wordpress.org/support/view/theme-reviews/%s?filter=5#postform', get_template() ),
		) );

		?>
		<div class="wrap" id="siteorigin-about-page">
			<ul class="top-area-tabs">

				<?php if( !empty( $about[ 'tour_url' ] ) ) : ?>
					<li>
						<a href="<?php echo esc_url( $about[ 'tour_url' ] ) ?>" class="about-button-tour" target="_blank">
							<?php esc_html_e( 'Take a Tour', 'siteorigin-corp' ) ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if( !empty( $about[ 'newsletter_url' ] ) ) : ?>
					<li>
						<a href="<?php echo esc_url( $about[ 'newsletter_url' ] ) ?>" class="about-button-updates" target="_blank">
							<span class="dashicons dashicons-email"></span>
							<?php esc_html_e( 'Get Updates', 'siteorigin-corp' ) ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if( !empty( $about[ 'documentation_url' ] ) ) : ?>
					<li>
						<a href="<?php echo esc_url( $about[ 'documentation_url' ] ) ?>" class="about-button-docs" target="_blank">
							<span class="dashicons dashicons-sos"></span>
							<?php esc_html_e( 'Documentation', 'siteorigin-corp' ) ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if( !empty( $about[ 'review_url' ] ) ) : ?>
					<li>
						<a href="<?php echo esc_url( $about[ 'review_url' ] ) ?>" class="about-button-updates" target="_blank">
							<span class="dashicons dashicons-star-filled"></span>
							<?php esc_html_e( 'Write a Review', 'siteorigin-corp' ) ?>
						</a>
					</li>
				<?php endif; ?>

				<?php if( ! empty( $about[ 'premium_url' ] ) && ! class_exists( 'SiteOrigin_Premium' ) ) : ?>
					<li class="about-highlight">
						<a href="<?php echo esc_url( $about[ 'premium_url' ] ) ?>" class="about-button-updates" target="_blank">
							<span class="dashicons dashicons-arrow-up-alt"></span>
							<?php esc_html_e( 'Upgrade to Premium', 'siteorigin-corp' ) ?>
						</a>
					</li>
				<?php endif; ?>

			</ul>
			
			<div class="about-header">
				<div class="about-container">
					<?php if ( ! empty( $about[ 'title_image' ] ) ) : ?>
						<div class="title-image-wrapper">
							<img
								src="<?php echo esc_url( $about[ 'title_image' ] ) ?>"
								title="<?php echo esc_attr( $about[ 'title' ] ) ?>"
						        <?php if( ! empty( $about[ 'title_image_2x' ] ) ) : ?>
						            srcset="<?php echo esc_url( $about[ 'title_image_2x' ] ) ?> 2x"
						        <?php endif ?>
						        />
							<div class="version"><?php echo esc_html( $about['version'] ) ?></div>
						</div>
					<?php else : ?>
						<h1 class="title-image-wrapper">
							<?php echo esc_html( $about[ 'title' ] ) ?>
							<div class="version"><?php echo esc_html( $about['version'] ) ?></div>
						</h1>
					<?php endif; ?>
				</div>
			</div>

			<?php if( ! empty( $about[ 'video_thumbnail' ] ) ) : ?>
				<div class="about-video">
					<div class="about-container">
						<a href="<?php echo esc_url( $about[ 'video_url' ] ) ?>" class="about-play-video" target="_blank">
							<?php if( empty( $about[ 'no_video' ] ) ) : ?>
								<svg version="1.1" id="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								     viewBox="0 0 540 320.6" style="enable-background:new 0 0 540 320.6;" xml:space="preserve">
									<path class="st0" d="M511,0H29C13,0,0,13,0,29v262.6c0,16,13,29,29,29h482c16,0,29-13,29-29V29C540,13,527,0,511,0z"/>
									<path class="st1" d="M326.9,147.3c4.2,2.6,6.9,7.6,6.9,13c0,5.4-2.7,10.3-7.2,13.2l-94.9,69.9c-2.6,2.2-6.1,3.5-9.8,3.5
									c-8.7,0-15.7-7-15.7-15.7V89.4c0-8.6,7-15.7,15.7-15.7c3.7,0,7.3,1.3,10.1,3.7L326.9,147.3z"/>
								</svg>
							<?php endif ?>
						</a>

						<div class="about-video-images">
							<?php
							if( is_array( $about[ 'video_thumbnail' ] ) ) {
								$images = $about[ 'video_thumbnail' ];
							}
							else {
								$images = array( $about[ 'video_thumbnail' ] );
							}

							foreach( $images as $image ) {
								?><div style="background-image: url(<?php echo esc_url( $image ) ?>);" class="about-video-image"></div><?php
							}
							?>
						</div>

						<?php if( empty( $about[ 'no_video' ] ) ) : ?>
							<div class="about-video-watch">
								<a href="<?php echo esc_url( $about[ 'video_url' ] ) ?>" target="_blank">
									<?php esc_html_e( 'Watch The Video', 'siteorigin-corp' ) ?>
								</a>
							</div>
						<?php endif ?>

						<?php if( ! empty( $about['description'] ) ) : ?>
							<div class="about-video-description">
								<?php echo wp_kses_post( $about['description'] ) ?>
							</div>
						<?php endif; ?>

						<?php if( $theme->get( 'ThemeURI' ) ) : ?>
							<div class="about-share">
								<div class="about-share-title">
									<?php echo esc_html( sprintf( __( 'If you like %s, please share it!', 'siteorigin-corp' ), $theme->get( 'Name' ) ) ) ?>
								</div>

								<a href="<?php echo esc_url( $this->get_share_link( 'facebook' ) ) ?>" class="about-share-facebook" target="_blank">
									<span class="dashicons dashicons-facebook-alt"></span>
								</a>
								<a href="<?php echo esc_url( $this->get_share_link( 'twitter' ) ) ?>" class="about-share-twitter" target="_blank">
									<span class="dashicons dashicons-twitter"></span>
								</a>
								<a href="<?php echo esc_url( $this->get_share_link( 'google_plus' ) ) ?>" class="about-share-googleplus" target="_blank">
									<span class="dashicons dashicons-googleplus"></span>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if( ! empty( $about['sections'] ) ) : ?>
				<div class="about-sections">
					<?php foreach( $about['sections'] as $section ) : if( is_string( $section ) ) $section = array( 'id' => $section ) ?>
						<div class="about-section about-container">
							<?php get_template_part( 'admin/about/page', $section['id'] ) ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="about-siteorigin-logo">
				<p>
					<?php echo esc_html( __( 'Proudly Created By', 'siteorigin-corp' ) ) ?>
				</p>
				<a href="https://siteorigin.com/" target="_blank">
					<img src="<?php echo get_template_directory_uri() ?>/inc/settings/css/images/siteorigin.png" />
				</a>
			</div>
		</div>
		<?php
	}
}
