<?php

defined( 'ABSPATH' ) || exit;

$review_id = $_GET['id'];

$reviewsTable = new RC_Reviews_Data();
$review = $reviewsTable->get($review_id);
$review = $review[0];
?>
<div class="rc-container">
    <form method="post" id="rc_reviews_form">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
					<?php _e('Author', 'reviewscollector'); ?>
                </th>
                <td>
                    <input
                            type="text"
                            name="review_author"
                            value="<?php echo isset($review->review_author) ? $review->review_author : ''; ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
					<?php _e('Author Email', 'reviewscollector'); ?>
                </th>
                <td>
                    <input
                            type="text"
                            name="review_author_email"
                            value="<?php echo isset($review->review_author_email) ? $review->review_author_email : ''; ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
					<?php _e('Date', 'reviewscollector'); ?>
                </th>
                <td>
                    <input
                            type="text"
                            name="review_date"
                            value="<?php echo isset($review->review_date) ? $review->review_date : ''; ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
					<?php _e('Content', 'reviewscollector'); ?>
                </th>
                <td>
                        <textarea
                                rows="6" cols="85"
                                name="review_content"
                        /><?php echo isset($review->review_content) ? $review->review_content : ''; ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">
					<?php _e('Rating', 'reviewscollector'); ?>
                </th>
                <td>
                    <input
                            type="text"
                            name="review_rating"
                            value="<?php echo isset($review->review_rating) ? $review->review_rating : ''; ?>"
                    />
                </td>
            </tr>
            <tr>
                <th scope="row">
					<?php _e('Approved.', 'reviewscollector'); ?>
                </th>
                <td>
                    <input type="checkbox" name="review_approved" <?php echo (isset($review->review_approved) && $review->review_approved) ? 'checked="true"' : ''; ?> value="1" />
                </td>
            </tr>

            </tbody>
        </table>
        <br/>
        <div class="rc-element">
            <input type="hidden" name="id" value="<?php echo $review_id; ?>" />
            <input type="hidden" name="rc_action_review" value="rc_save_review" />
            <?php wp_nonce_field('rc_save_review_form_nonce_action', 'rc_save_review_form_nonce'); ?>
            <input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Save Review', 'reviewscollector' ); ?>" />
        </div>
        <br/>
    </form>
</div>