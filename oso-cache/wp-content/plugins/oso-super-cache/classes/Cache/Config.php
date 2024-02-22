<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Config
{
    private static $instance;

    private $config;
    private $cacheActivated = 'no';
    private $configChanged;

    public static function getInstance ()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        // Get all config values
        $this->loadConfig('active');

        // Get cache status
        if ($this->get('cacheActivated') == 'yes') {
            $this->cacheActivated = 'yes';
        } else {
            $this->cacheActivated = 'no';
        }
    }

    public function loadConfig($configType = 'active')
    {
        $this->config[$configType] = $this->getConfig($configType);

        return $this->config[$configType];
    }

    public function getConfig($configType = 'active')
    {
        $config = [];

        if ($configType == 'inactive') {
            $config = get_option('OSOSuperCacheConfigInactive', 'does not exist');

            if ($config === 'does not exist') {
                $config = $this->getConfig('active');
            }
        } else {
            $config = get_option('OSOSuperCacheConfigActive', 'does not exist');

            if ($config === 'does not exist') {
                $config = $this->defaultConfig();
            }
        }

        return $config;
    }

    public function cacheActivated()
    {
        return $this->cacheActivated === 'yes' ? true : false;
    }

    public function getCurrentPreset()
    {
        return get_option('OSOSuperCacheConfigPreset', 'default');
    }

    public function setCurrentPreset($preset)
    {
        return update_option('OSOSuperCacheConfigPreset', $preset, 'no');
    }

    public function defaultConfig()
    {
        return [
            'cacheActivated'=>'no',

            /* Preloader & Optimization */
            'preloaderActivated'=>true, // true = active, false = inactive
            'maxSimultaneousTasks'=>5, // How many tasks can be run at the same time to cache and optimize a requested non-cached page

            /* Pages and Feed */
            'loggedInUserGetCachedPages'=>true,
            'cacheLateInitialization'=>false, // late or super-late
            'cacheSeparateFileByDeviceType'=>false, // mobile, mobile+tablet
            'cache404Pages'=>true,
            'cacheFeeds'=>true,
            'cachePagesWithQueryStrings'=>false,
            'cacheSearchResults'=>true,
            'cacheCronService'=>false,
            'cacheCronInterval'=>15,

            /* Cache exceptions */
            'cacheDontCachePagesContainQuery'=>[
            ],
            'cacheDontCachePagesContainPath'=>[
                'wp-login.php',
                '/([a-z\-\_0-9]*)sitemap([a-z\-\_0-9]*)\.xml',
                '^(.*)/amp(/)?$',
                '([a-z\-\_0-9]*)\.rss',
                '(/page/[0-9]{2,})',
            ],
            'cacheDontCachePagesOfPostType'=>[
            ],
            'cacheDontCachePagesOfTaxonomy'=>[
            ],
            'cacheDontUseCacheWhenUserAgent'=>[
            ],
            'cacheDontUseCacheWhenCookie'=>[
            ],

            /* Cache lifetimes */
            'cacheLifetime'=>[
                'home'=>604800,
                'archives'=>[
                    '-'=>604800,
                ],
                'postType'=>[
                    '-'=>604800,
                ],
                'feed'=>86400,
                '404'=>604800,
                'garbage'=>31536000,
            ],
            'cacheRefreshCacheAfterPublish'=>true,
            'cacheRefreshHomeCacheAfterPublish'=>true,
            'cacheRefreshArchiveCacheAfterPublish'=>true,
            'cacheRefreshFeedCacheAfterPublish'=>true,
            'cacheRefreshCacheAfterComment'=>true,
            'cacheShowMetaBox'=>true,
            'cacheShowRefreshOptionInQuickEdit'=>true,

            'cacheGzipOutput'=>false,
            'cacheGzipCompressionLevel'=>6,

            /* Styles */
            'stylesMerge'=>true,
            'stylesExcludeStyleTags'=>true,
            'stylesMinify'=>true,
            'stylesPreloadTag'=>true,
            'stylesGzipOutput'=>false,
            'stylesGzipCompressionLevel'=>6,
            'stylesOptimizeGoogleFonts'=>true,
//            'stylesPosition'=>'after',
            'stylesLocation'=>'header',
            'stylesExternalStylesPosition'=>'after',

            /* Page Optimization */
            'excludedScripts'=>[],
            'excludedStyles'=>[],

            /* Scripts */
            'scriptsMerge'=>true,
            'scriptsSmartBundles'=>false,
            'scriptsMinify'=>true,
            'scriptsDefer'=>false,
            'scriptsPreloadTag'=>true,
            'scriptsFixSemicolon'=>true,
            'scriptsGZIPOutput'=>false,
            'scriptsGzipCompressionLevel'=>6,
            'scriptsLocation'=>'footer',
            'scriptsExternalScriptsPosition'=>'before', // after: after the local scripts, before: before the local scripts

            /* Images */
            'imagesLazyLoad' => false,
            'imagesLazyLoadExclude' => [],

            /* DNS Prefetch - only works for Scripts or Styles if merging is activated */
            'dnsPrefetch'=>true,

            /* Minification */
            'minifyRemoveHTMLComments'=>true,
            'minifyRemoveWhitespace'=>true,

            /* Fragment Caching */
            'fragmentCaching'=>false,
            'fragmentCachingMaskPhrase'=>'',

            /* Browser Cache */
            'browserCacheHeaderManagementOnPages'=>true,
            'browserCacheSetControlHeader'=>true,
            'browserCacheControlHeaderExpiresLifetime'=>31536000, // 365 days
            'browserCacheControlPolicy'=>'public-max-age',
            'browserCacheSetLastModified'=>true,
            'browserCacheSetETag'=>true,
            'browserCacheSetOSOSuperCacheTag'=>true,
            'browserCacheModifyHtaccess'=>true,

            /* Browser Security */
            'browserSecurityHeader'=>false,
            'browserSecurityContentSecurityPolicyHeader'=>false,
            'browserSecurityContentSecurityPolicy'=>[
                'default-src \'self\';',
                'script-src \'self\' \'unsafe-inline\' *.wp.com *.wordpress.com *.google-analytics.com *.googleapis.com;',
                'style-src \'self\' \'unsafe-inline\' *.wp.com *.wordpress.com *.googleapis.com data:;',
                'font-src \'self\' *.googleapis.com fonts.gstatic.com data:;',
                'img-src \'self\' *.w.org *.wp.com *.wordpress.com *.google-analytics.com *.gravatar.com data:;',
            ],
            'browserSecurityReferrerPolicy'=>'-',
            'browserSecurityStrictTransportSecurity'=>false,
            'browserSecurityXFrameOptions'=>'disabled',
            'browserSecurityXContentTypeOptions'=>false,
            'browserSecurityXXSSProtection'=>false,

            /* CDN */
            'cdn'=>false,
            'cdnProvider'=>'CDNOther',
            'cdnURL'=>'',

            /* Miscellaneous */
            'miscellaneousDisableEmojis'=>true,
            'miscellaneousDisableGenerator'=>true,
            'miscellaneousDisableManifest'=>true,
            'miscellaneousDisableFeeds'=>false,
            'miscellaneousDisableRSD'=>false,
            'miscellaneousDisableRESTAPI'=>false,
            'miscellaneousDisableOEmbed'=>false,
            'miscellaneousDisableTPPSliderRevolutionGenerator'=>false,
            'miscellaneousDisableTPPLayerSliderGenerator'=>false,
            'miscellaneousDisableTPPVisualComposerGenerator'=>false,
            'miscellaneousDisableOsoSuperCacheRefreshCacheNotice'=>true,
            'miscellaneousDisableOsoSuperCacheToolbarMenuItem'=>false,
            'miscellaneousNginx'=>false,

            /* Debug */
            'debugAddCacheInformation'=>true,
        ];
    }

    public function presetOnlyPages()
    {
        $config = $this->defaultConfig();
        $config['stylesMerge']                  = false;
        $config['scriptsMerge']                 = false;

        return $config;
    }

    public function presetEcommerce()
    {
        $config = $this->defaultConfig();
        $config['loggedInUserGetCachedPages']       = false;
        $config['cacheLateInitialization']          = 'late';
        $config['cacheDontCachePagesContainPath']   = [
            'wp-login.php',
            '/([a-z\-\_0-9]*)sitemap([a-z\-\_0-9]*)\.xml',
            '^(.*)/amp(/)?$',
            '([a-z\-\_0-9]*)\.rss',
            '(/page/[0-9]{2,})',
            '/basket',
            '/cart',
            '/checkout',
            '/shipping',
            '/payment',
            '/account',
            '/my-account',
        ];

        return $config;
    }

    public function presetMagazine()
    {
        $config = $this->defaultConfig();
        $config['cacheLifetime']['home'] = 900;
        $config['cacheLifetime']['archives']['-'] = 604800;
        $config['cacheLifetime']['postType']['-'] = 604800;
        $config['cacheLifetime']['feed'] = 3600;

        return $config;
    }

    public function presetCorporate()
    {
        $config = $this->defaultConfig();
        $config['cacheLifetime']['home'] = 604800;
        $config['cacheLifetime']['archives']['-'] = 604800;
        $config['cacheLifetime']['postType']['-'] = 2592000;
        $config['cacheLifetime']['feed'] = 86400;

        return $config;
    }

    public function presetTestCSS()
    {
        // Test CSS only
        $config = $this->defaultConfig();
        $config['minifyRemoveHTMLComments'] = false;
        $config['minifyRemoveWhitespace'] = false;
        $config['scriptsMerge'] = false;
        $config['scriptsMinify'] = false;
        $config['scriptsDefer'] = false;
        $config['dnsPrefetch'] = false;

        return $config;
    }

    public function presetTestJS()
    {
        // Test JS only
        $config = $this->defaultConfig();
        $config['minifyRemoveHTMLComments'] = false;
        $config['minifyRemoveWhitespace'] = false;
        $config['scriptsMinify'] = false; // Sometimes minifaction can break correct javascript
        $config['stylesMerge'] = false;
        $config['stylesMinify'] = false;
        $config['dnsPrefetch'] = false;

        return $config;
    }

    /**
     * get function.
     *
     * @access public
     * @param mixed $configKey (default: null)
     * @param string $configType (default: 'active')
     * @return void
     */
    public function get($configKey = null, $configType = 'active')
    {
        // Get complete config
        if (empty($configKey)) {
            if (!empty($this->config[$configType])) {
                return $this->config[$configType];
            } else {
                return false;
            }
        } else {
            if (isset($this->config[$configType][$configKey])) {
                return $this->config[$configType][$configKey];
            } else {
                // Fallback - if $configType is "inactive" and we have no "inactive" value, we try to get the "active" value
                if ($configType == 'inactive' && isset($this->config['active'][$configKey])) {
                    return $this->config['active'][$configKey];
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * saveConfig function.
     *
     * @access public
     * @param mixed $configData
     * @param string $configType (default: 'active')
     * @param bool $setChangedFlag (default: true)
     * @return void
     */
    public function saveConfig($configData, $configType = 'active', $setChangedFlag = true)
    {
        if ($configType == 'active') {
            update_option('OSOSuperCacheConfigActive', $configData, 'no');
        } else {
            update_option('OSOSuperCacheConfigInactive', $configData, 'no');
        }

        if ($setChangedFlag) {
            update_option('OSOSuperCacheConfigChanged', 1, 'no');
        } else {
            update_option('OSOSuperCacheConfigChanged', 0, 'no');
        }

        // Make a notification, that the current config is not yet active
        $this->getConfigChangedStatus();

        // Reload current config
        $this->loadConfig('inactive');
        $this->loadConfig('active');
    }

    /**
     * getConfigChangedStatus function.
     *
     * @access public
     * @return void
     */
    public function getConfigChangedStatus()
    {
        $this->configChanged = get_option('OSOSuperCacheConfigChanged', 0);

        return $this->configChanged;
    }
}
