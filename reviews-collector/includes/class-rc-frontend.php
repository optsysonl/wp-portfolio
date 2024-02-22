<?php

defined( 'ABSPATH' ) || exit;

class RC_Frontend {

    public static function init(){
        wp_register_script('rc-script', RC_PLUGIN_DIR_URI .'assets/js/frontend-script.js', array('jquery'), '1.0', true);
        wp_register_style('rc-style', RC_PLUGIN_DIR_URI .'assets/css/frontend-style.css');

        if(!is_admin()) {
            add_action('wp_footer', __CLASS__ . '::rc_popup');
            add_shortcode('reviews-collector', __CLASS__ . '::rc_short_code');
        }
    }

    /**
     * @name add_frontend_attachments
     * @description enqueue frontend styles & scripts
     */
    public static function add_frontend_attachments(){
        wp_enqueue_script('rc-script');
        wp_enqueue_style('rc-style');
        wp_enqueue_style('rc-font', RC_PLUGIN_DIR_URI . 'assets/css/all.min.css');
        add_action('wp_enqueue_scripts', __CLASS__ . '::rc_popup_custom_styles');

        wp_localize_script('rc-script', 'rc_data',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('rc-ajax-nonce')
            )
        );
    }

    /**
     * @name rc_popup
     * @description Build rc popup structure
     */
    public static function rc_popup(){

        self::add_frontend_attachments();

        $options = get_option('rc_options');
        $display_button = isset($options['review_button_display']);

        $html = '';
        if($display_button) {
            $html .= self::get_review_button();
        }

        $html .= '<div class="rc-popup-overlay"></div>';
        $html .= '<div class="rc-popup-container" data-step="0">';
            $html .= '<div class="rc-close"><div></div><div></div></div>';

            $html .= '<div class="rc-inner">';
                $html .= self::get_rating_container();
            $html .= '</div>';

        $html .= '</div>';

        echo $html;
    }


    /**
     * @name rc_short_code
     * @description Display rc form as short code
     */
    public static function rc_short_code($atts){

        self::add_frontend_attachments();

        $attr = shortcode_atts( array(
            'id' => 0
        ), $atts );

        $shortcode_id = $attr['id'];

        if($shortcode_id == 0){
            return;
        }

//        $options = get_option('rc_options');
        $rcs_options = get_post_meta($shortcode_id, 'rcs_options', true);
        $html = '';

        switch($rcs_options['shortcode_type']){
            case 'type_1':
                $rc_content_classes = 'rc-content';
                $html .= '<div class="rc-shortcode-content" data-step="0">';
                    $html .= '<div class="'.$rc_content_classes.'">';
                        $html .= self::get_rating_container();
                    $html .= '</div>';
                $html .= '</div>';
                break;
            case 'type_2':

                $display_button = isset($rcs_options['rl_display_review_button']);
                $display_content = isset($rcs_options['rl_review_button_content_location']);
                $columns = (isset($rcs_options['rl_columns']) && $rcs_options['rl_columns'] > 0) ? $rcs_options['rl_columns'] : 1;

                $rc_content_classes = 'rc-content with-leave-a-review-button';
                $rc_content_classes .= ($display_content) ? ' rc-with-content-open-popup' : '';

                $html .= '<div class="rc-shortcode-list-content columns-'.$columns.'">';
                    $html .= '<div class="rc-list">';
                        $html .= self::get_rc_list($shortcode_id, $rcs_options);
                    $html .= '</div>';
                $html .= '</div>';
                if($display_button) {
                    $html .= '<div class="rc-shortcode-content" data-step="0">';
                        $html .= '<div class="' . $rc_content_classes . '">';
                        if($display_button) {
                            $html .= '<div class="rc-first-button-container">';
                                $html .= self::get_review_button();
                            $html .= '</div>';
                        }
                        if($display_content) {
                            $html .= self::get_rating_container();
                        }
                        $html .= '</div>';
                    $html .= '</div>';
                }

                break;
            default:
                break;
        }

        echo $html;
    }

    /**
     * @name get_review_button
     * @description Leave a review button html.
     * @return string
     */
    public static function get_review_button(){
        $html = '<div class="rc-popup-button"><span>Leave a review</span></div>';
        return $html;
    }


    /**
     * @name get_content
     * @description Build rc form structure
     * @return string
     */
    public static function get_rating_container(){
        ob_start();
        include(RC_ABSPATH .'templates/rc-ratings-container.php');
        $html = ob_get_clean();

        return $html;
    }

	/**
	 * @name get_rc_list
	 * @description Build rc data structure
     * @param {integer} $item_id
     * @param {array} $rcs_options
	 * @return string
	 */
	public static function get_rc_list($item_id = 0, $rcs_options = array()){
        $atts = array(
            'per_page'     	=> ( isset($rcs_options['display_reviews_count']) && (int)$rcs_options['display_reviews_count'] > 0 ) ? $rcs_options['display_reviews_count'] : 2,
        );
        $rc_option = get_option('rc_options');
        $rc_options_popup = get_option('rc_options_popup');
        $rc_options_sharing = get_option('rc_options_sharing');
        $rc_options_reviews = get_option('rc_options_reviews');

		ob_start();
		switch ($rcs_options['rl_display_type']) {
			case 'type_1' :
				include(RC_ABSPATH .'templates/rc-review-list.php');
				break;
			case 'type_2' :
				include(RC_ABSPATH .'templates/rc-review-carousel.php');
				break;
		}
		$html = ob_get_clean();

		return $html;
	}

	public static function rc_popup_custom_styles(){
        $options = get_option('rc_options');
        $rc_options_popup = get_option('rc_options_popup');

        $overlay = self::hex2rgba($rc_options_popup['popup_overlay_color'], 0.5);
        $button_bg_color = $options['button_background_color'];
        $button_text_color = $options['button_text_color'];
        $color1 = $rc_options_popup['popup_main_color'];
        $color2 = $rc_options_popup['popup_second_color'];
        $button_position = '';

        switch($options['review_button_position']){
            case 'top_right':
                $button_position .= 'top: 20px; bottom: auto; left: auto; right: 20px;';
                break;
            case 'top_left':
                $button_position .= 'top: 20px; bottom: auto; left: 20px; right: auto;';
                break;
            case 'bottom_right':
                $button_position .= 'top: auto; bottom: 20px; left: auto; right: 20px;';
                break;
            case 'bottom_left':
                $button_position .= 'top: auto; bottom: 20px; left: 20px; right: auto;';
                break;
        }

        $custom_css = "
            .rc-popup-button {
                background-color: {$button_bg_color};
                color: {$button_text_color};
                {$button_position};
            }
            .rc-popup-overlay {
                background-color: {$overlay}
            }
            .rc-title {
                background-color: {$color1}
            }
            .rc-form-row input[type=\"text\"],
            .rc-form-row input[type=\"email\"],
            .rc-form-row input[type=\"url\"],
            .rc-form-row input[type=\"password\"],
            .rc-form-row input[type=\"search\"],
            .rc-form-row input[type=\"number\"],
            .rc-form-row input[type=\"tel\"],
            .rc-form-row input[type=\"range\"],
            .rc-form-row input[type=\"date\"],
            .rc-form-row input[type=\"month\"],
            .rc-form-row input[type=\"week\"],
            .rc-form-row input[type=\"time\"],
            .rc-form-row input[type=\"datetime\"],
            .rc-form-row input[type=\"datetime-local\"],
            .rc-form-row input[type=\"color\"],
            .rc-form-row textarea {
                color: {$color1};
                border-color: {$color2};
            }
            .rc-form-row.error input,
            .rc-form-row.error textarea {
                border-color: #ff0000;
            }
            .rc-form-row textarea::-webkit-input-placeholder,
            .rc-form-row input::-webkit-input-placeholder {color: {$color1};}
            .rc-form-row textarea::-moz-placeholder,
            .rc-form-row input::-moz-placeholder {color: {$color1};}
            .rc-form-row textarea:-ms-input-placeholder,
            .rc-form-row input:-ms-input-placeholder {color: {$color1};}
            .rc-form-row textarea:-moz-placeholder,
            .rc-form-row input:-moz-placeholder {color: {$color1};}
            .rc-stars-container-inner .rc-star-item.item-on-active .rc-star-item-modal,
            .rc-stars-container-inner .rc-star-item.item-on-active .target-star-item {
                color: {$color1};
            }
            .rc-button,
            .rc-form-row button[type=\"submit\"],
            .rc-form-row input[type=\"submit\"] {
                background: {$color1};
            }
            .rc-variants-container a {
                color: {$color1};
            }
            .rc-variants-container {
                color: {$color1};
            }
            .rc-popup-content p,
            .rc-review-thankyou {
                color: {$color1};
            }
            .rc-stars-container-inner .rc-star-item {
                color: {$color2};
            }
            .rc-stars-container-inner .rc-star-item img {
                color: {$color2};
            }
            /*.quote-container i {
                color: ;
            }*/
        ";

        wp_register_style( 'custom-rc-styles', false);
        wp_enqueue_style( 'custom-rc-styles');
        wp_add_inline_style( 'custom-rc-styles', $custom_css );
    }

    public static function hex2rgba($color, $opacity = false) {

        $default = 'rgb(0,0,0)';

        if(empty($color)) {
            return $default;
        }

        if ($color[0] == '#' ) {
            $color = substr( $color, 1 );
        }

        if (strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return $default;
        }

        $rgb =  array_map('hexdec', $hex);

        if($opacity){
            if(abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",",$rgb).')';
        }

        return $output;
    }

}























