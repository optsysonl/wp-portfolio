<?php
$options = get_option('rc_options');
$rc_options_popup = get_option('rc_options_popup');
?>
<div class="rc-popup-content" data-item-step="0">
    <div class="rc-title">
        <h3>
            <?php
            if(!empty($rc_options_popup['text']['title'])) :
                echo esc_html($rc_options_popup['text']['title']);
            else:
                echo esc_html('Please take a moment to review your experience with us. Your feedback not only helps us, it helps other potential customers.');
            endif;
            ?>
        </h3>
    </div>
    <div class="rc-content">
        <div class="rc-rating-container">
            <div class="rc-stars-container-inner">
                <div class="rc-star-item" data-rating="1">
                    <img class="target-star-item" src="/wp-content/plugins/reviews-collector/assets/img/star-solid.svg" />
                    <span class="rc-star-item-modal">
                        <?php
                        if (!empty($rc_options_popup['rating_text']['first'])):
                            echo esc_html($rc_options_popup['rating_text']['first']);
                        else:
                            echo esc_html('Poor');
                        endif;
                        ?>
                    </span>
                </div>
                <div class="rc-star-item" data-rating="2">
                    <img class="target-star-item" src="/wp-content/plugins/reviews-collector/assets/img/star-solid.svg" />
                    <span class="rc-star-item-modal">
                        <?php
                        if (!empty($rc_options_popup['rating_text']['second'])):
                            echo esc_html($rc_options_popup['rating_text']['second']);
                        else:
                            echo esc_html('Subpar');
                        endif;
                        ?>
                    </span>
                </div>
                <div class="rc-star-item" data-rating="3">
                    <img class="target-star-item" src="/wp-content/plugins/reviews-collector/assets/img/star-solid.svg" />
                    <span class="rc-star-item-modal">
                        <?php
                        if (!empty($rc_options_popup['rating_text']['third'])):
                            echo esc_html($rc_options_popup['rating_text']['third']);
                        else:
                            echo esc_html('Okay');
                        endif;
                        ?>
                    </span>
                </div>
                <div class="rc-star-item" data-rating="4">
                    <img class="target-star-item" src="/wp-content/plugins/reviews-collector/assets/img/star-solid.svg" />
                    <span class="rc-star-item-modal">
                        <?php
                        if (!empty($rc_options_popup['rating_text']['fourth'])):
                            echo esc_html($rc_options_popup['rating_text']['fourth']);
                        else:
                            echo esc_html('Good');
                        endif;
                        ?>
                    </span>
                </div>
                <div class="rc-star-item" data-rating="5">
                    <img class="target-star-item" src="/wp-content/plugins/reviews-collector/assets/img/star-solid.svg" />
                    <span class="rc-star-item-modal">
                        <?php
                        if (!empty($rc_options_popup['rating_text']['fifth'])):
                            echo esc_html($rc_options_popup['rating_text']['fifth']);
                        else:
                            echo esc_html('Great');
                        endif;
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="rc-ajax-container" data-item-step="1"></div>