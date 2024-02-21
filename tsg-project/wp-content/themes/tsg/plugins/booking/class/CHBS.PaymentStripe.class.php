<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPaymentStripe
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->paymentMethod=array
		(
			'alipay'															=>	array(__('Alipay','chauffeur-booking-system')),
			'card'																=>	array(__('Cards','chauffeur-booking-system')),			
			'ideal'																=>	array(__('iDEAL','chauffeur-booking-system')),
			'fpx'																=>	array(__('FPX','chauffeur-booking-system')),
			'bacs_debit'														=>	array(__('Bacs Direct Debit','chauffeur-booking-system')),
			'bancontact'														=>	array(__('Bancontact','chauffeur-booking-system')),
			'giropay'															=>	array(__('Giropay','chauffeur-booking-system')),
			'p24'																=>	array(__('Przelewy24','chauffeur-booking-system')),
			'eps'																=>	array(__('EPS','chauffeur-booking-system')),
			'sofort'															=>	array(__('Sofort','chauffeur-booking-system')),
			'sepa_debit'														=>	array(__('SEPA Direct Debit','chauffeur-booking-system'))
		);
		
		asort($this->paymentMethod);
	}
	
	/**************************************************************************/
	
	function getPaymentMethod()
	{
		return($this->paymentMethod);
	}
	
	/**************************************************************************/
	
	function isPaymentMethod($paymentMethod)
	{
		return(array_key_exists($paymentMethod,$this->paymentMethod) ? true : false);
	}
	
	/**************************************************************************/
	
	function createSession($booking,$bookingBilling,$bookingForm)
	{
		$Validation=new CHBSValidation();
		
		Stripe\Stripe::setApiKey($bookingForm['meta']['payment_stripe_api_key_secret']);

		/***/
		
		$productId=$bookingForm['meta']['payment_stripe_product_id'];
		
		if($Validation->isEmpty($productId))
		{
			$product=\Stripe\Product::create(
			[
				'name'															=> __('Chauffeur service','chauffeur-booking-system')
			]);		
			
			$productId=$product->id;
			
			CHBSPostMeta::updatePostMeta($bookingForm['post']->ID,'payment_stripe_product_id',$productId);
		}
		
		/***/
		
		$price=\Stripe\Price::create(
		[
			'product'															=>	$productId,
			'unit_amount'														=>	$bookingBilling['summary']['pay']*100,
			'currency'															=>	$booking['meta']['currency_id'],
		]);

		/***/
		
		$currentURLAddress=home_url();
		if($Validation->isEmpty($bookingForm['meta']['payment_stripe_success_url_address']))
			$bookingForm['meta']['payment_stripe_success_url_address']=$currentURLAddress;
		if($Validation->isEmpty($bookingForm['meta']['payment_stripe_cancel_url_address']))
			$bookingForm['meta']['payment_stripe_cancel_url_address']=$currentURLAddress;
		
		$session=\Stripe\Checkout\Session::create
		(
			[
				'payment_method_types'											=>	$bookingForm['meta']['payment_stripe_method'],
				'mode'															=>	'payment',
				'line_items'													=>
				[
					[
						'price'													=>	$price->id,
						'quantity'												=>	1
					]
				],
				'success_url'													=>	$bookingForm['meta']['payment_stripe_success_url_address'],
				'cancel_url'													=>	$bookingForm['meta']['payment_stripe_cancel_url_address']
			]		
		);
		
		return($session->id);
	}
    
    /**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/