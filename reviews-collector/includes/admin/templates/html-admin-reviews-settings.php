<?php

defined('ABSPATH') || exit;

$rc_option = get_option('rc_options_reviews');
?>
<form method="post" id="rc_slider_settings_form">
    <div class="rc-container cmb2-metabox cmb2-wrap form-table">
        <div class="cmb-row">
            <div class="cmb-th">
                <h3><?php _e('Reviews adding settings', 'reviewscollector'); ?></h3>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <label for=""><?php _e('New reviews approving', 'reviewscollector'); ?></label>
            </div>
            <div class="cmb-td">
                <?php
                $new_review_approving = (isset($rc_option['new_review_approving'])) ? $rc_option['new_review_approving'] : '';
                ?>
                <select name="reviews_collector[new_review_approving]" class="custom-select">
                    <option <?php echo ($new_review_approving == 'no_one') ? 'selected="selected"' : ''; ?>
                            value="no_one"><?php _e('No one is approve', 'reviewscollector'); ?></option>
                    <option <?php echo ($new_review_approving == 'rating1') ? 'selected="selected"' : ''; ?>
                            value="rating1"><?php _e('Ratings > 1', 'reviewscollector'); ?></option>
                    <option <?php echo ($new_review_approving == 'rating2') ? 'selected="selected"' : ''; ?>
                            value="rating2"><?php _e('Ratings > 2', 'reviewscollector'); ?></option>
                    <option <?php echo ($new_review_approving == 'rating3') ? 'selected="selected"' : ''; ?>
                            value="rating3"><?php _e('Ratings > 3', 'reviewscollector'); ?></option>
                    <option <?php echo ($new_review_approving == 'rating4') ? 'selected="selected"' : ''; ?>
                            value="rating4"><?php _e('Ratings > 4', 'reviewscollector'); ?></option>
                </select>
                <p class="description" id="tagline-description">You can change approval status later on review edit
                    page.</p>
            </div>
        </div>
        <div class="cmb-row">
            <div class="cmb-th">
                <div class="rc-element">
                    <input type="hidden" name="rc_action" value="rc_save_reviews_settings"/>
                    <?php wp_nonce_field('rc_save_reviews_settings_form_nonce_action', 'rc_save_reviews_settings_form_nonce'); ?>
                    <input type="submit" class="button-primary xtei_submit_button" style=""
                           value="<?php esc_attr_e('Save Settings', 'reviewscollector'); ?>"/>
                </div>
            </div>
        </div>
    </div>
</form>