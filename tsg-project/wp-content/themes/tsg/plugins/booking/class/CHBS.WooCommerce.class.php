<?php

/******************************************************************************/
/******************************************************************************/

class CHBSWooCommerce
{
	/**************************************************************************/
	
    function __construct()
    {
        
    }
    
    /**************************************************************************/
    
    function isEnable($meta)
    {
        return((class_exists('WooCommerce')) && ($meta['woocommerce_enable']) && (!$meta['price_hide']));
    }
    
    /**************************************************************************/
    
    function isPayment($paymentId,$dictionary=null)
    {
        if(is_null($dictionary)) $dictionary=$this->getPaymentDictionary();

        foreach($dictionary as $value)
        {
            if($value->{'id'}==$paymentId) return(true);
        }
        
        return(false);
    }
    
    /**************************************************************************/
    
    function getPaymentDictionary()
    {
        $dictionary=WC()->payment_gateways->payment_gateways();

        foreach($dictionary as $index=>$value)
        {
            if(!(isset($value->enabled) && ($value->enabled==='yes')))
            {
                unset($dictionary[$index]);
            }
        }
        
        return($dictionary);
    }
    
    /**************************************************************************/
    
    function getPaymentName($paymentId,$dictionary=null)
    {
        if(is_null($dictionary)) $dictionary=$this->getPaymentDictionary();
        
        foreach($dictionary as $value)
        {
            if($value->{'id'}==$paymentId) return($value->{'title'});
        }        
        
        return(null);
    }
    
    /**************************************************************************/
    
    function sendBooking($bookingId,$bookingForm,$data)
    {
        global $woocommerce;
        $Bookings=new CHBSBooking();
        if (($booking = $Bookings->getBooking($bookingId)) === false) return (false);
        $billing = $Bookings->createBilling($bookingId, $data);
        $passenger_adult = $data['passenger_adult_service_type_'.$data['service_type_id']];
        $passenger_children = $data['passenger_children_service_type_'.$data['service_type_id']];
        $source_product_id = $bookingForm['meta']['booking_source_product'];
        foreach ($billing['detail'] as $detail){
            $product = $this->prepareProduct
            (
                array
                (
                    'post' => array
                    (
                        'post_title' => $detail['name']
                    ),
                    'meta' => array
                    (
                        'related_product_id' => $bookingId,
                        'image_id' => $detail['image_id'],
                        'chbs_price_gross' => $detail['value_gross'],
                        'chbs_tax_value' => $detail['tax_value'],
                        '_regular_price' => $detail['value_net'],
                        '_sale_price' => $detail['value_net'],
                        '_price' => $detail['value_net']
                    )
                )
            );
            $detail['contact_information'] = $data['form_element_field_contact_information'];
            $detail['passenger_adult'] = $passenger_adult;
            $detail['passenger_children'] = $passenger_children;
            $detail['related_product_id'] = $bookingId;

            $AirportTransferProduct = new AirportTransferProduct();
            $item_id = $AirportTransferProduct->get_product_variation($source_product_id, $product, $detail);
            $woocommerce->cart->add_to_cart( $source_product_id, 1, $item_id, array(), $detail);

        }
        return(null);
    }
    
    /**************************************************************************/
    
    function prepareProduct($data)
    {
 		$argument=array
        (
			'post'=>array
            (
				'post_title'													=>  '',
				'post_content'													=>  '',
				'post_status'													=>  'publish',
				'post_type'														=>  'product',
			),
			'meta'=>array
            (
                'related_product_id'                                            =>  0,
				'chbs_price_gross'												=>  0,
				'chbs_tax_value'												=>  0,
				'_visibility'													=>  'visible',
				'_stock_status'													=>  'instock',
				'_downloadable'													=>  'no',
				'_virtual'														=>  'yes',
				'_regular_price'												=>  0,
				'_sale_price'													=>  0,
				'_purchase_note'												=>  '',
				'_featured'														=>  'no',
				'_weight'														=>  '',
				'_length'														=>  '',
				'_width'														=>  '',
				'_height'														=>  '',
				'_sku'															=>  '',
				'_product_attributes'											=>  array(),
				'_sale_price_dates_from'										=>  '',
				'_sale_price_dates_to'											=>  '',
				'_price'														=>  0,
				'_sold_individually'											=>  '',
				'_manage_stock'													=>  'no',
				'_backorders'													=>  'no',
				'_stock'														=>  '',
                'total_sales'													=>  '0',
			),
		);
        
        if(isset($data['post']))
        {
            foreach($data['post'] as $index=>$value)
                $argument['post'][$index]=$value;
        }
        
        if(isset($data['meta']))
        {
            foreach($data['meta'] as $index=>$value)
                $argument['meta'][$index]=$value;
        }        
        
		return($argument);       
    }
    
    /**************************************************************************/
    
	function createProduct($product_data, $data) {
        global $woocommerce;

        $args = array(
            'type'               => '', // Simple product by default
            'name'               => $product_data['post']['post_title'],
            'description'        => __("", "woocommerce"),
            'short_description'  => __("", "woocommerce"),
            'sku'                => $data['type'],
            'regular_price'      => $product_data['meta']['_price'],
            'image_id'           => $product_data['meta']['image_id'],
            'reviews_allowed'    => true,
            'attributes'         => array(),
            'category_ids'       => array( 204 ),
        );

        $product = $this->wc_get_product_object_type( $args['type'] );

        if( ! $product ) {
            return false;
        }

        $product->set_name( $args['name'] );
        if( isset( $args['slug'] ) ) {
            $product->set_slug($args['slug']);
        }

        $product->set_description( $args['description'] );
        $product->set_short_description( $args['short_description'] );

        $product->set_status( isset($args['status']) ? $args['status'] : 'publish' );

        $product->set_catalog_visibility( isset($args['visibility']) ? $args['visibility'] : 'visible' );

        $product->set_featured(  isset($args['featured']) ? $args['featured'] : false );

        $product->set_virtual( isset($args['virtual']) ? $args['virtual'] : false );

        // Prices
        $product->set_regular_price( $args['regular_price'] );
        $product->set_sale_price( isset( $args['sale_price'] ) ? $args['sale_price'] : '' );
        $product->set_price( isset( $args['sale_price'] ) ? $args['sale_price'] :  $args['regular_price'] );
        if( isset( $args['sale_price'] ) ){
            $product->set_date_on_sale_from( isset( $args['sale_from'] ) ? $args['sale_from'] : '' );
            $product->set_date_on_sale_to( isset( $args['sale_to'] ) ? $args['sale_to'] : '' );
        }

        // Downloadable (boolean)
        $product->set_downloadable(  isset($args['downloadable']) ? $args['downloadable'] : false );
        if( isset($args['downloadable']) && $args['downloadable'] ) {
            $product->set_downloads(  isset($args['downloads']) ? $args['downloads'] : array() );
            $product->set_download_limit(  isset($args['download_limit']) ? $args['download_limit'] : '-1' );
            $product->set_download_expiry(  isset($args['download_expiry']) ? $args['download_expiry'] : '-1' );
        }

        // Taxes
        if ( get_option( 'woocommerce_calc_taxes' ) === 'yes' ) {
            $product->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
            $product->set_tax_class(  isset($args['tax_class']) ? $args['tax_class'] : '' );
        }

        // SKU and Stock (Not a virtual product)
        if( isset($args['virtual']) && ! $args['virtual'] ) {
            $product->set_sku( isset( $args['sku'] ) ? $args['sku'] : '' );
            $product->set_manage_stock( isset( $args['manage_stock'] ) ? $args['manage_stock'] : false );
            $product->set_stock_status( isset( $args['stock_status'] ) ? $args['stock_status'] : 'instock' );
            if( isset( $args['manage_stock'] ) && $args['manage_stock'] ) {
                $product->set_stock_status( $args['stock_qty'] );
                $product->set_backorders( isset( $args['backorders'] ) ? $args['backorders'] : 'no' ); // 'yes', 'no' or 'notify'
            }
        }

        // Sold Individually
        $product->set_sold_individually( isset( $args['sold_individually'] ) ? $args['sold_individually'] : false );

        // Weight, dimensions and shipping class
        $product->set_weight( isset( $args['weight'] ) ? $args['weight'] : '' );
        $product->set_length( isset( $args['length'] ) ? $args['length'] : '' );
        $product->set_width( isset(  $args['width'] ) ? $args['width']  : '' );
        $product->set_height( isset( $args['height'] ) ? $args['height'] : '' );
        if( isset( $args['shipping_class_id'] ) ) {
            $product->set_shipping_class_id($args['shipping_class_id']);
        }

        // Upsell and Cross sell (IDs)
        $product->set_upsell_ids( isset( $args['upsells'] ) ? $args['upsells'] : '' );
        $product->set_cross_sell_ids( isset( $args['cross_sells'] ) ? $args['upsells'] : '' );

        // Attributes et default attributes
        if( isset( $args['attributes'] ) ) {
            $product->set_attributes($this->wc_prepare_product_attributes($args['attributes']));
        }
        if( isset( $args['default_attributes'] ) ) {
            $product->set_default_attributes($args['default_attributes']); // Needs a special formatting
        }

        // Reviews, purchase note and menu order
        $product->set_reviews_allowed( isset( $args['reviews'] ) ? $args['reviews'] : false );
        $product->set_purchase_note( isset( $args['note'] ) ? $args['note'] : '' );
        if( isset( $args['menu_order'] ) ) {
            $product->set_menu_order($args['menu_order']);
        }

        // Product categories and Tags
        if( isset( $args['category_ids'] ) ) {
            $product->set_category_ids($args['category_ids']);
        }
        if( isset( $args['tag_ids'] ) ) {
            $product->set_tag_ids($args['tag_ids']);
        }


        // Images and Gallery
        $product->set_image_id( isset( $args['image_id'] ) ? $args['image_id'] : "" );
        $product->set_gallery_image_ids( isset( $args['gallery_ids'] ) ? $args['gallery_ids'] : array() );

        ## --- SAVE PRODUCT --- ##
        $product_id = $product->save();

        return $product_id;
	}

    function wc_prepare_product_attributes( $attributes ){
        global $woocommerce;

        $data = array();
        $position = 0;

        foreach( $attributes as $taxonomy => $values ){
            if( ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }

            $attribute = new WC_Product_Attribute();

            $term_ids = array();

            foreach( $values['term_names'] as $term_name ){
                if( term_exists( $term_name, $taxonomy ) ) {
                    $term_ids[] = get_term_by('name', $term_name, $taxonomy)->term_id;
                }else {
                    continue;
                }
            }

            $taxonomy_id = wc_attribute_taxonomy_id_by_name( $taxonomy ); // Get taxonomy ID

            $attribute->set_id( $taxonomy_id );
            $attribute->set_name( $taxonomy );
            $attribute->set_options( $term_ids );
            $attribute->set_position( $position );
            $attribute->set_visible( $values['is_visible'] );
            $attribute->set_variation( $values['for_variation'] );

            $data[$taxonomy] = $attribute; // Set in an array

            $position++; // Increase position
        }
        return $data;
    }

	function wc_get_product_object_type($type){
        if( isset($type) &&$type === 'variable' ){
            $product = new WC_Product_Variable();
        } elseif( isset($type) && $type === 'grouped' ){
            $product = new WC_Product_Grouped();
        } elseif( isset($type) && $type === 'external' ){
            $product = new WC_Product_External();
        } else {
            $product = new WC_Product_Chauffeur();
        }

        if( ! is_a( $product, 'WC_Product' ) ) {
            return false;
        }else {
            return $product;
        }
    }

    /**************************************************************************/
    
    function locateTemplate($template,$templateName,$templatePath) 
    {
        global $woocommerce;
       
        $templateTemp=$template;
        if(!$templatePath) $templatePath=$woocommerce->template_url;
 
        $pluginPath=PLUGIN_CHBS_PATH.'woocommerce/';
     
        $template=locate_template(array($templatePath.$templateName,$templateName));
 
        if((!$template) && (file_exists($pluginPath.$templateName)))
            $template=$pluginPath.$templateName;
 
        if(!$template) $template=$templateTemp;
   
        return ($template);
    }
    
    /**************************************************************************/
    
    function getUserData()
    {
        $userData=array();
        $userCurrent=wp_get_current_user();
        
        $Country=new WC_Countries();
        $Customer=new WC_Customer($userCurrent->ID);
        
        $billingAddress=$Customer->get_billing();
        
        $userData['client_contact_detail_first_name']=$userCurrent->user_firstname;
        $userData['client_contact_detail_last_name']=$userCurrent->user_lastname;
        $userData['client_contact_detail_email_address']=$userCurrent->user_email;
        $userData['client_contact_detail_phone_number']=$billingAddress['phone'];
        
        $userData['client_billing_detail_enable']=1;
        $userData['client_billing_detail_company_name']=$billingAddress['company'];
        $userData['client_billing_detail_tax_number']=null;
        $userData['client_billing_detail_street_name']=$billingAddress['address_1'];
        $userData['client_billing_detail_street_number']=$billingAddress['address_2'];
        $userData['client_billing_detail_city']=$billingAddress['city'];
        $userData['client_billing_detail_state']=null;
        $userData['client_billing_detail_postal_code']=$billingAddress['postcode'];
        $userData['client_billing_detail_country_code']=$billingAddress['country'];
        
        $state=$billingAddress['state'];
        $country=$billingAddress['country'];
        
        $countryState=$Country->get_states();
        
        if((isset($countryState[$country])) && (isset($countryState[$country][$state])))
            $userData['client_billing_detail_state']=$countryState[$country][$state];
        
        return($userData);
    }
    
    /**************************************************************************/
    
    function getPaymentURLAddress($bookingId)
    {
        $order=wc_get_order($bookingId);
        
        if($order!==false)
            return($order->get_checkout_payment_url());
        
        return(null);
    }
    
    /**************************************************************************/
    
    function addAction()
    {
//        add_action('woocommerce_order_status_changed',array($this,'changeStaus'));
//        add_action('woocommerce_email_customer_details',array($this,'createOrderEmailMessageBody'));
//
//
//        add_action('woocommerce_order_status_changed', array($this, 'woocommerce_order_status_changed'),10, 4);
    }

    function woocommerce_order_status_changed($id, $status_transition_from, $status_transition_to, $that){
        $order = wc_get_order( $id);
        $CHBSBooking = new CHBSBooking();
        foreach( $order->get_items() as $key=>$item ){
            $booking_id = wc_get_order_item_meta($key, 'Transfer Booking ID', true);
            if(($booking = $CHBSBooking->getBooking($booking_id)) !== false){
                $status = 'wc-'.$status_transition_to;
                CHBSPostMeta::updatePostMeta($booking['post']->ID,'booking_status_id',$status);
            }
        }
    }

    /**************************************************************************/

    function changeStaus($order_id)
    {
        $order=new WC_Order($order_id);

        $status=array
        (
            'pending'                                                           =>  1,
            'processing'                                                        =>  2,
            'on-hold'                                                           =>  1,
            'completed'                                                         =>  4,
            'cancelled'                                                         =>  3,
            'refunded'                                                          =>  4,
            'failed'                                                            =>  4
        );
        
        $meta=CHBSPostMeta::getPostMeta($order_id);
        if((array_key_exists('booking_id',$meta)) && ($meta['booking_id']>0))
        {
            if(array_key_exists($order->get_status(),$status))
            {
                CHBSPostMeta::updatePostMeta($meta['booking_id'],'booking_status_id',$status[$order->get_status()]);
            }
        }
    }
    
    /**************************************************************************/
    
    function createOrderEmailMessageBody($order,$sent_to_admin=false)
    {
        if(!($order instanceof WC_Order)) return(''); 
        
//        $Email=new CHBSEmail();
        $Booking=new CHBSBooking();
        
        $meta=CHBSPostMeta::getPostMeta($order->get_id());
        
        $bookingId=(int)$meta['booking_id'];
        
        if($bookingId<=0) return;
        
        if(($booking=$Booking->getBooking($bookingId))===false) return;
        
        $data=array();
        
//        $data['style']=$Email->getEmailStyle();
        
        $data['booking']=$booking;
        $data['booking']['billing']=$Booking->createBilling($bookingId);
        $data['booking']['booking_title']=$booking['post']->post_title;
        
        $data['document_header_exclude']=1;
                
        if(!$sent_to_admin)
            unset($data['booking']['booking_form_name']);
        
        $Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'email_booking.php');
        $body=$Template->output();
        
        echo $body;
    }
    
    /**************************************************************************/


    /**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/