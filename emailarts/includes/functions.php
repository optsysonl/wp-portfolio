<?php

/**
 * Builds an HTML anchor element.
 *
 * @param string $url Link URL.
 * @param string $anchor_text Anchor label text.
 * @param string|array $args Optional. Link options.
 * @return string Formatted anchor element.
 */
function wpea_link( $url, $anchor_text, $args = '' ) {
    $args = wp_parse_args( $args, array(
        'id' => null,
        'class' => null,
    ) );

    $atts = array_merge( $args, array(
        'href' => esc_url( $url ),
    ) );

    return sprintf(
        '<a %1$s>%2$s</a>',
        wpea_format_atts( $atts ),
        esc_html( $anchor_text )
    );
}

function wpea_switch_locale( $locale, callable $callback, ...$args ) {
    static $available_locales = null;

//    if ( ! isset( $available_locales ) ) {
//        $available_locales = array_merge(
//            array( 'en_US' ),
//            get_available_languages()
//        );
//    }

//    $previous_locale = determine_locale();
//
//    $do_switch_locale = (
//        $locale !== $previous_locale &&
//        in_array( $locale, $available_locales, true ) &&
//        in_array( $previous_locale, $available_locales, true )
//    );
//
//    if ( $do_switch_locale ) {
//        wpea_unload_textdomain();
//        switch_to_locale( $locale );
//        wpea_load_textdomain( $locale );
//    }

    $result = call_user_func( $callback, ...$args );

//    if ( $do_switch_locale ) {
//        wpea_unload_textdomain( true );
//        restore_previous_locale();
//        wpea_load_textdomain( $previous_locale );
//    }

    return $result;
}


/**
 * @param $apiUrl
 * @param $publicKey
 * @param $privateKey
 *
 * @return MailWizzApi_Config
 */
function mwznb_build_sdk_config($publicKey, $privateKey) {
    $apiUrl = 'https://campaignmaker.com/api';
    return new MailWizzApi_Config(array(
        'apiUrl'        => $apiUrl,
        'publicKey'     => $publicKey,
        'privateKey'    => $privateKey,
    ));
}

/**
 * Restore the original config
 *
 * @param $oldConfig
 */
function mwznb_restore_sdk_config($oldConfig) {
    if (!empty($oldConfig) && $oldConfig instanceof MailWizzApi_Config) {
        MailWizzApi_Base::setConfig($oldConfig);
    }
}

/**
 * @param array $freshFields
 * @param $fieldName
 * @param array $listSelectedFields
 */
function mwznb_generate_fields_table(array $freshFields, $fieldName, array $listSelectedFields = array()) {
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
        <th width="40" align="left"><?php echo  __('Show', 'mwznb');?></th>
        <th width="60" align="left"><?php echo  __('Required', 'mwznb');?></th>
        <th align="left"><?php echo  __('Label', 'mwznb');?></th>
        <th align="left"><?php echo  __('Field Type', 'mwznb');?></th>
        </thead>
        <tbody>
        <?php foreach ($freshFields as $field) { ?>
            <?php //var_dump( empty($listSelectedFields), $listSelectedFields, $field['tag'], in_array($field['tag'], $listSelectedFields)); ?>
                <tr>
                <td width="40" align="left">
                    <input type="hidden" name="<?php echo $fieldName; ?>[<?php echo $field['tag']?>][required]" value="<?php echo $field['required'];?>">
                    <input type="hidden" name="<?php echo $fieldName; ?>[<?php echo $field['tag']?>][label]" value="<?php echo $field['label'];?>">
                    <input
                            name="<?php echo $fieldName; ?>[<?php echo $field['tag']?>][display]"
                            value="<?php echo $field['tag']?>"
                            type="checkbox"<?php echo empty($listSelectedFields) || in_array($field['tag'], $listSelectedFields) ? ' checked="checked"':''?>
                    />
                </td>
                <td width="60" align="left">
                    <?php echo $field['required'];?>
                </td>
                <td align="left">
                    <?php echo $field['label'];?>
                </td>
                <td align="left">
                    <select name="<?php echo $fieldName; ?>[<?php echo $field['tag']?>][type]">
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                    </select>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
}
/**
 * Template for available fields
 *
 * @param array $freshFields
 * @param $fieldName
 * @param array $listSelectedFields
 */
function mwznb_build_fields_table(array $freshFields, $fieldName) {
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
        <th width="40" align="left"><?php echo  __('Show', 'mwznb');?></th>
        <th width="60" align="left"><?php echo  __('Required', 'mwznb');?></th>
        <th align="left"><?php echo  __('Label', 'mwznb');?></th>
        <th align="left"><?php echo  __('Field Type', 'mwznb');?></th>
        </thead>
        <tbody>
        <?php foreach ($freshFields as $key=>$field) { ?>
                <tr>
                <td width="40" align="left">
                    <input type="hidden" name="<?php echo $fieldName; ?>[<?php echo $key; ?>][required]" value="<?php echo $field['required'];?>">
                    <input type="hidden" name="<?php echo $fieldName; ?>[<?php echo $key; ?>][label]" value="<?php echo $field['label'];?>">

                    <?php
                        $required = false;
                        $checked = (isset($field['display']) && $field['display'] == '1') ? true : false;
                        if($field['required'] == 'yes'){
                            $checked = true;
                            $required = true;
                        }

                    ?>

                    <input
                            name="<?php echo $fieldName; ?>[<?php echo $key; ?>][display]"
                            value="1"
                            type="checkbox"
                            <?php echo ($checked) ? ' checked="checked"' : ''; ?>
                            <?php echo ($required) ? 'disabled' : ''; ?>
                    />
                </td>
                <td width="60" align="left">
                    <?php echo $field['required'];?>
                </td>
                <td align="left">
                    <?php echo $field['label'];?>
                </td>
                <td align="left">
                    <select name="<?php echo $fieldName; ?>[<?php echo $key; ?>][type]">
                        <option value="text" <?php echo ($field['type'] == 'text') ? 'selected="selected"': ''; ?>>Text</option>
                        <option value="email" <?php echo ($field['type'] == 'email') ? 'selected="selected"': ''; ?>>Email</option>
                    </select>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
}

/**
 * AJAX get available fields
 * @param array $freshFields
 * @param $fieldName
 * @param array $listSelectedFields
 * @return string
 */

function mwznb_get_generate_fields_table_template(array $freshFields, $fieldName, array $listSelectedFields = array()){
    $template = '
        <table cellpadding="0" cellspacing="0">
        <thead>
        <th width="40" align="left">'.  __("Show", "mwznb") .'</th>
        <th width="60" align="left">'.  __("Required", "mwznb") .'</th>
        <th align="left">'.  __("Label", "mwznb") .'</th>
        <th align="left">'.  __("Field Type", "mwznb") .'</th>
        </thead>
        <tbody>';

        foreach ($freshFields as $field) {

            $required = '';
            $checked = (empty($listSelectedFields) || in_array($field['tag'], $listSelectedFields)) ? 'checked="checked"' : '';
            if ($field['required'] == 'yes') {
                $checked = 'checked="checked"';
                $required = 'disabled';
            }


            $template .= '
            <tr>
                <td width="40" align="left">
                    <input type="hidden" name="'. $fieldName .'['. $field["tag"] .'][required]" value="'. $field["required"] .'">
                    <input type="hidden" name="'. $fieldName .'['. $field["tag"] .'][label]" value="'. $field["label"] .'">
                    <input 
                        name="'. $fieldName .'['. $field["tag"] .'][display]" 
                        value="1" 
                        type="checkbox" 
                        '. $checked .'
                        '. $required .'
                         />
                </td>
                <td width="60" align="left">'. $field['required'] .'</td>
                <td align="left">'. $field['label'] .'</td>
                <td align="left">
                    
                    <select name="'. $fieldName .'['. $field["tag"] .'][type]">
                        <option value="text" >Text</option>
                        <option value="email" >Email</option>
                    </select>
                    
                </td>
            </tr>';
        }
    $template .= '</tbody>
    </table>
    ';

    return $template;
}

function wpea_validate_configuration() {
    return apply_filters( 'wpea_validate_configuration',
        WPEA_VALIDATE_CONFIGURATION
    );
}

/**
 * Converts multi-dimensional array to a flat array.
 *
 * @param mixed $input Array or item of array.
 * @return array Flatten array.
 */
function wpea_array_flatten( $input ) {
    if ( ! is_array( $input ) ) {
        return array( $input );
    }

    $output = array();

    foreach ( $input as $value ) {
        $output = array_merge( $output, wpea_array_flatten( $value ) );
    }

    return $output;
}

/**
 * Returns the current request URL.
 */
function wpea_get_request_uri() {
    static $request_uri = '';

    if ( empty( $request_uri ) ) {
        $request_uri = add_query_arg( array() );
    }

    return sanitize_url( $request_uri );
}

function wpea_is_localhost() {
    $sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
    return in_array( strtolower( $sitename ), array( 'localhost', '127.0.0.1' ) );
}

function wpea_verify_nonce( $nonce, $action = 'wp_rest' ) {
    return wp_verify_nonce( $nonce, $action );
}

function wpea_is_rtl( $locale = '' ) {
    static $rtl_locales = array(
        'ar' => 'Arabic',
        'ary' => 'Moroccan Arabic',
        'azb' => 'South Azerbaijani',
        'fa_IR' => 'Persian',
        'haz' => 'Hazaragi',
        'he_IL' => 'Hebrew',
        'ps' => 'Pashto',
        'ug_CN' => 'Uighur',
    );

    if ( empty( $locale )
        and function_exists( 'is_rtl' ) ) {
        return is_rtl();
    }

    if ( empty( $locale ) ) {
        $locale = determine_locale();
    }

    return isset( $rtl_locales[$locale] );
}

function wpea_enctype_value( $enctype ) {
    $enctype = trim( $enctype );

    if ( empty( $enctype ) ) {
        return '';
    }

    $valid_enctypes = array(
        'application/x-www-form-urlencoded',
        'multipart/form-data',
        'text/plain',
    );

    if ( in_array( $enctype, $valid_enctypes ) ) {
        return $enctype;
    }

    $pattern = '%^enctype="(' . implode( '|', $valid_enctypes ) . ')"$%';

    if ( preg_match( $pattern, $enctype, $matches ) ) {
        return $matches[1]; // for back-compat
    }

    return '';
}

function wpea_create_nonce( $action = 'wp_rest' ) {
    return wp_create_nonce( $action );
}

function wpea_deprecated_function( $function_name, $version, $replacement ) {
    if ( WP_DEBUG ) {
        if ( function_exists( '__' ) ) {
            trigger_error(
                sprintf(
                /* translators: 1: PHP function name, 2: version number, 3: alternative function name */
                    __( 'Function %1$s is <strong>deprecated</strong> since EmailArts version %2$s! Use %3$s instead.', 'emailarts' ),
                    $function_name, $version, $replacement
                ),
                E_USER_DEPRECATED
            );
        } else {
            trigger_error(
                sprintf(
                    'Function %1$s is <strong>deprecated</strong> since EmailArts version %2$s! Use %3$s instead.',
                    $function_name, $version, $replacement
                ),
                E_USER_DEPRECATED
            );
        }
    }
}

/**
 * Registers post types used for this plugin.
 */
function wpea_register_post_types() {
    if ( class_exists( 'EmailArts' ) ) {
        EmailArts::register_post_type();
        return true;
    } else {
        return false;
    }
}

function wpea_autop_or_not() {
    return (bool) apply_filters( 'wpea_autop_or_not', WPEA_AUTOP );
}

function wpea_form_controls_class( $type, $default_classes = '' ) {
    $type = trim( $type );

    if ( is_string( $default_classes ) ) {
        $default_classes = explode( ' ', $default_classes );
    }

    $classes = array(
        'wpea-form-control',
        sprintf( 'wpea-%s', rtrim( $type, '*' ) ),
    );

    if ( str_ends_with( $type, '*' ) ) {
        $classes[] = 'wpea-validates-as-required';
    }

    $classes = array_merge( $classes, $default_classes );
    $classes = array_filter( array_unique( $classes ) );

    return implode( ' ', $classes );
}

function wpea_get_validation_error( $name ) {
    if ( ! $form = wpea_get_current_form() ) {
        return '';
    }

    return $form->validation_error( $name );
}

function wpea_get_hangover( $name, $default_value = null ) {
//    if ( ! wpea_is_posted() ) {
//        return $default_value;
//    }

//    $submission = Submission::get_instance();
//
//    if ( ! $submission
//        or $submission->is( 'mail_sent' ) ) {
//        return $default_value;
//    }

    return isset( $_POST[$name] ) ? wp_unslash( $_POST[$name] ) : $default_value;
}

function wpea_exclude_blank( $input ) {
    $output = array_filter( $input,
        static function ( $i ) {
            return isset( $i ) && '' !== $i;
        }
    );

    return array_values( $output );
}

function wpea_flat_join( $input, $args = '' ) {
    $args = wp_parse_args( $args, array(
        'separator' => ', ',
    ) );

    $input = wpea_array_flatten( $input );
    $output = array();

    foreach ( (array) $input as $value ) {
        if ( is_scalar( $value ) ) {
            $output[] = trim( (string) $value );
        }
    }

    return implode( $args['separator'], $output );
}

function wpea_rmdir_p( $dir ) {
    if ( is_file( $dir ) ) {
        $file = $dir;

        if ( @unlink( $file ) ) {
            return true;
        }

        $stat = stat( $file );

        if ( @chmod( $file, $stat['mode'] | 0200 ) ) { // add write for owner
            if ( @unlink( $file ) ) {
                return true;
            }

            @chmod( $file, $stat['mode'] );
        }

        return false;
    }

    if ( ! is_dir( $dir ) ) {
        return false;
    }

    if ( $handle = opendir( $dir ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( $file == "."
                or $file == ".." ) {
                continue;
            }

            wpea_rmdir_p( path_join( $dir, $file ) );
        }

        closedir( $handle );
    }

    if ( false !== ( $files = scandir( $dir ) )
        and ! array_diff( $files, array( '.', '..' ) ) ) {
        return rmdir( $dir );
    }

    return false;
}

function wpea_plugin_path( $path = '' ) {
    return path_join( WPEA_PLUGIN_DIR, trim( $path, '/' ) );
}

function wpea_plugin_url( $path = '' ) {
    $url = plugins_url( $path, WPEA_PLUGIN );

    if ( is_ssl()
        and 'http:' == substr( $url, 0, 5 ) ) {
        $url = 'https:' . substr( $url, 5 );
    }

    return $url;
}

function wpea_load_js() {
    return apply_filters( 'wpea_load_js', WPEA_LOAD_JS );
}


/**
 * Returns true if CSS for this plugin is loaded.
 */
function wpea_load_css() {
    return apply_filters( 'wpea_load_css', WPEA_LOAD_CSS );
}

function wpea_support_html5_fallback() {
    return (bool) apply_filters( 'wpea_support_html5_fallback', false );
}