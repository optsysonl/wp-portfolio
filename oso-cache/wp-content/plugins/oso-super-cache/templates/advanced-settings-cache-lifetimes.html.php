<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('Cache Lifetimes', 'AS Cache Lifetimes - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Refresh Cache Settings', 'AS Cache Lifetimes - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Refresh when published', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheRefreshCacheAfterPublish">
                        <input<?php echo $checkboxCacheRefreshCacheAfterPublish; ?> type="checkbox" name="cacheRefreshCacheAfterPublish" id="cacheRefreshCacheAfterPublish" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex("Automatically refreshes the cache of the post when it's published.", 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex("This will increase the time to save a post, but the post is immediately cached.", 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Refresh homepage when post published', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheRefreshHomeCacheAfterPublish">
                        <input<?php echo $checkboxCacheRefreshHomeCacheAfterPublish; ?> type="checkbox" name="cacheRefreshHomeCacheAfterPublish" id="cacheRefreshHomeCacheAfterPublish" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex("Automatically refreshes the cache of the homepage when a post is published.", 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Refresh corresponding archives when the post is published', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheRefreshArchiveCacheAfterPublish">
                        <input<?php echo $checkboxCacheRefreshArchiveCacheAfterPublish; ?> type="checkbox" name="cacheRefreshArchiveCacheAfterPublish" id="cacheRefreshArchiveCacheAfterPublish" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex("Automatically refreshes the cache of the archives when a post is published. Only archives of the same post type as the post will be refreshed.", 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Refresh feeds when post published', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheRefreshFeedCacheAfterPublish">
                        <input<?php echo $checkboxCacheRefreshFeedCacheAfterPublish; ?> type="checkbox" name="cacheRefreshFeedCacheAfterPublish" id="cacheRefreshFeedCacheAfterPublish" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Refreshes the cache of all feeds when a post is published.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Refresh after comment', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheRefreshCacheAfterComment">
                        <input<?php echo $checkboxCacheRefreshCacheAfterComment; ?> type="checkbox" name="cacheRefreshCacheAfterComment" id="cacheRefreshCacheAfterComment" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Refreshes the cache of the page on which the comment was entered.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Show Meta Box', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheShowMetaBox">
                        <input<?php echo $checkboxCacheShowMetaBox; ?> type="checkbox" name="cacheShowMetaBox" id="cacheShowMetaBox" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Shows meta box with options for cache refresh and cache exclusion.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Show Refresh Option in QuickEdit', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheShowRefreshOptionInQuickEdit">
                        <input<?php echo $checkboxCacheShowRefreshOptionInQuickEdit; ?> type="checkbox" name="cacheShowRefreshOptionInQuickEdit" id="cacheShowRefreshOptionInQuickEdit" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Shows the option to refresh the cache in the QuickEdit.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <legend><?php _ex('Default Lifetime Settings', 'AS Cache Lifetimes - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheLifetimeHome"><?php _ex('Home', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputCacheLifetimeHome; ?>" type="text" name="cacheLifetimeHome" id="cacheLifetimeHome" class="regular-text">
                    <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Cache refreshes the cached page.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Examples:', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Hour</strong>: 3600', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>12 Hours</strong>: 43200', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Day</strong>: 86400', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Month</strong>: 2592000', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Year</strong>: 31536000', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheLifetimeArchives"><?php _ex('Archives', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputCacheLifetimeArchives['-']; ?>" type="text" name="cacheLifetimeArchives[-]" id="cacheLifetimeArchives" class="regular-text">
                    <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Cache refreshes the cached archive page. Archives are pages which lists many posts.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheLifetimePostType"><?php _ex('Posts', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputCacheLifetimePostType['-']; ?>" type="text" name="cacheLifetimePostType[-]" id="cacheLifetimePostType" class="regular-text">
                    <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Cache refreshes the cached post. A post is a single page.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheLifetimeFeed"><?php _ex('Feeds', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputCacheLifetimeFeed; ?>" type="text" name="cacheLifetimeFeed" id="cacheLifetimeFeed" class="regular-text">
                    <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Cache refreshes the cached feed.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheLifetime404"><?php _ex('404 (not found)', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputCacheLifetime404; ?>" type="text" name="cacheLifetime404" id="cacheLifetime404" class="regular-text">
                    <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Cache removes a 404 page from the cache index.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheLifetimeGarbage"><?php _ex('Garbage Collector', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputCacheLifetimeGarbage; ?>" type="text" name="cacheLifetimeGarbage" id="cacheLifetimeGarbage" class="regular-text">
                    <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Caches deletes a cached page.', 'Garbage Collector - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('This only happens to cached pages that have been deleted in WordPress but still exist in OSO Super Cache. The garbage collector deletes these files after the given lifetime.', 'Garbage Collector - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('This lifetime should be much higher than the other lifetimes.', 'Garbage Collector - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>
            <?php wp_nonce_field('oso_super_cache_advanced_settings_cache_lifetimes'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save all settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>


            <fieldset>
                <legend><?php _ex('Lifetime Settings', 'AS Cache Lifetimes - Headline of a fieldset', 'oso-super-cache'); ?></legend>
                <p><?php _ex("You can set different lifetimes for each post type. Leave the field empty or enter <strong>0</strong> if you want to use the default settings.", 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></p>
                <p><?php _ex("If you don't want to cache a specific post type, you can exclude this post type under <strong>Cache Exceptions</strong>.", 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></p>
            </fieldset>

            <?php
            $lastPostType = end($postTypes);
            reset($postTypes);

            foreach ($postTypes as $postTypeData) {
                $postTypeName = esc_html($postTypeData->name);
            ?>
            <fieldset>

                <legend><?php echo $postTypeData->label; ?></legend>

                <div class="form-group">
                    <div class="form-title">
                        <label for="cacheLifetimeArchives-<?php echo $postTypeName; ?>"><?php _ex('Archives', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                    </div>
                    <div class="form-field">
                        <input value="<?php echo $inputCacheLifetimeArchives[$postTypeName]; ?>" type="text" name="cacheLifetimeArchives[<?php echo $postTypeName; ?>]" id="cacheLifetimeArchives-<?php echo $postTypeName; ?>" class="regular-text">
                        <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Cache refreshes the cached archive page. Archives are pages which lists many posts.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-title">
                        <label for="cacheLifetimePostType-<?php echo $postTypeName; ?>"><?php _ex('Posts', 'AS Cache Lifetimes - Setting title', 'oso-super-cache'); ?></label>
                    </div>
                    <div class="form-field">
                        <input value="<?php echo $inputCacheLifetimePostType[$postTypeName]; ?>" type="text" name="cacheLifetimePostType[<?php echo $postTypeName; ?>]" id="cacheLifetimePostType-<?php echo $postTypeName; ?>" class="regular-text">
                        <span class="description"><?php _ex('Max lifetime in seconds before OSO Super Cache refreshes the cached post. A post is a single page.', 'AS Cache Lifetimes - Setting description', 'oso-super-cache'); ?></span>
                    </div>
                </div>
                <?php
                if ($postTypeData->name == $lastPostType->name) {
                ?>
                <hr>
                <input class="button-primary" type="submit" value="<?php _ex('Save all settings', 'Button title', 'oso-super-cache'); ?>">
                <?php
                }
                ?>

            </fieldset>
        <?php
            }
        ?>
    </form>
</div>