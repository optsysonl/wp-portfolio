<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class CDNOther
{

    private static $instance;

    private $cdnURL;

    public static function getInstance()
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
        $this->cdnURL = (Factory::get('Cache\Frontend\Resolver')->isHTTPS() ? 'https://' : 'http://').Factory::get('Cache\Config')->get('cdnURL');
    }

    /**
     * processHTML function.
     *
     * @access public
     * @param mixed &$sourceCode
     * @return void
     */
    public function processHTML(&$sourceCode)
    {
        Factory::get('Cache\Frontend\CDN')->modifySrc($sourceCode, [Factory::get('Cache\Frontend\CDNOther'), 'modifySrcCallback']);
        Factory::get('Cache\Frontend\CDN')->modifySrcset($sourceCode, [Factory::get('Cache\Frontend\CDNOther'), 'modifySrcsetCallback']);
        Factory::get('Cache\Frontend\CDN')->modifyLink($sourceCode, [Factory::get('Cache\Frontend\CDNOther'), 'modifyLinkCallback']);

        Factory::get('Cache\Frontend\CDN')->modifyBackgroundUrl($sourceCode, [Factory::get('Cache\Frontend\CDNOther'), 'modifyBackgroundUrlCallback']);
    }

    /**
     * modifySrcCallback function.
     *
     * @access public
     * @param mixed $matches
     * @return void
     */
    public function modifySrcCallback($matches)
    {
        $tag = $matches[0];

        if (!empty($matches[4])) {
            $orgSrc = $matches[3];

            $tag = str_replace(
                $orgSrc,
                str_replace(
                    $matches[4],
                    $this->modifyURL($matches[4]),
                    $matches[3]
                ),
                $matches[0]
            );
        }

        return $tag;
    }

    /**
     * modifySrcsetCallback function.
     *
     * @access public
     * @param mixed $matches
     * @return void
     */
    public function modifySrcsetCallback($matches)
    {
        $tag = $matches[0];

        if (!empty($matches[4])) {
            $orgSrcset = $matches[3];

            // Split srcsets
            $sources = explode(',', $matches[4]);

            foreach ($sources as $key => $source) {
                $source = trim($source);
                $sourceParts = explode(' ', $source);
                $sources[$key] = $this->modifyURL($sourceParts[0]).(isset($sourceParts[1]) ? ' '.$sourceParts[1] : '');
            }

            $tag = str_replace(
                $orgSrcset,
                str_replace(
                    $matches[4],
                    implode(', ', $sources),
                    $matches[3]
                ),
                $matches[0]
            );
        }

        return $tag;
    }

    /**
     * modifyLinkCallback function.
     *
     * @access public
     * @param mixed $matches
     * @return void
     */
    public function modifyLinkCallback($matches)
    {
        // no rel OR rel is stylesheet or icon -> replace href
        $tag = $matches[0];

        if (!empty($matches[3])) {
            $rel = strpos($matches[0], 'rel=');

            if ($rel === false || preg_match('/\<link([^>]+?)(rel=["|\']?(stylesheet|([^"\']*?)icon|([^"\']*?)preload)["|\']?)([^>]*?)\>/', $matches[0])) {
                $orgHref = $matches[2];

                $tag = str_replace(
                    $orgHref,
                    str_replace(
                        $matches[3],
                        $this->modifyURL($matches[3]),
                        $matches[2]
                    ),
                    $matches[0]
                );
            }
        }

        return $tag;
    }

    /**
     * @method modifyBackgroundUrlCallback
     *
     * @access public
     * @param mixed $matches
     * @return string
     */
    public function modifyBackgroundUrlCallback($matches){
        $url = $matches[0];

        if(!empty($matches[2])){
            $url = str_replace(
                $matches[2],
                $this->modifyURL($matches[2]),
                $matches[0]
            );
        }

        return $url;
    }

    /**
     * modifyURL function.
     *
     * @access public
     * @param mixed $orgURL
     * @return void
     */
    public function modifyURL($orgURL)
    {
        $urlInfo = parse_url($orgURL);

        if (!empty($urlInfo['host'])) {
            if ($urlInfo['host'] == Factory::get('Cache\Frontend\Resolver')->getRequestedDomain()) {
                return $this->cdnURL.'/'.(!empty($urlInfo['path']) ? ltrim($urlInfo['path'], '/') : '').(!empty($urlInfo['query']) ? '?'.$urlInfo['query'] : '');
            } else {
                return $orgURL;
            }
        } elseif (strpos($orgURL, 'data:') !== false) {
            return $orgURL;
        } else {
            return $this->cdnURL.'/'.ltrim($orgURL);
        }
    }
}
