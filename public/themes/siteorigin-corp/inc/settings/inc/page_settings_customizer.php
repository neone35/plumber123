<?php

class SiteOrigin_Settings_Page_Settings_Customizer {

	function __construct(){
		// Customizer integration
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_enqueue_preview' ) );
	}

	/**
	 * Create the singleton
	 *
	 * @return SiteOrigin_Settings
	 */
	static function single(){
		static $single;

		if( empty($single) ) {
			$single = new self();
		}

		return $single;
	}

	/**
	 * Register all the archives in the customizer
	 *
	 * @param $wp_customize
	 */
	function customize_register( $wp_customize ){
		if( !current_theme_supports( 'siteorigin-template-settings' ) ) return;

		// We'll use a single panel for theme settings
		if( method_exists($wp_customize, 'add_panel') ) {
			$wp_customize->add_panel( 'page_settings', array(
				'title' => __( 'Page Template Settings', 'siteorigin-corp' ),
				'description' => __( 'Change layouts for various pages on your site.', 'siteorigin-corp' ),
				'priority' => 11,
			) );
		}

		// Add general page templates
		$types = array();
		$templates = array(
			'home' => __( 'Blog Page', 'siteorigin-corp' ),
			'search' => __( 'Search Results', 'siteorigin-corp' ),
			'date' => __( 'Date Archives', 'siteorigin-corp' ),
			'author' => __( 'Author Archives', 'siteorigin-corp' ),
			'404' => __( 'Not Found', 'siteorigin-corp' ),
		);
		foreach ( $templates as $template => $title ) {
			$types[] = array(
				'group' => 'template',
				'id' => $template,
				'title' => $title
			);
		}

		// Add public post types
		$post_types = get_post_types( array( 'public' => true, 'has_archive' => true ), 'objects' );
		foreach( $post_types as $post_type => $post_type_data ) {
			if( empty( $post_type_data->label ) ) continue;

			$types[] = array(
				'group' => 'archive',
				'id' => $post_type,
				'title' => __( 'Type', 'siteorigin-corp' ) . ': ' . $post_type_data->label
			);
		}

		$taxonomies = get_taxonomies( array( 'public' => true, 'publicly_queryable' => true ), 'objects' );
		foreach( $taxonomies as $tax_slug => $taxonomy ) {
			if( empty( $taxonomy->label ) ) continue;

			$types[] = array(
				'group' => 'taxonomy',
				'id' => $tax_slug,
				'title' => __( 'Taxonomy', 'siteorigin-corp' ) . ': ' . $taxonomy->label
			);
		}

		// Now add controls for all the sections
		foreach( $types as $i => $type ) {
			$wp_customize->add_section( 'page_settings_' . $type['group'] . '_' . $type['id'], array(
				'title' => $type['title'],
				'priority' => ( $i * 5 ) + 10,
				'panel' => 'page_settings',
			) );

			// Now add the settings
			$settings = SiteOrigin_Settings_Page_Settings::single()->get_settings( $type['group'], $type['id'] );
			$defaults = SiteOrigin_Settings_Page_Settings::single()->get_settings_defaults( $type['group'], $type['id'] );

			foreach( $settings as $id => $setting ) {
				$sanitize_callback = 'sanitize_text_field';
				switch( $setting['type'] ) {
					case 'checkbox':
						$sanitize_callback = array( 'SiteOrigin_Settings_Sanitize', 'boolean' );
						break;
				}

				$wp_customize->add_setting( 'page_settings_' . $type['group'] . '_' . $type['id'] . '[' . $id . ']' , array(
					'default' => isset( $defaults[ $id ] ) ? $defaults[ $id ] : false,
					'transport' => 'refresh',
					'capability' => 'edit_theme_options',
					'type' => 'theme_mod',
					'sanitize_callback' => $sanitize_callback,
				) );

				// Setup the control arguments for the controller
				$control_args = array(
					'label' => $setting['label'],
					'type' => $setting['type'],
					'description' => !empty( $setting['description'] ) ? $setting['description'] : false,
					'section'  => 'page_settings_' . $type['group'] . '_' . $type['id'],
					'settings' => 'page_settings_' . $type['group'] . '_' . $type['id'] . '[' . $id . ']',
				);

				if( $setting['type'] == 'select' ) {
					$control_args['choices'] = $setting['options'];
				}

				$wp_customize->add_control(
					'page_settings_' . $type['group'] . '_' . $type['id'] . '_' . $id,
					$control_args
				);
			}
		}
	}

	function enqueue_customizer(){
		if( !current_theme_supports( 'siteorigin-template-settings' ) ) return;

		wp_enqueue_script(
			'siteorigin-page-template-settings',
			get_template_directory_uri() . '/inc/settings/js/page-settings-admin' . SITEORIGIN_THEME_JS_PREFIX . '.js',
			array( 'jquery', 'customize-controls' ),
			SITEORIGIN_THEME_VERSION
		);
	}

	/**
	 *
	 */
	function customize_enqueue_preview(){
		if( !current_theme_supports( 'siteorigin-template-settings' ) ) return;

		wp_enqueue_script(
			'siteorigin-page-template-settings',
			get_template_directory_uri() . '/inc/settings/js/page-settings' . SITEORIGIN_THEME_JS_PREFIX . '.js',
			array( 'jquery', 'customize-preview' ),
			SITEORIGIN_THEME_VERSION
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'customize_preview_localize' ) );
	}

	function customize_preview_localize(){
		wp_localize_script( 'siteorigin-page-template-settings', 'soTemplateSettings', array(
			'page' => SiteOrigin_Settings_Page_Settings::get_current_page(),
		) );
	}
}

SiteOrigin_Settings_Page_Settings_Customizer::single();
