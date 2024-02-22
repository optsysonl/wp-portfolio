<?php

$permalink = 'http://reviews-collector.com/';
$tweettext = 'http://reviews-collector.com/';
$show_count = true;
$send = 'false';
$width = 180;

$rc_option = get_option('rc_options');
$rc_options_sharing = get_option('rc_options_sharing');
$rc_options_popup = get_option('rc_options_popup');
$image = wp_get_attachment_image($rc_options_popup['popup_logo_image'], 'full');

?>
<div class="rc-popup-content" data-item-step="1">
    <div class="rc-title">
        <h3>
            <?php
            if(!empty($rc_options_popup['text']['good_review'])) :
                echo esc_html($rc_options_popup['text']['good_review']);
            else:
                echo esc_html('Please take a moment to share your experience with us.');
            endif;
            ?>
        </h3>
    </div>
    <div class="rc-content">
        <div class="rc-review-from social" data-rating="<?php echo $_POST['rating'] ?>">
            <?php if(!empty($rc_options_sharing['social']['fb_company_name'])): ?>
                <div>
                    <a
                            target="_blank"
                            id="facebook"
                            data-track="yes"
                            data-track-name="funnel_click"
                            data-track-key="facebook"
                            data-track-text="Facebook"
                            data-track-destination="outbound"
                            data-track-context="channel_selection"
                            data-track-sort-order="2"
                            href="https://www.facebook.com/login/?next=https%3A%2F%2Fwww.facebook.com%2F<?php echo $rc_options_sharing['social']['fb_company_name']; ?>%2Freviews%2F"
                    ><div class="link-text">
                        </div>
                        <span class="rc-social-icon facebook"></span>
                    </a>
                </div>
            <?php endif; ?>
            <?php if(!empty($rc_options_sharing['social']['yelp_company_id'])): ?>
                <div>
                    <a
                            id="yelp"
                            target="_blank"
                            data-track="yes"
                            data-track-name="funnel_click"
                            data-track-key="yelp"
                            data-track-text="Yelp"
                            data-track-destination="outbound"
                            data-track-context="channel_selection"
                            data-track-sort-order="0"
                            href="https://www.yelp.com/writeareview/biz/<?php echo $rc_options_sharing['social']['yelp_company_id']; ?>"
                    >
                        <span class="rc-social-icon yelp"></span>
                    </a>
                </div>
            <?php endif; ?>
            <?php if(!empty($rc_options_sharing['social']['google_company_id'])): ?>
                <div>
                    <a
                            id="google"
                            target="_blank"
                            data-track="yes"
                            data-track-name="funnel_click"
                            data-track-key="google"
                            data-track-text="Google"
                            data-track-destination="outbound"
                            data-track-context="channel_selection"
                            data-track-sort-order="0"
                            href="https://search.google.com/local/writereview?placeid=<?php echo $rc_options_sharing['social']['google_company_id']; ?>"
                    >
                        <span class="rc-social-icon google"></span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="rc-variants-container">
            <a class="get-our-site-form" href="javascript: void(0);">Leave review on our site.</a>
            <a class="get-our-site-form" href="javascript: void(0);"><?php echo $image; ?></a>
        </div>
    </div>
</div>


