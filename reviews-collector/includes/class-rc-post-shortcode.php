<?php

class RC_Post_Shortcode
{

    public function __construct()
    {
        add_action('init', array(__CLASS__, 'register_shortcode'), 5);
        add_action('add_meta_boxes', array(__CLASS__, 'shortcode_settings_metabox'));
        add_action('save_post', array(__CLASS__, 'shortcode_settings_save'));
    }

    public static function register_shortcode()
    {
        register_post_type(
            'rc_shortcode',
            array(
                'labels' => array(
                    'name' => __('Shortcodes', 'review-collector'),
                    'singular_name' => __('Shortcode', 'review-collector'),
                    'menu_name' => _x('Shortcodes', 'Admin menu name', 'review-collector'),
                    'add_new' => __('Add Shortcode', 'review-collector'),
                    'add_new_item' => __('Add new Shortcode', 'review-collector'),
                    'edit' => __('Edit', 'review-collector'),
                    'edit_item' => __('Edit Shortcode', 'review-collector'),
                    'new_item' => __('New Shortcode', 'review-collector'),
                    'view_item' => __('View Shortcode', 'review-collector'),
                    'search_items' => __('Search Shortcodes', 'review-collector'),
                    'not_found' => __('No Shortcodes found', 'review-collector'),
                    'not_found_in_trash' => __('No Shortcodes found in trash', 'review-collector'),
                    'parent' => __('Parent Shortcode', 'review-collector'),
                    'filter_items_list' => __('Filter Shortcodes', 'review-collector'),
                    'items_list_navigation' => __('Shortcodes navigation', 'review-collector'),
                    'items_list' => __('Shortcodes list', 'review-collector'),
                ),
                'description' => __('', 'review-collector'),
                'public' => true,
                'show_ui' => true,
                'capability_type' => 'post',
                'map_meta_cap' => true,
                'publicly_queryable' => false,
                'exclude_from_search' => true,
                'show_in_menu' => current_user_can('manage_options') ? 'review-collector' : true,
                'hierarchical' => false,
                'rewrite' => false,
                'query_var' => false,
                'supports' => array('title'),
                'show_in_nav_menus' => false,
                'show_in_admin_bar' => true,
            )
        );
    }

    public static function shortcode_settings_metabox()
    {
        add_meta_box('rc_shortcode_id', 'Shortcode settings', array(__CLASS__, 'rc_shortcode_settings_metabox_content'), 'rc_shortcode', 'advanced', 'high');
    }

    public static function rc_shortcode_settings_metabox_content($post)
    {
        $rcs_options = get_post_meta($post->ID, 'rcs_options', true);

        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('rc_admin_custom_js', RC_PLUGIN_DIR_URI . 'assets/js/admin-script.js', array('jquery', 'wp-color-picker'), '', true);

        ?>

        <div class="rc-shortcode-settings cmb2-options-page">

            <p class="description">
                <label for="wpcf7-shortcode">Copy this shortcode and paste it into your
                    post,
                    page, or text widget content:</label>
                <span style="display: block; margin-top: 4px;"
                      class="shortcode wp-ui-highlight">
                                            <input
                                                    type="text"
                                                    id="wpcf7-shortcode"
                                                    onfocus="this.select();"
                                                    readonly="readonly"
                                                    class="large-text code"
                                                    value="[reviews-collector id=&quot;<?php echo $post->ID; ?>&quot;]"
                                                    style="background: transparent; border: 0;box-shadow: none; color: #fff; padding-top: 3px;"
                                            >
                                        </span>
            </p>

            <div class="rc-tabs-wrapper">
                <div class="rc-tabs-pannel nav-tab-wrapper">
                    <a data-tab="tab_5" class="nav-tab nav-tab-active" href="javascript: void(0);">Main</a>
                    <a data-tab="tab_2" class="nav-tab" href="javascript: void(0);">List settings</a>
                    <a data-tab="tab_1" class="nav-tab" href="javascript: void(0);">Carousel settings</a>
                    <a data-tab="tab_4" class="nav-tab" href="javascript: void(0);">Review styles</a>
                </div>

                <form method="post" id="rc_shortcode_settings_form">

                    <div data-tab="tab_5" class="rc-tabs-content cmb2-wrap form-table rc-tabs-content-active">
                        <div class="cmb2-metabox cmb-field-list">
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="rcs_shortcode_type">
                                        <?php _e('Shortcode content type', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <select id="rcs_shortcode_type" name="rcs_options[shortcode_type]" class="custom-select">
                                        <option value="type_1" <?php echo ($rcs_options['shortcode_type'] == 'type_1') ? 'selected="selected"' : ''; ?>>
                                            Display reviews form
                                        </option>
                                        <option value="type_2" <?php echo ($rcs_options['shortcode_type'] == 'type_2') ? 'selected="selected"' : ''; ?>>
                                            Display reviews
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <h3>List settings</h3>
                                </div>
                                <div class="cmb-td">
                                </div>
                            </div>

                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Reviews list type', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <select name="rcs_options[rl_display_type]" class="custom-select">
                                        <option value="type_1" <?php echo (isset($rcs_options['rl_display_type']) && $rcs_options['rl_display_type'] == 'type_1') ? 'selected="selected"' : ''; ?>>
                                            List
                                        </option>
                                        <option value="type_2" <?php echo (isset($rcs_options['rl_display_type']) && $rcs_options['rl_display_type'] == 'type_2') ? 'selected="selected"' : ''; ?>>
                                            Carousel
                                        </option>
                                    </select>
                                    <p class="description">Works only if shortcode content type is 'review list'.</p>
                                </div>
                            </div>

                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Columns', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <select name="rcs_options[rl_columns]" class="custom-select">
                                        <option value="1" <?php echo ($rcs_options['rl_columns'] == '1') ? 'selected="selected"' : ''; ?>>
                                            1
                                        </option>
                                        <option value="2" <?php echo ($rcs_options['rl_columns'] == '2') ? 'selected="selected"' : ''; ?>>
                                            2
                                        </option>
                                        <option value="3" <?php echo ($rcs_options['rl_columns'] == '3') ? 'selected="selected"' : ''; ?>>
                                            3
                                        </option>
                                        <option value="4" <?php echo ($rcs_options['rl_columns'] == '4') ? 'selected="selected"' : ''; ?>>
                                            4
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Display reviews count', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="number"
                                            name="rcs_options[display_reviews_count]"
                                            value="<?php echo $rcs_options['display_reviews_count']; ?>"
                                    />
                                </div>
                            </div>

                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <h3>Form settings</h3>
                                </div>
                                <div class="cmb-td">
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Display review button', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[rl_display_review_button]"
                                            value="1"
                                        <?php echo (isset($rcs_options['rl_display_review_button'])) ? 'checked="checked"' : ''; ?>
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Display form in content', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[rl_review_button_content_location]"
                                            value="1"
                                        <?php echo (isset($rcs_options['rl_review_button_content_location'])) ? 'checked="checked"' : ''; ?>
                                    />
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- reviews list tab -->
                    <div data-tab="tab_2" class="rc-tabs-content cmb2-wrap form-table">
                        <div class="cmb2-metabox cmb-field-list">

                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Display pagination', 'reviewscollector'); ?>
                                    </label>
                                </div>

                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[display_pagination]"
                                        <?php echo (isset($rcs_options['display_pagination']) && $rcs_options['display_pagination']) ? 'checked="true"' : ''; ?>
                                            value="1"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- main settings tab -->
                    <div data-tab="tab_1" class="rc-tabs-content cmb2-wrap form-table">
                        <div class="cmb2-metabox cmb-field-list">
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Autoplay', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[carousel_autoplay]"
                                            value="1"
                                        <?php echo (isset($rcs_options['carousel_autoplay'])) ? "checked='checked'" : ""; ?>
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Autoplay speed', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="number"
                                            name="rcs_options[carousel_autoplay_speed]"
                                            value="<?php echo (isset($rcs_options['carousel_autoplay_speed'])) ? $rcs_options['carousel_autoplay_speed'] : ""; ?>"
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Animation speed', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="number"
                                            name="rcs_options[carousel_animation_speed]"
                                            value="<?php echo (isset($rcs_options['carousel_animation_speed'])) ? $rcs_options['carousel_animation_speed'] : ""; ?>"
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Show navigation buttons', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[carousel_display_nav_buttons]"
                                            value="1"
                                        <?php echo (isset($rcs_options['carousel_display_nav_buttons'])) ? 'checked="checked"' : ""; ?>
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Navigation main color', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="text"
                                            name="rcs_options[carousel_nav_main_color]"
                                            value="<?php echo (isset($rcs_options['carousel_nav_main_color'])) ? $rcs_options['carousel_nav_main_color'] : '#000000'; ?>"
                                            class="cpa-color-picker"
                                    />
                                </div>
                            </div>
                        </div>
                    </div

                    <!-- styles tab -->
                    <div data-tab="tab_4" class="rc-tabs-content cmb2-wrap form-table">
                        <div class="cmb2-metabox cmb-field-list">
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                    <?php _e('Display user avatar', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[display_user_avatar]"
                                        <?php echo (isset($rcs_options['display_user_avatar']) && $rcs_options['display_user_avatar']) ? 'checked="true"' : ''; ?>
                                            value="1"
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                    <?php _e('Display user name', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[display_user_name]"
                                        <?php echo (isset($rcs_options['display_user_name']) && $rcs_options['display_user_name']) ? 'checked="true"' : ''; ?>
                                            value="1"
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                        <?php _e('Display review added date', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[display_date]"
                                        <?php echo (isset($rcs_options['display_date']) && $rcs_options['display_date']) ? 'checked="true"' : ''; ?>
                                            value="1"
                                    />
                                </div>
                            </div>
                            <div class="cmb-row">
                                <div class="cmb-th">
                                    <label for="">
                                    <?php _e('Display rating', 'reviewscollector'); ?>
                                    </label>
                                </div>
                                <div class="cmb-td">
                                    <input
                                            type="checkbox"
                                            name="rcs_options[display_rating]"
                                        <?php echo (isset($rcs_options['display_rating']) && $rcs_options['display_rating']) ? 'checked="true"' : ''; ?>
                                            value="1"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
        </div>

        <?php

    }

    public static function shortcode_settings_save($post_id)
    {
        if (!isset($_POST['rcs_options'])) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $options = $_POST['rcs_options'];

        update_post_meta($post_id, 'rcs_options', $options);
    }
}

return new RC_Post_Shortcode();































