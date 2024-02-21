<?php
/**
 * Created by PhpStorm.
 * User: optsysonl
 * Date: 23.07.2021
 * Time: 13:53
 */

class CustomWCProductAddonsCart extends WC_Product_Addons_Cart
{
    public function __construct()
    {

    }

    public function init(){
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data_custom' ), 11, 2 );
    }

    public function add_cart_item_data_custom( $cart_item_data, $product_id ) {
        if ( isset( $_POST ) && ! empty( $product_id ) ) {
            $post_data = $_POST;
        } else {
            return;
        }

        $product_addons = WC_Product_Addons_Helper::get_product_addons( $product_id );
//        var_dump($cart_item_data['booking_extra']);
        if ( empty( $cart_item_data['booking_extra'] ) ) {
            $cart_item_data['booking_extra'] = array();
        }

        if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
            include_once( dirname( __FILE__ ) . '/fields/abstract-wc-product-addons-field.php' );

            foreach ( $product_addons as $addon ) {
                // If type is heading, skip.
                if ( 'heading' === $addon['type'] ) {
                    continue;
                }

                $value = wp_unslash(isset($cart_item_data['booking_extra'][$addon['field_name']]) ? $cart_item_data['booking_extra'][$addon['field_name']] : '');
//                $value = wp_unslash( isset( $post_data[ 'addon-' . $addon['field_name'] ] ) ? $post_data[ 'addon-' . $addon['field_name'] ] : '' );

                switch ( $addon['type'] ) {
                    case 'checkbox':
                        include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php' );
                        $field = new WC_Product_Addons_Field_List( $addon, $value );
                        break;
                    case 'multiple_choice':
                        switch ( $addon['display'] ) {
                            case 'radiobutton':
                                include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-list.php' );
                                $field = new WC_Product_Addons_Field_List( $addon, $value );
                                break;
                            case 'images':
                            case 'select':
                                include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-select.php' );
                                $field = new WC_Product_Addons_Field_Select( $addon, $value );
                                break;
                        }
                        break;
                    case 'custom_text':
                    case 'custom_textarea':
                    case 'custom_price':
                    case 'input_multiplier':
                        include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-custom.php' );
                        $field = new WC_Product_Addons_Field_Custom( $addon, $value );
                        break;
                    case 'file_upload':
                        include_once( dirname( __FILE__ ) . '/fields/class-wc-product-addons-field-file-upload.php' );
                        $field = new WC_Product_Addons_Field_File_Upload( $addon, $value );
                        break;
                }

                $data = $field->get_cart_item_data();

                if ( is_wp_error( $data ) ) {
                    // Throw exception for add_to_cart to pickup.
                    throw new Exception( $data->get_error_message() );
                } elseif ( $data ) {
                    $cart_item_data['addons'] = array_merge( $cart_item_data['addons'], apply_filters( 'woocommerce_product_addon_cart_item_data', $data, $addon, $product_id, $post_data ) );
                }
            }
        }

        return $cart_item_data;
    }
}