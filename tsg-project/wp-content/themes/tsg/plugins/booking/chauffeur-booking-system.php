<?php

load_plugin_textdomain('chauffeur-booking-system',false,dirname(plugin_basename(__FILE__)).'/languages/');

require_once('include.php');

$Plugin=new CHBSPlugin();
$WooCommerce=new CHBSWooCommerce();

register_activation_hook(__FILE__,array($Plugin,'pluginActivation'));

add_action('init',array($Plugin,'init'));
add_action('after_setup_theme',array($Plugin,'afterSetupTheme'));
add_filter('woocommerce_locate_template',array($WooCommerce,'locateTemplate'),1,3);

$WidgetBookingForm=new CHBSWidgetBookingForm();
$WidgetBookingForm->register();