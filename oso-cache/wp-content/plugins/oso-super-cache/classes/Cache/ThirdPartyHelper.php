<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class ThirdPartyHelper
{

    private static $instance;

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
    }

    /**
     * getInitHook function gives you the info, which hook OSO Super  Cache
     * is using to start handling the request.
     *
     * @access public
     * @return void
     */
    public function getInitHook()
    {
        $hook = 'plugins_loaded';

        if (Factory::get('Cache\Config')->get('cacheLateInitialization') == false) {
            $hook = 'plugins_loaded';
        } elseif (Factory::get('Cache\Config')->get('cacheLateInitialization') == 'late') {
            $hook = 'wp_loaded';
        } elseif (Factory::get('Cache\Config')->get('cacheLateInitialization') == 'super-late') {
            $hook = 'wp';
        }

        return $hook;
    }

    /**
     * getFragmentCachingPhrase function gives you the mask phrase but only
     * if Fragment Caching is activated.
     *
     * @access public
     * @return void
     */
    public function getFragmentCachingPhrase()
    {
        $maskPhrase = false;

        if (Factory::get('Cache\Config')->get('fragmentCaching')) {
            $maskPhrase =  Factory::get('Cache\Config')->get('fragmentCachingMaskPhrase');
        }

        return $maskPhrase;
    }

    /**
     * isActivated function gives you the info, if OSO Super Cache is activated.
     *
     * @access public
     * @return void
     */
    public function isActivated()
    {
        $status = false;

        if (Factory::get('Cache\Config')->get('cacheActivated') == 'yes') {
            $status = true;
        }

        return $status;
    }

    /**
     * isFragmentCachingActivated function gives you the info, if
     * Fragment Caching is activated.
     *
     * @access public
     * @return void
     */
    public function isFragmentCachingActivated()
    {
        $status = false;

        if (Factory::get('Cache\Config')->get('fragmentCaching')) {
            $status = true;
        }

        return $status;
    }

    /**
     * willFragmentCachingPerform function gives you the information, if
     * OSO Super  Cache will perform its Fragment Caching technique on the requested page.
     * This information is complete after the wp hook.
     * Please let us know, if you need this information before or at the "wp" hook because the first hook you can use
     * is the template_redirect hook.
     *
     * @access public
     * @return void
     */
    public function willFragmentCachingPerform()
    {
        $status = false;

        if (Factory::get('Cache\Config')->get('cacheActivated') == 'yes') {
            if (Factory::get('Cache\Config')->get('fragmentCaching')) {
                if (Factory::get('Cache\Frontend\HandleRequest')->cacheRequest) {
                    $status = true;
                }
            }
        }

        return $status;
    }

    /**
     * willRequestedPageCached function gives you the information, if the
     * requested page will be cached or not. This information is complete after the wp hook.
     * Please let us know, if you need this information before or at the "wp" hook because the first hook you can use
     * is the template_redirect hook.
     *
     * @access public
     * @return void
     */
    public function willRequestedPageCached()
    {
        $status = false;

        if (Factory::get('Cache\Config')->get('cacheActivated') == 'yes') {
            if (Factory::get('Cache\Frontend\HandleRequest')->cacheRequest) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * localizeScript function is identical to wp_localize_script() except it will be excluded from the JS merging process
     * and you can create cacheable Nonces. To do so just write a value like this wp_create_nonce__YourNonceName
     * This will be detected by OSO Super  Cache and transformed into wp_create_nonce('YourNonceName'); the Nonce is now cacheable.
     *
     * @access public
     * @param mixed $handle
     * @param mixed $name
     * @param mixed $data
     * @return void
     */
    public function localizeScript($handle, $name, $data)
    {
        if ($this->willRequestedPageCached()) {
            Factory::get('Cache\Frontend\Scripts')->registerLocalizeScriptData($handle, $name, $data);
        } else {
            // Fallback
            wp_localize_script($handle, $name, $data);
        }
    }
}
