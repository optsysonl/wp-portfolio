<?php

//add_action( 'wpea_init', 'wpea_add_form_tag_hidden', 10, 0 );

function wpea_add_form_tag_hidden() {
	wpea_add_form_tag( 'hidden',
		'wpea_hidden_form_tag_handler',
		array(
			'name-attr' => true,
			'display-hidden' => true,
		)
	);
}

function wpea_hidden_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$atts = array();

	$class = wpea_form_controls_class( $tag->type );
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();

	$value = (string) reset( $tag->values );
	$value = $tag->get_default_option( $value );
	$atts['value'] = $value;

	$atts['type'] = 'hidden';
	$atts['name'] = $tag->name;
	$atts = wpea_format_atts( $atts );

	$html = sprintf( '<input %s />', $atts );
	return $html;
}
