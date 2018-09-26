<?php

/**
* Class SiteOrigin_Settings
 *
 * A simple settings framework that works with the customizer in magical ways.
*/
class SiteOrigin_Settings {

	/**
	 * @var array Default setting values
	 */
	private $defaults;

	/**
	 * @var The current theme name
	 */
	private $theme_name;

	/**
	 * @var array The theme settings
	 */
	private $settings;

	/**
	 * @var array The settings sections
	 */
	private $sections;

	function __construct(){
		$this->add_actions();

		$this->defaults = array();
		$this->settings = array();
		$this->sections = array();
		$this->loc = array();

		if( !empty( $_POST['wp_customize'] ) && $_POST['wp_customize'] == 'on' && is_customize_preview() ) {
			add_filter( 'siteorigin_setting', array( $this, 'customizer_filter' ), 15, 2 );
		}

		add_action( 'after_setup_theme', array( $this, 'load_settings_extras' ) );
		add_action( 'after_setup_theme', array( $this, 'handle_migrations' ) );

		spl_autoload_register( array( $this, '_autoload' ) );

		if( current_user_can( 'install_themes' ) && is_admin() ) {
			SiteOrigin_Settings_Upgrade::single();
		}
	}

	/**
	 * Create the singleton
	 *
	 * @return SiteOrigin_Settings
	 */
	static function single(){
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}

	function _autoload( $class_name ){
		if( strpos( $class_name, 'SiteOrigin_Settings_Control_' ) === 0 ) {
			$file = strtolower( str_replace( 'SiteOrigin_Settings_Control_', '', $class_name ) );
			include( dirname( __FILE__ ) . '/inc/control/' . $file . '.php' );
		}
		elseif ( strpos( $class_name, 'SiteOrigin_Settings_' ) === 0 ) {
			$file = strtolower( str_replace( 'SiteOrigin_Settings_', '', $class_name ) );
			include( dirname( __FILE__ ) . '/inc/' . $file . '.php' );
		}
	}

	/**
	 * Get a theme setting value
	 *
	 * @param $setting
	 *
	 * @return string
	 */
	function get( $setting ) {
		static $old_settings = false;
		if( $old_settings === false ) {
			$old_settings = get_option( get_template() . '_theme_settings' );
		}

		if( isset( $old_settings[$setting] ) ) {
			$default = $old_settings[$setting];
		}
		else {
			$default = isset( $this->defaults[$setting] ) ? $this->defaults[$setting] : false;
		}

		// Handle setting migration
		return apply_filters( 'siteorigin_setting', get_theme_mod( 'theme_settings_' . $setting, $default ), $setting );
	}

	/**
	 * Handle migration of settings from one key to another
	 */
	function handle_migrations(){
		$migrations = apply_filters( 'siteorigin_settings_migrated_settings', array(  ) );
		if( empty( $migrations ) ) return;

		$migration_key = md5( serialize( $migrations ) );
		if( $migration_key !== get_theme_mod( 'migration_key' ) ) {
			foreach( $migrations as $to => $from ) {
				$raw_to = get_theme_mod( 'theme_settings_' . $to, null );
				$raw_from = get_theme_mod( 'theme_settings_' . $from, null );

				if( is_null( $raw_to ) && ! is_null( $raw_from ) ) {
					set_theme_mod( 'theme_settings_' . $to, $raw_from );
				}
			}

			set_theme_mod( 'migration_key', $migration_key );
		}
	}

	/**
	 * Filter SiteOrigin settings based on customizer values. Gets around early use of setting values in customizer preview.
	 *
	 * @param $value
	 * @param $setting
	 *
	 * @return mixed
	 */
	function customizer_filter( $value, $setting ){
		if (
			empty( $_REQUEST['nonce'] ) ||
			!wp_verify_nonce( $_REQUEST['nonce'], 'preview-customize_' . get_stylesheet() )
		) return $value;

		static $customzier_values = null;
		if( is_null( $customzier_values ) && ! empty( $_POST['customized'] ) ) {
			$customzier_values =  json_decode( stripslashes( $_POST['customized'] ), true );
		}

		if( isset( $customzier_values[ 'theme_settings_' . $setting ] ) ) {
			$value = $customzier_values[ 'theme_settings_' . $setting ];
		}

		return $value;
	}

	/**
	 * Get all theme settings values currently in the database.
	 *
	 * @param bool $defaults Should we add the defaults.
	 *
	 * @return array|void
	 */
	function get_all( $defaults = false ){
		$settings = get_theme_mods();
		if( empty($settings) ) return array();

		foreach( array_keys($settings) as $k ) {
			if( strpos( $k, 'theme_settings_' ) !== 0 ) {
				unset($settings[$k]);
			}
		}

		if( $defaults ) {
			$settings = wp_parse_args( $settings, $this->defaults );
		}

		return $settings;
	}

	/**
	 * Set a theme setting value. Simple wrapper for set theme mod.
	 *
	 * @param $setting
	 * @param $value
	 */
	function set( $setting, $value ) {
		set_theme_mod( 'theme_settings_' . $setting, $value );
		set_theme_mod( 'custom_css_key', false );
	}

	/**
	 * Add all the necessary actions
	 */
	function add_actions(){
		add_action( 'after_setup_theme', array( $this, 'init' ), 5 );
		add_action( 'customize_register', array( $this, 'customize_register' ) );

		add_action( 'customize_preview_init', array( $this, 'enqueue_preview' ) );
		add_action( 'wp_head', array( $this, 'display_custom_css' ), 11 );
	}

	/**
	 * Check if a setting is currently at its default value
	 *
	 * @param string $setting The setting name.
	 *
	 * @return bool Is the setting current at its default value.
	 */
	function is_default( $setting ){
		$default = $this->get_default( $setting );
		return $this->get($setting) == $default;
	}

	/**
	 * Get the default value for the setting
	 *
	 * @param string $setting The name of the setting
	 *
	 * @return bool|mixed
	 */
	function get_default( $setting ) {
		return isset( $this->defaults[$setting] ) ? $this->defaults[$setting] : false;
	}

	/**
	 * Initialize the theme settings
	 */
	function init(){
		$theme = wp_get_theme();
		$this->theme_name = $theme->get_template();
		$this->defaults = apply_filters( 'siteorigin_settings_defaults', $this->defaults );
	}

	/**
	 * @param array $settings
	 */
	function configure( $settings ){
		foreach( $settings as $section_id => $section ) {
			$this->add_section( $section_id, !empty($section['title']) ? $section['title'] : '' );
			$fields = !empty($section['fields']) ? $section['fields'] : array();
			foreach( $fields as $field_id => $field ) {
				$args = array_merge(
					!empty($field['args']) ? $field['args'] : array(),
					$field
				);
				unset($args['label']);
				unset($args['type']);
				unset($args['teaser']);

				if( !empty($field['teaser']) ) {
					$this->add_teaser(
						$section_id,
						$field_id,
						$field['type'],
						!empty($field['label']) ? $field['label'] : '',
						$args
					);
				}
				else {
					$this->add_field(
						$section_id,
						$field_id,
						$field['type'],
						!empty($field['label']) ? $field['label'] : '',
						$args
					);
				}
			}
		}
	}

	/**
	 * @param $id
	 * @param $title
	 * @param string|bool $after Add this section after another one
	 */
	function add_section( $id, $title, $after = false ) {

		if( $after === false ) {
			$index = null;
		}
		else if( $after === '' ) {
			$index = 0;
		}
		else if( $after !== false ) {
			$index = array_search( $after, array_keys( $this->sections ) ) + 1;
			if( $index == count( array_keys($this->sections) ) ) {
				$index = null;
			}
		}

		$new_section = array( $id => array(
			'id' => $id,
			'title' => $title,
		) );

		if( $index === null ) {
			// Null means we add this at the end or the current position
			$this->sections = array_merge(
				$this->sections,
				$new_section
			);
		}
		else if( $index === 0 ) {
			$this->sections = array_merge(
				$new_section,
				$this->sections
			);
		}
		else {
			$this->sections = array_merge(
				array_slice( $this->sections, 0, $index, true ),
				$new_section,
				array_slice( $this->sections, $index, count($this->sections), true )
			);
		}

		if( empty($this->settings[$id]) ) {
			$this->settings[$id] = array();
		}
	}

	/**
	 * Add a new settings field
	 *
	 * @param $section
	 * @param $id
	 * @param $type
	 * @param null $label
	 * @param array $args
	 * @param string|bool $after Add this field after another one
	 */
	function add_field( $section, $id, $type, $label = null, $args = array(), $after = false ) {

		if( empty($this->settings[$section]) ) {
			$this->settings[$section] = array();
		}

		$new_field = array(
			'id' => $id,
			'type' => $type,
			'label' => $label,
			'args' => $args,
		);

		if( isset($this->settings[$section][$id]) ) {
			$this->settings[$section][$id] = wp_parse_args(
				$new_field,
				$this->settings[$section][$id]
			);
		}

		if( $after === false ) {
			$index = null;
		}
		else if( $after === '' ) {
			$index = 0;
		}
		else if( $after !== false ) {
			$index = array_search( $after, array_keys( $this->settings[$section] ) ) + 1;
			if( $index == count( $this->settings[$section] ) ) {
				$index = null;
			}
		}

		if( $index === null ) {
			// Null means we add this at the end or the current position
			$this->settings[$section] = array_merge(
				$this->settings[$section],
				array( $id => $new_field )
			);
		}
		else if( $index === 0 ) {
			$this->settings[$section] = array_merge(
				array( $id => $new_field ),
				$this->settings[$section]
			);
		}
		else {
			$this->settings[$section] = array_merge(
				array_slice( $this->settings[$section], 0, $index, true ),
				array( $id => $new_field ),
				array_slice( $this->settings[$section], $index, count( $this->settings[$section] ), true )
			);
		}

	}

	/**
	 * Add a teaser field that points to a premium upgrade page
	 *
	 * @param $section
	 * @param $id
	 * @param $type
	 * @param $label
	 * @param array $args
	 * @param string|bool $after Add this field after another one
	 */
	function add_teaser( $section, $id, $type, $label, $args = array(), $after = false ) {
		if( apply_filters('siteorigin_settings_display_teaser', true, $section, $id) ) {
			// The theme hasn't implemented this setting yet
			$this->add_field( $section, $id, 'teaser', $label, $args, $after);
		}
		else {
			// Handle this field elsewhere
			do_action( 'siteorigin_settings_add_teaser_field', $this, $section, $id, $type, $label, $args, $after );
		}
	}

	static $control_classes = array(
		'media' => 'WP_Customize_Media_Control',
		'color' => 'WP_Customize_Color_Control',
		'teaser' => 'SiteOrigin_Settings_Control_Teaser',
		'image_select' => 'SiteOrigin_Settings_Control_Image_Select',
		'font' => 'SiteOrigin_Settings_Control_Font',
		'widget' => 'SiteOrigin_Settings_Control_Widget',
		'measurement' => 'SiteOrigin_Settings_Control_Measurement',
	);

	static $sanitize_callbacks = array(
		'url' => 'esc_url_raw',
		'color' => 'sanitize_hex_color',
		'media' => array( 'SiteOrigin_Settings_Sanitize', 'intval' ),
		'checkbox' => array( 'SiteOrigin_Settings_Sanitize', 'boolean' ),
		'range' => array( 'SiteOrigin_Settings_Sanitize', 'floatval' ),
		'widget' => array( 'SiteOrigin_Settings_Sanitize', 'widget' ),
		'measurement' => array( 'SiteOrigin_Settings_Control_Measurement', 'sanitize_value' ),
	);

	/**
	 * Register everything for the customizer
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function customize_register( $wp_customize ){
		// Let everything setup the settings
		if( !did_action( 'siteorigin_settings_init' ) ) {
			do_action( 'siteorigin_settings_init' );
		}

		// We'll use a single panel for theme settings
		if( method_exists($wp_customize, 'add_panel') ) {
			$wp_customize->add_panel( 'theme_settings', array(
				'title' => __( 'Theme Settings', 'siteorigin-corp' ),
				'description' => __( 'Change settings for your theme.', 'siteorigin-corp' ),
				'priority' => 10,
			) );
		}

		if(
			! defined( 'SITEORIGIN_IS_PREMIUM' ) &&
			! defined( 'SITEORIGIN_PREMIUM_VERSION' )
			&& defined( 'SITEORIGIN_THEME_PREMIUM_URL' )
		) {
			$wp_customize->add_section( 'theme_settings_premium', array(
				'title' => __( 'SiteOrigin Premium', 'siteorigin-corp' ),
				'priority' => 1,
				'panel' => 'theme_settings',
			) );

			$wp_customize->add_setting( 'theme_settings_premium_message' , array(
				'default' => '',
				'transport' => 'refresh',
				'capability' => 'edit_theme_options',
				'type' => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
			) );

			$wp_customize->add_control(
				new SiteOrigin_Settings_Control_Premium(
					$wp_customize,
					'theme_settings_premium_message',
					array(
						'label' => __( 'SiteOrigin Premium', 'siteorigin-corp' ),
						'section'  => 'theme_settings_premium',
						'settings' => 'theme_settings_premium_message',
					)
				)
			);
		}

		// Add sections for what would have been tabs before
		$i = 0;
		foreach( $this->sections as $id => $args ) {
			$i++;
			$wp_customize->add_section( 'theme_settings_' . $id, array(
				'title' => $args['title'],
				'priority' => ( $i * 5 ) + 10,
				'panel' => 'theme_settings',
			) );
		}

		// Handle old settings for legacy reasons
		static $old_settings = false;
		if( $old_settings === false ) {
			$old_settings = get_option( get_template() . '_theme_settings' );
		}

		// Finally, add the settings
		foreach( $this->settings as $section_id => $settings ) {
			foreach( $settings as $setting_id => $setting_args ) {
				$control_class = false;

				// Setup the sanitize callback
				$sanitize_callback = 'sanitize_text_field';
				if( !empty( $setting_args['args']['sanitize_callback'] ) ) {
					$sanitize_callback = $setting_args['args']['sanitize_callback'];
				}
				else if( !empty( self::$sanitize_callbacks[ $setting_args['type'] ] ) ) {
					$sanitize_callback = self::$sanitize_callbacks[ $setting_args['type'] ];
				}

				// Get the default value
				if( isset( $old_settings[ $section_id . '_' . $setting_id ] ) ) {
					$default = $old_settings[$section_id . '_' . $setting_id];
				}
				else {
					$default = isset( $this->defaults[ $section_id . '_' . $setting_id ] ) ? $this->defaults[ $section_id . '_' . $setting_id ] : '';
				}

				// Create the customizer setting
				$wp_customize->add_setting( 'theme_settings_' . $section_id . '_' . $setting_id , array(
					'default' => $default,
					'transport' => empty($setting_args['args']['live']) ? 'refresh' : 'postMessage',
					'capability' => 'edit_theme_options',
					'type' => 'theme_mod',
					'sanitize_callback' => $sanitize_callback,
				) );

				// Setup the control arguments for the controller
				$control_args = array(
					'label' => $setting_args['label'],
					'section'  => 'theme_settings_' . $section_id,
					'settings' => 'theme_settings_' . $section_id . '_' . $setting_id,
				);

				if( !empty( $setting_args['args']['description'] ) ) {
					$control_args['description'] = $setting_args['args']['description'];
				}

				// Add different control args for the different field types
				if( $setting_args['type'] == 'radio' || $setting_args['type'] == 'select' || $setting_args['type'] == 'image_select' || $setting_args['type'] == 'text' ) {
					if( !empty($setting_args['args']['options']) ) {
						$control_args['choices'] = $setting_args['args']['options'];
					}
					if( !empty($setting_args['args']['choices']) ) {
						$control_args['choices'] = $setting_args['args']['choices'];
					}

					if( $setting_args['type'] == 'text' && ! empty( $control_args['choices'] ) ) {
						$control_class = 'SiteOrigin_Settings_Control_Text_Select';
					}
				}

				if( $setting_args['type'] == 'teaser' && ! empty( $setting_args['args']['featured'] ) ) {
					$control_args['featured'] = $setting_args['args']['featured'];
				}

				// Arguments for the range field
				if( $setting_args['type'] == 'range' ) {
					$control_args['input_attrs'] = array(
						'min' => !empty($setting_args['args']['min']) ? $setting_args['args']['min'] : 0,
						'max' => !empty($setting_args['args']['max']) ? $setting_args['args']['max'] : 100,
						'step' => !empty($setting_args['args']['step']) ? $setting_args['args']['step'] : 0.1,
					);
				}
				else if( $setting_args['type'] == 'widget' ) {
					$control_args['widget_args'] = array(
						'class' => !empty($setting_args['args']['widget_class']) ? $setting_args['args']['widget_class'] : false,
						'bundle_widget' => !empty($setting_args['args']['bundle_widget']) ? $setting_args['args']['bundle_widget'] : false,
					);
				}
				else if( $setting_args['type'] == 'media' ) {
					$control_args = wp_parse_args( $control_args, array(
						'section' => 'media',
						'mime_type' => 'image',
					) );
				}

				if( empty( $control_class ) ) {
					$control_class = !empty( self::$control_classes[ $setting_args['type'] ] ) ? self::$control_classes[ $setting_args['type'] ] : false;
				}

				if( !empty( $control_class ) ) {
					$wp_customize->add_control(
						new $control_class(
							$wp_customize,
							'theme_settings_' . $section_id . '_' . $setting_id,
							$control_args
						)
					);
				}
				else {
					$control_args['type'] = $setting_args['type'];
					$wp_customize->add_control(
						'theme_settings_' . $section_id . '_' . $setting_id,
						$control_args
					);
				}

			}
		}
	}

	/**
	 * Enqueue everything necessary for the live previewing in the Customizer
	 */
	function enqueue_preview(){
		if( !did_action('siteorigin_settings_init') ) {
			do_action('siteorigin_settings_init');
		}

		$values = array();
		foreach( $this->settings as $section_id => $section ) {
			foreach( $section as $setting_id => $setting ) {
				$values[$section_id . '_' . $setting_id] = $this->get($section_id . '_' . $setting_id);
			}
		}

		wp_enqueue_script( 'siteorigin-settings-tinycolor', get_template_directory_uri() . '/inc/settings/js/tinycolor' . SITEORIGIN_THEME_JS_PREFIX . '.js', array(), SITEORIGIN_THEME_VERSION );
		wp_enqueue_script( 'siteorigin-settings-live-preview', get_template_directory_uri() . '/inc/settings/js/live' . SITEORIGIN_THEME_JS_PREFIX . '.js', array('jquery'), SITEORIGIN_THEME_VERSION );
		wp_localize_script( 'siteorigin-settings-live-preview', 'soSettings', array(
			'css' => apply_filters( 'siteorigin_settings_custom_css', '', $this->get_all( ) ),
			'settings' => !empty($values) ? $values : false
		) );
	}

	/**
	 * Display all the generated custom CSS.
	 */
	function display_custom_css(){
		$settings = $this->get_all( );
		$css = apply_filters( 'siteorigin_settings_custom_css', '', $settings );

		if( !empty($css) ) {

			$css_key = md5( json_encode( array(
				'css' => $css,
				'settings' => $this->get_all(),
			) ) );

			if( $css_key !== get_theme_mod( 'custom_css_key' ) || ( defined('WP_DEBUG') && WP_DEBUG ) ) {
				$css_lines = array_map( "trim", preg_split("/[\r\n]+/", $css ) );
				foreach( $css_lines as $i => & $line ) {
					preg_match_all( '/\$\{([a-zA-Z0-9_]+)\}/', $line, $matches );
					if( empty($matches[0]) ) continue;

					$replaced = 0;

					for( $j = 0; $j < count($matches[0]); $j++ ) {
						$current = $this->get( $matches[1][$j] );
						$default = isset($this->defaults[$matches[1][$j]]) ? $this->defaults[$matches[1][$j]] : false;

						if( $current != $default && str_replace('%', '%%', $current) != $default ) {
							// Lets store that we've replaced something in this line
							$replaced++;
						}

						$line = str_replace( $matches[0][$j], $current, $line );
					}

					if( $replaced == 0 ) {
						// Remove any lines where we haven't done anything
						unset($css_lines[$i]);
					}
				}

				$css = implode(' ', $css_lines);

				// Now, lets handle the custom functions.
				$css = preg_replace_callback('/\.([a-z\-]+) *\(([^\)]*)\) *;/', array($this, 'css_functions'), $css);

				// Finally, we'll combine all imports and put them at the top of the file
				preg_match_all( '/@import url\(([^\)]+)\);/', $css, $matches );
				if( !empty($matches[0]) ) {
					$webfont_imports = array();

					for( $i = 0; $i < count($matches[0]); $i++ ) {
						if( strpos('//fonts.googleapis.com/css', $matches[1][$i]) !== -1 ) {
							if ( ! in_array( $matches[1][ $i ], $webfont_imports ) ) {
								$webfont_imports[] = $matches[1][$i];
							}
							$css = str_replace( $matches[0][$i], '', $css );
						}
					}

					if( !empty($webfont_imports) ) {
						$args = array(
							'family' => array(),
							'subset' => array(),
						);

						// Combine all webfont imports into a single argument
						foreach( $webfont_imports as $url ) {
							$url = parse_url($url);
							if( empty($url['query']) ) continue;
							parse_str( $url['query'], $query );

							if( !empty($query['family']) ) {
								$args['family'][] = $query['family'];
							}

							$args['subset'][] = !empty($query['subset']) ? $query['subset'] : 'latin';
						}

						// Clean up the arguments
						$args['subset'] = array_unique( $args['subset'] );
						$args['family'] = array_unique( $args['family'] );

						$args['family'] = array_map( 'urlencode', $args['family'] );
						$args['subset'] = array_map( 'urlencode', $args['subset'] );
						$args['family'] = implode('|', $args['family']);
						$args['subset'] = implode(',', $args['subset']);

						$import = '@import url(' . add_query_arg( $args, '//fonts.googleapis.com/css' ) . ');';
						$css = $import . "\n" . $css;
					}
				}

				// Now lets remove empty rules
				do {
					$css = preg_replace('/[^;\{\}]*?\{ *\}/', ' ', $css, -1, $count);
				} while( $count > 0 );
				$css = trim($css);

				set_theme_mod( 'custom_css', $css );
				set_theme_mod( 'custom_css_key', $css_key );
			}
			else {
				$css = get_theme_mod('custom_css');
			}

			if( !empty($css) ) {
				?>
				<style type="text/css" id="<?php echo esc_attr($this->theme_name) ?>-settings-custom" data-siteorigin-settings="true">
					<?php echo strip_tags($css) ?>
				</style>
				<?php
			}
		}
	}

	/**
	 * LESS style CSS functions
	 *
	 * @param $match
	 *
	 * @return string
	 */
	function css_functions($match) {
		$function = $match[1];

		if( $function == 'single' ) return '';

		$css_functions = SiteOrigin_Settings_CSS_Functions::single();
		if( method_exists( $css_functions, $function ) ) {
			return $css_functions->$function( $match );
		}

		return '';
	}

	/**
	 * Get the names of a specific template part
	 *
	 * @param $parts
	 * @param $part_name
	 *
	 * @return array
	 */
	static function template_part_names($parts, $part_name){
		$return = array();

		$parent_parts = glob( get_template_directory().'/'.$parts.'*.php' );
		$child_parts = glob( get_stylesheet_directory().'/'.$parts.'*.php' );

		$files = array_unique( array_merge(
			!empty($parent_parts) ? $parent_parts : array(),
			!empty($child_parts) ? $child_parts : array()
		) );

		if( !empty($files) ) {
			foreach( $files as $file ) {
				$p = pathinfo($file);
				$filename = explode('-', $p['filename'], 2);
				$name = isset($filename[1]) ? $filename[1] : '';

				$info = get_file_data($file, array(
					'name' => $part_name,
				) );

				$return[$name] = $info['name'];
			}
		}

		ksort($return);
		return $return;
	}

	/**
	 * Convert an attachment URL to a post ID
	 *
	 * @param $image_url
	 *
	 * @return mixed
	 */
	static function get_image_id( $image_url ){
		if( empty( $image_url ) ) return false;

		$attachment_id = wp_cache_get( $image_url, 'siteorigin_image_id' );

		if( $attachment_id === false ) {
			global $wpdb;
			$attachment = $wpdb->get_col(
				$wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )
			);
			$attachment_id = !empty($attachment[0]) ? $attachment[0] : 0;
			wp_cache_set( $image_url, $attachment_id, 'siteorigin_image_id', 86400 );
		}

		return $attachment_id;
	}

	/**
	 * Load all the extra components of the settings framework.
	 */
	function load_settings_extras(){
		if( has_filter( 'siteorigin_page_settings' ) ) {
			SiteOrigin_Settings_Page_Settings::single();
		}

		if( has_filter( 'siteorigin_settings_font_settings' ) ) {
			SiteOrigin_Settings_Webfont_Manager::single();
		}

		if( is_admin() && has_filter( 'siteorigin_about_page' ) && apply_filters( 'siteorigin_about_page_show', true ) ) {
			SiteOrigin_Settings_About_Page::single();
		}
	}

	/**
	 * Get the Premium upgrade URL
	 */
	static function get_premium_url( $featured_addon = false ){
		// Let themes and plugins add their affiliate ID
		$ref = apply_filters( 'siteorigin_premium_affiliate_id', false );

		// Get the args we want to add to the URL
		$args = array(
			'ref' => $ref,
			'featured_theme' => get_template(),
			'featured_addon' => $featured_addon
		);
		foreach( $args as $k => $v ) {
			if( empty( $v ) ) {
				unset( $args[$k] );
				continue;
			}
			$args[$k] = urlencode( $v );
		}

		$url = add_query_arg( $args, defined( 'SITEORIGIN_THEME_PREMIUM_URL' ) ? SITEORIGIN_THEME_PREMIUM_URL : 'https://siteorigin.com/downloads/premium/' );

		return $url;
	}
}

// Setup the single
SiteOrigin_Settings::single();

/**
 * Access a single setting
 *
 * @param $setting string The name of the setting.
 *
 * @return mixed The setting value
 */
function siteorigin_setting( $setting ){
	return SiteOrigin_Settings::single()->get( $setting );
}

/**
 * Set the value of a single setting. Included here for backwards compatibility.
 *
 * @param $setting
 * @param $value
 */
function siteorigin_settings_set( $setting, $value ){
	SiteOrigin_Settings::single()->set( $setting, $value );
}
