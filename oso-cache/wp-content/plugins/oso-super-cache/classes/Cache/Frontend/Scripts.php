<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Scripts
{
    private static $instance;

    private $homePath                   = null;
    private $blogHosts                  = [];
    private $alternativeBlogHost        = null;
    private $blogPath                   = null;
    private $alternativeblogPath        = null;

    private $deferActive                = false;
    private $detectedScripts            = [];
    private $detectedExternalScripts    = [];
    private $sourceCodeScripts          = [];
    private $sourceCodeScriptsSmartBundles = [];

    private $htmlScript                     = '';
    private $registeredLocalizeScriptData   = [];

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        global $wp_scripts;

        // Support for multisites, get_home_path() is not available for some reasons
        $this->homePath = ABSPATH;

        $urlInfo = parse_url($wp_scripts->base_url);

        Factory::get('Cache\Log')->addLog(__METHOD__, 'Base URL: '.$wp_scripts->base_url);

        $this->blogHosts[] = $urlInfo['host'];
        $this->blogPath = !empty($urlInfo['path']) ? $urlInfo['path'] : '';
        $this->deferActive = Factory::get('Cache\Config')->get('scriptsDefer');

        $alternativeUrlInfo = parse_url(WP_CONTENT_URL);

        if ($alternativeUrlInfo['host'] != $urlInfo['host']) {
            $this->alternativeBlogHost = $alternativeUrlInfo['host'];
            $this->blogHosts[] = $alternativeUrlInfo['host'];
            $this->alternativeblogPath = !empty($alternativeUrlInfo['path']) ? $alternativeUrlInfo['path'] : '';

            // DNS Prefetch
            Factory::get('Cache\Frontend\Prefetch')->addHost($alternativeUrlInfo['host']);
        }
    }

    /**
     * optimize function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function optimize(&$html)
    {
        // Find scripts first, because they can contain <style> tags
        $html = preg_replace_callback('/<script.*<\/script>/Us', [$this, 'collectScripts'], $html);

        // Process scripts
        $this->processDetectedScripts();

        // Create JavaScript file and set htmlStyle with the <script>-tag
        $this->createScriptFile();
    }

    /**
     * collectScripts function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    public function collectScripts($tag)
    {
        // Detect if script is of type javascript
        $scriptType = [];
        preg_match('/\<script([^\>]*)type=("|\')([^"\']*)("|\')/Us', $tag[0], $scriptType);

        // Only <script>-tags without type attribute or with type attribute text/javascript are JavaScript
        if (empty($scriptType) || !empty($scriptType) && strtolower($scriptType[3]) == 'text/javascript') {
            // Exclude external scripts
            $scriptLocation = [];

            if (preg_match('/<script(.*?)src=("|\')([^"\']*)("|\')/', $tag[0], $scriptLocation)) {
                $urlInfo = parse_url($scriptLocation[3]);

                // Only internal scripts will be merged
                if ((!empty($urlInfo['host']) && in_array($urlInfo['host'], $this->blogHosts)) || empty($urlInfo['host'])) {
                    $this->detectedScripts[] = $scriptLocation[3];

                    $tag[0] = '';
                } else {
                    if ($this->deferActive) {
                        // Check if defer-attribute is not present yet
                        $scriptDefer = [];
                        preg_match('/<((script)[^>]*)>(.*)\<\/(script)>/Us', $tag[0], $scriptDefer);

                        if (strpos($scriptDefer[1], ' defer') === false) {
                            $this->detectedExternalScripts[] = '<'.$scriptDefer[1].' defer></script>';
                        } else {
                            $this->detectedExternalScripts[] = $tag[0];
                        }
                    } else {
                        $this->detectedExternalScripts[] = $tag[0];
                    }

                    $tag[0] = '';

                    // DNS Prefetch
                    Factory::get('Cache\Frontend\Prefetch')->addHost($urlInfo['host']);
                }
            } else {
                $this->detectedScripts[] = $tag[0];

                $tag[0] = '';
            }
        }

        return $tag[0];
    }

    /**
     * processDetectedScripts function.
     *
     * @access public
     * @return void
     */
    public function processDetectedScripts()
    {
        $scriptBundle = Factory::get('Cache\Tools')->generateRandomString(8);
        $lastScriptType = '';

        if (!empty($this->detectedScripts)) {
            foreach ($this->detectedScripts as $script) {

                // Smart bundles active
                if (Factory::get('Cache\Config')->get('scriptsSmartBundles')) {
                    // Inline scripts
                    if (strpos($script, '<script') !== false) {

                        if (empty($lastScriptType) || $lastScriptType != 'inline') {
                            $scriptBundle = 'in-'.Factory::get('Cache\Tools')->generateRandomString(8);
                        }

                        $this->sourceCodeScriptsSmartBundles[$scriptBundle][] = $this->fixMissingSemicolon(trim(preg_replace('/<script([^>]*?)>(.*)<\/script>/s', '$2', $script)));

                        $lastScriptType = 'inline';

                    } else {

                        // External script
                        if (empty($lastScriptType) || $lastScriptType != 'external') {
                            $scriptBundle = 'ex-'.Factory::get('Cache\Tools')->generateRandomString(8);
                        }

                        // wp-includes scripts on top
                        if (strpos($script, '/wp-includes/js/') !== false) {
                            $this->sourceCodeScriptsSmartBundles['wp-includes'][] = $this->fixMissingSemicolon($this->loadScriptSourceCode($script));
                        } else {
                            $this->sourceCodeScriptsSmartBundles[$scriptBundle][] = $this->fixMissingSemicolon($this->loadScriptSourceCode($script));

                            $lastScriptType = 'external';
                        }
                    }

                } else {
                    // Inline scripts
                    if (strpos($script, '<script') !== false) {
                        $this->sourceCodeScripts[] = $this->fixMissingSemicolon(trim(preg_replace('/<script([^>]*?)>(.*)<\/script>/s', '$2', $script)));
                    } else {
                        // External script
                        $this->sourceCodeScripts[] = $this->fixMissingSemicolon($this->loadScriptSourceCode($script));
                    }
                }
            }
        }
    }

    /**
     * loadScriptSourceCode function.
     *
     * @access public
     * @param mixed $url
     * @return void
     */
    public function loadScriptSourceCode($url)
    {
        Factory::get('Cache\Log')->addLog(__METHOD__, 'Requested URL: '.$url);

        $source = '';

        $originalHomePath = $this->homePath;
        $originalBlogPath = $this->blogPath;

        $urlInfo = parse_url($url);
        $urlInfo['path'] = urldecode($urlInfo['path']);

        // If protocol is missing detect current protocol
        if (empty($urlInfo['scheme']) && substr($url, 0, 2) == '//') {
            $isHttps = Factory::get('Cache\Tools')->isHttps();

            $url = ($isHttps ? 'https' : 'http').':'.$url;
        }

        // Detect which host is used
        if (!empty($this->alternativeBlogHost) && strpos($url, $this->alternativeBlogHost) !== false) {
            $this->homePath = WP_CONTENT_DIR.'/';
            $this->blogPath = $this->alternativeblogPath;
        }

        if (!empty($this->blogPath)) {
            $urlInfo['path'] = strpos($urlInfo['path'], $this->blogPath) === 0 ? substr($urlInfo['path'], strlen($this->blogPath)) : $urlInfo['path'];
        }

        Factory::get('Cache\Log')->addLog(__METHOD__, 'JS path corrected: '.$urlInfo['path']);

        $pathInfo = pathinfo($urlInfo['path']);

        if (!empty($pathInfo['extension']) && $pathInfo['extension'] == 'js') {
            // Try to find the file on the filesystem
            if (!empty($urlInfo['path']) && $urlInfo['path'] !== '/') {
                // We don't check if the file exists, because this costs too much time.
                // In most cases the file will exists, but when it fails, we have a fallback

                $localPath = $this->homePath.ltrim($urlInfo['path'], '/');

                Factory::get('Cache\Log')->addLog(__METHOD__, 'Local path for given URL: '.$localPath);

                $source = file_get_contents($localPath);
            } else {
                Factory::get('Cache\Log')->addLog(__METHOD__, 'Looks like a dynamic script');
            }
        }

        if (empty($source)) {
            $url = html_entity_decode(trim($url));

            Factory::get('Cache\Log')->addLog(__METHOD__, 'Load script by using the URL: '.$url);

            // We need to define a user_agent or file_get_contents will replace & into &amp;
            // Yes - http is correct even when it's a https connection...
            $options = [
                'http' => [
                    'method' => 'GET',
                    'user_agent' => (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'OSO-Super-Cache/'.OSO_SUPER_CACHE_VERSION),
                ]
            ];

            $context = stream_context_create($options);

            $source = file_get_contents($url, false, $context);
        }

        $this->homePath = $originalHomePath;
        $this->blogPath = $originalBlogPath;

        return $source;
    }

    /**
     * handleDeferOnInlineScript function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function handleDeferOnInlineScript(&$html)
    {
        $html = preg_replace_callback('/<((script)[^>]*)>(.*)\<\/(script)>/Us', [$this, 'makeInlineScriptDeferable'], $html);
    }

    /**
     * makeInlineScriptDeferable function.
     *
     * @access public
     * @param mixed $scriptTag
     * @return void
     */
    public function makeInlineScriptDeferable($scriptTag)
    {
        // Check if type attribute is present and of text/javascript
        $typeMatch = [];
        preg_match('/type=("|\')([^"\']{1,})("|\')/', $scriptTag[0], $typeMatch);

        if (!empty($typeMatch[2]) && $typeMatch[2] == 'text/javascript' || empty($typeMatch)) {
            if (strpos($scriptTag[0], ' defer') === false && strpos($scriptTag[0], 'src') === false) {
                $scriptTag = '<'.$scriptTag[1].' defer>window.addEventListener(\'DOMContentLoaded\', function() { '."\n".$scriptTag[3]."\n".' });</script>';
            } else {
                $scriptTag = $scriptTag[0];
            }
        } else {
            $scriptTag = $scriptTag[0];
        }

        return $scriptTag;
    }

    /**
     * createScriptFile function.
     *
     * @access public
     * @return void
     */
    public function createScriptFile()
    {
        global $wp_scripts;

        // Smart bundles active - BETA
        if (Factory::get('Cache\Config')->get('scriptsSmartBundles')) {

            $suffix = Factory::get('Cache\Frontend\Resolver')->isHTTPS() ? '_https' : '';

            $deferAttribute = '';

            if ($this->deferActive) {
                $deferAttribute = ' defer';
            }

            // wp-includes
            if (!empty($this->sourceCodeScriptsSmartBundles['wp-includes'])) {
                $source = implode("\n", $this->sourceCodeScriptsSmartBundles['wp-includes']);

                if (Factory::get('Cache\Config')->get('scriptsMinify')) {
                    if (Factory::get('Cache\Debug')->isDebugEnabled() == false) {
                        Factory::get('Cache\Frontend\Minify')->minifyJS($source);
                    }
                }

                $extension = 'js';

                if (Factory::get('Cache\Config')->get('scriptsGZIPOutput')) {
                    $extension = 'php';

                    $source = $this->addGzipCode($source);
                }

                $filename = Factory::get('Cache\Tools')->getHash($this->sourceCodeScriptsSmartBundles['wp-includes']).$suffix.'.'.$extension;

                unset($this->sourceCodeScriptsSmartBundles['wp-includes']);

                Factory::get('Cache\Frontend\Cache')->createCachefile($filename, $source, 'js');

                $jsURL = content_url().'/cache/oso_super_cache/'.Factory::get('Cache\Frontend\Resolver')->getCurrentBlogId().'/js/'.$filename;

                if (Factory::get('Cache\Config')->get('scriptsPreloadTag')) {
                    // Add to preload stack
                    Factory::get('Cache\Frontend\Preload')->add($jsURL, 'script');
                }

                $this->htmlScript .= '<script type="text/javascript" src="'.$jsURL.'"'.$deferAttribute.'></script>';
            }

            if (!empty($this->sourceCodeScriptsSmartBundles)) {
                foreach ($this->sourceCodeScriptsSmartBundles as $scriptBundle => $scripts) {

                    $source = implode("\n", $scripts);

                    if (Factory::get('Cache\Config')->get('scriptsMinify')) {
                        if (Factory::get('Cache\Debug')->isDebugEnabled() == false) {
                            Factory::get('Cache\Frontend\Minify')->minifyJS($source);
                        }
                    }

                    if (strpos($scriptBundle, 'in-') !== false) {

                        $this->htmlScript .= '<script type="text/javascript"'.$deferAttribute.'>'.$source.'</script>';

                    } else {

                        $extension = 'js';

                        if (Factory::get('Cache\Config')->get('scriptsGZIPOutput')) {
                            $extension = 'php';

                            $source = $this->addGzipCode($source);
                        }

                        $filename = Factory::get('Cache\Tools')->getHash($scripts).$suffix.'.'.$extension;

                        Factory::get('Cache\Frontend\Cache')->createCachefile($filename, $source, 'js');

                        $jsURL = content_url().'/cache/oso_super_cache/'.Factory::get('Cache\Frontend\Resolver')->getCurrentBlogId().'/js/'.$filename;

                        if (Factory::get('Cache\Config')->get('scriptsPreloadTag')) {
                            // Add to preload stack
                            Factory::get('Cache\Frontend\Preload')->add($jsURL, 'script');
                        }

                        $this->htmlScript .= '<script type="text/javascript" src="'.$jsURL.'"'.$deferAttribute.'></script>';

                        if (!empty($this->detectedExternalScripts)) {
                            if (Factory::get('Cache\Config')->get('scriptsExternalScriptsPosition') == 'before') {
                                $this->htmlScript = implode('', $this->detectedExternalScripts).$this->htmlScript;
                            } else {
                                $this->htmlScript = $this->htmlScript.implode('', $this->detectedExternalScripts);
                            }
                        }
                    }
                }
            }

        } else {
            $source = implode("\n", $this->sourceCodeScripts);

            if (Factory::get('Cache\Config')->get('scriptsMinify')) {
                if (Factory::get('Cache\Debug')->isDebugEnabled() == false) {
                    Factory::get('Cache\Frontend\Minify')->minifyJS($source);
                }
            }

            $extension = 'js';

            if (Factory::get('Cache\Config')->get('scriptsGZIPOutput')) {
                $extension = 'php';

                $source = $this->addGzipCode($source);
            }

            $suffix = Factory::get('Cache\Frontend\Resolver')->isHTTPS() ? '_https' : '';

            $filename = Factory::get('Cache\Tools')->getHash($this->sourceCodeScripts).$suffix.'.'.$extension;

            Factory::get('Cache\Frontend\Cache')->createCachefile($filename, $source, 'js');

            $deferAttribute = '';

            if ($this->deferActive) {
                $deferAttribute = ' defer';
            }

            $jsURL = content_url().'/cache/oso_super_cache/'.Factory::get('Cache\Frontend\Resolver')->getCurrentBlogId().'/js/'.$filename;

            //TODO enable with feature added option 'enable preload'
//            if (Factory::get('Cache\Config')->get('scriptsPreloadTag')) {
//                // Add to preload stack
//                Factory::get('Cache\Frontend\Preload')->add($jsURL, 'script');
//            }

            $this->htmlScript = '<script type="text/javascript" src="'.$jsURL.'"'.$deferAttribute.'></script>';

            if (!empty($this->detectedExternalScripts)) {
                if (Factory::get('Cache\Config')->get('scriptsExternalScriptsPosition') == 'before') {
                    $this->htmlScript = implode('', $this->detectedExternalScripts).$this->htmlScript;
                } else {
                    $this->htmlScript = $this->htmlScript.implode('', $this->detectedExternalScripts);
                }
            }
        }

        // Free resources
        unset($this->detectedScripts);
        unset($this->detectedExternalScripts);
        unset($this->sourceCodeScripts);
        unset($this->sourceCodeScriptsSmartBundles);
    }

    /**
     * fixMissingSemicolon function.
     *
     * @access public
     * @param mixed $code
     * @return void
     */
    public function fixMissingSemicolon($source)
    {
        if (Factory::get('Cache\Config')->get('scriptsFixSemicolon')) {
            $source = rtrim($source);

            // Works for () and (jQuery)
            if (substr($source, -1) === ')') {
                $source .= ';';
            }
        }

        return $source;
    }

    /**
     * addGzipCode function.
     *
     * @access public
     * @param mixed $jsCode
     * @return void
     */
    public function addGzipCode($jsCode)
    {
        $topPHPCode = "<?php\n";
        $topPHPCode .= "include_once '".Factory::get('Cache\Frontend\Header')->classLocation()."'; \n";
        $topPHPCode .= Factory::get('Cache\Frontend\Header')->getContentTypeHeaderCode('application/javascript', true)."\n";
        $topPHPCode .= "\OSOSuperCache\Cache\Frontend\Header::getInstance()->setConfig(unserialize(base64_decode('".base64_encode(serialize(Factory::get('Cache\Frontend\Header')->getHeaderRelatedConfigs()))."')));\n";
        $topPHPCode .= '\OSOSuperCache\Cache\Frontend\Header::getInstance()->getHeader(__FILE__);'."\n";
        $topPHPCode .= "ini_set('zlib.output_compression_level', ".Factory::get('Cache\Config')->get('scriptsGzipCompressionLevel').");\n";
        $topPHPCode .= "if (ini_get('zlib.output_compression')) {\n";
        $topPHPCode .=  "ob_start();\n";
        $topPHPCode .= "} else {\n";
        $topPHPCode .=  "ob_start('ob_gzhandler');\n";
        $topPHPCode .= "}\n";
        $topPHPCode .= "?>\n";

        $bottomPHPCode = "\n<?php ob_end_flush(); ?>";

        return $topPHPCode.$jsCode.$bottomPHPCode;
    }

    /**
     * getHTMLScript function.
     *
     * @access public
     * @return void
     */
    public function getHTMLScript()
    {
        return $this->htmlScript;
    }

    /**
     * registerLocalizeScriptData function.
     *
     * @access public
     * @param mixed $handle
     * @param mixed $name
     * @param mixed $data
     * @return void
     */
    public function registerLocalizeScriptData($handle, $name, $data)
    {
        if (!empty($handle) && !empty($name) && !empty($data) && is_string($name)) {
            $this->registeredLocalizeScriptData[$handle] = [
                'name'=>$name,
                'data'=>$data,
            ];
        }
    }

    /**
     * getLocalizedScriptData function.
     *
     * @access public
     * @return void
     */
    public function getLocalizedScriptData()
    {
        return $this->registeredLocalizeScriptData;
    }

    /**
     * getLocalizedScriptDataCode function.
     *
     * @access public
     * @return void
     */
    public function getLocalizedScriptDataCode()
    {
        $phpCode = '';

        if (!empty($this->registeredLocalizeScriptData)) {
            $phpCode .= '<?php $osoSuperCacheLocalizedScriptData = json_decode("'.addslashes(json_encode($this->registeredLocalizeScriptData)).'", true); echo \OSOSuperCache\Cache\Frontend\Scripts::processLocalizedScriptData($osoSuperCacheLocalizedScriptData); ?>';
        }

        return $phpCode;
    }

    /**
     * processLocalizedScriptData function.
     *
     * @access public
     * @static
     * @param mixed $scriptData
     * @return void
     */
    public static function processLocalizedScriptData($scriptData)
    {
        $localizedScript = '';

        if (!empty($scriptData) && is_array($scriptData)) {

            $localizedScript .= "<script type=\"text/javascript\">\n/* <![CDATA[ */\n";

            // Anonymous function for detecting nonces
            $detectNonceFunction = function (&$value, $key) {

                if (is_string($value) && strpos($value, 'wp_create_nonce__') !== false) {
                    $value = str_replace('wp_create_nonce__', '', $value);
                    $value = wp_create_nonce($value);
                }
            };

            foreach ($scriptData as $handle => $data) {

                array_walk_recursive($data['data'], $detectNonceFunction);

                $localizedScript .= "var ".$data['name']." = ".json_encode($data['data']).";\n";
            }

            $localizedScript .= "/* ]]> */</script>";
        }

        return $localizedScript;
    }

    public function excludeScripts(){
        //TODO make some checking if there are settings to use or not page optimization

        $pageOptimization = Factory::get('Cache\Config')->get('pageOptimization', 'inactive');
        if(!empty($pageOptimization['excludedScripts'])){
            foreach($pageOptimization['excludedScripts'] as $pageId => $excludedScript){
                if(is_page($pageId)){
                    $scripts = explode(' ',$excludedScript);
                    foreach ($scripts as $script){
                        wp_dequeue_script($script);
                    }
                }
            }
        }
    }
}
