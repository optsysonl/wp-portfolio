<?php

defined( 'ABSPATH' ) || exit;

class RC_AJAX {

    public static function init(){
        add_action('init', array(__CLASS__, 'define_ajax'), 0);
        self::add_ajax_events();
    }

    public static function define_ajax(){
        // phpcs:disable
        if ( ! empty( $_GET['rc-ajax'] ) ) {
            if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
                @ini_set( 'display_errors', 1 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
            }
            $GLOBALS['wpdb']->hide_errors();
        }
        // phpcs:enable
    }

    public static function add_ajax_events(){
        $base_ajax_events = array(
            'get_base_form',
            'get_social_form',
            'get_variants',
			'save_review'
        );

        foreach($base_ajax_events as $ajax_event) {
            add_action('wp_ajax_' . $ajax_event, array(__CLASS__, $ajax_event));
            add_action('wp_ajax_nopriv_' . $ajax_event, array(__CLASS__, $ajax_event));
        }
    }

	public static function save_review() {
		check_ajax_referer( 'rc-ajax-nonce', 'nonce' );

		$review = rc_insert_review(wp_unslash( $_POST ));
		if ( is_wp_error( $review ) ) {
			die(-1);
		}
		ob_start();
		include(RC_ABSPATH . 'templates/rc-review-submit.php');
		$form = ob_get_clean();

		wp_send_json($form);
    }

    public static function get_variants(){
        check_ajax_referer( 'rc-ajax-nonce', 'nonce' );

        ob_start();

		if (isset($_POST['rating'])) {
			if ($_POST['rating'] > 3) {
				include(RC_ABSPATH . 'templates/rc-review-social-form.php');
			} else {
				include(RC_ABSPATH . 'templates/rc-review-form-bad.php');
			}
		} else {
			die(-1);
		}
        $form = ob_get_clean();

        wp_send_json($form);
    }

    public static function get_base_form(){
        check_ajax_referer( 'rc-ajax-nonce', 'nonce' );

        ob_start();
        include(RC_ABSPATH . 'templates/rc-review-form-good.php');
        $form = ob_get_clean();

        wp_send_json($form);
    }

    public static function get_social_form(){
        check_ajax_referer( 'rc-ajax-nonce', 'nonce' );

        ob_start();
        include(RC_ABSPATH . 'templates/rc-review-social-form.php');

        $form = ob_get_clean();

        wp_send_json($form);
    }
}
RC_AJAX::init();