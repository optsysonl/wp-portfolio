<?php

/**
 * Template for review carouse type
 */

$reviewsTable = new RC_Reviews_Data();
$data = $reviewsTable->get_carousel($atts);

$carousel_autoplay = (isset($rcs_options['carousel_autoplay'])) ? 1 : 0;
$carousel_autoplaySpeed = (isset($rcs_options['carousel_autoplay_speed'])) ? $rcs_options['carousel_autoplay_speed'] : 1000;
$carousel_animation_speed = (isset($rcs_options['carousel_animation_speed'])) ? $rcs_options['carousel_animation_speed'] : 600;
$carousel_columns = (isset($rcs_options['rl_columns'])) ? $rcs_options['rl_columns'] : 1;

wp_enqueue_style('rc-carousel-theme', RC_PLUGIN_DIR_URI . 'assets/css/slick-theme.css');
wp_enqueue_style('rc-carousel-style', RC_PLUGIN_DIR_URI . 'assets/css/slick.css');
wp_enqueue_script('rc-carousel-script', RC_PLUGIN_DIR_URI.'assets/js/slick.min.js', array('jquery'), '1.0', false);
$carousel_display_navigation = (isset($rcs_options['carousel_display_nav_buttons'])) ? 1 : 0;
$carousel_nav_main_color = (isset($rcs_options['carousel_nav_main_color'])) ? $rcs_options['carousel_nav_main_color'] : '#fff';
$style = "
            .slick-prev:before, .slick-next:before {
                color: {$carousel_nav_main_color}
            }               
        ";

wp_register_style( 'rc-carousel-custom-style', false);
wp_enqueue_style('rc-carousel-custom-style');
wp_add_inline_style('rc-carousel-custom-style', $style);

?>
<?php if (count($data['reviews']) > 0) : ?>
    <div
            class="rc-review-carousel"
            data-displaynavigation="<?php echo $carousel_display_navigation; ?>"
            data-autoplay="<?php echo $carousel_autoplay; ?>"
            data-autoplayspeed="<?php echo $carousel_autoplaySpeed; ?>"
            data-columns="<?php echo $carousel_columns; ?>"
            data-speed="<?php echo $carousel_animation_speed; ?>"
    >
        <?php foreach ($data['reviews'] as $review) : ?>
            <?php include(RC_ABSPATH .'templates/rc-review-item.php'); ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div>No reviews available</div>
<?php endif; ?>
