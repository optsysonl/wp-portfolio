<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('JavaScript', 'AS JavaScript - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Optimization', 'AS JavaScript - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Merge', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsMerge">
                        <input<?php echo $checkboxScriptsMerge; ?> type="checkbox" name="scriptsMerge" id="scriptsMerge" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Merges all local JavaScript files into a single file.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Smart Bundles BETA', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsSmartBundles">
                        <input<?php echo $checkboxScriptsSmartBundles; ?> type="checkbox" name="scriptsSmartBundles" id="scriptsSmartBundles" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Merges local JavaScript files into smart bundles. Increases number of JavaScript files but improves cacheability.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Minify', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsMinify">
                        <input<?php echo $checkboxScriptsMinify; ?> type="checkbox" name="scriptsMinify" id="scriptsMinify" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Minifies merged JavaScript.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Defer', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsDefer">
                        <input<?php echo $checkboxScriptsDefer; ?> type="checkbox" name="scriptsDefer" id="scriptsDefer" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Merged and external JavaScript is executed after the page has finished parsing. Does not work in Internet Explorer 10 or older.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Preload Tag', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsPreloadTag">
                        <input<?php echo $checkboxScriptsPreloadTag; ?> type="checkbox" name="scriptsPreloadTag" id="scriptsPreloadTag" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Adds &lt;link rel="preload"...&gt; tags in &lt;head&gt; for faster loading.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Fix JavaScript', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsFixSemicolon">
                        <input<?php echo $checkboxScriptsFixSemicolon; ?> type="checkbox" name="scriptsFixSemicolon" id="scriptsFixSemicolon" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Adds missing semicolons. When merging JavaScript, it may happen that the JavaScript will not be executed afterwards. This is often due to a missing semicolon in the JavaScript.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Compress', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsGZIPOutput">
                        <input<?php echo $checkboxScriptsGZIPOutput; ?> type="checkbox" name="scriptsGZIPOutput" id="scriptsGZIPOutput" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Sends compressed JavaScript to the browser. Requires <strong>Merge</strong> to be activated.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description text-orange"><?php _ex('<strong>Note</strong>: We recommend not to activate this option. Instead, activate the option <strong>Modify .htaccess</strong> in <a href="?page=oso-super-cache-advanced-settings&tab=browser"><strong>Browser</strong></a>. It is more efficient than this option.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="scriptsGzipCompressionLevel"><?php _ex('Compression level', 'Compression - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="scriptsGzipCompressionLevel" id="scriptsGzipCompressionLevel">
                        <option<?php echo $optionScriptsGzipCompressionLevelMinimum; ?> value="minimal"><?php _ex('Minimal', 'Compression - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionScriptsGzipCompressionLevelDefault; ?> value="default"><?php _ex('Balanced', 'Compression - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionScriptsGzipCompressionLevelMaximum; ?> value="maximum"><?php _ex('Maximal', 'Compression - Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('<strong>Minimal</strong>: processing increased &asymp; 2.5-times, file size reduced &asymp; 4.2-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Balanced</strong>: processing increased &asymp; 5.5-times, file size reduced &asymp; 4.8-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Maximal</strong>: processing increased &asymp; 7.8-times, file size reduced &asymp; 4.9-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Processing a file before sending it to the browser takes x amount of time (we are talking about milliseconds). When the option <strong>Compress JavaScript</strong> is activated, this time is x-times longer, depending on the compression option. Given times are an example and will vary.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <legend><?php _ex('Positioning', 'AS JavaScript - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Script Location', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsLocationHeader">
                        <input<?php echo $radioScriptsLocationHeader; ?> type="radio" name="scriptsLocation" id="scriptsLocationHeader" value="header">
                        <span class="option-title"><?php _ex('Header', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <label for="scriptsLocationFooter">
                        <input<?php echo $radioScriptsLocationFooter; ?> type="radio" name="scriptsLocation" id="scriptsLocationFooter" value="footer">
                        <span class="option-title"><?php _ex('Footer', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <span class="description"><?php _ex('<strong>Header</strong>: The JavaScript is placed in &lt;head&gt;.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Footer</strong>: The JavaScript is placed before &lt;/body&gt;.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('External Script Position', 'AS JavaScript - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="scriptsExternalScriptsBefore">
                        <input<?php echo $radioScriptsExternalScriptsBefore; ?> type="radio" name="scriptsExternalScriptsPosition" id="scriptsExternalScriptsBefore" value="before">
                        <span class="option-title"><?php _ex('Before', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <label for="scriptsExternalScriptsAfter">
                        <input<?php echo $radioScriptsExternalScriptsAfter; ?> type="radio" name="scriptsExternalScriptsPosition" id="scriptsExternalScriptsAfter" value="after">
                        <span class="option-title"><?php _ex('After', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <span class="description"><?php _ex('<strong>Before</strong>: The external JavaScript is placed before the local JavaScript (recommended).', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>After</strong>: The external JavaScript is placed after the local JavaScript.', 'AS JavaScript - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_javascript'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>
