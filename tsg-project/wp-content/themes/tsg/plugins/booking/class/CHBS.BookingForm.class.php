<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingForm
{
	/**************************************************************************/
	
    function __construct()
    {
		$this->fieldMandatory=array
		(
			'client_contact_detail_phone_number'								=>	array
			(
				'label'															=>	__('Phone number','chauffeur-booking-system'),
				'mandatory'														=>	0
			),
			'client_billing_detail_company_name'								=>	array
			(
				'label'															=>	__('Company registered name','chauffeur-booking-system'),
				'mandatory'														=>	0
			),
			'client_billing_detail_tax_number'									=>	array
			(
				'label'															=>	__('Tax number','chauffeur-booking-system'),
				'mandatory'														=>	0
			),
			'client_billing_detail_street_name'									=>	array
			(
				'label'															=>	__('Street name','chauffeur-booking-system'),
				'mandatory'														=>	1
			),
			'client_billing_detail_street_number'								=>	array
			(
				'label'															=>	__('Street number','chauffeur-booking-system'),
				'mandatory'														=>	0
			),
			'client_billing_detail_city'										=>	array
			(
				'label'															=>	__('City','chauffeur-booking-system'),
				'mandatory'														=>	1
			),
			'client_billing_detail_state'										=>	array
			(
				'label'															=>	__('State','chauffeur-booking-system'),
				'mandatory'														=>	0
			),
			'client_billing_detail_postal_code'									=>	array
			(
				'label'															=>	__('Postal code','chauffeur-booking-system'),
				'mandatory'														=>	1
			),
			'client_billing_detail_country_code'								=>	array
			(
				'label'															=>	__('Country','chauffeur-booking-system'),
				'mandatory'														=>	1
			)
		);
		
        $this->vehicleSortingType=array
        (
            1                                                                   =>  array(__('Price ascending','chauffeur-booking-system')),
            2                                                                   =>  array(__('Price descending','chauffeur-booking-system')),
            3                                                                   =>  array(__('Vehicle number ascending','chauffeur-booking-system')),
            4                                                                   =>  array(__('Vehicle number descending','chauffeur-booking-system')),
        );
    }
        
    /**************************************************************************/
    
    public function init()
    {
        $this->registerCPT();
    }
    
	/**************************************************************************/

    public static function getCPTName()
    {
        return(PLUGIN_CHBS_CONTEXT.'_booking_form');
    }
    
    /**************************************************************************/
    
    private function registerCPT()
    {
        $BookingDriver=new CHBSBookingDriver();
        
		register_post_type
		(
			self::getCPTName(),
			array
			(
				'labels'														=>	array
				(
					'name'														=>	__('Booking Forms','chauffeur-booking-system'),
					'singular_name'												=>	__('Booking Form','chauffeur-booking-system'),
					'add_new'													=>	__('Add New','chauffeur-booking-system'),
					'add_new_item'												=>	__('Add New Booking Form','chauffeur-booking-system'),
					'edit_item'													=>	__('Edit Booking Form','chauffeur-booking-system'),
					'new_item'													=>	__('New Booking Form','chauffeur-booking-system'),
					'all_items'													=>	__('Booking Forms','chauffeur-booking-system'),
					'view_item'													=>	__('View Booking Form','chauffeur-booking-system'),
					'search_items'												=>	__('Search Booking Forms','chauffeur-booking-system'),
					'not_found'													=>	__('No Booking Forms Found','chauffeur-booking-system'),
					'not_found_in_trash'										=>	__('No Booking Forms Found in Trash','chauffeur-booking-system'), 
					'parent_item_colon'											=>	'',
                    'menu_name'													=>	__('Chauffeur Booking System','chauffeur-booking-system')
				),	
				'public'														=>	false,
                'menu_icon'														=>	'dashicons-calendar-alt',
				'show_ui'														=>	true, 
//				'show_in_menu'													=>	'edit.php?post_type='.CHBSBookingForm::getCPTName(),
				'capability_type'												=>	'post',
				'menu_position'													=>	100,
                'map_meta_cap'													=>	true,
				'hierarchical'													=>	false,  
				'rewrite'														=>	false,  
				'supports'														=>	array('title','page-attributes','thumbnail')  
			)
		);
        
        add_action('save_post',array($this,'savePost'));
        add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
        add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_booking_form',array($this,'adminCreateMetaBoxClass'));
        
        add_shortcode(PLUGIN_CHBS_CONTEXT.'_booking_form',array($this,'createBookingForm'));
        add_shortcode(PLUGIN_CHBS_CONTEXT.'_booking_driver_confirmation',array($BookingDriver,'createConfirmationForm'));
        
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
    }
    
    /**************************************************************************/
    
    static function getShortcodeName()
    {
        return(PLUGIN_CHBS_CONTEXT.'_booking_form');
    }
    
    /**************************************************************************/
    
    function addMetaBox()
    {
        add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_form',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
    }
    
    /**************************************************************************/
    
    function addMetaBoxMain()
    {
        global $post;
               
        $Route=new CHBSRoute();
        $Driver=new CHBSDriver();
        $Payment=new CHBSPayment();
        $Country=new CHBSCountry();
        $Vehicle=new CHBSVehicle();
        $Location=new CHBSLocation();
        $Currency=new CHBSCurrency();
        $GoogleMap=new CHBSGoogleMap();
        $ServiceType=new CHBSServiceType();
		$PaymentStripe=new CHBSPaymentStripe();
        $BookingStatus=new CHBSBookingStatus();
        $BookingGratuity=new CHBSBookingGratuity();
        $BookingFormStyle=new CHBSBookingFormStyle();
        $BookingFormElement=new CHBSBookingFormElement();
        
		$data=array();
        
        $data['meta']=CHBSPostMeta::getPostMeta($post);
        
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_form');
        
        $data['dictionary']['route']=$Route->getDictionary();
        $data['dictionary']['payment']=$Payment->getPayment();
        
        $data['dictionary']['color']=$BookingFormStyle->getColor();
        
        $data['dictionary']['vehicle']=$Vehicle->getDictionary();
        $data['dictionary']['vehicle_category']=$Vehicle->getCategory();
        
        $data['dictionary']['service_type']=$ServiceType->getServiceType();
  
        $data['dictionary']['country']=$Country->getCountry();
        
        $data['dictionary']['booking_status']=$BookingStatus->getBookingStatus();
        
        $data['dictionary']['google_map']['position']=$GoogleMap->getPosition();
        $data['dictionary']['google_map']['route_avoid']=$GoogleMap->getRouteAvoid();
        $data['dictionary']['google_map']['map_type_control_id']=$GoogleMap->getMapTypeControlId();
        $data['dictionary']['google_map']['map_type_control_style']=$GoogleMap->getMapTypeControlStyle();
        
        $data['dictionary']['form_element_panel']=$BookingFormElement->getPanel($data['meta']);
        
        $data['dictionary']['driver']=$Driver->getDictionary();
        
        $data['dictionary']['location']=$Location->getDictionary();
        
        $data['dictionary']['currency']=$Currency->getCurrency();
        
        $data['dictionary']['vehicle_sorting_type']=$this->vehicleSortingType;
        
        $data['dictionary']['gratuity_type']=$BookingGratuity->getType();
        
		$data['dictionary']['field_mandatory']=$this->fieldMandatory;
		
		$data['dictionary']['payment_stripe_method']=$PaymentStripe->getPaymentMethod();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_booking_form.php');
		echo $Template->output();	        
    }
    
    /**************************************************************************/
    
    function adminCreateMetaBoxClass($class) 
    {
        array_push($class,'to-postbox-1');
        return($class);
    }
    
    /**************************************************************************/
    
    function savePost($postId)
    {      
        if(!$_POST) return(false);
        
        if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_booking_form_noncename','savePost')===false) return(false);
        
		$meta=array();
        
        $Date=new CHBSDate();
        $Route=new CHBSRoute();
        $Length=new CHBSLength();
        $Driver=new CHBSDriver();
        $Vehicle=new CHBSVehicle();
        $Payment=new CHBSPayment();
        $Country=new CHBSCountry();
        $Currency=new CHBSCurrency();
        $Location=new CHBSLocation();
        $Validation=new CHBSValidation();
        $ServiceType=new CHBSServiceType();
        $PaymentStripe=new CHBSPaymentStripe();
		$BookingStatus=new CHBSBookingStatus();
        $BookingGratuity=new CHBSBookingGratuity();
        $BookingFormStyle=new CHBSBookingFormStyle();
        
		$this->setPostMetaDefault($meta);
        
        /***/
        /***/
        
        $meta['service_type_id']=(array)CHBSHelper::getPostValue('service_type_id');
        foreach($meta['service_type_id'] as $index=>$value)
        {
            if(!$ServiceType->isServiceType($value))
                unset($meta['service_type_id'][$index]);
        }
        
        if(!count($meta['service_type_id']))
            $meta['service_type_id']=array(1,2,3);
        
        $meta['service_type_id_default']=(int)CHBSHelper::getPostValue('service_type_id_default');
        
        /***/
        
        $field=array('transfer_type_enable_1','transfer_type_enable_3');
        
        foreach($field as $fieldIndex)
        {
            $meta[$fieldIndex]=(array)CHBSHelper::getPostValue($fieldIndex);
            foreach($meta[$fieldIndex] as $index=>$value)
            {
                if(!$ServiceType->isServiceType($value))
                    unset($meta[$fieldIndex][$index]);
            }        
            if(!count($meta[$fieldIndex]))
                $meta[$fieldIndex]=array(); 
        }
        
        /***/
        
        $meta['vehicle_category_id']=(array)CHBSHelper::getPostValue('vehicle_category_id');
        if(in_array(-1,$meta['vehicle_category_id']))
        {
            $meta['vehicle_category_id']=array(-1);
        }
        else
        {
            $category=$Vehicle->getCategory();
            foreach($meta['vehicle_category_id'] as $index=>$value)
            {
                if(!isset($category[$value]))
                    unset($category[$value]);                
            }
        }
        
        if(!count($meta['vehicle_category_id']))
            $meta['vehicle_category_id']=array(-1);
            
        $meta['vehicle_id_default']=(int)CHBSHelper::getPostValue('vehicle_id_default');
        
        /***/
        
        $meta['vehicle_filter_enable']=(array)CHBSHelper::getPostValue('vehicle_filter_enable');
        foreach($meta['vehicle_filter_enable'] as $index=>$value)
        {
            if(!in_array($value,array(1,2,3,4)))
                unset($meta['vehicle_filter_enable'][$index]);
        }
        
        /***/  
        
        $meta['vehicle_sorting_type']=CHBSHelper::getPostValue('vehicle_sorting_type');
        if(!array_key_exists($meta['vehicle_sorting_type'],$this->vehicleSortingType))
            $meta['vehicle_sorting_type']=1;
        
        /***/    
            
        $meta['vehicle_pagination_vehicle_per_page']=(int)CHBSHelper::getPostValue('vehicle_pagination_vehicle_per_page');
        if(!$Validation->isNumber($meta['vehicle_pagination_vehicle_per_page'],1,99))
            $meta['vehicle_pagination_vehicle_per_page']=0;
 
        $meta['vehicle_limit']=(int)CHBSHelper::getPostValue('vehicle_limit');
        if(!$Validation->isNumber($meta['vehicle_limit'],1,99))
            $meta['vehicle_limit']=0;
        
        $meta['vehicle_bid_enable']=(int)CHBSHelper::getPostValue('vehicle_bid_enable');
        if(!$Validation->isBool($meta['vehicle_bid_enable']))
            $meta['vehicle_bid_enable']=0;		
		
        $meta['vehicle_bid_max_percentage_discount']=(int)CHBSHelper::getPostValue('vehicle_bid_max_percentage_discount');
        if(!$Validation->isFloat($meta['vehicle_bid_max_percentage_discount'],0,99.99))
            $meta['vehicle_bid_max_percentage_discount']=0;		
		
		/***/
		
		$meta['field_mandatory']=(array)CHBSHelper::getPostValue('field_mandatory');
		foreach($meta['field_mandatory'] as $index=>$value)
		{
			if(!array_key_exists($value,$this->fieldMandatory))
				unset($meta['field_mandatory'][$index]);
		}
		
        /***/
                
        $meta['route_id']=(array)CHBSHelper::getPostValue('route_id');
        if(in_array(-1,$meta['route_id']))
        {
            $meta['route_id']=array(-1);
        }
        else
        {
            $directory=$Route->getDictionary();
            foreach($meta['route_id'] as $index=>$value)
            {
                if(!isset($directory[$value]))
                    unset($directory[$value]);                
            }
        }
        
        if(!count($meta['route_id']))
            $meta['route_id']=array(-1);        
        
        $meta['route_list_item_empty_enable']=CHBSHelper::getPostValue('route_list_item_empty_enable');
        if(!$Validation->isBool($meta['route_list_item_empty_enable']))
            $meta['route_list_item_empty_enable']=0;        
        
        $meta['route_list_item_empty_text']=CHBSHelper::getPostValue('route_list_item_empty_text');
        
        $meta['currency']=(array)CHBSHelper::getPostValue('currency');
        if(in_array(-1,$meta['currency']))
        {
            $meta['currency']=array(-1);
        }
        else
        {
            foreach($Currency->getCurrency() as $index=>$value)
            {
                if(!$Currency->isCurrency($index))
                {
                    unset($meta['currency'][$index]);
                }
            }
        }
        
        if(!count($meta['currency']))
            $meta['currency']=array(-1);         

        /***/

        $meta['extra_time_step']=CHBSHelper::getPostValue('extra_time_step');
        $meta['extra_time_enable']=CHBSHelper::getPostValue('extra_time_enable');
        $meta['extra_time_range_min']=CHBSHelper::getPostValue('extra_time_range_min');
        $meta['extra_time_range_max']=CHBSHelper::getPostValue('extra_time_range_max');
        $meta['extra_time_unit']=CHBSHelper::getPostValue('extra_time_unit');
        
        if(!$Validation->isBool($meta['extra_time_enable'])) {
            $meta['extra_time_enable'] = 1;
        }
        if(!$Validation->isNumber($meta['extra_time_range_min'],0,9999)) {
            $meta['extra_time_range_min'] = 0;
        }
        if(!$Validation->isNumber($meta['extra_time_range_max'],1,9999)) {
            $meta['extra_time_range_max'] = 24;
        }
        if(!$Validation->isNumber($meta['extra_time_step'],1,9999)) {
            $meta['extra_time_step'] = 1;
        }
        if(!in_array($meta['extra_time_unit'],array(1,2))) {
            $meta['extra_time_unit'] = 2;
        }
        
        if(($meta['extra_time_range_min']>=$meta['extra_time_range_max']) || (!count(array_intersect(array(1,3),$meta['service_type_id']))))
        {
            $meta['extra_time_step']=1;
            $meta['extra_time_range_min']=0;
            $meta['extra_time_range_max']=24;
        }

        /***/
        
        $meta['duration_min']=CHBSHelper::getPostValue('duration_min');
        $meta['duration_max']=CHBSHelper::getPostValue('duration_max');        
        $meta['duration_step']=CHBSHelper::getPostValue('duration_step');     

        if(!$Validation->isNumber($meta['duration_min'],1,9999))
            $meta['duration_min']=1;    
        if(!$Validation->isNumber($meta['duration_max'],1,9999))
            $meta['duration_max']=24;            
        if(!$Validation->isNumber($meta['duration_step'],1,9999))
            $meta['duration_step']=1;       
        
        if(($meta['duration_min']>=$meta['duration_max']) || (!count(array_intersect(array(2),$meta['service_type_id']))))
        {
            $meta['duration_min']=1;
            $meta['duration_max']=24;
            $meta['duration_step']=1; 
        }     

        /***/
        $meta['booking_source_product']=CHBSHelper::getPostValue('booking_source_product');
        if(!$Validation->isNumber($meta['booking_source_product'], 0, 999999)){
            $meta['booking_source_product'] = '';
        }

        /***/
        
        $meta['booking_period_from']=CHBSHelper::getPostValue('booking_period_from');
        if(!$Validation->isNumber($meta['booking_period_from'],0,9999)) {
            $meta['booking_period_from'] = '';
        }
        $meta['booking_period_to']=CHBSHelper::getPostValue('booking_period_to');
        if(!$Validation->isNumber($meta['booking_period_to'],0,9999)) {
            $meta['booking_period_to'] = '';
        }
        $meta['booking_period_type']=CHBSHelper::getPostValue('booking_period_type');
        if(!in_array($meta['booking_period_type'],array(1,2,3))) {
            $meta['booking_period_type'] = 1;
        }
        
        /***/
        
        $meta['booking_vehicle_interval']=CHBSHelper::getPostValue('booking_vehicle_interval');
        if(!$Validation->isNumber($meta['booking_vehicle_interval'],0,9999)) {
            $meta['booking_vehicle_interval'] = 0;
        }
        
        /***/
        
        $meta['price_hide']=CHBSHelper::getPostValue('price_hide');
        if(!$Validation->isBool($meta['price_hide'])) {
            $meta['price_hide'] = 0;
        }
        
        /***/
        
        $meta['order_sum_split']=CHBSHelper::getPostValue('order_sum_split');
        if(!$Validation->isBool($meta['order_sum_split'])) {
            $meta['order_sum_split'] = 0;
        }
        
        $meta['show_net_price_hide_tax']=CHBSHelper::getPostValue('show_net_price_hide_tax');
        if(!$Validation->isBool($meta['show_net_price_hide_tax'])) {
            $meta['show_net_price_hide_tax'] = 0;
        }
        
        /***/

        $meta['gratuity_enable']=CHBSHelper::getPostValue('gratuity_enable');
        if(!$Validation->isBool($meta['gratuity_enable'])) {
            $meta['gratuity_enable'] = 0;
        }

        $meta['gratuity_admin_type']=CHBSHelper::getPostValue('gratuity_admin_type');
        if(!$BookingGratuity->isType($meta['gratuity_admin_type'])) {
            $meta['gratuity_admin_type'] = 1;
        }
            
        $meta['gratuity_admin_value']=CHBSPrice::formatToSave(CHBSHelper::getPostValue('gratuity_admin_value'),false);
        if(!$Validation->isPrice($meta['gratuity_admin_value'])) {
            $meta['gratuity_admin_value'] = CHBSPrice::getDefaultPrice();
        }
		$meta['gratuity_admin_value']=CHBSPrice::formatToSave($meta['gratuity_admin_value'],true);
		
        $meta['gratuity_customer_enable']=CHBSHelper::getPostValue('gratuity_customer_enable');
        if(!$Validation->isBool($meta['gratuity_customer_enable'])) {
            $meta['gratuity_customer_enable'] = 0;
        }
        
        $meta['gratuity_customer_type']=(array)CHBSHelper::getPostValue('gratuity_customer_type');
        foreach($meta['gratuity_customer_type'] as $index=>$value)
        {
            if(!$BookingGratuity->isType($value))
                unset($meta['gratuity_customer_type'][$index]);
        }
  
        /***/
        
        $meta['vehicle_price_round']=CHBSPrice::formatToSave(CHBSHelper::getPostValue('vehicle_price_round'),false);   
        if(!$Validation->isFloat($meta['vehicle_price_round'],0.00,999999.99)) {
            $meta['vehicle_price_round'] = CHBSPrice::getDefaultPrice();
        }
        
        /***/
        
        $meta['booking_summary_hide_fee']=CHBSHelper::getPostValue('booking_summary_hide_fee');
        if(!$Validation->isBool($meta['booking_summary_hide_fee'])) {
            $meta['booking_summary_hide_fee'] = 0;
        }
        
        /***/
        
        $meta['prevent_double_vehicle_booking_enable']=CHBSHelper::getPostValue('prevent_double_vehicle_booking_enable');
        if(!$Validation->isBool($meta['prevent_double_vehicle_booking_enable'])) {
            $meta['prevent_double_vehicle_booking_enable'] = 0;
        }
        
        /***/
        
        $meta['vehicle_in_the_same_booking_passenger_sum_enable']=CHBSHelper::getPostValue('vehicle_in_the_same_booking_passenger_sum_enable');
        if(!$Validation->isBool($meta['vehicle_in_the_same_booking_passenger_sum_enable'])) {
            $meta['vehicle_in_the_same_booking_passenger_sum_enable'] = 0;
        }
        
        /***/        
        
        $meta['step_second_enable']=CHBSHelper::getPostValue('step_second_enable');
        if(!$Validation->isBool($meta['step_second_enable'])) {
            $meta['step_second_enable'] = 1;
        }
        
        /***/        
        
        $meta['thank_you_page_enable']=CHBSHelper::getPostValue('thank_you_page_enable');
        if(!$Validation->isBool($meta['thank_you_page_enable'])) {
            $meta['thank_you_page_enable'] = 1;
        }
        
        $meta['thank_you_page_button_back_to_home_label']=CHBSHelper::getPostValue('thank_you_page_button_back_to_home_label');
        $meta['thank_you_page_button_back_to_home_url_address']=CHBSHelper::getPostValue('thank_you_page_button_back_to_home_url_address');
        
        /***/
        
        $meta['distance_minimum']=CHBSHelper::getPostValue('distance_minimum');        
        if(!$Validation->isNumber($meta['distance_minimum'],0,99999)) {
            $meta['distance_minimum'] = 0;
        }
        if(CHBSOption::getOption('length_unit')==2) {
            $meta['distance_minimum'] = $Length->convertUnit($meta['distance_minimum'], 2, 1);
        }
        
        $meta['duration_minimum']=CHBSHelper::getPostValue('duration_minimum');        
        if(!$Validation->isNumber($meta['duration_minimum'],0,999999999))
            $meta['duration_minimum']=0;     
             
        $meta['order_value_minimum']=CHBSPrice::formatToSave(CHBSHelper::getPostValue('order_value_minimum'),false);        
        if(!$Validation->isPrice($meta['order_value_minimum']))
            $meta['order_value_minimum']=CHBSPrice::getDefaultPrice();   
		
		$meta['order_value_minimum']=CHBSPrice::formatToSave($meta['order_value_minimum'],true);

        /***/
        
        $meta['timepicker_step']=CHBSHelper::getPostValue('timepicker_step');
        if(!$Validation->isNumber($meta['timepicker_step'],1,9999))
            $meta['timepicker_step']=30;           
        
        $meta['timepicker_dropdown_list_enable']=CHBSHelper::getPostValue('timepicker_dropdown_list_enable');
        if(!$Validation->isBool($meta['timepicker_dropdown_list_enable']))
            $meta['timepicker_dropdown_list_enable']=0;          
        
        $meta['form_preloader_enable']=CHBSHelper::getPostValue('form_preloader_enable');
        if(!$Validation->isBool($meta['form_preloader_enable']))
            $meta['form_preloader_enable']=0;           

        $meta['form_preloader_image_src']=CHBSHelper::getPostValue('form_preloader_image_src');
        
        $meta['form_preloader_background_opacity']=CHBSHelper::getPostValue('form_preloader_background_opacity');
        if(!$Validation->isNumber($meta['form_preloader_background_opacity'],0,100))
            $meta['form_preloader_background_opacity']=20;
        
        $meta['form_preloader_background_color']=CHBSHelper::getPostValue('form_preloader_background_color');
        if(!$Validation->isColor($meta['form_preloader_background_color']))
            $meta['form_preloader_background_color']='FFFFFF';
            
        /***/
        
        $meta['billing_detail_state']=CHBSHelper::getPostValue('billing_detail_state');
        if(!in_array($meta['billing_detail_state'],array(1,2,3,4)))
            $meta['billing_detail_state']=1;   
        
        $meta['billing_detail_list_state']=CHBSHelper::getPostValue('billing_detail_list_state');
        
        /***/   
        
        $meta['booking_status_default_id']=CHBSHelper::getPostValue('booking_status_default_id');
        if(!$BookingStatus->isBookingStatus($meta['booking_status_default_id']))
            $meta['booking_status_default_id']=1;

        /***/
        
        $driverDictionary=$Driver->getDictionary();
        $meta['driver_default_id']=CHBSHelper::getPostValue('driver_default_id');
        if(!array_key_exists($meta['driver_default_id'],$driverDictionary))
            $meta['driver_default_id']=-1;

		/***/
		
        $meta['country_default']=CHBSHelper::getPostValue('country_default');
		if(!$Country->isCountry($meta['country_default']))
			$meta['country_default']=-1;
		
        /***/
        
        $meta['geolocation_server_side_enable']=CHBSHelper::getPostValue('geolocation_server_side_enable');
        if(!$Validation->isBool($meta['geolocation_server_side_enable']))
            $meta['geolocation_server_side_enable']=0;        
        
        /***/ 
       
        $meta['summary_sidebar_sticky_enable']=CHBSHelper::getPostValue('summary_sidebar_sticky_enable');
        if(!$Validation->isBool($meta['summary_sidebar_sticky_enable']))
            $meta['summary_sidebar_sticky_enable']=0;
        
        /***/
        
        $meta['scroll_to_booking_extra_after_select_vehicle_enable']=CHBSHelper::getPostValue('scroll_to_booking_extra_after_select_vehicle_enable');
        if(!$Validation->isBool($meta['scroll_to_booking_extra_after_select_vehicle_enable']))
            $meta['scroll_to_booking_extra_after_select_vehicle_enable']=0;        
 
		/***/
		
        $meta['dropoff_location_field_enable']=CHBSHelper::getPostValue('dropoff_location_field_enable');
        if(!$Validation->isBool($meta['dropoff_location_field_enable']))
            $meta['dropoff_location_field_enable']=0;  
		
		/***/
		
        $meta['passenger_number_vehicle_list_enable']=CHBSHelper::getPostValue('passenger_number_vehicle_list_enable');
        if(!$Validation->isBool($meta['passenger_number_vehicle_list_enable']))
            $meta['passenger_number_vehicle_list_enable']=1;  
		
		/***/
		
        $meta['suitcase_number_vehicle_list_enable']=CHBSHelper::getPostValue('suitcase_number_vehicle_list_enable');
        if(!$Validation->isBool($meta['suitcase_number_vehicle_list_enable']))
            $meta['suitcase_number_vehicle_list_enable']=1;  
		
		/***/
		
        $meta['use_my_location_link_enable']=CHBSHelper::getPostValue('use_my_location_link_enable');
        if(!$Validation->isBool($meta['use_my_location_link_enable']))
            $meta['use_my_location_link_enable']=0;  
		
        /***/
        
        $meta['woocommerce_enable']=CHBSHelper::getPostValue('woocommerce_enable');
        if(!$Validation->isBool($meta['woocommerce_enable']))
            $meta['woocommerce_enable']=0;       
      
        /***/
        
        $meta['woocommerce_account_enable_type']=CHBSHelper::getPostValue('woocommerce_account_enable_type');
        if(!in_array($meta['woocommerce_account_enable_type'],array(0,1,2)))
            $meta['woocommerce_account_enable_type']=0;       
        
        /***/
        
        $meta['coupon_enable']=CHBSHelper::getPostValue('coupon_enable');
        if(!$Validation->isBool($meta['coupon_enable']))
            $meta['coupon_enable']=0;    
        
        /***/
        
        $meta['passenger_adult_enable_service_type_1']=CHBSHelper::getPostValue('passenger_adult_enable_service_type_1');
        if(!$Validation->isBool($meta['passenger_adult_enable_service_type_1']))
            $meta['passenger_adult_enable_service_type_1']=0; 
        
        $meta['passenger_children_enable_service_type_1']=CHBSHelper::getPostValue('passenger_children_enable_service_type_1');
        if(!$Validation->isBool($meta['passenger_children_enable_service_type_1']))
            $meta['passenger_children_enable_service_type_1']=0; 
        
        $meta['passenger_adult_enable_service_type_2']=CHBSHelper::getPostValue('passenger_adult_enable_service_type_2');
        if(!$Validation->isBool($meta['passenger_adult_enable_service_type_2']))
            $meta['passenger_adult_enable_service_type_2']=0; 
        
        $meta['passenger_children_enable_service_type_2']=CHBSHelper::getPostValue('passenger_children_enable_service_type_2');
        if(!$Validation->isBool($meta['passenger_children_enable_service_type_2']))
            $meta['passenger_children_enable_service_type_2']=0; 
        
        $meta['passenger_adult_enable_service_type_3']=CHBSHelper::getPostValue('passenger_adult_enable_service_type_3');
        if(!$Validation->isBool($meta['passenger_adult_enable_service_type_3']))
            $meta['passenger_adult_enable_service_type_3']=0; 
        
        $meta['passenger_children_enable_service_type_3']=CHBSHelper::getPostValue('passenger_children_enable_service_type_3');
        if(!$Validation->isBool($meta['passenger_children_enable_service_type_3']))
            $meta['passenger_children_enable_service_type_3']=0;         
        
        $meta['passenger_adult_default_number']=CHBSHelper::getPostValue('passenger_adult_default_number');
        if(!$Validation->isNumber($meta['passenger_adult_default_number'],0,99,true))
            $meta['passenger_adult_default_number']=0;
        $meta['passenger_children_default_number']=CHBSHelper::getPostValue('passenger_children_default_number'); 
        if(!$Validation->isNumber($meta['passenger_children_default_number'],0,99,true))
            $meta['passenger_children_default_number']=0;             
  
        $meta['calculate_price_by_passenger_quantity']=CHBSHelper::getPostValue('calculate_price_by_passenger_quantity');
        if(!$Validation->isBool($meta['calculate_price_by_passenger_quantity'])) {
            $meta['calculate_price_by_passenger_quantity'] = 0;
        }

        $meta['show_price_per_single_passenger']=CHBSHelper::getPostValue('show_price_per_single_passenger');
        if(!$Validation->isBool($meta['show_price_per_single_passenger']))
            $meta['show_price_per_single_passenger']=0;
            
        /***/
        
        $meta['calculation_method_service_type_1']=CHBSHelper::getPostValue('calculation_method_service_type_1');
        if(!in_array($meta['calculation_method_service_type_1'],array(1,2)))
            $meta['calculation_method_service_type_1']=1;
        
        $meta['calculation_method_service_type_3']=CHBSHelper::getPostValue('calculation_method_service_type_3');
        if(!in_array($meta['calculation_method_service_type_3'],array(1,2)))
            $meta['calculation_method_service_type_3']=1;   
        
        /***/
        
        $meta['base_location']=CHBSHelper::getPostValue('base_location');
        $meta['base_location_coordinate_lat']=CHBSHelper::getPostValue('base_location_coordinate_lat');
        $meta['base_location_coordinate_lng']=CHBSHelper::getPostValue('base_location_coordinate_lng');
        
        if($Validation->isEmpty($meta['base_location']))
        {
            $meta['base_location_coordinate_lat']='';
            $meta['base_location_coordinate_lng']='';
        }
        
        $meta['waypoint_enable']=CHBSHelper::getPostValue('waypoint_enable');
        if(!$Validation->isBool($meta['waypoint_enable']))
            $meta['waypoint_enable']=0;          
        
        $locationDictionary=$Location->getDictionary();
        
        $field=array('location_fixed_pickup_service_type_1','location_fixed_dropoff_service_type_1','location_fixed_pickup_service_type_2','location_fixed_dropoff_service_type_2');
        
        foreach($field as $fieldName)
        {
            $meta[$fieldName]=(array)CHBSHelper::getPostValue($fieldName);
            foreach($meta[$fieldName] as $index=>$value)
            {
                if($value==-1)
                {
                    $meta[$fieldName]=array(-1);
                    break;
                }

                if(!array_key_exists($value,$locationDictionary))                        
                    unset($meta[$fieldName][$index]);
            }
        }       
        
        $meta['location_fixed_list_item_empty_enable']=CHBSHelper::getPostValue('location_fixed_list_item_empty_enable');
        if(!$Validation->isBool($meta['location_fixed_list_item_empty_enable']))
            $meta['location_fixed_list_item_empty_enable']=0;           
        
        $meta['location_fixed_list_item_empty_text']=CHBSHelper::getPostValue('location_fixed_list_item_empty_text');
        
        $meta['location_fixed_autocomplete_enable']=CHBSHelper::getPostValue('location_fixed_autocomplete_enable');
        if(!$Validation->isBool($meta['location_fixed_autocomplete_enable']))
            $meta['location_fixed_autocomplete_enable']=0;                
        
        $meta['ride_time_multiplier']=CHBSHelper::getPostValue('ride_time_multiplier');
        if(!$Validation->isFloat($meta['ride_time_multiplier'],0,99.99))
            $meta['ride_time_multiplier']=1.00;  
        else $meta['ride_time_multiplier']=number_format(preg_replace('/,/','.',$meta['ride_time_multiplier']),2,'.','');
            
        $meta['google_autosugestion_address_type']=CHBSHelper::getPostValue('google_autosugestion_address_type');
        if(!in_array($meta['google_autosugestion_address_type'],array(1,2)))
            $meta['google_autosugestion_address_type']=2;         
        
        $meta['icon_field_enable']=CHBSHelper::getPostValue('icon_field_enable');
        if(!$Validation->isBool($meta['icon_field_enable']))
            $meta['icon_field_enable']=0;         
        
        $meta['navigation_top_enable']=CHBSHelper::getPostValue('navigation_top_enable');
        if(!$Validation->isBool($meta['navigation_top_enable']))
            $meta['navigation_top_enable']=0;        
        
        $meta['step_1_right_panel_visibility']=CHBSHelper::getPostValue('step_1_right_panel_visibility');
        if(!$Validation->isBool($meta['step_1_right_panel_visibility']))
            $meta['step_1_right_panel_visibility']=0;   
        
        $meta['vehicle_more_info_default_show']=CHBSHelper::getPostValue('vehicle_more_info_default_show');
        if(!$Validation->isBool($meta['vehicle_more_info_default_show']))
            $meta['vehicle_more_info_default_show']=0;                 
        
        $meta['booking_title']=CHBSHelper::getPostValue('booking_title');

        /***/
        /***/
        
		$businessHour=array();
        $businessHourPost=CHBSHelper::getPostValue('business_hour');
        
		foreach(array_keys($Date->day) as $index)
		{
			$businessHour[$index]=array('start'=>null,'stop'=>null);
			
            $businessHourPost[$index][0]=$Date->formatTimeToStandard($businessHourPost[$index][0]);
            $businessHourPost[$index][1]=$Date->formatTimeToStandard($businessHourPost[$index][1]);
            
            if((isset($businessHourPost[$index][0])) && (isset($businessHourPost[$index][1])))
            {
                if(($Validation->isTime($businessHourPost[$index][0],false)) && ($Validation->isTime($businessHourPost[$index][1],false)))
                {
                    $result=$Date->compareTime($businessHourPost[$index][0],$businessHourPost[$index][1]);

                    if($result==2)
                    {
                        $businessHour[$index]['start']=$businessHourPost[$index][0];
                        $businessHour[$index]['stop']=$businessHourPost[$index][1];
                    }
                }
            }
		}
 
		$meta['business_hour']=$businessHour;
        
        /***/
        
		$dateExclude=array();
        $dateExcludePost=array();
        
        $dateExcludePostStart=CHBSHelper::getPostValue('date_exclude_start');
        $dateExcludePostStop=CHBSHelper::getPostValue('date_exclude_stop');
        
        foreach($dateExcludePostStart as $index=>$value)
        {
            if(isset($dateExcludePostStop[$index]))
                $dateExcludePost[]=array($dateExcludePostStart[$index],$dateExcludePostStop[$index]);
        }
      
		foreach($dateExcludePost as $index=>$value)
		{
            $value[0]=$Date->formatDateToStandard($value[0]);
            $value[1]=$Date->formatDateToStandard($value[1]);
            
			if(!$Validation->isDate($value[0],true)) continue;
			if(!$Validation->isDate($value[1],true)) continue;

			if($Date->compareDate($value[0],$value[1])==1) continue;
			if($Date->compareDate(date_i18n('d-m-Y'),$value[1])==1) continue;
			
			$dateExclude[]=array('start'=>$value[0],'stop'=>$value[1]);
		}
        
		$meta['date_exclude']=$dateExclude;
        
        /***/
        /***/
        
        $meta['payment_mandatory_enable']=CHBSHelper::getPostValue('payment_mandatory_enable');
        if(!$Validation->isBool($meta['payment_mandatory_enable']))
            $meta['payment_mandatory_enable']=0; 
        
        $meta['payment_processing_enable']=CHBSHelper::getPostValue('payment_processing_enable');
        if(!$Validation->isBool($meta['payment_processing_enable']))
            $meta['payment_processing_enable']=1;   
        
        $meta['payment_woocommerce_step_3_enable']=CHBSHelper::getPostValue('payment_woocommerce_step_3_enable');
        if(!$Validation->isBool($meta['payment_woocommerce_step_3_enable']))
            $meta['payment_woocommerce_step_3_enable']=1;              
                
        $meta['payment_deposit_enable']=CHBSHelper::getPostValue('payment_deposit_enable');
        if(!$Validation->isBool($meta['payment_deposit_enable']))
            $meta['payment_deposit_enable']=0;         
        
        $meta['payment_deposit_value']=CHBSHelper::getPostValue('payment_deposit_value');
        if(!$Validation->isNumber($meta['payment_deposit_value'],0,100))
            $meta['payment_deposit_value']=30;             
        
        if($meta['payment_deposit_enable']==0)
            $meta['payment_deposit_value']=30;
        
        /***/
        
        $meta['payment_id']=(array)CHBSHelper::getPostValue('payment_id');
        foreach($meta['payment_id'] as $index=>$value)
        {
            if(!$Payment->isPayment($value))
                unset($meta['payment_id'][$value]);
        }
        
		$meta['payment_default_id']=(int)CHBSHelper::getPostValue('payment_default_id');
		if(!$Payment->isPayment($meta['payment_default_id']))
			$meta['payment_default_id']=-1;
		
		/**/
		
		$meta['payment_cash_logo_src']=CHBSHelper::getPostValue('payment_cash_logo_src');
		$meta['payment_cash_info']=CHBSHelper::getPostValue('payment_cash_info');
		
		/**/
		
        $meta['payment_stripe_api_key_secret']=CHBSHelper::getPostValue('payment_stripe_api_key_secret');
        $meta['payment_stripe_api_key_publishable']=CHBSHelper::getPostValue('payment_stripe_api_key_publishable');		
		
		$meta['payment_stripe_method']=CHBSHelper::getPostValue('payment_stripe_method');
				
		if(is_array($meta['payment_stripe_method']))
		{
			foreach($meta['payment_stripe_method'] as $index=>$value)
			{
				if(!$PaymentStripe->isPaymentMethod($value))
					unset($meta['payment_stripe_method'][$index]);
			}
		}
		
		if((!is_array($meta['payment_stripe_method'])) || (!count($meta['payment_stripe_method'])))
			$meta['payment_stripe_method']=array('card');
				
		$meta['payment_stripe_product_id']=CHBSHelper::getPostValue('payment_stripe_product_id');
		
		$meta['payment_stripe_redirect_duration']=CHBSHelper::getPostValue('payment_stripe_redirect_duration');		
		if(!$Validation->isNumber($meta['payment_stripe_redirect_duration'],-1,99)) {
            $meta['payment_stripe_redirect_duration'] = 5;
        }
		
		$meta['payment_stripe_success_url_address']=CHBSHelper::getPostValue('payment_stripe_success_url_address');
		$meta['payment_stripe_cancel_url_address']=CHBSHelper::getPostValue('payment_stripe_cancel_url_address');
		$meta['payment_stripe_logo_src']=CHBSHelper::getPostValue('payment_stripe_logo_src');
		$meta['payment_stripe_info']=CHBSHelper::getPostValue('payment_stripe_info');
		
		/**/
		
//		$meta['payment_paypal_email_address']=CHBSHelper::getPostValue('payment_paypal_email_address');
		
		$meta['payment_paypal_sandbox_mode_enable']=CHBSHelper::getPostValue('payment_paypal_sandbox_mode_enable');
		if($Validation->isBool($meta['payment_paypal_sandbox_mode_enable'])) {
            $meta['payment_paypal_sandbox_mode_enable'] = 0;
        }
		
		$meta['payment_paypal_redirect_duration']=CHBSHelper::getPostValue('payment_paypal_redirect_duration');
		if(!$Validation->isNumber($meta['payment_paypal_redirect_duration'],-1,99)) {
            $meta['payment_paypal_redirect_duration'] = 5;
        }

		$meta['payment_paypal_logo_src']=CHBSHelper::getPostValue('payment_paypal_logo_src');
		$meta['payment_paypal_info']=CHBSHelper::getPostValue('payment_paypal_info');
		
		/**/
		
		$meta['payment_wire_transfer_logo_src']=CHBSHelper::getPostValue('payment_wire_transfer_logo_src');
		$meta['payment_wire_transfer_info']=CHBSHelper::getPostValue('payment_wire_transfer_info');

		/***/
		
		$meta['payment_credit_card_pickup_logo_src']=CHBSHelper::getPostValue('payment_credit_card_pickup_logo_src');
		$meta['payment_credit_card_pickup_info']=CHBSHelper::getPostValue('payment_credit_card_pickup_info');
		
        /***/
        
        $field=array('pickup','dropoff');
        
        foreach($field as $fieldName)
        {
            $meta['driving_zone_restriction_'.$fieldName.'_location_enable']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_enable');
            if(!$Validation->isBool($meta['driving_zone_restriction_'.$fieldName.'_location_enable']))
                $meta['driving_zone_restriction_'.$fieldName.'_location_enable']=0;            
        
            $meta['driving_zone_restriction_'.$fieldName.'_location_country']=(array)CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_country');
            foreach($meta['driving_zone_restriction_'.$fieldName.'_location_country'] as $index=>$value)
            {
                if($value==-1)
                {
                    $meta['driving_zone_restriction_'.$fieldName.'_location_country']=array(-1);
                    break;
                }

                if(!$Country->isCountry($value))
                    unset($meta['driving_zone_restriction_'.$fieldName.'_location_country'][$index]);
            }
            
            if(!count($meta['driving_zone_restriction_'.$fieldName.'_location_country']))
                $meta['driving_zone_restriction_'.$fieldName.'_location_country']=array(-1);
            
            $meta['driving_zone_restriction_'.$fieldName.'_location_area_radius']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area_radius');
            if(!$Validation->isNumber($meta['driving_zone_restriction_'.$fieldName.'_location_area_radius'],0,99999)) {
                $meta['driving_zone_restriction_' . $fieldName . '_location_area_radius'] = 50;
            }
            
            $meta['driving_zone_restriction_'.$fieldName.'_location_area']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area');
            $meta['driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lat']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lat');
            $meta['driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lng']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lng');
        }
             
        /***/
        /***/
        
        $FormElement=new CHBSBookingFormElement();
        $FormElement->save($postId);        
        
        /***/
        /***/

        $GoogleMap=new CHBSGoogleMap();
                
        $meta['google_map_default_location_type']=CHBSHelper::getPostValue('google_map_default_location_type');
        if(!in_array($meta['google_map_default_location_type'],array(1,2)))
            $meta['google_map_default_location_type']=1;       
        
        $meta['google_map_default_location_fixed']=CHBSHelper::getPostValue('google_map_default_location_fixed');
        $meta['google_map_default_location_fixed_coordinate_lat']=CHBSHelper::getPostValue('google_map_default_location_fixed_coordinate_lat');
        $meta['google_map_default_location_fixed_coordinate_lng']=CHBSHelper::getPostValue('google_map_default_location_fixed_coordinate_lng');
        
        $meta['google_map_route_avoid']=(array)CHBSHelper::getPostValue('google_map_route_avoid');
        if(in_array(-1,$meta['google_map_route_avoid']))
        {
            $meta['google_map_route_avoid']=array(-1);
        }
        else
        {
            $avoid=$GoogleMap->getRouteAvoid();
            foreach($meta['google_map_route_avoid'] as $index=>$value)
            {
                if(!isset($avoid[$value]))
                    unset($meta['google_map_route_avoid'][$value]);                
            }
        }
        
        $meta['google_map_traffic_layer_enable']=CHBSHelper::getPostValue('google_map_traffic_layer_enable');  
        $meta['google_map_draggable_enable']=CHBSHelper::getPostValue('google_map_draggable_enable');  
        $meta['google_map_scrollwheel_enable']=CHBSHelper::getPostValue('google_map_scrollwheel_enable');  
        
        if(!$Validation->isBool($meta['google_map_traffic_layer_enable']))
            $meta['google_map_traffic_layer_enable']=0;     
        if(!$Validation->isBool($meta['google_map_draggable_enable']))
            $meta['google_map_draggable_enable']=1;        
        if(!$Validation->isBool($meta['google_map_scrollwheel_enable']))
            $meta['google_map_scrollwheel_enable']=1;             

        /***/
        
        $meta['google_map_draggable_location_enable']=CHBSHelper::getPostValue('google_map_draggable_location_enable'); 
        if(!$Validation->isBool($meta['google_map_draggable_location_enable']))
            $meta['google_map_draggable_location_enable']=0;              
        
        /***/
        
        $meta['google_map_map_type_control_enable']=CHBSHelper::getPostValue('google_map_map_type_control_enable');  
        $meta['google_map_map_type_control_id']=CHBSHelper::getPostValue('google_map_map_type_control_id'); 
        $meta['google_map_map_type_control_style']=CHBSHelper::getPostValue('google_map_map_type_control_style'); 
        $meta['google_map_map_type_control_position']=CHBSHelper::getPostValue('google_map_map_type_control_position');  
        
        if(!$Validation->isBool($meta['google_map_map_type_control_enable']))
            $meta['google_map_map_type_control_enable']=0;   
        if(!array_key_exists($meta['google_map_map_type_control_id'],$GoogleMap->getMapTypeControlId()))
            $meta['google_map_map_type_control_id']='SATELLITE';        
        if(!array_key_exists($meta['google_map_map_type_control_style'],$GoogleMap->getMapTypeControlStyle()))
            $meta['google_map_map_type_control_style']='DEFAULT';         
        if(!array_key_exists($meta['google_map_map_type_control_position'],$GoogleMap->getPosition()))
            $meta['google_map_map_type_control_position']='TOP_CENTER';
        
        /***/
        
        $meta['google_map_zoom_control_enable']=CHBSHelper::getPostValue('google_map_zoom_control_enable');  
        $meta['google_map_zoom_control_position']=CHBSHelper::getPostValue('google_map_zoom_control_position');  
        $meta['google_map_zoom_control_level']=CHBSHelper::getPostValue('google_map_zoom_control_level'); 
        
        if(!$Validation->isBool($meta['google_map_zoom_control_enable']))
            $meta['google_map_zoom_control_enable']=0;   
        if(!array_key_exists($meta['google_map_zoom_control_position'],$GoogleMap->getPosition()))
            $meta['google_map_zoom_control_position']='TOP_CENTER';        
        if(!$Validation->isNumber($meta['google_map_zoom_control_level'],1,21))
            $meta['google_map_zoom_control_position']=6;   

        /***/
        /***/
        
        $meta['google_calendar_enable']=CHBSHelper::getPostValue('google_calendar_enable');  
        $meta['google_calendar_id']=CHBSHelper::getPostValue('google_calendar_id');  
        $meta['google_calendar_settings']=CHBSHelper::getPostValue('google_calendar_settings');  
        
        if(!$Validation->isBool($meta['google_calendar_enable']))
            $meta['google_calendar_enable']=0;           
        
        /***/
        
        $meta['style_color']=(array)CHBSHelper::getPostValue('style_color');   
        foreach($meta['style_color'] as $index=>$value)
        {
            if(!$BookingFormStyle->isColor($index))
            {
                unset($meta['style_color'][$index]);
                continue;
            }
            
            if(!$Validation->isColor($value,true))
                $meta['style_color'][$index]='';
        }
        
        /***/
        /***/

		foreach($meta as $index=>$value)
            CHBSPostMeta::updatePostMeta($postId,$index,$value);    
        
        $BookingFormStyle->createCSSFile();
    }
    
	/**************************************************************************/
	
	function setPostMetaDefault(&$meta)
	{
        $BookingFormStyle=new CHBSBookingFormStyle();
        
        CHBSHelper::setDefault($meta,'booking_source_product', '');
        CHBSHelper::setDefault($meta,'service_type_id',array(1,2,3));
        CHBSHelper::setDefault($meta,'service_type_id_default',1);
        
        CHBSHelper::setDefault($meta,'transfer_type_enable_1',array());
        CHBSHelper::setDefault($meta,'transfer_type_enable_3',array(1,2,3));
        
        CHBSHelper::setDefault($meta,'vehicle_category_id',array(-1));
        CHBSHelper::setDefault($meta,'vehicle_id_default',-1);        
        CHBSHelper::setDefault($meta,'vehicle_filter_enable',array(1,2,4));
        CHBSHelper::setDefault($meta,'vehicle_sorting_type',0);
        CHBSHelper::setDefault($meta,'vehicle_pagination_vehicle_per_page',0);
        CHBSHelper::setDefault($meta,'vehicle_limit',0);
        CHBSHelper::setDefault($meta,'vehicle_bid_enable',0);
        CHBSHelper::setDefault($meta,'vehicle_bid_max_percentage_discount',0);
        
        CHBSHelper::setDefault($meta,'route_id',array(-1));
        CHBSHelper::setDefault($meta,'route_list_item_empty_enable',0);
        CHBSHelper::setDefault($meta,'route_list_item_empty_text','');
        
        CHBSHelper::setDefault($meta,'booking_extra_category_id',array(-1));
        
        CHBSHelper::setDefault($meta,'booking_extra_category_display_enable',0);

        CHBSHelper::setDefault($meta,'currency',array(-1));
        
        CHBSHelper::setDefault($meta,'extra_time_enable',1);
        CHBSHelper::setDefault($meta,'extra_time_range_min',0);
        CHBSHelper::setDefault($meta,'extra_time_range_max',24);
        CHBSHelper::setDefault($meta,'extra_time_step',1);
        CHBSHelper::setDefault($meta,'extra_time_unit',2);
        
        CHBSHelper::setDefault($meta,'duration_min',1);
        CHBSHelper::setDefault($meta,'duration_max',24);
        CHBSHelper::setDefault($meta,'duration_step',1);
        
        CHBSHelper::setDefault($meta,'booking_period_from','');
        CHBSHelper::setDefault($meta,'booking_period_to','');
        CHBSHelper::setDefault($meta,'booking_period_type',1);
        
        CHBSHelper::setDefault($meta,'booking_vehicle_interval',0);
        
        CHBSHelper::setDefault($meta,'booking_summary_hide_fee',0);
        CHBSHelper::setDefault($meta,'price_hide',0);       
        CHBSHelper::setDefault($meta,'order_sum_split',0);       
        CHBSHelper::setDefault($meta,'show_net_price_hide_tax',0);  
        
        CHBSHelper::setDefault($meta,'gratuity_enable',0);
        CHBSHelper::setDefault($meta,'gratuity_admin_type',1);
        CHBSHelper::setDefault($meta,'gratuity_admin_value','');
        CHBSHelper::setDefault($meta,'gratuity_customer_enable',0);
        CHBSHelper::setDefault($meta,'gratuity_customer_type',array(1));
        
        CHBSHelper::setDefault($meta,'vehicle_price_round','');
        
        CHBSHelper::setDefault($meta,'prevent_double_vehicle_booking_enable',0);
        CHBSHelper::setDefault($meta,'vehicle_in_the_same_booking_passenger_sum_enable',0);
        
        CHBSHelper::setDefault($meta,'step_second_enable',1);
        CHBSHelper::setDefault($meta,'thank_you_page_enable',1);
        CHBSHelper::setDefault($meta,'thank_you_page_button_back_to_home_label',__('Back To Home','chauffeur-booking-system'));
        CHBSHelper::setDefault($meta,'thank_you_page_button_back_to_home_url_address','');
        
        CHBSHelper::setDefault($meta,'distance_minimum',0);
        CHBSHelper::setDefault($meta,'duration_minimum',0);
        CHBSHelper::setDefault($meta,'order_value_minimum',CHBSPrice::getDefaultPrice());
        CHBSHelper::setDefault($meta,'timepicker_step',30);
        
        CHBSHelper::setDefault($meta,'timepicker_dropdown_list_enable',1);
        
        CHBSHelper::setDefault($meta,'form_preloader_enable',1);
        CHBSHelper::setDefault($meta,'form_preloader_image_src','');
        CHBSHelper::setDefault($meta,'form_preloader_background_opacity',20);
        CHBSHelper::setDefault($meta,'form_preloader_background_color','FFFFFF');
        
        CHBSHelper::setDefault($meta,'billing_detail_state',1);
        CHBSHelper::setDefault($meta,'billing_detail_list_state','');       
        
        CHBSHelper::setDefault($meta,'booking_status_default_id',1);
        
        CHBSHelper::setDefault($meta,'driver_default_id',-1);
        CHBSHelper::setDefault($meta,'country_default',-1);
        
        CHBSHelper::setDefault($meta,'geolocation_server_side_enable',1);
        
        CHBSHelper::setDefault($meta,'summary_sidebar_sticky_enable',0);
        
        CHBSHelper::setDefault($meta,'scroll_to_booking_extra_after_select_vehicle_enable',1);
        
		CHBSHelper::setDefault($meta,'dropoff_location_field_enable',1);
		
		CHBSHelper::setDefault($meta,'passenger_number_vehicle_list_enable',1);
		CHBSHelper::setDefault($meta,'suitcase_number_vehicle_list_enable',1);
		CHBSHelper::setDefault($meta,'use_my_location_link_enable',0);
		
		$fieldMandatory=array();
		foreach($this->fieldMandatory as $index=>$value)
		{
			if((int)$value['mandatory']===1)
				$fieldMandatory[]=$index;
		}	
		
		CHBSHelper::setDefault($meta,'field_mandatory',$fieldMandatory);
		
        CHBSHelper::setDefault($meta,'woocommerce_enable',0);
        CHBSHelper::setDefault($meta,'woocommerce_account_enable_type',1);
        
        CHBSHelper::setDefault($meta,'coupon_enable',0);
        
        CHBSHelper::setDefault($meta,'passenger_adult_enable_service_type_1',0);
        CHBSHelper::setDefault($meta,'passenger_children_enable_service_type_1',0);
        CHBSHelper::setDefault($meta,'passenger_adult_enable_service_type_2',0);
        CHBSHelper::setDefault($meta,'passenger_children_enable_service_type_2',0);
        CHBSHelper::setDefault($meta,'passenger_adult_enable_service_type_3',0);
        CHBSHelper::setDefault($meta,'passenger_children_enable_service_type_3',0);
        
        CHBSHelper::setDefault($meta,'passenger_adult_default_number','');
        CHBSHelper::setDefault($meta,'passenger_children_default_number','');
        
        CHBSHelper::setDefault($meta,'calculate_price_by_passenger_quantity',0);
        CHBSHelper::setDefault($meta,'show_price_per_single_passenger',0);

        CHBSHelper::setDefault($meta,'calculation_method_service_type_1',1);
        CHBSHelper::setDefault($meta,'calculation_method_service_type_3',1);        
        
        CHBSHelper::setDefault($meta,'base_location','');
        CHBSHelper::setDefault($meta,'base_location_coordinate_lat','');
        CHBSHelper::setDefault($meta,'base_location_coordinate_lng','');        
        
        CHBSHelper::setDefault($meta,'waypoint_enable',1);

        CHBSHelper::setDefault($meta,'location_fixed_autocomplete_enable',0);
        
        CHBSHelper::setDefault($meta,'location_fixed_pickup_service_type_1',array(-1));
        CHBSHelper::setDefault($meta,'location_fixed_dropoff_service_type_1',array(-1));
        CHBSHelper::setDefault($meta,'location_fixed_pickup_service_type_2',array(-1));
        CHBSHelper::setDefault($meta,'location_fixed_dropoff_service_type_2',array(-1));

        CHBSHelper::setDefault($meta,'location_fixed_list_item_empty_enable',0);
        CHBSHelper::setDefault($meta,'location_fixed_list_item_empty_text','');
        
        CHBSHelper::setDefault($meta,'ride_time_multiplier','1.00');
        
        CHBSHelper::setDefault($meta,'google_autosugestion_address_type',2);
        CHBSHelper::setDefault($meta,'icon_field_enable',0);
        
        CHBSHelper::setDefault($meta,'navigation_top_enable',1);
        CHBSHelper::setDefault($meta,'step_1_right_panel_visibility',1);
        CHBSHelper::setDefault($meta,'vehicle_more_info_default_show',0);
        
        CHBSHelper::setDefault($meta,'booking_title',__('Booking %s','chauffeur-booking-system'));
        
        /***/
        
		for($i=1;$i<8;$i++)
		{
			if(!isset($meta['business_hour'][$i]))
                $meta['business_hour'][$i]=array('start'=>null,'stop'=>null);
		}	

		if(!array_key_exists('date_exclude',$meta))
			$meta['date_exclude']=array();        
        
        /***/
        
        CHBSHelper::setDefault($meta,'payment_mandatory_enable',0);
        CHBSHelper::setDefault($meta,'payment_processing_enable',1);
        CHBSHelper::setDefault($meta,'payment_woocommerce_step_3_enable',1);        
        
        CHBSHelper::setDefault($meta,'payment_deposit_enable',0);
        CHBSHelper::setDefault($meta,'payment_deposit_value',30);
        
        CHBSHelper::setDefault($meta,'payment_id',array(1));
		CHBSHelper::setDefault($meta,'payment_default_id',-1);
		
		CHBSHelper::setDefault($meta,'payment_cash_logo_src','');
		CHBSHelper::setDefault($meta,'payment_cash_info','');

        CHBSHelper::setDefault($meta,'payment_stripe_api_key_secret','');
        CHBSHelper::setDefault($meta,'payment_stripe_api_key_publishable','');
		CHBSHelper::setDefault($meta,'payment_stripe_method',array('card'));
		CHBSHelper::setDefault($meta,'payment_stripe_product_id','');
		CHBSHelper::setDefault($meta,'payment_stripe_redirect_duration','5');
		CHBSHelper::setDefault($meta,'payment_stripe_success_url_address','');
		CHBSHelper::setDefault($meta,'payment_stripe_cancel_url_address','');
		CHBSHelper::setDefault($meta,'payment_stripe_logo_src','');
		CHBSHelper::setDefault($meta,'payment_stripe_info','');
		
        CHBSHelper::setDefault($meta,'payment_paypal_email_address','');
		CHBSHelper::setDefault($meta,'payment_paypal_redirect_duration','5');
        CHBSHelper::setDefault($meta,'payment_paypal_sandbox_mode_enable',0);
		CHBSHelper::setDefault($meta,'payment_paypal_logo_src','');        
		CHBSHelper::setDefault($meta,'payment_paypal_info','');

		CHBSHelper::setDefault($meta,'payment_wire_transfer_logo_src','');
		CHBSHelper::setDefault($meta,'payment_wire_transfer_info','');
		
		CHBSHelper::setDefault($meta,'payment_credit_card_pickup_logo_src','');
		CHBSHelper::setDefault($meta,'payment_credit_card_pickup_info','');
		
        /***/
     
        CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_enable','0');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_enable','0');

        CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_country','-1');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_country','-1');

        CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area','');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area_radius','50');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area_coordinate_lat','');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area_coordinate_lng','');

        CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area','');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area_radius','50');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area_coordinate_lat','');
        CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area_coordinate_lng','');
              
        /***/
        
        CHBSHelper::setDefault($meta,'booking_new_sender_email_account_id',-1);
        CHBSHelper::setDefault($meta,'booking_new_recipient_email_address','');
        
        CHBSHelper::setDefault($meta,'nexmo_sms_enable',0);
        CHBSHelper::setDefault($meta,'nexmo_sms_api_key','');
        CHBSHelper::setDefault($meta,'nexmo_sms_api_key_secret','');
        CHBSHelper::setDefault($meta,'nexmo_sms_sender_name','');
        CHBSHelper::setDefault($meta,'nexmo_sms_recipient_phone_number','');
        CHBSHelper::setDefault($meta,'nexmo_sms_message',__('New booking has been received.','chauffeur-booking-system'));
     
        CHBSHelper::setDefault($meta,'twilio_sms_enable',0);
        CHBSHelper::setDefault($meta,'twilio_sms_api_sid','');
        CHBSHelper::setDefault($meta,'twilio_sms_api_token','');
        CHBSHelper::setDefault($meta,'twilio_sms_sender_phone_number','');
        CHBSHelper::setDefault($meta,'twilio_sms_recipient_phone_number','');
        CHBSHelper::setDefault($meta,'twilio_sms_message',__('New booking has been received.','chauffeur-booking-system'));
        
        CHBSHelper::setDefault($meta,'telegram_enable',0);
        CHBSHelper::setDefault($meta,'telegram_token','');
        CHBSHelper::setDefault($meta,'telegram_group_id','');
        CHBSHelper::setDefault($meta,'telegram_message',__('New booking has been received.','chauffeur-booking-system'));
        
        /***/
                
        CHBSHelper::setDefault($meta,'google_map_default_location_type',1);
        CHBSHelper::setDefault($meta,'google_map_default_location_fixed','');
        CHBSHelper::setDefault($meta,'google_map_default_location_fixed_coordinate_lat','');
        CHBSHelper::setDefault($meta,'google_map_default_location_fixed_coordinate_lng','');
        
        CHBSHelper::setDefault($meta,'google_map_route_avoid',-1);
        
        CHBSHelper::setDefault($meta,'google_map_draggable_enable',1);
        CHBSHelper::setDefault($meta,'google_map_scrollwheel_enable',1);
        CHBSHelper::setDefault($meta,'google_map_traffic_layer_enable',0);
        
        CHBSHelper::setDefault($meta,'google_map_draggable_location_enable',0);
        
        CHBSHelper::setDefault($meta,'google_map_map_type_control_enable',0);
        CHBSHelper::setDefault($meta,'google_map_map_type_control_id','SATELLITE');
        CHBSHelper::setDefault($meta,'google_map_map_type_control_style','DEFAULT');
        CHBSHelper::setDefault($meta,'google_map_map_type_control_position','TOP_CENTER');
        
        CHBSHelper::setDefault($meta,'google_map_zoom_control_enable',0);
        CHBSHelper::setDefault($meta,'google_map_zoom_control_style','DEFAULT');
        CHBSHelper::setDefault($meta,'google_map_zoom_control_position','TOP_CENTER');
        CHBSHelper::setDefault($meta,'google_map_zoom_control_level',6);
        
        CHBSHelper::setDefault($meta,'google_map_pan_control_enable',0);
        CHBSHelper::setDefault($meta,'google_map_pan_control_position','TOP_CENTER');        

        CHBSHelper::setDefault($meta,'google_map_scale_control_enable',0);
        CHBSHelper::setDefault($meta,'google_map_scale_control_position','TOP_CENTER');        
        
        CHBSHelper::setDefault($meta,'google_map_street_view_enable',0);
        CHBSHelper::setDefault($meta,'google_map_street_view_postion','TOP_CENTER');        
        
        /***/
        
        CHBSHelper::setDefault($meta,'google_calendar_enable',0);
        CHBSHelper::setDefault($meta,'google_calendar_id','');
        CHBSHelper::setDefault($meta,'google_calendar_settings','');
        
        /***/
        
        CHBSHelper::setDefault($meta,'style_color',array_fill(1,count($BookingFormStyle->getColor()),''));   
	}
    
    /**************************************************************************/
    
    function getDictionary($attr=array())
    {
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'booking_form_id'   												=>	0
		);
		
		$attribute=shortcode_atts($default,$attr);
		
		CHBSHelper::preservePost($post,$bPost);
		
		$argument=array
		(
			'post_type'															=>	self::getCPTName(),
			'post_status'														=>	'publish',
			'posts_per_page'													=>	-1,
			'orderby'															=>	array('menu_order'=>'asc','title'=>'asc')
		);
		
		if(array_key_exists('booking_form_id',$attr))
        {
			$argument['p']=$attribute['booking_form_id'];
            if((int)$argument['p']<=0) return($dictionary);
        }

		$forms = get_posts($argument);

        foreach ($forms as $form){
            $dictionary[$form->ID]['post'] = $form;
            $dictionary[$form->ID]['meta'] = CHBSPostMeta::getPostMeta($form);
        }

		return($dictionary);        
    }
    
    /**************************************************************************/
    
    function createBookingForm($attr)
    {
        $Length=new CHBSLength();
        $TaxRate=new CHBSTaxRate();
        $TransferType=new CHBSTransferType();
        
		$action=CHBSHelper::getGetValue('action',false);
                
		$default=array
		(
			'booking_form_id'   												=>	0,
            'currency'                                                          =>  '',
            'widget_mode'                                                       =>  0,
            'widget_style'                                                      =>  1,
            'widget_service_type_id'                                            =>  1,
            'widget_booking_form_url'                                           =>  '',
            'widget_booking_form_new_window'                                    =>  0,
		);
		
        $data=array();
        
		$attribute=shortcode_atts($default,$attr);               

        if(!is_array($data=$this->checkBookingForm($attribute['booking_form_id'],$attribute['currency'],true))) return;
             
        $data['ajax_url']=admin_url('admin-ajax.php');
        
        $data['booking_form_post_id']=$attribute['booking_form_id'];
        $data['booking_form_html_id']=CHBSHelper::createId('chbs_booking_form');
        
        $data['dictionary']['transfer_type']=$TransferType->getTransferType();

        $data['dictionary']['tax_rate']=$TaxRate->getDictionary();
                
        $dictionary=$Length->getUnit();
        $data['length_unit']=$dictionary[CHBSOption::getOption('length_unit')];
        $data['length_unit_id']=CHBSOption::getOption('length_unit');
       
        if($attribute['widget_mode']==1)
        {
            if(!in_array($attribute['widget_service_type_id'],$data['meta']['service_type_id']))
            {
                $attribute['widget_service_type_id']=$data['meta']['service_type_id'][0];
            }
        }
        
        $data['widget_mode']=$attribute['widget_mode'];
        $data['widget_style']=$attribute['widget_style'];
        $data['widget_service_type_id']=$attribute['widget_service_type_id'];
        $data['widget_booking_form_url']=$attribute['widget_booking_form_url'];
        $data['widget_booking_form_new_window']=$attribute['widget_booking_form_new_window'];
        
        $data['datetime_period']=$this->getBookingFormDateAvailable($data['meta']);
        
        $Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'public/public.php');
        return($Template->output());
    }
    
    /**************************************************************************/
    
    function bookingFormDisplayError($message,$displayError)
    {
        if(!$displayError) return;
        echo '<div class="chbs-booking-form-error">'.esc_html($message).'</div>';
    }
    
    /**************************************************************************/
    
    function checkBookingForm($bookingFormId,$currency=null,$displayError=false)
    {
        $data=array();
        
        $Validation=new CHBSValidation();
        $WooCommerce=new CHBSWooCommerce();
        
        $bookingForm=$this->getDictionary(array('booking_form_id'=>$bookingFormId));
        if(!count($bookingForm)) 
        {
            $this->bookingFormDisplayError(__('Booking form with provided ID doesn\'t exist.','chauffeur-booking-form'),$displayError);
            return(-1);
        }
        
        $data['post']=$bookingForm[$bookingFormId]['post'];
        $data['meta']=$bookingForm[$bookingFormId]['meta'];
       
        if(in_array(3,$data['meta']['service_type_id']))
        {
            $data['dictionary']['route']=$this->getBookingFormRoute($data['meta']);
            if(!count($data['dictionary']['route'])) 
            {
                $this->bookingFormDisplayError(__('There are not assigned routes for flat rate service type. Please create at least one route or disable "Flat rate" service type in booking form settings.','chauffeur-booking-form'),$displayError);
                return(-2);
            }
        }   
        
        $data['dictionary']['vehicle']=$this->getBookingFormVehicle($data['meta']);
        
        if(!count($data['dictionary']['vehicle'])) 
        { 
            $this->bookingFormDisplayError(__('Plugin cannot find at least one vehicle.','chauffeur-booking-form'),$displayError);
            return(-3);
        }
        
        if($Validation->isEmpty($currency))
            $currency=CHBSHelper::getGetValue('currency',false);
        
        if(in_array($currency,$data['meta']['currency']))
            $data['currency']=$currency;
        else $data['currency']=CHBSOption::getOption('currency');
        
        if($WooCommerce->isEnable($data['meta']))
        {
            $data['dictionary']['payment_woocommerce']=$WooCommerce->getPaymentDictionary();
        }
        else 
        {
            $data['dictionary']['payment']=$this->getBookingFormPayment($data['meta']);
        }
        
//        $data['dictionary']['booking_extra']=$this->getBookingFormExtra($data['meta']);
//
//        $data['dictionary']['booking_extra_category']=$this->getBookingFormExtraCategory($data['dictionary']['booking_extra']);
        
        $data['dictionary']['vehicle_category']=$this->getBookingFormVehicleCategory($data['meta']);
  
        $data['vehicle_bag_count_range']=$this->getVehicleBagCountRange($data['dictionary']['vehicle']);
        $data['vehicle_passenger_count_range']=$this->getVehiclePassengerCountRange($data);
        
        /****/
        
        $TaxRate=new CHBSTaxRate();
        $Country=new CHBSCountry();
        $Geofence=new CHBSGeofence();
        $PriceRule=new CHBSPriceRule();
        
        $data['dictionary']['country']=$Country->getCountry();
        $data['dictionary']['geofence']=$Geofence->getDictionary();
        $data['dictionary']['tax_rate']=$TaxRate->getDictionary();
        $data['dictionary']['price_rule']=$PriceRule->getDictionary();
        
        /****/

        $data['step']=array();
        $data['step']['disable']=array();
        
        if(($data['meta']['step_second_enable']!=1) && (count($data['dictionary']['vehicle'])==1))
        {
            $data['step']['disable']=array(2);
        }
        
        $data['step']['dictionary']=array
        (
            1                                                                   =>  array
            (
                'navigation'                                                    =>  array
                (
                    'number'                                                    =>  __('1','chauffeur-booking-system'),
                    'label'                                                     =>  __('Enter Ride Details','chauffeur-booking-system'),
                ),
                'button'                                                        =>  array
                (
                    'next'                                                      =>  __('Choose a vehicle','chauffeur-booking-system')
                )
            ),
            //Arrival transport & extras choosing step
            2                                                                   =>  array
            (
                'navigation'                                                    =>  array
                (                
                    'number'                                                    =>  __('2','chauffeur-booking-system'),
                    'label'                                                     =>  __('Choose a Vehicle','chauffeur-booking-system')
                ),
                'button'                                                        =>  array
                (
                    'prev'                                                      =>  __('Choose ride details','chauffeur-booking-system'),
                    'next'                                                      => array(
                        'next_1'                                                    =>  ((int)$data['meta']['price_hide']===1 ? __('Send now','chauffeur-booking-system') : __('Enter contact details','chauffeur-booking-system')),
                        'next_3'                                                    =>  __('Choose return vehicle','chauffeur-booking-system')
                    )
                )
            ),

            // Return transport & extras choosing step
            3                                                                   =>  array
            (
                'navigation'                                                    =>  array
                (
                    'number'                                                    =>  __('3','chauffeur-booking-system'),
                    'label'                                                     =>  __('Choose Return Vehicle','chauffeur-booking-system')
                ),
                'button'                                                        =>  array
                (
                    'prev'                                                      =>  __('Choose a vehicle','chauffeur-booking-system'),
                    'next'                                                      =>  __('Enter contact details','chauffeur-booking-system')
                )
            ),
            4                                                                   =>  array
            (
                'navigation'                                                    =>  array
                (
                    'number'                                                    =>  __('4','chauffeur-booking-system'),
                    'label'                                                     =>  __('Enter Contact Details','chauffeur-booking-system')
                ),
                'button'                                                        =>  array
                (
                    'prev'                                                      =>  __('Choose a vehicle','chauffeur-booking-system'),
                    'next'                                                      =>  __('Book now','chauffeur-booking-system')
                )
            ),
            5                                                                   =>  array
            (
                'navigation'                                                    =>  array
                (
                    'number'                                                    =>  __('5','chauffeur-booking-system'),
                    'label'                                                     =>  __('Booking Summary','chauffeur-booking-system')
                ),
                'button'                                                        =>  array
                (
                    'prev'                                                      =>  __('Enter contact details','chauffeur-booking-system'),
                    'next'                                                      =>  ((int)$data['meta']['price_hide']===1 ? __('Send now','chauffeur-booking-system') : __('Book now','chauffeur-booking-system'))
                )
            )            
        );
        
        if(in_array(2,$data['step']['disable']))
        {
            $data['step']['dictionary'][4]['navigation']['number']=$data['step']['dictionary'][3]['navigation']['number'];
            $data['step']['dictionary'][3]['navigation']['number']=$data['step']['dictionary'][2]['navigation']['number'];
            
            $data['step']['dictionary'][1]['button']['next']=$data['step']['dictionary'][2]['button']['next'];
            $data['step']['dictionary'][3]['button']['prev']=$data['step']['dictionary'][2]['button']['prev'];
        }
       
        $data['vehicle_id_default']=0;
        if(in_array(2,$data['step']['disable']))
        {
            reset($data['dictionary']['vehicle']);
            $data['vehicle_id_default']=key($data['dictionary']['vehicle']);
        }
        else {
            $data['vehicle_id_default']=$data['meta']['vehicle_id_default'];
        }
        
        foreach($data['step']['disable'] as $value)
            unset($data['step']['dictionary'][$value]);
        
        /***/
           
        $GeoLocation=new CHBSGeoLocation();
		
		if($data['meta']['country_default']=='-1')
        {
			if((int)$data['meta']['geolocation_server_side_enable']===1)
			{
				$data['client_country_code']=$GeoLocation->getCountryCode();
			}
		}
		else $data['client_country_code']=$data['meta']['country_default'];
		
        /***/
        
        $Location=new CHBSLocation();
        
        $data['dictionary']['location']=$Location->getDictionary();
        
        $field=array('location_fixed_pickup_service_type_1','location_fixed_dropoff_service_type_1','location_fixed_pickup_service_type_2','location_fixed_dropoff_service_type_2');

        foreach($field as $fieldName)
        {
            foreach($data['meta'][$fieldName] as $index=>$value)
            {
                if($value==-1)
                {
                    $data['meta'][$fieldName]=array();
                    break;
                }

                if(array_key_exists($value,$data['dictionary']['location']))                        
                {
                    $location=$data['dictionary']['location'][$value];
                    $data['meta'][$fieldName][$value]=array('id'=>$value,'address'=>$location['meta']['location_name'],'formatted_address'=>$location['post']->post_title,'lat'=>$location['meta']['location_name_coordinate_lat'],'lng'=>$location['meta']['location_name_coordinate_lng'],'dropoff_disable'=>array());
                }
                
                if($fieldName==='location_fixed_pickup_service_type_1')
                {
                    $t=$data['dictionary']['location'][$value]['meta']['location_dropoff_disable_service_type_1'];
                    
                    if(is_array($t))
                    {
                        if(array_key_exists($bookingFormId,$t))
                        {
                            if(!in_array(-1,$t[$bookingFormId]))
                                $data['meta'][$fieldName][$value]['dropoff_disable']=$t[$bookingFormId];
                        }
                    }
                }
                
                if($fieldName==='location_fixed_pickup_service_type_2')
                {
                    $t=$data['dictionary']['location'][$value]['meta']['location_dropoff_disable_service_type_2'];
                    
                    if(is_array($t))
                    {
                        if(array_key_exists($bookingFormId,$t))
                        {
                            if(!in_array(-1,$t[$bookingFormId]))
                                $data['meta'][$fieldName][$value]['dropoff_disable']=$t[$bookingFormId];
                        }
                    }
                }
            }
            
			foreach($data['meta'][$fieldName] as $index=>$value)
			{
				if(!is_array($value)) unset($data['meta'][$fieldName][$index]);
			}		
        }
        
        $data['meta']['waypoint_enable']=($data['meta']['waypoint_enable']==1) && (!count($data['meta']['location_fixed_pickup_service_type_1'])) && (!count($data['meta']['location_fixed_dropoff_service_type_1']));

        /***/

        return($data);
    }
    
    /**************************************************************************/
    
    function getBookingFormVehicle($meta)
    {
        $category=array();
        
        if(count($meta['vehicle_category_id'])) {
            $category = array_diff($meta['vehicle_category_id'], array(-1));
        }

        $Date=new CHBSDate();
        $Vehicle=new CHBSVehicle();
        
        $dictionary=$Vehicle->getDictionary(array('category_id'=>$category),$meta['vehicle_sorting_type']);
                        
        $data=CHBSHelper::getPostOption();
                
        if(isset($data['service_type_id']))
        {
            $serviceTypeId=$data['service_type_id'];
            
            /***/

            if((int)$serviceTypeId===3)
            {
                $Route=new CHBSRoute();
                
                $route=$Route->getDictionary(array('route_id'=>(int)$data['route_service_type_3']));
                $route=$Route->getEnableVehicleFromRoute($route);
                
                foreach($dictionary as $index=>$value)
                {
                    if(!in_array($index,$route))
                        unset($dictionary[$index]);
                }
            }
            
            /***/
            
            $pickupDate=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
            $pickupTime=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);    
            
            /***/
            
            $returnDate=null;
            $returnTime=null;
            
            if(in_array($serviceTypeId,array(1,3)))
            {
                if((int)$data['transfer_type_service_type_'.$serviceTypeId]===3)
                {
                    $returnDate=$Date->formatDateToStandard($data['return_date_service_type_'.$serviceTypeId]);
                    $returnTime=$Date->formatTimeToStandard($data['return_time_service_type_'.$serviceTypeId]);                       
                }
            }
            
            /***/
                    
            $duration=$data['duration_sum'];
            
            if($meta['step_second_enable']==1) {
                $dictionary = $Vehicle->checkAvailability($dictionary, $pickupDate, $pickupTime, $returnDate, $returnTime, $duration, $data, $meta);
            }

        }

        $Vehicle->getVehicleAttribute($dictionary);
        
        return($dictionary);
    }
    
    /**************************************************************************/
    
    function getBookingFormVehicleCategory($meta)
    {
        $Vehicle=new CHBSVehicle();
        $dictionary=$Vehicle->getCategory();
     
        $vehicleCategory=array();
        if(count($meta['vehicle_category_id']))
            $vehicleCategory=array_diff($meta['vehicle_category_id'],array(-1));
                
        if(!count($vehicleCategory)) return($dictionary);
        
        foreach($dictionary as $index=>$value)
        {
            if(!in_array($index,$vehicleCategory))
                unset($dictionary[$index]);
        }

        return($dictionary);
    }
    
    /**************************************************************************/
    
    function getBookingFormRoute($meta)
    {
        $Route=new CHBSRoute();
        
        $route=array();
        if(count($meta['route_id']))
            $route=array_diff($meta['route_id'],array(-1));      
        
        $dictionary=$Route->getDictionary(array('route_id'=>$route));
        
        return($dictionary);
    }
   
    /**************************************************************************/
    
    function getBookingFormPayment($meta)
    {
        $Payment=new CHBSPayment();
        
        $payment=$Payment->getPayment();
        foreach($payment as $index=>$value)
        {
            if(!in_array($index,$meta['payment_id']))
               unset($payment[$index]);
        }
		
        return($payment);
    }
    
    /**************************************************************************/
    
    function getBookingFormExtra($meta)
    {
        $category=array();
        
        $data=CHBSHelper::getPostOption();
        
        $ServiceType=new CHBSServiceType();
        $TransferType=new CHBSTransferType();

        if(count($meta['booking_extra_category_id']))
            $category=array_diff($meta['booking_extra_category_id'],array(-1));

//        $BookingExtra=new CHBSBookingExtra();
//        $dictionary=$BookingExtra->getDictionary(array('category_id'=>$category));
                
        $serviceTypeId=array_key_exists('service_type_id',$data) ? $data['service_type_id'] : 0;
        
//        if($ServiceType->isServiceType($serviceTypeId))
//        {
//            $transferTypeId=array_key_exists('transfer_type_service_type_'.$serviceTypeId,$data) ? $data['transfer_type_service_type_'.$serviceTypeId] : 0;
//            if(!$TransferType->isTransferType($transferTypeId)) $transferTypeId=1;
//
//            foreach($dictionary as $index=>$value)
//            {
//                if(array_key_exists('service_type_id_enable',$value['meta']))
//                {
//                    if(is_array($value['meta']['service_type_id_enable']))
//                    {
//                        if(!in_array($serviceTypeId,$value['meta']['service_type_id_enable']))
//                            unset($dictionary[$index]);
//                    }
//                }
//                if(array_key_exists('transfer_type_id_enable',$value['meta']))
//                {
//                    if(is_array($value['meta']['transfer_type_id_enable']))
//                    {
//                        if(!in_array($transferTypeId,$value['meta']['transfer_type_id_enable']))
//                            unset($dictionary[$index]);
//                    }
//                }
//            }
//        }
        
//        $Coupon=new CHBSCoupon();
//        $coupon=$Coupon->checkCode();
        
//        if($coupon!==false)
//        {
//            $discountPercentage=$coupon['meta']['discount_percentage'];
//            foreach($dictionary as $index=>$value)
//                $dictionary[$index]['meta']['price']=round($dictionary[$index]['meta']['price']*(1-$discountPercentage/100),2);
//        }
        $dictionary =[];
        return($dictionary);        
    }
	
	/**************************************************************************/
    
	function getBookingFormExtraCategory($bookingExtra)
	{
//		$BookingExtra=new CHBSBookingExtra();
//		$bookingExtraCategory=$BookingExtra->getCategory();
		
//		foreach($bookingExtraCategory as $index=>$value)
//			$bookingExtraCategory[$index]['_unset']=1;
//
//		foreach($bookingExtra as $bookingExtraIndex=>$bookingExtraValue)
//		{
//			if((isset($bookingExtraValue['category'])) && (is_array($bookingExtraValue['category'])))
//			{
//				foreach($bookingExtraValue['category'] as $categoryIndex=>$categoryValue)
//				{
//					foreach($categoryValue as $categoryValueValue)
//					{
//						if(isset($bookingExtraCategory[$categoryValueValue['term_id']]))
//							$bookingExtraCategory[$categoryValueValue['term_id']]['_unset']=0;
//					}
//				}
//			}
//		}
//
//		foreach($bookingExtraCategory as $index=>$value)
//		{
//			if((int)$bookingExtraCategory[$index]['_unset']===1)
//				unset($bookingExtraCategory[$index]);
//		}
		
		return([]);
	}
	
    /**************************************************************************/
    
    function getAvailableStepNumber($stepCurrent,$stepRequest,$bookingForm)
    {
        if(in_array($stepRequest,$bookingForm['step']['disable']))
            return($this->getAvailableStepNumber($stepCurrent,($stepRequest>$stepCurrent ? $stepRequest+1 : $stepRequest-1),$bookingForm));
        
        return($stepRequest);
    }
    
    /**************************************************************************/

    function goToStep()
    {
        $response=array();

        $Date=new CHBSDate();
        $Length=new CHBSLength();
        $Validation=new CHBSValidation();
        $TransferType=new CHBSTransferType();
       
        $data=CHBSHelper::getPostOption();

        if($data['transfer_type_service_type_'.$data['service_type_id']] == 1 && $data['step_request'] == 3){
            if($data['step'] == 4){
                $data['step_request'] = 2;
            }else{
                $data['step_request'] = 4;
            }
        }

        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            if($bookingForm===-3)
            {
                $response['step']=1;
                $this->setErrorGlobal($response,__('Cannot find at least one vehicle available in selected time period.','chauffeur-booking-system'));
                $this->createFormResponse($response);
            }
        }
       
        $response['booking_summary_hide_fee']=$bookingForm['meta']['booking_summary_hide_fee'];
        
        if((!in_array($data['step_request'],array(2,3,4,5))) || (!in_array($data['step'],array(1,2,3,4))))
        {
            $response['step']=1;
            $this->createFormResponse($response);            
        }

        $data['step_request']=$this->getAvailableStepNumber($data['step'],$data['step_request'],$bookingForm);
        
        /***/
        /***/
        
        if($data['step_request']>1)
        {
            if(!in_array($data['service_type_id'],$bookingForm['meta']['service_type_id']))
                $data['service_type_id']=1;
            
            $data['pickup_date_service_type_'.$data['service_type_id']]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$data['service_type_id']]);
            $data['pickup_time_service_type_'.$data['service_type_id']]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$data['service_type_id']]);          
            
            $dateTimeError=false;
            $validateReturnDateTime=false;
                        
            if(count($bookingForm['meta']['transfer_type_enable_'.$data['service_type_id']]))
            {
                if(!$TransferType->isTransferType($data['transfer_type_service_type_'.$data['service_type_id']]))
                    $this->setErrorLocal($response,CHBSHelper::getFormName('transfer_type_service_type_3',false),__('Select a valid transfer type.','chauffeur-booking-system'));
                else 
                {
                    if((int)$data['transfer_type_service_type_'.$data['service_type_id']]===3)
                    {
                        $validateReturnDateTime=true;
                     
                        $data['return_date_service_type_'.$data['service_type_id']]=$Date->formatDateToStandard($data['return_date_service_type_'.$data['service_type_id']]);
                        $data['return_time_service_type_'.$data['service_type_id']]=$Date->formatTimeToStandard($data['return_time_service_type_'.$data['service_type_id']]);                        
                    }
                }
            }

            if(!$validateReturnDateTime)
            {
                CHBSHelper::removeUIndex($data,'return_date_service_type_'.$data['service_type_id'],'return_time_service_type_'.$data['service_type_id']);
                
                $data['return_date_service_type_'.$data['service_type_id']]=null;
                $data['return_time_service_type_'.$data['service_type_id']]=null;
            }
            
            /***/
            
            // check if format of pickup date is valid
            if(!$Validation->isDate($data['pickup_date_service_type_'.$data['service_type_id']]))
            {
                $dateTimeError=true;
                $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
            }
            // check if format of pickup time is valid
            if(!$Validation->isTime($data['pickup_time_service_type_'.$data['service_type_id']]))
            {   
                $dateTimeError=true;
                $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time.','chauffeur-booking-system'));
            }
            if($validateReturnDateTime)
            {
                // check if format of return date is valid
                if(!$Validation->isDate($data['return_date_service_type_'.$data['service_type_id']]))
                {
                    $dateTimeError=true;
                    $this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                }
                // check if format of return time is valid
                if(!$Validation->isTime($data['return_time_service_type_'.$data['service_type_id']]))
                {   
                    $dateTimeError=true;
                    $this->setErrorLocal($response,CHBSHelper::getFormName('return_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time.','chauffeur-booking-system'));
                }                
            }
            
            /***/
            
            if(!$dateTimeError)
            {
                // check if pickup date/time is later than current date/time
                if(in_array($Date->compareDate($data['pickup_date_service_type_'.$data['service_type_id']].' '.$data['pickup_time_service_type_'.$data['service_type_id']],date_i18n('Y-m-d H:i')),array(2)))
                {
                    $dateTimeError=true;
                    $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Pickup date and time has to be later than current one.','chauffeur-booking-system'));                    
                }                    
            }            
            
            /***/
            
            if(!$dateTimeError)
            {
                if($validateReturnDateTime)
                {
                    // check if return date/time is later than pickup date/time
                    if(in_array($Date->compareDate($data['pickup_date_service_type_'.$data['service_type_id']].' '.$data['pickup_time_service_type_'.$data['service_type_id']],$data['return_date_service_type_'.$data['service_type_id']].' '.$data['return_time_service_type_'.$data['service_type_id']]),array(0,1)))
                    {
                        $dateTimeError=true;
                        $this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Return date and time has to be later than pick up date and time.','chauffeur-booking-system'));                    
                    }
                }
            }          
            
            /***/
            
            // check booking period for pickup date/time
            if(!$dateTimeError)
            {
                $bookingPeriodFrom=$bookingForm['meta']['booking_period_from'];
                if(!$Validation->isNumber($bookingPeriodFrom,0,9999))
                    $bookingPeriodFrom=0;
                
                list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'pickup',$bookingPeriodFrom);
                if($Date->compareDate($date1,$date2)===2)
                {
                    $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                    $dateTimeError=true;                    
                }       

                if(!$dateTimeError)
                {
                    $bookingPeriodTo=$bookingForm['meta']['booking_period_to'];
                    if($Validation->isNumber($bookingPeriodTo,0,9999))
                    {
                        $bookingPeriodTo+=$bookingPeriodFrom;
                        
                        list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'pickup',$bookingPeriodTo);    
                        if($Date->compareDate($date1,$date2)===1)
                        {
                            $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                            $dateTimeError=true;                    
                        }                               
                    }
                }
            }
            
            // check booking period for return date/time
            if((!$dateTimeError) && ($validateReturnDateTime))
            {
                $bookingPeriodFrom=$bookingForm['meta']['booking_period_from'];
                if(!$Validation->isNumber($bookingPeriodFrom,0,9999))
                    $bookingPeriodFrom=0;
                
                list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'return',$bookingPeriodFrom);
                if($Date->compareDate($date1,$date2)===2)
                {
                    $this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                    $dateTimeError=true;                    
                }       

                if(!$dateTimeError)
                {
                    $bookingPeriodTo=$bookingForm['meta']['booking_period_to'];
                    if($Validation->isNumber($bookingPeriodTo,0,9999))
                    {
                        $bookingPeriodTo+=$bookingPeriodFrom;
                        list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'return',$bookingPeriodTo);
                        
                        if($Date->compareDate($date1,$date2)===1)
                        {
                            $this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                            $dateTimeError=true;                    
                        }                               
                    }
                }
            }
            
            /****/
            
            // check exclude dates
            if(!$dateTimeError)
            {
                if(is_array($bookingForm['meta']['date_exclude']))
                {
                    foreach($bookingForm['meta']['date_exclude'] as $index=>$value)
                    {
                        if($Date->dateInRange($data['pickup_date_service_type_'.$data['service_type_id']],$value['start'],$value['stop']))
                        {
                            $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                            $dateTimeError=true;
                            break;
                        }
                        
                        if($validateReturnDateTime)
                        {
                            if($Date->dateInRange($data['return_date_service_type_'.$data['service_type_id']],$value['start'],$value['stop']))
                            {
                                $this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                                $dateTimeError=true;
                                break;
                            }                            
                        }
                    }
                }
            }
            
            /***/

            // check business hours
            if(!$dateTimeError)
            {
                $number=$Date->getDayNumberOfWeek($data['pickup_date_service_type_'.$data['service_type_id']]);
                
                if(isset($bookingForm['meta']['business_hour'][$number]))
                {
                    if(($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['start'])) && ($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['stop'])))
                    {
                        if(!$Date->timeInRange($data['pickup_time_service_type_'.$data['service_type_id']],$bookingForm['meta']['business_hour'][$number]['start'],$bookingForm['meta']['business_hour'][$number]['stop']))
                        {
                            $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time.','chauffeur-booking-system'));
                            $dateTimeError=true;
                        }
                    }
                    else
                    {
                        $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                        $dateTimeError=true;                        
                    }
                }
            }
            if((!$dateTimeError) && ($validateReturnDateTime))
            {
                $number=$Date->getDayNumberOfWeek($data['return_date_service_type_'.$data['service_type_id']]);
                
                if(isset($bookingForm['meta']['business_hour'][$number]))
                {
                    if(($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['start'])) && ($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['stop'])))
                    {
                        if(!$Date->timeInRange($data['return_time_service_type_'.$data['service_type_id']],$bookingForm['meta']['business_hour'][$number]['start'],$bookingForm['meta']['business_hour'][$number]['stop']))
                        {
                            $this->setErrorLocal($response,CHBSHelper::getFormName('return_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time in format.','chauffeur-booking-system'));
                            $dateTimeError=true;
                        }
                    }
                    else
                    {
                        $this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
                        $dateTimeError=true;                        
                    }
                }                
            }
            
            /***/
            
            if(in_array($data['service_type_id'],array(1,2)))
            {              
                if(count($bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']]))
                {
                    if(!array_key_exists($data['fixed_location_pickup_service_type_'.$data['service_type_id']],$bookingForm['dictionary']['location']))
                        $this->setErrorLocal($response,CHBSHelper::getFormName('fixed_location_pickup_service_type_'.$data['service_type_id'],false),__('Select a valid location.','chauffeur-booking-system'));
                }
                else
                {
                    if(!$Validation->isCoordinateGroup($data['pickup_location_coordinate_service_type_'.$data['service_type_id']]))
                        $this->setErrorLocal($response,CHBSHelper::getFormName('pickup_location_coordinate_service_type_'.$data['service_type_id'],false),__('Enter a valid location.','chauffeur-booking-system'));

                    if(($data['service_type_id']==1) && ($bookingForm['meta']['waypoint_enable']==1))
                    {
                        if(is_array($data['waypoint_location_coordinate_service_type_1']))
                        {
                            unset($data['waypoint_location_coordinate_service_type_1'][0]);
                            foreach($data['waypoint_location_coordinate_service_type_1'] as $index=>$value)
                            {
                                if(!$Validation->isCoordinateGroup($value))
                                    $this->setErrorLocal($response,CHBSHelper::getFormName('waypoint_location_service_type_1-'.$index,false),__('Enter a valid location.','chauffeur-booking-system'));
                            }
                        }
                    }
                }
                
                if(count($bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']]))
                {  
                    if(!array_key_exists($data['fixed_location_dropoff_service_type_'.$data['service_type_id']],$bookingForm['dictionary']['location']))
                        $this->setErrorLocal($response,CHBSHelper::getFormName('fixed_location_dropoff_service_type_'.$data['service_type_id'],false),__('Select a valid location.','chauffeur-booking-system'));
                    else 
                    {
                        $Location=new CHBSLocation();
                        
                        $bookingFormId=$bookingForm['post']->ID;
                        
                        $fixedLocationPickupId=$data['fixed_location_pickup_service_type_'.$data['service_type_id']];
                        
                        $fixedLocationPickupData=$Location->getDictionary(array('location_id'=>$fixedLocationPickupId));
                        
                        $t=$fixedLocationPickupData[$fixedLocationPickupId]['meta'];
                        
                        if((is_array($t)) && (array_key_exists('location_dropoff_disable_service_type_'.$data['service_type_id'],$t)))
                        {
                            $t=$t['location_dropoff_disable_service_type_'.$data['service_type_id']];
                            
                            if(array_key_exists($bookingFormId,$t))
                            {
                                if(!in_array(-1,$t[$bookingFormId]))
                                {
                                    if(in_array($data['fixed_location_dropoff_service_type_'.$data['service_type_id']],$t[$bookingFormId]))
                                        $this->setErrorLocal($response,CHBSHelper::getFormName('fixed_location_dropoff_service_type_'.$data['service_type_id'],false),__('This drop off location is not available for selected pickup location.','chauffeur-booking-system'));
                                }
                            }
                        }
                    }
                }
                else
                {
                    if($data['service_type_id']==1)
                    {
                        if(!$Validation->isCoordinateGroup($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]))
                            $this->setErrorLocal($response,CHBSHelper::getFormName('dropoff_location_coordinate_service_type_'.$data['service_type_id'],false),__('Enter a valid location.','chauffeur-booking-system'));
                    }
                    else if($data['service_type_id']==2)
                    {
                        if($Validation->isNotEmpty($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]))
                        {
                            if(!$Validation->isCoordinateGroup($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]))
                                $this->setErrorLocal($response,CHBSHelper::getFormName('dropoff_location_coordinate_service_type_'.$data['service_type_id'],false),__('Enter a valid location.','chauffeur-booking-system'));
                        }
                    }
                }
            }
            
			if(in_array($data['service_type_id'],array(3)))
            {
				if(!array_key_exists($data['route_service_type_3'],$bookingForm['dictionary']['route']))
                    $this->setErrorLocal($response,CHBSHelper::getFormName('route_service_type_3',false),__('Enter a valid route.','chauffeur-booking-system'));
				else
				{
					$pickupHour=$bookingForm['dictionary']['route'][$data['route_service_type_3']]['meta']['pickup_hour'];

					$dayOfWeek=$Date->getDayNumberOfWeek($data['pickup_date_service_type_3']);

					if((is_array($pickupHour[$dayOfWeek])) && (is_array($pickupHour[$dayOfWeek]['hour'])) && (count($pickupHour[$dayOfWeek]['hour'])))
					{
						$pickupHourFound=false;
						
						foreach($pickupHour[$dayOfWeek]['hour'] as $index=>$value)
						{
							if($value==$data['pickup_time_service_type_3'])
							{
								$pickupHourFound=true;
								break;
							}
						}
						
						if(!$pickupHourFound)
							$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_time_service_type_3',false),__('Enter a valid time.','chauffeur-booking-system'));
					}
				}
			}            
            
            if(in_array($data['service_type_id'],array(2)))
            {
                $find=false;
                $value=$data['duration_service_type_2'];
                
                for($i=$bookingForm['meta']['duration_min'];$i<=$bookingForm['meta']['duration_max'];$i+=$bookingForm['meta']['duration_step'])
                {
                    if($i==$value)
                    {
                        $find=true;
                        break;
                    }
                }
                
                if(!$find) $this->setErrorLocal($response,CHBSHelper::getFormName('duration_service_type_2',false),__('Enter a valid duration.','chauffeur-booking-system'));
            }
            
            if(count($bookingForm['meta']['transfer_type_enable_'.$data['service_type_id']]))
            {
                if(!$TransferType->isTransferType($data['transfer_type_service_type_'.$data['service_type_id']]))
                    $this->setErrorLocal($response,CHBSHelper::getFormName('transfer_type_service_type_3',false),__('Select a valid transfer type.','chauffeur-booking-system'));
                else 
                {
                    if($data['transfer_type_service_type_'.$data['service_type_id']]===3)
                    {


                    }
                }
            }
            
            if((CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult')) || CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children'))
            {
                $sum=0;
                
                if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
                {
                    if(!$Validation->isNumber($data['passenger_adult_service_type_'.$data['service_type_id']],0,99))
                    {
                        $this->setErrorLocal($response,CHBSHelper::getFormName('passenger_adult_service_type_'.$data['service_type_id'],false),__('Enter a valid number of adult passengers.','chauffeur-booking-system'));
                    }
                    else $sum+=$data['passenger_adult_service_type_'.$data['service_type_id']];
                }
                            
                if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children'))
                {
                    if(!$Validation->isNumber($data['passenger_children_service_type_'.$data['service_type_id']],0,99))
                    {
                        $this->setErrorLocal($response,CHBSHelper::getFormName('passenger_children_service_type_'.$data['service_type_id'],false),__('Enter a valid number of children passengers.','chauffeur-booking-system'));
                    }
                    else $sum+=$data['passenger_children_service_type_'.$data['service_type_id']];
                }                
                
                if($sum===0)
                {
                    if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
                        $this->setErrorLocal($response,CHBSHelper::getFormName('passenger_adult_service_type_'.$data['service_type_id'],false),__('Enter a valid number of adult passengers.','chauffeur-booking-system'));
                    if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
                        $this->setErrorLocal($response,CHBSHelper::getFormName('passenger_children_service_type_'.$data['service_type_id'],false),__('Enter a valid number of children passengers.','chauffeur-booking-system'));
                }
            }
            
            if(in_array($data['service_type_id'],array(1,3)))
            {
                if($bookingForm['meta']['extra_time_enable']==1)
                {
                    $find=false;
                    $value=$data['extra_time_service_type_'.$data['service_type_id']];
                    
                    for($i=$bookingForm['meta']['extra_time_range_min'];$i<=$bookingForm['meta']['extra_time_range_max'];$i+=$bookingForm['meta']['extra_time_step'])
                    {
                        if($i==$value)
                        {
                            $find=true;
                            break;
                        }                        
                    }
                    
                    if(!$find) $this->setErrorLocal($response,CHBSHelper::getFormName('extra_time_service_type_'.$data['service_type_id'],false),__('Select a valid extra time value.','chauffeur-booking-system'));
                }
            }
            
            if(!isset($response['error']))
            {
                if(in_array($data['service_type_id'],array(1)))
                {
                    $distanceSum=$data['distance_sum'];
                    $distanceMinimum=$bookingForm['meta']['distance_minimum'];
                    
                    if(CHBSOption::getOption('length_unit')==2)
                    {
                        $distanceSum=round($Length->convertUnit($distanceSum,1,2),1);
                        $distanceMinimum=round($Length->convertUnit($bookingForm['meta']['distance_minimum'],1,2),1);
                    }
                    
                    if($distanceMinimum>=$distanceSum)
                    {
                        if(CHBSOption::getOption('length_unit')==2)
                            $this->setErrorGlobal($response,sprintf(__('Distance cannot to be lower than %s miles.','chauffeur-booking-system'),$distanceMinimum));
                        else $this->setErrorGlobal($response,sprintf(__('Distance cannot to be lower than %s kilometers.','chauffeur-booking-system'),$distanceMinimum));
                    }
                }
                
                if(in_array($data['service_type_id'],array(1,2)))
                {
                    $durationSum=$data['duration_sum'];
                    $durationMinimum=$bookingForm['meta']['duration_minimum'];                    
                    
                    if($durationMinimum>=$durationSum)
                        $this->setErrorGlobal($response,sprintf(__('Duration cannot to be lower than %s minutes.','chauffeur-booking-system'),$durationMinimum));
                }
            }
            
            if(isset($response['error']))
            {
                $response['step']=1;
                $this->createFormResponse($response);
            }
        }        
        
        /***/
                        
        if($data['step_request']>2)
        {
            $error=false;
            
            if(!array_key_exists($data['vehicle_id'],$bookingForm['dictionary']['vehicle']))
            {
                $error=true;
				$data['step']=2;
				$data['step_request']=2;
                $this->setErrorGlobal($response,__('Select a vehicle.','chauffeur-booking-system'));
            }
            
            if(!$error)
            {
                foreach($bookingForm['dictionary']['booking_extra'] as $index=>$value)
                {
                    if((int)$value['meta']['mandatory']===1)
                    {
                        if((in_array(-1,$value['meta']['vehicle_id'])) || (in_array($data['vehicle_id'],$value['meta']['vehicle_id'])))
                        {
                            if((int)$data['booking_extra_value'][$index]===-1)
                            {
                                $error=true;
                                $this->setErrorGlobal($response,__('Select all booking extra marked as required (*).','chauffeur-booking-system'));
                                break;
                            }
                        }
                    }
                }
            }
            
            if(!$error)
            {
                $passengerSum=0;
                if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult')) {
                    $passengerSum += $data['passenger_adult'];
                }
                if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children')) {
                    $passengerSum += $data['passenger_children'];
                }

                // *rule
                $argument=array
                (
                    'booking_form_id'                                           =>  (int)$data['booking_form_id'],
                    'service_type_id'                                           =>  (int)$data['service_type_id'],
                    'transfer_type_id'                                          =>  $data['transfer_type_service_type_'.$data['service_type_id']],
                    'pickup_location_coordinate'                                =>  $data['pickup_location_coordinate_service_type_'.$data['service_type_id']],
                    'dropoff_location_coordinate'                               =>  $data['dropoff_location_coordinate_service_type_'.$data['service_type_id']],
                    'fixed_location_pickup'                                     =>  (int)$data['fixed_location_pickup_service_type_'.$data['service_type_id']],
                    'fixed_location_dropoff'                                    =>  (int)$data['fixed_location_dropoff_service_type_'.$data['service_type_id']],
                    'route_id'                                                  =>  (int)$data['route_service_type_3'],
                    'vehicle_id'                                                =>  (int)$data['vehicle_id'],
                    'pickup_date'                                               =>  $data['pickup_date_service_type_'.$data['service_type_id']],
                    'pickup_time'                                               =>  $data['pickup_time_service_type_'.$data['service_type_id']],
                    'base_location_distance'                                    =>  CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id']),
                    'base_location_return_distance'                             =>  CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],true),                    
                    'distance'                                                  =>  $data['distance_map'],
                    'distance_sum'                                              =>  $data['distance_sum'],
                    'duration'                                                  =>  in_array($data['service_type_id'],array(1,3)) ? 0 : $data['duration_service_type_2']*60,
                    'duration_map'                                              =>  $data['duration_map'],
                    'duration_sum'                                              =>  in_array($data['service_type_id'],array(1,3)) ? $data['duration_sum'] : $data['duration_service_type_2']*60,
                    'passenger_sum'                                             =>  $passengerSum
                );

                $PriceRule=new CHBSPriceRule();
                
                $priceRule=$PriceRule->getPriceFromRule($argument,$bookingForm);
   
                if((int)$priceRule['calculation_on_request_enable']===1)
                {
                    $error=true;
                    $this->setErrorGlobal($response,__('Select a vehicle.','chauffeur-booking-system'));                        
                }
            }
            
            if(!$error)
            {
                if(($bookingForm['meta']['order_value_minimum']>0) && ((int)$bookingForm['meta']['price_hide']===0))
                {
                    $Booking=new CHBSBooking();

                    $data['booking_form']=$bookingForm;

                    if(($price=$Booking->calculatePrice($data,null,false,true))!==false)      
                    {
                        $orderValueMinimum=number_format($bookingForm['meta']['order_value_minimum']*CHBSCurrency::getExchangeRate(),2,'.','');
                        if($orderValueMinimum>$price['total']['sum']['gross']['value'])
                        {
                            $this->setErrorGlobal($response,sprintf(__('Minimum value of order is %s.','chauffeur-booking-system'),CHBSPrice::format($orderValueMinimum,CHBSCurrency::getFormCurrency())));
                        }
                    }
                }
            }
            
            if(isset($response['error'])) $response['step']=2;
        }

        if($data['step_request']>3 && $data['transfer_type_service_type_'.$data['service_type_id']] == 3 )
        {
            $error=false;

            if(!array_key_exists($data['vehicle_id_return'],$bookingForm['dictionary']['vehicle']))
            {
                $error=true;
                $data['step']=3;
                $data['step_request']=3;
                $this->setErrorGlobal($response,__('Select a vehicle.','chauffeur-booking-system'));
            }

            if(!$error)
            {
                foreach($bookingForm['dictionary']['booking_extra'] as $index=>$value)
                {
                    if((int)$value['meta']['mandatory']===1)
                    {
                        if((in_array(-1,$value['meta']['vehicle_id_return'])) || (in_array($data['vehicle_id_return'],$value['meta']['vehicle_id_return'])))
                        {
                            if((int)$data['booking_extra_value'][$index]===-1)
                            {
                                $error=true;
                                $this->setErrorGlobal($response,__('Select all booking extra marked as required (*).','chauffeur-booking-system'));
                                break;
                            }
                        }
                    }
                }
            }

            if(!$error)
            {
                $passengerSum=0;
                if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult')) {
                    $passengerSum += $data['passenger_adult'];
                }
                if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children')) {
                    $passengerSum += $data['passenger_children'];
                }

                // *rule
                $argument=array
                (
                    'booking_form_id'                                           =>  (int)$data['booking_form_id'],
                    'service_type_id'                                           =>  (int)$data['service_type_id'],
                    'transfer_type_id'                                          =>  $data['transfer_type_service_type_'.$data['service_type_id']],
                    'pickup_location_coordinate'                                =>  $data['pickup_location_coordinate_service_type_'.$data['service_type_id']],
                    'dropoff_location_coordinate'                               =>  $data['dropoff_location_coordinate_service_type_'.$data['service_type_id']],
                    'fixed_location_pickup'                                     =>  (int)$data['fixed_location_pickup_service_type_'.$data['service_type_id']],
                    'fixed_location_dropoff'                                    =>  (int)$data['fixed_location_dropoff_service_type_'.$data['service_type_id']],
                    'route_id'                                                  =>  (int)$data['route_service_type_3'],
                    'vehicle_id'                                                =>  (int)$data['vehicle_id_return'],
                    'pickup_date'                                               =>  $data['pickup_date_service_type_'.$data['service_type_id']],
                    'pickup_time'                                               =>  $data['pickup_time_service_type_'.$data['service_type_id']],
                    'base_location_distance'                                    =>  CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id']),
                    'base_location_return_distance'                             =>  CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],true),
                    'distance'                                                  =>  $data['distance_map'],
                    'distance_sum'                                              =>  $data['distance_sum'],
                    'duration'                                                  =>  in_array($data['service_type_id'],array(1,3)) ? 0 : $data['duration_service_type_2']*60,
                    'duration_map'                                              =>  $data['duration_map'],
                    'duration_sum'                                              =>  in_array($data['service_type_id'],array(1,3)) ? $data['duration_sum'] : $data['duration_service_type_2']*60,
                    'passenger_sum'                                             =>  $passengerSum
                );

                $PriceRule=new CHBSPriceRule();

                $priceRule=$PriceRule->getPriceFromRule($argument,$bookingForm);

                if((int)$priceRule['calculation_on_request_enable']===1)
                {
                    $error=true;
                    $this->setErrorGlobal($response,__('Select a vehicle.','chauffeur-booking-system'));
                }
            }

            if(!$error)
            {
                if(($bookingForm['meta']['order_value_minimum']>0) && ((int)$bookingForm['meta']['price_hide']===0))
                {
                    $Booking=new CHBSBooking();

                    $data['booking_form']=$bookingForm;

                    if(($price=$Booking->calculatePrice($data,null,false,true))!==false)
                    {
                        $orderValueMinimum=number_format($bookingForm['meta']['order_value_minimum']*CHBSCurrency::getExchangeRate(),2,'.','');
                        if($orderValueMinimum>$price['total']['sum']['gross']['value'])
                        {
                            $this->setErrorGlobal($response,sprintf(__('Minimum value of order is %s.','chauffeur-booking-system'),CHBSPrice::format($orderValueMinimum,CHBSCurrency::getFormCurrency())));
                        }
                    }
                }
            }

            if(isset($response['error'])) $response['step']=3;
        }
         
        /***/

        if($data['step_request'] > 4){
            if(empty($data['form_element_field_arrival_airline'])) {
                $this->setErrorLocal($response, CHBSHelper::getFormName('form_element_field_arrival_airline', false), __('Enter arrival airline.', 'chauffeur-booking-system'));
            }
            if(empty($data['form_element_field_arrival_flight'])) {
                $this->setErrorLocal($response, CHBSHelper::getFormName('form_element_field_arrival_flight', false), __('Enter arrival flight number.', 'chauffeur-booking-system'));
            }
            if($data['transfer_type_service_type_'.$data['service_type_id']] === '3') {
                if (empty($data['form_element_field_departure_airline'])) {
                    $this->setErrorLocal($response, CHBSHelper::getFormName('form_element_field_departure_airline', false), __('Enter departure airline.', 'chauffeur-booking-system'));
                }
                if (empty($data['form_element_field_departure_flight'])) {
                    $this->setErrorLocal($response, CHBSHelper::getFormName('form_element_field_departure_flight', false), __('Enter departure flight number.', 'chauffeur-booking-system'));
                }
            }

            if(isset($response['error'])) {
                $response['step'] = 4;
            }
        }
        
        /***/



        if(!isset($response['error']))
        {
            if($data['step_request']>4)
            {
                if(isset($response['error']))
                {
                    $response['step']=4;
                }
                else {
                    $response['step']=5;
                    $response['payment_id']=-1;
                    $response['thank_you_page_enable'] = false;
                    $response['cart_url'] = wc_get_cart_url();
                    $booking = new CHBSBooking();
                    $booking->sendBooking($data,$bookingForm);
                }
            }
        }
                        
        /***/
        /***/

        if($data['step_request']==2) {
            $vehicleHtml=$this->vehicleFilter(false);
            
            if($Validation->isNotEmpty($vehicleHtml))
            {
                $response['route_info'] =  $this->getRouteInfo('2');
                $response['vehicle']=$vehicleHtml;
                $response['vehicle_passenger_filter_field']=$this->createVehiclePassengerFilterField($bookingForm['vehicle_passenger_count_range']['min'],$bookingForm['vehicle_passenger_count_range']['max']);
            }
            else 
            {
                $response['step']=1;
                $this->setErrorGlobal($response,__('There are no vehicles which match your filter criteria.','chauffeur-booking-system'));
                $this->createFormResponse($response);
            }

            $response['booking_extra']=$this->createBookingFormExtra($bookingForm, $data, '1');
        }

        if($data['step_request']==3)
        {
            $vehicleHtml=$this->vehicleFilter(false);

            if($Validation->isNotEmpty($vehicleHtml))
            {
                $response['route_info'] = $this->getRouteInfo('3');
                $response['vehicle']=$vehicleHtml;
                $response['vehicle_passenger_filter_field']=$this->createVehiclePassengerFilterField($bookingForm['vehicle_passenger_count_range']['min'],$bookingForm['vehicle_passenger_count_range']['max']);
            }
            else
            {
                $response['step']=2;
                $this->setErrorGlobal($response,__('There are no vehicles which match your filter criteria.','chauffeur-booking-system'));
                $this->createFormResponse($response);
            }

            $response['booking_extra']=$this->createBookingFormExtra($bookingForm, $data, '3');
        }

        if($data['step_request']==4)
        {
            $response['booking_information'] = $this->createClientFormInformation($bookingForm);
        }
        
        /***/
        
        if(!isset($response['error']))
        {
            $response['step']=$data['step_request'];
            $data['step']=$response['step'];
        }
        else $data['step_request']=$data['step'];
		
        $response['summary']=$this->createSummary($data,$bookingForm);
        
        $this->createFormResponse($response);
        
        /***/
    }

	/**************************************************************************/

    function getDatePeriod($data,$bookingForm,$type,$delta)
    {
        $date=array();
        
        if((int)$bookingForm['meta']['booking_period_type']===1)
        {
            $date[0]=$data[$type.'_date_service_type_'.$data['service_type_id']];
            $date[1]=date_i18n('d-m-Y',CHBSDate::strtotime('+'.$delta.' days'));
        }
        elseif((int)$bookingForm['meta']['booking_period_type']===2)
        {
            $date[0]=$data[$type.'_date_service_type_'.$data['service_type_id']].' '.$data[$type.'_time_service_type_'.$data['service_type_id']];;
            $date[1]=date_i18n('d-m-Y H:i',CHBSDate::strtotime('+'.$delta.' hours'));                            
        }
        elseif((int)$bookingForm['meta']['booking_period_type']===3)
        {
            $date[0]=$data[$type.'_date_service_type_'.$data['service_type_id']].' '.$data[$type.'_time_service_type_'.$data['service_type_id']];;
            $date[1]=date_i18n('d-m-Y H:i',CHBSDate::strtotime('+'.$delta.' minutes'));                            
        } 
        
        return($date);
    }

    /**************************************************************************/
    
    function setErrorLocal(&$response,$field,$message)
    {
        if(!isset($response['error']))
        {
            $response['error']['local']=array();
            $response['error']['global']=array();
        }
        
        array_push($response['error']['local'],array('field'=>$field,'message'=>$message));
    }
    
    /**************************************************************************/
    
    function setErrorGlobal(&$response,$message)
    {
        if(!isset($response['error']))
        {
            $response['error']['local']=array();
            $response['error']['global']=array();
        }
        
        array_push($response['error']['global'],array('message'=>$message));
    }
    
    /**************************************************************************/
    
    function createFormResponse($response)
    {
        echo json_encode($response);
        exit();        
    }
    
    /**************************************************************************/
    
    function createSummaryPriceElementAjax($bid=false)
    {
        $Date=new CHBSDate();
        
        $response=array();
        
        $data=CHBSHelper::getPostOption();
        
        $serviceTypeId=$data['service_type_id'];
        
        $data['pickup_date_service_type_'.$serviceTypeId]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
        $data['pickup_time_service_type_'.$serviceTypeId]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);  
        
        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            $response['html']=null;
            $this->createFormResponse($response);
        }
		
		CHBSBookingHelper::getPriceType($data['booking_form'],$priceType,$sumType,$showTax,$data['step']);
        
        $price=array();
        
        $response['html']=$this->createSummaryPriceElement($data,$bookingForm,$price);
		
		if($bid)
		{
			if(array_key_exists('bid_min',$price['other']))
			{
				$response['bid_vehicle_min_value']=$price['other']['bid_min'];
				$response['bid_vehicle_min_format']=CHBSPrice::format($response['bid_vehicle_min_value'],CHBSCurrency::getFormCurrency());
				$response['bid_question']=sprintf(__('A minimum price to use is %s. Would like to continue with this value?','chauffeur-booking-system'),$response['bid_vehicle_min_format']);
			}	
			else if(array_key_exists('bid_value',$price['other']))
			{
				$response['bid_notice']=__('Your BID amount accepted.','chauffeur-booking-system');
			}
		}
		
        $this->createFormResponse($response);
    }
    
    /**************************************************************************/
    
    function createSummaryPriceElement($data,$bookingForm,&$price, $extra_price=0)
    {
        if((int)$bookingForm['meta']['price_hide']===1)
        {
            return(null);
        }

        $html = '';
        $Booking=new CHBSBooking();
        $vehicle= new CHBSVehicle();

        $data['booking_form']=$bookingForm;

        if(($price=$Booking->calculatePrice($data,null,$data['booking_form']['meta']['booking_summary_hide_fee'],false))===false) {
            return(null);
        }

        CHBSBookingHelper::getPriceType($data['booking_form'],$priceType,$sumType,$showTax,$data['step']);

        if((int)$data['booking_form']['meta']['booking_summary_hide_fee']===0)
        {
            if($price['initial_total']['sum'][$priceType]['value']!=0)
            {
                $html.=
                '
                    <div class="chbs-summary-price-element-deliver-fee">
                        <span>'.__('Initial fee','chauffeur-booking-system').'</span>
                        <span>'.$price['initial_total']['sum'][$priceType]['format'].'</span>
                    </div>
                ';
            }
            if($price['delivery']['sum'][$priceType]['value']!=0)
            {
                $html.=
                '
                    <div class="chbs-summary-price-element-deliver-fee">
                        <span>'.__('Delivery fee','chauffeur-booking-system').'</span>
                        <span>'.$price['delivery']['sum'][$priceType]['format'].'</span>
                    </div>
                ';
            }
            if($price['delivery_return']['sum'][$priceType]['value']!=0)
            {
                $html.=
                '
                    <div class="chbs-summary-price-element-deliver-fee">
                        <span>'.__('Return to base fee','chauffeur-booking-system').'</span>
                        <span>'.$price['delivery_return']['sum'][$priceType]['format'].'</span>
                    </div>
                ';
            }
            if($price['extra_time']['sum'][$priceType]['value']!=0)
            {
                $html.=
                '
                    <div class="chbs-summary-price-element-time-extra">
                        <span>'.__('Extra time','chauffeur-booking-system').'</span>
                        <span>'.$price['extra_time']['sum'][$priceType]['format'].'</span>
                    </div>
                ';
            }
        }

        if($price['vehicle_grouped']['sum'][$priceType]['value']!=0)
        {
            $html .=
                '
                    <div class="chbs-summary-price-element-vehicle-fee">
                        <span>' . __('Selected vehicle', 'chauffeur-booking-system') . '</span>
                        <span>' . $price['vehicle_grouped']['sum'][$priceType]['format'] . '</span>
                    </div>
                ';
        }

        if($extra_price!=0)
        {        
            $html.=
            '
                <div class="chbs-summary-price-element-booking-extra">
                    <span>'.__('Extra options','chauffeur-booking-system').'</span>
                    <span>'.$vehicle->getPriceFormatHtml($extra_price).'</span>
                </div>
            ';   
        }
        
        if(($priceType==='net') && ($showTax))
        {
            if($price['tax']['sum']['value']!=0)
            {
                $html.=
                '
                    <div class="chbs-summary-price-element-booking-extra">
                        <span>'.__('Tax','chauffeur-booking-system').'</span>
                        <span>'.$price['tax']['sum']['format'].'</span>
                    </div>
                ';
            }
        }
        
        if($price['gratuity']['value']>0.00)
        {
            $html.=
            '
                <div class="chbs-summary-price-element-booking-extra">
                    <span>'.__('Gratuity','chauffeur-booking-system').'</span>
                    <span>'.$price['gratuity']['format'].'</span>
                </div>
            ';                   
        }

        $_total_price = $price['total']['sum'][$sumType]['value'] + $extra_price;
        $html.=
        '
            <div class="chbs-summary-price-element-total">
                <span>'.__('Total','chauffeur-booking-system').'</span>
                <span>'.$vehicle->getPriceFormatHtml($_total_price).'</span>
            </div>
        ';            
        
//        if(CHBSBookingHelper::isPaymentDepositEnable($data['booking_form']['meta']))
//        {
//            $html.=
//            '
//                <div class="chbs-summary-price-element-pay">
//                    <span>'.sprintf(__('To pay <span>(%s%% deposit)</span>','chauffeur-booking-system'),$bookingForm['meta']['payment_deposit_value']).'</span>
//                    <span>'.$price['pay']['sum']['gross']['format'].'</span>
//                </div>
//            ';
//        }

        $html=
        '
            <div class="chbs-summary-price-element">
                '.$html.'
            </div>
        ';

        return($html);
    }
    
    /**************************************************************************/
    
    function createSummaryAjax($bid=false){

        $Date=new CHBSDate();

        $response=array();

        $data=CHBSHelper::getPostOption();

        $serviceTypeId=$data['service_type_id'];

        $data['pickup_date_service_type_'.$serviceTypeId]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
        $data['pickup_time_service_type_'.$serviceTypeId]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);

        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            $response['html']=null;
            $this->createFormResponse($response);
        }
//        $response['html']=$this->createSummaryPriceElement($data,$bookingForm,$price);

        $response['html'] = $this->createSummary($data,$bookingForm)[0];
        $this->createFormResponse($response);
    }

    function createSummary($data,$bookingForm)
    {
        $response=array();

        $Date=new CHBSDate();
        $Length=new CHBSLength();
//        $Country=new CHBSCountry();
        $TaxRate=new CHBSTaxRate();
        $Duration=new CHBSDuration();
        $Validation=new CHBSValidation();
        $ServiceType=new CHBSServiceType();
        $TransferType=new CHBSTransferType();
//        $BookingExtra=new CHBSBookingExtra();
//        $BookingGratuity=new CHBSBookingGratuity();
        $BookingFormSummary=new CHBSBookingFormSummary();

        $_product_addons = WC_Product_Addons_Helper::get_product_addons( $bookingForm['meta']['booking_source_product'] );

        $product_addons = [];
        foreach($_product_addons as $addon){
            $product_addons[$addon['field_name']] = $addon;
        }

        $serviceType=$ServiceType->getServiceType($data['service_type_id']);

        /***/
        
        $taxRateDictionary=$TaxRate->getDictionary();
   
        /***/
        
        $pickupDate=$Date->formatDateToDisplay($data['pickup_date_service_type_'.$data['service_type_id']]);
        $pickupTime=$Date->formatTimeToDisplay($data['pickup_time_service_type_'.$data['service_type_id']]);
		
        /***/
                
        $bookingExtraHtml=array();
        $extra_price = 0;
        $bookingExtraList = array();
        $_bookingExtra = isset($data['booking_extra_id']) ? $data['booking_extra_id'] : '';
        $bookingExtra = explode(',', $_bookingExtra);
        if(!empty($bookingExtra)){
            foreach($bookingExtra as $value){
                $addon_name = str_replace('chbs_', '', $value);
                $quantity = $data[$addon_name];
                $addon_name = str_replace('_arrival', '', $addon_name);
                $addon = isset($product_addons[$addon_name]) ? $product_addons[$addon_name] : [];
                if(!empty($addon)) {
                    array_push($bookingExtraList, $quantity . ' x ' . $addon['name'] . ' - ' . $addon['price']);
                    $extra_price += $quantity * $addon['price'];
                }
            }
        }

        if($data['transfer_type_service_type_'.$data['service_type_id']] == 3) {
            $bookingExtraReturnList = array();
            $_bookingExtraReturn = isset($data['booking_extra_return_id']) ? $data['booking_extra_return_id'] : '';
            $bookingExtraReturn = explode(',', $_bookingExtraReturn);
            if (!empty($bookingExtraReturn)) {
                foreach ($bookingExtraReturn as $value) {
                    $addon_name = str_replace('chbs_', '', $value);
                    $quantity = $data[$addon_name];
                    $addon_name = str_replace('_return', '', $addon_name);
                    $addon = isset($product_addons[$addon_name]) ? $product_addons[$addon_name] : [];
                    if (!empty($addon)) {
                        array_push($bookingExtraReturnList, $quantity . ' x ' . $addon['name'] . ' - ' . $addon['price']);
                        $extra_price += $quantity * $addon['price'];
                    }
                }
            }
        }

        /***/

        $price=array();
        $priceHtml=$this->createSummaryPriceElement($data,$bookingForm,$price, $extra_price);

        /***/
        
        $routeHtml=array(null,null);

        if(in_array($data['service_type_id'],array(1,2)))
        {
            $waypointLocationHtml=null;
            
            if($data['service_type_id']==1)
                $routeHtml['label']=__('From - To','chauffeur-booking-system');
            else
                $routeHtml['label']=__('Pickup location','chauffeur-booking-system');
            
            if(count($bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']]))
            {
                $pickupLocationId=$data['fixed_location_pickup_service_type_'.$data['service_type_id']];
                $pickupLocationHtml=$bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']][$pickupLocationId]['formatted_address'];                
            }
            else
            {
                $pickupLocation=json_decode(stripslashes($data['pickup_location_coordinate_service_type_'.$data['service_type_id']]));
                $pickupLocationHtml=$pickupLocation->{'formatted_address'};
                                
                if(($data['service_type_id']==1) && ($bookingForm['meta']['waypoint_enable']==1))
                {
                    if(is_array($data['waypoint_location_coordinate_service_type_1']))
                    {
                        foreach($data['waypoint_location_coordinate_service_type_1'] as $value)
                        {
                            $waypointLocation=json_decode($value);
                            $waypointLocationHtml.=' - '.$waypointLocation->{'formatted_address'};                       
                        }
                    }
                }
            }
            
            if(count($bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']]))
            {
                $dropoffLocationId=$data['fixed_location_dropoff_service_type_'.$data['service_type_id']];
                $dropoffLocationHtml=$bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']][$dropoffLocationId]['formatted_address'];
            }
            else
            {
                $dropoffLocation=json_decode(stripslashes($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]));
                $dropoffLocationHtml=$dropoffLocation->{'formatted_address'};                   
            }
            
            if($data['service_type_id']==1)
                $routeHtml['value']=$pickupLocationHtml.$waypointLocationHtml.' - '.$dropoffLocationHtml;
            else $routeHtml['value']=$pickupLocationHtml;
        }
        else
        {
            $routeHtml['label']=__('Route','chauffeur-booking-system');
            $routeHtml['value']=$bookingForm['dictionary']['route'][$data['route_service_type_3']]['post']->post_title;            
        }

        /***/
        
        $returnDate=null;
        $returnTime=null;
        $transferType=null;

        if(in_array($data['service_type_id'],array(1,3)))
        {
            if((count($bookingForm['meta']['transfer_type_enable_1'])) || (count($bookingForm['meta']['transfer_type_enable_3'])))
            {
                $transferType=$TransferType->getTransferTypeName($data['transfer_type_service_type_'.$data['service_type_id']]);
                
                if((int)$data['transfer_type_service_type_'.$data['service_type_id']]===3)
                {
                    if($Validation->isNotEmpty($data['return_date_service_type_'.$data['service_type_id']]))
                        $returnDate=$Date->formatDateToDisplay($data['return_date_service_type_'.$data['service_type_id']]);
                    if($Validation->isNotEmpty($data['return_time_service_type_'.$data['service_type_id']]))
                        $returnTime=$Date->formatTimeToDisplay($data['return_time_service_type_'.$data['service_type_id']]); 
                }
            }            
        }

        /***/
        
        $passengerHtml=null;
        if(
            (CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult')) ||
            (CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children'))
        ) {
            $passengerHtml = CHBSBookingHelper::getPassengerLabel($data['passenger_adult_service_type_' . $data['service_type_id']], $data['passenger_children_service_type_' . $data['service_type_id']]);
        }
        
        /***/
		
        switch($data['step_request'])
        {
            case 2:
            case 3:
            case 4:

                $BookingFormSummary->add
                (
                    array
                    (
                        __('Service type','chauffeur-booking-system'),
                        $serviceType[0]
                    )
                );

                if($Validation->isNotEmpty($transferType))
                {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('Transfer type','chauffeur-booking-system'),
                            $transferType
                        )
                    );
                }

                if(in_array($data['service_type_id'],array(1,2))) {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('From', 'chauffeur-booking-system'),
                            $pickupLocationHtml
                        )
                    );
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('To', 'chauffeur-booking-system'),
                            $dropoffLocationHtml
                        )
                    );
                }else{
                    $BookingFormSummary->add
                    (
                        array
                        (
                            $routeHtml['label'],
                            $routeHtml['value']
                        )
                    );
                }

                if($data['service_type_id']==2)
                {
                    if($Validation->isNotEmpty($dropoffLocationHtml))
                    {
                        $BookingFormSummary->add
                        (
                            array
                            (
                                __('Dropoff location','chauffeur-booking-system'),
                                $dropoffLocationHtml
                            )
                        );
                    }
                }

                $BookingFormSummary->add
                (
                    array
                    (
                        __('Pickup date, time','chauffeur-booking-system'),
                        $pickupDate.', '.$pickupTime
                    )
                );

                if($data['transfer_type_service_type_'.$data['service_type_id']] == 3 && ($Validation->isNotEmpty($returnDate)) && ($Validation->isNotEmpty($returnTime)))
                {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('Return date, time','chauffeur-booking-system'),
                            $returnDate.', '.$returnTime
                        )
                    );
                }

                if(($bookingForm['meta']['extra_time_enable']==1) && (in_array($data['service_type_id'],array(1,3))))
                {
                    $value=$data['extra_time_service_type_'.$data['service_type_id']];

                    if($value>0)
                    {
                        $BookingFormSummary->add
                        (
                            array
                            (
                                __('Extra time','chauffeur-booking-system'),
                                sprintf(((int)$bookingForm['meta']['extra_time_unit']===1 ? __('%s minutes','chauffeur-booking-system') : __('%s hours','chauffeur-booking-system')),$value)
                            )
                        );
                    }
                }


                $distance = (in_array($data['service_type_id'],array(1,3))) ? $data['distance_sum'] * 2 : $data['distance_sum'];
                $duration = (in_array($data['service_type_id'],array(1,3))) ? $data['duration_sum'] * 2 : $data['duration_sum'];
                $BookingFormSummary->add
                (
                    array
                    (
                        array
                        (
                            __('Total distance','chauffeur-booking-system'),
                            $Length->format($distance)
                        ),
                        array
                        (
                            __('Total time','chauffeur-booking-system'),
                            $Duration->format($duration)
                        ),
                    ),
                    2
                );

                if(isset($data['vehicle_id'])) {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('Arrival Vehicle', 'chauffeur-booking-system'),
                            $bookingForm['dictionary']['vehicle'][$data['vehicle_id']]['post']->post_title
                        )
                    );
                }
                if (count($bookingExtraList)) {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('Arrival Extra options', 'chauffeur-booking-system'),
                            $bookingExtraList
                        ),
                        3
                    );
                }

                if($data['transfer_type_service_type_'.$data['service_type_id']] == 3 && $data['vehicle_id_return'] !== "-1") {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('Return Vehicle', 'chauffeur-booking-system'),
                            $bookingForm['dictionary']['vehicle'][$data['vehicle_id_return']]['post']->post_title //todo get return vehicle data
                        )
                    );
                }

                if (count($bookingExtraReturnList)) {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('Return Extra options', 'chauffeur-booking-system'),
                            $bookingExtraReturnList
                        ),
                        3
                    );
                }


                if($Validation->isNotEmpty($passengerHtml))
                {
                    $BookingFormSummary->add
                    (
                        array
                        (
                            __('Passengers','chauffeur-booking-system'),
                            $passengerHtml
                        )
                    );
                }

                $response[0]=$BookingFormSummary->create(__('Summary','chauffeur-booking-system')).$priceHtml;
            break;
        }
        
        return($response);
    }
    
    /**************************************************************************/ 
    
    function createVehicle($data,&$priceToSort)
    {
        $html=array(null);
        
        $Vehicle=new CHBSVehicle();
        $Validation=new CHBSValidation();
        
        /***/
        
        $thumbnail=get_the_post_thumbnail_url($data['vehicle_id'],PLUGIN_CHBS_CONTEXT.'_vehicle');
        if($thumbnail!==false)
        {
            $htmlGallery=null;
            
            $galleryImageUrl=array();
            
            foreach($data['vehicle']['meta']['gallery_image_id'] as $value)
            {
                $url=wp_get_attachment_image_src($value,'full');
                if($url!==false) array_push($galleryImageUrl,$url[0]);
            }
            
            if(count($galleryImageUrl))
            {
                foreach($galleryImageUrl as $galleryImageUrlValue)
                    $htmlGallery.='<li><img src="'.esc_url($galleryImageUrlValue).'"></li>';
                
                $htmlGallery='<div class="chbs-vehicle-gallery"><ul>'.$htmlGallery.'</ul></div>';
            }
            
            /***/
            
            $alt=null;
            $class=array('chbs-vehicle-image');
            
            if($Validation->isNotEmpty($htmlGallery))
            {
                $alt=__('Click to open vehicle gallery.','chauffeur-booking-system');
                array_push($class,'chbs-vehicle-image-has-gallery');
            }
            
            $html[0]='<div'.CHBSHelper::createCSSClassAttribute($class).'><img src="'.esc_url($thumbnail).'" alt="'.esc_attr($alt).'"/></div>'.$htmlGallery;
        }
            
        /***/

        // *rule
        $argument=array
        (
            'booking_form_id'                                                   =>  $data['booking_form_id'],
            'service_type_id'                                                   =>  $data['service_type_id'],
            'transfer_type_id'                                                  =>  $data['transfer_type_id'],
            'pickup_location_coordinate'                                        =>  $data['pickup_location_coordinate'],
            'dropoff_location_coordinate'                                       =>  $data['dropoff_location_coordinate'],
            'fixed_location_pickup'                                             =>  $data['fixed_location_pickup'],
            'fixed_location_dropoff'                                            =>  $data['fixed_location_dropoff'],
            'transfer_type_id'                                                  =>  $data['transfer_type_id'],
            'route_id'                                                          =>  $data['route_id'],
            'vehicle_id'                                                        =>  $data['vehicle_id'],
            'pickup_date'                                                       =>  $data['pickup_date'],
            'pickup_time'                                                       =>  $data['pickup_time'],
            'passenger_adult'                                                   =>  $data['passenger_adult'],
            'passenger_children'                                                =>  $data['passenger_children'],
            'base_location_distance'                                            =>  $data['base_location_distance'],
            'base_location_return_distance'                                     =>  $data['base_location_return_distance'],
            'distance'                                                          =>  $data['distance'],
            'distance_sum'                                                      =>  $data['distance_sum'],
            'duration'                                                          =>  $data['duration'],
            'duration_map'                                                      =>  $data['duration_map'],
            'duration_sum'                                                      =>  $data['duration_sum'],
            'booking_form'                                                      =>  $data['booking_form']
        );

        $price=$Vehicle->calculatePrice($argument,true,true,array('enable'=>0));

        /***/
        
        $htmlDescription=null;
        
        if(CHBSPlugin::isAutoRideTheme())
        {
            if($Validation->isNotEmpty($data['vehicle']['meta']['description']))
                $htmlDescription='<p>'.$data['vehicle']['meta']['description'].'</p>';            
        }
        else
        {
            if($Validation->isNotEmpty($data['vehicle']['post']->post_content))
                $htmlDescription='<p>'.$data['vehicle']['post']->post_content.'</p>';
        }

        if((array_key_exists('attribute',$data['vehicle'])) && (is_array($data['vehicle']['attribute'])))
        {
            $i=0;
            $htmlAttribute=array(null,null);
            $count=ceil(count($data['vehicle']['attribute'])/2);
            
            foreach($data['vehicle']['attribute'] as $value)
            {
                $index=($i++)<$count ? 0 : 1;
                $htmlAttribute[$index].=
                '
                    <li class="chbs-clear-fix">
                        <div>'.esc_html($value['name']).'</div>
                        <div>'.esc_html($value['value']).'</div>
                    </li>
                ';
            }
            
            if($Validation->isNotEmpty($htmlAttribute[0]))
                $htmlAttribute[0]='<ul class="chbs-list-reset">'.$htmlAttribute[0].'</ul>';
            if($Validation->isNotEmpty($htmlAttribute[1]))
                $htmlAttribute[1]='<ul class="chbs-list-reset">'.$htmlAttribute[1].'</ul>';                
            
            $htmlDescription.=
            '
                <div class="chbs-vehicle-content-description-attribute chbs-clear-fix">
                    '.$htmlAttribute[0].'
                    '.$htmlAttribute[1].'    
                </div>
            ';
        }
        
        if($Validation->isNotEmpty($htmlDescription))
        {
            $classDescription=array('chbs-vehicle-content-description');
            if((int)$data['booking_form']['meta']['vehicle_more_info_default_show']===1)
                array_push($classDescription,'chbs-state-open');
            
            $htmlDescription='<div'.CHBSHelper::createCSSClassAttribute($classDescription).'><div>'.$htmlDescription.'</div></div>';
        }
        
        /****/
        
        $htmlMoreInfo=null;
        if($Validation->isNotEmpty($htmlDescription))
        {
            $htmlMoreInfo = '';
            /*$htmlMoreInfo=
            '
                <div class="chbs-vehicle-content-meta-button">
                    <a href="#" class="'.((int)$data['booking_form']['meta']['vehicle_more_info_default_show']===1 ? 'chbs-state-selected' : '').'">
                        <span class="chbs-circle chbs-meta-icon-arrow-vertical-small"></span>
                        <span>'.esc_html__('More info','chauffeur-booking-system').'</span>
                        <span>'.esc_html__('Less info','chauffeur-booking-system').'</span>
                    </a> 
                </div>
            ';*/
        }

        /***/
        
        $htmlPrice=null;
        if(((int)$data['booking_form']['meta']['price_hide']===0) && ((int)$price['price']['base']['calculation_on_request_enable']===0))
        {
            CHBSBookingHelper::getPriceType($data['booking_form'],$priceType,$sumType,$taxShow,$data['step']);
            
            $priceToDisplay=$price['price']['sum'][$priceType]['formatHtml'];
                        
            if((int)$data['booking_form']['meta']['show_price_per_single_passenger']===1)
            {
                if(CHBSBookingHelper::isPassengerEnable($data['booking_form']['meta'],$data['service_type_id'],-1))
                {
                    if($price['price']['other'][$priceType]['price_passenger_children']['value']!=$price['price']['other'][$priceType]['price_passenger_adult']['value'])
                    {
                        $priceToDisplay=$price['price']['other'][$priceType]['price_passenger_children']['formatHtml'].' - '.$price['price']['other'][$priceType]['price_passenger_adult']['formatHtml'];
                    }
                    else $priceToDisplay=$price['price']['other'][$priceType]['price_passenger_adult']['formatHtml'];
                }
                else if(CHBSBookingHelper::isPassengerEnable($data['booking_form']['meta'],$data['service_type_id'],'adult'))
                {
                    $priceToDisplay=$price['price']['other'][$priceType]['price_passenger_adult']['formatHtml'];
                }
                else if(CHBSBookingHelper::isPassengerEnable($data['booking_form']['meta'],$data['service_type_id'],'children'))
                {
                    $priceToDisplay=$price['price']['other'][$priceType]['price_passenger_children']['formatHtml'];
                }
            }
            
            $htmlPrice=
            '
                <div class="chbs-vehicle-content-price">
                    <span>
                        <span>'.$priceToDisplay.'</span>
                    </span>
                </div>  
            ';
			
			if(CHBSBookingHelper::isVehicleBidPriceEnable($data['booking_form']))
			{
				$option=CHBSHelper::getPostOption();
				
				$class=array(array(),array());
				
				$class[0]=array('chbs-vehicle-content-price-bid');
				$class[1]=array('chbs-hidden');
				$class[2]=array('chbs-hidden');
				
				$value=null;
				
				if(is_array($option['vehicle_bid_price']))
				{
					if(array_key_exists((int)$data['vehicle_id'],$option['vehicle_bid_price']))
						$value=$option['vehicle_bid_price'][(int)$data['vehicle_id']];
				}
					
				if($data['vehicle_selected_id']==$data['vehicle_id'])
				{
					if($Validation->isPrice($value)) unset($class[2][0]);
					else unset($class[1][0]);
				}
						
				$htmlPrice.=
				'
					<div'.CHBSHelper::createCSSClassAttribute($class[0]).'>
						<div'.CHBSHelper::createCSSClassAttribute($class[1]).'>
							<a href="#" class="chbs-button chbs-button-style-3">'.esc_html('Bid price','chauffeur-booking-system').'</a>
						</div>
						<div'.CHBSHelper::createCSSClassAttribute($class[2]).'>
							<input type="text" placeholder="'.esc_attr('Enter a price','chauffeur-booking-system').'" name="'.CHBSHelper::getFormName('vehicle_bid_price['.(int)$data['vehicle_id'].']',false).'" value="'.esc_attr($value).'">
							<a href="#" class="chbs-button chbs-button-style-3">'.esc_html('Bid','chauffeur-booking-system').'</a>
							<a href="#" class="chbs-button chbs-button-style-3">'.esc_html('Cancel','chauffeur-booking-system').'</a>
						</div>
					</div>
				';
			}
        }        
                
        if((int)$price['price']['base']['calculation_on_request_enable']===1)
        {
            $htmlSelect=
            '
                <a href="'.esc_attr($price['price']['base']['calculation_on_request_redirect_url']).'" target="_blank" class="chbs-button chbs-button-style-2 chbs-button-on-request">
                    '.esc_html__('On Request','chauffeur-booking-system').'
                </a>               
            ';
        }
        else 
        {
            $htmlSelect=
            '
                <a href="#" class="chbs-button chbs-button-style-2 '.($data['vehicle_selected_id']==$data['vehicle_id'] ? 'chbs-state-selected' : null).'">
                    '.esc_html__('Select','chauffeur-booking-system').'
                    <span class="chbs-meta-icon-tick"></span>
                </a>            
            ';
        }
        
        $distance=CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],false,false);
        $returnDistance=CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],true,false);
        
		/***/
		
		$htmlVehicleInfo=null;
		if((int)$data['booking_form']['meta']['passenger_number_vehicle_list_enable']===1)
		{
			$htmlVehicleInfo.=
            '<span class="chbs-meta-icon-container">
				<span class="chbs-meta-icon-people"></span>
				<span class="chbs-circle">'.$data['vehicle']['meta']['passenger_count'].'</span>				
			</span>';
		}
		if((int)$data['booking_form']['meta']['suitcase_number_vehicle_list_enable']===1)
		{
			$htmlVehicleInfo.=
			'<span class="chbs-meta-icon-container">
				<span class="chbs-meta-icon-bag"></span>
                <span class="chbs-circle">'.$data['vehicle']['meta']['bag_count'].'</span>			
			</span>';
		}
		if($Validation->isNotEmpty($htmlVehicleInfo))
		{
			$htmlVehicleInfo=
			'
				<div class="chbs-vehicle-content-meta-info">
					<div>
						'.$htmlVehicleInfo.'
					</div>
				</div>				
			';
		}
		
		/***/
		
        $html=
        '
            <div class="chbs-vehicle chbs-clear-fix" data-id="'.esc_attr($data['vehicle_id']).'" data-base_location_cooridnate_lat="'.esc_attr($data['vehicle']['meta']['base_location_coordinate_lat']).'"  data-base_location_cooridnate_lng="'.esc_attr($data['vehicle']['meta']['base_location_coordinate_lng']).'">

                '.$html[0].'

                <div class="chbs-vehicle-content">
                
                    <div class="chbs-vehicle-content-header"> 
                        <span>'.get_the_title($data['vehicle_id']).'</span>
                        '.$htmlSelect.'
                    </div>
                    <div class="chbs-vehicle-content-body">
                        '.$htmlPrice.'
                     </div>
                    <div class="chbs-vehicle-bottom-info">
                        <div class="chbs-vehicle-bottom-info-inner">'.$htmlDescription.'</div>
                        <div class="chbs-vehicle-content-meta">
                            <div>
                                '.$htmlMoreInfo.'
                                '.$htmlVehicleInfo.'
                            </div>
                        </div>
                    </div>

                </div>
                
                <input type="hidden" name="'.CHBSHelper::getFormName('base_location_vehicle_distance['.(int)$data['vehicle_id'].']',false).'" value="'.$distance.'"/>
                <input type="hidden" name="'.CHBSHelper::getFormName('base_location_vehicle_return_distance['.(int)$data['vehicle_id'].']',false).'" value="'.$returnDistance.'"/>

            </div>
        ';
        
        $priceToSort=$price['price']['sum']['gross']['value'];
        
        return($html);
    }

    /**************************************************************************/ 
    
    function getVehiclePassengerCountRange($bookingForm)
    {
        $count=array();
        foreach($bookingForm['dictionary']['vehicle'] as $value)
            array_push($count,$value['meta']['passenger_count']);
            
        $count=array('min'=>1,'max'=>max($count));
        
        $data=CHBSHelper::getPostOption();
        
        if(array_key_exists('service_type_id',$data))
        {
            if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id']))
            {
                $sum=CHBSBookingHelper::getPassenegerSum($bookingForm['meta'],$data);

                if($sum>1) $count['min']=$sum;
                if($count['min']>$count['max']) $count['max']=$count['min'];
            }
        }
        
        return($count);
    }
    
     /**************************************************************************/ 
    
    function getVehicleBagCountRange($vehicle)
    {
        $count=array();
        foreach($vehicle as $value)
            array_push($count,$value['meta']['bag_count']);
            
        $count=array('min'=>1,'max'=>max($count));
        
        return($count);      
    }
    
    /**************************************************************************/
    
    function vehicleFilter($ajax=true)
    {           
        if(!is_bool($ajax)) $ajax=true;
        
        $html=null;
        $response=array();
        
        $Date=new CHBSDate();
        $Validation=new CHBSValidation();
        
        $data=CHBSHelper::getPostOption();
        
        $serviceTypeId=$data['service_type_id'];
        
        $data['pickup_date_service_type_'.$serviceTypeId]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
        $data['pickup_time_service_type_'.$serviceTypeId]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);  
        
        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            if(!$ajax) return($html);
            
            $this->setErrorGlobal($response,__('There are no vehicles which match your filter criteria.','chauffeur-booking-system'));
            $this->createFormResponse($response);
        }
        
        $response['booking_summary_hide_fee']=$bookingForm['meta']['booking_summary_hide_fee'];
        
        if(!$Validation->isNumber($data['vehicle_standard'],1,4)) $data['vehicle_standard']=1;
        if(!$Validation->isNumber($data['vehicle_bag_count'],1,99)) $data['vehicle_bag_count']=1;
        if(!$Validation->isNumber($data['vehicle_passenger_count'],1,99)) $data['vehicle_passenger_count']=1;        
        
        $sum=CHBSBookingHelper::getPassenegerSum($bookingForm['meta'],$data);
        
        if($sum>0) 
        {
            if($data['vehicle_passenger_count']<$sum) {
                $data['vehicle_passenger_count'] = $sum;
            }
        }
        
        $attribute=array();
        
        /***/
        
        $meta=$bookingForm['meta'];
        
        $vehicleCategory=$this->getBookingFormVehicleCategory($bookingForm['meta']);
        if($data['vehicle_category']!=0) {
            $attribute = array('category_id' => $data['vehicle_category']);
        }
        
        if(isset($attribute['category_id']))
        {
            if(!array_key_exists($attribute['category_id'],$vehicleCategory))
                $attribute['category_id']=array_keys($vehicleCategory);
            
            $meta['vehicle_category_id']=(array)$attribute['category_id'];
        }
        else
        {
            if(!in_array(-1,$bookingForm['meta']['vehicle_category_id']))
            {
                $attribute['category_id']=array_keys($vehicleCategory);
                $meta['vehicle_category_id']=(array)$attribute['category_id'];
            }
        }
        
        /***/
        
        $dictionary=$this->getBookingFormVehicle($meta);

        $vehicleHtml=array();
        $vehiclePrice=array();

        $PriceRule=new CHBSPriceRule();

        $bookingData=array
        (
            'pickup_location_coordinate'                                    =>  $data['pickup_location_coordinate_service_type_'.$data['service_type_id']],
            'dropoff_location_coordinate'                                   =>  $data['dropoff_location_coordinate_service_type_'.$data['service_type_id']],
        );
        $isset_price_rule = $PriceRule->checkIssetPriceRule($bookingData,$bookingForm);

        $vehicles = array();
        foreach($dictionary as $index=>$value){
            if(!(
                ($value['meta']['passenger_count']>=$data['vehicle_passenger_count']) &&
                ($value['meta']['bag_count']>=$data['vehicle_bag_count']) &&
                ($value['meta']['standard']>=$data['vehicle_standard']))
            ) {
                continue;
            }

            /**
             * filter vehicles with property 'show only for price rules'
             */

            if(!$isset_price_rule && (int) $value['meta']['visibility_type'] === 2){
                continue;
            }

            /**
             * Every vehicle must have type in admin "Vehicle Types"
             */
            if(array_key_exists('type_category', $value) && !empty($value['type_category'])) {
                $cat_key = $value['type_category']->term_id;
                if (!array_key_exists($cat_key, $vehicles)) {
                    $vehicles[$cat_key] = [];
                    $vehicles[$cat_key]['vehicle_pc'] = 0;
                }

                if ($vehicles[$cat_key]['vehicle_pc'] == 0) {
                    $vehicles[$cat_key]['vehicle_pc'] = $value['meta']['passenger_count'];
                    $vehicles[$cat_key]['vehicle'] = $value;
                } else if ($value['meta']['passenger_count'] < $vehicles[$cat_key]['vehicle_pc']) {
                    $vehicles[$cat_key]['vehicle_pc'] = $value['meta']['passenger_count'];
                    $vehicles[$cat_key]['vehicle'] = $value;
                } else if ((int)$value['meta']['passenger_count'] == $vehicles[$cat_key]['vehicle_pc']) {
                    $vehicles[$cat_key]['vehicle'] = $value;
                }
            }
        }

        $vehicle_index = 0;
        foreach($vehicles as $category_type){
            $value = $category_type['vehicle'];
            $argument = array
            (
                'booking_form_id' => $bookingForm['post']->ID,
                'service_type_id' => $data['service_type_id'],
                'transfer_type_id' => $data['transfer_type_service_type_' . $data['service_type_id']],
                'pickup_location_coordinate' => $data['pickup_location_coordinate_service_type_' . $data['service_type_id']],
                'dropoff_location_coordinate' => $data['dropoff_location_coordinate_service_type_' . $data['service_type_id']],
                'fixed_location_pickup' => $data['fixed_location_pickup_service_type_' . $data['service_type_id']],
                'fixed_location_dropoff' => $data['fixed_location_dropoff_service_type_' . $data['service_type_id']],
                'transfer_type_id' => $data['transfer_type_service_type_' . $data['service_type_id']],
                'route_id' => $data['route_service_type_3'],
                'vehicle' => $value,
                'vehicle_id' => $value['post']->ID,
                'vehicle_selected_id' => $data['vehicle_id'],
                'pickup_date' => $data['pickup_date_service_type_' . $data['service_type_id']],
                'pickup_time' => $data['pickup_time_service_type_' . $data['service_type_id']],
                'passenger_adult' => $data['passenger_adult_service_type_' . $data['service_type_id']],
                'passenger_children' => $data['passenger_children_service_type_' . $data['service_type_id']],
                'base_location_distance' => CHBSBookingHelper::getBaseLocationDistance($value['post']->ID),
                'base_location_return_distance' => CHBSBookingHelper::getBaseLocationDistance($value['post']->ID, true),
                'distance' => $data['distance_map'],
                'distance_sum' => $data['distance_sum'],
                'duration' => in_array($data['service_type_id'], array(1, 3)) ? 0 : $data['duration_service_type_2'] * 60,
                'duration_map' => $data['duration_map'],
                'duration_sum' => in_array($data['service_type_id'], array(1, 3)) ? $data['duration_sum'] : $data['duration_service_type_2'] * 60,
                'booking_form' => $bookingForm
            );

            $price = 0;
            $vehicleHtml[$vehicle_index] = $this->createVehicle($argument, $price);
            $vehiclePrice[$vehicle_index] = $price;
            $vehicle_index++;
        }

        if(in_array((int)$bookingForm['meta']['vehicle_sorting_type'],array(1,2)))
        {
            asort($vehiclePrice);         
            if((int)$bookingForm['meta']['vehicle_sorting_type']===2)
                $vehiclePrice=array_reverse($vehiclePrice,true);
        }
        
        /***/
        
        $i=0;
        if($bookingForm['meta']['vehicle_limit']>0)
        {
            foreach($vehiclePrice as $index=>$value)
            {
                if((++$i)>$bookingForm['meta']['vehicle_limit'])
                    unset($vehicleHtml[$index],$vehiclePrice[$index]);
            }
        }
        
        /**/
        
        $i=0;
        $vehiclePerPage=(int)$bookingForm['meta']['vehicle_pagination_vehicle_per_page'];
        
        foreach($vehiclePrice as $index=>$value)
        {
            $class=array();
            
            if($vehiclePerPage>0)
            {
                if(($i++)>=$vehiclePerPage)
                    array_push($class,'chbs-hidden');
            }
            
            $html.='<li'.CHBSHelper::createCSSClassAttribute($class).'>'.$vehicleHtml[$index].'</li>';
        }
        
        $html='<ul class="chbs-list-reset">'.$html.'</ul>';
        
        $html.=$this->createPagination($dictionary,$bookingForm['meta']['vehicle_pagination_vehicle_per_page']);
        
        $response['html']=$html;
        
        if($Validation->isEmpty($html))
        {
            if($ajax)
            {
                $this->setErrorGlobal($response,__('There are no vehicles which match your filter criteria.','chauffeur-booking-system'));
                $this->createFormResponse($response);
            }
        }
        
        if(!$ajax) return($html);
        
        $this->createFormResponse($response);
    }

    /**************************************************************************/

    function getRouteInfo($step = '2'){
        $data=CHBSHelper::getPostOption();

        $main = '';
        $transferDate = '';
        $pickUpLocation = '';
        $dropOffLocation = '';

        if($step === '2'){
            $main = "Arrival";
            $transferDate = $data['pickup_date_service_type_' . $data['service_type_id']];
            $pickUpLocation = $data['pickup_location_service_type_' . $data['service_type_id']];
            $dropOffLocation = $data['dropoff_location_service_type_' . $data['service_type_id']];
        }else{
            $main = "Return";
            $transferDate = $data['return_date_service_type_'. $data['service_type_id']];
            $pickUpLocation = $data['dropoff_location_service_type_' . $data['service_type_id']];
            $dropOffLocation = $data['pickup_location_service_type_' . $data['service_type_id']];
        }

        //var_dump($data);
        $html = '<div class="transfer-step-route-title">';

        $html .= '<span class="transfer-step-route-title-main">'.$main.'</span>';

        $html .= '<div class="small">';
        $html .= '<span class="small-title">From: </span> <span class="transfer-step-route-title-location">'.$pickUpLocation.'</span><br/>';
        $html .= '<span class="small-title">To: </span><span class="transfer-step-route-title-location">'.$dropOffLocation.'</span><br/>';
        $html .= '<span class="small-title">Date: </span><span class="transfer-step-route-title-date">'.date("F j", strtotime($transferDate)).'</span>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;

    }
    
    /**************************************************************************/
    
    function createClientFormSignUp($bookingForm,$userData=array())
    {
//        $User=new CHBSUser();
//        $WooCommerce=new CHBSWooCommerce();
        $BookingFormElement=new CHBSBookingFormElement();
        
        /***/
        
        $data=CHBSHelper::getPostOption();
        if(count($userData)) {
            $data=array_merge($data,$userData);
        }

        /***/
        
        $html=null;
        $htmlElement=array(null,null,null,null,null,null);
        
        foreach($bookingForm['dictionary']['country'] as $index=>$value) {
            $htmlElement[0] .= '<option value="' . esc_attr($index) . '" ' . ($data['client_billing_detail_country_code'] == $index ? 'selected' : null) . '>' . esc_html($value[0]) . '</option>';
        }
        
        $htmlElement[1]=$BookingFormElement->createField(1,$data['service_type_id'],$bookingForm['meta']);
        
        $htmlElement[2]=$BookingFormElement->createField(2,$data['service_type_id'],$bookingForm['meta']);
        
        $panel=$BookingFormElement->getPanel($bookingForm['meta']);
        foreach($panel as $index=>$value)
        {
            if(in_array($value['id'],array(1,2))) continue;
            $htmlElement[3].=$BookingFormElement->createField($value['id'],$data['service_type_id'],$bookingForm['meta']);
        }
        
//        if($WooCommerce->isEnable($bookingForm['meta']))
//        {
//            if(!$User->isSignIn())
//            {
//                if(in_array((int)$bookingForm['meta']['woocommerce_account_enable_type'],array(1,2)))
//                {
//                    $class=array(array('chbs-form-checkbox'),array());
//
//                    if(in_array((int)$bookingForm['meta']['woocommerce_account_enable_type'],array(2)))
//                    {
//
//                    }
//                    else
//                    {
//                        if((int)$data['client_sign_up_enable']===0)
//                        {
//                            array_push($class[1],'chbs-hidden');
//                        }
//                        else
//                        {
//                            array_push($class[0],'chbs-state-selected');
//                        }
//                    }
//
//                    $htmlElement[4].=
//                    '
//                        <div class="chbs-clear-fix">
//                            <label class="chbs-form-label-group">
//                    ';
//
//                    if(in_array((int)$bookingForm['meta']['woocommerce_account_enable_type'],array(2)))
//                    {
//                        $htmlElement[4].=esc_html__('New account','chauffeur-booking-system');
//                    }
//                    else
//                    {
//                        $htmlElement[4].=
//                        '
//                                <span'.CHBSHelper::createCSSClassAttribute($class[0]).'>
//                                    <span class="chbs-meta-icon-tick"></span>
//                                </span>
//                                <input type="hidden" name="'.CHBSHelper::getFormName('client_sign_up_enable',false).'" value="'.esc_attr($data['client_sign_up_enable']).'"/>
//                                '.esc_html__('Create an account?','chauffeur-booking-system').'
//                        ';
//                    }
//
//                    $htmlElement[4].=
//                    '
//                            </label>
//                        </div>
//
//                        <div'.CHBSHelper::createCSSClassAttribute($class[1]).'>
//
//                            <div class="chbs-clear-fix">
//                                <div class="chbs-form-field chbs-form-field-width-33">
//                                    <label>'.esc_html__('Login *','chauffeur-booking-system').'</label>
//                                    <input type="text" name="'.CHBSHelper::getFormName('client_sign_up_login',false).'"/>
//                                </div>
//                                <div class="chbs-form-field chbs-form-field-width-33">
//                                    <label>
//                                        '.esc_html__('Password *','chauffeur-booking-system').'
//                                        &nbsp;
//                                        <a href="#" class="chbs-sign-up-password-generate">'.esc_html__('Generate','chauffeur-booking-system').'</a>
//                                        <a href="#" class="chbs-sign-up-password-show">'.esc_html__('Show','chauffeur-booking-system').'</a>
//                                    </label>
//                                    <input type="password" name="'.CHBSHelper::getFormName('client_sign_up_password',false).'"/>
//                                </div>
//                                <div class="chbs-form-field chbs-form-field-width-33">
//                                    <label>'.esc_html__('Re-type password *','chauffeur-booking-system').'</label>
//                                    <input type="password" name="'.CHBSHelper::getFormName('client_sign_up_password_retype',false).'"/>
//                                </div>
//                            </div>
//
//                        </div>
//                    ';
//                }
//            }
//        }
        
        /***/
        
        $class=array();
        
//        if($WooCommerce->isEnable($bookingForm['meta']))
//        {
//            if(($User->isSignIn()) || ((int)$data['client_account']===1) || ((int)$bookingForm['meta']['woocommerce_account_enable_type']===0)) unset($class[1]);
//        }else {
//            unset($class[1]);
//        }
        $transfer_type = $data['transfer_type_service_type_'.$data['service_type_id']];
        $html=
        '
            <div'.CHBSHelper::createCSSClassAttribute($class).'>

                <div class="chbs-box-shadow">
                    <h4 class="chbs-booking-extra-header">
                        <span class="chbs-circle" style="display: inline-flex;align-items: center; justify-content: center;"><i class="fa fa-plane" style="height: 27px; line-height: 27px;"></i></span>
                        <span>Flight Information</span>
                    </h4>
                        
                    <div class="chbs-clear-fix">
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Arrival Airline *</label>
                            <input type="text" name="chbs_form_element_field_arrival_airline" value="">
                        </div>
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Arrival Flight *</label>
                            <input type="text" name="chbs_form_element_field_arrival_flight" value="">
                        </div>                        
                    </div>';

        if((int)$transfer_type === 3) {
            $html .=
                '
                    <div class="chbs-clear-fix">
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Departure Airline *</label>
                            <input type="text" name="chbs_form_element_field_departure_airline" value="">
                        </div>          
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Departure Flight *</label>
                            <input type="text" name="chbs_form_element_field_departure_flight" value="">
                        </div>                
                    </div>';
        }

        $html .=
            '
                    <h4 class="chbs-booking-extra-header" style="margin-top: 20px;">
                        <span class="chbs-circle"><i class="fa fa-address-book"></i></span>
                        <span>Contact Information</span>
                    </h4>
                    <div class="chbs-clear-fix">
                        <div class="chbs-form-field chbs-form-field-width-100">
                            <label>Best way to contact you:</label>
                            <textarea style="max-height: 160px;" name="chbs_form_element_field_contact_information"></textarea>
                            <p class="field-information">
                                Please provide us with either Phone Number, Email or Facebook page link where we can message you if we need to get in touch with you. Note: you may not have
                                phone service in international location, however you may have WiFi, therefore it is recommend to use Facebook messenger to contact us. 
                            </p>
                        </div>                
                    </div>   
        ';
//        $html=
//        '
//            <div'.CHBSHelper::createCSSClassAttribute($class).'>
//
//                <div class="chbs-box-shadow">
//
//                    '.$htmlElement[1].'
//
//                    '.$htmlElement[4].'
//        ';
		
		/***/
		
//		if((int)$bookingForm['meta']['billing_detail_state']===4) {
//		    return($html.$htmlElement[3]);
//        }
		
		/***/
        
		$checkboxHtml=null;
        $class=array(array('chbs-form-checkbox'),array());

        if((int)$bookingForm['meta']['billing_detail_state']===3)
        {
            $class[0]=$class[1]=array();
            $checkboxHtml='<input type="hidden" name="'.CHBSHelper::getFormName('client_billing_detail_enable',false).'" value="1"/> ';
        }
        else
        {
            if(!array_key_exists('client_billing_detail_enable',$data))
                $data['client_billing_detail_enable']=(int)$bookingForm['meta']['billing_detail_state']-1;
            
            if((int)$data['client_billing_detail_enable']===0)
            {
                array_push($class[1],'chbs-hidden');
            }
            else
            {
                array_push($class[0],'chbs-state-selected');
            }
            
            $checkboxHtml=
            '
                <span'.CHBSHelper::createCSSClassAttribute($class[0]).'>
                    <span class="chbs-meta-icon-tick"></span>
                </span>
                <input type="hidden" name="'.CHBSHelper::getFormName('client_billing_detail_enable',false).'" value="'.esc_attr($data['client_billing_detail_enable']).'"/> 
            ';
        }
        
        /***/
        
        $state=CHBSHelper::splitBy($bookingForm['meta']['billing_detail_list_state']);
        if(count($state))
        {
            foreach($state as $value)
                $htmlElement[5].='<option value="'.esc_attr($value).'" '.($data['client_billing_detail_state']==$value ? 'selected' : null).'>'.esc_html($value).'</option>';
            
            $htmlElement[5]=
            '
                <select name="'.CHBSHelper::getFormName('client_billing_detail_state',false).'">
                    '.$htmlElement[5].'
                </select>  
            ';
        }
        else
        {
            $htmlElement[5]=
            '
                <input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_state',false).'" value="'.esc_attr($data['client_billing_detail_state']).'"/>
            ';
        }
        
        /***/
        
        $html.=
        '
                    <div class="chbs-clear-fix">
                        <label class="chbs-form-label-group">
                            '.$checkboxHtml.'
                            '.esc_html__('Billing address','chauffeur-booking-system').'
                        </label>                    
                    </div>

                    <div'.CHBSHelper::createCSSClassAttribute($class[1]).'>

                        <div class="chbs-clear-fix">
                            <div class="chbs-form-field chbs-form-field-width-50">
                                <label>'.esc_html__('Company registered name','chauffeur-booking-system').(in_array('client_billing_detail_company_name',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                <input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_company_name',false).'" value="'.esc_attr($data['client_billing_detail_company_name']).'"/>
                            </div>
                            <div class="chbs-form-field chbs-form-field-width-50">
                                <label>'.esc_html__('Tax number','chauffeur-booking-system').(in_array('client_billing_detail_tax_number',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                <input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_tax_number',false).'" value="'.esc_attr($data['client_billing_detail_tax_number']).'"/>
                            </div>
                        </div>

                        <div class="chbs-clear-fix">
                            <div class="chbs-form-field chbs-form-field-width-33">
                                <label>'.esc_html__('Street','chauffeur-booking-system').(in_array('client_billing_detail_street_name',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                <input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_street_name',false).'" value="'.esc_attr($data['client_billing_detail_street_name']).'"/>
                            </div>
                            <div class="chbs-form-field chbs-form-field-width-33">
                                <label>'.esc_html__('Street number','chauffeur-booking-system').(in_array('client_billing_detail_street_number',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                <input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_street_number',false).'" value="'.esc_attr($data['client_billing_detail_street_number']).'"/>
                            </div>
                            <div class="chbs-form-field chbs-form-field-width-33">
                                <label>'.esc_html__('City','chauffeur-booking-system').(in_array('client_billing_detail_city',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                <input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_city',false).'" value="'.esc_attr($data['client_billing_detail_city']).'"/>
                            </div>                    
                        </div>

                        <div class="chbs-clear-fix">
                            <div class="chbs-form-field chbs-form-field-width-33">
                                <label>'.esc_html__('State','chauffeur-booking-system').(in_array('client_billing_detail_state',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                '.$htmlElement[5].'
                            </div>
                            <div class="chbs-form-field chbs-form-field-width-33">
                                <label>'.esc_html__('Postal code','chauffeur-booking-system').(in_array('client_billing_detail_postal_code',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                <input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_postal_code',false).'" value="'.esc_attr($data['client_billing_detail_postal_code']).'"/>
                            </div>
                            <div class="chbs-form-field chbs-form-field-width-33">
                                <label>'.esc_html__('Country','chauffeur-booking-system').(in_array('client_billing_detail_country_code',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
                                <select name="'.CHBSHelper::getFormName('client_billing_detail_country_code',false).'">
                                    '.$htmlElement[0].'
                                </select>
                            </div>                    
                        </div> 
                        
                        '.$htmlElement[2].'
                            
                    </div>
                    
                    '.$htmlElement[3].'
                        
                </div>
                
            </div>
        ';
        
        return($html);
    }

    function createClientFormInformation($bookingForm)
    {
        $BookingFormElement=new CHBSBookingFormElement();

        /***/

        $data=CHBSHelper::getPostOption();

        /***/

        $html=null;
        $htmlElement=array(null,null,null,null,null,null);

        foreach($bookingForm['dictionary']['country'] as $index=>$value) {
            $htmlElement[0] .= '<option value="' . esc_attr($index) . '" ' . ($data['client_billing_detail_country_code'] == $index ? 'selected' : null) . '>' . esc_html($value[0]) . '</option>';
        }

        $htmlElement[1]=$BookingFormElement->createField(1,$data['service_type_id'],$bookingForm['meta']);

        $htmlElement[2]=$BookingFormElement->createField(2,$data['service_type_id'],$bookingForm['meta']);

        $panel=$BookingFormElement->getPanel($bookingForm['meta']);
        foreach($panel as $index=>$value)
        {
            if(in_array($value['id'],array(1,2))) continue;
            $htmlElement[3].=$BookingFormElement->createField($value['id'],$data['service_type_id'],$bookingForm['meta']);
        }

        $class=array();

        $transfer_type = $data['transfer_type_service_type_'.$data['service_type_id']];
        $html=
            '
            <div'.CHBSHelper::createCSSClassAttribute($class).'>

                <div class="chbs-box-shadow">
                    <h4 class="chbs-booking-extra-header">
                        <span class="chbs-circle" style="display: inline-flex;align-items: center; justify-content: center;"><i class="fa fa-plane" style="height: 27px; line-height: 27px;"></i></span>
                        <span>Flight Information</span>
                    </h4>
                        
                    <div class="chbs-clear-fix">
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Arrival Airline *</label>
                            <input type="text" name="chbs_form_element_field_arrival_airline" value="">
                        </div>
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Arrival Flight *</label>
                            <input type="text" name="chbs_form_element_field_arrival_flight" value="">
                        </div>                        
                    </div>';

        if((int)$transfer_type === 3) {
            $html .=
                '
                    <div class="chbs-clear-fix">
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Departure Airline *</label>
                            <input type="text" name="chbs_form_element_field_departure_airline" value="">
                        </div>          
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>Departure Flight *</label>
                            <input type="text" name="chbs_form_element_field_departure_flight" value="">
                        </div>                
                    </div>';
        }

        $html .=
            '
                    <h4 class="chbs-booking-extra-header" style="margin-top: 20px;">
                        <span class="chbs-circle"><i class="fa fa-address-book"></i></span>
                        <span>Contact Information</span>
                    </h4>
                    <div class="chbs-clear-fix">
                        <div class="chbs-form-field chbs-form-field-width-100">
                            <label>Best way to contact you:</label>
                            <textarea style="max-height: 160px;" name="chbs_form_element_field_contact_information"></textarea>
                            <p class="field-information">
                                Please provide us with either Phone Number, Email or Facebook page link where we can message you if we need to get in touch with you. Note: you may not have
                                phone service in international location, however you may have WiFi, therefore it is recommend to use Facebook messenger to contact us. 
                            </p>
                        </div>                
                    </div>   
        ';

        return($html);
    }
    
    /**************************************************************************/
   
    function createClientFormSignIn($bookingForm)
    {
        $User=new CHBSUser();
        $WooCommerce=new CHBSWooCommerce();
        
        if(!$WooCommerce->isEnable($bookingForm['meta'])) return;
        if($User->isSignIn()) return;
        
        if((int)$bookingForm['meta']['woocommerce_account_enable_type']===0) return;
        
        $data=CHBSHelper::getPostOption();
        
        $html=
        '
            <div class="chbs-client-form-sign-in">

                <div class="chbs-box-shadow">
                
                    <div class="chbs-clear-fix">
                        <label class="chbs-form-label-group">'.esc_html__('Sign In','chauffeur-booking-system').'</label>
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>'.esc_html__('Login *','chauffeur-booking-system').'</label>
                            <input type="text" name="'.CHBSHelper::getFormName('client_sign_in_login',false).'" value=""/>
                        </div>
                        <div class="chbs-form-field chbs-form-field-width-50">
                            <label>'.esc_html__('Password *','chauffeur-booking-system').'</label>
                            <input type="password" name="'.CHBSHelper::getFormName('client_sign_in_password',false).'" value=""/>
                        </div>
                    </div>
                 
                </div>
                
                <div class="chbs-clear-fix">
                
                   <a href="#" class="chbs-button chbs-button-style-2 chbs-button-sign-up">
                        '.esc_html__('Don\'t Have an Account?','chauffeur-booking-system').'
                   </a> 
                   
                   <a href="#" class="chbs-button chbs-button-style-1 chbs-button-sign-in">
                       '.esc_html__('Sign In','chauffeur-booking-system').'
                   </a> 
                   
                   <input type="hidden" name="'.CHBSHelper::getFormName('client_account',false).'" value="'.(int)$data['client_account'].'"/> 
                    
                </div>

            </div>
        ';
        
        return($html);
    }
    
    /**************************************************************************/
    
    function manageEditColumns($column)
    {
        $column=array
        (
            'cb'                                                                =>  $column['cb'],
            'title'                                                             =>  __('Title','chauffeur-booking-system'),
            'service_type'                                                      =>  __('Service type','chauffeur-booking-system'),
            'date'                                                              =>  $column['date']
        );
   
		return($column);          
    }
    
    /**************************************************************************/
    
    function managePostsCustomColumn($column)
    {
		global $post;
		
		$meta=CHBSPostMeta::getPostMeta($post);
        
        $Validation=new CHBSValidation();
        $ServiceType=new CHBSServiceType();
        
		switch($column) 
		{
			case 'service_type':
                
                $html=null;
                
                foreach($meta['service_type_id'] as $value)
                {
                    $serviceType=$ServiceType->getServiceType($value);
                    
                    if($Validation->isNotEmpty($html)) $html.=', ';
                    $html.=$serviceType[0];
                }
                
                echo esc_html($html);
                
			break;
        }
    }
    
    /**************************************************************************/
    
    function manageEditSortableColumns($column)
    {
		return($column);       
    }
    
    /**************************************************************************/   
    
    function userSignIn()
    {
        $data=CHBSHelper::getPostOption();
        
        $response=array('user_sign_in'=>0);
        
        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            $this->setErrorGlobal($response,__('Login error.','chauffeur-booking-system'));
            $this->createFormResponse($response);
        }
        
        $User=new CHBSUser();
        $WooCommerce=new CHBSWooCommerce();
        
        if(!$User->signIn($data['client_sign_in_login'],$data['client_sign_in_password']))
            $this->setErrorGlobal($response,__('Login error.','chauffeur-booking-system'));
        else 
        {
            $userData=$WooCommerce->getUserData();
            
            $response['user_sign_in']=1;  
            
            $response['summary']=$this->createSummary($data,$bookingForm);
            $response['client_form_sign_up']=$this->createClientFormSignUp($bookingForm,$userData);
        }
        
        $this->createFormResponse($response);
    }
    
    /**************************************************************************/
    
    function createVehiclePassengerFilterField($min,$max)
    {
        $html=null;
        
        for($i=$min;$i<=$max;$i++)
            $html.='<option value="'.esc_attr($i).'"'.($i==1 ? ' selected="selected"' : '').'>'.esc_html($i).'</option>';
            
        $html='<select name="'.CHBSHelper::getFormName('vehicle_passenger_count',false).'">'.$html.'</select>';

        return($html);
    }
    
    /**************************************************************************/
    
    function checkCouponCode()
    {        
        $response=array();
        
        $data=CHBSHelper::getPostOption();
        
        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            $response['html']=null;
            CHBSHelper::createJSONResponse($response);
        }
        
        $price=array();
        $response['html']=$this->createSummaryPriceElement($data,$bookingForm,$price);
        
        $Coupon=new CHBSCoupon();
        $coupon=$Coupon->checkCode();
        
        $response['error']=$coupon===false ? 1 : 0;
        
        if($response['error']===1)
           $response['message']=__('Provided coupon is invalid.','chauffeur-booking-system'); 
        else 
            $response['message']=__('Provided coupon is valid. Discount has been granted.','chauffeur-booking-system');
        
        CHBSHelper::createJSONResponse($response);
    }
    
    /**************************************************************************/
    
    function setGratuityCustomer()
    {
        $response=array();
        
        $data=CHBSHelper::getPostOption();
        
        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            $response['html']=null;
            CHBSHelper::createJSONResponse($response);
        }
        
        $price=array();
        $response['html']=$this->createSummaryPriceElement($data,$bookingForm,$price);
        
        if($price['gratuity']['value']>0.00)
           $response['message']=__('Gratuity has been added to the sum of the booking.','chauffeur-booking-system'); 
        else 
            $response['message']=__('Gratuity has been set to 0.00.','chauffeur-booking-system');
        
        $response['gratuity']=$price['gratuity']['value'];
        
        CHBSHelper::createJSONResponse($response);        
    }
    
    /**************************************************************************/
    
    function createBookingFormExtra($bookingForm,$data, $transfer_type_target='1')
    {

        $html=null;
        $transfer_type = $data['transfer_type_service_type_'.$data['service_type_id']]; //transfer type, 1 - one way, 3 - round trip

        $source_product = $bookingForm['meta']['booking_source_product'];

        $product_addons = WC_Product_Addons_Helper::get_product_addons( $source_product );
        $html .=
                '
                <h4 class="chbs-booking-extra-header">
                    <span class="chbs-circle"><i class="fa fa-shopping-cart"></i></span>
                    <span>' . esc_html__('Extra options', 'chauffeur-booking-system') . '</span>
                </h4>
            ';
        $html .= '<div class="chbs-booking-extra-list">';
            $html .= '<ul class="chbs-list-reset">';

                foreach ($product_addons as $index=>$addon){
                    $class = [];
                    array_push($class, 'chbs-booking-extra-list-item-quantity-enable');

                    $html .= '
                        <li ' . CHBSHelper::createCSSClassAttribute($class) . '>
                            <div class="chbs-column-1">
                                <span class="booking-form-extra-name">
                                    ' . $addon['name'] . '
                                </span>
                        ';
                    if ((int)$bookingForm['meta']['price_hide'] === 0) {
                        CHBSBookingHelper::getPriceType($bookingForm, $priceType, $sumType, $taxShow, $data['step']);

                        $html .=
                            '
                                <span class="booking-form-extra-price">
                                    ' . wc_price($addon['price']) . '
                                </span>
                            ';
                    }
                    $html .=
                        '
                                <span class="booking-form-extra-description">
                                    ' . esc_html($addon['description']) . '
                                </span>
                            </div>
                        ';

                    if ($transfer_type_target === '3') {
                        $fieldName = $addon['field_name'] . '_return';
                    }else {
                        $fieldName = $addon['field_name'] . '_arrival';
                    }
                    $html .=
                        '
                        <div class="chbs-column-2">
                            <div class="chbs-form-field">
                                <div class="chbs-quantity-section">
                                    <input
                                        type="text"
                                        name="' . CHBSHelper::getFormName($fieldName, false) . '"
                                        value="' . (array_key_exists($fieldName, $data) ? $data[$fieldName] : 1) . '"
                                    />
                                </div>
                            </div>
                        </div>
                        ';
//                    $bookingExtraIdSelected = preg_split('/,/', $data['booking_extra_id']);

                    $class = array('chbs-button', 'chbs-button-style-2');

                    if ($transfer_type_target === '3') {
                        $data_attribute = 'data-value-return';
                    }else {
                        $data_attribute = 'data-value-arrival';
                    }

                    $html .=
                        '
                            <div class="chbs-column-3">
                                <a href="#"' . CHBSHelper::createCSSClassAttribute($class) . ' '.$data_attribute.'="' .  CHBSHelper::getFormName($fieldName, false) . '">
                                    ' . esc_html__('Select', 'chauffeur-booking-system') . '
                                    <span class="chbs-meta-icon-tick"></span>
                                </a>
                            </div>
                        </li>';
                }

                $html .= '</ul>';
            $html .= '</div>';

        return($html);
    }
    
    /**************************************************************************/
    
    function getBookingFormDateAvailable($meta)
    {
        $date=array();
        
        $Date=new CHBSDate();
        $Validation=new CHBSValidation();
        
        $type=array(1=>'days',2=>'hours',3=>'minutes');
        
        /***/
              
        $dateStart=strtotime('+ '.(int)$meta['booking_period_from'].' '.$type[(int)$meta['booking_period_type']],strtotime(date_i18n('d-m-Y G:i')));
        		
        $offset=(int)$meta['booking_period_from'];
        
        if((int)$meta['booking_period_type']===1)
           $offset*=24;
        if((int)$meta['booking_period_type']===3)
            $offset*=3600;       
        
        /***/
        
        if($Validation->isEmpty($meta['booking_period_to'])) $dateStop=null;
        else $dateStop=strtotime('+ '.(int)$meta['booking_period_to'].' '.$type[(int)$meta['booking_period_type']],$dateStart);
     
        /***/
        
		$date['min']=date('d-m-Y H:i:s',$dateStart);
		$date['max']=is_null($dateStop) ? null : date('d-m-Y H:i:s',$dateStop);

        return($date);
    }
    
    /**************************************************************************/
    
	function createPagination($dictionary,$vehiclePerPage)
	{
        $vehicleTotal=count($dictionary);
        
        if($vehiclePerPage<=0) return(null);
        if($vehiclePerPage>=$vehicleTotal) return(null);
        
        $html=
		'
            <div class="chbs-pagination" data-page_current="1" data-vehicle_per_page="'.(int)$vehiclePerPage.'">
                <a href="#-1" class="chbs-pagination-prev"></a>
                <a href="#1" class="chbs-pagination-next"></a>
			</div>
		';
		
		return($html);
	}

    /**************************************************************************/
	
	function checkVehicleBidPrice()
    {        
        $response=array();
        
        $data=CHBSHelper::getPostOption();
        
        if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
        {
            $response['html']=null;
            CHBSHelper::createJSONResponse($response);
        }
		
		if(!array_key_exists($data['vehicle_id'],$bookingForm['dictionary']['vehicle']))
		{
            $response['html']=null;
            CHBSHelper::createJSONResponse($response);			
		}
		
		$this->createSummaryPriceElementAjax(true);
    }
	
    /**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/