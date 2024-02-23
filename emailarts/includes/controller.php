<?php
/**
 * Controller for front-end requests, scripts, and styles
 */


add_action(
	'parse_request',
	'wpea_control_init',
	20, 0
);

/**
 * Handles a submission in non-Ajax mode.
 */
function wpea_control_init() {

	if ( isset( $_POST['_wpea_form'] ) ) {
		$form = wpea_form( (int) $_POST['_wpea_form'] );

		if ( $form ) {
			exit(MailWizzApi_Json::encode($form->submit()));
		}
	}
}


/**
 * Registers main scripts and styles.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_register_script(
			'emailarts',
			wpea_plugin_url( 'assets/js/index.js' ),
            array( 'jquery','swv' ),
			WPEA_VERSION,
			true
		);

		if ( wpea_load_js() ) {
			wpea_enqueue_scripts();
		}

		wp_register_style(
			'emailarts',
			wpea_plugin_url( 'assets/css/styles.css' ),
			array(),
			WPEA_VERSION,
			'all'
		);

		if ( wpea_load_css() ) {
			wpea_enqueue_styles();
		}
	},
	10, 0
);


/**
 * Enqueues scripts.
 */
function wpea_enqueue_scripts() {
	wp_enqueue_script( 'emailarts' );

	$wpea = array(
		'api' => array(
			'root' => sanitize_url( get_rest_url() ),
			'namespace' => 'emailarts/v1',
		),
	);

	if ( defined( 'WP_CACHE' ) and WP_CACHE ) {
		$wpea['cached'] = 1;
	}

	wp_localize_script( 'emailarts', 'wpea', $wpea );

	do_action( 'wpea_enqueue_scripts' );
}


/**
 * Returns true if the main script is enqueued.
 */
function wpea_script_is() {
	return wp_script_is( 'emailarts' );
}


/**
 * Enqueues styles.
 */
function wpea_enqueue_styles() {
	wp_enqueue_style( 'emailarts' );

	if ( wpea_is_rtl() ) {
		wp_enqueue_style( 'emailarts-rtl' );
	}

	do_action( 'wpea_enqueue_styles' );
}


/**
 * Returns true if the main stylesheet is enqueued.
 */
function wpea_style_is() {
	return wp_style_is( 'emailarts' );
}


add_action(
	'wp_enqueue_scripts',
	'wpea_html5_fallback',
	20, 0
);

/**
 * Enqueues scripts and styles for the HTML5 fallback.
 */
function wpea_html5_fallback() {
	if ( ! wpea_support_html5_fallback() ) {
		return;
	}

	if ( wpea_script_is() ) {
		wp_enqueue_script( 'emailarts-form-html5-fallback' );
	}

	if ( wpea_style_is() ) {
		wp_enqueue_style( 'jquery-ui-smoothness' );
	}
}
