<?php

su_add_shortcode( array(
		'id' => 'list',
		'callback' => 'su_shortcode_list',
		'image' => su_get_plugin_url() . 'admin/images/shortcodes/list.svg',
		'name' => __( 'List', 'shortcodes-ultimate' ),
		'type' => 'wrap',
		'group' => 'content',
		'atts' => array(
			'icon' => array(
				'type' => 'icon',
				'default' => '',
				'name' => __( 'Icon', 'shortcodes-ultimate' ),
				'desc' => __( 'You can upload custom icon for this list or pick a built-in icon', 'shortcodes-ultimate' )
			),
			'icon_color' => array(
				'type' => 'color',
				'default' => '#333333',
				'name' => __( 'Icon color', 'shortcodes-ultimate' ),
				'desc' => __( 'This color will be applied to the selected icon. Does not works with uploaded icons', 'shortcodes-ultimate' )
			),
			'class' => array(
				'type' => 'extra_css_class',
				'name' => __( 'Extra CSS class', 'shortcodes-ultimate' ),
				'desc' => __( 'Additional CSS class name(s) separated by space(s)', 'shortcodes-ultimate' ),
				'default' => '',
			),
		),
		'content' => __( "<ul>\n<li>List item</li>\n<li>List item</li>\n<li>List item</li>\n</ul>", 'shortcodes-ultimate' ),
		'desc' => __( 'Styled unordered list', 'shortcodes-ultimate' ),
		'icon' => 'list-ol',
	) );

function su_shortcode_list( $atts = null, $content = null ) {

	$atts = shortcode_atts( array(
			'icon' => 'icon: star',
			'icon_color' => '#333',
			'style' => null,
			'class' => ''
		), $atts, 'list' );

	// Backward compatibility // 4.2.3+
	if ( $atts['style'] !== null ) {
		switch ( $atts['style'] ) {
		case 'star':
			$atts['icon'] = 'icon: star';
			$atts['icon_color'] = '#ffd647';
			break;
		case 'arrow':
			$atts['icon'] = 'icon: arrow-right';
			$atts['icon_color'] = '#00d1ce';
			break;
		case 'check':
			$atts['icon'] = 'icon: check';
			$atts['icon_color'] = '#17bf20';
			break;
		case 'cross':
			$atts['icon'] = 'icon: remove';
			$atts['icon_color'] = '#ff142b';
			break;
		case 'thumbs':
			$atts['icon'] = 'icon: thumbs-o-up';
			$atts['icon_color'] = '#8a8a8a';
			break;
		case 'link':
			$atts['icon'] = 'icon: external-link';
			$atts['icon_color'] = '#5c5c5c';
			break;
		case 'gear':
			$atts['icon'] = 'icon: cog';
			$atts['icon_color'] = '#ccc';
			break;
		case 'time':
			$atts['icon'] = 'icon: time';
			$atts['icon_color'] = '#a8a8a8';
			break;
		case 'note':
			$atts['icon'] = 'icon: edit';
			$atts['icon_color'] = '#f7d02c';
			break;
		case 'plus':
			$atts['icon'] = 'icon: plus-sign';
			$atts['icon_color'] = '#61dc3c';
			break;
		case 'guard':
			$atts['icon'] = 'icon: shield';
			$atts['icon_color'] = '#1bbe08';
			break;
		case 'event':
			$atts['icon'] = 'icon: bullhorn';
			$atts['icon_color'] = '#ff4c42';
			break;
		case 'idea':
			$atts['icon'] = 'icon: sun';
			$atts['icon_color'] = '#ffd880';
			break;
		case 'settings':
			$atts['icon'] = 'icon: cogs';
			$atts['icon_color'] = '#8a8a8a';
			break;
		case 'twitter':
			$atts['icon'] = 'icon: twitter-sign';
			$atts['icon_color'] = '#00ced6';
			break;
		}
	}

	if ( strpos( $atts['icon'], 'icon:' ) !== false ) {
		$atts['icon'] = '<i class="fa fa-' . trim( str_replace( 'icon:', '', $atts['icon'] ) ) . '" style="color:' . $atts['icon_color'] . '"></i>';
		su_query_asset( 'css', 'font-awesome' );
	}

	else {
		$atts['icon'] = '<img src="' . $atts['icon'] . '" alt="" />';
	}

	su_query_asset( 'css', 'su-shortcodes' );

	return '<div class="su-list su-list-style-' . $atts['style'] . su_get_css_class( $atts ) . '">' . str_replace( '<li>', '<li>' . $atts['icon'] . ' ', su_do_nested_shortcodes( $content, 'list' ) ) . '</div>';

}
