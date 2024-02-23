<?php


class EmailArtsUpdate
{
    public static function getURL(){
        $updateURL = 'https://software.osobrand.net/plugins/emailarts';
        return $updateURL;
    }

    /**
     *
     */
    public function handlePluginAPI($result, $action, $args){
        if (!empty($action) && $action == 'plugin-information' && !empty($args->slug)) {
            if ($args->slug == dirname(WPEA_PLUGIN_SLUG)) {
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
            EmailArtsUpdate::getURL().'/index.php',
            [
                'timeout'   =>45,
                'body'      =>[
                    'version'=>WPEA_VERSION,
                    'product'=>dirname(WPEA_PLUGIN_SLUG),
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
    public static function handleTransientUpdatePlugins($transient){
        if(isset($transient->response[WPEA_PLUGIN_SLUG])){
            return $transient;
        }

        $updateInformation = self::getLatestVersion();
        if (!empty($updateInformation)) {
            if (version_compare(WPEA_VERSION, $updateInformation->new_version, '<')) {
                $transient->response[WPEA_PLUGIN_SLUG] = $updateInformation;
            }
        }
        return $transient;
    }

    /**
     * @name getLatestVersion
     *
     * @access public
     */
    public static function getLatestVersion(){
        $response = wp_remote_post(
            self::getURL().'/index.php',
            [
                'timeout'   =>45,
                'body'      =>[
                    'version'=>WPEA_VERSION,
                    'product'=>dirname(WPEA_PLUGIN_SLUG),
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