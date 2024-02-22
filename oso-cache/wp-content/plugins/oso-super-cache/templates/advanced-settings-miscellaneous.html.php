<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('Miscellaneous', 'AS Miscellaneous - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('WordPress', 'AS Miscellaneous - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Emojis', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableEmojis">
                        <input<?php echo $checkboxMiscellaneousDisableEmojis; ?> type="checkbox" name="miscellaneousDisableEmojis" id="miscellaneousDisableEmojis" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the loading of the emoji JavaScript.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Feeds', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableFeeds">
                        <input<?php echo $checkboxMiscellaneousDisableFeeds; ?> type="checkbox" name="miscellaneousDisableFeeds" id="miscellaneousDisableFeeds" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;link rel=&quot;alternate&quot; ... href=&quot;.../feed/&quot; ...&gt; in &lt;head&gt; for the feed and comment feed.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Manifest', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableManifest">
                        <input<?php echo $checkboxMiscellaneousDisableManifest; ?> type="checkbox" name="miscellaneousDisableManifest" id="miscellaneousDisableManifest" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;link rel=&quot;wlwmanifest&quot; ...&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Meta-Generator', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableGenerator">
                        <input<?php echo $checkboxMiscellaneousDisableGenerator; ?> type="checkbox" name="miscellaneousDisableGenerator" id="miscellaneousDisableGenerator" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;meta name=&quot;generator&quot; content=&quot;WordPress ...&quot;&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('oEmbed', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableOEmbed">
                        <input<?php echo $checkboxMiscellaneousDisableOEmbed; ?> type="checkbox" name="miscellaneousDisableOEmbed" id="miscellaneousDisableOEmbed" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;link rel=&quot;application/json+oembed&quot; href=&quot;...&quot;&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('REST API', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableRESTAPI">
                        <input<?php echo $checkboxMiscellaneousDisableRESTAPI; ?> type="checkbox" name="miscellaneousDisableRESTAPI" id="miscellaneousDisableRESTAPI" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;link rel=&quot;https://api.w.org/&quot; href=&quot;...&quot;&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('RSD', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableRSD">
                        <input<?php echo $checkboxMiscellaneousDisableRSD; ?> type="checkbox" name="miscellaneousDisableRSD" id="miscellaneousDisableRSD" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;link rel=&quot;EditURI&quot; type=&quot;application/rsd+xml&quot; ...&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_miscellaneous'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

        <fieldset>

            <legend><?php _ex('Third-Party Plugins', 'AS Miscellaneous - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('LayerSlider Generator', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableTPPLayerSliderGenerator">
                        <input<?php echo $checkboxMiscellaneousDisableTPPLayerSliderGenerator; ?> type="checkbox" name="miscellaneousDisableTPPLayerSliderGenerator" id="miscellaneousDisableTPPLayerSliderGenerator" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;meta name=&quot;generator&quot; content=&quot;Powered by LayerSlider ...&quot;&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Slider Revolution Generator', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableTPPSliderRevolutionGenerator">
                        <input<?php echo $checkboxMiscellaneousDisableTPPSliderRevolutionGenerator; ?> type="checkbox" name="miscellaneousDisableTPPSliderRevolutionGenerator" id="miscellaneousDisableTPPSliderRevolutionGenerator" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;meta name=&quot;generator&quot; content=&quot;Powered by Slider Revolution ...&quot;&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Visual Composer Generator', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableTPPVisualComposerGenerator">
                        <input<?php echo $checkboxMiscellaneousDisableTPPVisualComposerGenerator; ?> type="checkbox" name="miscellaneousDisableTPPVisualComposerGenerator" id="miscellaneousDisableTPPVisualComposerGenerator" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the &lt;meta name=&quot;generator&quot; content=&quot;Powered by Visual Composer ...&quot;&gt; in &lt;head&gt;.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

        <fieldset>

            <legend><?php _ex('OSO Super Cache', 'AS Miscellaneous - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Refresh Cache Notice', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableOSOSuperCacheRefreshCacheNotice">
                        <input<?php echo $checkboxMiscellaneousDisableOSOSuperCacheRefreshCacheNotice; ?> type="checkbox" name="miscellaneousDisableOSOSuperCacheRefreshCacheNotice" id="miscellaneousDisableOSOSuperCacheRefreshCacheNotice" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the notification to refresh the cache after the status of a plugin or theme has changed.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Disable toolbar menu item', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousDisableOSOSuperCacheToolbarMenuItem">
                        <input<?php echo $checkboxMiscellaneousDisableOSOSuperCacheToolbarMenuItem; ?> type="checkbox" name="miscellaneousDisableOSOSuperCacheToolbarMenuItem" id="miscellaneousDisableOSOSuperCacheToolbarMenuItem" value="1"> <span class="option-title"><?php _ex('Disable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disables the menu item of OSO Super Cache in the admin toolbar.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('NGINX Server', 'AS Miscellaneous - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="miscellaneousNginx">
                        <input<?php echo $checkboxMiscellaneousNginx; ?> type="checkbox" name="miscellaneousNginx" id="miscellaneousNginx" value="1"> <span class="option-title"><?php _ex('Enable', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Enable this option when your server is running NGINX to disable .htaccess message.', 'AS Miscellaneous - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>