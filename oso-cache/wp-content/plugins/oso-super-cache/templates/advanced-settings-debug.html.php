<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('Debug', 'AS Debug - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Debug', 'AS Debug - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Add Cache Information', 'AS Debug - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="debugAddCacheInformation">
                        <input<?php echo $checkboxDebugAddCacheInformation; ?> type="checkbox" name="debugAddCacheInformation" id="debugAddCacheInformation" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Adds an HTML-comment at the bottom of the page with information about the cached page.', 'AS Debug - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_debug'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>

    <h4><?php _ex('Debug-Log', 'AS Debug - Headline', 'oso-super-cache'); ?></h4>
    <p><?php _ex('Place this code in your wp-config.php to log when OSO Super Cache is merging the JavaScript and CSS.', 'AS Debug - Setting description', 'oso-super-cache'); ?></p>
    <code>
        define('OSO_SUPER_CACHE_DEBUG', true);
        <br>
        define('OSO_SUPER_CACHE_DEBUG_WRITE_TO_FILE', dirname(__FILE__) . '/oso-super-cache.log');
    </code>

</div>