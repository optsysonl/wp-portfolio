<?php

/**
 * Template for
 */

$reviewsTable = new RC_Reviews_Data();
$page = isset( $_GET['rcpage'] ) ? abs( (int) $_GET['rcpage'] ) : 1;


$display_pagination = (isset($rcs_options['display_pagination'])) ? true : false;
$data = $reviewsTable->get_list($atts, $page);

?>
<?php if (count($data['reviews']) > 0) : ?>

    <div class="rc-review-list">
        <?php foreach ($data['reviews'] as $review) : ?>
            <?php include(RC_ABSPATH .'templates/rc-review-item.php'); ?>
        <?php endforeach; ?>
    </div>

    <?php if($display_pagination): ?>
        <?php if($data['pagination']['page_count'] > 1) :
            $pagination = paginate_links( array(
                'base' => add_query_arg( 'rcpage', '%#%' ),
                'total' => $data['pagination']['page_count'],
                'current' => $page
            ));
            ?>
            <div class="pagination">
                <?php echo $pagination ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

<?php else : ?>
    <div>No reviews available</div>
<?php endif; ?>
