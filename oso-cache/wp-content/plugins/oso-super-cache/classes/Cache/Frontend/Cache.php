<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Cache
{
    private static $instance;
    private $cacheTaskToken = '';

    protected $cacheFolder;
    protected $subCacheFolder;

    protected $outputBuffer;

    public $bufferActive = false;

    public static function getInstance ()
    {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->cacheFolder = WP_CONTENT_DIR.'/cache/oso_super_cache';

        // Check if main cache folders exists
        if (!file_exists($this->cacheFolder)) {
            mkdir($this->cacheFolder);
        }

        // Support for multisites
        $currentBlogId = Factory::get('Cache\Frontend\Resolver')->getCurrentBlogId();

        if (!empty($currentBlogId)) {
            $this->cacheFolder = $this->cacheFolder.'/'.$currentBlogId;

            if (!file_exists($this->cacheFolder)) {
                mkdir($this->cacheFolder);
            }
        }

        if (!file_exists($this->cacheFolder.'/page/')) {
            mkdir($this->cacheFolder.'/page/');
        }

        if (!file_exists($this->cacheFolder.'/js/')) {
            mkdir($this->cacheFolder.'/js/');
        }

        if (!file_exists($this->cacheFolder.'/css/')) {
            mkdir($this->cacheFolder.'/css/');
        }

        // Check if .htaccess exists
        if (!file_exists($this->cacheFolder.'/page/.htaccess')) {
            file_put_contents($this->cacheFolder.'/page/.htaccess', "<Files *.php>\nOrder Deny,Allow\nDeny from all\n</Files>");
        }
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    /**
     * getMainCacheFolderPath function.
     *
     * @access public
     * @return void
     */
    public function getMainCacheFolderPath()
    {
        return $this->cacheFolder;
    }

    /**
     * getSubCacheFolder function.
     *
     * @access public
     * @return void
     */
    public function getSubCacheFolder()
    {
        $subCacheFolder = '';

        // Don't use hash if page is 404
        if (!Factory::get('Cache\Frontend\Resolver')->getCondition('is_404')) {
            $subCacheFolder = '/'.substr(Factory::get('Cache\Frontend\Resolver')->getHash(), 0, 2);
        }

        return $subCacheFolder;
    }

    /**
     * getCacheFilename function.
     *
     * @access public
     * @return void
     */
    public function getCacheFilename()
    {
        $getHash = Factory::get('Cache\Frontend\Resolver')->getHash();

        // Don't use hash if page is 404
        if (Factory::get('Cache\Frontend\Resolver')->getCondition('is_404')) {
            $getHash = '404';
        }

        $suffix = Factory::get('Cache\Frontend\Resolver')->isHTTPS() ? '_https' : '';

        $cacheFilename = $getHash.$suffix.'.php';

        return $cacheFilename;
    }

    /**
     * saveBuffer function.
     *
     * @access public
     * @return void
     */
    public function saveBuffer($buffer = null)
    {
        //! TODO - optimize handle outputBuffer since a cached file will always be loaded. endBuffer only necessary when page is not cached
        if ($this->bufferActive) {

            $pageContent = '';

            if (Factory::get('Cache\Config')->get('fragmentCaching') == false) {
                $this->outputBuffer = $buffer;
            } else {
                $this->outputBuffer = ob_get_contents();
            }

            // Detect if buffer is empty (e.g. when a header redirection was set by 3rd party plugin)
            if (Factory::get('Cache\Tools')->getStringLength($this->outputBuffer) < 255) {
                echo $this->outputBuffer;
            } else {

                Factory::get('Cache\Init')->runtimeEnd = microtime(1);

                // Detect if current site is a feed
                if (Factory::get('Cache\Frontend\Resolver')->getCondition('is_feed')) {
                    $pageContent = $this->outputBuffer;

                    Factory::get('Cache\Frontend\Feed')->prepareCacheContent($pageContent);
                } else {
                    // If page should be cached and saved
                    if (Factory::get('Cache\Frontend\HandleRequest')->cacheRequest) {

                        // Exclude sections
                        Factory::get('Cache\Frontend\Optimizer')->excludeSection($this->outputBuffer);

                        // Check if we should remove html comments or whitespace
                        if (Factory::get('Cache\Config')->get('minifyRemoveHTMLComments') || Factory::get('Cache\Config')->get('minifyRemoveWhitespace')) {

                            if (Factory::get('Cache\Debug')->isDebugEnabled() == false) {
                                Factory::get('Cache\Frontend\Minify')->cleanHTML($this->outputBuffer);
                            }
                        }

                        // Collect and merge styles and scripts
                        Factory::get('Cache\Frontend\Optimizer')->optimize($this->outputBuffer);

                        // Defer JS
                        if (Factory::get('Cache\Config')->get('scriptsDefer') && Factory::get('Cache\Config')->get('scriptsMerge')) {
                            //! Still necessary due excluded inline scripts
                            Factory::get('Cache\Frontend\Scripts')->handleDeferOnInlineScript($this->outputBuffer);
                        }

                        $pageContent = $this->outputBuffer;

                        if (Factory::get('Cache\Config')->get('fragmentCaching')) {
                            // Parse and execute fragments for non-cached version
                            Factory::get('Cache\Frontend\FragmentCaching')->parseContentAndExecuteFragments($this->outputBuffer);

                            // Parse fragments for cached version
                            Factory::get('Cache\Frontend\FragmentCaching')->parseContentForFragments($pageContent);
                        }

                        // Mask xml-tags
                        Factory::get('Cache\Frontend\Optimizer')->maskXMLTags($pageContent);

                        // When fragment caching is active, we have to add </body></html> at the end of our buffer
                        if (Factory::get('Cache\Config')->get('fragmentCaching') == true) {
                            $pageContent .= "\n</body></html>";
                        }

                        // Check if CDN is active
                        if (Factory::get('Cache\Config')->get('cdn')) {
                            Factory::get('Cache\Frontend\CDN')->processHTML($pageContent);
                            /*
                                For performance reasons only in the cached file all URLs
                                are replace with the CDN URLs. Same with lazyLoad, only in the
                                cached version img-tags are modified.
                            */
                        }

                        // Image optimization
                        if (Factory::get('Cache\Config')->get('imagesLazyLoad')) {
                            Factory::get('Cache\Frontend\Images')->optimize($pageContent);
                        }

                        // Re-Insert sections
                        Factory::get('Cache\Frontend\Optimizer')->reInsertSection($pageContent);

                        // Re-Insert sections
                        Factory::get('Cache\Frontend\Optimizer')->reInsertSection($this->outputBuffer, true);

                        // Parse localized script data into the document before first <script>-tag
                        Factory::get('Cache\Frontend\Optimizer')->placeLocalizedScriptData($this->outputBuffer, false);

                    } elseif (Factory::get('Cache\Frontend\HandleRequest')->cacheRequestButDontSave) {
                        // If page should be parsed, but not saved
                        if (Factory::get('Cache\Config')->get('fragmentCaching')) {
                            // Parse and execute fragments for non-cached version
                            Factory::get('Cache\Frontend\FragmentCaching')->parseContentAndExecuteFragments($this->outputBuffer);

                            // Check if CDN is active
                            if (Factory::get('Cache\Config')->get('cdn')) {
                                Factory::get('Cache\Frontend\CDN')->processHTML($this->outputBuffer);
                            }

                            // Parse localized script data into the document not necessary because default wp_localize_script routine will be used in this case
                        }
                    }
                }

                #Factory::get('Cache\Init')->runtimeEnd = microtime(1);

                $debugDataCacheFile = '';
                $debugDataBuffer = '';

                if (Factory::get('Cache\Frontend\HandleRequest')->cacheRequest) {

                    if (Factory::get('Cache\Config')->get('debugAddCacheInformation')) {
                        $debugDataCacheFile = "\n".'<?php $__OSO_SUPER_CACHE_RUNTIME = floatval("'.Factory::get('Cache\Init')->getTotalRuntime().'");  ?>';
                        $debugDataCacheFile .= "\n".'<?php echo str_replace("{{\$_runtime}}", $__OSO_SUPER_CACHE_RUNTIME, $this->getCacheInfo()); ?>';
                    }

                    // Parse localized script data into the document before first <script>-tag
                    $pageContentCacheFile = $pageContent;

                    Factory::get('Cache\Frontend\Optimizer')->placeLocalizedScriptData($pageContentCacheFile);

                    $this->createCachefile($this->getCacheFilename(), $pageContentCacheFile.$debugDataCacheFile, 'page', $this->getSubCacheFolder());

                    unset($pageContentCacheFile);
                }

                // Unregister cache task
                $this->unregisterCacheTask();

                // When fragmentCaching is disabled, we have to save current page data, because finishCaching() is not executed
                if (Factory::get('Cache\Frontend\HandleRequest')->cacheRequestButDontSave == false && Factory::get('Cache\Config')->get('fragmentCaching') == false) {

                    if (Factory::get('Cache\Config')->get('debugAddCacheInformation')) {
                        $debugDataBuffer = eval('return str_replace("{{\$_runtime}}", \OSOSuperCache\Factory::get(\'Cache\Init\')->getTotalRuntime(), $this->getCacheInfo());');
                    }

                    Factory::get('Cache\Frontend\Resolver')->savePageData();

                    // Parse localized script data into the document before first <script>-tag
                    Factory::get('Cache\Frontend\Optimizer')->placeLocalizedScriptData($pageContent, false);

                    // Send buffer - gzip not possible at this moment
                    return $pageContent.$debugDataBuffer;
                }
            }

            // Free resources
            unset($pageContent);
        }
    }

    /**
     * createCachefile function.
     *
     * @access public
     * @param mixed $filename
     * @param mixed $content
     * @param mixed $cacheType
     * @param string $subCacheFolder (default: '')
     * @return void
     */
    public function createCachefile($filename, $content, $cacheType, $subCacheFolder = '')
    {
        $cacheTypeFolder = $this->getFolderByCacheType($cacheType);

        // Check if cache directory exists
        $canWriteToFolder = $this->createCacheDirectory($cacheTypeFolder.$subCacheFolder);

        if ($canWriteToFolder) {
            file_put_contents($this->getMainCacheFolderPath().$cacheTypeFolder.$subCacheFolder.'/'.$filename, $content);
        }
    }

    /**
     * createCacheDirectory function.
     *
     * @access public
     * @param mixed $folder
     * @return void
     */
    public function createCacheDirectory($folder)
    {
        $createFolderStatus = false;

        if (!file_exists($this->getMainCacheFolderPath().$folder.'/')) {
            $createFolderStatus = mkdir($this->getMainCacheFolderPath().$folder.'/', 0777, true);
        } else {
            $createFolderStatus = true;
        }

        return $createFolderStatus;
    }

    /**
     * startBuffer function.
     *
     * @access public
     * @return void
     */
    public function startBuffer()
    {
        $this->bufferActive = true;

        if (Factory::get('Cache\Config')->get('fragmentCaching')) {
            ob_start();
        } else {
            ob_start([$this, 'saveBuffer']);
        }
    }

    /**
     * endBuffer function.
     *
     * @access public
     * @return void
     */
    public function endBuffer($sendBuffer = false)
    {
        $this->bufferActive = false;

        ob_end_clean();

        if ($sendBuffer) {
            echo $this->outputBuffer;
        }

        unset($this->outputBuffer);
    }

    /**
     * loadCacheFile function.
     *
     * @access public
     * @return void
     */
    public function loadCacheFile()
    {
        $cacheFilename = $this->getCacheFilename();
        $subCacheFolder = $this->getSubCacheFolder();

        if ($this->checkIfCacheExists($cacheFilename, 'page', $subCacheFolder)) {
            $filenameWithPath = $this->getMainCacheFolderPath().$this->getFolderByCacheType('page').$subCacheFolder.'/'.$cacheFilename;

            // Check if header management is active for non css and js files
            if (Factory::get('Cache\Config')->get('browserCacheHeaderManagementOnPages')) {
                // Header management
                $headerConfig = Factory::get('Cache\Frontend\Header')->getHeaderRelatedConfigs();

                // Replace default max lifetime with post type specific lifetime
                $headerConfig['browserCacheControlHeaderExpiresLifetime'] = Factory::get('Cache\Frontend\Resolver')->getMaxLifetime();

                // Set config for header
                Factory::get('Cache\Frontend\Header')->setConfig($headerConfig);

                // Send charset
                Factory::get('Cache\Frontend\Header')->getContentTypeHeaderCode('text/html');

                // Return header
                Factory::get('Cache\Frontend\Header')->getHeader($filenameWithPath);

                // Send browser the information to prefetch dns
                if (Factory::get('Cache\Config')->get('dnsPrefetch')) {
                    Factory::get('Cache\Frontend\Header')->getDNSPrefetchHeader();
                }
            }

            // Check if 404, if yes, send header
            if (Factory::get('Cache\Frontend\Resolver')->getCondition('is_404')) {
                header('HTTP/1.0 404 Not Found');
            }

            // Check if cached file should be gzip
            if (!empty(Factory::get('Cache\Config')->get('cacheGzipOutput'))) {
                ini_set('zlib.output_compression_level', Factory::get('Cache\Config')->get('cacheGzipCompressionLevel'));

                if (ini_get('zlib.output_compression')) {
                    ob_start();
                } else {
                    ob_start('ob_gzhandler');
                }
            }

            $cacheTypeFolder = $this->getFolderByCacheType('page');

            include_once($filenameWithPath);

            // Update runtime
            Factory::get('Cache\Init')->runtimeEnd = microtime(1);

            Factory::get('Cache\Frontend\Resolver')->updateRuntimeWithCache();

            if (!empty(Factory::get('Cache\Config')->get('cacheGzipOutput'))) {
                ob_end_flush();
            }

            exit;
        }
    }

    /**
     * getFolderByCacheType function.
     *
     * @access public
     * @param mixed $cacheType
     * @return void
     */
    public function getFolderByCacheType($cacheType)
    {
        $cacheTypeFolder = '/page';

        if ($cacheType == 'page') {
            $cacheTypeFolder = '/page';
        } elseif ($cacheType == 'js') {
            $cacheTypeFolder = '/js';
        } elseif ($cacheType == 'css') {
            $cacheTypeFolder = '/css';
        }

        return $cacheTypeFolder;
    }

    /**
     * checkIfCacheExists function.
     *
     * @access public
     * @param mixed $filename
     * @param mixed $cacheType
     * @param string $subCacheFolder (default: '')
     * @return void
     */
    public function checkIfCacheExists($filename, $cacheType, $subCacheFolder = '')
    {
        $cacheTypeFolder = $this->getFolderByCacheType($cacheType);

        if (file_exists($this->getMainCacheFolderPath().$cacheTypeFolder.$subCacheFolder.'/'.$filename)) {
            return true;
        }

        return false;
    }

    /**
     * isRequestedPageCached function.
     *
     * @access public
     * @return void
     */
    public function isRequestedPageCached()
    {
        if ($this->checkIfCacheExists($this->getCacheFilename(), 'page', $this->getSubCacheFolder())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * getCacheInfo function.
     *
     * @access public
     * @return void
     */
    public function getCacheInfo()
    {
        Factory::get('Cache\Init')->runtimeEnd = microtime(1);

        $fileStat = stat($this->getMainCacheFolderPath().'/page'.$this->getSubCacheFolder().'/'.$this->getCacheFilename());

        $DateTime = new \DateTime();

        $html = '<!--
                Cached page generated by OSO Super Cache
                Time to create cache: {{$_runtime}} sec
                Time to load cache: '.Factory::get('Cache\Init')->getTotalRuntime().'
                Cache file created on: '.($DateTime->setTimestamp($fileStat[9])->format(\DateTime::ATOM)).'
                Current time: '.($DateTime->setTimestamp(time())->format(\DateTime::ATOM)).'
                -->';

        return $html;
    }

    /**
     * isCacheTaskAvailable function.
     *
     * @access public
     * @return void
     */
    public function isCacheTaskAvailable()
    {
        $cacheTaskAvailable = false;
        $activeCacheTasks = get_option('OSOSuperCacheActiveCacheTasks', []);

        // Remove old tasks
        if (!empty($activeCacheTasks)) {
            foreach ($activeCacheTasks as $token => $timestamp) {
                // If a task is older than 45 seconds, we assume it is finished but wasn't removed for unknown reasons
                if ($timestamp < (time()-45)) {
                    unset($activeCacheTasks[$token]);
                }
            }
        }

        // Check if a free task is available
        if (count($activeCacheTasks) < Factory::get('Cache\Config')->get('maxSimultaneousTasks')) {
            $cacheTaskAvailable = true;

            $this->cacheTaskToken = Factory::get('Cache\Tools')->generateRandomString(4);

            $activeCacheTasks[$this->cacheTaskToken] = time();
        }

        update_option('OSOSuperCacheActiveCacheTasks', $activeCacheTasks, 'no');

        return $cacheTaskAvailable;
    }

    /**
     * unregisterCacheTask function.
     *
     * @access public
     * @return void
     */
    public function unregisterCacheTask()
    {
        $activeCacheTasks = get_option('OSOSuperCacheActiveCacheTasks', []);

        if (!empty($activeCacheTasks) && !empty($activeCacheTasks[$this->cacheTaskToken])) {
            unset($activeCacheTasks[$this->cacheTaskToken]);

            update_option('OSOSuperCacheActiveCacheTasks', $activeCacheTasks, 'no');
        }
    }
}
