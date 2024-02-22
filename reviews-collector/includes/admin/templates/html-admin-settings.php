<?php

defined('ABSPATH') || exit;

$rc_option = get_option('rc_options');

?>
<form method="post" id="rc_settings_form">
    <div class="rc-container cmb2-metabox cmb2-wrap form-table">
        <div class="cmb-row">
            <div class="cmb-td"><h3><?php _e('Review button settings', 'reviewscollector'); ?></h3></div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Show "Leave a Review" button global.', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input type="checkbox"
                       name="reviews_collector[review_button_display]" <?php echo (isset($rc_option['review_button_display']) && $rc_option['review_button_display']) ? 'checked="true"' : ''; ?>
                       value="1"/>
            </div>
        </div>


        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Display button in shortcode content.', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input type="checkbox"
                       name="reviews_collector[review_button_display_shortcode]" <?php echo (isset($rc_option['review_button_display_shortcode']) && $rc_option['review_button_display_shortcode']) ? 'checked="true"' : ''; ?>
                       value="1"/>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Content location', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <?php
                $review_button_content_location = (isset($rc_option['review_button_content_location'])) ? $rc_option['review_button_content_location'] : '';
                ?>
                <select name="reviews_collector[review_button_content_location]" class="custom-select">
                    <option <?php echo ($review_button_content_location == 'popup') ? 'selected="selected"' : ''; ?>
                            value="popup"><?php _e('Popup', 'reviewscollector'); ?></option>
                    <option <?php echo ($review_button_content_location == 'content') ? 'selected="selected"' : ''; ?>
                            value="content"><?php _e('Content', 'reviewscollector'); ?></option>
                </select>
                <p class="description" id="tagline-description">Used only for button in shortcode content.</p>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('"Leave a Review" button position.', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <select name="reviews_collector[review_button_position]" class="custom-select">
                    <option <?php echo ($rc_option['review_button_position'] == 'top_right') ? 'selected="selected"' : ''; ?>
                            value="top_right"><?php _e('Top Right', 'reviewscollector'); ?></option>
                    <option <?php echo ($rc_option['review_button_position'] == 'top_left') ? 'selected="selected"' : ''; ?>
                            value="top_left"><?php _e('Top Left', 'reviewscollector'); ?></option>
                    <option <?php echo ($rc_option['review_button_position'] == 'bottom_right') ? 'selected="selected"' : ''; ?>
                            value="bottom_right"><?php _e('Bottom Right', 'reviewscollector'); ?></option>
                    <option <?php echo ($rc_option['review_button_position'] == 'bottom_left') ? 'selected="selected"' : ''; ?>
                            value="bottom_left"><?php _e('Bottom Left', 'reviewscollector'); ?></option>
                </select>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Button color', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input type="text" name="reviews_collector[button_background_color]"
                       value="<?php echo $rc_option['button_background_color']; ?>" class="cpa-color-picker">
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Button text color', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input type="text" name="reviews_collector[button_text_color]"
                       value="<?php echo $rc_option['button_text_color']; ?>" class="cpa-color-picker">
            </div>
        </div>

        <div class="cmb-row">
            <div class="cmb-td">
                <div class="rc-element">
                    <input type="hidden" name="rc_action" value="rc_save_settings"/>
                    <?php wp_nonce_field('rc_save_settings_form_nonce_action', 'rc_save_settings_form_nonce'); ?>
                    <input type="submit" class="button-primary xtei_submit_button" style=""
                           value="<?php esc_attr_e('Save Settings', 'reviewscollector'); ?>"/>
                </div>
            </div>
        </div>
    </div>
</form>