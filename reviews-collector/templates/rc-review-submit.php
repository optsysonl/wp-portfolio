<?php
$rc_option = get_option('rc_options');
$image = wp_get_attachment_image($rc_option['popup_logo_image'], 'full');
$rc_options_popup = get_option('rc_options_popup');
?>
<div class="rc-popup-content" data-item-step="">
    <div class="rc-content">
        <div class="rc-review-thankyou">
            <div class="rc-submit-logo">
                <?php echo $image; ?>
            </div>
            <span>
                <?php
                if(!empty($rc_options_popup['text']['thank_you_message'])) :
                    echo esc_html($rc_options_popup['text']['thank_you_message']);
                else:
                    echo esc_html('Your message has been submitted. Thank you for your feedback!');
                endif;
                ?>
            </span>
        </div>
    </div>
</div>
