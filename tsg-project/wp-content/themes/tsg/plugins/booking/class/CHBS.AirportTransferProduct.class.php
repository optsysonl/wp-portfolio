<?php
/**
 * Created by PhpStorm.
 * User: optsysonl
 * Date: 23.07.2021
 * Time: 13:49
 */

class AirportTransferProduct
{
    public function __construct()
    {
    }

    public function init(){

    }

    /**
     * @name get_product_variation
     *
     * @param $product_data
     * @param $detail
     * @return int|WP_Error
     * @throws Exception
     */
    public function get_product_variation($product_id, $product_data, $detail){
        if($product_id == 0){
            return 0;
        }
        // allways need to fill all attribute values
        $passenger = (int)$detail['passenger_adult'] + (int)$detail['passenger_children'];
        $match_attributes =  array(
            "attribute_passengers" => (string) $passenger,
            "attribute_location-pickup" => $detail['pickup_location_short'],
            "attribute_location-dropoff" => $detail['dropoff_location_short'],
            "attribute_vehicle" => $detail['vehicle_name']
        );
        //find variation by attribute options
        $data_store   = WC_Data_Store::load( 'product' );
        $variation_id = $data_store->find_matching_product_variation(
            new \WC_Product( $product_id), $match_attributes
        );
        //if variation is not exist create it
        if(!$variation_id){
            $variation_id = $this->add_new_variation($product_id, $match_attributes, $product_data, $detail);
        }

        return $variation_id;
    }

    /**
     * @name add_new_variation
     * @description Addd new variation and create new attribute & options when needed
     *
     * @param $product_id
     * @param $variations
     * @param $product_data
     * @param $detail
     * @return int|WP_Error
     */
    public function add_new_variation($product_id, $variations, $product_data, $detail){
        $product = wc_get_product($product_id);

        $variation_post = array(
            'post_title'  => $product->get_name(),
            'post_name'   => 'product-'.$product_id.'-variation',
            'post_status' => 'publish',
            'post_parent' => $product_id,
            'post_type'   => 'product_variation',
            'guid'        => $product->get_permalink(),
        );

        $variation_id = wp_insert_post( $variation_post );
        $variation = new WC_Product_Variation($variation_id);
        $attributes = get_post_meta( $product_id ,'_product_attributes', true);
        foreach($attributes as $key => $attribute ){
            $name = 'attribute_' . $key;
            if( array_key_exists($name, $variations)){
                $options = $attribute['value'];
                $options = explode(' | ', $options);
                $value = $variations[$name];
                if(!in_array($value, $options)){
                    $options[] = $value;
                }
                $options_str = '';
                foreach($options as $index => $option){
                    if($index !== 0){
                        $options_str .= ' | ';
                    }
                    $options_str .= trim($option);
                }
                $attributes[$key]['value'] = $options_str;
            }
        }
        update_post_meta( $product_id ,'_product_attributes', $attributes );

        //set attributes
        $variation->set_attributes($variations);

        $variation->set_weight('');

        $variation->set_price($product_data['meta']['_price']);
        $variation->set_regular_price($product_data['meta']['_price']);

        $thumbnail_id = (int) $product_data['meta']['image_id'];
        if(isset($thumbnail_id) && $thumbnail_id !== 0) {
            update_post_meta( $variation_id, '_thumbnail_id', $thumbnail_id );
        }

        $variation->save();

        return $variation_id;
    }
}