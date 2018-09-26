<?php

class SiteOrigin_Settings_Control_Widget extends WP_Customize_Control {

	public $type = 'siteorigin-widget-setting';

	public $widget_args;

	function render_content( ){
		if( empty( $this->widget_args['class'] ) ) return;

		if ( ! empty( $this->label ) ) {
			?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
		}
		if ( ! empty( $this->description ) ) {
			?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php
		}

		if( !class_exists( $this->widget_args['class'] ) && !empty( $this->widget_args['bundle_widget'] ) && class_exists('SiteOrigin_Widgets_Bundle') ) {
			// If this is a widget bundle widget, and the class isn't available, then try activate it.
			SiteOrigin_Widgets_Bundle::single()->activate_widget( $this->widget_args['bundle_widget'] );
		}

		if( !class_exists( $this->widget_args['class'] ) ) {
			// Display the message prompting the user to install the widget plugin from WordPress.org
			?><div class="so-settings-widget-form"><?php
			_e('This field requires the Widgets Bundle plugin.', 'siteorigin-corp');
			echo ' ';
			printf( __( '<a href="%s">Install</a> the Widgets Bundle now.', 'siteorigin-corp' ), 'https://wordpress.org/plugins/so-widgets-bundle/' );
			?></div>
			<input type="hidden" class="widget-value" value="<?php esc_attr( $this->value()  ) ?>" />
			<?php
		}
		else {
			$widget_values = $this->value();
			if( is_string( $widget_values ) ) {
				if( is_serialized( $widget_values ) ) {
					$widget_values = unserialize( $widget_values );
				}
				else {
					$widget_values = json_decode( $widget_values, true );
				}
			}

			// Render the widget form
			$the_widget = new $this->widget_args['class']();
			$the_widget->id = 1;
			$the_widget->number = 1;
			ob_start();
			$the_widget->form( $widget_values );
			$form = '<p><a href="" class="button-secondary so-widget-close">' . __( 'Close', 'siteorigin-corp' ) . '</a></p>' . ob_get_clean();
			// Convert the widget field naming into ones that Settings will use
			$exp = preg_quote( $the_widget->get_field_name('____') );
			$exp = str_replace('____', '(.*?)', $exp);
			$form = preg_replace( '/'.$exp.'/', 'siteorigin_settings_widget['.preg_quote(1).'][$1]', $form );
			$form .= '<p><a href="" class="button-secondary so-widget-close">' . __( 'Close', 'siteorigin-corp' ) . '</a></p>';

			?>
			<div class="so-settings-widget-form">
				<div class="so-widget-form" data-widget-class="<?php echo esc_attr( $this->widget_args['class'] ) ?>">
					<?php echo $form ?>
				</div>
				<a href="#" class="button-primary so-edit-widget"><?php esc_html_e( 'Edit Widget', 'siteorigin-corp' ) ?></a>
			</div>
			<?php
		}
	}

	public function enqueue() {
		wp_enqueue_script( 'siteorigin-settings-widget-control', get_template_directory_uri() . '/inc/settings/js/control/widget-setting-control' . SITEORIGIN_THEME_JS_PREFIX . '.js', array( 'jquery', 'customize-controls' ) );
		wp_enqueue_style( 'siteorigin-settings-widget-control', get_template_directory_uri() . '/inc/settings/css/control/widget-setting-control.css', array() );
	}
}
