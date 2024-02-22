<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('Cache Exceptions', 'AS Cache Exceptions - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex("Don't cache settings", 'AS Cache Exceptions - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <p><?php _ex('Exceptions here cause a page not to be cached.', 'AS Cache Exceptions - Fieldset description', 'oso-super-cache'); ?></p>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheDontCachePagesContainQuery"><?php _ex("Don't cache pages with query string", 'AS Cache Exceptions - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <textarea name="cacheDontCachePagesContainQuery" id="cacheDontCachePagesContainQuery" cols="80" rows="5"><?php echo esc_textarea($textareaCacheDontCachePagesContainQuery); ?></textarea>
                    <span class="description"><?php _ex('One query variable per line. If you need to exclude a query variable that is a key of an array, you have to use JSON.', 'AS Cache Exceptions - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheDontCachePagesContainPath"><?php _ex("Don't cache the following pages", 'AS Cache Exceptions - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <textarea name="cacheDontCachePagesContainPath" id="cacheDontCachePagesContainPath" cols="80" rows="5"><?php echo esc_textarea($textareaCacheDontCachePagesContainPath); ?></textarea>
                    <span class="description"><?php _ex('One path per line. You can use regular expressions.', 'AS Cache Exceptions - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex("Don't cache these post types", 'AS Cache Exceptions - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <?php
                        foreach ($postTypes as $postTypeData) {
                            $postTypeName = esc_html($postTypeData->name);
                        ?>
                        <label for="cacheDontCachePagesOfPostType-<?php echo $postTypeName; ?>">
                            <input<?php echo $checkboxCacheDontCachePagesOfPostType[$postTypeName]; ?> type="checkbox" name="cacheDontCachePagesOfPostType[<?php echo $postTypeName; ?>]" id="cacheDontCachePagesOfPostType-<?php echo $postTypeName; ?>" value="1">
                            <span class="option-title"><?php echo esc_html($postTypeData->label).' <em>'.$postTypeName.'</em>'; ?></span>
                        </label>
                        <?php
                        }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex("Don't cache these taxonomies", 'AS Cache Exceptions - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <?php
                    foreach ($taxonomies as $taxonomyData) {
                        $taxonomyName = esc_html($taxonomyData->name);
                    ?>
                    <label for="cacheDontCachePagesOfTaxonomy-<?php echo $taxonomyName; ?>">
                        <input<?php echo $checkboxCacheDontCachePagesOfTaxonomy[$taxonomyName]; ?> type="checkbox" name="cacheDontCachePagesOfTaxonomy[<?php echo $taxonomyName; ?>]" id="cacheDontCachePagesOfTaxonomy-<?php echo $taxonomyName; ?>" value="1">
                        <span class="option-title"><?php echo esc_html($taxonomyData->label).' <em>'.$taxonomyName.'</em>'; ?></span>
                    </label>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_cache_exceptions'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

        <fieldset>

            <legend><?php _ex("Don't use cache settings", 'AS Cache Exceptions - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <p><?php _ex('Exceptions here cause the visitor to receive an uncached page.', 'AS Cache Exceptions - Fieldset description', 'oso-super-cache'); ?></p>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheDontUseCacheWhenUserAgent"><?php _ex("User Agent", 'AS Cache Exceptions - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <textarea name="cacheDontUseCacheWhenUserAgent" id="cacheDontUseCacheWhenUserAgent" cols="80" rows="5"><?php echo esc_textarea($textareaCacheDontUseCacheWhenUserAgent); ?></textarea>
                    <span class="description"><?php _ex("One user agent per line. If the user agent matches the visitor’s user agent, the visitor does not receive a cached page.", 'AS Cache Exceptions - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

             <div class="form-group">
                <div class="form-title">
                    <label for="cacheDontUseCacheWhenCookie"><?php _ex("Cookie", 'AS Cache Exceptions - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <textarea name="cacheDontUseCacheWhenCookie" id="cacheDontUseCacheWhenCookie" cols="80" rows="5"><?php echo esc_textarea($textareaCacheDontUseCacheWhenCookie); ?></textarea>
                    <span class="description"><?php _ex("One cookie name per line. If the visitor’s cookie matches, the visitor does not receive a cached page.", 'AS Cache Exceptions - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>
