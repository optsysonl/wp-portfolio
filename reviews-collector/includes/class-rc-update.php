<?php

/*
 *
 */

class RC_Review_Update {

    public function getURL(){
        $updateURL = 'https://software.osobrand.net/plugins/reviews-collector';
        return $updateURL;
    }

    /**
     *
     */
    public function handlePluginAPI($result, $action, $args){
        if (!empty($action) && $action == 'plugin-information' && !empty($args->slug)) {
            if ($args->slug == dirname(RC_PLUGIN_SLUG)) {
                $result = $this->getPluginInformation();
            }
        }

        return $result;
    }

    /**
     *
     */
    public function getPluginInformation(){
        $response = wp_remote_post(
            RC_Review_Update::getURL().'/index.php',
            [
                'timeout'   =>45,
                'body'      =>[
                    'version'=>RC_VERSION,
                    'product'=>dirname(RC_PLUGIN_SLUG),
                ]
            ]
        );

        if (!empty($response) && is_array($response) && !empty($response['body'])) {
            $body = json_decode($response['body']);

            if (!empty($body->success) && !empty($body->pluginInformation)) {
                return unserialize($body->pluginInformation);
            }
        }
    }

    /**
     * @name handleTransientUpdatePlugins
     * @param $transient
     * @return mixed
     */
    public function handleTransientUpdatePlugins($transient){
        if(isset($transient->response[RC_PLUGIN_SLUG])){
            return $transient;
        }

        $updateInformation = self::getLatestVersion();
        if (!empty($updateInformation)) {
            if (version_compare(RC_VERSION, $updateInformation->new_version, '<')) {
                $transient->response[RC_PLUGIN_SLUG] = $updateInformation;
            }
        }
        return $transient;
    }

    /**
     * @name getLatestVersion
     *
     * @access public
     * @return void
     */
    public function getLatestVersion(){
        $response = wp_remote_post(
            self::getURL().'/index.php',
            [
                'timeout'   =>45,
                'body'      =>[
                    'version'=>RC_VERSION,
                    'product'=>dirname(RC_PLUGIN_SLUG),
                ],
            ]
        );

        if (!empty($response) && is_array($response) && !empty($response['body'])) {
            $body = json_decode($response['body'], false);
            $body->updateInformation->icons = json_decode(json_encode($body->updateInformation->icons), true);
            $body->updateInformation->banners = json_decode(json_encode($body->updateInformation->banners), true);
            $body->updateInformation->banners_rtl = json_decode(json_encode($body->updateInformation->banners_rtl), true);

            if (!empty($body->success) && !empty($body->updateInformation)) {
                return $body->updateInformation;
            }
        }
    }
}
