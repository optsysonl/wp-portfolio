<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Optimizer
{

    private static $instance;

    private $homePath                   = null;
    private $blogHost                   = null;
    private $blogPath                   = null;

    private $preservedTags              = [];
    private $excludedSections           = [];

    public static function getInstance ()
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
        $this->blogPath = !empty($urlInfo['path']) ? $urlInfo['path'] : '';
        $this->deferActive = Factory::get('Cache\Config')->get('scriptsDefer');
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
        global $wp_scripts;

        $html = apply_filters('oso_super_cache_before_optimization', $html);

        if (Factory::get('Cache\Config')->get('scriptsMerge') || Factory::get('Cache\Config')->get('stylesMerge')) {

            // Escape conditional tags
            $html = preg_replace_callback('/<!--\[([^\]]+)\]>(.*)\<\!\[endif\]--\>/Us', [$this, 'maskTags'], $html);

            // Escape noscript tags
            $html = preg_replace_callback('/<noscript.*<\/noscript>/Us', [$this, 'maskTags'], $html);

            if (Factory::get('Cache\Config')->get('scriptsMerge')) {
                // Optimize JavaScript
                Factory::get('Cache\Frontend\Scripts')->optimize($html);
            } else {
                // If disabled we need to preserve the script tags because <style> tags can be inside of a <script>
                $html = preg_replace_callback('/<script.*<\/script>/Us', [$this, 'maskTags'], $html);
            }

            if (Factory::get('Cache\Config')->get('stylesMerge')) {
                // Optimize CSS
                Factory::get('Cache\Frontend\Styles')->optimize($html);
            }

            // Replace conditional tags (and script-tags) back
            $this->reInsertPreservedTags($html, true);

            /*
                BUG: Prefetch not working when stylesMerge is disabled
            */

            // Set link to css
            if (Factory::get('Cache\Config')->get('stylesMerge')) {
                if (Factory::get('Cache\Config')->get('stylesLocation') == 'footer') {
                    $html = preg_replace(
                        '/\<head(\s[^>]*?)?>/',
                        '<head$1>'
                        . (Factory::get('Cache\Config')->get('dnsPrefetch') ? Factory::get('Cache\Frontend\Prefetch')->getDNSPrefetchHTML() : '')
                        /* Todo dnsPrefetch > preload */
                        . (Factory::get('Cache\Config')->get('dnsPrefetch') ? Factory::get('Cache\Frontend\Preload')->getPreloadHTML() : '')
                        . Factory::get('Cache\Frontend\Styles')->getHTMLGoogleFont(),
                        $html
                    );
                    if (Factory::get('Cache\Config')->get('fragmentCaching')) {
                        $html .= Factory::get('Cache\Frontend\Styles')->getHTMLStyle();
                    } else {
                        $html = str_replace('</body>', Factory::get('Cache\Frontend\Styles')->getHTMLStyle() . '</body>', $html);
                    }
                } else {
                    $html = preg_replace(
                        '/\<head(\s[^>]*?)?>/',
                        '<head$1>'
                        . (Factory::get('Cache\Config')->get('dnsPrefetch') ? Factory::get('Cache\Frontend\Prefetch')->getDNSPrefetchHTML() : '')
                        /* Todo dnsPrefetch > preload */
                        . (Factory::get('Cache\Config')->get('dnsPrefetch') ? Factory::get('Cache\Frontend\Preload')->getPreloadHTML() : '')
                        . Factory::get('Cache\Frontend\Styles')->getHTMLStyle()
                        .Factory::get('Cache\Frontend\Styles')->getHTMLGoogleFont(),
                        $html
                    );
                }
            }

            if (Factory::get('Cache\Config')->get('scriptsMerge')) {
                if (Factory::get('Cache\Config')->get('scriptsLocation') == 'footer') {
                    // Set link to js in the footer
                    if (Factory::get('Cache\Config')->get('fragmentCaching')) {
                        $html .= Factory::get('Cache\Frontend\Scripts')->getHTMLScript();
                    } else {
                        $html = str_replace('</body>', Factory::get('Cache\Frontend\Scripts')->getHTMLScript().'</body>', $html);
                    }
                } else {
                    // Set link to js in the head
                    $html = str_replace('</head>', Factory::get('Cache\Frontend\Scripts')->getHTMLScript().'</head>', $html);
                }
            }
        }

        $html = apply_filters('oso_super_cache_after_optimization', $html);
    }

    /**
     * placeLocalizedScriptData function.
     *
     * @access public
     * @param mixed &$html
     * @param bool $phpCode (default: true)
     * @return void
     */
    public function placeLocalizedScriptData (&$html, $phpCode = true)
    {
        // Check if localized script data was registered
        $localizeScriptData = Factory::get('Cache\Frontend\Scripts')->getLocalizedScriptData();

        if (!empty($localizeScriptData)) {

            if ($phpCode) {
                $localizeScriptDataCode = Factory::get('Cache\Frontend\Scripts')->getLocalizedScriptDataCode();
            } else {
                $localizeScriptDataCode = Factory::get('Cache\Frontend\Scripts')->processLocalizedScriptData($localizeScriptData);
            }

            $positionOfFirstScriptTag = strpos($html, '<script');

            if ($positionOfFirstScriptTag !== false) {
                $html = substr_replace($html, $localizeScriptDataCode."\n<script", $positionOfFirstScriptTag, 7); // 7 = <script
            }
        }
    }

    /**
     * maskTags function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    public function maskTags($tag)
    {
        $uniqueToken = Factory::get('Cache\Tools')->generateRandomString(32);

        $this->preservedTags[$uniqueToken] = $tag[0];

        return $uniqueToken;
    }

    /**
     * reInsertPreservedTags function.
     *
     * @access public
     * @param mixed &$html
     * @param bool $freeResources (default: false)
     * @return void
     */
    public function reInsertPreservedTags(&$html, $freeResources = false)
    {
        if (!empty($this->preservedTags)) {
            foreach ($this->preservedTags as $uniqueToken => $tag) {
                $html = str_replace($uniqueToken, $tag, $html);
            }

            if ($freeResources) {
                // Free resources
                unset($this->preservedTags);
            }
        }
    }

    /**
     * maskXMLTags function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function maskXMLTags(&$html)
    {
        $html = preg_replace_callback('/\<\?xml([^>]*)\>/', [$this, 'replaceXMLTags'], $html);
    }

    /**
     * fixXMLTags function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    private function replaceXMLTags($tag)
    {
        return '<?php echo "<?"."xml"; ?>'.$tag[1].'>';
    }

    /**
     * excludeSection function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function excludeSection(&$html)
    {
        $html = preg_replace_callback('/<!--\[oso-super-cache-exclude-start\]-->(.*)\<\!--\[oso-super-cache-exclude-end\]--\>/Us', [$this, 'extractSection'], $html);
    }

    /**
     * extractSection function.
     *
     * @access public
     * @param mixed $tag
     * @return void
     */
    public function extractSection($tag)
    {
        $uniqueToken = Factory::get('Cache\Tools')->generateRandomString(32);

        $this->excludedSections[$uniqueToken] = $tag[1]; // [1] because we want to remove the html-comments

        return $uniqueToken;
    }

    /**
     * reInsertSection function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function reInsertSection(&$html, $freeResources = false)
    {
        if (!empty($this->excludedSections)) {
            foreach ($this->excludedSections as $uniqueToken => $section) {
                $html = str_replace($uniqueToken, $section, $html);
            }

            if ($freeResources) {
                // Free resources
                unset($this->excludedSections);
            }
        }
    }
}
