<?php

function wpea_get_current_form() {
    if ( $current = EmailArts::get_current() ) {
        return $current;
    }
}

/**
 * Wrapper function of WPEA_ContactForm::get_instance().
 *
 * @param EmailArts|WP_Post|int $post Object or post ID.
 * @return EmailArts|null Contact form object. Null if unset.
 */
function wpea_form( $post ) {
    return EmailArts::get_instance( $post );
}

/**
 * Saves the form data.
 */
function wpea_save_form( $args = '', $context = 'save' ) {
    $args = wp_parse_args( $args, array(
        'id' => -1,
        'title' => null,
        'locale' => null,
        'form' => null,
        'mail' => null,
        'mail_2' => null,
        'messages' => null,
        'available_fields' => null,
    ) );

    $args = wp_unslash( $args );

    $args['id'] = (int) $args['id'];

    if ( -1 == $args['id'] ) {
        $form = EmailArts::get_template();
    } else {
        $form = wpea_form( $args['id'] );
    }

    if ( empty( $form ) ) {
        return false;
    }

    if ( null !== $args['title'] ) {
        $form->set_title( $args['title'] );
    }

    if ( null !== $args['locale'] ) {
        $form->set_locale( $args['locale'] );
    }

    $properties = array();

    if ( null !== $args['list_id'] ) {
        $properties['list_id'] = $args['list_id'];
    }

    if ( null !== $args['form'] ) {
        $properties['form'] = wpea_sanitize_form( $args['form'] );
    }

    if ( null !== $args['mail'] ) {
        $properties['mail'] = wpea_sanitize_mail( $args['mail'] );
        $properties['mail']['active'] = true;
    }

    if ( null !== $args['mail_2'] ) {
        $properties['mail_2'] = wpea_sanitize_mail( $args['mail_2'] );
    }

    if ( null !== $args['messages'] ) {
        $properties['messages'] = wpea_sanitize_messages( $args['messages'] );
    }

    if ( null !== $args['available_fields'] ) {
        $properties['available_fields'] = wpea_sanitaze_available_fields($args['available_fields']);
    }

    $form->set_properties( $properties );

    do_action( 'wpea_save_contact_form', $form, $args, $context );

    if ( 'save' == $context ) {
        $form->save();
    }

    return $form;
}

/**
 * Sanitizes the form property data.
 */
function wpea_sanitize_form( $input, $default_template = '' ) {
    if ( null === $input ) {
        return $default_template;
    }

    $output = trim( $input );

//    if ( ! current_user_can( 'unfiltered_html' ) ) {
        $output = wpea_kses( $output, 'form' );
//    }

    return $output;
}

/**
 * Sanitizes the mail property data.
 */
function wpea_sanitize_mail( $input, $defaults = array() ) {
    $input = wp_parse_args( $input, array(
        'active' => false,
        'subject' => '',
        'sender' => '',
        'recipient' => '',
        'body' => '',
        'additional_headers' => '',
        'attachments' => '',
        'use_html' => false,
        'exclude_blank' => false,
    ) );

    $input = wp_parse_args( $input, $defaults );

    $output = array();
    $output['active'] = (bool) $input['active'];
    $output['subject'] = trim( $input['subject'] );
    $output['sender'] = trim( $input['sender'] );
    $output['recipient'] = trim( $input['recipient'] );
    $output['body'] = trim( $input['body'] );

    if ( ! current_user_can( 'unfiltered_html' ) ) {
        $output['body'] = wpea_kses( $output['body'], 'mail' );
    }

    $output['additional_headers'] = '';

    $headers = str_replace( "\r\n", "\n", $input['additional_headers'] );
    $headers = explode( "\n", $headers );

    foreach ( $headers as $header ) {
        $header = trim( $header );

        if ( '' !== $header ) {
            $output['additional_headers'] .= $header . "\n";
        }
    }

    $output['additional_headers'] = trim( $output['additional_headers'] );
    $output['attachments'] = trim( $input['attachments'] );
    $output['use_html'] = (bool) $input['use_html'];
    $output['exclude_blank'] = (bool) $input['exclude_blank'];

    return $output;
}

/**
 * Sanitizes the messages property data.
 */
function wpea_sanitize_messages( $input, $defaults = array() ) {
    $output = array();

    foreach ( wpea_messages() as $key => $val ) {
        if ( isset( $input[$key] ) ) {
            $output[$key] = trim( $input[$key] );
        } elseif ( isset( $defaults[$key] ) ) {
            $output[$key] = $defaults[$key];
        }
    }

    return $output;
}

function wpea_sanitaze_available_fields($input, $defaults= array()){
    $output = array();
    //TODO sanitize array
    return $input;
}

/**
 * Generates a random hash string for a form.
 *
 * @param int $post_id Post ID.
 * @return string SHA-1 hash.
 */
function wpea_generate_form_hash( $post_id ) {
    return sha1( implode( '|', array(
        get_current_user_id(),
        $post_id,
        time(),
        home_url(),
    ) ) );
}