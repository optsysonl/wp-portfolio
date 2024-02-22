<?php

defined( 'ABSPATH' ) || exit;

function rc_get_admin_reviews(){

	$reviewsTable = new RC_Reviews_Admin_Table();
	$reviewsTable->prepare_items();
	?>
	<div class="wrap">
		<?php $reviewsTable->display(); ?>
	</div>
	<?php
}

function rc_insert_review($data) {
	if (!isset($data['data']) && count($data['data']) == 0) {
		return new WP_Error('data_not_found');
	}
    $rc_option = get_option('rc_options_reviews');
	$new_review_approving = $rc_option['new_review_approving'];

	$review_data = [
		'review_author'			=>'',
		'review_author_email'	=>'',
		'review_content'		=>'',
		'review_rating'			=>''

    ];

	foreach ($data['data'] as $field) {
        switch($field['name']) {
            case 'user-name':
                $review_data['review_author'] = trim($field['value']);
                break;
            case 'user-email':
                $review_data['review_author_email'] = trim($field['value']);
				break;
            case 'user-message':
                $review_data['review_content'] = trim($field['value']);
				break;
            case 'user-rating':
                $review_data['review_rating'] = trim($field['value']);
				break;
        }
    }

    switch($new_review_approving){
        case 'rating1':
            if($review_data['review_rating'] > 1) {
                $review_data['review_approved'] = 1;
            }
            break;
        case 'rating2':
            if($review_data['review_rating'] > 2) {
                $review_data['review_approved'] = 1;
            }
            break;
        case 'rating3':
            if($review_data['review_rating'] > 3) {
                $review_data['review_approved'] = 1;
            }
            break;
        case 'rating4':
            if($review_data['review_rating'] > 4) {
                $review_data['review_approved'] = 1;
            }
            break;
        case 'no_one':
            break;
        default:
            break;
    }

	$review_data['user_id'] = 0;
	$user = wp_get_current_user();
	if ( $user->exists() ) {
	    $review_data['user_id'] = $user->ID;
	}

	$review_data['review_author_ip'] = isset( $_SERVER['REMOTE_ADDR'] ) ?  wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
	$review_data['review_date'] = gmdate( 'Y-m-d H:i:59' );

	$reviewsTable = new RC_Reviews_Data();
	$reviewsTable->add($review_data);

}