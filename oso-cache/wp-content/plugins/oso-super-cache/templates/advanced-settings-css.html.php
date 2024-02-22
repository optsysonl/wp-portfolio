<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('CSS', 'AS CSS - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Optimization', 'AS CSS - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Merge', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesMerge">
                        <input<?php echo $checkboxStylesMerge; ?> type="checkbox" name="stylesMerge" id="stylesMerge" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Merges all local registered CSS files into a single file.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Exclude &lt;style&gt;-Tags', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesExcludeStyleTags">
                        <input<?php echo $checkboxStylesExcludeStyleTags; ?> type="checkbox" name="stylesExcludeStyleTags" id="stylesExcludeStyleTags" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('CSS from &lt;style&gt;-tags are not be merged.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Minify', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesMinify">
                        <input<?php echo $checkboxStylesMinify; ?> type="checkbox" name="stylesMinify" id="stylesMinify" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Minifies merged CSS.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Preload Tag', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesPreloadTag">
                        <input<?php echo $checkboxStylesPreloadTag; ?> type="checkbox" name="stylesPreloadTag" id="stylesPreloadTag" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Adds a &lt;link rel="preload"...&gt; tag in &lt;head&gt; for faster loading.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Compress', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesGzipOutput">
                        <input<?php echo $checkboxStylesGzipOutput; ?> type="checkbox" name="stylesGzipOutput" id="stylesGzipOutput" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Sends compressed CSS to the browser. Requires <strong>Merge</strong> to be activated.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description text-orange"><?php _ex('<strong>Note</strong>: We recommend not to activate this option. Instead, activate the option <strong>Modify .htaccess</strong> in <a href="?page=oso-super-cache-advanced-settings&tab=browser"><strong>Browser</strong></a>. It is more efficient than this option.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="stylesGzipCompressionLevel"><?php _ex('Compression level', 'Compression - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="stylesGzipCompressionLevel" id="stylesGzipCompressionLevel">
                        <option<?php echo $optionStylesGzipCompressionLevelMinimum; ?> value="minimal"><?php _ex('Minimal', 'Compression - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionStylesGzipCompressionLevelDefault; ?> value="default"><?php _ex('Balanced', 'Compression - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionStylesGzipCompressionLevelMaximum; ?> value="maximum"><?php _ex('Maximal', 'Compression - Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('<strong>Minimal</strong>: processing increased &asymp; 2.5-times, file size reduced &asymp; 4.2-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Balanced</strong>: processing increased &asymp; 5.5-times, file size reduced &asymp; 4.8-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Maximal</strong>: processing increased &asymp; 7.8-times, file size reduced &asymp; 4.9-times.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Processing a file before sending it to the browser takes x amount of time (we are talking about milliseconds). When the option <strong>Compress CSS</strong> is activated, this time is x-times longer, depending on the compression option. Given times are an example and will vary.', 'Compression - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Optimize Google Fonts', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesOptimizeGoogleFonts">
                        <input<?php echo $checkboxStylesOptimizeGoogleFonts; ?> type="checkbox" name="stylesOptimizeGoogleFonts" id="stylesOptimizeGoogleFonts" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex("Optimizes embedding of multiple fonts from Google.", 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_css'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

        <fieldset>

            <legend><?php _ex('Positioning', 'AS CSS - Headline of a fieldset', 'oso-super-cache'); ?></legend>
            <div class="form-group">
                <div class="form-title"><?php _ex('Style Location', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesPositionAfter">
                        <input<?php echo $radioStylesLocationHeader; ?> type="radio" name="stylesLocation" id="stylesLocationHeader" value="header">
                        <span class="option-title"><?php _ex('Header', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <label for="stylesPositionBefore">
                        <input<?php echo $radioStylesLocationFooter; ?> type="radio" name="stylesLocation" id="stylesLocationFooter" value="footer">
                        <span class="option-title"><?php _ex('Footer', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <span class="description"><?php _ex('<strong>After</strong>: The &lt;link&gt;-tag to the local CSS file is placed after the &lt;head&gt;-tag.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Before</strong>: The &lt;link&gt;-tag to the local CSS file is placed before the &lt;/head&gt;-tag.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('External Style Position', 'AS CSS - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="stylesExternalPositionAfter">
                        <input<?php echo $radioStylesExternalStylesAfter; ?> type="radio" name="stylesExternalStylesPosition" id="stylesExternalPositionBefore" value="after">
                        <span class="option-title"><?php _ex('After &lt;body&gt;', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <label for="stylesExternalPositionBefore">
                        <input<?php echo $radioStylesExternalStylesBefore; ?> type="radio" name="stylesExternalStylesPosition" id="stylesExternalPositionAfter" value="before">
                        <span class="option-title"><?php _ex('Before &lt;/body&gt;', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <span class="description"><?php _ex('<strong>After</strong>: The &lt;link&gt;-tag to the local CSS file is placed after the &lt;body&gt;-tag.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Before</strong>: The &lt;link&gt;-tag to the local CSS file is placed before the &lt;/body&gt;-tag.', 'AS CSS - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

    </form>
</div>