<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Styles
{
    private static $instance;

    private $homePath                   = null;
    private $blogHost                   = null;
    private $blogHosts                  = [];
    private $alternativeBlogHost        = null;
    private $blogPath                   = null;
    private $alternativeblogPath        = null;

    private $detectedStyles             = [];
    private $detectedGoogleFonts        = [];
    private $sourceCodeStyles           = [];

    private $currentAbsolutePathWeb     = '';
    private $currentAbsolutePath        = '';
    private $importStatements           = [];

    private $htmlStyle                  = '';
    private $htmlGoogleFont             = '';

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

        $this->blogHost = $urlInfo['host'];
        $this->blogHosts[] = $urlInfo['host'];
        $this->blogPath = !empty($urlInfo['path']) ? $urlInfo['path'] : '';

        $alternativeUrlInfo = parse_url(WP_CONTENT_URL);

        if ($alternativeUrlInfo['host'] != $urlInfo['host']) {
            $this->alternativeBlogHost = $alternativeUrlInfo['host'];
            $this->blogHosts[] = $alternativeUrlInfo['host'];
            $this->alternativeblogPath = !empty($alternativeUrlInfo['path']) ? $alternativeUrlInfo['path'] : '';

            // DNS Prefetch
            Factory::get('Cache\Frontend\Prefetch')->addHost($alternativeUrlInfo['host']);
        }
    }

    public function optimize(&$html)
    {
        // Find styles
        if (!empty(Factory::get('Cache\Config')->get('stylesExcludeStyleTags'))) {
            $html = preg_replace_callback('/<link([^>]*)>/Us', [$this, 'collectStyles'], $html);
        } else {
            $html = preg_replace_callback('/<style.*<\/style>|<link([^>]*)>/Us', [$this, 'collectStyles'], $html);
        }

        // Process styles
        $this->processDetectedStyles();

        // Create CSS file and set htmlStyle with the <link>-tag
        $this->createCSSFile();

        if (Factory::get('Cache\Config')->get('stylesOptimizeGoogleFonts')) {
            // Process Google Fonts
            $this->processDetectedGoogleFonts();
        }
    }

    /**
     * collectStyles function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    public function collectStyles($tag)
    {
        // Detect if style is of type stylesheet
        if (strpos($tag[0], '<style') !== false || preg_match('/rel=("|\')stylesheet("|\')|type=("|\')text\/css("|\')/', $tag[0])) {
            // Detect external styles
            $styleLocation = [];

            if (preg_match('/href=("|\')([^"\']*)("|\')/', $tag[0], $styleLocation)) {
                $urlInfo = parse_url($styleLocation[2]);

                // Only internal scripts will be merged
                if ((!empty($urlInfo['host']) && in_array($urlInfo['host'], $this->blogHosts)) || empty($urlInfo['host'])) {
                    // Detect media attribute
                    $mediaValue = [];

                    if (preg_match('/media=("|\')([^"\']*)("|\')/', $tag[0], $mediaValue)) {
                        if (in_array($mediaValue[2], ['all', 'print']) || strpos($mediaValue[2], 'screen') !== false) {
                            $this->detectedStyles[] = [
                                'url'=>$styleLocation[2],
                                'media'=>$mediaValue[2],
                            ];

                            $tag[0] = '';
                        }
                    } else {
                        $this->detectedStyles[]['url'] = $styleLocation[2];

                        $tag[0] = '';
                    }
                } else {
                    // Detect Google fonts
                    if (in_array($urlInfo['host'], ['fonts.googleapis.com', 'fonts.gstatic.com'])) {
                        if (Factory::get('Cache\Config')->get('stylesOptimizeGoogleFonts')) {
                            $this->detectedGoogleFonts[] = $styleLocation[2];

                            $tag[0] = '';
                        }
                    }

                    // DNS Prefetch
                    Factory::get('Cache\Frontend\Prefetch')->addHost($urlInfo['host']);
                }
            } else {
                $this->detectedStyles[]['style'] = $tag[0];

                $tag[0] = '';
            }
        }

        return $tag[0];
    }


    /**
     * processDetectedStyles function.
     *
     * @access public
     * @return void
     */
    public function processDetectedStyles()
    {
        if (!empty($this->detectedStyles)) {
            foreach ($this->detectedStyles as $style) {
                // Inline styles
                if (!empty($style['style'])) {
                    $this->sourceCodeStyles[] = trim(preg_replace('/<style([^>]*?)>(.*)<\/style>/s', '$2', $style['style']));
                } else {

                    $suffix = Factory::get('Cache\Frontend\Resolver')->isHTTPS() ? '_https' : '';
                    $filename = 'pre_cache_'.Factory::get('Cache\Tools')->getHash($style['url']).$suffix.'.css';

                    // Check if pre-cache file exists
                    if (Factory::get('Cache\Frontend\Cache')->checkIfCacheExists($filename, 'css')) {
                        $this->sourceCodeStyles[] = file_get_contents(Factory::get('Cache\Frontend\Cache')->getMainCacheFolderPath().'/css/'.$filename);
                    } else {
                        $mediaQueryStart    = '';
                        $mediaQueryEnd      = '';

                        if (!empty($style['media'])) {
                            $mediaValue = [];

                            if (preg_match('/((not|only)\s+?)?(all|print|screen)\s+?(and|not|only)\s+?(.*)|(all|print|screen)|(\((.*)\))/', $style['media'], $mediaValue)) {
                                if ($mediaValue[0] != 'all') {
                                    //! TODO check syntax for not|only
                                    $mediaQueryStart = '@media '.$mediaValue[0].' { ';
                                    $mediaQueryEnd = ' }';
                                }
                            }
                        }

                        // External style
                        $sourceCode = '';
                        $sourceCode = $mediaQueryStart.$this->loadStyleSourceCode($style['url']).$mediaQueryEnd;

                        $this->sourceCodeStyles[] = $sourceCode;

                        Factory::get('Cache\Frontend\Cache')->createCachefile($filename, $sourceCode, 'css');

                        unset($sourceCode);
                    }
                }
            }
        }
    }

    /**
     * loadStyleSourceCode function.
     *
     * @access public
     * @param mixed $url
     * @return void
     */
    public function loadStyleSourceCode($url)
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

        Factory::get('Cache\Log')->addLog(__METHOD__, 'Path: '.$urlInfo['path']);

        // Detect which host is used
        if (!empty($this->alternativeBlogHost) && strpos($url, $this->alternativeBlogHost) !== false) {
            $this->homePath = WP_CONTENT_DIR.'/';
            $this->blogPath = $this->alternativeblogPath;
        }

        // Remove subfolder from URL to match with root path
        if (!empty($this->blogPath)) {
            $urlInfo['path'] = strpos($urlInfo['path'], $this->blogPath) === 0 ? substr($urlInfo['path'], strlen($this->blogPath)) : $urlInfo['path'];
        }

        Factory::get('Cache\Log')->addLog(__METHOD__, 'CSS path corrected: '.$urlInfo['path']);

        $pathInfo = pathinfo($urlInfo['path']);

        if (!empty($pathInfo['extension']) && $pathInfo['extension'] == 'css') {
            // Try to find the file on the filesystem
            if (!empty($urlInfo['path']) && $urlInfo['path'] !== '/') {

                // We don't check if the file exists, because this costs too much time.
                // In most cases the file will exists, but when it fails, we have a fallback
                $localPath = $this->homePath.ltrim($urlInfo['path'], '/');

                Factory::get('Cache\Log')->addLog(__METHOD__, 'Local path for given URL: '.$localPath);

                $source = file_get_contents($localPath);
            } else {
                Factory::get('Cache\Log')->addLog(__METHOD__, 'Looks like a dynamic style');
            }
        }

        if (empty($source)) {
            $url = html_entity_decode(trim($url));

            Factory::get('Cache\Log')->addLog(__METHOD__, 'Load style by using the URL: '.$url);

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

        if (!empty($source)) {
            //! TODO more testing
            $this->currentAbsolutePath      = dirname(!empty($urlInfo['path']) ? $urlInfo['path'] : '/');
            $this->currentAbsolutePathWeb   = $this->blogPath.dirname(!empty($urlInfo['path']) ? $urlInfo['path'] : '/');

            Factory::get('Cache\Log')->addLog(__METHOD__, 'Set currentAbsolutePath: '.$this->currentAbsolutePath);
            Factory::get('Cache\Log')->addLog(__METHOD__, 'Set currentAbsolutePathWeb: '.$this->currentAbsolutePathWeb);

            // Remove @charset
            $source = preg_replace('/@charset([^;]*);/', '', $source);

            // Fix all url() pathes
            $source = preg_replace_callback('/url\(["|\']?([^"\'\)]+)["|\']?\)/', [$this, 'replaceCSSPathes'], $source);

            // Handle @import statements
            //! TODO handle media queries from @import statements
            $source = preg_replace_callback('/@import(([\s]+)url)?([^;@]+);/s', ([$this, 'handleImportStatements']), $source);
        } else {
            if ($source === false) {
                Factory::get('Cache\Log')->addLog(__METHOD__, 'No style found for: '.$url);
            } else {
                Factory::get('Cache\Log')->addLog(__METHOD__, 'Style is empty of: '.$url);
            }
        }

        $this->homePath = $originalHomePath;
        $this->blogPath = $originalBlogPath;

        return $source;
    }

     /**
     * replaceCSSPathes function.
     *
     * @access public
     * @param mixed $matches
     * @return void
     */
    public function replaceCSSPathes($matches)
    {
        Factory::get('Cache\Log')->addLog(__METHOD__, 'Replace CSS path: '.$matches[1]);

        $newPathWithFile = $matches[1];

        $levelToGoUpwards = substr_count($matches[1], '../');

        // Fix the path when url() contains something like this ../../../
        if (!empty($levelToGoUpwards)) {
            $pathAfterGoingUpwards = array_slice(
                explode('/', $this->currentAbsolutePathWeb),
                0,
                $levelToGoUpwards*(-1)
            );

            $newPathWithFile = implode('/', $pathAfterGoingUpwards).'/'.str_replace('../', '', $matches[1]);
        } else {
            // Fix the path when file is in the same location like the origin css file
            if (strpos($matches[1], './') !== false) {
                $newPathWithFile = $this->currentAbsolutePathWeb.str_replace('./', '/', $matches[1]);
            } elseif (substr($matches[1], 0, 1) !== '/' && strpos($matches[1], '://') === false && strpos($matches[1], 'data:') === false) {
                $newPathWithFile = $this->currentAbsolutePathWeb.'/'.$matches[1];
            }
        }

        Factory::get('Cache\Log')->addLog(__METHOD__, 'Replaced CSS path: '.$newPathWithFile);

        return 'url(\''.$newPathWithFile.'\')';
    }

    /**
     * handleImportStatements function.
     *
     * @access public
     * @param mixed $importStatementMatch
     * @return void
     */
    public function handleImportStatements($importStatementMatch)
    {
        Factory::get('Cache\Log')->addLog(__METHOD__, 'Handle import statement: '.$importStatementMatch[3]);

        // Remove blogPath from URL
        if (!empty($this->blogPath)) {
            $blogPathPosition = strpos($importStatementMatch[3], $this->blogPath);

            if ($blogPathPosition !== false) {
                // Remove blogPath from the beginning of the path
                $importStatementMatch[3] = substr($importStatementMatch[3], 0, $blogPathPosition).substr($importStatementMatch[3], strlen($this->blogPath)+$blogPathPosition);

                Factory::get('Cache\Log')->addLog(__METHOD__, 'Corrected handle import statement: '.$importStatementMatch[3]);
            }
        }

        $originalPathToFile     = $this->currentAbsolutePath;
        $originalWebPathToFile  = $this->currentAbsolutePathWeb;

        // Ignore :// pathes
        if (strpos($importStatementMatch[3], '://') === false) {
            // Find real path
            $pathToCSSFile = $this->resolveLocalPath($originalPathToFile, $importStatementMatch[3]);

            Factory::get('Cache\Log')->addLog(__METHOD__, 'Absolute path: '.$pathToCSSFile);

            // Overwrite currentPathToCSSFile
            $this->currentAbsolutePath      = dirname($pathToCSSFile);
            $this->currentAbsolutePathWeb   = $this->blogPath.dirname($pathToCSSFile);

            Factory::get('Cache\Log')->addLog(__METHOD__, 'Set currentAbsolutePath: '.$this->currentAbsolutePath);
            Factory::get('Cache\Log')->addLog(__METHOD__, 'Set currentAbsolutePathWeb: '.$this->currentAbsolutePathWeb);

            $pathToCSSFile = rtrim($this->homePath, '/').$pathToCSSFile;

            Factory::get('Cache\Log')->addLog(__METHOD__, 'Path to CSS file: '.$pathToCSSFile);

            // Load css
            if (file_exists($pathToCSSFile)) {
                Factory::get('Cache\Log')->addLog(__METHOD__, 'Style loaded: '.$importStatementMatch[3]);

                $source = file_get_contents($pathToCSSFile);
            } else {
                Factory::get('Cache\Log')->addLog(__METHOD__, 'Style not loaded: '.$importStatementMatch[3]);

                $source = '';
            }

            // Remove @charset
            $source = preg_replace('/@charset([^;]*);/', '', $source);

            // Fix all url() pathes
            $source = preg_replace_callback('/url\(["|\']?([^"\'\)]+)["|\']?\)/', [$this, 'replaceCSSPathes'], $source);

            // Check for import statements
            $source = preg_replace_callback('/@import(([\s]+)url)?([^;@]+);/s', ([$this, 'handleImportStatements']), $source);

            // Restore original currentPathToCSSFile
            $this->currentAbsolutePath      = $originalPathToFile;
            $this->currentAbsolutePathWeb   = $originalWebPathToFile;

            return $source;
        } else {
            // Import statement is not local or uses :// - we don't parse this resource and add it to the top

            // Check if "url" is missing and add it
            if (strpos($importStatementMatch[1], 'url') === false) {
                // Check if ' or " is missing
                if (strpos($importStatementMatch[3], '"') === false && strpos($importStatementMatch[3], "'") === false) {
                    $importStatementMatch[3] = "'".trim($importStatementMatch[3])."'";
                }

                $this->importStatements[] = '@import url('.trim($importStatementMatch[3]).');';

                return '';
            } else {
                $this->importStatements[] = $importStatementMatch[0];

                return '';
            }
        }
    }

    /**
     * resolveLocalPath function.
     *
     * @access public
     * @param mixed $currentFilePath
     * @param mixed $pathToResolve
     * @return void
     */
    public function resolveLocalPath($currentFilePath, $pathToResolve)
    {
        $pathToResolve = trim(str_replace(['\'', '"', '(', ')'], '', $pathToResolve));

        $newPathWithFile = $pathToResolve;

        $levelToGoUpwards = substr_count($pathToResolve, '../');

        // Fix the path when url() contains something like this ../../../
        if (!empty($levelToGoUpwards)) {
            $pathAfterGoingUpwards = array_slice(
                explode('/', $currentFilePath),
                0,
                $levelToGoUpwards*(-1)
            );

            $newPathWithFile = implode('/', $pathAfterGoingUpwards).'/'.str_replace('../', '', $pathToResolve);
        } else {
            // Fix the path when file is in the same location like the origin css file
            if (strpos($pathToResolve, './') !== false) {
                $newPathWithFile = $currentFilePath.str_replace('./', '/', $pathToResolve);
            } elseif (substr($pathToResolve, 0, 1) !== '/' && strpos($pathToResolve, '://') === false && strpos($pathToResolve, 'data:') === false) {
                $newPathWithFile = $currentFilePath.'/'.$pathToResolve;
            }
        }

        return $newPathWithFile;
    }

    /**
     * processDetectedGoogleFonts function.
     *
     * @access public
     * @return void
     */
    public function processDetectedGoogleFonts()
    {
        $fontFamilies   = [];
        $fontSubsets    = [];

        if (!empty($this->detectedGoogleFonts)) {
            foreach ($this->detectedGoogleFonts as $url) {
                Factory::get('Cache\Log')->addLog(__METHOD__, 'Google Font URL: '.$url);

                $url = html_entity_decode($url);

                $urlInfo = parse_url($url);

                // Fix a bug where WordPress/Themes/Plugins replace & is broken
                if (!empty($urlInfo['fragment'])) {
                    $urlInfo['query'] = str_replace('&038;', '&', $urlInfo['query'].$urlInfo['fragment']);
                }

                if (!empty($urlInfo['query'])) {
                    $query = [];

                    parse_str($urlInfo['query'], $query);

                    if (!empty($query['family'])) {
                        $splitFonts = explode('|', $query['family']);

                        if (!empty($splitFonts)) {
                            foreach ($splitFonts as $font) {
                                $colonPos = strpos($font, ':');

                                if (!empty($colonPos)) {
                                    $fontFamily = substr($font, 0, $colonPos);

                                    $fontFamilies[$fontFamily]['font'] = $fontFamily;

                                    $fontOptions = explode(',', substr($font, $colonPos+1));

                                    if (!empty($fontOptions)) {
                                        foreach ($fontOptions as $options) {
                                            $fontFamilies[$fontFamily]['options'][$options] = $options;
                                        }
                                    }
                                } else {
                                    $fontFamilies[$font]['font'] = $font;
                                }
                            }
                        }
                    }

                    if (!empty($query['subset'])) {
                        $subsets = explode(',', $query['subset']);

                        if (!empty($subsets)) {
                            foreach ($subsets as $subset) {
                                $fontSubsets[$subset] = $subset;
                            }
                        }
                    }
                }
            }

            if (!empty($fontFamilies)) {
                $urlFamily = '';

                foreach ($fontFamilies as $font) {
                    if (!empty($font['font'])) {
                        Factory::get('Cache\Log')->addLog(__METHOD__, 'Process Google Font: '.$font['font']);

                        $urlFamily .= $font['font'];

                        if (!empty($font['options'])) {
                            $urlFamily .= ':'.implode(',', $font['options']);
                        }

                        $urlFamily .= '|';
                    }
                }

                $urlFamily = rtrim($urlFamily, '|');

                $urlFamily = urlencode($urlFamily);

                $this->htmlGoogleFont = '<link defer href="https://fonts.googleapis.com/css?family='.$urlFamily.(!empty($fontSubsets) ? '&amp;subset='.urlencode(implode(',', $fontSubsets)) : '').'" rel="stylesheet">';
            }
        }
    }

    /**
     * createCSSFile function.
     *
     * @access public
     * @return void
     */
    public function createCSSFile()
    {
        global $wp_styles;

        $source = implode("\n", $this->sourceCodeStyles);

        if (!empty($this->importStatements)) {
            $source = implode("\n", $this->importStatements)."\n".$source;
        }

        if (Factory::get('Cache\Config')->get('stylesMinify')) {
            if (Factory::get('Cache\Debug')->isDebugEnabled() == false) {
                Factory::get('Cache\Frontend\Minify')->minifyCSS($source);
            }
        }

        $extension = 'css';

        if (Factory::get('Cache\Config')->get('stylesGzipOutput')) {
            $extension = 'php';

            $source = Factory::get('Cache\Frontend\Styles')->addGzipCode($source);
        }

        $suffix = Factory::get('Cache\Frontend\Resolver')->isHTTPS() ? '_https' : '';

        $filename = Factory::get('Cache\Tools')->getHash($this->sourceCodeStyles).$suffix.'.'.$extension;

        Factory::get('Cache\Frontend\Cache')->createCachefile($filename, $source, 'css');

        $cssURL = content_url().'/cache/oso_super_cache/'.Factory::get('Cache\Frontend\Resolver')->getCurrentBlogId().'/css/'.$filename;


        //TODO enable with feature added option 'enable preload'
//        if (Factory::get('Cache\Config')->get('stylesPreloadTag')) {
//            // Add to preload stack
//            Factory::get('Cache\Frontend\Preload')->add($cssURL, 'style');
//        }

        //TODO add 'defer option' into plugin settings
        $this->htmlStyle = '<link rel="stylesheet" defer href="'.$cssURL.'" type="text/css" media="all">';
    }

    /**
     * addGzipCode function.
     *
     * @access public
     * @param mixed $cssCode
     * @return void
     */
    public function addGzipCode($cssCode)
    {
        $topPHPCode = "<?php\n";
        $topPHPCode .= "include_once '".Factory::get('Cache\Frontend\Header')->classLocation()."'; \n";
        $topPHPCode .= Factory::get('Cache\Frontend\Header')->getContentTypeHeaderCode('text/css', true)."\n";
        $topPHPCode .= "\OSOSuperCache\Cache\Frontend\Header::getInstance()->setConfig(unserialize(base64_decode('".base64_encode(serialize(Factory::get('Cache\Frontend\Header')->getHeaderRelatedConfigs()))."')));\n";
        $topPHPCode .= '\OSOSuperCache\Cache\Frontend\Header::getInstance()->getHeader(__FILE__);'."\n";
        $topPHPCode .= "ini_set('zlib.output_compression_level', ".Factory::get('Cache\Config')->get('stylesGzipCompressionLevel').");\n";
        $topPHPCode .= "if (ini_get('zlib.output_compression')) {\n";
        $topPHPCode .=  "ob_start();\n";
        $topPHPCode .= "} else {\n";
        $topPHPCode .=  "ob_start('ob_gzhandler');\n";
        $topPHPCode .= "}\n";
        $topPHPCode .= "?>\n";

        $bottomPHPCode = "\n<?php ob_end_flush(); ?>";

        return $topPHPCode.$cssCode.$bottomPHPCode;
    }

    public function getHTMLStyle()
    {
        return $this->htmlStyle;
    }

    public function getHTMLGoogleFont()
    {
        return $this->htmlGoogleFont;
    }

    public function excludeStyles(){
        //TODO make some checking if there are settings to use or not page optimization

        $pageOptimization = Factory::get('Cache\Config')->get('pageOptimization', 'inactive');
        if(!empty($pageOptimization['excludedStyles'])){
            foreach($pageOptimization['excludedStyles'] as $pageId => $excludedStyle){
                if(is_page($pageId)){
                    $styles = explode(' ',$excludedStyle);
                    foreach ($styles as $style){
                        wp_dequeue_style($style);
                    }
                }
            }
        }
    }
}
