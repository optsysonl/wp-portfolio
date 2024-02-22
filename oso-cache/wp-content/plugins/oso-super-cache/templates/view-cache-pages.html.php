<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/view-cache.svg" alt="">-->
<!--    <h3>--><?php //_ex('Pages', 'Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->
<div class="content">

    <div class="messages">
        <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <fieldset>

        <?php
        if (!empty($searchString)) {
            ?>
            <legend><?php printf(_x('Cached pages: Search result for &#8222;%s&#8220;', 'Headline of a fieldset', 'oso-super-cache'), $searchString); ?></legend><?php
        } else {
            ?>
            <legend><?php _ex('Cached pages', 'Headline of a fieldset', 'oso-super-cache'); ?></legend><?php
        }
        ?>

        <form method="post">
            <div class="search-bar">
                <?php wp_nonce_field('oso_super_cache_view_cache_search'); ?>
                <input type="hidden" name="searchFormSend" value="1">
                <input type="search" name="search-page" value="<?php echo $searchString; ?>">
                <input class="button" type="submit"
                       value="<?php _ex('Search page', 'Button title', 'oso-super-cache'); ?>">
            </div>
        </form>

        <form method="post">
            <table class="full-width">
                <thead>
                <tr>
                    <th><input type="checkbox" name="checkAll" value="0"></th>
                    <th><?php _ex('SSL', 'Table head', 'oso-super-cache'); ?></th>
                    <th><?php _ex('Domain', 'Table head', 'oso-super-cache'); ?></th>
                    <th><?php _ex('Path', 'Table head', 'oso-super-cache'); ?></th>
                    <th><?php _ex('Runtime without cache', 'Table head', 'oso-super-cache'); ?></th>
                    <th><?php _ex('Runtime with cache', 'Table head', 'oso-super-cache'); ?></th>
                    <th><?php _ex('Last updated', 'Table head', 'oso-super-cache'); ?></th>
                    <th><?php _ex("Next update", 'Table head', 'oso-super-cache'); ?></th>
                    <th><?php _ex("Cache storage", 'Table head', 'oso-super-cache'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($pagesData['pages'])) {

                    foreach ($pagesData['pages'] as $pageData) {
                        ?>
                        <tr>
                            <td class="align-center"><input type="checkbox" name="urls[]"
                                                            value="<?php echo esc_html(($pageData->https ? 'https-' : '') . $pageData->hash); ?>">
                            </td>
                            <td class="align-center">
                                <?php
                                if ($pageData->https) {
                                    ?>
                                    <span class="dashicons dashicons-yes"></span>
                                    <?php
                                } else {
                                    ?>
                                    <span class="dashicons dashicons-no"></span>
                                    <?php
                                }
                                ?>
                            </td>
                            <td><?php echo $pageData->domain; ?></td>
                            <td><?php echo $pageData->url; ?></td>
                            <td class="align-right"><?php echo number_format_i18n($pageData->runtime_without_cache, 3); ?><?php _ex('sec', 'Datetime', 'oso-super-cache'); ?></td>
                            <td class="align-right"><?php echo !empty($pageData->runtime_with_cache) ? number_format_i18n($pageData->runtime_with_cache, 3) : '-'; ?><?php _ex('sec', 'Datetime', 'oso-super-cache'); ?></td>
                            <td class="align-right"><?php echo $pageData->last_updated != '0000-00-00 00:00:00' ? \OSOSuperCache\Factory::get('Cache\Tools')->formatTimestamp(strtotime($pageData->last_updated)) : '-'; ?></td>
                            <td class="align-right"><?php echo $pageData->next_update != '0000-00-00 00:00:00' ? \OSOSuperCache\Factory::get('Cache\Tools')->formatTimestamp(strtotime($pageData->next_update)) : '-'; ?></td>
                            <td class="align-center">
                                <?php
                                if ($pageData->dont_cache) {
                                    ?>
                                    <span class="dashicons dashicons-no"></span>
                                    <?php
                                } else {
                                    ?>
                                    <span class="dashicons dashicons-yes"></span>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="9"
                            class="align-center"><?php _ex('No pages cached.', 'No result', 'oso-super-cache'); ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>

            <p>
                <span class="dashicons dashicons-yes"></span> <?php _ex('Page will be cached', 'Status', 'oso-super-cache'); ?>
                <br>
                <span class="dashicons dashicons-no"></span> <?php _ex('Page will not be cached', 'Status', 'oso-super-cache'); ?>
            </p>

            <?php
            if (!empty($pagesData['pages'])) {
                ?>
                <div class="action-bar">
                    <div class="actions">
                        <?php wp_nonce_field('oso_super_cache_view_cache_action'); ?>
                        <input type="hidden" name="formSend" value="1">
                        <select name="cachedPageAction">
                            <option value="-"><?php _ex('Bulk Actions', 'Select option', 'oso-super-cache'); ?></option>
                            <option value="swap-dont-cache"><?php _ex("Swap &quot;Cache storage&quot; status", 'Select option', 'oso-super-cache'); ?></option>
                            <option value="remove-from-cache"><?php _ex('Remove from cache', 'Select option', 'oso-super-cache'); ?></option>
                            <option value="mark-for-refresh"><?php _ex('Mark for refresh', 'Select option', 'oso-super-cache'); ?></option>
                        </select>
                        <input class="button" type="submit"
                               value="<?php _ex('Apply', 'Button title', 'oso-super-cache'); ?>">
                    </div>
                    <div class="paging">
                        <div class="tablenav">
                            <div class="tablenav-pages">
                                <span class="displaying-num"><?php printf(_nx('%s item', '%s items', $pagesData['totalItems'], 'Paging', 'oso-super-cache'), number_format_i18n($pagesData['totalItems'])); ?></span>
                                <span class="pagination-links">
                                <?php
                                if ($currentPage == 1 || $currentPage == 2) {
                                    ?>
                                    <span class="screen-reader-text"><?php _ex('First page', 'Paging', 'oso-super-cache'); ?></span>
                                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                                    <?php
                                } else {
                                    ?>
                                    <a class="first-page button"
                                       href="?page=oso-super-cache-view-cache&amp;paged=1<?php echo !empty($searchString) ? '&amp;search-page=' . rawurlencode($searchString) : ''; ?>">
                                        <span class="screen-reader-text"><?php _ex('First page', 'Paging', 'oso-super-cache'); ?></span>
                                        <span aria-hidden="true">«</span>
                                    </a>
                                    <?php
                                }
                                ?>

                                    <?php
                                    if ($currentPage < 2) {
                                        ?>
                                        <span class="screen-reader-text"><?php _ex('Previous page', 'Paging', 'oso-super-cache'); ?></span>
                                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                                        <?php
                                    } else {
                                        ?>
                                        <a class="prev-page button"
                                           href="?page=oso-super-cache-view-cache&amp;paged=<?php echo $currentPage - 1; ?><?php echo !empty($searchString) ? '&amp;search-page=' . rawurlencode($searchString) : ''; ?>">
                                        <span class="screen-reader-text"><?php _ex('Previous page', 'Paging', 'oso-super-cache'); ?></span>
                                        <span aria-hidden="true">‹</span>
                                    </a>
                                        <?php
                                    }
                                    ?>

                                    <span id="table-paging" class="paging-input">
                                    <span class="tablenav-paging-text"><?php printf(_x('%1$s of <span class="total-pages">%2$s</span>', 'Paging', 'oso-super-cache'), $currentPage, $pagesData['totalPages']); ?>
                                </span>

                                        <?php
                                        if ($currentPage == $pagesData['totalPages']) {
                                            ?>
                                            <span class="screen-reader-text"><?php _ex('Next page', 'Paging', 'oso-super-cache'); ?></span>
                                            <span class="tablenav-pages-navspan button disabled"
                                                  aria-hidden="true">›</span>
                                            <?php
                                        } else {
                                            ?>
                                            <a class="next-page button"
                                               href="?page=oso-super-cache-view-cache&amp;paged=<?php echo $currentPage + 1; ?><?php echo !empty($searchString) ? '&amp;search-page=' . rawurlencode($searchString) : ''; ?>">
                                        <span class="screen-reader-text"><?php _ex('Next page', 'Paging', 'oso-super-cache'); ?></span>
                                        <span aria-hidden="true">›</span>
                                    </a>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                        if ($currentPage == $pagesData['totalPages'] || $pagesData['totalPages'] - 1 == $currentPage) {
                                            ?>
                                            <span class="screen-reader-text"><?php _ex('Last page', 'Paging', 'oso-super-cache'); ?></span>
                                            <span class="tablenav-pages-navspan button disabled"
                                                  aria-hidden="true">»</span>
                                            <?php
                                        } else {
                                            ?>
                                            <a class="last-page button"
                                               href="?page=oso-super-cache-view-cache&amp;paged=<?php echo $pagesData['totalPages']; ?><?php echo !empty($searchString) ? '&amp;search-page=' . rawurlencode($searchString) : ''; ?>">
                                        <span class="screen-reader-text"><?php _ex('Last page', 'Paging', 'oso-super-cache'); ?></span>
                                        <span aria-hidden="true">»</span>
                                    </a>
                                            <?php
                                        }
                                        ?>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </form>
    </fieldset>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('input[type="checkbox"][name="checkAll"]').on('change', function () {

            var currentCheckbox = jQuery(this);

            jQuery(this).closest('table').find('input[type="checkbox"]').each(function () {
                jQuery(this).prop('checked', currentCheckbox.prop('checked'));
            });
        });
    });
</script>