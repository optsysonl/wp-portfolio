<?php

defined( 'ABSPATH' ) || exit;

class RC_Admin {
    public function __construct(){
        add_action('init', array($this, 'includes'));
        add_action( 'admin_notices', array( $this, 'display_notices') );

        //TODO move this action into new class 'manage'
        add_action('admin_init', array($this, 'handle_settings_form_submit'), 99);
        add_action('admin_init', array($this, 'handle_review_save'), 99);
    }

    public function includes(){
        wp_enqueue_style('rc-admin-styles', RC_PLUGIN_DIR_URI . 'assets/css/admin-styles.css');

        include_once dirname(__FILE__) . '/class-rc-admin-menus.php';
    }

    public function handle_review_save(){
        global $rc_errors, $rc_success_msg;


        if(isset($_POST['rc_action_review'])) {

            $action = $_POST['rc_action_review'];

            if (!check_admin_referer($action.'_form_nonce_action', $action.'_form_nonce')) {
                $rc_errors[] = __( 'Something went wrong! please try again.', 'reviewscollector' );
                return;
            }
            $is_update = false;

            switch($action){
                case 'rc_save_review':
                    $data['review_author'] = $_POST['review_author'];
                    $data['review_author_email'] = $_POST['review_author_email'];
                    $data['review_date'] = $_POST['review_date'];
                    $data['review_content'] = $_POST['review_content'];
                    $data['review_rating'] = $_POST['review_rating'];
                    $data['review_approved'] = isset($_POST['review_approved']) ? $_POST['review_approved'] : 0;

                    $reviewsTable = new RC_Reviews_Data();

                    if(isset($_POST['id']) && !empty($_POST['id'])){
                        $data['id'] = $_POST['id'];
                        $is_update = $reviewsTable->update($data);
                    }else{
                        $is_update = $reviewsTable->add($data);
                    }
                    break;
            }

            if( $is_update ){
                $rc_success_msg[] = __( 'Settings has been saved successfully.', 'reviewscollector' );
            }else{
                $rc_errors[] = __( 'Something went wrong! please try again.', 'reviewscollector' );
            }

        }
    }

    public function handle_settings_form_submit(){
        global $rc_errors, $rc_success_msg;
        if(isset($_POST['rc_action'])) {

            $action = $_POST['rc_action'];
			if (!check_admin_referer($action.'_form_nonce_action', $action.'_form_nonce')) {
				$rc_errors[] = __( 'Something went wrong! please try again.', 'reviewscollector' );
				return;
			}



			$option_name = '';
            $options = $_POST['reviews_collector'];
			switch ($action) {
			    case 'rc_save_settings' :
					$option_name = 'rc_options';
                    break;
				case 'rc_save_popup_settings' :
					$option_name = 'rc_options_popup';
					break;
				case 'rc_save_sharing_settings' :
					$option_name = 'rc_options_sharing';
					break;
				case 'rc_save_reviews_settings' :
					$option_name = 'rc_options_reviews';
					break;

			}

			$is_update = false;
			if (!empty($options) && $option_name != '') {
				$is_update = update_option($option_name, $options);
            }

            if( $is_update ){
                $rc_success_msg[] = __( 'Settings has been saved successfully.', 'reviewscollector' );
            }else{
                $rc_errors[] = __( 'Something went wrong! please try again.', 'reviewscollector' );
            }

        }
    }

    /**
     * @name display_notices
     * @description Display messages.
     */
    public function display_notices(){
        global $rc_errors, $rc_success_msg, $rc_warnings, $rc_info_msg;

        if ( ! empty( $rc_errors ) ) {
            foreach ( $rc_errors as $error ) :
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo $error; ?></p>
                </div>
            <?php
            endforeach;
        }

        if ( ! empty( $rc_success_msg ) ) {
            foreach ( $rc_success_msg as $success ) :
                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo $success; ?></p>
                </div>
            <?php
            endforeach;
        }

        if ( ! empty( $rc_warnings ) ) {
            foreach ( $rc_warnings as $warning ) :
                ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php echo $warning; ?></p>
                </div>
            <?php
            endforeach;
        }

        if ( ! empty( $rc_info_msg ) ) {
            foreach ( $rc_info_msg as $info ) :
                ?>
                <div class="notice notice-info is-dismissible">
                    <p><?php echo $info; ?></p>
                </div>
            <?php
            endforeach;
        }
    }

}

return new RC_Admin();