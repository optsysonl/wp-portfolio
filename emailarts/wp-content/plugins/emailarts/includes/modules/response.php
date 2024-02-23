<?php
/**
** A base module for [response]
**/

/* form_tag handler */

//add_action( 'wpea_init', 'wpea_add_form_tag_response', 10, 0 );

function wpea_add_form_tag_response() {
	wpea_add_form_tag( 'response',
		'wpea_response_form_tag_handler',
		array(
			'display-block' => true,
		)
	);
}

function wpea_response_form_tag_handler( $tag ) {
	if ( $contact_form = wpea_get_current_form() ) {
		return $contact_form->form_response_output();
	}
}
