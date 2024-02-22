<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class Feed {

    private static $instance;

    protected $feedContentType = 'application/octet-stream';

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone () {}

    private function __wakeup () {}

    protected function __construct () {}

    /**
     * handleFeed function.
     *
     * @access public
     * @param mixed $forComments (default: null)
     * @param mixed $feed
     * @return void
     */
    public function handleFeed ($forComments = null, $feed) {

        // Check if current page should be cached
        if (Factory::get('Cache\Frontend\Cache')->bufferActive) {

            if (function_exists('feed_content_type')) {
                $this->feedContentType = \feed_content_type($feed);
            }

            if (function_exists('do_feed_'.$feed)) {
                call_user_func('do_feed_'.$feed, $forComments);
            }

            Factory::get('Cache\Frontend\HandleRequest')->finishCaching();

            exit;
        }
    }

    /**
     * prepareCacheContent function.
     *
     * @access public
     * @param mixed &$xmlContent
     * @return void
     */
    public function prepareCacheContent (&$xmlContent) {

        $maskStart = Factory::get('Cache\Tools')->generateRandomString(32);
        $maskEnd = Factory::get('Cache\Tools')->generateRandomString(32);

        // Replace <? to avoid php with active short_tags cause a fatal error
        $xmlContent = str_replace(['<?', '?>'], [$maskStart, $maskEnd], $xmlContent);
        $xmlContent = str_replace([$maskStart, $maskEnd], ['<?php echo \'<?\'; ?>', '<?php echo \'?>\'; ?>'], $xmlContent);
        $xmlContent = '<?php header(\'Content-Type: '.$this->feedContentType.'; charset='.get_option('blog_charset').'\'); ?>'.$xmlContent;
    }
}
?>