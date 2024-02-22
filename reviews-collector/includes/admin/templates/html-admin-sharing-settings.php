<?php

defined('ABSPATH') || exit;

$rc_option = get_option('rc_options_sharing');

?>
<form method="post" id="rc_color_settings_form">
    <div class="rc-container cmb2-metabox cmb2-wrap form-table">
        <div class="cmb-row">
            <div class="cmb-th">
                <h3><?php _e('Sharing settings', 'reviewscollector'); ?></h3>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Facebook', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[social][fb_company_name]"
                        value="<?php echo (!empty($rc_option['social']['fb_company_name'])) ? $rc_option['social']['fb_company_name'] : ''; ?>"
                />
                <p class="description" id="tagline-description">Insert company id.</p>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Yelp', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[social][yelp_company_id]"
                        value="<?php echo (!empty($rc_option['social']['yelp_company_id'])) ? $rc_option['social']['yelp_company_id'] : ''; ?>"
                />
                <p class="description" id="tagline-description">Insert company id.</p>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('Google', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <input
                        type="text"
                        name="reviews_collector[social][google_company_id]"
                        value="<?php echo (!empty($rc_option['social']['google_company_id'])) ? $rc_option['social']['google_company_id'] : ''; ?>"
                />
                <p class="description" id="tagline-description">Insert company id. You can find it on this <a
                            target="_blank" href="https://developers.google.com/places/place-id">page.</a></p>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <div class="rc-element">
                    <input type="hidden" name="rc_action" value="rc_save_sharing_settings"/>
                    <?php wp_nonce_field('rc_save_sharing_settings_form_nonce_action', 'rc_save_sharing_settings_form_nonce'); ?>
                    <input type="submit" class="button-primary xtei_submit_button" style=""
                           value="<?php esc_attr_e('Save Settings', 'reviewscollector'); ?>"/>
                </div>
            </div>
        </div>
    </div>
</form>