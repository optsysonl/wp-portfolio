<?php

function wpea_form_tag_function($atts, $content = null, $codde = ''){
    if ( is_string( $atts ) ) {
        $atts = explode( ' ', $atts, 2 );
    }

    $id = trim( $atts['id'] );
    $title = trim( $atts['title'] );
    $form = wpea_get_form_by_hash( $id );


    if ( ! $form ) {
        return sprintf(
            '<p class="wpea-form-not-found"><strong>%1$s</strong> %2$s</p>',
            esc_html( __( 'Error:', 'emailarts' ) ),
            esc_html( __( "Form not found.", 'emailarts' ) )
        );
    }

    $callback = static function ( $form, $atts ) {
        return $form->form_html( $atts );
    };

    $output = wpea_switch_locale(
        $form->locale(),
        $callback,
        $form, $atts
    );

    return $output;

}

function wpea_get_form_by_hash( $hash ) {
    global $wpdb;

    $hash = trim( $hash );

    if ( strlen( $hash ) < 7 ) {
        return null;
    }

    $like = $wpdb->esc_like( $hash ) . '%';

    $q = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_hash'"
        . $wpdb->prepare( " AND meta_value LIKE %s", $like );

    if ( $post_id = $wpdb->get_var( $q ) ) {
        return wpea_form( $post_id );
    }
}