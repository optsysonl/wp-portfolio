<?php


require_once WPEA_PLUGIN_DIR . '/includes/functions.php';
require_once WPEA_PLUGIN_DIR . '/includes/formatting.php';
require_once WPEA_PLUGIN_DIR . '/includes/emailarts-form-functions.php';
require_once WPEA_PLUGIN_DIR . '/includes/validation-functions.php';
require_once WPEA_PLUGIN_DIR . '/includes/I10n.php';
require_once WPEA_PLUGIN_DIR . '/includes/form-functions.php';

require_once WPEA_PLUGIN_DIR . '/includes/classes/ConfigValidator.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/EmailArts.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/EmailArtsForms.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/EmailArtsFormsList.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/EmailArtsHTMLFormatter.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/EmailArtsTemplate.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/EmailArtsUpdate.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/FormTag.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/FormTagsManager.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/PocketHolder.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/ShortcodesManager.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/Validation.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/WPEAPipe.php';
require_once WPEA_PLUGIN_DIR . '/includes/classes/WPEAPipes.php';

require_once WPEA_PLUGIN_DIR . '/includes/modules/hidden.php';
require_once WPEA_PLUGIN_DIR . '/includes/modules/response.php';
require_once WPEA_PLUGIN_DIR . '/includes/modules/submit.php';
require_once WPEA_PLUGIN_DIR . '/includes/modules/text.php';

require_once WPEA_PLUGIN_DIR . '/includes/swv/swv.php';

//load MailWizzApi
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Base.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Config.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Json.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Params.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/ParamsIterator.php';

require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Cache/Abstract.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Cache/Apc.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Cache/Database.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Cache/Dummy.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Cache/File.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Cache/Xcache.php';

require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/CampaignBounces.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/Campaigns.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/CampaignsTracking.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/Countries.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/Customers.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/ListFields.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/Lists.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/ListSegments.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/ListSubscribers.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/Templates.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Endpoint/TransactionalEmails.php';

require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Http/Client.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Http/Request.php';
require_once WPEA_PLUGIN_DIR . '/includes/MailWizzApi/Http/Response.php';

if ( is_admin() ) {
    require_once WPEA_PLUGIN_DIR . '/admin/admin.php';
} else {
    require_once WPEA_PLUGIN_DIR . '/includes/controller.php';
}

add_action('plugins_loaded', 'wpea', 10, 0);
function wpea(){
    add_shortcode('emailarts', 'wpea_form_tag_function'); //TODO add wpea_form_function
}

add_action('init', 'wpea_init', 10, 0);
function wpea_init(){
    wpea_get_request_uri();
    wpea_register_post_types();

    do_action( 'wpea_init' );
}

add_action( 'admin_init', 'wpea_upgrade', 10, 0 );
function wpea_upgrade() {
    add_action('plugins_api', array('EmailArtsUpdate', 'handlePluginAPI'), 9002, 3);
    add_filter('pre_set_site_transient_update_plugins', array('EmailArtsUpdate', 'handleTransientUpdatePlugins'));
}