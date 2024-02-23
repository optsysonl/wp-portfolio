<?php
/**
 * Returns a formatted string of HTML attributes.
 *
 * @param array $atts Associative array of attribute name and value pairs.
 * @return string Formatted HTML attributes.
 */
function wpea_format_atts( $atts ) {
    $atts_filtered = array();

    foreach ( $atts as $name => $value ) {
        $name = strtolower( trim( $name ) );

        if ( ! preg_match( '/^[a-z_:][a-z_:.0-9-]*$/', $name ) ) {
            continue;
        }

        static $boolean_attributes = array(
            'checked',
            'disabled',
            'inert',
            'multiple',
            'readonly',
            'required',
            'selected',
        );

        if ( in_array( $name, $boolean_attributes ) and '' === $value ) {
            $value = false;
        }

        if ( is_numeric( $value ) ) {
            $value = (string) $value;
        }

        if ( null === $value or false === $value ) {
            unset( $atts_filtered[$name] );
        } elseif ( true === $value ) {
            $atts_filtered[$name] = $name; // boolean attribute
        } elseif ( is_string( $value ) ) {
            $atts_filtered[$name] = trim( $value );
        }
    }

    $output = '';

    foreach ( $atts_filtered as $name => $value ) {
        $output .= sprintf( ' %1$s="%2$s"', $name, esc_attr( $value ) );
    }

    return trim( $output );
}

/**
 * Sanitizes content for allowed HTML tags for the specified context.
 *
 * @param string $input Content to filter.
 * @param string $context Context used to decide allowed tags and attributes.
 * @return string Filtered text with allowed HTML tags and attributes intact.
 */
function wpea_kses( $input, $context = 'form' ) {
    $output = wp_kses(
        $input,
        wpea_kses_allowed_html( $context )
    );

    return $output;
}

/**
 * Returns an array of allowed HTML tags and attributes for a given context.
 *
 * @param string $context Context used to decide allowed tags and attributes.
 * @return array Array of allowed HTML tags and their allowed attributes.
 */
function wpea_kses_allowed_html( $context = 'form' ) {
    static $allowed_tags = array();

    if ( isset( $allowed_tags[$context] ) ) {
        return apply_filters(
            'wpea_kses_allowed_html',
            $allowed_tags[$context],
            $context
        );
    }

    $allowed_tags[$context] = wp_kses_allowed_html( 'post' );

    if ( 'form' === $context ) {
        $additional_tags_for_form = array(
            'button' => array(
                'disabled' => true,
                'name' => true,
                'type' => true,
                'value' => true,
            ),
            'datalist' => array(),
            'fieldset' => array(
                'disabled' => true,
                'name' => true,
            ),
            'input' => array(
                'accept' => true,
                'alt' => true,
                'capture' => true,
                'checked' => true,
                'disabled' => true,
                'list' => true,
                'max' => true,
                'maxlength' => true,
                'min' => true,
                'minlength' => true,
                'multiple' => true,
                'name' => true,
                'placeholder' => true,
                'readonly' => true,
                'size' => true,
                'step' => true,
                'type' => true,
                'value' => true,
            ),
            'label' => array(
                'for' => true,
            ),
            'legend' => array(),
            'meter' => array(
                'value' => true,
                'min' => true,
                'max' => true,
                'low' => true,
                'high' => true,
                'optimum' => true,
            ),
            'optgroup' => array(
                'disabled' => true,
                'label' => true,
            ),
            'option' => array(
                'disabled' => true,
                'label' => true,
                'selected' => true,
                'value' => true,
            ),
            'output' => array(
                'for' => true,
                'name' => true,
            ),
            'progress' => array(
                'max' => true,
                'value' => true,
            ),
            'select' => array(
                'disabled' => true,
                'multiple' => true,
                'name' => true,
                'size' => true,
            ),
            'textarea' => array(
                'cols' => true,
                'disabled' => true,
                'maxlength' => true,
                'minlength' => true,
                'name' => true,
                'placeholder' => true,
                'readonly' => true,
                'rows' => true,
                'spellcheck' => true,
                'wrap' => true,
            ),
        );

        $additional_tags_for_form = array_map(
            static function ( $elm ) {
                $global_attributes = array(
                    'aria-atomic' => true,
                    'aria-checked' => true,
                    'aria-describedby' => true,
                    'aria-details' => true,
                    'aria-disabled' => true,
                    'aria-hidden' => true,
                    'aria-invalid' => true,
                    'aria-label' => true,
                    'aria-labelledby' => true,
                    'aria-live' => true,
                    'aria-relevant' => true,
                    'aria-required' => true,
                    'aria-selected' => true,
                    'class' => true,
                    'data-*' => true,
                    'id' => true,
                    'inputmode' => true,
                    'role' => true,
                    'style' => true,
                    'tabindex' => true,
                    'title' => true,
                );

                return array_merge( $global_attributes, (array) $elm );
            },
            $additional_tags_for_form
        );

        $allowed_tags[$context] = array_merge(
            $allowed_tags[$context],
            $additional_tags_for_form
        );
    }

    return apply_filters(
        'wpea_kses_allowed_html',
        $allowed_tags[$context],
        $context
    );
}

/**
 * Navigates through an array, object, or scalar, and
 * normalizes newline characters in the each value.
 *
 * @param mixed $input The array or string to be processed.
 * @param string $to Optional. The newline character that is used in the output.
 * @return mixed Processed value.
 */
function wpea_normalize_newline_deep( $input, $to = "\n" ) {
    if ( is_array( $input ) ) {
        $result = array();

        foreach ( $input as $key => $text ) {
            $result[$key] = wpea_normalize_newline_deep( $text, $to );
        }

        return $result;
    }

    return wpea_normalize_newline( $input, $to );
}

/**
 * Normalizes newline characters.
 *
 * @param string $text Input text.
 * @param string $to Optional. The newline character that is used in the output.
 * @return string Normalized text.
 */
function wpea_normalize_newline( $text, $to = "\n" ) {
    if ( ! is_string( $text ) ) {
        return $text;
    }

    $nls = array( "\r\n", "\r", "\n" );

    if ( ! in_array( $to, $nls ) ) {
        return $text;
    }

    return str_replace( $nls, $to, $text );
}

function wpea_sanitize_unit_tag( $tag ) {
    $tag = preg_replace( '/[^A-Za-z0-9_-]/', '', $tag );
    return $tag;
}

function wpea_autop( $input, $br = true ) {
    $placeholders = array();

    // Replace non-HTML embedded elements with placeholders.
    $input = preg_replace_callback(
        '/<(math|svg).*?<\/\1>/is',
        static function ( $matches ) use ( &$placeholders ) {
            $placeholder = sprintf(
                '<%1$s id="%2$s" />',
                EmailArtsHTMLFormatter::placeholder_inline,
                sha1( $matches[0] )
            );

            list( $placeholder ) =
                EmailArtsHTMLFormatter::normalize_start_tag( $placeholder );

            $placeholders[$placeholder] = $matches[0];

            return $placeholder;
        },
        $input
    );
//    var_dump($input);
    $formatter = new EmailArtsHTMLFormatter( array(
        'auto_br' => $br,
    ) );

    $chunks = $formatter->separate_into_chunks( $input );

    $output = $formatter->format( $chunks );

    // Restore from placeholders.
    $output = str_replace(
        array_keys( $placeholders ),
        array_values( $placeholders ),
        $output
    );

    return $output;
}

function wpea_strip_quote_deep( $input ) {
    if ( is_string( $input ) ) {
        return wpea_strip_quote( $input );
    }

    if ( is_array( $input ) ) {
        $result = array();

        foreach ( $input as $key => $text ) {
            $result[$key] = wpea_strip_quote_deep( $text );
        }

        return $result;
    }
}

function wpea_strip_quote( $text ) {
    $text = trim( $text );

    if ( preg_match( '/^"(.*)"$/s', $text, $matches ) ) {
        $text = $matches[1];
    } elseif ( preg_match( "/^'(.*)'$/s", $text, $matches ) ) {
        $text = $matches[1];
    }

    return $text;
}

function wpea_canonicalize( $text, $args = '' ) {
    // for back-compat
    if ( is_string( $args ) and '' !== $args
        and false === strpos( $args, '=' ) ) {
        $args = array(
            'strto' => $args,
        );
    }

    $args = wp_parse_args( $args, array(
        'strto' => 'lower',
        'strip_separators' => false,
    ) );

    static $charset = null;

    if ( ! isset( $charset ) ) {
        $charset = get_option( 'blog_charset' );

        $is_utf8 = in_array(
            $charset,
            array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' )
        );

        if ( $is_utf8 ) {
            $charset = 'UTF-8';
        }
    }

    $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, $charset );

    if ( function_exists( 'mb_convert_kana' ) ) {
        $text = mb_convert_kana( $text, 'asKV', $charset );
    }

    if ( $args['strip_separators'] ) {
        $text = preg_replace( '/[\r\n\t ]+/', '', $text );
    } else {
        $text = preg_replace( '/[\r\n\t ]+/', ' ', $text );
    }

    if ( 'lower' == $args['strto'] ) {
        if ( function_exists( 'mb_strtolower' ) ) {
            $text = mb_strtolower( $text, $charset );
        } else {
            $text = strtolower( $text );
        }
    } elseif ( 'upper' == $args['strto'] ) {
        if ( function_exists( 'mb_strtoupper' ) ) {
            $text = mb_strtoupper( $text, $charset );
        } else {
            $text = strtoupper( $text );
        }
    }

    $text = trim( $text );
    return $text;
}