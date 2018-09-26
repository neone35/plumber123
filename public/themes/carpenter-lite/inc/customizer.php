<?php
/**
 * Carpenter Lite Theme Customizer
 *
 * @package Carpenter Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function carpenter_lite_customize_register( $wp_customize ) {
	
function carpenter_lite_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		
	$wp_customize->add_setting('color_scheme', array(
		'default' => '#584632',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','carpenter-lite'),
			'description'	=> __('Select color from here.','carpenter-lite'),
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);

	
	$wp_customize->add_setting('headerbg-color', array(
		'default' => '#584632',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'headerbg-color',array(
			'description'	=> __('Select background color for header.','carpenter-lite'),
			'section' => 'colors',
			'settings' => 'headerbg-color'
		))
	);
	
	$wp_customize->add_setting('nav-color', array(
		'default' => '#c49454',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'nav-color',array(
			'description'	=> __('Select background color for navigation bar.','carpenter-lite'),
			'section' => 'colors',
			'settings' => 'nav-color'
		))
	);
	
	$wp_customize->add_setting('footer-color', array(
		'default' => '#c49454',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'footer-color',array(
			'description'	=> __('Select background color for footer.','carpenter-lite'),
			'section' => 'colors',
			'settings' => 'footer-color'
		))
	);
	
	
	// Slider Section Start		
	$wp_customize->add_section(
        'slider_section',
        array(
            'title' => __('Slider Settings', 'carpenter-lite'),
            'priority' => null,
			'description'	=> __('Recommended image size (1420x567). Slider will work only when you select the static front page.','carpenter-lite'),	
        )
    );
	
	$wp_customize->add_setting('page-setting7',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide one:','carpenter-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting8',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide two:','carpenter-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting9',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide three:','carpenter-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('slide_text',array(
			'sanitize_callback' => 'sanitize_text_field',
	));	 

	$wp_customize->add_control( 'slide_text', array(
    	   'section'   => 'slider_section',
    	   'label'     => __('Add text for slider button.','carpenter-lite'),
    	   'type'      => 'text'
     ));
	
	$wp_customize->add_setting('hide_slider',array(
			'default' => true,
			'sanitize_callback' => 'carpenter_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_slider', array(
		   'settings' => 'hide_slider',
    	   'section'   => 'slider_section',
    	   'label'     => __('Check this to hide slider.','carpenter-lite'),
    	   'type'      => 'checkbox'
     ));	
	
	// Slider Section End
	 
	 // Contact Section

	$wp_customize->add_section(
        'contact_section',
        array(
            'title' => __('Contact Details', 'carpenter-lite'),
            'priority' => null,
			'description'	=> __('Add your contact info here.','carpenter-lite'),	
        )
    );	
	
	$wp_customize->add_setting('phone-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('phone-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add email address and phone number here.','carpenter-lite'),
			'section'	=> 'contact_section'
	));	
	
	$wp_customize->add_setting('email-txt',array(
			'sanitize_callback'	=> 'sanitize_email'
	));
	
	$wp_customize->add_control('email-txt',array(
			'type'	=> 'text',
			'section'	=> 'contact_section'
	));
	
	$wp_customize->add_setting('street-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('street-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add street and city here.','carpenter-lite'),
			'section'	=> 'contact_section'
	));
	
	$wp_customize->add_setting('city-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('city-txt',array(
			'type'	=> 'text',
			'section'	=> 'contact_section'
	));	
	
	$wp_customize->add_setting('top-link',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('top-link',array(
			'type'	=> 'text',
			'label'	=> __('Add link for get a quote button.','carpenter-lite'),
			'section'	=> 'contact_section'
	));
	
	// Homepage Section Start		
	$wp_customize->add_section(
        'homepage_section',
        array(
            'title' => __('Homepage Boxes', 'carpenter-lite'),
            'priority' => null,
			'description'	=> __('Select pages for homepage boxes. This section will be displayed only when you select the static front page.','carpenter-lite'),	
        )
    );	
	
	$wp_customize->add_setting('page-setting1',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting1',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for first box:','carpenter-lite'),
			'section'	=> 'homepage_section'
	));	
	
	$wp_customize->add_setting('page-setting2',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting2',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for second box:','carpenter-lite'),
			'section'	=> 'homepage_section'
	));	
	
	$wp_customize->add_setting('page-setting3',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting3',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for third box:','carpenter-lite'),
			'section'	=> 'homepage_section'
	));	
	
	$wp_customize->add_setting('hide_section',array(
			'default' => true,
			'sanitize_callback' => 'carpenter_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_section', array(
		   'settings' => 'hide_section',
    	   'section'   => 'homepage_section',
    	   'label'     => __('Check this to hide section.','carpenter-lite'),
    	   'type'      => 'checkbox'
     ));
	
}
add_action( 'customize_register', 'carpenter_lite_customize_register' );	

function carpenter_lite_css(){
		?>
        <style>
				a, 
				.tm_client strong,
				.postmeta a:hover,
				#sidebar ul li a:hover,
				.blog-post h3.entry-title{
					color:<?php echo esc_attr(get_theme_mod('color_scheme','#584632')); ?>;
				}
				a.blog-more:hover,
				.nav-links .current, 
				.nav-links a:hover,
				#commentform input#submit,
				input.search-submit,
				.nivo-controlNav a.active,
				.blog-date .date,
				.section-box .sec-left a,
				#slider .top-bar .slide-button:hover,
				a.read-more:hover,
				#slider .top-bar .slide-button,
				.ser-content h4 a{
					background-color:<?php echo esc_attr(get_theme_mod('color_scheme','#584632')); ?>;
				}
				#header{
					background-color:<?php echo esc_attr(get_theme_mod('headerbg-color','#584632')); ?>;
				}
				.copyright-wrapper{
					background-color:<?php echo esc_attr(get_theme_mod('footer-color','#c49454')); ?>;
				}
				#navigation,
				.sitenav ul li:hover > ul{
					background-color:<?php echo esc_attr(get_theme_mod('nav-color','#c49454')); ?>;
				}
		</style>
	<?php }
add_action('wp_head','carpenter_lite_css');

function carpenter_lite_customize_preview_js() {
	wp_enqueue_script( 'carpenter-lite-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20141216', true );
}
add_action( 'customize_preview_init', 'carpenter_lite_customize_preview_js' );