<?php


class EmailArtsTemplate
{
    public static function get_default($prop = 'form'){
        if ( 'form' == $prop ) {
            $template = self::form();
        } elseif ( 'mail_2' == $prop ) {
            $template = self::mail();
        } elseif ( 'messages' == $prop ) {
            $template = self::messages();
        } else {
            $template = null;
        }

        return apply_filters( 'wpea_default_template', $template, $prop );
    }

    public static function form(){
//        $template = sprintf(
//            '
//<label> %2$s
//    [text* your-name autocomplete:name] </label>
//
//<label> %3$s
//    [email* your-email autocomplete:email] </label>
//
//<label> %4$s
//    [text* your-subject] </label>
//
//<label> %5$s %1$s
//    [textarea your-message] </label>
//
//[submit "%6$s"]',
//            __( '(optional)', 'emailarts' ),
//            __( 'Your name', 'emailarts' ),
//            __( 'Your email', 'emailarts' ),
//            __( 'Subject', 'emailarts' ),
//            __( 'Your message', 'emailarts' ),
//            __( 'Submit', 'emailarts' )
//        );
        $template = '';

        return trim( $template );
    }

    public static function mail() {
        $template = array(
            'subject' => sprintf(
            /* translators: 1: blog name, 2: [your-subject] */
                _x( '%1$s "%2$s"', 'mail subject', 'emailarts' ),
                '[_site_title]',
                '[your-subject]'
            ),
            'sender' => sprintf(
                '%s <%s>',
                '[_site_title]',
                self::from_email()
            ),
            'body' =>
                sprintf(
                /* translators: %s: [your-name] [your-email] */
                    __( 'From: %s', 'emailarts' ),
                    '[your-name] [your-email]'
                ) . "\n"
                . sprintf(
                /* translators: %s: [your-subject] */
                    __( 'Subject: %s', 'emailarts' ),
                    '[your-subject]'
                ) . "\n\n"
                . __( 'Message Body:', 'emailarts' )
                . "\n" . '[your-message]' . "\n\n"
                . '-- ' . "\n"
                . sprintf(
                /* translators: 1: blog name, 2: blog URL */
                    __( 'This is a notification that a form was submitted on your website (%1$s %2$s).', 'emailarts' ),
                    '[_site_title]',
                    '[_site_url]'
                ),
            'recipient' => '[_site_admin_email]',
            'additional_headers' => 'Reply-To: [your-email]',
            'attachments' => '',
            'use_html' => 0,
            'exclude_blank' => 0,
        );

        return $template;
    }

    public static function from_email() {
        $admin_email = get_option( 'admin_email' );

        if ( wpea_is_localhost() ) {
            return $admin_email;
        }

        $sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
        $sitename = strtolower( $sitename );

        if ( 'www.' === substr( $sitename, 0, 4 ) ) {
            $sitename = substr( $sitename, 4 );
        }

        if ( strpbrk( $admin_email, '@' ) === '@' . $sitename ) {
            return $admin_email;
        }

        return 'wordpress@' . $sitename;
    }

    public static function messages() {
        $messages = array();

        foreach ( wpea_messages() as $key => $arr ) {
            $messages[$key] = $arr['default'];
        }

        return $messages;
    }
}

function wpea_messages() {
    $messages = array(
        'mail_sent_ok' => array(
            'description'
            => __( "Sender's message was sent successfully", 'emailarts' ),
            'default'
            => __( "Thank you for your message. It has been sent.", 'emailarts' ),
        ),

        'mail_sent_ng' => array(
            'description'
            => __( "Sender's message failed to send", 'emailarts' ),
            'default'
            => __( "There was an error trying to send your message. Please try again later.", 'emailarts' ),
        ),

        'validation_error' => array(
            'description'
            => __( "Validation errors occurred", 'emailarts' ),
            'default'
            => __( "One or more fields have an error. Please check and try again.", 'emailarts' ),
        ),

        'spam' => array(
            'description'
            => __( "Submission was referred to as spam", 'emailarts' ),
            'default'
            => __( "There was an error trying to send your message. Please try again later.", 'emailarts' ),
        ),

        'accept_terms' => array(
            'description'
            => __( "There are terms that the sender must accept", 'emailarts' ),
            'default'
            => __( "You must accept the terms and conditions before sending your message.", 'emailarts' ),
        ),

        'invalid_required' => array(
            'description'
            => __( "There is a field that the sender must fill in", 'emailarts' ),
            'default'
            => __( "Please fill out this field.", 'emailarts' ),
        ),

        'invalid_too_long' => array(
            'description'
            => __( "There is a field with input that is longer than the maximum allowed length", 'emailarts' ),
            'default'
            => __( "This field has a too long input.", 'emailarts' ),
        ),

        'invalid_too_short' => array(
            'description'
            => __( "There is a field with input that is shorter than the minimum allowed length", 'emailarts' ),
            'default'
            => __( "This field has a too short input.", 'emailarts' ),
        ),
    );

    return apply_filters( 'wpea_messages', $messages );
}