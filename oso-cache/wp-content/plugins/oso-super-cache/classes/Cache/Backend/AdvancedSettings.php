<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class AdvancedSettings
{
    private static $instance;

    private $imagePath;
    private $saveSuccessful = 0;

    public static function getInstance ()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    protected function __construct()
    {
        // Load inactive config
        Factory::get('Cache\Config')->loadConfig('inactive');
    }

    /**
     * display function.
     *
     * @access public
     * @return void
     */
    public function display()
    {
        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        $tab = !empty($_GET['tab']) ? $_GET['tab'] : 'general';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings.html.php';

        if ($tab == 'general') {
            $this->displayTabGeneral();
        } elseif ($tab == 'html') {
            $this->displayTabHTML();
        } elseif ($tab == 'image') {
            $this->displayTabImage();
        } elseif ($tab == 'javascript') {
            $this->displayTabJavaScript();
        } elseif ($tab == 'css') {
            $this->displayTabCSS();
        } elseif ($tab == 'page-optimization') {
            $this->displayPageOptimization();
        } elseif ($tab == 'cache-lifetimes') {
            $this->displayTabCacheLifetimes();
        } elseif ($tab == 'cache-exceptions') {
            $this->displayTabCacheExceptions();
        } elseif ($tab == 'browser') {
            $this->displayTabBrowser();
        } elseif ($tab == 'miscellaneous') {
            $this->displayTabMiscellaneous();
        } elseif ($tab == 'debug') {
            $this->displayTabDebug();
        } else {
            $this->displayTabGeneral();
        }
    }

    /**
     * displayTabGeneral function.
     *
     * @access public
     * @return void
     */
    public function displayTabGeneral()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_general')) {
            $this->saveGeneral($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxCacheActivated                 = Factory::get('Cache\Config')->get('cacheActivated', 'inactive') == 'yes' ? ' checked' : '';
        $optionCacheLateInitializationFalse     = Factory::get('Cache\Config')->get('cacheLateInitialization', 'inactive') === false ? ' selected' : '';
        $optionCacheLateInitializationLate      = Factory::get('Cache\Config')->get('cacheLateInitialization', 'inactive') === 'late' ? ' selected' : '';
        $optionCacheLateInitializationSuperLate = Factory::get('Cache\Config')->get('cacheLateInitialization', 'inactive') === 'super-late' ? ' selected' : '';
        $inputMaxSimultaneousTasks              = intval(Factory::get('Cache\Config')->get('maxSimultaneousTasks', 'inactive'));

        $optionCacheSeparateFileByDeviceTypeDisabled    = Factory::get('Cache\Config')->get('cacheSeparateFileByDeviceType', 'inactive') === false ? ' selected' : '';
        $optionCacheSeparateFileByDeviceTypeMobile      = Factory::get('Cache\Config')->get('cacheSeparateFileByDeviceType', 'inactive') === 'mobile' ? ' selected' : '';
        $optionCacheSeparateFileByDeviceTypeMobileTablet= Factory::get('Cache\Config')->get('cacheSeparateFileByDeviceType', 'inactive') === 'mobile+tablet' ? ' selected' : '';

        $checkboxLoggedInUserGetCachedPages = Factory::get('Cache\Config')->get('loggedInUserGetCachedPages', 'inactive') ? ' checked' : '';
        $checkboxCacheSearchResults         = Factory::get('Cache\Config')->get('cacheSearchResults', 'inactive') ? ' checked' : '';
        $checkboxCachePagesWithQueryStrings = Factory::get('Cache\Config')->get('cachePagesWithQueryStrings', 'inactive') ? ' checked' : '';
        $checkboxCacheFeeds                 = Factory::get('Cache\Config')->get('cacheFeeds', 'inactive') ? ' checked' : '';
        $checkboxCache404Pages              = Factory::get('Cache\Config')->get('cache404Pages', 'inactive') ? ' checked' : '';
        $checkboxCacheGzipOutput            = Factory::get('Cache\Config')->get('cacheGzipOutput', 'inactive') ? ' checked' : '';

        $optionCacheGzipCompressionLevelMinimum   = Factory::get('Cache\Config')->get('cacheGzipCompressionLevel', 'inactive') === 1 ? ' selected' : '';
        $optionCacheGzipCompressionLevelDefault   = Factory::get('Cache\Config')->get('cacheGzipCompressionLevel', 'inactive') === 6 ? ' selected' : '';
        $optionCacheGzipCompressionLevelMaximum   = Factory::get('Cache\Config')->get('cacheGzipCompressionLevel', 'inactive') === 9 ? ' selected' : '';

        $checkboxPreloaderActivated         = Factory::get('Cache\Config')->get('preloaderActivated', 'inactive') ? ' checked' : '';

        $checkboxCacheCronService           = Factory::get('Cache\Config')->get('cacheCronService', 'inactive') ? ' checked' : '';
        $optionCacheCronInterval5Minutes    = Factory::get('Cache\Config')->get('cacheCronInterval', 'inactive') === 5 ? ' selected' : '';
        $optionCacheCronInterval10Minutes   = Factory::get('Cache\Config')->get('cacheCronInterval', 'inactive') === 10 ? ' selected' : '';
        $optionCacheCronInterval15Minutes   = Factory::get('Cache\Config')->get('cacheCronInterval', 'inactive') === 15 ? ' selected' : '';
        $optionCacheCronInterval30Minutes   = Factory::get('Cache\Config')->get('cacheCronInterval', 'inactive') === 30 ? ' selected' : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-general.html.php';
    }

    /**
     * displayTabHTML function.
     *
     * @access public
     * @return void
     */
    public function displayTabHTML()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_html')) {
            $this->saveHTML($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxMinifyRemoveHTMLComments   = Factory::get('Cache\Config')->get('minifyRemoveHTMLComments', 'inactive') ? ' checked' : '';
        $checkboxMinifyRemoveWhitespace     = Factory::get('Cache\Config')->get('minifyRemoveWhitespace', 'inactive') ? ' checked' : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-html.html.php';
    }

    /**
     * displayTabImage function.
     *
     * @access public
     * @return void
     */
    public function displayTabImage()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_image')) {
            $this->saveImage($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxImagesLazyLoad = Factory::get('Cache\Config')->get('imagesLazyLoad', 'inactive') ? ' checked' : '';
        $textareaImagesLazyLoadExclude = !empty(Factory::get('Cache\Config')->get('imagesLazyLoadExclude', 'inactive')) ? implode("\n", Factory::get('Cache\Config')->get('imagesLazyLoadExclude', 'inactive')) : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-image.html.php';
    }

    /**
     * displayTabJavaScript function.
     *
     * @access public
     * @return void
     */
    public function displayTabJavaScript()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_javascript')) {
            $this->saveJavaScript($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxScriptsMerge               = Factory::get('Cache\Config')->get('scriptsMerge', 'inactive') ? ' checked' : '';
        $checkboxScriptsSmartBundles        = Factory::get('Cache\Config')->get('scriptsSmartBundles', 'inactive') ? ' checked' : '';
        $checkboxScriptsMinify              = Factory::get('Cache\Config')->get('scriptsMinify', 'inactive') ? ' checked' : '';
        $checkboxScriptsDefer               = Factory::get('Cache\Config')->get('scriptsDefer', 'inactive') ? ' checked' : '';
        $checkboxScriptsPreloadTag          = Factory::get('Cache\Config')->get('scriptsPreloadTag', 'inactive') ? ' checked' : '';
        $checkboxScriptsFixSemicolon        = Factory::get('Cache\Config')->get('scriptsFixSemicolon', 'inactive') ? ' checked' : '';
        $checkboxScriptsGZIPOutput          = Factory::get('Cache\Config')->get('scriptsGZIPOutput', 'inactive') ? ' checked' : '';

        $optionScriptsGzipCompressionLevelMinimum   = Factory::get('Cache\Config')->get('scriptsGzipCompressionLevel', 'inactive') === 1 ? ' selected' : '';
        $optionScriptsGzipCompressionLevelDefault   = Factory::get('Cache\Config')->get('scriptsGzipCompressionLevel', 'inactive') === 6 ? ' selected' : '';
        $optionScriptsGzipCompressionLevelMaximum   = Factory::get('Cache\Config')->get('scriptsGzipCompressionLevel', 'inactive') === 9 ? ' selected' : '';

        $radioScriptsLocationHeader = Factory::get('Cache\Config')->get('scriptsLocation', 'inactive') == 'header' ? ' checked' : '';
        $radioScriptsLocationFooter = Factory::get('Cache\Config')->get('scriptsLocation', 'inactive') == 'footer' ? ' checked' : '';

        $radioScriptsExternalScriptsAfter   = Factory::get('Cache\Config')->get('scriptsExternalScriptsPosition', 'inactive') == 'after' ? ' checked' : '';
        $radioScriptsExternalScriptsBefore  = Factory::get('Cache\Config')->get('scriptsExternalScriptsPosition', 'inactive') == 'before' ? ' checked' : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-javascript.html.php';
    }

    /**
     * displayTabCSS function.
     *
     * @access public
     * @return void
     */
    public function displayTabCSS()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_css')) {
            $this->saveCSS($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxStylesMerge                = Factory::get('Cache\Config')->get('stylesMerge', 'inactive') ? ' checked' : '';
        $checkboxStylesExcludeStyleTags     = Factory::get('Cache\Config')->get('stylesExcludeStyleTags', 'inactive') ? ' checked' : '';
        $checkboxStylesMinify               = Factory::get('Cache\Config')->get('stylesMinify', 'inactive') ? ' checked' : '';
        $checkboxStylesPreloadTag           = Factory::get('Cache\Config')->get('stylesPreloadTag', 'inactive') ? ' checked' : '';
        $checkboxStylesGzipOutput           = Factory::get('Cache\Config')->get('stylesGzipOutput', 'inactive') ? ' checked' : '';

        $optionStylesGzipCompressionLevelMinimum    = Factory::get('Cache\Config')->get('stylesGzipCompressionLevel', 'inactive') === 1 ? ' selected' : '';
        $optionStylesGzipCompressionLevelDefault    = Factory::get('Cache\Config')->get('stylesGzipCompressionLevel', 'inactive') === 6 ? ' selected' : '';
        $optionStylesGzipCompressionLevelMaximum    = Factory::get('Cache\Config')->get('stylesGzipCompressionLevel', 'inactive') === 9 ? ' selected' : '';

        $radioStylesLocationHeader = Factory::get('Cache\Config')->get('stylesLocation', 'inactive') == 'header' ? ' checked' : '';
        $radioStylesLocationFooter = Factory::get('Cache\Config')->get('stylesLocation', 'inactive') == 'footer' ? ' checked' : '';

        $radioStylesExternalStylesAfter   = Factory::get('Cache\Config')->get('stylesExternalStylesPosition', 'inactive') == 'after' ? ' checked' : '';
        $radioStylesExternalStylesBefore  = Factory::get('Cache\Config')->get('stylesExternalStylesPosition', 'inactive') == 'before' ? ' checked' : '';

        $checkboxStylesOptimizeGoogleFonts          = Factory::get('Cache\Config')->get('stylesOptimizeGoogleFonts', 'inactive') ? ' checked' : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-css.html.php';
    }

    /**
     * @method displayPageOptimization
     *
     * @access public
     * @return void
     */
    public function displayPageOptimization(){
        if(!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_page_optimization')){
            $this->savePageOptimization($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $pageOptimization = Factory::get('Cache\Config')->get('pageOptimization', 'inactive');
        $excludedScripts = !empty($pageOptimization['excludedScripts']) ? $pageOptimization['excludedScripts'] : [];
        $excludedStyles = !empty($pageOptimization['excludedStyles']) ? $pageOptimization['excludedStyles'] : [];

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-page-optimization.html.php';
    }

    /**
     * displayTabCacheLifetimes function.
     *
     * @access public
     * @return void
     */
    public function displayTabCacheLifetimes()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_cache_lifetimes')) {
            $this->saveCacheLifetimes($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxCacheRefreshCacheAfterPublish          = Factory::get('Cache\Config')->get('cacheRefreshCacheAfterPublish', 'inactive') ? ' checked' : '';
        $checkboxCacheRefreshHomeCacheAfterPublish      = Factory::get('Cache\Config')->get('cacheRefreshHomeCacheAfterPublish', 'inactive') ? ' checked' : '';
        $checkboxCacheRefreshArchiveCacheAfterPublish   = Factory::get('Cache\Config')->get('cacheRefreshArchiveCacheAfterPublish', 'inactive') ? ' checked' : '';
        $checkboxCacheRefreshFeedCacheAfterPublish      = Factory::get('Cache\Config')->get('cacheRefreshFeedCacheAfterPublish', 'inactive') ? ' checked' : '';
        $checkboxCacheRefreshCacheAfterComment          = Factory::get('Cache\Config')->get('cacheRefreshCacheAfterComment', 'inactive') ? ' checked' : '';
        $checkboxCacheShowMetaBox                       = Factory::get('Cache\Config')->get('cacheShowMetaBox', 'inactive') ? ' checked' : '';
        $checkboxCacheShowRefreshOptionInQuickEdit      = Factory::get('Cache\Config')->get('cacheShowRefreshOptionInQuickEdit', 'inactive') ? ' checked' : '';

        $cacheLifetime                      = Factory::get('Cache\Config')->get('cacheLifetime', 'inactive');
        $inputCacheLifetimeHome             = intval($cacheLifetime['home']);
        $inputCacheLifetimeArchives['-']    = intval($cacheLifetime['archives']['-']);
        $inputCacheLifetimePostType['-']    = intval($cacheLifetime['postType']['-']);
        $inputCacheLifetimeFeed             = intval($cacheLifetime['feed']);
        $inputCacheLifetime404              = intval($cacheLifetime['404']);
        $inputCacheLifetimeGarbage          = intval($cacheLifetime['garbage']);

        $postTypes = $this->getPostTypes();

        // Get their configured lifetimes
        foreach ($postTypes as $postTypeData) {
            // Archives
            if (isset($cacheLifetime['archives'][$postTypeData->name])) {
                $inputCacheLifetimeArchives[$postTypeData->name] = intval($cacheLifetime['archives'][$postTypeData->name]);
            } else {
                $inputCacheLifetimeArchives[$postTypeData->name] = '';
            }

            // Posts
            if (isset($cacheLifetime['postType'][$postTypeData->name])) {
                $inputCacheLifetimePostType[$postTypeData->name] = intval($cacheLifetime['postType'][$postTypeData->name]);
            } else {
                $inputCacheLifetimePostType[$postTypeData->name] = '';
            }
        }

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-cache-lifetimes.html.php';
    }

    /**
     * displayTabCacheExceptions function.
     *
     * @access public
     * @return void
     */
    public function displayTabCacheExceptions()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_cache_exceptions')) {
            $this->saveCacheExceptions($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $postTypes = $this->getPostTypes();
        $taxonomies = $this->getTaxonomies();

        // Don't cache pages which contains one of the following query vars
        $textareaCacheDontCachePagesContainQuery = Factory::get('Cache\Config')->get('cacheDontCachePagesContainQuery', 'inactive');

        if (!empty($textareaCacheDontCachePagesContainQuery)) {
            // Check if value is multidimensional
            if (Factory::get('Cache\Tools')->isArrayMultidimensional($textareaCacheDontCachePagesContainQuery)) {
                $textareaCacheDontCachePagesContainQuery = json_encode($textareaCacheDontCachePagesContainQuery);
            } else {
                $textareaCacheDontCachePagesContainQuery = implode("\n", $textareaCacheDontCachePagesContainQuery);
            }
        } else {
            $textareaCacheDontCachePagesContainQuery = '';
        }

        // Don't cache pages which contains one of the following path
        $textareaCacheDontCachePagesContainPath = implode("\n", Factory::get('Cache\Config')->get('cacheDontCachePagesContainPath', 'inactive'));

        // Don't cache pages of the following post type
        $cacheDontCachePagesOfPostType  = Factory::get('Cache\Config')->get('cacheDontCachePagesOfPostType', 'inactive');

        // Check which post type is excluded
        if (!empty($postTypes)) {
            foreach ($postTypes as $postTypeData) {
                $checkboxCacheDontCachePagesOfPostType[$postTypeData->name] = !empty($cacheDontCachePagesOfPostType) && in_array($postTypeData->name, $cacheDontCachePagesOfPostType) ? ' checked' : '';
            }
        }

        // Don't cache pages of the following taxonomy
        $cacheDontCachePagesOfTaxonomy  = Factory::get('Cache\Config')->get('cacheDontCachePagesOfTaxonomy', 'inactive');

        // Check which taxonomy is excluded
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomyData) {
                $checkboxCacheDontCachePagesOfTaxonomy[$taxonomyData->name] = !empty($cacheDontCachePagesOfTaxonomy) && in_array($taxonomyData->name, $cacheDontCachePagesOfTaxonomy) ? ' checked' : '';
            }
        }

        // Don't use cache when user agent
        $textareaCacheDontUseCacheWhenUserAgent = implode("\n", Factory::get('Cache\Config')->get('cacheDontUseCacheWhenUserAgent', 'inactive'));

        // Don't use cache when cookie
        $textareaCacheDontUseCacheWhenCookie = implode("\n", Factory::get('Cache\Config')->get('cacheDontUseCacheWhenCookie', 'inactive'));

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-cache-exceptions.html.php';
    }

    /**
     * displayTabBrowser function.
     *
     * @access public
     * @return void
     */
    public function displayTabBrowser()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_browser')) {
            $this->saveBrowser($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxBrowserCacheSetControlHeader           = Factory::get('Cache\Config')->get('browserCacheSetControlHeader', 'inactive') ? ' checked' : '';
        $inputBrowserCacheControlHeaderExpiresLifetime  = intval(Factory::get('Cache\Config')->get('browserCacheControlHeaderExpiresLifetime', 'inactive'));

        $optionBrowserCacheControlPolicyPublic          = Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') === 'public' ? ' selected' : '';
        $optionBrowserCacheControlPolicyPrivate         = Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') === 'private' ? ' selected' : '';
        $optionBrowserCacheControlPolicyPublicMaxAge    = Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') === 'public-max-age' ? ' selected' : '';
        $optionBrowserCacheControlPolicyPrivateMaxAge   = Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') === 'private-max-age' ? ' selected' : '';
        $optionBrowserCacheControlPolicyNoCache         = Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') === 'no-cache' ? ' selected' : '';

        $checkboxBrowserCacheSetLastModified            = Factory::get('Cache\Config')->get('browserCacheSetLastModified', 'inactive') ? ' checked' : '';
        $checkboxBrowserCacheSetETag                    = Factory::get('Cache\Config')->get('browserCacheSetETag', 'inactive') ? ' checked' : '';
        $checkboxBrowserCacheSetOSOSuperCacheTag        = Factory::get('Cache\Config')->get('browserCacheSetOSOSuperCacheTag', 'inactive') ? ' checked' : '';
        $checkboxBrowserCacheHeaderManagementOnPages    = Factory::get('Cache\Config')->get('browserCacheHeaderManagementOnPages', 'inactive') ? ' checked' : '';
        $checkboxBrowserCacheModifyHtaccess             = Factory::get('Cache\Config')->get('browserCacheModifyHtaccess', 'inactive') ? ' checked' : '';
        $checkboxDNSPrefetch                            = Factory::get('Cache\Config')->get('dnsPrefetch', 'inactive') ? ' checked' : '';
        $checkboxBrowserCacheDisableXCacheHeaders       = Factory::get('Cache\Config')->get('browserCacheDisableXCacheHeaders', 'inactive') ? ' checked' : '';

        $checkboxBrowserSecurityHeader                          = Factory::get('Cache\Config')->get('browserSecurityHeader', 'inactive') ? ' checked' : '';
        $checkboxBrowserSecurityContentSecurityPolicyHeader     = Factory::get('Cache\Config')->get('browserSecurityContentSecurityPolicyHeader', 'inactive') ? ' checked' : '';

        $browserSecurityContentSecurityPolicy = Factory::get('Cache\Config')->get('browserSecurityContentSecurityPolicy', 'inactive');

        if (empty($browserSecurityContentSecurityPolicy)) {
            $browserSecurityContentSecurityPolicy = Factory::get('Cache\Config')->defaultConfig()['browserSecurityContentSecurityPolicy'];
        }

        $textareaBrowserSecurityContentSecurityPolicy           = implode("\n", $browserSecurityContentSecurityPolicy);

        $optionBrowserSecurityReferrerPolicyNoPolicy            = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === '-' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicyNoReferrer          = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'no-referrer' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicyNoReferrerWD        = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'no-referrer-when-downgrade' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicySameOrigin          = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'same-origin' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicyOrigin              = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'origin' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicyStrictOrigin        = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'strict-origin' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicyOriginWCO           = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'origin-when-cross-origin' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicyStrictOriginWCO     = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'strict-origin-when-cross-origin' ? ' selected' : '';
        $optionBrowserSecurityReferrerPolicyUnsafeURL           = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'inactive') === 'unsafe-url' ? ' selected' : '';

        $checkboxBrowserSecurityStrictTransportSecurity         = Factory::get('Cache\Config')->get('browserSecurityStrictTransportSecurity', 'inactive') ? ' checked' : '';
        $radioBrowserSecurityXFrameOptionsDisabled              = Factory::get('Cache\Config')->get('browserSecurityXFrameOptions', 'inactive') == 'disabled' ? ' checked' : '';
        $radioBrowserSecurityXFrameOptionsDeny                  = Factory::get('Cache\Config')->get('browserSecurityXFrameOptions', 'inactive') == 'deny' ? ' checked' : '';
        $radioBrowserSecurityXFrameOptionsSameOrigin            = Factory::get('Cache\Config')->get('browserSecurityXFrameOptions', 'inactive') == 'sameorigin' ? ' checked' : '';
        $checkboxBrowserSecurityXContentTypeOptions             = Factory::get('Cache\Config')->get('browserSecurityXContentTypeOptions', 'inactive') ? ' checked' : '';
        $checkboxBrowserSecurityXXSSProtection                  = Factory::get('Cache\Config')->get('browserSecurityXXSSProtection', 'inactive') ? ' checked' : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-browser.html.php';
    }

    /**
     * displayTabMiscellaneous function.
     *
     * @access public
     * @return void
     */
    public function displayTabMiscellaneous()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_miscellaneous')) {
            $this->saveMiscellaneous($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxMiscellaneousDisableEmojis     = Factory::get('Cache\Config')->get('miscellaneousDisableEmojis', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableGenerator  = Factory::get('Cache\Config')->get('miscellaneousDisableGenerator', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableManifest   = Factory::get('Cache\Config')->get('miscellaneousDisableManifest', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableFeeds      = Factory::get('Cache\Config')->get('miscellaneousDisableFeeds', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableRSD        = Factory::get('Cache\Config')->get('miscellaneousDisableRSD', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableRESTAPI    = Factory::get('Cache\Config')->get('miscellaneousDisableRESTAPI', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableOEmbed     = Factory::get('Cache\Config')->get('miscellaneousDisableOEmbed', 'inactive') ? ' checked' : '';

        // Third party plugins (TTP)
        $checkboxMiscellaneousDisableTPPSliderRevolutionGenerator   = Factory::get('Cache\Config')->get('miscellaneousDisableTPPSliderRevolutionGenerator', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableTPPLayerSliderGenerator        = Factory::get('Cache\Config')->get('miscellaneousDisableTPPLayerSliderGenerator', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousDisableTPPVisualComposerGenerator     = Factory::get('Cache\Config')->get('miscellaneousDisableTPPVisualComposerGenerator', 'inactive') ? ' checked' : '';

        // OSO Super Cache
        $checkboxmiscellaneousDisableOSOSuperCacheRefreshCacheNotice      = Factory::get('Cache\Config')->get('miscellaneousDisableOSOSuperCacheRefreshCacheNotice', 'inactive') ? ' checked' : '';
        $checkboxmiscellaneousDisableOSOSuperCacheToolbarMenuItem         = Factory::get('Cache\Config')->get('miscellaneousDisableOSOSuperCacheToolbarMenuItem', 'inactive') ? ' checked' : '';
        $checkboxMiscellaneousNginx                                 = Factory::get('Cache\Config')->get('miscellaneousNginx', 'inactive') ? ' checked' : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-miscellaneous.html.php';
    }

    /**
     * displayTabDebug function.
     *
     * @access public
     * @return void
     */
    public function displayTabDebug()
    {
        if (!empty($_POST['formSend']) && check_admin_referer('oso_super_cache_advanced_settings_debug')) {
            $this->saveDebug($_POST);

            Factory::get('Cache\Backend\Backend')->addMessage(_x('Saved successfully.', 'Status message', 'oso-super-cache'), 'success');
        }

        $checkboxDebugAddCacheInformation   = Factory::get('Cache\Config')->get('debugAddCacheInformation', 'inactive') ? ' checked' : '';

        include Factory::get('Cache\Backend\Backend')->templatePath.'/advanced-settings-debug.html.php';
    }

    /**
     * saveGeneral function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveGeneral($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['cacheActivated'] = !empty($data['cacheActivated']) ? 'yes' : 'no';

        $inactiveConfig['cacheLateInitialization'] = false;

        if (!empty($data['cacheLateInitialization']) && ($data['cacheLateInitialization'] == 'late' || $data['cacheLateInitialization'] == 'super-late')) {
            $inactiveConfig['cacheLateInitialization'] = $data['cacheLateInitialization'];
        }

        $inactiveConfig['maxSimultaneousTasks'] = !empty($data['maxSimultaneousTasks']) && intval($data['maxSimultaneousTasks']) > 0 ? intval($data['maxSimultaneousTasks']) : 5;

        $inactiveConfig['cacheSeparateFileByDeviceType'] = false;

        if (!empty($data['cacheSeparateFileByDeviceType']) && ($data['cacheSeparateFileByDeviceType'] == 'mobile' || $data['cacheSeparateFileByDeviceType'] == 'mobile+tablet')) {
            $inactiveConfig['cacheSeparateFileByDeviceType'] = $data['cacheSeparateFileByDeviceType'];
        }

        $inactiveConfig['loggedInUserGetCachedPages'] = !empty($data['loggedInUserGetCachedPages']) ? true : false;
        $inactiveConfig['cacheSearchResults'] = !empty($data['cacheSearchResults']) ? true : false;
        $inactiveConfig['cachePagesWithQueryStrings'] = !empty($data['cachePagesWithQueryStrings']) ? true : false;
        $inactiveConfig['cacheFeeds'] = !empty($data['cacheFeeds']) ? true : false;
        $inactiveConfig['cache404Pages'] = !empty($data['cache404Pages']) ? true : false;
        $inactiveConfig['cacheGzipOutput'] = !empty($data['cacheGzipOutput']) ? true : false;

        $inactiveConfig['cacheGzipCompressionLevel'] = 6;

        if (!empty($data['cacheGzipCompressionLevel'])) {
            if ($data['cacheGzipCompressionLevel'] == 'minimal') {
                $inactiveConfig['cacheGzipCompressionLevel'] = 1;
            }

            if ($data['cacheGzipCompressionLevel'] == 'maximum') {
                $inactiveConfig['cacheGzipCompressionLevel'] = 9;
            }
        }

        $inactiveConfig['preloaderActivated'] = !empty($data['preloaderActivated']) ? true : false;
        $inactiveConfig['cacheCronService'] = !empty($data['cacheCronService']) ? true : false;

        $inactiveConfig['cacheCronInterval'] = 15;

        if (!empty($data['cacheCronInterval'])) {
            if ($data['cacheCronInterval'] == 5) {
                $inactiveConfig['cacheCronInterval'] = 5;
            }

            if ($data['cacheCronInterval'] == 10) {
                $inactiveConfig['cacheCronInterval'] = 10;
            }

            if ($data['cacheCronInterval'] == 30) {
                $inactiveConfig['cacheCronInterval'] = 30;
            }
        }

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveHTML function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveHTML($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['minifyRemoveHTMLComments'] = !empty($data['minifyRemoveHTMLComments']) ? true : false;
        $inactiveConfig['minifyRemoveWhitespace']   = !empty($data['minifyRemoveWhitespace']) ? true : false;

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveImage function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveImage($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['imagesLazyLoad'] = !empty($data['imagesLazyLoad']) ? true : false;

        $inactiveConfig['imagesLazyLoadExclude']   = [];

        if (!empty($data['imagesLazyLoadExclude'])) {

            $data['imagesLazyLoadExclude'] = stripslashes($data['imagesLazyLoadExclude']);

            $data['imagesLazyLoadExclude'] = preg_split('/\r\n|[\r\n]/', $data['imagesLazyLoadExclude']);

            if (!empty($data['imagesLazyLoadExclude'])) {
                foreach ($data['imagesLazyLoadExclude'] as $path) {
                    $path = trim(stripslashes($path));

                    if (!empty($path)) {
                        $inactiveConfig['imagesLazyLoadExclude'][] = $path;
                    }
                }
            }
        }

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveJavaScript function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveJavaScript($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['scriptsMerge']                 = !empty($data['scriptsMerge']) ? true : false;
        $inactiveConfig['scriptsSmartBundles']          = !empty($data['scriptsSmartBundles']) ? true : false;
        $inactiveConfig['scriptsMinify']                = !empty($data['scriptsMinify']) ? true : false;
        $inactiveConfig['scriptsDefer']                 = !empty($data['scriptsDefer']) ? true : false;
        $inactiveConfig['scriptsPreloadTag']            = !empty($data['scriptsPreloadTag']) ? true : false;
        $inactiveConfig['scriptsFixSemicolon']          = !empty($data['scriptsFixSemicolon']) ? true : false;
        $inactiveConfig['scriptsGZIPOutput']            = !empty($data['scriptsGZIPOutput']) ? true : false;

        $inactiveConfig['scriptsGzipCompressionLevel'] = 6;

        if (!empty($data['scriptsGzipCompressionLevel'])) {
            if ($data['scriptsGzipCompressionLevel'] == 'minimal') {
                $inactiveConfig['scriptsGzipCompressionLevel'] = 1;
            }

            if ($data['scriptsGzipCompressionLevel'] == 'maximum') {
                $inactiveConfig['scriptsGzipCompressionLevel'] = 9;
            }
        }

        $inactiveConfig['scriptsLocation']  = 'header';

        if (!empty($data['scriptsLocation'])) {
            if ($data['scriptsLocation'] == 'footer') {
                $inactiveConfig['scriptsLocation'] = 'footer';
            }
        }

        $inactiveConfig['scriptsExternalScriptsPosition']  = 'after';

        if (!empty($data['scriptsExternalScriptsPosition'])) {
            if ($data['scriptsExternalScriptsPosition'] == 'before') {
                $inactiveConfig['scriptsExternalScriptsPosition'] = 'before';
            }
        }

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveCSS function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveCSS($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['stylesMerge']                  = !empty($data['stylesMerge']) ? true : false;
        $inactiveConfig['stylesExcludeStyleTags']       = !empty($data['stylesExcludeStyleTags']) ? true : false;
        $inactiveConfig['stylesMinify']                 = !empty($data['stylesMinify']) ? true : false;
        $inactiveConfig['stylesPreloadTag']             = !empty($data['stylesPreloadTag']) ? true : false;

        $inactiveConfig['stylesGzipOutput'] = !empty($data['stylesGzipOutput']) ? true : false;

        $inactiveConfig['stylesGzipCompressionLevel'] = 6;

        if (!empty($data['stylesGzipCompressionLevel'])) {
            if ($data['stylesGzipCompressionLevel'] == 'minimal') {
                $inactiveConfig['stylesGzipCompressionLevel'] = 1;
            }

            if ($data['stylesGzipCompressionLevel'] == 'maximum') {
                $inactiveConfig['stylesGzipCompressionLevel'] = 9;
            }
        }

        $inactiveConfig['stylesLocation']  = 'header';

        if (!empty($data['stylesLocation'])) {
            if ($data['stylesLocation'] == 'footer') {
                $inactiveConfig['stylesLocation'] = 'footer';
            }
        }

        $inactiveConfig['stylesExternalStylesPosition']  = 'after';

        if (!empty($data['stylesExternalStylesPosition'])) {
            if ($data['stylesExternalStylesPosition'] == 'before') {
                $inactiveConfig['stylesExternalStylesPosition'] = 'before';
            }
        }

        $inactiveConfig['stylesOptimizeGoogleFonts'] = !empty($data['stylesOptimizeGoogleFonts']) ? true : false;

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * @method savePageOptimization
     *
     * @access public
     * @param $data
     * @return void
     */
    public function savePageOptimization($data){
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $jsRules = [];
        foreach($data['excludedScripts'] as $excludedJs){
            $jsRules[$excludedJs['pageId']] = $excludedJs['itemId'];
        }
        $inactiveConfig['pageOptimization']['excludedScripts'] = !empty($jsRules) ? $jsRules : [];

        $styleRules = [];
        foreach($data['excludedStyles'] as $excludedStyle){
            $styleRules[$excludedStyle['pageId']] = $excludedStyle['itemId'];
        }
        $inactiveConfig['pageOptimization']['excludedStyles'] = !empty($styleRules) ? $styleRules : [];

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveCacheLifetimes function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveCacheLifetimes($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['cacheRefreshCacheAfterPublish']        = !empty($data['cacheRefreshCacheAfterPublish']) ? true : false;
        $inactiveConfig['cacheRefreshHomeCacheAfterPublish']    = !empty($data['cacheRefreshHomeCacheAfterPublish']) ? true : false;
        $inactiveConfig['cacheRefreshArchiveCacheAfterPublish'] = !empty($data['cacheRefreshArchiveCacheAfterPublish']) ? true : false;
        $inactiveConfig['cacheRefreshFeedCacheAfterPublish']    = !empty($data['cacheRefreshFeedCacheAfterPublish']) ? true : false;
        $inactiveConfig['cacheRefreshCacheAfterComment']        = !empty($data['cacheRefreshCacheAfterComment']) ? true : false;
        $inactiveConfig['cacheShowMetaBox']                     = !empty($data['cacheShowMetaBox']) ? true : false;
        $inactiveConfig['cacheShowRefreshOptionInQuickEdit']    = !empty($data['cacheShowRefreshOptionInQuickEdit']) ? true : false;

        $inactiveConfig['cacheLifetime']['home']            = !empty($data['cacheLifetimeHome']) ? intval($data['cacheLifetimeHome']) : 0;
        $inactiveConfig['cacheLifetime']['archives']['-']   = !empty($data['cacheLifetimeArchives']['-']) ? intval($data['cacheLifetimeArchives']['-']) : 0;
        $inactiveConfig['cacheLifetime']['postType']['-']   = !empty($data['cacheLifetimePostType']['-']) ? intval($data['cacheLifetimePostType']['-']) : 0;
        $inactiveConfig['cacheLifetime']['feed']            = !empty($data['cacheLifetimeFeed']) ? intval($data['cacheLifetimeFeed']) : 0;
        $inactiveConfig['cacheLifetime']['404']             = !empty($data['cacheLifetime404']) ? intval($data['cacheLifetime404']) : 604800;
        $inactiveConfig['cacheLifetime']['garbage']         = !empty($data['cacheLifetimeGarbage']) ? intval($data['cacheLifetimeGarbage']) : 31536000;

        // Check post types
        $allPostTypes = $this->getPostTypes();

        if (!empty($data['cacheLifetimeArchives'])) {
            foreach ($data['cacheLifetimeArchives'] as $key => $value) {
                // Check if post type exists
                if ($key != '-' && !empty($allPostTypes[$key])) {
                    $inactiveConfig['cacheLifetime']['archives'][$key]  = !empty($value) ? intval($value) : 0;
                }
            }
        }

        if (!empty($data['cacheLifetimePostType'])) {
            foreach ($data['cacheLifetimePostType'] as $key => $value) {
                // Check if post type exists
                if ($key != '-' && !empty($allPostTypes[$key])) {
                    $inactiveConfig['cacheLifetime']['postType'][$key]  = !empty($value) ? intval($value) : 0;
                }
            }
        }

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveCacheExceptions function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveCacheExceptions($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['cacheDontCachePagesContainQuery']  = [];

        // Check if json
        if (!empty($data['cacheDontCachePagesContainQuery'])) {
            $data['cacheDontCachePagesContainQuery'] = stripslashes($data['cacheDontCachePagesContainQuery']);

            if (Factory::get('Cache\Tools')->isStringJSON($data['cacheDontCachePagesContainQuery'])) {
                $inactiveConfig['cacheDontCachePagesContainQuery'] = json_decode($data['cacheDontCachePagesContainQuery'], true);
            } else {
                $data['cacheDontCachePagesContainQuery'] = preg_split('/\r\n|[\r\n]/', $data['cacheDontCachePagesContainQuery']);

                if (!empty($data['cacheDontCachePagesContainQuery'])) {
                    foreach ($data['cacheDontCachePagesContainQuery'] as $queryVar) {
                        $queryVar = trim(stripslashes($queryVar));

                        if (!empty($queryVar)) {
                            $inactiveConfig['cacheDontCachePagesContainQuery'][] = $queryVar;
                        }
                    }
                }
            }
        }

        $inactiveConfig['cacheDontCachePagesContainPath']   = [];

        if (!empty($data['cacheDontCachePagesContainPath'])) {
            $data['cacheDontCachePagesContainPath'] = stripslashes($data['cacheDontCachePagesContainPath']);

            $data['cacheDontCachePagesContainPath'] = preg_split('/\r\n|[\r\n]/', $data['cacheDontCachePagesContainPath']);

            if (!empty($data['cacheDontCachePagesContainPath'])) {
                foreach ($data['cacheDontCachePagesContainPath'] as $path) {
                    $path = trim(stripslashes($path));

                    if (!empty($path)) {
                        $inactiveConfig['cacheDontCachePagesContainPath'][] = $path;
                    }
                }
            }
        }

        // Check post types
        $allPostTypes = $this->getPostTypes();

        $inactiveConfig['cacheDontCachePagesOfPostType'] = [];

        if (!empty($data['cacheDontCachePagesOfPostType'])) {
            foreach ($data['cacheDontCachePagesOfPostType'] as $key => $value) {
                // Check if post type exists
                if (!empty($allPostTypes[$key])) {
                    $inactiveConfig['cacheDontCachePagesOfPostType'][]  = $key;
                }
            }
        }

        // Check taxonomies
        $allTaxonomies = $this->getTaxonomies();

        $inactiveConfig['cacheDontCachePagesOfTaxonomy'] = [];

        if (!empty($data['cacheDontCachePagesOfTaxonomy'])) {
            foreach ($data['cacheDontCachePagesOfTaxonomy'] as $key => $value) {
                // Check if taxonomy exists
                if (!empty($allTaxonomies[$key])) {
                    $inactiveConfig['cacheDontCachePagesOfTaxonomy'][]  = $key;
                }
            }
        }

        $inactiveConfig['cacheDontUseCacheWhenUserAgent']   = [];

        if (!empty($data['cacheDontUseCacheWhenUserAgent'])) {
            $data['cacheDontUseCacheWhenUserAgent'] = stripslashes($data['cacheDontUseCacheWhenUserAgent']);

            $data['cacheDontUseCacheWhenUserAgent'] = preg_split('/\r\n|[\r\n]/', $data['cacheDontUseCacheWhenUserAgent']);

            if (!empty($data['cacheDontUseCacheWhenUserAgent'])) {
                foreach ($data['cacheDontUseCacheWhenUserAgent'] as $path) {
                    $path = trim(stripslashes($path));

                    if (!empty($path)) {
                        $inactiveConfig['cacheDontUseCacheWhenUserAgent'][] = $path;
                    }
                }
            }
        }

        $inactiveConfig['cacheDontUseCacheWhenCookie']   = [];

        if (!empty($data['cacheDontUseCacheWhenCookie'])) {
            $data['cacheDontUseCacheWhenCookie'] = stripslashes($data['cacheDontUseCacheWhenCookie']);

            $data['cacheDontUseCacheWhenCookie'] = preg_split('/\r\n|[\r\n]/', $data['cacheDontUseCacheWhenCookie']);

            if (!empty($data['cacheDontUseCacheWhenCookie'])) {
                foreach ($data['cacheDontUseCacheWhenCookie'] as $path) {
                    $path = trim(stripslashes($path));

                    if (!empty($path)) {
                        $inactiveConfig['cacheDontUseCacheWhenCookie'][] = $path;
                    }
                }
            }
        }

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveBrowser function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveBrowser($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['browserCacheSetControlHeader'] = !empty($data['browserCacheSetControlHeader']) ? true : false;
        $inactiveConfig['browserCacheControlPolicy']    = 'public-max-age';

        if (!empty($data['browserCacheControlPolicy'])) {
            if ($data['browserCacheControlPolicy'] == 'public') {
                $inactiveConfig['browserCacheControlPolicy'] = 'public';
            }

            if ($data['browserCacheControlPolicy'] == 'private') {
                $inactiveConfig['browserCacheControlPolicy'] = 'private';
            }

            if ($data['browserCacheControlPolicy'] == 'private-max-age') {
                $inactiveConfig['browserCacheControlPolicy'] = 'private-max-age';
            }

            if ($data['browserCacheControlPolicy'] == 'no-cache') {
                $inactiveConfig['browserCacheControlPolicy'] = 'no-cache';
            }
        }

        $inactiveConfig['browserCacheControlHeaderExpiresLifetime'] = !empty($data['browserCacheControlHeaderExpiresLifetime']) ? intval($data['browserCacheControlHeaderExpiresLifetime']) : 0;
        $inactiveConfig['browserCacheSetLastModified']              = !empty($data['browserCacheSetLastModified']) ? true : false;
        $inactiveConfig['browserCacheSetETag']                      = !empty($data['browserCacheSetETag']) ? true : false;
        $inactiveConfig['browserCacheSetOSOSuperCacheTag']           = !empty($data['browserCacheSetOSOSuperCacheTag']) ? true : false;
        $inactiveConfig['browserCacheHeaderManagementOnPages']      = !empty($data['browserCacheHeaderManagementOnPages']) ? true : false;
        $inactiveConfig['browserCacheModifyHtaccess']               = !empty($data['browserCacheModifyHtaccess']) ? true : false;
        $inactiveConfig['dnsPrefetch']                              = !empty($data['dnsPrefetch']) ? true : false;
        $inactiveConfig['browserCacheDisableXCacheHeaders']         = !empty($data['browserCacheDisableXCacheHeaders']) ? true : false;

        $inactiveConfig['browserSecurityHeader']                        = !empty($data['browserSecurityHeader']) ? true : false;
        $inactiveConfig['browserSecurityContentSecurityPolicyHeader']   = !empty($data['browserSecurityContentSecurityPolicyHeader']) ? true : false;

        $inactiveConfig['browserSecurityContentSecurityPolicy']   = [];

        if (!empty($data['browserSecurityContentSecurityPolicy'])) {
            $data['browserSecurityContentSecurityPolicy'] = stripslashes($data['browserSecurityContentSecurityPolicy']);

            $data['browserSecurityContentSecurityPolicy'] = preg_split('/\r\n|[\r\n]/', $data['browserSecurityContentSecurityPolicy']);

            if (!empty($data['browserSecurityContentSecurityPolicy'])) {
                foreach ($data['browserSecurityContentSecurityPolicy'] as $directives) {
                    $directives = trim(stripslashes($directives));

                    if (!empty($directives)) {
                        $inactiveConfig['browserSecurityContentSecurityPolicy'][] = $directives;
                    }
                }
            }
        }

        $inactiveConfig['browserSecurityReferrerPolicy']    = '-';

        if (!empty($data['browserSecurityReferrerPolicy'])) {
            if ($data['browserSecurityReferrerPolicy'] == 'no-referrer') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'no-referrer';
            }

            if ($data['browserSecurityReferrerPolicy'] == 'no-referrer-when-downgrade') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'no-referrer-when-downgrade';
            }

            if ($data['browserSecurityReferrerPolicy'] == 'same-origin') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'same-origin';
            }

            if ($data['browserSecurityReferrerPolicy'] == 'origin') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'origin';
            }

            if ($data['browserSecurityReferrerPolicy'] == 'strict-origin') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'strict-origin';
            }

            if ($data['browserSecurityReferrerPolicy'] == 'origin-when-cross-origin') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'origin-when-cross-origin';
            }

            if ($data['browserSecurityReferrerPolicy'] == 'strict-origin-when-cross-origin') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'strict-origin-when-cross-origin';
            }

            if ($data['browserSecurityReferrerPolicy'] == 'unsafe-url') {
                $inactiveConfig['browserSecurityReferrerPolicy'] = 'unsafe-url';
            }
        }

        $inactiveConfig['browserSecurityStrictTransportSecurity']       = !empty($data['browserSecurityStrictTransportSecurity']) ? true : false;

        $inactiveConfig['browserSecurityXFrameOptions']       = 'disabled';

        if (!empty($data['browserSecurityXFrameOptions'])) {
            if ($data['browserSecurityXFrameOptions'] == 'deny') {
                $inactiveConfig['browserSecurityXFrameOptions'] = 'deny';
            }

            if ($data['browserSecurityXFrameOptions'] == 'sameorigin') {
                $inactiveConfig['browserSecurityXFrameOptions'] = 'sameorigin';
            }
        }

        $inactiveConfig['browserSecurityXContentTypeOptions']   = !empty($data['browserSecurityXContentTypeOptions']) ? true : false;
        $inactiveConfig['browserSecurityXXSSProtection']        = !empty($data['browserSecurityXXSSProtection']) ? true : false;

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveMiscellaneous function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveMiscellaneous($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['miscellaneousDisableEmojis']       = !empty($data['miscellaneousDisableEmojis']) ? true : false;
        $inactiveConfig['miscellaneousDisableGenerator']    = !empty($data['miscellaneousDisableGenerator']) ? true : false;
        $inactiveConfig['miscellaneousDisableManifest']     = !empty($data['miscellaneousDisableManifest']) ? true : false;
        $inactiveConfig['miscellaneousDisableFeeds']        = !empty($data['miscellaneousDisableFeeds']) ? true : false;
        $inactiveConfig['miscellaneousDisableRSD']          = !empty($data['miscellaneousDisableRSD']) ? true : false;
        $inactiveConfig['miscellaneousDisableRESTAPI']      = !empty($data['miscellaneousDisableRESTAPI']) ? true : false;
        $inactiveConfig['miscellaneousDisableOEmbed']       = !empty($data['miscellaneousDisableOEmbed']) ? true : false;

        $inactiveConfig['miscellaneousDisableTPPSliderRevolutionGenerator'] = !empty($data['miscellaneousDisableTPPSliderRevolutionGenerator']) ? true : false;
        $inactiveConfig['miscellaneousDisableTPPLayerSliderGenerator']      = !empty($data['miscellaneousDisableTPPLayerSliderGenerator']) ? true : false;
        $inactiveConfig['miscellaneousDisableTPPVisualComposerGenerator']   = !empty($data['miscellaneousDisableTPPVisualComposerGenerator']) ? true : false;

        $inactiveConfig['miscellaneousDisableOSOSuperCacheRefreshCacheNotice']    = !empty($data['miscellaneousDisableOSOSuperCacheRefreshCacheNotice']) ? true : false;
        $inactiveConfig['miscellaneousDisableOSOSuperCacheToolbarMenuItem']       = !empty($data['miscellaneousDisableOSOSuperCacheToolbarMenuItem']) ? true : false;
        $inactiveConfig['miscellaneousNginx']                               = !empty($data['miscellaneousNginx']) ? true : false;

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * saveDebug function.
     *
     * @access public
     * @param mixed $data
     * @return void
     */
    public function saveDebug($data)
    {
        $inactiveConfig = Factory::get('Cache\Config')->get(null, 'inactive');

        $inactiveConfig['debugAddCacheInformation'] = !empty($data['debugAddCacheInformation']) ? true : false;

        // Save config
        Factory::get('Cache\Config')->saveConfig($inactiveConfig, 'inactive');

        Factory::get('Cache\Config')->setCurrentPreset('custom');
    }

    /**
     * applyNewConfig function.
     *
     * @access public
     * @return void
     */
    public function applyNewConfig($refreshCache = false)
    {
        $newConfig = Factory::get('Cache\Config')->getConfig('inactive');

        Factory::get('Cache\Config')->saveConfig(
            $newConfig,
            'active',
            false
        );

        // Remove "Activate OSO Super Cache" message
        if (!empty($newConfig['cacheActivated']) && $newConfig['cacheActivated'] === 'yes') {
            update_option('OSOSuperCacheActivatedMessage', true, 'yes');
        }

        // Save config as custom config
        update_option('OSOSuperCacheConfigCustom', Factory::get('Cache\Config')->getConfig(), 'no');

        // Modify .htaccess
        $this->modifyHtaccess($newConfig['browserCacheModifyHtaccess']);

        $this->modifyHtaccessSecurity($newConfig['browserSecurityHeader']);

        // Clear cache
        if ($refreshCache) {
            Factory::get('Cache\Frontend\Garbage')->clearCache();

            // Preload homepage
            Factory::get('Cache\Frontend\InstantPreloader')->preload(get_site_url());
        }

        Factory::get('Cache\Backend\Backend')->addMessage(_x('Your new configuration is now active.', 'Status message', 'oso-super-cache'), 'success');
    }

    /**
     * resetNewConfig function.
     *
     * @access public
     * @return void
     */
    public function resetNewConfig()
    {
        Factory::get('Cache\Config')->saveConfig(
            Factory::get('Cache\Config')->getConfig('active'),
            'inactive',
            false
        );

        Factory::get('Cache\Backend\Backend')->addMessage(_x('Your changes were discarded.', 'Status message', 'oso-super-cache'), 'success');
    }

    /**
     * getPostTypes function. Returns all post types ordered naturally
     *
     * @access public
     * @return void
     */
    public function getPostTypes()
    {
        $postTypes = get_post_types(['public'=>true], 'objects');

        $orderedPostTypes = [];

        // Build list
        foreach ($postTypes as $postType) {
            $orderedPostTypes[$postType->name] = $postType->label;
        }

        // Order list
        asort($orderedPostTypes, SORT_NATURAL | SORT_FLAG_CASE);

        $newOrderedPostTypes = [];

        foreach ($orderedPostTypes as $postType => $postTypeData) {
            $newOrderedPostTypes[$postType] = $postTypes[$postType];
        }

        unset($postTypes);
        unset($orderedPostTypes);

        return $newOrderedPostTypes;
    }

    /**
     * getTaxonomies function.
     *
     * @access public
     * @return void
     */
    public function getTaxonomies()
    {
        $taxonomies = get_taxonomies(['public'=>true], 'objects');

        $orderedTaxonomies = [];

        // Build list
        foreach ($taxonomies as $taxonomy) {
            $orderedTaxonomies[$taxonomy->name] = $taxonomy->label;
        }

        // Order list
        asort($orderedTaxonomies, SORT_NATURAL | SORT_FLAG_CASE);

        $newOrderedTaxonomies = [];

        foreach ($orderedTaxonomies as $taxonomy => $taxonomyData) {
            $newOrderedTaxonomies[$taxonomy] = $taxonomies[$taxonomy];
        }

        unset($taxonomies);
        unset($orderedTaxonomies);

        return $newOrderedTaxonomies;
    }

    /**
     * modifyHtaccess function.
     *
     * @access public
     * @param mixed $modifyStatus
     * @return void
     */
    public function modifyHtaccess($modifyStatus)
    {
        // Check if .htaccess is writeable
        if (defined('ABSPATH')) {
            $htaccessPath = ABSPATH.'.htaccess';

            if (defined('OSO_SUPER_CACHE_HTACCESS_PATH')) {
                $htaccessPath = OSO_SUPER_CACHE_HTACCESS_PATH.'.htaccess';
            }

            if (file_exists($htaccessPath) && is_writable($htaccessPath)) {
                $htaccessContent = file_get_contents($htaccessPath);

                // Remove OSO Super Cache Code if exists
                $htaccessContent = trim(preg_replace('/(.*?)(# BEGIN OSO Super Cache(.*)# END OSO Super Cache(.*?))/Us', '$1$4', $htaccessContent));

                $osoSuperCacheHtaccessContent = '';

                if ($modifyStatus) {
                    // Add OSO Super Cache Code
                    $osoSuperCacheHtaccessContent = "# BEGIN OSO Super Cache\n";
                    $osoSuperCacheHtaccessContent .= $this->getHtaccessDeflateCode()."\n";
                    $osoSuperCacheHtaccessContent .= $this->getHtaccessTypesCode()."\n";
                    $osoSuperCacheHtaccessContent .= $this->getHtaccessExpiresCode()."\n";
                    $osoSuperCacheHtaccessContent .= $this->getHtaccessHeadersCode()."\n";
                    $osoSuperCacheHtaccessContent .= "\n# END OSO Super Cache\n";
                }

                file_put_contents($htaccessPath, trim($osoSuperCacheHtaccessContent."\n".$htaccessContent));
            }
        }
    }

    /**
     * modifyHtaccessSecurity function.
     *
     * @access public
     * @param mixed $modifyStatus
     * @return void
     */
    public function modifyHtaccessSecurity($modifyStatus)
    {
        // Check if .htaccess is writeable
        if (defined('ABSPATH')) {
            $htaccessPath = ABSPATH.'.htaccess';

            if (defined('OSO_SUPER_CACHE_HTACCESS_PATH')) {
                $htaccessPath = OSO_SUPER_CACHE_HTACCESS_PATH.'.htaccess';
            }

            if (file_exists($htaccessPath) && is_writable($htaccessPath)) {
                $htaccessContent = file_get_contents($htaccessPath);

                // Remove OSO Super Cache Code if exists
                $htaccessContent = trim(preg_replace('/(.*?)(# BEGIN Security OSO Super Cache(.*)# END Security OSO Super Cache(.*?))/Us', '$1$4', $htaccessContent));

                $osoSuperCacheHtaccessContent = '';

                if ($modifyStatus) {
                    $osoSuperCacheHtaccessContent = "# BEGIN Security OSO Super Cache\n\n";
                    $osoSuperCacheHtaccessContent .= "<IfModule mod_headers.c>\n";

                    // Content-Security-Policy
                    if (Factory::get('Cache\Config')->get('browserSecurityContentSecurityPolicyHeader', 'active') && Factory::get('Cache\Config')->get('browserSecurityContentSecurityPolicy', 'active')) {
                        $osoSuperCacheHtaccessContent .= "Header set Content-Security-Policy \"";
                        $osoSuperCacheHtaccessContent .= rtrim(implode(" ", Factory::get('Cache\Config')->get('browserSecurityContentSecurityPolicy', 'active')), ';');
                        $osoSuperCacheHtaccessContent .= "\"\n";
                    }

                    // Referrer-Policy
                    $referrerPolicy = Factory::get('Cache\Config')->get('browserSecurityReferrerPolicy', 'active');
                    if (!empty($referrerPolicy) && $referrerPolicy != '-') {
                        $osoSuperCacheHtaccessContent .= "Header always set Referrer-Policy ".$referrerPolicy."\n";
                    }

                    // Strict-Transport-Security
                    if (Factory::get('Cache\Config')->get('browserSecurityStrictTransportSecurity', 'active')) {
                        $osoSuperCacheHtaccessContent .= "Header set Strict-Transport-Security \"max-age=631138519; includeSubDomains\"\n";
                    }

                    // X-Frame-Options
                    $browserSecurityXFrameOptions = Factory::get('Cache\Config')->get('browserSecurityXFrameOptions', 'active');

                    if ($browserSecurityXFrameOptions == 'deny') {
                        $osoSuperCacheHtaccessContent .= "Header set X-Frame-Options DENY\n";
                    }

                    if ($browserSecurityXFrameOptions == 'sameorigin') {
                        $osoSuperCacheHtaccessContent .= "Header set X-Frame-Options SAMEORIGIN\n";
                    }

                    // X-Content-Type-Options
                    if (Factory::get('Cache\Config')->get('browserSecurityXContentTypeOptions', 'active')) {
                        $osoSuperCacheHtaccessContent .= "Header set X-Content-Type-Options nosniff\n";
                    }

                    // X-XSS-Protection
                    if (Factory::get('Cache\Config')->get('browserSecurityXXSSProtection', 'active')) {
                        $osoSuperCacheHtaccessContent .= "Header set X-XSS-Protection \"1; mode=block\"\n";
                    }

                    $osoSuperCacheHtaccessContent .= "</IfModule>\n";
                    $osoSuperCacheHtaccessContent .= "\n# END Security OSO Super Cache\n";
                }

                file_put_contents($htaccessPath, trim($osoSuperCacheHtaccessContent."\n".$htaccessContent));
            }
        }
    }

    /**
     * getHtaccessDeflateCode function.
     *
     * @access public
     * @return void
     */
    public function getHtaccessDeflateCode()
    {
        $deflate = "
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE application/atom_xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/x-shockwave-flash

    <IfModule mod_expires.c>
        <FilesMatch \"\.(js|css|ico|jpe?g|gif|png|svg)$\">
            ExpiresActive on
            ExpiresDefault \"access plus 1 years\"
            SetOutputFilter DEFLATE
        </FilesMatch>
    </IfModule>
</IfModule>";

        return $deflate;
    }

    /**
     * getHtaccessTypesCode function.
     *
     * @access public
     * @return void
     */
    public function getHtaccessTypesCode()
    {
        $types = "
<IfModule mod_mime.c>
    AddType application/epub+zip .epub
    AddType application/java-archive .jar
    AddType application/javascript .js
    AddType application/json .json
    AddType application/msword .doc
    AddType application/octet-stream .arc
    AddType application/octet-stream .bin
    AddType application/ogg .ogx
    AddType application/pdf .pdf
    AddType application/rtf .rtf
    AddType application/vnd.amazon.ebook .azw
    AddType application/vnd.apple.installer+xml .mpkg
    AddType application/vnd.mozilla.xul+xml .xul
    AddType application/vnd.ms-excel .xls
    AddType application/vnd.ms-powerpoint .ppt
    AddType application/vnd.oasis.opendocument.presentation .odp
    AddType application/vnd.oasis.opendocument.spreadsheet .ods
    AddType application/vnd.oasis.opendocument.text .odt
    AddType application/vnd.visio .vsd
    AddType application/x-7z-compressed .7z
    AddType application/x-abiword .abw
    AddType application/x-bzip .bz
    AddType application/x-bzip2 .bz2
    AddType application/x-csh .csh
    AddType application/x-rar-compressed .rar
    AddType application/x-sh .sh
    AddType application/x-shockwave-flash .swf
    AddType application/x-tar .tar
    AddType application/xhtml+xml .xhtml
    AddType application/xml .xml
    AddType application/zip .zip
    AddType audio/aac .aac
    AddType audio/midi .mid .midi
    AddType audio/ogg .oga
    AddType audio/webm .weba
    AddType audio/x-wav .wav
    AddType font/ttf .ttf
    AddType font/woff .woff
    AddType font/woff2 .woff2
    AddType application/font-woff2 .woff2
    AddType image/gif .gif
    AddType image/jpeg .jpeg .jpg
    AddType image/svg+xml .svg
    AddType image/tiff .tif .tiff
    AddType image/webp .webp
    AddType image/x-icon .ico
    AddType text/calendar .ics
    AddType text/css .css
    AddType text/csv .csv
    AddType text/html .htm .html
    AddType video/mpeg .mpeg
    AddType video/ogg .ogv
    AddType video/webm .webm
    AddType video/x-msvideo .avi
</IfModule>";

        return $types;
    }

    /**
     * getHtaccessExpiresCode function.
     *
     * @access public
     * @return void
     */
    public function getHtaccessExpiresCode()
    {
        $expires = "
<IfModule mod_expires.c>
    ExpiresActive On   
    ExpiresByType font/ttf \"access plus 1 year\"
    ExpiresByType font/otf \"access plus 1 year\"
    ExpiresByType font/woff \"access plus 1 year\"
    ExpiresByType application/font-woff2 \"access plus 1 year\"
    ExpiresByType application/acad A31536000
    ExpiresByType application/arj A31536000
    ExpiresByType application/base64 A31536000
    ExpiresByType application/binhex A31536000
    ExpiresByType application/binhex4 A31536000
    ExpiresByType application/book A31536000
    ExpiresByType application/cdf A31536000
    ExpiresByType application/clariscad A31536000
    ExpiresByType application/commonground A31536000
    ExpiresByType application/drafting A31536000
    ExpiresByType application/dsptype A31536000
    ExpiresByType application/dxf A31536000
    ExpiresByType application/ecmascript A31536000
    ExpiresByType application/envoy A31536000
    ExpiresByType application/excel A31536000
    ExpiresByType application/fractals A31536000
    ExpiresByType application/freeloader A31536000
    ExpiresByType application/futuresplash A31536000
    ExpiresByType application/gnutar A31536000
    ExpiresByType application/groupwise A31536000
    ExpiresByType application/hlp A31536000
    ExpiresByType application/hta A31536000
    ExpiresByType application/i-deas A31536000
    ExpiresByType application/iges A31536000
    ExpiresByType application/inf A31536000
    ExpiresByType application/java A31536000
    ExpiresByType application/java-byte-code A31536000
    ExpiresByType application/javascript A31536000
    ExpiresByType application/lha A31536000
    ExpiresByType application/lzx A31536000
    ExpiresByType application/mac-binary A31536000
    ExpiresByType application/mac-binhex A31536000
    ExpiresByType application/mac-binhex40 A31536000
    ExpiresByType application/mac-compactpro A31536000
    ExpiresByType application/macbinary A31536000
    ExpiresByType application/marc A31536000
    ExpiresByType application/mbedlet A31536000
    ExpiresByType application/mcad A31536000
    ExpiresByType application/mime A31536000
    ExpiresByType application/mspowerpoint A31536000
    ExpiresByType application/msword A31536000
    ExpiresByType application/mswrite A31536000
    ExpiresByType application/netmc A31536000
    ExpiresByType application/octet-stream A31536000
    ExpiresByType application/oda A31536000
    ExpiresByType application/pdf A31536000
    ExpiresByType application/pkcs-12 A31536000
    ExpiresByType application/pkcs-crl A31536000
    ExpiresByType application/pkcs10 A31536000
    ExpiresByType application/pkcs7-mime A31536000
    ExpiresByType application/pkcs7-signature A31536000
    ExpiresByType application/pkix-cert A31536000
    ExpiresByType application/pkix-crl A31536000
    ExpiresByType application/plain A31536000
    ExpiresByType application/postscript A31536000
    ExpiresByType application/powerpoint A31536000
    ExpiresByType application/pro_eng A31536000
    ExpiresByType application/ringing-tones A31536000
    ExpiresByType application/rtf A31536000
    ExpiresByType application/sdp A31536000
    ExpiresByType application/sea A31536000
    ExpiresByType application/set A31536000
    ExpiresByType application/sla A31536000
    ExpiresByType application/smil A31536000
    ExpiresByType application/solids A31536000
    ExpiresByType application/sounder A31536000
    ExpiresByType application/step A31536000
    ExpiresByType application/streamingmedia A31536000
    ExpiresByType application/toolbook A31536000
    ExpiresByType application/vda A31536000
    ExpiresByType application/vnd.fdf A31536000
    ExpiresByType application/vnd.hp-hpgl A31536000
    ExpiresByType application/vnd.hp-pcl A31536000
    ExpiresByType application/vnd.ms-excel A31536000
    ExpiresByType application/vnd.ms-pki.certstore A31536000
    ExpiresByType application/vnd.ms-pki.pko A31536000
    ExpiresByType application/vnd.ms-pki.seccat A31536000
    ExpiresByType application/vnd.ms-pki.stl A31536000
    ExpiresByType application/vnd.ms-powerpoint A31536000
    ExpiresByType application/vnd.ms-project A31536000
    ExpiresByType application/vnd.rn-realmedia A31536000
    ExpiresByType application/vnd.rn-realplayer A31536000
    ExpiresByType application/vnd.wap.wmlc A31536000
    ExpiresByType application/vnd.wap.wmlscriptc A31536000
    ExpiresByType application/vnd.xara A31536000
    ExpiresByType application/vocaltec-media-desc A31536000
    ExpiresByType application/vocaltec-media-file A31536000
    ExpiresByType application/wordperfect A31536000
    ExpiresByType application/wordperfect6.0 A31536000
    ExpiresByType application/wordperfect6.1 A31536000
    ExpiresByType application/x-123 A31536000
    ExpiresByType application/x-aim A31536000
    ExpiresByType application/x-authorware-bin A31536000
    ExpiresByType application/x-authorware-map A31536000
    ExpiresByType application/x-authorware-seg A31536000
    ExpiresByType application/x-bcpio A31536000
    ExpiresByType application/x-binary A31536000
    ExpiresByType application/x-binhex40 A31536000
    ExpiresByType application/x-bsh A31536000
    ExpiresByType application/x-bytecode.elisp A31536000
    ExpiresByType application/x-bytecode.python A31536000
    ExpiresByType application/x-bzip A31536000
    ExpiresByType application/x-bzip2 A31536000
    ExpiresByType application/x-cdf A31536000
    ExpiresByType application/x-cdlink A31536000
    ExpiresByType application/x-chat A31536000
    ExpiresByType application/x-cmu-raster A31536000
    ExpiresByType application/x-cocoa A31536000
    ExpiresByType application/x-compactpro A31536000
    ExpiresByType application/x-compress A31536000
    ExpiresByType application/x-compressed A31536000
    ExpiresByType application/x-conference A31536000
    ExpiresByType application/x-cpio A31536000
    ExpiresByType application/x-cpt A31536000
    ExpiresByType application/x-csh A31536000
    ExpiresByType application/x-deepv A31536000
    ExpiresByType application/x-director A31536000
    ExpiresByType application/x-dvi A31536000
    ExpiresByType application/x-elc A31536000
    ExpiresByType application/x-envoy A31536000
    ExpiresByType application/x-esrehber A31536000
    ExpiresByType application/x-excel A31536000
    ExpiresByType application/x-frame A31536000
    ExpiresByType application/x-freelance A31536000
    ExpiresByType application/x-gsp A31536000
    ExpiresByType application/x-gss A31536000
    ExpiresByType application/x-gtar A31536000
    ExpiresByType application/x-gzip A31536000
    ExpiresByType application/x-hdf A31536000
    ExpiresByType application/x-helpfile A31536000
    ExpiresByType application/x-httpd-imap A31536000
    ExpiresByType application/x-ima A31536000
    ExpiresByType application/x-internett-signup A31536000
    ExpiresByType application/x-inventor A31536000
    ExpiresByType application/x-ip2 A31536000
    ExpiresByType application/x-java-class A31536000
    ExpiresByType application/x-java-commerce A31536000
    ExpiresByType application/x-javascript A31536000
    ExpiresByType application/x-koan A31536000
    ExpiresByType application/x-ksh A31536000
    ExpiresByType application/x-latex A31536000
    ExpiresByType application/x-lha A31536000
    ExpiresByType application/x-lisp A31536000
    ExpiresByType application/x-livescreen A31536000
    ExpiresByType application/x-lotus A31536000
    ExpiresByType application/x-lotusscreencam A31536000
    ExpiresByType application/x-lzh A31536000
    ExpiresByType application/x-lzx A31536000
    ExpiresByType application/x-mac-binhex40 A31536000
    ExpiresByType application/x-macbinary A31536000
    ExpiresByType application/x-magic-cap-package-1.0 A31536000
    ExpiresByType application/x-mathcad A31536000
    ExpiresByType application/x-meme A31536000
    ExpiresByType application/x-midi A31536000
    ExpiresByType application/x-mif A31536000
    ExpiresByType application/x-mix-transfer A31536000
    ExpiresByType application/x-mplayer2 A31536000
    ExpiresByType application/x-msexcel A31536000
    ExpiresByType application/x-mspowerpoint A31536000
    ExpiresByType application/x-navi-animation A31536000
    ExpiresByType application/x-navidoc A31536000
    ExpiresByType application/x-navimap A31536000
    ExpiresByType application/x-navistyle A31536000
    ExpiresByType application/x-netcdf A31536000
    ExpiresByType application/x-netcdf A31536000
    ExpiresByType application/x-newton-compatible-pkg A31536000
    ExpiresByType application/x-omc A31536000
    ExpiresByType application/x-omcdatamaker A31536000
    ExpiresByType application/x-omcregerator A31536000
    ExpiresByType application/x-pagemaker A31536000
    ExpiresByType application/x-pcl A31536000
    ExpiresByType application/x-pixclscript A31536000
    ExpiresByType application/x-pkcs10 A31536000
    ExpiresByType application/x-pkcs12 A31536000
    ExpiresByType application/x-pkcs7-certificates A31536000
    ExpiresByType application/x-pkcs7-certreqresp A31536000
    ExpiresByType application/x-pkcs7-mime A31536000
    ExpiresByType application/x-pkcs7-signature A31536000
    ExpiresByType application/x-pointplus A31536000
    ExpiresByType application/x-portable-anymap A31536000
    ExpiresByType application/x-project A31536000
    ExpiresByType application/x-qpro A31536000
    ExpiresByType application/x-rtf A31536000
    ExpiresByType application/x-sdp A31536000
    ExpiresByType application/x-sea A31536000
    ExpiresByType application/x-seelogo A31536000
    ExpiresByType application/x-sh A31536000
    ExpiresByType application/x-shar A31536000
    ExpiresByType application/x-shockwave-flash A31536000
    ExpiresByType application/x-sit A31536000
    ExpiresByType application/x-sprite A31536000
    ExpiresByType application/x-stuffit A31536000
    ExpiresByType application/x-sv4cpio A31536000
    ExpiresByType application/x-sv4crc A31536000
    ExpiresByType application/x-tar A31536000
    ExpiresByType application/x-tbook A31536000
    ExpiresByType application/x-tcl A31536000
    ExpiresByType application/x-tex A31536000
    ExpiresByType application/x-texinfo A31536000
    ExpiresByType application/x-troff A31536000
    ExpiresByType application/x-troff-man A31536000
    ExpiresByType application/x-troff-me A31536000
    ExpiresByType application/x-troff-ms A31536000
    ExpiresByType application/x-troff-msvideo A31536000
    ExpiresByType application/x-ustar A31536000
    ExpiresByType application/x-visio A31536000
    ExpiresByType application/x-vnd.audioexplosion.mzz A31536000
    ExpiresByType application/x-vnd.ls-xpix A31536000
    ExpiresByType application/x-vrml A31536000
    ExpiresByType application/x-wais-source A31536000
    ExpiresByType application/x-winhelp A31536000
    ExpiresByType application/x-wintalk A31536000
    ExpiresByType application/x-world A31536000
    ExpiresByType application/x-wpwin A31536000
    ExpiresByType application/x-wri A31536000
    ExpiresByType application/x-x509-ca-cert A31536000
    ExpiresByType application/x-x509-user-cert A31536000
    ExpiresByType application/x-zip-compressed A31536000
    ExpiresByType application/xml A31536000
    ExpiresByType application/zip A31536000
    ExpiresByType audio/aiff A31536000
    ExpiresByType audio/basic A31536000
    ExpiresByType audio/it A31536000
    ExpiresByType audio/make A31536000
    ExpiresByType audio/make.my.funk A31536000
    ExpiresByType audio/mid A31536000
    ExpiresByType audio/midi A31536000
    ExpiresByType audio/mod A31536000
    ExpiresByType audio/mpeg A31536000
    ExpiresByType audio/mpeg3 A31536000
    ExpiresByType audio/nspaudio A31536000
    ExpiresByType audio/s3m A31536000
    ExpiresByType audio/tsp-audio A31536000
    ExpiresByType audio/tsplayer A31536000
    ExpiresByType audio/vnd.qcelp A31536000
    ExpiresByType audio/voc A31536000
    ExpiresByType audio/voxware A31536000
    ExpiresByType audio/wav A31536000
    ExpiresByType audio/x-adpcm A31536000
    ExpiresByType audio/x-aiff A31536000
    ExpiresByType audio/x-au A31536000
    ExpiresByType audio/x-gsm A31536000
    ExpiresByType audio/x-jam A31536000
    ExpiresByType audio/x-liveaudio A31536000
    ExpiresByType audio/x-mid A31536000
    ExpiresByType audio/x-midi A31536000
    ExpiresByType audio/x-mod A31536000
    ExpiresByType audio/x-mpeg A31536000
    ExpiresByType audio/x-mpeg-3 A31536000
    ExpiresByType audio/x-mpequrl A31536000
    ExpiresByType audio/x-nspaudio A31536000
    ExpiresByType audio/x-pn-realaudio A31536000
    ExpiresByType audio/x-pn-realaudio-plugin A31536000
    ExpiresByType audio/x-psid A31536000
    ExpiresByType audio/x-realaudio A31536000
    ExpiresByType audio/x-twinvq A31536000
    ExpiresByType audio/x-twinvq-plugin A31536000
    ExpiresByType audio/x-vnd.audioexplosion.mjuicemediafile A31536000
    ExpiresByType audio/x-voc A31536000
    ExpiresByType audio/x-wav A31536000
    ExpiresByType audio/xm A31536000
    ExpiresByType chemical/x-pdb A31536000
    ExpiresByType drawing/x-dwf A31536000
    ExpiresByType i-world/i-vrml A31536000
    ExpiresByType image/bmp A31536000
    ExpiresByType image/cmu-raster A31536000
    ExpiresByType image/fif A31536000
    ExpiresByType image/g3fax A31536000
    ExpiresByType image/gif A31536000
    ExpiresByType image/ief A31536000
    ExpiresByType image/jpeg A31536000
    ExpiresByType image/jutvision A31536000
    ExpiresByType image/naplps A31536000
    ExpiresByType image/pict A31536000
    ExpiresByType image/pjpeg A31536000
    ExpiresByType image/png A31536000
    ExpiresByType image/svg+xml A31536000
    ExpiresByType image/tiff A31536000
    ExpiresByType image/vasa A31536000
    ExpiresByType image/vnd.dwg A31536000
    ExpiresByType image/vnd.fpx A31536000
    ExpiresByType image/vnd.net-fpx A31536000
    ExpiresByType image/vnd.rn-realflash A31536000
    ExpiresByType image/vnd.rn-realpix A31536000
    ExpiresByType image/vnd.wap.wbmp A31536000
    ExpiresByType image/vnd.xiff A31536000
    ExpiresByType image/webp A31536000
    ExpiresByType image/x-cmu-raster A31536000
    ExpiresByType image/x-dwg A31536000
    ExpiresByType image/x-icon A31536000
    ExpiresByType image/x-jg A31536000
    ExpiresByType image/x-jps A31536000
    ExpiresByType image/x-niff A31536000
    ExpiresByType image/x-niff A31536000
    ExpiresByType image/x-pcx A31536000
    ExpiresByType image/x-pict A31536000
    ExpiresByType image/x-portable-anymap A31536000
    ExpiresByType image/x-portable-bitmap A31536000
    ExpiresByType image/x-portable-graymap A31536000
    ExpiresByType image/x-portable-greymap A31536000
    ExpiresByType image/x-portable-pixmap A31536000
    ExpiresByType image/x-quicktime A31536000
    ExpiresByType image/x-rgb A31536000
    ExpiresByType image/x-tiff A31536000
    ExpiresByType image/x-windows-bmp A31536000
    ExpiresByType image/x-xbitmap A31536000
    ExpiresByType image/x-xbm A31536000
    ExpiresByType image/x-xpixmap A31536000
    ExpiresByType image/x-xwd A31536000
    ExpiresByType image/x-xwindowdump A31536000
    ExpiresByType image/xbm A31536000
    ExpiresByType image/xpm A31536000
    ExpiresByType message/rfc822 A31536000
    ExpiresByType model/iges A31536000
    ExpiresByType model/vnd.dwf A31536000
    ExpiresByType model/vrml A31536000
    ExpiresByType model/x-pov A31536000
    ExpiresByType multipart/x-gzip A31536000
    ExpiresByType multipart/x-ustar A31536000
    ExpiresByType multipart/x-zip A31536000
    ExpiresByType music/crescendo A31536000
    ExpiresByType music/x-karaoke A31536000
    ExpiresByType paleovu/x-pv A31536000
    ExpiresByType text/asp A31536000
    ExpiresByType text/css A31536000
    ExpiresByType text/ecmascript A31536000
    ExpiresByType text/javascript A31536000
    ExpiresByType text/mcf A31536000
    ExpiresByType text/pascal A31536000
    ExpiresByType text/plain A31536000
    ExpiresByType text/richtext A31536000
    ExpiresByType text/scriplet A31536000
    ExpiresByType text/sgml A31536000
    ExpiresByType text/tab-separated-values A31536000
    ExpiresByType text/uri-list A31536000
    ExpiresByType text/vnd.abc A31536000
    ExpiresByType text/vnd.fmi.flexstor A31536000
    ExpiresByType text/vnd.rn-realtext A31536000
    ExpiresByType text/vnd.wap.wml A31536000
    ExpiresByType text/vnd.wap.wmlscript A31536000
    ExpiresByType text/webviewhtml A31536000
    ExpiresByType text/x-asm A31536000
    ExpiresByType text/x-audiosoft-intra A31536000
    ExpiresByType text/x-c A31536000
    ExpiresByType text/x-component A31536000
    ExpiresByType text/x-fortran A31536000
    ExpiresByType text/x-h A31536000
    ExpiresByType text/x-java-source A31536000
    ExpiresByType text/x-la-asf A31536000
    ExpiresByType text/x-m A31536000
    ExpiresByType text/x-pascal A31536000
    ExpiresByType text/x-script A31536000
    ExpiresByType text/x-script.csh A31536000
    ExpiresByType text/x-script.elisp A31536000
    ExpiresByType text/x-script.guile A31536000
    ExpiresByType text/x-script.ksh A31536000
    ExpiresByType text/x-script.lisp A31536000
    ExpiresByType text/x-script.perl A31536000
    ExpiresByType text/x-script.perl-module A31536000
    ExpiresByType text/x-script.phyton A31536000
    ExpiresByType text/x-script.rexx A31536000
    ExpiresByType text/x-script.scheme A31536000
    ExpiresByType text/x-script.sh A31536000
    ExpiresByType text/x-script.tcl A31536000
    ExpiresByType text/x-script.tcsh A31536000
    ExpiresByType text/x-script.zsh A31536000
    ExpiresByType text/x-server-parsed-html A31536000
    ExpiresByType text/x-setext A31536000
    ExpiresByType text/x-sgml A31536000
    ExpiresByType text/x-speech A31536000
    ExpiresByType text/x-uil A31536000
    ExpiresByType text/x-uuencode A31536000
    ExpiresByType text/x-vcalendar A31536000
    ExpiresByType text/xml A31536000
    ExpiresByType video/animaflex A31536000
    ExpiresByType video/avi A31536000
    ExpiresByType video/avs-video A31536000
    ExpiresByType video/dl A31536000
    ExpiresByType video/fli A31536000
    ExpiresByType video/gl A31536000
    ExpiresByType video/mpeg A31536000
    ExpiresByType video/msvideo A31536000
    ExpiresByType video/quicktime A31536000
    ExpiresByType video/vdo A31536000
    ExpiresByType video/vivo A31536000
    ExpiresByType video/vnd.rn-realvideo A31536000
    ExpiresByType video/vnd.vivo A31536000
    ExpiresByType video/vosaic A31536000
    ExpiresByType video/x-amt-demorun A31536000
    ExpiresByType video/x-amt-showrun A31536000
    ExpiresByType video/x-atomic3d-feature A31536000
    ExpiresByType video/x-dl A31536000
    ExpiresByType video/x-dv A31536000
    ExpiresByType video/x-fli A31536000
    ExpiresByType video/x-gl A31536000
    ExpiresByType video/x-isvideo A31536000
    ExpiresByType video/x-motion-jpeg A31536000
    ExpiresByType video/x-mpeg A31536000
    ExpiresByType video/x-mpeq2a A31536000
    ExpiresByType video/x-ms-asf A31536000
    ExpiresByType video/x-ms-asf-plugin A31536000
    ExpiresByType video/x-msvideo A31536000
    ExpiresByType video/x-qtc A31536000
    ExpiresByType video/x-scm A31536000
    ExpiresByType video/x-sgi-movie A31536000
    ExpiresByType windows/metafile A31536000
    ExpiresByType www/mime A31536000
    ExpiresByType x-conference/x-cooltalk A31536000
    ExpiresByType x-music/x-midi A31536000
    ExpiresByType x-world/x-3dmf A31536000
    ExpiresByType x-world/x-svr A31536000
    ExpiresByType x-world/x-vrml A31536000
    ExpiresByType x-world/x-vrt A31536000
    ExpiresByType xgl/drawing A31536000
    ExpiresByType xgl/movie A31536000
</IfModule>";

        return $expires;
    }

    /**
     * getHtaccessHeadersCode function.
     *
     * @access public
     * @return void
     */
    public function getHtaccessHeadersCode()
    {
        $headers = "
<IfModule mod_headers.c>
    <FilesMatch \"\.(js|css|ico|jpe?g|gif|png|svg)$\">\n";

        if (Factory::get('Cache\Config')->get('browserCacheSetETag', 'inactive')) {
            $headers .= "\t\tFileETag MTime Size\n";
        }

        if (Factory::get('Cache\Config')->get('browserCacheSetControlHeader', 'inactive')) {
            if (Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') == 'public') {
                $headers .= "\t\tHeader set Cache-Control \"public\"\n";
            }

            if (Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') == 'private') {
                $headers .= "\t\tHeader set Cache-Control \"private\"\n";
            }

            if (Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') == 'public-max-age') {
                $headers .= "\t\tHeader set Cache-Control \"max-age=".Factory::get('Cache\Config')->get('browserCacheControlHeaderExpiresLifetime', 'inactive').", public\"\n";
            }

            if (Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') == 'private-max-age') {
                $headers .= "\t\tHeader set Cache-Control \"max-age=".Factory::get('Cache\Config')->get('browserCacheControlHeaderExpiresLifetime', 'inactive').", private\"\n";
            }

            if (Factory::get('Cache\Config')->get('browserCacheControlPolicy', 'inactive') == 'no-cache') {
                $headers .= "\t\tHeader set Cache-Control \"no-cache, no-store, max-age=0\"\n";
            }
        }

        if (Factory::get('Cache\Config')->get('browserCacheSetOSOSuperCacheTag', 'inactive')) {
            $headers .= "\t\tHeader set X-Powered-By \"OSO-Super-Cache\"\n";
        }

        $headers .= "\t</FilesMatch>
</IfModule>";

        // CDN CORS setting
        if (Factory::get('Cache\Config')->get('cdn')) {
            $headers .= "\n
<IfModule mod_headers.c>
    <FilesMatch \"\.(ttf|ttc|otf|eot|woff|woff2|font.css|css|js|gif|png|jpe?g|svg|svgz|ico|webp)$\">
        Header set Access-Control-Allow-Origin \"*\"
    </FilesMatch>
</IfModule>
";
        }

        return $headers;
    }
}
