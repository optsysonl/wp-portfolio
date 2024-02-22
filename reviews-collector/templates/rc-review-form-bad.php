<?php

$rating = 5;
if (isset($_POST['rating'])) {
    $rating = (int)$_POST['rating'];
}
$rc_option = get_option('rc_options');
$rc_options_popup = get_option('rc_options_popup');

?>
<div class="rc-popup-content" data-item-step="2">
    <div class="rc-title">
        <h3>
            <?php
            if(!empty($rc_options_popup['text']['bad_review'])) :
                echo esc_html($rc_options_popup['text']['bad_review']);
            else:
                echo esc_html('We strive for 100% customer satisfaction. If we fell short, please tell us more so we can address your concerns.');
            endif;
            ?>
        </h3>
    </div>
    <div class="rc-content">
        <?php include(RC_ABSPATH .'templates/rc-review-form.php'); ?>
    </div>
</div>

