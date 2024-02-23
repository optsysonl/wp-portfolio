<?php

require_once WPEA_PLUGIN_DIR . '/admin/includes/admin-functions.php';
require_once WPEA_PLUGIN_DIR . '/admin/includes/tag-generator.php';

add_action(
    'admin_init',
    static function () {
        do_action( 'wpea_admin_init' );
    },
    10, 0
);

add_action(
    'admin_menu',
    'wpea_admin_menu',
    9, 0
);

function wpea_admin_menu() {
    do_action( 'wpea_admin_menu' );

    add_menu_page(
        __( 'EmailArts', 'emailarts' ),
        __( 'EmailArts', 'emailarts' )
        . wpea_admin_menu_change_notice(),
        'manage_options',
        'wpea',
        'wpea_admin_management_page',
        'dashicons-email',
        30
    );

    $edit = add_submenu_page( 'wpea',
        __( 'Edit Form', 'emailarts' ),
        __( 'EmailArts', 'emailarts' )
        . wpea_admin_menu_change_notice( 'wpea' ),
        'manage_options',
        'wpea',
        'wpea_admin_management_page'
    );

    add_action( 'load-' . $edit, 'wpea_load_admin_page', 10, 0 );

    $addnew = add_submenu_page( 'wpea',
        __( 'Add New Form', 'emailarts' ),
        __( 'Add New', 'emailarts' )
        . wpea_admin_menu_change_notice( 'wpea-new' ),
        'manage_options',
        'wpea-new',
        'wpea_admin_add_new_page'
    );

    add_action( 'load-' . $addnew, 'wpea_load_admin_page', 10, 0 );

    $settings = add_submenu_page( 'wpea',
        __( 'EmailArts Settings', 'emailarts' ),
        __( 'Settings', 'emailarts' )
        . wpea_admin_menu_change_notice( 'wpea-settings' ),
        'manage_options',
        'wpea-settings',
        'wpea_admin_settings'
    );

    add_action( 'load-' . $settings, 'wpea_load_admin_page', 10, 0 );
}

add_action('admin_enqueue_scripts', 'wpea_admin_enqueue_scripts', 10, 1);

function wpea_admin_enqueue_scripts(){
    wp_enqueue_style( 'wpea-editor-style', WPEA_PLUGIN_DIR_URI . 'admin/assets/editor.css', false, WPEA_VERSION );
    wp_enqueue_script('wpea-editor-script', WPEA_PLUGIN_DIR_URI . 'admin/assets/editor.js', array('jquery'), WPEA_VERSION);
}

/**
 * Plugin Settings page
 */
function wpea_admin_settings(){
    $settings = get_option('WPEmailArts_settings');
    if ($settings !==  null) {
        $settings = unserialize($settings);
    }
    $apiConnectionStatus = (isset($settings['api-connection-status'])) ? $settings['api-connection-status'] : false;

    ?>
    <div class="wrap" id="wpea-form-list-table">

        <h1 class="wp-heading-inline"><?php
            echo esc_html( __( 'EmailArts Settings', 'emailarts' ) );
            ?></h1>

        <hr class="wp-header-end">

        <form id="wpea-api-connection">

            <h2>API connection</h2>

            <div class="api-status connected" style="<?php echo ($apiConnectionStatus) ? '' : 'display:none;'; ?>">
                <button type="button" id="remove-api-connection" class="button-primary">Remove API connection</button>
            </div>

            <div class="api-status not-connected" style="<?php echo ($apiConnectionStatus) ? 'display:none;' : ''; ?>">
                <p>
                    <label>Public Key</label>
                    <input id="wpea-global-settings-public-key" style="width: 500px;" type="text" name="wp_EmailArts[publicKey]" value="" />
                </p>

                <p>
                    <label>Private Key</label>
                    <input id="wpea-global-settings-private-key" style="width: 500px;" type="text" name="wp_EmailArts[privateKey]" value="" />
                </p>

                <p class="submit">
                    <button class="button-primary woocommerce-save-button" type="submit">Connect API</button>
                    <?php wp_nonce_field( 'wpea_save_settings', 'nonce_wpea_save_settings' ); ?>
                    <input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=wpea-settings">
                </p>
            </div>

            <div class="notifications"></div>

        </form>

    </div>
    <?php
}

function wpea_admin_management_page() {
    if($post = wpea_get_current_form()){
//    if($_GET['post']){
//        $post_id = $_GET['post'];
        $post_id = $post->initial() ? -1 : $post->id();

        require_once WPEA_PLUGIN_DIR . '/admin/includes/editor.php';
        require_once WPEA_PLUGIN_DIR . '/admin/edit-form.php';
        return;
    }

    $list_table = new EmailArtsFormsList();
    $list_table->prepare_items();

    ?>
    <div class="wrap" id="wpea-form-list-table">

        <h1 class="wp-heading-inline"><?php
            echo esc_html( __( 'EmailArts Forms', 'emailarts' ) );
            ?></h1>

        <?php
//        if ( current_user_can( 'wpea_edit_forms' ) ) {
            echo wpea_link(
                menu_page_url( 'wpea-new', false ),
                __( 'Add New', 'emailarts' ),
                array( 'class' => 'page-title-action' )
            );
//        }

        if ( ! empty( $_REQUEST['s'] ) ) {
            echo sprintf(
                '<span class="subtitle">'
                /* translators: %s: search keywords */
                . __( 'Search results for &#8220;%s&#8221;', 'emailarts' )
                . '</span>',
                esc_html( $_REQUEST['s'] )
            );
        }
        ?>

        <hr class="wp-header-end">

        <?php
        do_action( 'ea_admin_warnings',
            'wpea', wpea_current_action(), null
        );

//        wpea_welcome_panel();

        do_action( 'ea_admin_notices',
            'wpea', wpea_current_action(), null
        );
        ?>

        <form method="get" action="">
            <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
            <?php $list_table->search_box( __( 'Search EmailArts Forms', 'emailarts' ), 'ea-contact' ); ?>
            <?php $list_table->display(); ?>
        </form>

    </div>
    <?php
}

function wpea_admin_menu_change_notice( $menu_slug = '' ) {
    $counts = apply_filters( 'wpea_admin_menu_change_notice',
        array(
            'wpea' => 0,
            'wpea-new' => 0
        )
    );

    if ( empty( $menu_slug ) ) {
        $count = absint( array_sum( $counts ) );
    } elseif ( isset( $counts[$menu_slug] ) ) {
        $count = absint( $counts[$menu_slug] );
    } else {
        $count = 0;
    }

    if ( $count ) {
        return sprintf(
            ' <span class="update-plugins %1$d"><span class="plugin-count">%2$s</span></span>',
            $count,
            esc_html( number_format_i18n( $count ) )
        );
    }

    return '';
}

add_filter(
    'set_screen_option_wpea_forms_per_page',
    static function ( $result, $option, $value ) {
        $wpea_screens = array(
            'wpea_forms_per_page',
        );

        if ( in_array( $option, $wpea_screens ) ) {
            $result = $value;
        }

        return $result;
    },
    10, 3
);

function wpea_load_admin_page(){
    global $plugin_page;
    $action = wpea_current_action();
    do_action( 'wpea_admin_load',
        isset( $_GET['page'] ) ? trim( $_GET['page'] ) : '',
        $action
    );

    switch($action){
        case 'save':
            $id = isset($_POST['post_ID']) ? $_POST['post_ID'] : '-1';
            check_admin_referer( 'wpea-save-form_' . $id );

            $args = $_REQUEST;
            $args['id'] = $id;

            $args['title'] = isset( $_POST['post_title'] )
                ? $_POST['post_title'] : null;

            $args['locale'] = isset( $_POST['wpea-locale'] )
                ? $_POST['wpea-locale'] : null;

            $args['form'] = isset( $_POST['wpea-form'] )
                ? $_POST['wpea-form'] : '';

            $args['list_id'] = isset($_POST['list_ID']) ? $_POST['list_ID'] : null;

//            $args['mail'] = isset( $_POST['wpea-mail'] )
//                ? $_POST['wpea-mail'] : array();
//
//            $args['mail_2'] = isset( $_POST['wpea-mail-2'] )
//                ? $_POST['wpea-mail-2'] : array();

//            $args['messages'] = isset( $_POST['wpea-messages'] )
//                ? $_POST['wpea-messages'] : array();

            $args['available_fields'] = isset( $_POST['wpea-available-fields'] )
                ? $_POST['wpea-available-fields'] : '';



            $form = wpea_save_form( $args );

            if ( $form and wpea_validate_configuration() ) {
                $config_validator = new ConfigValidator( $form );
                $config_validator->validate();
                $config_validator->save();
            }

            $query = array(
                'post' => $form ? $form->id() : 0,
                'active-tab' => isset( $_POST['active-tab'] )
                    ? (int) $_POST['active-tab'] : 0,
            );

            if ( ! $form ) {
                $query['message'] = 'failed';
            } elseif ( -1 == $id ) {
                $query['message'] = 'created';
            } else {
                $query['message'] = 'saved';
            }

            $redirect_to = add_query_arg( $query, menu_page_url( 'wpea', false ) );
            wp_safe_redirect( $redirect_to );
            exit();

            break;
        case 'copy':
            $id = empty($_POST['id']) ? absint($_REQUEST['post']) : absint($_POST['id']);
            $query = array();
            if($form = wpea_form($id)){
                $new_form = $form->copy();
                $new_form->save();

                $query['post'] = $new_form->id();
                $query['message'] = 'created';
            }
            $redirect_to = add_query_arg($query, menu_page_url('wpea'), false);
            wp_safe_redirect($redirect_to);
            break;
        case 'delete':
            $posts = empty( $_POST['post_ID'] )
                ? (array) $_REQUEST['post']
                : (array) $_POST['post_ID'];

            $deleted = 0;

            foreach ( $posts as $post ) {
                $post = EmailArts::get_instance( $post );

                if ( empty( $post ) ) {
                    continue;
                }

                if ( ! $post->delete() ) {
                    wp_die( __( "Error in deleting.", 'emailarts' ) );
                }

                $deleted += 1;
            }

            $query = array();

            if ( ! empty( $deleted ) ) {
                $query['message'] = 'deleted';
            }

            $redirect_to = add_query_arg( $query, menu_page_url( 'wpea', false ) );

            wp_safe_redirect( $redirect_to );

            break;
    }



    $post = null;

    if('wpea-new' == $plugin_page){
        $post = EmailArts::get_template(array(
            'locale' => isset( $_GET['locale'] ) ? $_GET['locale'] : null,
        ));
    }elseif( ! empty( $_GET['post'] ) ){
        $post = EmailArts::get_instance($_GET['post']);
    }


    $current_screen = get_current_screen();

//    $help_tabs = new wpea_Help_Tabs( $current_screen );

    if ( $post ) {
//        $help_tabs->set_help_tabs( 'edit' );
    } else {
//        $help_tabs->set_help_tabs( 'list' );

        if ( ! class_exists( 'EmailArtsFormsList' ) ) {
            require_once WPEA_PLUGIN_DIR . '/includes/classes/EmailArtsFormsList.php';
        }

        add_filter(
            'manage_' . $current_screen->id . '_columns',
            array( 'EmailArtsFormsList', 'define_columns' ),
            10, 0
        );

        add_screen_option( 'per_page', array(
            'default' => 20,
            'option' => 'wpea_forms_per_page',
        ) );
    }

}

function wpea_admin_add_new_page() {
    $post = wpea_get_current_form();

    if ( ! $post ) {
        $post = EmailArts::get_template();
    }

    $post_id = -1;



    require_once WPEA_PLUGIN_DIR . '/admin/includes/editor.php';
    require_once WPEA_PLUGIN_DIR . '/admin/edit-form.php';
}

/**
 * AJAX - Generating form from available fields
 */
add_action('wp_ajax_wpea_create_form', 'wpea_create_form_callback');
function wpea_create_form_callback(){
    $form_id = $_POST['form_id'];
    $form = wpea_form($form_id);

    $template = '';

    foreach($form->available_fields as $key=>$field){
        if($field['required'] == 'yes' || $field['display'] == '1') {
            $template .= sprintf('
            <label> %1$s           
                [%4$s%3$s %2$s placeholder "%1$s"]
            </label>
        ',
                $field['label'],
                $key,
                ($field['required'] == 'yes') ? '*' : '',
                $field['type']
            );
        }
    }
    $template .= '[submit "Submit"]';

    exit(MailWizzApi_Json::encode(array(
        'result' => 'success',
        'template' => $template
    )));
}

add_action('wp_ajax_wpea_save_api_connection_credentials', 'wpea_save_api_connection_credentials_callback');
function wpea_save_api_connection_credentials_callback(){
    $publicKey = $_POST['public_key'];
    $privateKey = $_POST['private_key'];

    if(
        !empty($publicKey) &&
        !empty($privateKey)
    ) {
        $oldSdkConfig = MailWizzApi_Base::getConfig();
        MailWizzApi_Base::setConfig(mwznb_build_sdk_config($publicKey, $privateKey));
        $endpoint = new MailWizzApi_Endpoint_Lists();
        $response = $endpoint->getLists(1, 50);
        $response = $response->body->toArray();
        if (isset($response['status']) && $response['status'] == 'success' && !empty($response['data']['records'])) {
            foreach ($response['data']['records'] as $list) {
                $freshLists[] = array(
                    'list_uid' => $list['general']['list_uid'],
                    'name' => $list['general']['name']
                );
            }
        }

        mwznb_restore_sdk_config($oldSdkConfig);
        unset($oldSdkConfig);

        if($response['status'] == 'success'){

            //save API credentials
            $settings = [
                'publicKey'             => $publicKey,
                'privateKey'            => $privateKey,
                'api-connection-status' => true
            ];
            update_option('WPEmailArts_settings', serialize($settings));


            exit(MailWizzApi_Json::encode(array(
                'result' => 'updated',
                'message' => 'API successfully connected.'
            )));
        }else{
            exit(MailWizzApi_Json::encode(array(
                'result' => 'error',
                'message' => 'Please enter valid API keys'
            )));
        }
    }

    exit(MailWizzApi_Json::encode(array(
        'result' => 'error',
        'message' => 'Please enter valid API keys'
    )));
}

/**
 * AJAX removing API connection
 *
 * @action wp_ajax_wpea_remove_api_connection_credentials
 * @callback wpea_remove_api_connection_credentials_callback
 */
add_action('wp_ajax_wpea_remove_api_connection_credentials', 'wpea_remove_api_connection_credentials_callback');
function wpea_remove_api_connection_credentials_callback(){
    $settings = [
        'publicKey' => '',
        'privateKey' => '',
        'api-connection-status' => false
    ];
    update_option('WPEmailArts_settings', serialize($settings));

    exit(MailWizzApi_Json::encode(array(
        'result' => 'updated',
        'message' => 'API credentials successfully cleared.'
    )));

}

add_action('wp_ajax_wpea_form_apply_list_changes', 'wpea_form_apply_list_changes_callback');
function wpea_form_apply_list_changes_callback(){
//    var_dump($_POST);

    $post_id = $_POST['post_ID'];
    $list_id = $_POST['list_ID'];

//    try to save changes to the form if it's already exists
    if(isset($post_id) && $post_id !== '-1'){
        //TODO save list id
    }

    $settings = get_option('WPEmailArts_settings');
    if ($settings !==  null) {
        $settings = unserialize($settings);
    }

    //get available fields

//    if (!isset($response['status']) || $response['status'] != 'success' || empty($response['data']['records']) || count($response['data']['records']) == 0) {
//            die();
//        }

//        $available_fields = $post->available_fields;
//
//        $selected_fields = (!empty($available_fields))? $available_fields : [];
        $selected_fields = [];
        $fieldName = 'wpea-available-fields';

        $oldSdkConfig = MailWizzApi_Base::getConfig();
        MailWizzApi_Base::setConfig(mwznb_build_sdk_config($settings['publicKey'], $settings['privateKey']));

        $endpoint = new MailWizzApi_Endpoint_ListFields();
        $response = $endpoint->getFields($list_id);
        $response = $response->body->toArray();
//
        mwznb_restore_sdk_config($oldSdkConfig);
        unset($oldSdkConfig);

//        var_dump($response['data']['records']);
//
//        if (!isset($response['status']) || $response['status'] != 'success' || empty($response['data']['records']) || count($response['data']['records']) == 0) {
//            die();
//        }
        $fields_template = mwznb_get_generate_fields_table_template((array)$response['data']['records'], $fieldName, $selected_fields);


    exit(MailWizzApi_Json::encode(array(
        'fields_data'   => $response['data']['records'],
        'template'      => $fields_template
    )));
}

/**
 * AJAX returns available fields from selected list
 *
 * @action wp_ajax_wpea_get_available_fields
 * @callback wpea_get_available_fields_callback
 */
add_action('wp_ajax_wpea_get_available_fields', 'wpea_get_available_fields_callback');
function wpea_get_available_fields_callback(){
//    if (!isset($response['status']) || $response['status'] != 'success' || empty($response['data']['records']) || count($response['data']['records']) == 0) {
//            die();
//        }

//        $available_fields = $post->available_fields;
//
//        $selected_fields = (!empty($available_fields))? $available_fields : [];
//        $fieldName = 'wpea-available-fields';
//
//        $oldSdkConfig = MailWizzApi_Base::getConfig();
//        MailWizzApi_Base::setConfig(mwznb_build_sdk_config($settings['publicKey'], $settings['privateKey']));
//
//        $endpoint = new MailWizzApi_Endpoint_ListFields();
//        $response = $endpoint->getFields($settings['list_uid']);
//        $response = $response->body->toArray();
//
//        mwznb_restore_sdk_config($oldSdkConfig);
//        unset($oldSdkConfig);
//
//        if (!isset($response['status']) || $response['status'] != 'success' || empty($response['data']['records']) || count($response['data']['records']) == 0) {
//            die();
//        }
//        mwznb_generate_fields_table((array)$response['data']['records'], $fieldName, $selected_fields);
}