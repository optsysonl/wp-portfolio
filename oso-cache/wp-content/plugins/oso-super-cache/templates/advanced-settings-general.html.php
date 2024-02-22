<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('General', 'AS General - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Basic Settings', 'AS General - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Activate OSO Super Cache', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheActivated">
                        <input<?php echo $checkboxCacheActivated; ?> type="checkbox" name="cacheActivated" id="cacheActivated" value="yes"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?> <i class="dashicons dashicons-lightbulb <?php echo !empty($checkboxCacheActivated) ? 'text-green' : 'text-red'; ?>"></i></span>
                    </label>
                    <span class="description"><?php _ex('When activated, OSO Super Cache starts caching your website.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('If deactivated, the site behaves as before.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheLateInitialization"><?php _ex('Late Initialization', 'AS General - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="cacheLateInitialization" id="cacheLateInitialization">
                        <option<?php echo $optionCacheLateInitializationFalse; ?> value="no"><?php _ex('No Late Initialization', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheLateInitializationLate; ?> value="late"><?php _ex('Late', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheLateInitializationSuperLate; ?> value="super-late"><?php _ex('Super Late', 'Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('<strong>No Late Initialization</strong>: Recommended for maximum performance. Uses <strong>plugins_loaded</strong> for initialization.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Late</strong>: Less performance boost but more compatibility. Uses <strong>wp_loaded</strong> for initialization.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Super Late</strong>: Much less performance boost, but maximum compatibility. When you have to activate this option, we recommend you that to optimize your scripts and plugins. Uses <strong>wp</strong> for initialization.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="maxSimultaneousTasks"><?php _ex('Max simultaneous tasks', 'AS General - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputMaxSimultaneousTasks; ?>" type="text" name="maxSimultaneousTasks" id="maxSimultaneousTasks" class="regular-text">
                    <span class="description"><?php _ex('Maximum simultaneous tasks that can run to cache and optimize an uncached page.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <legend><?php _ex('Cache Settings', 'AS General - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheSeparateFileByDeviceType"><?php _ex('Separate cache files', 'AS General - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="cacheSeparateFileByDeviceType" id="cacheSeparateFileByDeviceType">
                        <option<?php echo $optionCacheSeparateFileByDeviceTypeDisabled; ?> value="no"><?php _ex('Disabled', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheSeparateFileByDeviceTypeMobile; ?> value="mobile"><?php _ex('Mobile', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheSeparateFileByDeviceTypeMobileTablet; ?> value="mobile+tablet"><?php _ex('Mobile & Tablet', 'Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('<strong>Disabled</strong>: No separate cache file for mobile devices. Recommended option.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Mobile</strong>: OSO Super Cache creates a separate cache file for mobile devices (smartphones).', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Mobile &amp; Tablet</strong>: OSO Super Cache creates two separate cache files for mobile devices (smartphones) and tablets.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Use this option only if you do not have a responsive design and mobile devices receive a different design.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description text-orange"><?php _ex('<strong>Note</strong>: We do not recommend this option due double/triple amount of cache files. You should switch to a responsive design as soon as possible.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Logged-in Users', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="loggedInUserGetCachedPages">
                        <input<?php echo $checkboxLoggedInUserGetCachedPages; ?> type="checkbox" name="loggedInUserGetCachedPages" id="loggedInUserGetCachedPages" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Logged-in users receive cached pages.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Search results', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheSearchResults">
                        <input<?php echo $checkboxCacheSearchResults; ?> type="checkbox" name="cacheSearchResults" id="cacheSearchResults" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Search requests and results are cached.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Pages with query strings', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cachePagesWithQueryStrings">
                        <input<?php echo $checkboxCachePagesWithQueryStrings; ?> type="checkbox" name="cachePagesWithQueryStrings" id="cachePagesWithQueryStrings" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Pages with query strings (e.g. ?orderSomething=DESC) are cached. The query var must be registered in <strong>query_vars</strong> respectively <strong>$wp_query</strong>.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Feeds', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheFeeds">
                        <input<?php echo $checkboxCacheFeeds; ?> type="checkbox" name="cacheFeeds" id="cacheFeeds" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('All feeds will be cached.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('404 (not found)', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cache404Pages">
                        <input<?php echo $checkboxCache404Pages; ?> type="checkbox" name="cache404Pages" id="cache404Pages" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('All requests to non-existent pages receive a single cached 404 page.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Compress pages', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheGzipOutput">
                        <input<?php echo $checkboxCacheGzipOutput; ?> type="checkbox" name="cacheGzipOutput" id="cacheGzipOutput" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Send compressed pages to the browser.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheGzipCompressionLevel"><?php _ex('Compression level', 'Compression - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="cacheGzipCompressionLevel" id="cacheGzipCompressionLevel">
                        <option<?php echo $optionCacheGzipCompressionLevelMinimum; ?> value="minimal"><?php _ex('Minimal', 'Compression - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheGzipCompressionLevelDefault; ?> value="default"><?php _ex('Balanced', 'Compression - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheGzipCompressionLevelMaximum; ?> value="maximum"><?php _ex('Maximal', 'Compression - Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('<strong>Minimal</strong>: processing increased &asymp; 2.5-times, file size reduced &asymp; 4.2-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Balanced</strong>: processing increased &asymp; 5.5-times, file size reduced &asymp; 4.8-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Maximal</strong>: processing increased &asymp; 7.8-times, file size reduced &asymp; 4.9-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Processing a file before sending it to the browser takes x amount of time (we are talking about milliseconds). When the option <strong>Compress pages</strong> is activated, this time is x-times longer, depending on the compression option. Given times are an example and will vary.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>
            <?php wp_nonce_field('oso_super_cache_advanced_settings_general'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

        <fieldset>

            <legend><?php _ex('Cache Preloader Settings', 'AS General - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Cache Preloader', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="preloaderActivated">
                        <input<?php echo $checkboxPreloaderActivated; ?> type="checkbox" name="preloaderActivated" id="preloaderActivated" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?> <i class="dashicons dashicons-lightbulb <?php echo !empty($checkboxPreloaderActivated) ? 'text-green' : 'text-red'; ?>"></i></span>
                    </label>
                    <span class="description"><?php _ex('Activates the Cache Preloader which preloads your pages as soon as their lifetime is over.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

        <fieldset>

            <legend><?php _ex('Cron Settings', 'AS General - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Cron Service', 'AS General - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheCronService">
                        <input<?php echo $checkboxCacheCronService; ?> type="checkbox" name="cacheCronService" id="cacheCronService" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Activates OSO Super Cache Cron Service.', 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cacheCronInterval"><?php _ex('Cron Runtimes', 'AS General - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="cacheCronInterval" id="cacheCronInterval">
                        <option<?php echo $optionCacheCronInterval5Minutes; ?> value="5"><?php _ex('Every 5 Minutes', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheCronInterval10Minutes; ?> value="10"><?php _ex('Every 10 Minutes', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheCronInterval15Minutes; ?> value="15"><?php _ex('Every 15 Minutes', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCacheCronInterval30Minutes; ?> value="30"><?php _ex('Every 30 Minutes', 'Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex("With OSO Super Cache activated, you need a cronjob service to run WordPress wp-cron.php. If you already use a cronjob service or have setup a cronjob, you don't need to activate our cron service.", 'AS General - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>
