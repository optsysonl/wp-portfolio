<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('Browser', 'AS Browser - Headline right of icon', 'osc-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <p><?php _ex('The configured header settings are applied to JavaScript and CSS files if they have merging and compression activated.', 'AS Browser - Setting page description', 'oso-super-cache'); ?></p>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Header', 'AS Browser - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Cache-Control header', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserCacheSetControlHeader">
                        <input<?php echo $checkboxBrowserCacheSetControlHeader; ?> type="checkbox" name="browserCacheSetControlHeader" id="browserCacheSetControlHeader" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('The configured <strong>Cache-Control policy</strong> header is sent to the browser.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="browserCacheControlPolicy"><?php _ex('Cache-Control policy', 'AS Browser - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="browserCacheControlPolicy" id="browserCacheControlPolicy">
                        <option<?php echo $optionBrowserCacheControlPolicyPublic; ?> value="public"><?php _ex('public', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserCacheControlPolicyPrivate; ?> value="private"><?php _ex('private', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserCacheControlPolicyPublicMaxAge; ?> value="public-max-age"><?php _ex('public-max-age', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserCacheControlPolicyPrivateMaxAge; ?> value="private-max-age"><?php _ex('private-max-age', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserCacheControlPolicyNoCache; ?> value="no-cache"><?php _ex('no-cache', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('Sends <strong>Cache-Control</strong> with your configured policy.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="browserCacheControlHeaderExpiresLifetime"><?php _ex('Max-Age / Max Lifetime', 'AS Browser - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputBrowserCacheControlHeaderExpiresLifetime; ?>" type="text" name="browserCacheControlHeaderExpiresLifetime" id="browserCacheControlHeaderExpiresLifetime" class="regular-text">
                    <span class="description"><?php _ex('Max lifetime in seconds before the browser requests the file again.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Examples:', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Hour</strong>: 3600', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>12 Hours</strong>: 43200', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Day</strong>: 86400', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Month</strong>: 2592000', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>1 Year</strong>: 31536000', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Last-Modified', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserCacheSetLastModified">
                        <input<?php echo $checkboxBrowserCacheSetLastModified; ?> type="checkbox" name="browserCacheSetLastModified" id="browserCacheSetLastModified" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Sends <strong>Last-Modified</strong> header.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('ETag', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserCacheSetETag">
                        <input<?php echo $checkboxBrowserCacheSetETag; ?> type="checkbox" name="browserCacheSetETag" id="browserCacheSetETag" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Sends <strong>ETag</strong> header.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('OSOSuperCache Cache Tag', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserCacheSetOSOSuperCacheCacheTag">
                        <input<?php echo $checkboxBrowserCacheSetOSOSuperCacheCacheTag; ?> type="checkbox" name="browserCacheSetOSOSuperCacheCacheTag" id="browserCacheSetOSOSuperCacheCacheTag" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Sends <strong>X-Powered-By: oso-super-cache</strong> header in order to identify which files are delivered by OSO Super Cache.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Headers on dynamic content', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserCacheHeaderManagementOnPages">
                        <input<?php echo $checkboxBrowserCacheHeaderManagementOnPages; ?> type="checkbox" name="browserCacheHeaderManagementOnPages" id="browserCacheHeaderManagementOnPages" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('All configured header settings are applied to pages as well.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Notice: <strong>Max-Age / Max Lifetime</strong> value will be ignored for dynamic content; instead the configured lifetime from <strong>Cache Lifetimes</strong> will be used.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Modify .htaccess', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserCacheModifyHtaccess">
                        <input<?php echo $checkboxBrowserCacheModifyHtaccess; ?> type="checkbox" name="browserCacheModifyHtaccess" id="browserCacheModifyHtaccess" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Adds cache settings like lifetime and compression for static files into the .htaccess file. Does not work multisite independent. ', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('X-Cache', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserCacheDisableXCacheHeaders">
                        <input<?php echo $checkboxBrowserCacheDisableXCacheHeaders; ?> type="checkbox" name="browserCacheDisableXCacheHeaders" id="browserCacheDisableXCacheHeaders" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Enable this option to disable X-Cache.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <legend><?php _ex('DNS-Prefetching', 'Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('DNS-Prefetching', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="dnsPrefetch">
                        <input<?php echo $checkboxDNSPrefetch; ?> type="checkbox" name="dnsPrefetch" id="dnsPrefetch" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('<strong>X-DNS-Prefetch-Control: on</strong> header will be sent and <strong>&lt;link rel=&quot;dns-prefetch&quot; ...&gt;</strong> will be placed in <strong>&lt;head&gt;</strong>.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('Requires <strong>Headers on dynamic content</strong> as well as JavaScript/CSS merging to be activated.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

        </fieldset>

        <fieldset>

            <legend><?php _ex('Security Header', 'AS Browser - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Enable security header', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserSecurityHeader">
                        <input<?php echo $checkboxBrowserSecurityHeader; ?> type="checkbox" name="browserSecurityHeader" id="browserSecurityHeader" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Adds security header settings into the .htaccess. Does not work multisite independent. ', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Content-Security-Policy header', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserSecurityContentSecurityPolicyHeader">
                        <input<?php echo $checkboxBrowserSecurityContentSecurityPolicyHeader; ?> type="checkbox" name="browserSecurityContentSecurityPolicyHeader" id="browserSecurityContentSecurityPolicyHeader" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('The configured <strong>Content-Security-Policy</strong> is sent to the browser.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="browserSecurityContentSecurityPolicy"><?php _ex("Content-Security-Policy configuration", 'AS Browser - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <textarea name="browserSecurityContentSecurityPolicy" id="browserSecurityContentSecurityPolicy" cols="80" rows="5"><?php echo esc_textarea($textareaBrowserSecurityContentSecurityPolicy); ?></textarea>
                    <span class="description"><?php _ex('Notice: This option is for experienced users. Making a mistake here can break your site. If your site breaks after you activated this option, open your <strong>.htaccess</strong> and remove everything between <strong># BEGIN Security OSO Super Cache</strong> and <strong># END Security OSO Super Cache</strong>.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="browserSecurityReferrerPolicy"><?php _ex('Referrer-Policy', 'AS Browser - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="browserSecurityReferrerPolicy" id="browserSecurityReferrerPolicy">
                        <option<?php echo $optionBrowserSecurityReferrerPolicyNoPolicy; ?> value="-"><?php _ex('no Referrer-Policy', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicyNoReferrer; ?> value="no-referrer"><?php _ex('no-referrer', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicyNoReferrerWD; ?> value="no-referrer-when-downgrade"><?php _ex('no-referrer-when-downgrade', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicySameOrigin; ?> value="same-origin"><?php _ex('same-origin', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicyOrigin; ?> value="origin"><?php _ex('origin', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicyStrictOrigin; ?> value="strict-origin"><?php _ex('strict-origin', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicyOriginWCO; ?> value="origin-when-cross-origin"><?php _ex('origin-when-cross-origin', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicyStrictOriginWCO; ?> value="strict-origin-when-cross-origin"><?php _ex('strict-origin-when-cross-origin', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionBrowserSecurityReferrerPolicyUnsafeURL; ?> value="unsafe-url"><?php _ex('unsafe-url', 'AS Browser - Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('This header determines the circumstances in which a referrer is sent.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Strict-Transport-Security', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserSecurityStrictTransportSecurity">
                        <input<?php echo $checkboxBrowserSecurityStrictTransportSecurity; ?> type="checkbox" name="browserSecurityStrictTransportSecurity" id="browserSecurityStrictTransportSecurity" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Ensures that all your traffic is sent through HTTPS.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('X-Frame-Options', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserSecurityXFrameOptionsDisabled">
                        <input<?php echo $radioBrowserSecurityXFrameOptionsDisabled; ?> type="radio" name="browserSecurityXFrameOptions" id="browserSecurityXFrameOptionsDisabled" value="disabled">
                        <span class="option-title"><?php _ex('Do not send X-Frame-Options', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                    </label>

                    <label for="browserSecurityXFrameOptionsDeny">
                        <input<?php echo $radioBrowserSecurityXFrameOptionsDeny; ?> type="radio" name="browserSecurityXFrameOptions" id="browserSecurityXFrameOptionsDeny" value="deny">
                        <span class="option-title">DENY</span>
                    </label>

                    <label for="browserSecurityXFrameOptionsSameOrigin">
                        <input<?php echo $radioBrowserSecurityXFrameOptionsSameOrigin; ?> type="radio" name="browserSecurityXFrameOptions" id="browserSecurityXFrameOptionsSameOrigin" value="sameorigin">
                        <span class="option-title">SAMEORIGIN</span>
                    </label>

                    <span class="description"><?php _ex('Disallow others to embed your site using &lt;frame&gt;, &lt;iframe&gt;, or &lt;object&gt;.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('X-Content-Type-Options', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserSecurityXContentTypeOptions">
                        <input<?php echo $checkboxBrowserSecurityXContentTypeOptions; ?> type="checkbox" name="browserSecurityXContentTypeOptions" id="browserSecurityXContentTypeOptions" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Disable MIME type sniffing.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('X-XSS-Protection', 'AS Browser - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="browserSecurityXXSSProtection">
                        <input<?php echo $checkboxBrowserSecurityXXSSProtection; ?> type="checkbox" name="browserSecurityXXSSProtection" id="browserSecurityXXSSProtection" value="nosniff"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('This header improves the security of your site against some types of XSS (Cross-Site-Scripting) attacks.', 'AS Browser - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_browser'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>