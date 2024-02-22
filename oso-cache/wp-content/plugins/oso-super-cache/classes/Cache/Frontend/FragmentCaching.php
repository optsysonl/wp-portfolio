<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Frontend;

use OSOSuperCache\Factory;

class FragmentCaching {

    private static $instance;

    protected $fragmentCachingMaskPhrase;

    public static function getInstance () {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct () {
        $this->fragmentCachingMaskPhrase = Factory::get('Cache\Config')->get('fragmentCachingMaskPhrase');
    }

    /**
     * parseContentForFragments function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function parseContentForFragments (&$html) {

        $html = str_replace([
                                '<!--[oso-super-cache start: '.$this->fragmentCachingMaskPhrase.']-->',
                                '<!--[oso-super-cache end: '.$this->fragmentCachingMaskPhrase.']-->',
                                '{'.$this->fragmentCachingMaskPhrase.'}',
                                '{/'.$this->fragmentCachingMaskPhrase.'}',
                            ], [
                                '<?php ',
                                ' ?>',
                                '<?php ',
                                ' ?>',
                            ], $html);
    }

    /**
     * parseContentAndExecuteFragments function.
     *
     * @access public
     * @param mixed &$html
     * @return void
     */
    public function parseContentAndExecuteFragments (&$html) {
        $html = preg_replace_callback('/\<\!--\[oso-super-cache start: '.$this->fragmentCachingMaskPhrase.'\]\-\-\>(.*)\<\!--\[oso-super-cache end: '.$this->fragmentCachingMaskPhrase.'\]--\>/Us', [$this, 'executeCode'], $html);
    }

    /**
     * executeCode function.
     *
     * @access public
     * @param mixed $code
     * @return void
     */
    public function executeCode ($code) {

        $parsedResult = null;

        if (!empty($code[1])) {

            try {

                $code[1] = str_replace(
                    [
                        '{'.$this->fragmentCachingMaskPhrase.'}',
                        '{/'.$this->fragmentCachingMaskPhrase.'}',
                    ],
                    [
                        '<?php',
                        '?>'
                    ],
                    $code[1]
                );

                ob_start();
                eval($code[1]);
                $parsedResult = ob_get_contents();
                ob_end_clean();

            } catch (\Exception $e) {

                error_log($e->getMessage());
            }
        }

        return $parsedResult;
    }
}
?>