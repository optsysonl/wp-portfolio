<?php
/**
 * Created by PhpStorm.
 * User: GreyGooroo
 * Date: 12.08.2019
 * Time: 17:33
 */

$display_user_avatar = (isset($rcs_options['display_user_avatar'])) ? $rcs_options['display_user_avatar'] : false;
$display_user_name = (isset($rcs_options['display_user_name'])) ? $rcs_options['display_user_name'] : false;
$display_date = (isset($rcs_options['display_date'])) ? $rcs_options['display_date'] : false;
$display_rating = (isset($rcs_options['display_rating'])) ? $rcs_options['display_rating'] : false;
$display_quote = (isset($rcs_options['display_quote_icon'])) ? $rcs_options['display_quote_icon'] : false;

?>
<div class="review-card">

    <article class="review" id="<?php echo $review->review_id ?>">
        <aside class="review__consumer-information">
            <?php if ($display_user_avatar) : ?>
                <div class="consumer-information__picture">
                    <div class="avatar-container">
                        <?php echo get_avatar($review->review_author_email, $size = 96, $default = '', $alt = $review->review_author, $args = null); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($display_user_name) : ?>
                <div class="consumer-information__details">
                    <div class="consumer-information__name">
                        <?php echo $review->review_author ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="review-content__header">
                <div class="review-content-header">
                    <?php if ($display_rating) : ?>
                        <div class="star-rating star-rating-<?php echo $review->review_rating ?> star-rating--medium">
                            <?php for ($i = 1; $i < 6; $i++) : ?>
                                <div class="star-item star-item--color">
                                    <img src="<?php echo plugins_url('/assets/img/', RC_PLUGIN_FILE) ?>star.svg"
                                         data-stars="<?php echo $i ?>" alt="Star <?php echo $i ?>">
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($display_date) : ?>
                        <div class="review-content-header__dates">
                            <span><?php echo mysql2date('Y-m-d', $review->review_date) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
<!--                <div class="quote-container">-->
<!--                    <p><i class="rc-fas rc-fa-quote-right" style="color: --><?php //echo $rcs_options['quote_icon_color']; ?><!--"></i></p>-->
<!--                </div>-->
        </aside>

        <section class="review__content">

            <div class="review-content">

                <div class="review-content__body">
                    <p class="review-content__text">
                        <?php echo $review->review_content ?>
                    </p>
                </div>
            </div>
        </section>
    </article>
</div>