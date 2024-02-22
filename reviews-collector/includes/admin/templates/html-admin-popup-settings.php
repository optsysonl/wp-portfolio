<?php

defined('ABSPATH') || exit;

$rc_option = get_option('rc_options_popup');

?>
<form method="post" id="rc_list_settings_form">
    <div class="rc-container cmb2-metabox cmb2-wrap form-table">
        <div class="cmb-row">
            <div class="cmb-th">
                <h3><?php _e('Form style settings', 'reviewscollector'); ?></h3>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Main color', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input type="text" name="reviews_collector[popup_main_color]"
                       value="<?php echo $rc_option['popup_main_color']; ?>" class="cpa-color-picker">
                <p class="description" id="tagline-description">Title, buttons</p>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Second color', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input type="text" name="reviews_collector[popup_second_color]"
                       value="<?php echo $rc_option['popup_second_color']; ?>" class="cpa-color-picker">
                <p class="description" id="tagline-description">Bottom line, text color</p>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Popup logo', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <?php
                $name = 'reviews_collector[popup_logo_image]';
                $value = !empty($rc_option['popup_logo_image']) ? $rc_option['popup_logo_image'] : '';
                $image = ' button">Upload image';
                $image_size = 'full';
                $display = 'none';

                if ($image_attributes = wp_get_attachment_image_src($value, $image_size)) {
                    $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
                    $display = 'inline-block';
                }
                ?>

                <div>
                    <a href="#" class="misha_upload_image_button<?php echo $image; ?></a>
                    <input
                            type="hidden"
                            name="<?php echo $name; ?>"
                            id="<?php echo $name; ?>"
                            value="<?php echo esc_attr($value); ?>"
                    />
                    <a href="#" class="misha_remove_image_button"
                       style="display:inline-block;display:<?php echo $display; ?>">Remove image</a>
                </div>

            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <h4><?php _e('Popup styles', 'reviewscollector'); ?></h4>
            </div>
            <div class="cmb-td"></div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Overlay color', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input type="text" name="reviews_collector[popup_overlay_color]"
                       value="<?php echo $rc_option['popup_overlay_color']; ?>" class="cpa-color-picker">
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <h4><?php _e('Form text', 'reviewscollector'); ?></h4>
            </div>
            <div class="cmb-td"></div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Text for rating step', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <textarea
                        name="reviews_collector[text][title]"
                        placeholder="Please take a moment to review your experience with us. Your feedback not only helps us, it helps other potential customers."
                ><?php echo (!empty($rc_option['text']['title'])) ? $rc_option['text']['title'] : ''; ?></textarea>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Text for share step', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <textarea
                        name="reviews_collector[text][good_review]"
                        placeholder="Please take a moment to share your experience with us."
                ><?php echo (!empty($rc_option['text']['good_review'])) ? $rc_option['text']['good_review'] : ''; ?></textarea>
                <!--                <p class="description" id="tagline-description">Text for good review</p>-->
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Text for good review', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <textarea
                        name="reviews_collector[text][form_good_review]"
                        placeholder="We strive for 100% customer satisfaction. If we fell short, please tell us more so we can address your concerns."
                ><?php echo (!empty($rc_option['text']['form_good_review'])) ? $rc_option['text']['form_good_review'] : ''; ?></textarea>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Text for bad review', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <textarea
                        name="reviews_collector[text][bad_review]"
                        placeholder="We strive for 100% customer satisfaction. If we fell short, please tell us more so we can address your concerns."
                ><?php echo (!empty($rc_option['text']['bad_review'])) ? $rc_option['text']['bad_review'] : ''; ?></textarea>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Thank you message', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <textarea
                        name="reviews_collector[text][thank_you_message]"
                        placeholder="Your message has been submitted. Thank you for your feedback!"
                ><?php echo (!empty($rc_option['text']['thank_you_message'])) ? $rc_option['text']['thank_you_message'] : ''; ?></textarea>
            </div>
        </div>

        <div class="cmb-row">
            <div class="cmb-th">
                <h4><?php _e('Rating text', 'reviewscollector'); ?></h4>
            </div>
            <div class="cmb-td"></div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('One star text', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[rating_text][first]"
                        placeholder="Poor"
                        value="<?php echo (!empty($rc_option['rating_text']['first'])) ? $rc_option['rating_text']['first'] : ''; ?>" />
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Two star text', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[rating_text][second]"
                        placeholder="Subpar"
                        value="<?php echo (!empty($rc_option['rating_text']['second'])) ? $rc_option['rating_text']['second'] : ''; ?>" />
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Three star text', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[rating_text][third]"
                        placeholder="Okay"
                        value="<?php echo (!empty($rc_option['rating_text']['third'])) ? $rc_option['rating_text']['third'] : ''; ?>" />
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Four star text', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[rating_text][fourth]"
                        placeholder="Good"
                        value="<?php echo (!empty($rc_option['rating_text']['fourth'])) ? $rc_option['rating_text']['fourth'] : ''; ?>" />
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Five star text', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[rating_text][fifth]"
                        placeholder="Great"
                        value="<?php echo (!empty($rc_option["rating_text"]['fifth'])) ? $rc_option['rating_text']['fifth'] : ''; ?>" />
            </div>
        </div>

        <div class="cmb-row">
            <div class="cmb-td">
                <div class="rc-element">
                    <input type="hidden" name="rc_action" value="rc_save_popup_settings"/>
                    <?php wp_nonce_field('rc_save_popup_settings_form_nonce_action', 'rc_save_popup_settings_form_nonce'); ?>
                    <input type="submit" class="button-primary xtei_submit_button" style=""
                           value="<?php esc_attr_e('Save Settings', 'reviewscollector'); ?>"/>
                </div>
            </div>
        </div>

    </div>
</form>