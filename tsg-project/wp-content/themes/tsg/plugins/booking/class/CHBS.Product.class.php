<?php
/**
 * Class WC_Product_Chauffeur
 * @description Product that use as base for creating
 * 'chauffeur transfer' variation products from him.
 */


class WC_Product_Chauffeur extends WC_Product
{
    public function __construct($product = 0)
    {
        $this->product_type = 'chauffeur';
        parent::__construct($product);
    }

    public function init()
    {
        add_filter('product_type_selector', array($this, 'product_data_tab'));
        add_filter( 'woocommerce_product_data_tabs', array($this, 'product_tab' ));
        add_action( 'admin_footer', array($this, 'chauffeur_booking_custom_js' ));

//        add_action( 'woocommerce_product_data_panels', array($this, 'product_tab_product_tab_content' ));
        add_action( 'woocommerce_process_product_meta', array($this, 'save_product_settings' ));
        add_filter( 'woocommerce_product_class', array($this, 'woocommerce_product_class'), 10, 2 );
    }

    public function woocommerce_product_class($classname, $product_type)
    {
        if ( $product_type == 'chauffeur' ) {
            $classname = 'WC_Product_Chauffeur';
        }

        return $classname;
    }

    public function product_data_tab($types)
    {
        $types['chauffeur'] = __('Airport Transfer Product', 'chauffeur-booking-system');
        return $types;
    }

    function product_tab ($tabs)
    {
//        array_push($tabs['attribute']['class'], 'show_if_variable show_if_chauffeur');
//        array_push($tabs['variations']['class'], 'show_if_chauffeur');
        return $tabs;
    }

    public function chauffeur_booking_custom_js()
    {
        if ( 'product' != get_post_type() ) {
            return;
        }

        ?>
            <script type='text/javascript'>
            jQuery( document ).ready( function() {
                jQuery( '.options_group.pricing' ).addClass( 'show_if_chauffeur' ).show();
                jQuery('.product_data_tabs .general_tab').addClass('show_if_chauffeur').show();
                jQuery('.variations_options.variations_tab').addClass('show_if_chauffeur').show();
                jQuery('#general_product_data .pricing').addClass('show_if_chauffeur').show();
                jQuery('#variable_product_options').addClass('show_if_chauffeur').show();
                jQuery('.enable_variation').addClass('show_if_chauffeur').show(); //show 'used for variations checkbox'
                //for Inventory tab
                // jQuery('.inventory_options').addClass('show_if_chauffeur').show();
                // jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_chauffeur').show();
                // jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_chauffeur').show();
                // jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_chauffeur').show();
            });
            </script>
        <?php
    }
}