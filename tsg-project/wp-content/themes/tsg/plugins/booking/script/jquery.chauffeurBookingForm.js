
;(function($,doc,win)
{
	"use strict";
	
	var ChauffeurBookingForm=function(object,option)
	{

        var $this=$(object);
		
		var $optionDefault;
		var $option=$.extend($optionDefault,option);
        
        var $marker=[];
        
        var $googleMap;
        
        var $directionsRenderer;
        
        var $directionsService;
        var $directionsServiceResponse;
        
        var $startLocation;
        
        var $googleMapHeightInterval;

        var $self=this;

        var $iti;
        
        this.setup=function()
        {      
            var helper=new CHBSHelper();
            helper.getMessageFromConsole();
            
            $self.e('select,input[type="hidden"]').each(function()
            {
                if($(this)[0].hasAttribute('data-value')) {
                    $(this).val($(this).attr('data-value'));
                }
            });
            
            $self.init();

        };

        this.init=function()
        {           
            var helper=new CHBSHelper();
            
            if(helper.isMobile())
            {
                $self.e('input[name="chbs_pickup_date_service_type_1"]').prop('readonly',true);
                $self.e('input[name="chbs_pickup_date_service_type_2"]').prop('readonly',true);
                $self.e('input[name="chbs_pickup_date_service_type_3"]').prop('readonly',true);
                $self.e('input[name="chbs_return_date_service_type_1"]').prop('readonly',true);
                $self.e('input[name="chbs_return_date_service_type_3"]').prop('readonly',true);
            }
            
            $self.createButtonRadio('.chbs-booking-extra');
            
            /***/
            
            $(window).resize(function() 
			{
                try
                {
                    $self.e('select').selectmenu('close');
                }
                catch(e) {}
                
                try
                {
                    $self.e('.chbs-datepicker').datepicker('hide');
                }
                catch(e) {}
                
                try
                {
                    $self.e('.chbs-timepicker').timepicker('hide');
                }
                catch(e) {}
                
                try
                {
                    $self.e('.ui-timepicker-wrapper').css({opacity:0});
                }
                catch(e) {}
                
                try
                {
                    var currCenter=$googleMap.getCenter();
                    google.maps.event.trigger($googleMap,'resize');
                    $googleMap.setCenter(currCenter);
                }
                catch(e) {}
			});

            $self.googleMapCustomizeHeight();

            $(window).on('resize',function(){
                $self.googleMapCustomizeHeight();
            });
            
			/***/

            var params = new URLSearchParams(window.location.search);

            if($('.chauffeur-destination-page').length > 0 || (params.has('from') && params.has('to'))) {
                if (params.get('from') !== '') {
                    this.createGeocodePoint($('input[name="chbs_pickup_location_service_type_' + parseInt($self.e('[name="chbs_service_type_id"]').val()) + '"]'));
                }
                if (params.get('to') !== '') {
                    this.createGeocodePoint($('input[name="chbs_dropoff_location_service_type_' + parseInt($self.e('[name="chbs_service_type_id"]').val()) + '"]'));
                }
            }

            //end test code


			if(parseInt($option.use_my_location_link_enable,10)===1)
			{
				if(navigator.geolocation)
				{
					$self.e('.chbs-my-location-link').css('display','inline');		
					navigator.geolocation.getCurrentPosition($self.myLocationSuccess,$self.myLocationError);	
				}
			}
			
            /***/
            
            var active=1;
            var panel=$self.e('.chbs-tab>ul').children('li[data-id="'+parseInt($self.e('[name="chbs_service_type_id"]').val())+'"]',10);
            
            if(panel.length===1) active=panel.index();
            
            $self.e('.chbs-tab').tabs(
            {
                activate                                                        :   function(event,ui)
                {
                    $self.googleMapReInit();

                    var serviceTypeId=$self.getServiceTypeId();
                    $self.setServiceTypeId(serviceTypeId);

                    $self.googleMapCreate();
                    $self.googleMapCreateRoute();
                },
                active                                                          :   active
            });
                          
            /***/
            
            $self.e('.chbs-main-navigation-default a').on('click',function(e)
            {
                e.preventDefault();
                
                var navigation=parseInt($(this).parent('li').data('step'),10);
                var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
                
                if(navigation-step===0) return;
                
                $self.goToStep(navigation-step);
            });

            $self.e('.chbs-main-content-step-5 .chbs-meta-icon-tick').on('click', function(e){
                $self.goToStep(1);
            });

            $self.e('.chbs-button-step-next').on('click',function(e)
            {
                e.preventDefault();
                $self.goToStep(1);
            });
            
            $self.e('.chbs-button-step-prev').on('click',function(e)
            {
                e.preventDefault();
                $self.goToStep(-1);
            });
            
             /***/
            
            $self.e('.chbs-form-field').on('click',function(e)
            {
                e.preventDefault();
                if(($(e.target).hasClass('chbs-location-add')) || ($(e.target).hasClass('chbs-location-remove'))) return;
                $(this).find(':input').focus(); 
                
                var select=$(this).find('select:not(.chbs-selectmenu-disable)');
                
                if(select.length)
                    select.selectmenu('open');
            });
            
             /***/
            
            $self.e('.chbs-location-add').on('click',function(e)
            {
                e.preventDefault();

                var field=$(this).parent('.chbs-form-field:first');
                var newField=$self.e('.chbs-form-field-location-autocomplete.chbs-hidden').clone(true,true);

                newField.insertAfter(field);
                newField.removeClass('chbs-hidden');
                
                newField.find(':input').focus();
                
                $self.googleMapAutocompleteCreate(newField.find('input[type="text"]'));
                
                $self.createLabelTooltip();
            });

            $self.e('.chbs-location-remove').on('click',function(e)
            {
                e.preventDefault();
                $(this).parent('.chbs-form-field:first').remove();

                $self.googleMapCreate();
                $self.googleMapCreateRoute();
            });       

            $self.e('.chbs-form-field-location-autocomplete input[type="text"]').each(function()
            {
                $self.googleMapAutocompleteCreate($(this));
            });
                       
            /***/
            
            $self.e('.chbs-payment>li>a').on('click',function(e)
            {
                e.preventDefault();
                
                $(this).parents('.chbs-payment').find('li>a').removeClass('chbs-state-selected');
                $(this).addClass('chbs-state-selected');
                
                $self.getGlobalNotice().addClass('chbs-hidden');
                
                $self.e('input[name="chbs_payment_id"]').val($(this).attr('data-payment-id'));
            });
            
            $self.e('>*').on('click','.chbs-form-checkbox',function(e)
            {
                var text=$(this).next('input[type="hidden"]');
                var value=parseInt(text.val(),10)===1 ? 0 : 1;
                
                if(value===1) $(this).addClass('chbs-state-selected');
                else $(this).removeClass('chbs-state-selected');
                
                $(this).next('input[type="hidden"]').on('change',function(e)
                { 
                    var value=parseInt($(this).val(),10)===1 ? 1 : 0;
                    var section=$(this).parents('.chbs-clear-fix:first').nextAll('div:first');

                    if(value===0) section.addClass('chbs-hidden');
                    else section.removeClass('chbs-hidden');

                    $(window).scroll();
                });
                
                text.val(value).trigger('change');
            });
            
            /***/

            $self.e('.chbs-main-content-step-2').on('click','.chbs-booking-extra .chbs-booking-extra-list .chbs-button.chbs-button-style-2',function(e)
            {
                e.preventDefault();
                if(typeof(window.tsg_analytics) !== 'undefined') {
                    window.tsg_analytics.track_event('TransferForm', 'Extra Button', $(this).attr('data-value-arrival'));
                }
                if(!$(this).parent('.chbs-button-radio').length) {
                    $(this).toggleClass('chbs-state-selected');
                }
                
                var data=[];
                $self.e('.chbs-main-content-step-2 .chbs-booking-extra-list .chbs-button.chbs-button-style-2').each(function()
                {
                    if($(this).hasClass('chbs-state-selected')) {
                        data.push($(this).attr('data-value-arrival'));
                    }
                });

                $(this).closest('li').toggleClass('active');

                $self.e('input[name="chbs_booking_extra_id"]').val(data.join(','));
            });

            $self.e('.chbs-main-content-step-3').on('click','.chbs-booking-extra .chbs-booking-extra-list .chbs-button.chbs-button-style-2',function(e)
            {
                e.preventDefault();
                if(typeof(window.tsg_analytics) !== 'undefined') {
                    window.tsg_analytics.track_event('TransferForm', 'Extra Button', $(this).attr('data-value-return'));
                }
                if(!$(this).parent('.chbs-button-radio').length) {
                    $(this).toggleClass('chbs-state-selected');
                }

                var data=[];
                $self.e('.chbs-main-content-step-3 .chbs-booking-extra-list .chbs-button.chbs-button-style-2').each(function()
                {
                    if($(this).hasClass('chbs-state-selected')) {
                        data.push($(this).attr('data-value-return'));
                    }
                });

                $(this).closest('li').toggleClass('active');

                $self.e('input[name="chbs_booking_extra_return_id"]').val(data.join(','));
            });
            
            $self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-list .chbs-button.chbs-button-style-2:not(.chbs-button-on-request)',function(e)
            {
                e.preventDefault();
                
                // if($(this).hasClass('chbs-state-selected')) return;
                if(typeof(window.tsg_analytics) !== 'undefined') {
                    window.tsg_analytics.track_event('TransferForm', 'Vehicle Button', parseInt($(this).parents('.chbs-vehicle').attr('data-id'), 10));
                }

                $self.e('.chbs-main-content-step-2 .chbs-vehicle-list .chbs-button.chbs-button-style-2').removeClass('chbs-state-selected');

                $(this).addClass('chbs-state-selected');

                $self.e('input[name="chbs_vehicle_id"]').val(parseInt($(this).parents('.chbs-vehicle').attr('data-id'),10));

				$self.e('.chbs-vehicle-content-price-bid>div').addClass('chbs-hidden');
				$self.e('.chbs-vehicle-list [name="chbs_vehicle_bid_price[]"]').val('');
				$(this).parents('.chbs-vehicle').find('.chbs-vehicle-content-price-bid>div:first-child').removeClass('chbs-hidden');

                $self.getGlobalNotice().addClass('chbs-hidden');
            });

            $self.e('.chbs-main-content-step-3').on('click','.chbs-vehicle-list .chbs-button.chbs-button-style-2:not(.chbs-button-on-request)',function(e)
            {
                e.preventDefault();
                if(typeof(window.tsg_analytics) !== 'undefined') {
                    window.tsg_analytics.track_event('TransferForm', 'Vehicle Button', parseInt($(this).parents('.chbs-vehicle').attr('data-id'), 10));
                }

                $self.e('.chbs-main-content-step-3 .chbs-vehicle-list .chbs-button.chbs-button-style-2').removeClass('chbs-state-selected');

                $(this).addClass('chbs-state-selected');

                $self.e('input[name="chbs_vehicle_id_return"]').val(parseInt($(this).parents('.chbs-vehicle').attr('data-id'),10));

                $self.e('.chbs-vehicle-content-price-bid>div').addClass('chbs-hidden');
                $self.e('.chbs-vehicle-list [name="chbs_vehicle_bid_price[]"]').val('');
                $(this).parents('.chbs-vehicle').find('.chbs-vehicle-content-price-bid>div:first-child').removeClass('chbs-hidden');

                $self.getGlobalNotice().addClass('chbs-hidden');
            });
            
            /***/
            
            $self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta a',function(e)
            {
                e.preventDefault();
                
                $(this).toggleClass('chbs-state-selected');
                
                var section=$(this).parents('.chbs-vehicle:first').find('.chbs-vehicle-content-description');
                
                var height=parseInt(section.children('div').actual('outerHeight',{includeMargin:true}),10);
                
                if(section.hasClass('chbs-state-open'))
                {
                    section.animate({height:0},150,function()
                    {
                        section.removeClass('chbs-state-open');
                        $(window).scroll();
                    });                      
                }
                else
                {
                    section.animate({height:height},150,function()
                    {
                        section.addClass('chbs-state-open');
                        $(window).scroll();
                    });                        
                }
            });
            
            /***/
            
            $self.e('.chbs-main-content-step-4').on('click','.chbs-summary .chbs-summary-header a',function(e)
            {
                e.preventDefault();
                $self.goToStep(parseInt($(this).attr('data-step'),10)-4);
            });
            
            /***/
            
            $self.e('.chbs-main-content-step-4').on('click','.chbs-coupon-code-section a',function(e)
            {
                e.preventDefault();
                
                $self.setAction('coupon_code_check');
       
                $self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
                {
                    $self.e('.chbs-summary-price-element').replaceWith(response.html);
                    
                    var object=$self.e('.chbs-coupon-code-section');
                    
                    object.qtip(
                    {
                        show            :	
                        { 
                            target      :	$(this) 
                        },
                        style           :	
                        { 
                            classes     :	(response.error===1 ? 'chbs-qtip chbs-qtip-error' : 'chbs-qtip chbs-qtip-success')
                        },
                        content         : 	
                        { 
                            text        :	response.message 
                        },
                        position        : 	
                        { 
                            my          :	($option.is_rtl ? 'bottom right' : 'bottom left'),
                            at          :	($option.is_rtl ? 'top right' : 'top left'),
                            container   :   object.parent()
                        }
                    }).qtip('show');	
                });
            });
            
            /***/
            
            $self.e('.chbs-main-content-step-4').on('click','.chbs-gratuity-section a',function(e)
            {
                e.preventDefault();
                
                $self.setAction('gratuity_customer_set');
       
                $self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
                {  
                    $self.e('.chbs-summary-price-element').replaceWith(response.html);
                    
                    var object=$self.e('.chbs-gratuity-section');
                    
                    object.qtip(
                    {
                        show            :	
                        { 
                            target      :	$(this) 
                        },
                        style           :	
                        { 
                            classes     :	(response.error===1 ? 'chbs-qtip chbs-qtip-error' : 'chbs-qtip chbs-qtip-success')
                        },
                        content         : 	
                        { 
                            text        :	response.message 
                        },
                        position        : 	
                        { 
                            my          :	($option.is_rtl ? 'bottom right' : 'bottom left'),
                            at          :	($option.is_rtl ? 'top right' : 'top left'),
                            container   :   object.parent()
                        }
                    }).qtip('show');
                    
                    object.find('[name="chbs_gratuity_customer_value"]').val(response.gratuity);
                });
            });            
            
            /***/
            
            $self.e('.chbs-datepicker').datepicker(
            {
                autoSize                                                        :   true,
                minDate                                                         :   $option.datetime_min_format,
                maxDate                                                         :   $option.datetime_max,
                dateFormat                                                      :   $option.date_format_js,
				beforeShow														:	function(date)
				{
					if($(date).attr('name')==='chbs_return_date_service_type_'+$self.getServiceTypeId())
					{
						try
						{
							var datePickup=$self.e('[name="chbs_pickup_date_service_type_'+$self.getServiceTypeId()+'"]').val();
							var dateParse=$.datepicker.parseDate($option.date_format_js,datePickup);
							
							if(dateParse!==null)
							{
								$(this).datepicker('option','minDate',datePickup); 
							}
						}
						catch(e)
						{
							
						}
					}	
				},
                beforeShowDay                                                   :   function(date)
                {
                    var helper=new CHBSHelper();
                    
                    date=$.datepicker.formatDate('dd-mm-yy',date);
                    
                    for(var i in $option.date_exclude)
                    {
                        var r=helper.compareDate([date,$option.date_exclude[i].start,$option.date_exclude[i].stop]);
                        if(r) return([false,'','']);
                    }
                    
                    /***/
                    
                    var sDate=date.split('-');
                    var date=new Date(sDate[2],sDate[1]-1,sDate[0]);
                    
                    var dayWeek=parseInt(date.getUTCDay(),10)+1;
                    
                    if((!$option.business_hour[dayWeek].start) || (!$option.business_hour[dayWeek].stop)) 
                        return([false,'','']);
                    
                    /***/
                    
                    return([true,'','']);
                },
                onSelect                                                        :   function(date,object)
                {
                    var helper=new CHBSHelper();
                    
                    var dateSelected=[object.selectedDay,object.selectedMonth+1,object.selectedYear];
                    
                    for(var i in dateSelected)
                    {
                        if(new String(dateSelected[i]).length===1) dateSelected[i]='0'+dateSelected[i];
                    }
                    
                    dateSelected=dateSelected[0]+'-'+dateSelected[1]+'-'+dateSelected[2];
                    
                    /***/
                    
                    var minTime='';
                    var maxTime='';
                                                        
                    /***/
             
                    if((helper.isEmpty(minTime)) || (helper.isEmpty(maxTime)))
                    {
                        var dayWeek=parseInt($(this).datepicker('getDate').getUTCDay(),10)+1;
                        if(new String(typeof($option.business_hour[dayWeek]))!=='undefined')
                        {
                            minTime=$option.business_hour[dayWeek].start;
                            maxTime=$option.business_hour[dayWeek].stop;
                        }
                    }
					
					/***/
					
					var t=$option.datetime_min.split(' ');
					
                    if(dateSelected===t[0])
                    {
						if(Date.parse('01/01/1970 '+t[1])>Date.parse('01/01/1970 '+minTime))
							minTime=t[1];
                    }
					
					/***/
					
					if(!helper.isEmpty($option.datetime_max))
					{
						var t=$option.datetime_max.split(' ');

						if(dateSelected===t[0])
						{
							if(Date.parse('01/01/1970 '+t[1])<Date.parse('01/01/1970 '+maxTime))
								maxTime=t[1];
						}					
					}
					
					/***/
					
					var option=
					{
						showOn												:   [],
						showOnFocus											:   false,
						timeFormat											:   $option.time_format,
						step                                                :   $option.timepicker_step,
						disableTouchKeyboard								:   true,
						minTime												:   minTime,
						maxTime												:   maxTime,
						disableTextInput									:	false
					};
						
					/***/
					
                    if(parseInt($option.timepicker_dropdown_list_enable,10)===1)
                    {
                        var prefix=$(this).attr('name').indexOf('pickup')>-1 ? 'pickup' : 'return';

                        var timeField=$self.e('input[name="chbs_'+prefix+'_time_service_type_'+$self.getServiceTypeId()+'"]');

                        try
                        {
                            timeField.timepicker('remove');
                        }
                        catch(e) {}
				
						if(parseInt($self.getServiceTypeId(),10)===3)
						{
							var routeField=$self.e('select[name="chbs_route_service_type_3"]>option:selected');
							var timeExclude=JSON.parse(routeField.attr('data-time_exclude'));
						
							var dayWeek=parseInt($(this).datepicker('getDate').getUTCDay(),10)+1;							
						
							if(timeExclude[dayWeek]!=undefined)
							{
								option.step=1;
								option.disableTextInput=true;
								option.disableTimeRanges=timeExclude[dayWeek];
								option.maxTime=timeExclude[dayWeek][timeExclude[dayWeek].length-2][1];
							}
						}
                    }
					
					/***/
					
					timeField.timepicker(option);

					/***/

					timeField.val('').timepicker('show');
					timeField.blur();

					$self.setTimepicker(timeField);
                }
            });
			
			$('.ui-datepicker').addClass('notranslate');

            if(parseInt($option.timepicker_dropdown_list_enable,10)===1)
            {
                $this.on('focusin','.chbs-timepicker',function()
                {
                    var helper=new CHBSHelper();

                    var prefix=$(this).attr('name').indexOf('pickup')>-1 ? 'pickup' : 'return';

                    var field=$self.e('input[name="chbs_'+prefix+'_date_service_type_'+$self.getServiceTypeId()+'"]');

                    if(helper.isEmpty(field.val()))
                    {
                        $(this).timepicker('remove');
                        field.click();
                        return;
                    }
                    else
                    {
                        if(helper.isEmpty($(this).val()))
                            $(this).timepicker('show');
                    }
                });
            }
            
            /***/
            
            $self.createSelectMenu();
            $self.createFixedLocationAutocomplete();
            
            /***/

            $self.e('.chbs-form-field').has('select').css({cursor:'pointer'});

            /***/

            $self.e('.chbs-main-content').on('click','.chbs-button-widget-submit',function(e)
            {
                e.preventDefault();
               
                var helper=new CHBSHelper();
                
                var data={};
                
                data.service_type_id=$self.getServiceTypeId();
                
                data.pickup_date=$self.e('[name="chbs_pickup_date_service_type_'+data.service_type_id+'"]').val();
                data.pickup_time=$self.e('[name="chbs_pickup_time_service_type_'+data.service_type_id+'"]').val();
                
                if($.inArray($self.getServiceTypeId(),[1,2])>-1)
                {
                    var coordinate=$self.e('[name="chbs_pickup_location_coordinate_service_type_'+data.service_type_id+'"]').val();
                    if(!helper.isEmpty(coordinate))
                    {
                        var json=JSON.parse(coordinate);
                        data.pickup_location_lat=json.lat;
                        data.pickup_location_lng=json.lng;
                        data.pickup_location_address=json.address;
                        data.pickup_location_formatted_address=json.formatted_address;
                        data.pickup_location_text=$self.e('[name="chbs_pickup_location_service_type_'+data.service_type_id+'"]').val();  
                    }
                    
                    var coordinate=$self.e('[name="chbs_dropoff_location_coordinate_service_type_'+data.service_type_id+'"]').val();
                    if(!helper.isEmpty(coordinate))
                    {
                        var json=JSON.parse(coordinate);
                        data.dropoff_location_lat=json.lat;
                        data.dropoff_location_lng=json.lng;
                        data.dropoff_location_address=json.address;
                        data.dropoff_location_formatted_address=json.formatted_address;
                        data.dropoff_location_text=$self.e('[name="chbs_dropoff_location_service_type_'+data.service_type_id+'"]').val();
                    }    
                    
                    var pickupLocationId=$self.e('[name="chbs_fixed_location_pickup_service_type_'+data.service_type_id+'"]').val();
                    if(parseInt(pickupLocationId,10)>0)
                        data.fixed_location_pickup_id=pickupLocationId;
                    
                    var dropoffLocationId=$self.e('[name="chbs_fixed_location_dropoff_service_type_'+data.service_type_id+'"]').val();
                    if(parseInt(dropoffLocationId,10)>0)
                        data.fixed_location_dropoff_id=dropoffLocationId;                    
                }
                else
                {
                    data.route_id=$self.e('[name="chbs_route_service_type_'+data.service_type_id+'"]').val();    
                }
                
                if($.inArray($self.getServiceTypeId(),[1,3])>-1)
                {
                    data.extra_time=$self.e('[name="chbs_extra_time_service_type_'+data.service_type_id+'"]').val();
                    data.transfer_type=$self.e('[name="chbs_transfer_type_service_type_'+data.service_type_id+'"]').val(); 
              
                    if($.inArray(data.transfer_type,[3]))
                    {
                        data.duration=$self.e('[name="chbs_duration_service_type_'+data.service_type_id+'"]').val();  
                        
                        data.return_date=$self.e('[name="chbs_return_date_service_type_'+data.service_type_id+'"]').val();  
                        data.return_time=$self.e('[name="chbs_return_time_service_type_'+data.service_type_id+'"]').val();  
                    }
                }
                
                if($.inArray($self.getServiceTypeId(),[2])>-1)
                {
                    data.duration=$self.e('[name="chbs_duration_service_type_'+data.service_type_id+'"]').val();  
                }
                
                var passengerAdult=$self.e('[name="chbs_passenger_adult_service_type_'+data.service_type_id+'"]');
                if(passengerAdult.length===1) data.passenger_adult=passengerAdult.val();
                
                var passengerChildren=$self.e('[name="chbs_passenger_children_service_type_'+data.service_type_id+'"]');
                if(passengerChildren.length===1) data.passenger_children=passengerChildren.val();
                
                data.currency=$self.e('[name="chbs_currency"]').val();
                
                data.widget_submit=1;

                /***/
                
                var url=$option.widget.booking_form_url;
                
                if(url.indexOf('?')===-1) url+='?';
                if(url.indexOf('&')!==-1) url+='&';
                
                url+=decodeURI($.param(data));
                
                var form=$self.e('form[name="chbs-form"]');
                
                form.attr('action',url).submit();
            });

            /***/

            $(document).unbind('keydown').bind('keydown',function(e) 
            {
                switch($(e.target).attr('name'))
                {
                    case 'chbs_passenger_adult_service_type_1':
                    case 'chbs_passenger_adult_service_type_2':
                    case 'chbs_passenger_adult_service_type_3':
                    case 'chbs_passenger_children_service_type_1':
                    case 'chbs_passenger_children_service_type_2':
                    case 'chbs_passenger_children_service_type_3':    

                        if($.inArray(parseInt(e.keyCode,10),[38,40])>-1)
                        {
                            var value=parseInt($(e.target).val(),10);
                            if(isNaN(value)) value=0;

                            if(parseInt(e.keyCode,10)===38)
                                value=(value+1)>99 ? 99 : value+1;
                            else if(parseInt(e.keyCode,10)===40)
                                value=(value-1)<0 ? 0 : value-1;

                            $(e.target).val(parseInt(value));
                        }
                    
                    break;
                } 
            });

            /***/

            $self.createLabelTooltip();
            
            /***/

            $self.googleMapCreate();
            $self.googleMapInit();

            var firstOption=$self.e('[name="chbs_route_service_type_3"]>option:first');

            if(parseInt(firstOption.val(),10)===-1)
            {
                if(typeof($startLocation)!=='undefined')
                {
                    var data=[{lat:$startLocation.lat(),lng:$startLocation.lng()}];
                    firstOption.attr('data-coordinate',JSON.stringify(data));
                }
            }

            $self.googleMapCreateRoute(function()
            {
                if(parseInt(helper.urlParam('widget_submit'),10)===1)
                {
                    $self.goToStep(1,function()
                    {
                        $this.removeClass('chbs-hidden');
                        $(window).scroll();
                    });
                }
                else $this.removeClass('chbs-hidden');
                $self.googleMapStartCustomizeHeight();
            });
            if($this.closest('.advanced-search-wrapper-homepage').length !== 0){
                $this.closest('.advanced-search-wrapper-homepage').find('.gdlr-core-pbf-background.gdlr-core-parallax').css({
                    'height': $this.closest('.advanced-search-wrapper-homepage').height()
                })
            }
            $self.moveSubmitButton();
            $self.setDefaultPayment();
            $self.setBidPriceVehicle();
        };
		
		/**********************************************************************/

        this.createGeocodePoint = function(_input){
            var geocoder=new google.maps.Geocoder;
            geocoder.geocode({'address':_input.val()},function(result,status)
            {
                if((status==='OK') && (result[0]))
                {
                    var locationAddress=$self.removeDoubleQuote(result[0].formatted_address);

                    var placeData=
                        {
                            lat                                             :   result[0].geometry.location.lat(),
                            lng                                             :   result[0].geometry.location.lng(),
                            address                                         :   $self.removeDoubleQuote(result[0].formatted_address),
                            formatted_address                               :   result[0].formatted_address
                        };
                    // update address field value and place json entity into data field
                    _input.val(locationAddress);
                    _input.next('input').val(JSON.stringify(placeData));

                    $self.googleMapCreate();
                    $self.googleMapCreateRoute();
                }
            });
        }


		this.myLocationSuccess=function(position)
		{
			$self.e('.chbs-my-location-link').on('click',function()
			{
				var coordinate= 
				{
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};
									
                var field=$self.e('input[name="chbs_pickup_location_coordinate_service_type_1"]');
                
                field.val(JSON.stringify(coordinate));
         
                $self.googleMapSetAddress(field,function()
                {
                    $self.googleMapCreate();
                    $self.googleMapCreateRoute();                    
                },1);				
			});
		};
		
		/**********************************************************************/
		
		this.myLocationError=function(error)
		{
			console.log(error);
		};
		
		/**********************************************************************/
		
		this.getVehicleSelectedId=function()
		{
			var vehicleId=parseInt($self.e('.chbs-vehicle .chbs-vehicle-content-header .chbs-button.chbs-state-selected').parents('.chbs-vehicle').attr('data-id'),10);
			return(vehicleId);
		}
		
		/**********************************************************************/
		
		this.setBidPriceVehicle=function()
		{
			$self.e('.chbs-main-content-step-2')
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-content-price-bid>div:first-child>a',function(e)
			{
				e.preventDefault();
				
				$(this).parent('div').addClass('chbs-hidden');
				$(this).parent('div').next('div').removeClass('chbs-hidden');
			});
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-content-price-bid>div+div>input+a',function(e)
			{
				var input=$(this).prev('input');
				
                e.preventDefault();
                
                $self.setAction('vehicle_bid_price_check');
       
				$self.preloader(true);
                $self.post($self.e('form[name="chbs-form"]').serialize(),function(response)				
				{
					$self.preloader(false);
					if(typeof(response.html)!=='undefined')
					{
						if(typeof(response.bid_vehicle_min_value)!='undefined')
						{
							if(confirm(response.bid_question))
							{
								input.val(response.bid_vehicle_min_value);
								
								$self.preloader(true);
								$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)				
								{
									$self.preloader(false);
									if(typeof(response.html)!=='undefined')
									{
										$self.e('.chbs-summary-price-element').replaceWith(response.html);
									}
								});
							}
							else
							{
								$self.e('.chbs-summary-price-element').replaceWith(response.html);
								if(typeof(response.bid_notice)!='undefined') alert(response.bid_notice);
							}
						}
						else
						{
							$self.e('.chbs-summary-price-element').replaceWith(response.html);
							if(typeof(response.bid_notice)!='undefined') alert(response.bid_notice);
						}
					}
				});
			});
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-content-price-bid>div+div>input+a+a',function(e)
			{
				e.preventDefault();
				
				$(this).parent('div').addClass('chbs-hidden');
				$(this).parent('div').prev('div').removeClass('chbs-hidden');
				
				$(this).parent('div').find('input[type="text"]').val('');
				
                $self.setAction('vehicle_bid_price_check');
       
                $self.post($self.e('form[name="chbs-form"]').serialize(),function(response)				
				{
					if(typeof(response.html)!=='undefined')
						$self.e('.chbs-summary-price-element').replaceWith(response.html);
				});
			});		
		};
		
		/**********************************************************************/
		
		this.setDefaultPayment=function()
		{
			var paymentId=parseInt($self.e('input[name="chbs_payment_id"]').val(),10);
			if(paymentId>0) $self.e('.chbs-payment>li>a[data-payment-id="'+paymentId+'"]').addClass('chbs-state-selected');
		};
        
        /**********************************************************************/
        
        this.moveSubmitButton=function()
        {
            if(($this.hasClass('chbs-widget')) && ($this.hasClass('chbs-widget')) && ($this.hasClass('chbs-widget-style-2')))
            {
                var button=$self.e('.chbs-main-content-step-1 .chbs-button-widget-submit').parent();

                button.clone().appendTo($self.e('#panel-1'));
                button.clone().appendTo($self.e('#panel-2'));
                button.clone().appendTo($self.e('#panel-3'));
                
                button.remove();
            }
        };
        
        /**********************************************************************/
        
        this.convertTimeToMinute=function(time)
        {
            time=time.split(':');
            return(time[0]*60+time[1]);
        };
        
        /**********************************************************************/
        
        this.createLabelTooltip=function()
        {
            $self.e('.chbs-tooltip').qtip(
            {
                style           :
                {
                    classes     :	'chbs-qtip chbs-qtip-success'
                },
                position        :
                {
                    my          :	'bottom left',
                    at          :	'top left',
                    container   :   $this
                }
            });
        };
        
        /**********************************************************************/
        
        this.setTimepicker=function(field)
        {
            if(parseInt($option.timepicker_dropdown_list_enable,10)===1)
                $('.ui-timepicker-wrapper').css({opacity:1,'width':field.parent('div').outerWidth()+1});
        };
        
        /**********************************************************************/
        
        this.createSelectMenu=function()
        {
            $self.e('select:not(.chbs-selectmenu-disable)').selectmenu(
            {
                open: function(event,ui)
                {
                    var select=$(this);
                    var selectmenu=$('#'+select.attr('id')+'-menu').parent('div');
                    
                    var field=select.parents('.chbs-form-field:first');
                    
                    var left=parseInt(selectmenu.css('left'),10)-1;
                    
                    var borderWidth=parseInt(field.css('border-left-width'),10)+parseInt(field.css('border-right-width'),10);
                    
                    var width=field[0].getBoundingClientRect().width-borderWidth;
                    
                    selectmenu.css({width:width+2,left:left});
                },
                change: function(event,ui)
                {
                    var name=$(this).attr('name');
                    
                    if(name==='chbs_route_service_type_3')
                    {
                        $self.googleMapCreate();
                        $self.googleMapCreateRoute();
                    }
                    
                    if($.inArray(name,['chbs_transfer_type_service_type_1','chbs_transfer_type_service_type_3'])>-1)
                    {
                        var section=$self.e('[name="chbs_return_date_service_type_'+$self.getServiceTypeId()+'"]').parent('div').parent('div');
                        
                        if(parseInt($(this).val(),10)===3) {
                            section.removeClass('chbs-hidden');
                        }
                        else section.addClass('chbs-hidden');
                    }
                    
                    if($.inArray(name,['chbs_extra_time_service_type_1','chbs_transfer_type_service_type_1','chbs_duration_service_type_2','chbs_extra_time_service_type_3','chbs_transfer_type_service_type_3'])>-1)
                    {
                        $self.reCalculateRoute();
                    }
                    
                    if(name==='chbs_navigation_responsive')
                    {
                        var navigation=parseInt($(this).val(),10);
                        
                        var step=parseInt($self.e('input[name="chbs_step"]').val(),10);    
                
                        if(navigation-step===0) return;

                        $self.goToStep(navigation-step);
                    }
                    
                    if($.inArray(name,['chbs_vehicle_passenger_count','chbs_vehicle_bag_count','chbs_vehicle_standard','chbs_vehicle_category'])>-1)
                    {
                        $self.setAction('vehicle_filter');
                        
                        $self.e('.chbs-vehicle-list').children().addClass('chbs-hidden');
                        $self.e('.chbs-vehicle-list').addClass('chbs-preloader-1');
                        
                        $self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
                        {       
                            $self.getGlobalNotice().addClass('chbs-hidden');
                            
                            var vehicleList=$self.e('.chbs-vehicle-list');
                            
                            vehicleList.html('').removeClass('chbs-preloader-1');
                            
                            if((typeof(response.error)!=='undefined') && (typeof(response.error.global)!=='undefined'))
                            {
                                $self.getGlobalNotice().removeClass('chbs-hidden').html(response.error.global[0].message);
                            }
                            else
                            {
                                vehicleList.html(response.html);
                                $self.recalculateVehiclePrice(response,1);
                            }
                            
                            $self.e('.chbs-vehicle-list').find('.chbs-button.chbs-button-style-2').removeClass('chbs-state-selected');
                            
                            $self.preloadVehicleImage();
                            
                            $self.e('input[name="chbs_vehicle_id"]').val(0);
                            $self.createSummaryElement();
                        });
                    }
                    
                    if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_dropoff_service_type_1','chbs_fixed_location_pickup_service_type_2','chbs_fixed_location_dropoff_service_type_3']>-1))
                    {
                        $self.googleMapSetAddress($(this),function()
                        {
                            $self.googleMapCreate();
                            $self.googleMapCreateRoute();                    
                        });                      
                    }
                    
                    if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_pickup_service_type_2']>-1))
                    {
                        $self.checkFixedLocationPickup(name);
                    }                    
                    
                    if($.inArray(name,['chbs_fixed_location_dropoff_service_type_1']>-1))
                    {
                        $self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_1');
                    }  
                    
                    if($.inArray(name,['chbs_fixed_location_dropoff_service_type_2']>-1))
                    {
                        $self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_2');
                    }     
                }
            });
                        
            $self.e('.ui-selectmenu-button .ui-icon.ui-icon-triangle-1-s').attr('class','chbs-meta-icon-arrow-vertical-large'); 
            
            $self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_1');
            $self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_2');
            
            var fInedx=[1,3];
            
            for(var i in fInedx)
            {
                var transferType=$self.e('[name="chbs_transfer_type_service_type_'+fInedx[i]+'"]');
                if(transferType.length===1)
                {
                    if(parseInt(transferType.val(),10)===3)
                    {
                        var returnDate=$self.e('[name="chbs_return_date_service_type_'+fInedx[i]+'"]');
                        returnDate.parents('.chbs-form-field').parent('.chbs-clear-fix').removeClass('chbs-hidden');
                    }
                }    
            }
        };
            
        /**********************************************************************/
        
        this.getFixedLocationSource=function(item)
        {
            var fixedLocation=[];
			$(item).next('select').find('option:not([disabled="disabled"])').each(function(index2,item2)
            {
                fixedLocation.push({label:item2.text,value:item2.value});
            });      
           
            return(fixedLocation);
        };
        
        /**********************************************************************/
        
		this.createFixedLocationAutocomplete=function()
        {
			$self.e('.chbs-form-field-location-fixed-autocomplete').each(function(index,item)
            {
                var fixedLocation=$self.getFixedLocationSource(item);
				if(fixedLocation.length)
				{
					$(item).autocomplete(
                    {
						source                                                  :   fixedLocation,
						minLength                                               :   0,
						focus                                                   :   function(event,ui)
                        {
							event.preventDefault();
						},
						select                                                  :   $self.handleFixedLocationAutocompleteChange,
                        change                                                  :   $self.handleFixedLocationAutocompleteChange
					}).focus(function()
                    {
						$(this).autocomplete('search');
					});
                    
                    $.ui.autocomplete.filter=function(array,term)
                    {
                        var matcher=new RegExp('^'+$.ui.autocomplete.escapeRegex(term),'i');
                        return $.grep(array,function(value) 
                        {
                            return(matcher.test(value.label || value.value || value));
                        });
                    };
				}
			});
        };
        
        /**********************************************************************/
		
		this.handleFixedLocationAutocompleteChange=function(event,ui)
		{
			event.preventDefault();
			var $select=$(event.target).next('select'),
				name=$select.attr('name');

            if(ui.item==null)
            {
                $(event.target).val('');
                $select.val('');
            }
            else
            {
                $(event.target).val(ui.item.label);
                $select.val(ui.item.value);
            }
			
			if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_dropoff_service_type_1','chbs_fixed_location_pickup_service_type_2','chbs_fixed_location_dropoff_service_type_3']>-1))
			{
				$self.googleMapSetAddress($select,function()
				{
					$self.googleMapCreate();
					$self.googleMapCreateRoute();                    
				});                      
			}

			if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_pickup_service_type_2']>-1))
			{
				$self.checkFixedLocationPickup(name);
			}                    

			if($.inArray(name,['chbs_fixed_location_dropoff_service_type_1']>-1))
			{
				$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_1');
			}  

			if($.inArray(name,['chbs_fixed_location_dropoff_service_type_2']>-1))
			{
				$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_2');
			}
		};
        
        /**********************************************************************/
        
        this.checkFixedLocationPickup=function(pickupLocationFieldName)
        {
            var dropoffLocationFieldName=pickupLocationFieldName.replace('pickup','dropoff');
            
            var dropoffLocationField=$self.e('[name="'+dropoffLocationFieldName+'"]');
            
            dropoffLocationField.children('option').removeAttr('disabled');
            
            try
            {
                dropoffLocationField.selectmenu('refresh');
            }
            catch(e) {}
            
            /***/
            
            var dataPickupLocation=$self.e('select[name="'+pickupLocationFieldName+'"]').children('option:selected').attr('data-location');
            if(typeof(dataPickupLocation)=='undefined') return;
            
            dataPickupLocation=JSON.parse(dataPickupLocation);
            
            if(!dataPickupLocation.dropoff_disable.length) return;
            
            for(var i in dataPickupLocation.dropoff_disable)
                dropoffLocationField.children('option[value="'+dataPickupLocation.dropoff_disable[i]+'"]').attr('disabled','disabled').removeAttr('selected');
            
            try
            {
                dropoffLocationField.selectmenu('refresh');
            }
            catch(e) {}
            
			$self.e('.chbs-form-field-location-fixed-autocomplete').each(function(index,item)
            {
                var fixedLocation=$self.getFixedLocationSource(item);
                $(item).autocomplete({source:fixedLocation});
            });
        };
        
        /**********************************************************************/
        
        this.getServiceTypeId=function()
        {
            return(parseInt($self.e('.ui-tabs .ui-tabs-active').attr('data-id'),10));
        };
        
        /**********************************************************************/
        
        this.setServiceTypeId=function(serviceTypeId)
        {
            $self.e('input[name="chbs_service_type_id"]').val(serviceTypeId);
        };
        
        /**********************************************************************/
        /**********************************************************************/

        this.setAction=function(name)
        {
            $self.e('input[name="action"]').val('chbs_'+name);
        };

        /**********************************************************************/
        
        this.e=function(selector)
        {
            return($this.find(selector));
        };

        /**********************************************************************/

        this.recalculateVehiclePrice=function(response,previousStep)
        {
            if((parseInt(response.booking_summary_hide_fee,10)===1) && (parseInt(previousStep,10)===1))
            {
                var vehicle=[];

                $self.e('.chbs-vehicle-list>ul>li').each(function()
                {
                    var helper=new CHBSHelper();
                    var parent=$(this).children('div:first');

                    if((!helper.isEmpty(parent.attr('data-base_location_cooridnate_lat'))) && (!helper.isEmpty(parent.attr('data-base_location_cooridnate_lng'))))
                        vehicle.push({id:parent.attr('data-id'),lat:parent.attr('data-base_location_cooridnate_lat'),lng:parent.attr('data-base_location_cooridnate_lng')});
                });

                if(vehicle.length)
                {
                    $self.e('.chbs-vehicle-list').children().addClass('chbs-hidden');
                    $self.e('.chbs-vehicle-list').addClass('chbs-preloader-1');
                    
                    var j=0;
                    for(var i in vehicle)
                    {
                        $self.calculateBaseLocationDistance(function(baseLocationData)
                        {
                            j++;

                            var vehicleElement=$self.e('.chbs-vehicle-list .chbs-vehicle[data-id="'+baseLocationData.id+'"]');

                            vehicleElement.find('[name="chbs_base_location_vehicle_distance['+baseLocationData.id+']"]').val(baseLocationData.distance);
                            vehicleElement.find('[name="chbs_base_location_vehicle_return_distance['+baseLocationData.id+']"]').val(baseLocationData.return_distance);

                            if(j===vehicle.length)
                            {
                                $self.goToStep(0);
                                return;
                            }

                        },vehicle[i]);
                    }
                }
            }         
        };

        /**********************************************************************/
        
        this.goToStep=function(stepDelta,callback)
        {   
            $self.preloader(true);
            
            $self.setAction('go_to_step');
            
            var step=$self.e('input[name="chbs_step"]');
            var stepRequest=$self.e('input[name="chbs_step_request"]');
            if(typeof(window.tsg_analytics) !== 'undefined') {
                window.tsg_analytics.track_event('TransferForm', (stepDelta > 0) ? 'Next Button' : 'Previous Button', parseInt(stepRequest.val(), 10));
            }

            // var step_index = ( (parseInt(step.val(),10)+stepDelta) === 4) ? 5 : (parseInt(step.val(),10)+stepDelta);
            var step_index = (parseInt(step.val(),10)+stepDelta);
            stepRequest.val(step_index);
            
            $self.setServiceTypeId($self.getServiceTypeId());
            
            var serviceTypeId=$self.getServiceTypeId();
            
            if(parseInt(stepRequest.val(),10)===2)
            {
                var sum=parseInt($self.e('[name="chbs_passenger_adult_service_type_'+serviceTypeId+'"]').val(),10)+parseInt($self.e('[name="chbs_passenger_children_service_type_'+serviceTypeId+'"]').val(),10);
                if(sum>0) $self.e('[name="chbs_vehicle_passenger_count"]').val(sum);
            }

            var helper=new CHBSHelper();
            var data={};
            data.service_type_id=$self.getServiceTypeId();

            var coordinate=$self.e('[name="chbs_pickup_location_coordinate_service_type_'+data.service_type_id+'"]').val();
            if(!helper.isEmpty(coordinate))
            {
                var json=JSON.parse(coordinate);
                data.pickup_location_formatted_address=json.formatted_address;
                if(typeof(window.tsg_analytics) !== 'undefined') {
                    window.tsg_analytics.track_search(data.pickup_location_formatted_address, 'From', 1);
                }
            }
            var coordinate=$self.e('[name="chbs_dropoff_location_coordinate_service_type_'+data.service_type_id+'"]').val();
            if(!helper.isEmpty(coordinate))
            {
                var json=JSON.parse(coordinate);
                data.pickup_location_formatted_address=json.formatted_address;
                if(typeof(window.tsg_analytics) !== 'undefined') {
                    window.tsg_analytics.track_search(data.pickup_location_formatted_address, 'To', 1);
                }
            }

            $self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
            {   
                var previousStep=$self.e('input[name="chbs_step"]').val();
                
                response.step=parseInt(response.step,10);
                
                $self.getGlobalNotice().addClass('chbs-hidden');

                if(parseInt(response.step,10) !== 5) {
                    $self.e('.chbs-main-content>div').css('display','none');
                    $self.e('.chbs-main-content>div:eq(' + (response.step - 1) + ')').css('display', 'block');
                }
                
                $self.e('input[name="chbs_step"]').val(response.step);
                
                google.maps.event.trigger($googleMap,'resize');
                
                $self.e('select[name="chbs_navigation_responsive"]').val(response.step);
                $self.e('select[name="chbs_navigation_responsive"]').selectmenu('refresh');
                  
                if(parseInt(response.step,10)===1) {
                    $self.googleMapStartCustomizeHeight();
                }else{
                    $self.googleMapStopCustomizeHeight();
                }
             
                switch(parseInt(response.step,10))
                {
                    case 1:
                        $('.chbs-main-content-step-1 .chbs-summary').remove();
                        $('.chbs-main-content-step-1 .chbs-summary-price-element').remove();
                        $self.googleMapCustomizeHeight();
                        break;
                    case 2:
                        var vehicle_id_input = $self.e('input[name="chbs_vehicle_id"]');

                        $self.e('.chbs-main-content-step-2 .chbs-booking-route-title-wrapper').html(response.route_info);

                        $self.e('.chbs-main-content-step-2 .chbs-vehicle-filter>.chbs-form-field:first>div').html(response.vehicle_passenger_filter_field);
                        if(typeof(response.vehicle)!=='undefined')
                        {
                            $self.e('.chbs-main-content-step-2 .chbs-vehicle-list').removeClass('chbs-preloader-1');
                            $self.e('.chbs-main-content-step-2 .chbs-vehicle-list').html(response.vehicle);
                            
                            $self.recalculateVehiclePrice(response,previousStep);
                        }
                        if(typeof(response.booking_extra)!=='undefined') {
                            $self.e('.chbs-main-content-step-2 .chbs-booking-extra').html(response.booking_extra);
                        }

                        var transfer_type=$self.e('[name="chbs_transfer_type_service_type_'+data.service_type_id+'"]').val();
                        $self.e('.chbs-main-content-step-2 > .chbs-main-content-navigation-button .chbs-button-step-next').hide();
                        $self.e('.chbs-main-content-step-2 > .chbs-main-content-navigation-button .chbs-button-step-next.next_'+transfer_type).show();


                        if(typeof(response.booking_extra)!=='undefined') {
                            $self.e('.chbs-main-content-step-2 .chbs-booking-extra').html(response.booking_extra);
                        }

                        //select cheapest vehicle
                        if(vehicle_id_input.val() == "-1") {
                            var min_price = 0, selected_index = 0;
                            $('.chbs-main-content-step-2 .chbs-vehicle-list > .chbs-list-reset > li').each(function () {
                                var price_text = $(this).find('.chbs-vehicle-content-price').text(),
                                    _index = $(this).index();
                                price_text = price_text.replace('$', '');
                                price_text = parseInt(price_text.split('.')[0]);
                                if (min_price === 0 || min_price >= price_text) {
                                    min_price = price_text;
                                    selected_index = _index;
                                }
                            });
                            $('.chbs-main-content-step-2 .chbs-vehicle-list > .chbs-list-reset > li').eq(selected_index).find('.chbs-vehicle-content-header .chbs-button').trigger('click');
                        }else{
                            var _id = vehicle_id_input.val();
                            if($('.chbs-main-content-step-2 .chbs-vehicle-list .chbs-vehicle[data-id="'+_id+'"] .chbs-button').length !== 0) {
                                $('.chbs-main-content-step-2 .chbs-vehicle-list .chbs-button.chbs-state-selected').removeClass('chbs-state-selected');
                                $('.chbs-main-content-step-2 .chbs-vehicle-list .chbs-vehicle[data-id="' + _id + '"] .chbs-button').addClass('chbs-state-selected');
                            }else{
                                $('.chbs-main-content-step-2 .chbs-vehicle-list > .chbs-list-reset > li').eq(0).find('.chbs-vehicle-content-header .chbs-button').trigger('click');
                            }
                        }
                        //end select cheapest vehicle

                        // close extra items category list
                        if( $('.chbs-booking-extra-category-list').length !== 0) {
                            $('.chbs-booking-extra-category-list > div:first-child > a').trigger('click');
                        }
                        // end close extra items category list

                        $self.preloadVehicleImage();
                        $self.createVehicleGallery();
                        
                        $self.createSelectMenu();
                        $self.createFixedLocationAutocomplete();

                        $self.manageBookingExtra('.chbs-main-content-step-2');
                    break;
                        
                    case 3:
                        var vehicle_id_input = $self.e('input[name="chbs_vehicle_id_return"]');

                        $self.e('.chbs-main-content-step-3 .chbs-booking-route-title-wrapper').html(response.route_info);

                        $self.e('.chbs-main-content-step-3 .chbs-vehicle-filter>.chbs-form-field:first>div').html(response.vehicle_passenger_filter_field);

                        if(typeof(response.vehicle)!=='undefined')
                        {
                            $self.e('.chbs-main-content-step-3 .chbs-vehicle-list').removeClass('chbs-preloader-1');
                            $self.e('.chbs-main-content-step-3 .chbs-vehicle-list').html(response.vehicle);

                            $self.recalculateVehiclePrice(response,previousStep);
                        }

                        if(typeof(response.booking_extra)!=='undefined') {
                            $self.e('.chbs-main-content-step-3 .chbs-booking-extra').html(response.booking_extra);
                        }

                        //select cheapest vehicle
                        if(vehicle_id_input.val() == "-1") {
                            var min_price = 0,
                                selected_index = 0;

                            $('.chbs-main-content-step-3 .chbs-vehicle-list > .chbs-list-reset > li').each(function () {
                                var price_text = $(this).find('.chbs-vehicle-content-price').text(),
                                    _index = $(this).index();
                                price_text = price_text.replace('$', '');
                                price_text = parseInt(price_text.split('.')[0]);
                                if (min_price === 0 || min_price >= price_text) {
                                    min_price = price_text;
                                    selected_index = _index;
                                }
                            });
                            $('.chbs-main-content-step-3 .chbs-vehicle-list > .chbs-list-reset > li').eq(selected_index).find('.chbs-vehicle-content-header .chbs-button').trigger('click');
                        }else{
                            var _id = vehicle_id_input.val();
                            $('.chbs-main-content-step-3 .chbs-vehicle-list .chbs-button.chbs-state-selected').removeClass('chbs-state-selected');
                            $('.chbs-main-content-step-3 .chbs-vehicle-list .chbs-vehicle[data-id="'+_id+'"] .chbs-button').addClass('chbs-state-selected');
                        }
                        //end select cheapest vehicle

                        $self.preloadVehicleImage();
                        $self.createVehicleGallery();

                        $self.createSelectMenu();
                        $self.createFixedLocationAutocomplete();

                        $self.manageBookingExtra('.chbs-main-content-step-3');

                    break;
                    
                    case 4:
                        if(typeof(response.summary)!=='undefined') {
                            $self.e('.chbs-main-content-step-4>.chbs-layout-25x75 .chbs-layout-column-left:first').html(response.summary[0]);
                        }
                        if(typeof(response.booking_information)!=='undefined') {
                            $self.e('.chbs-main-content-step-4 .chbs-client-form').html(response.booking_information);
                        }
                        
                    break;
                }

                $self.createLabelTooltip();

                $(window).scroll();

                $('.qtip').remove();
                
                if(typeof(response.error)!=='undefined')
                {
                    if(typeof(response.error.global[0])!=='undefined')
                        $self.getGlobalNotice().removeClass('chbs-hidden').html(response.error.global[0].message);
					
                    if(typeof(response.error.local)!=='undefined')
                    {
                        for(var index in response.error.local)
                        {
                            var selector,object;
                            
                            var sName=response.error.local[index].field.split('-');

                            if(isNaN(sName[1])) {
                                selector = '[name="' + sName[0] + '"]:eq(0)';
                            }else {
                                selector = '[name="' + sName[0] + '[]"]:eq(' + sName[1] + ')';
                            }
                                    
                            object=$self.e(selector).prevAll('label');
                                 
                            object.qtip(
                            {
                                show                                            :	
                                { 
                                    target                                      :	$(this) 
                                },
                                style                                           :	
                                { 
                                    classes                                     :	(response.error===1 ? 'chbs-qtip chbs-qtip-error' : 'chbs-qtip chbs-qtip-success')
                                },
                                content                                         : 	
                                { 
                                    text                                        :	response.error.local[index].message 
                                },
                                position                                        : 	
                                { 
                                    my                                          :	($option.rtl_mode ? 'bottom right' : 'bottom left'),
                                    at                                          :	($option.rtl_mode ? 'top right' : 'top left'),
                                    container                                   :   object.parents('[name="chbs-form"]')
                                }
                            }).qtip('show');	
                        }
                    }
                }
                if(parseInt(response.step,10)===5)
                {
                    $self.e('.chbs-main-navigation-default').addClass('chbs-hidden');
                    $self.e('.chbs-main-navigation-responsive').addClass('chbs-hidden');
                    if(parseInt(response.cart_url,10)!==1){
                        window.location.href = response.cart_url;
                    }
                }
                
                var offset=20;
                
                if($('#wpadminbar').length===1) {
                    offset += $('#wpadminbar').height();
                }
                
                if(typeof(callback)!=='undefined'){
                    callback();
                }

                if(parseInt(response.step,10)!==5) {
                    $self.preloader(false);
                }
            });
        };
        
        /**********************************************************************/
        
        this.manageBookingExtra=function(parent_class)
        {
            var bookingExtraList=$self.e(parent_class+' .chbs-booking-extra-list');
            
            if(bookingExtraList.length!==1) return;

            var extra_val;
            if(parent_class === '.chbs-main-content-step-2'){
                extra_val = $self.e('input[name="chbs_booking_extra_id"]').val()
                extra_val = extra_val.split(',');
                for(var i = 0; i < extra_val.length; i++){
                    bookingExtraList.find('.chbs-button.chbs-button-style-2[data-value-arrival="'+extra_val[i]+'"]').addClass('chbs-state-selected').closest('li').addClass('active');
                }
            }else if(parent_class === '.chbs-main-content-step-3'){
                extra_val = $self.e('input[name="chbs_booking_extra_return_id"]').val()
                extra_val = extra_val.split(',');
                for(var i = 0; i < extra_val.length; i++){
                    bookingExtraList.find('.chbs-button.chbs-button-style-2[data-value-return="'+extra_val[i]+'"]').addClass('chbs-state-selected').closest('li').addClass('active');
                }
            }
            $self.createSummaryElement();
        };
        
        /**********************************************************************/
        
		this.post=function(data,callback)
		{
			$.post($option.ajax_url,data,function(response)
			{ 
				callback(response); 
			},'json');
		};    
        
        /**********************************************************************/
        
        this.preloader=function(action)
        {
            $self.e('#chbs-preloader').css('display',(action ? 'block' : 'none'));
        };
        
        /**********************************************************************/
        
        this.preloadVehicleImage=function()
        {
            $self.e('.chbs-vehicle-list .chbs-vehicle-image img').one('load',function()
            {
                $(this).parent('.chbs-vehicle-image').animate({'opacity':1},300);
            }).each(function() 
            {
                if(this.complete) $(this).load();
            });
        };
        
        /**********************************************************************/
        
        this.createVehicleGallery=function()
        {
            $self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-list .chbs-vehicle-image img',function(e)
            {
                e.preventDefault();
                
                var gallery=$(this).parents('.chbs-vehicle-image:first').nextAll('.chbs-vehicle-gallery');
                
                if(parseInt(gallery.length,10)===1)
                {
                    $.fancybox.open(gallery.find('img'));
                }
            });
        };
        
        /**********************************************************************/
        /**********************************************************************/
       
        this.googleMapStartCustomizeHeight=function()
        {
            if(parseInt($option.widget.mode,10)===1) return;

            if($googleMapHeightInterval>0) return;

            $googleMapHeightInterval=window.setInterval(function()
            {
                $self.googleMapCustomizeHeight();
            },500);
        };
        
        /**********************************************************************/
       
        this.googleMapStopCustomizeHeight=function()
        {
            if(parseInt($option.widget.mode,10)===1) return;
            
            clearInterval($googleMapHeightInterval);
            $self.e('#chbs_google_map').height('420px');
            
            $googleMapHeightInterval=0;
        };        
        
        /**********************************************************************/
       
        this.googleMapCustomizeHeight=function()
        {
            if(parseInt($option.widget.mode,10)===1) {
                return;
            }
            
            var rideInfo=$self.e('.chbs-ride-info');
            var columnLeft=$self.e('.chbs-main-content-step-1>.chbs-layout-50x50>.chbs-layout-column-left');

            var _height = ($(window).width() < 768) ? 420 : parseInt(columnLeft.actual('height'),10) - parseInt(rideInfo.actual('height'),10);
            $self.e('#chbs_google_map').height(_height);
            
            google.maps.event.trigger($googleMap,'resize');
        };
       
        /**********************************************************************/

        this.googleMapAutocompleteCreate=function(text)
        {
            if(text.is('[readonly]')) return;

            var id='chbs_location_'+(new CHBSHelper()).getRandomString(16);

            text.attr('id',id).on('keypress',function(e)
            {
                if(e.which===13)
                {
                    e.preventDefault();
                    return(false);
                }
            });

            text.on('change',function()
            {
                if(!$.trim($(this).val()).length)
                {
                    text.siblings('input[type="hidden"]').val('');

                    $self.googleMapCreate();
                    $self.googleMapCreateRoute();                    
                }
            });

            var option={};
            var helper=new CHBSHelper();
            var name=new String(text.attr('name'));

            if(name.indexOf('pickup')>-1)
            {
                if(parseInt($option.driving_zone.pickup.enable)===1)
                {
                    if((!helper.isEmpty($option.driving_zone.pickup.area.coordinate.lat)) && (!helper.isEmpty($option.driving_zone.pickup.area.coordinate.lat)) && (parseInt($option.driving_zone.pickup.area.radius,10)>=0))
                    {
                        var circle=new google.maps.Circle(
                        {
                            center                                              :   new google.maps.LatLng($option.driving_zone.pickup.area.coordinate.lat,$option.driving_zone.pickup.area.coordinate.lng),
                            radius                                              :   $option.driving_zone.pickup.area.radius*1000
                        });

                        option.strictBounds=true;
                        option.bounds=circle.getBounds();
                    }

                    if($option.driving_zone.pickup.country.length)
                    {
                        if($.inArray(-1,$option.driving_zone.pickup.country)===-1)
                        {
                            option.componentRestrictions={};
                            option.componentRestrictions.country=$option.driving_zone.pickup.country;
                        }
                    }
                }                
            }

            if(name.indexOf('dropoff')>-1)
            {
                if(parseInt($option.driving_zone.dropoff.enable,10)===1)
                {
                    if((!helper.isEmpty($option.driving_zone.dropoff.area.coordinate.lat)) && (!helper.isEmpty($option.driving_zone.dropoff.area.coordinate.lat)) && (parseInt($option.driving_zone.dropoff.area.radius,10)>=0))
                    {
                        var circle=new google.maps.Circle(
                        {
                            center                                              :   new google.maps.LatLng($option.driving_zone.dropoff.area.coordinate.lat,$option.driving_zone.dropoff.area.coordinate.lng),
                            radius                                              :   $option.driving_zone.dropoff.area.radius*1000
                        });                    

                        option.strictBounds=true;
                        option.bounds=circle.getBounds();
                    }

                    if($option.driving_zone.dropoff.country.length)
                    {
                        if($.inArray(-1,$option.driving_zone.dropoff.country)===-1)
                        {
                            option.componentRestrictions={};
                            option.componentRestrictions.country=$option.driving_zone.dropoff.country;
                        }
                    }
                }                
            }

            var autocomplete=new google.maps.places.Autocomplete(document.getElementById(id),option);
            autocomplete.addListener('place_changed',function(id)
            {
                var place=autocomplete.getPlace();

                if(!place.geometry)
                {
                    alert($option.message.place_geometry_error);
                    text.val('');
                    return(false);
                }

                var placeData=
                {
                    lat                                                         :   place.geometry.location.lat(),
                    lng                                                         :   place.geometry.location.lng(),
                    formatted_address                                           :   $self.removeDoubleQuote(text.val())
                };

                var field=text.siblings('input[type="hidden"]');

                field.val(JSON.stringify(placeData));

                $self.googleMapSetAddress(field,function()
                {
                    $self.googleMapCreate();
                    $self.googleMapCreateRoute();                    
                });
            });
        };
        
        /**********************************************************************/
        /**********************************************************************/        
        
        this.googleMapInit=function()
        {
            if(!$self.googleMapExist()) return;
            
            if(parseInt($option.gooogle_map_option.default_location.type,10)===1)
            {
                if(navigator.geolocation) 
                {
                    $self.googleMapSetDefaultLocation();
                    
                    navigator.geolocation.getCurrentPosition(function(position)
                    {
                        $startLocation=new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                        $googleMap.setCenter($startLocation);
                    },
                    function()
                    {
                        $self.googleMapSetDefaultLocation();
                    });
                } 
                else
                {
                    $self.googleMapSetDefaultLocation();
                }
            }
            else $self.googleMapSetDefaultLocation();
        };
        
        /**********************************************************************/
        
        this.googleMapSetDefaultLocation=function()
        {
            if(typeof($startLocation)==='undefined')
                $startLocation=new google.maps.LatLng($option.gooogle_map_option.default_location.coordinate.lat,$option.gooogle_map_option.default_location.coordinate.lng);
            
            if($self.getServiceTypeId()===3) return;
            
            var helper=new CHBSHelper();
                     
            var coordinate=[];
            
            coordinate[0]=$self.e('[name="chbs_pickup_location_coordinate_service_type_'+$self.getServiceTypeId()+'"]').val();
            coordinate[1]=$self.e('[name="chbs_dropoff_location_coordinate_service_type_'+$self.getServiceTypeId()+'"]').val();
            
            if((!helper.isEmpty(coordinate[0])) && (!helper.isEmpty(coordinate[1])))
                $startLocation=new google.maps.LatLng(coordinate[0],coordinate[1]);

            $googleMap.setCenter($startLocation); 
        };
        
        /**********************************************************************/
        
        this.googleMapCreate=function()
        {
            if($self.e('#chbs_google_map').length!==1) return;
            
            $directionsRenderer=new google.maps.DirectionsRenderer();
            $directionsService=new google.maps.DirectionsService();
                                  
            var option= 
            {
                draggable                                                       :	$option.gooogle_map_option.draggable.enable,
                scrollwheel                                                     :	$option.gooogle_map_option.scrollwheel.enable,
                mapTypeId                                                       :	google.maps.MapTypeId[$option.gooogle_map_option.map_control.id],
                mapTypeControl                                                  :	$option.gooogle_map_option.map_control.enable,
                mapTypeControlOptions                                           :	
                {
                    style                                                       :	google.maps.MapTypeControlStyle[$option.gooogle_map_option.map_control.style],
                    position                                                    :	google.maps.ControlPosition[$option.gooogle_map_option.map_control.position]
                },
                zoom                                                            :	$option.gooogle_map_option.zoom_control.level,
                zoomControl                                                     :	$option.gooogle_map_option.zoom_control.enable,
                zoomControlOptions                                              :	
                {
                    position                                                    :	google.maps.ControlPosition[$option.gooogle_map_option.zoom_control.position]
                },
                streetViewControl                                               :   false
            };
                     
            $googleMap=new google.maps.Map($self.e('#chbs_google_map')[0],option);
                              
            if(parseInt($option.gooogle_map_option.traffic_layer.enable,10)===1)
            {
                var trafficLayer=new google.maps.TrafficLayer();
                trafficLayer.setMap($googleMap);
            }
            
            $directionsRenderer.setMap($googleMap);

            if($self.googleMapDraggableLocationAllowed())
            {
                $directionsRenderer.setOptions(
                {
                    draggable                                                   :   true,
                    suppressMarkers                                             :   false
                });
            }
            else
            {
                $directionsRenderer.setOptions(
                {
                    draggable                                                   :   false,
                    suppressMarkers                                             :   true
                });
            }

            if($self.googleMapDraggableLocationAllowed())
            {
                $googleMap.addListener('click',function(event) 
                {
                    var helper=new CHBSHelper();
                    
                    var pickupText=$self.e('[name="chbs_pickup_location_service_type_'+$self.getServiceTypeId()+'"]');
                    var pickupField=pickupText.siblings('input[type="hidden"]');
                    
                    var dropoffText=$self.e('[name="chbs_dropoff_location_service_type_'+$self.getServiceTypeId()+'"]');
                    var dropoffField=dropoffText.siblings('input[type="hidden"]');

                    if((!helper.isEmpty(pickupField.val())) && (!helper.isEmpty(dropoffField.val()))) return;

                    var geocoder=new google.maps.Geocoder;
                    geocoder.geocode({'location':event.latLng},function(result,status) 
                    {
                        if((status==='OK') && (result[0]))
                        {
                            var locationAddress=$self.removeDoubleQuote(result[0].formatted_address);

                            var placeData=
                            {
                                lat                                             :   event.latLng.lat(),
                                lng                                             :   event.latLng.lng(),
                                address                                         :   $self.removeDoubleQuote(result[0].formatted_address),
                                formatted_address                               :   result[0].formatted_address
                            };

                            if(helper.isEmpty(pickupField.val()))
                            {
                                pickupText.val(locationAddress);
                                pickupField.val(JSON.stringify(placeData));
                            }
                            else if(helper.isEmpty(dropoffField.val()))
                            {
                                dropoffText.val(locationAddress);
                                dropoffField.val(JSON.stringify(placeData));
                            }

                            $self.googleMapCreate();
                            $self.googleMapCreateRoute();
                        }
                    });
                });

                $directionsRenderer.addListener('directions_changed',function()
                {
                    var helper=new CHBSHelper();
                    
                    var geocoder=new google.maps.Geocoder;
                    var directions=$directionsRenderer.getDirections();
                    var route=directions.routes[0];
                    var routePoints=[];
                    
                    route.legs.forEach(function(item,index)
                    {
                        if(parseInt(index,10)===0)
                        {
                            routePoints.push(
                            {
                                'lat'                                           :   item.start_location.lat(),
                                'lng'                                           :   item.start_location.lng()
                            });
                        }
                        item.via_waypoints.forEach(function(item2,index2) 
                        {
                            routePoints.push(
                            {
                                'lat'                                           :   item2.lat(),
                                'lng'                                           :   item2.lng()
                            });
                        });
                        routePoints.push(
                        {
                            'lat'                                               :   item.end_location.lat(),
                            'lng'                                               :   item.end_location.lng()
                        });
                    });

                    var routeLength=routePoints.length;

                    var waypoints=$self.e('[name="chbs_waypoint_location_service_type_'+$self.getServiceTypeId()+'[]"]');

                    var locationFields=[];
                    locationFields.push($self.e('[name="chbs_pickup_location_service_type_'+$self.getServiceTypeId()+'"]'));
                    
                    $self.e('[name="chbs_waypoint_location_service_type_'+$self.getServiceTypeId()+'[]"]').each(function(index,waypointField) 
                    {
                        if(index>0) locationFields.push($(waypointField));
                    });
                    
                    locationFields.push($self.e('[name="chbs_dropoff_location_service_type_'+$self.getServiceTypeId()+'"]'));
                    
                    var locationFieldsLength=locationFields.length;

                    if(routeLength>locationFieldsLength)
                    {
                        var waypointFound;
                        for(var i=1;i<routeLength-1;i++)
                        {
                            waypointFound=false;
                            waypoints.each(function(j,obj) 
                            {
                                if(j>0)
                                {
                                    var waypointHidden=$(obj).siblings('input[type="hidden"]').val();
                         
                                    if(!helper.isEmpty(waypointHidden))
                                    {
                                        var waypointData=JSON.parse(waypointHidden);
                                        if((waypointData.lat==routePoints[i].lat) && (waypointData.lng==routePoints[i].lng))
                                        {
                                            waypointFound=true;
                                            return(false);
                                        }
                                    }
                                }
                            });
                            if(!waypointFound)
                            {
                                var pointIndex=i;
                                
                                geocoder.geocode({'location':new google.maps.LatLng(routePoints[pointIndex].lat,routePoints[pointIndex].lng)},function(result,status) 
                                {
                                    if(!((status==='OK') && (result[0]))) return;

                                    var locationAddress=$self.removeDoubleQuote(result[0].formatted_address);

                                    var waypointData=
                                    {
                                        lat                                     :   result[0].geometry.location.lat(),
                                        lng                                     :   result[0].geometry.location.lng(),
                                        address                                 :   $self.removeDoubleQuote(result[0].formatted_address),
                                        formatted_address                       :   result[0].formatted_address
                                    };
                                    
                                    $self.e('.chbs-location-add').eq(pointIndex).trigger('click');
                                    
                                    var newWypoint=$self.e('[name="chbs_waypoint_location_service_type_'+$self.getServiceTypeId()+'[]"]').eq(pointIndex);
                                    var newWypointField=newWypoint.siblings('input[type="hidden"]');

                                    newWypoint.val(locationAddress);
                                    newWypointField.val(JSON.stringify(waypointData));

                                    $self.googleMapCreate();
                                    $self.googleMapCreateRoute();
                                });
                            }
                        }
                    }
                    else
                    {
                        var pointMoved=false;
                        var routePointsIndex=0;
                        
                        locationFields.forEach(function(locationField,index) 
                        {
                            var helper=new CHBSHelper();
                            var locationData=locationField.siblings('input[type="hidden"]').val();

                            if((helper.isEmpty(locationData)) && (locationFieldsLength===2) && (index===1) && !((routePoints[0].lat==routePoints[1].lat) && (routePoints[0].lng==routePoints[1].lng)))
                            {
                                pointMoved=locationField;
                                var placeData=
                                {
                                    lat                                         :   routePoints[1].lat,
                                    lng                                         :   routePoints[1].lng
                                };
                                
                                locationField.siblings('input[type="hidden"]').val(JSON.stringify(placeData));                            
                            }
                            else if(!helper.isEmpty(locationData))
                            {
                                locationData=JSON.parse(locationData);
                                if(!(locationData.lat==routePoints[routePointsIndex].lat && locationData.lng==routePoints[routePointsIndex].lng))
                                {
                                    pointMoved=locationField;
                                    locationData.lat=routePoints[routePointsIndex].lat;
                                    locationData.lng=routePoints[routePointsIndex].lng;
                                    locationField.siblings('input[type="hidden"]').val(JSON.stringify(locationData));
                                }
                                routePointsIndex++;
                            }
                        });

                        if(pointMoved!=false)
                        {
                            var pointDetails=JSON.parse(pointMoved.siblings('input[type="hidden"]').val());
                            geocoder.geocode({'location':new google.maps.LatLng(pointDetails.lat,pointDetails.lng)},function(result,status) 
                            {
                                if((status==='OK') && (result[0]))
                                {
                                    var locationAddress=$self.removeDoubleQuote(result[0].formatted_address);
                                    pointMoved.val(locationAddress);
                                    pointDetails.formatted_address=locationAddress;
                                    pointMoved.siblings('input[type="hidden"]').val(JSON.stringify(pointDetails));
                                }
                            });

                            $self.googleMapCreate();
                            $self.googleMapCreateRoute();
                        }
                    }
                });
            }             
        };
        
        /**********************************************************************/
        
        this.getCoordinate=function()
        {
            var helper=new CHBSHelper();
            var coordinate=new Array();
            
            var serviceTypeId=$self.getServiceTypeId();
            var panelField=$self.e('#panel-'+(serviceTypeId)).children('.chbs-form-field-location-autocomplete,.chbs-form-field-location-fixed');
            
            if(serviceTypeId===1 || serviceTypeId===2)
            {
                panelField.each(function()
                {
                    if((serviceTypeId===2) && ($(this).hasClass('chbs-form-field-location-autocomplete')))
                    {
                        if($(this).children('input[name="chbs_dropoff_location_service_type_2"]').length===1) return(true);
                    }
                    
                    var c;
                    
                    try
                    {
                        if($(this).hasClass('chbs-form-field-location-autocomplete'))
                            c=JSON.parse($(this).children('input[type="hidden"]').val());
                        else 
                        {
							if(($(this).find('input.chbs-form-field-location-fixed-autocomplete').length===0) || ($(this).find('input.chbs-form-field-location-fixed-autocomplete').val().length))
							{
								c=JSON.parse($(this).find('select>option:selected').attr('data-location'));
							}
							else c={lat:'',lng:''};
                        }
                    }
                    catch(e)
                    {
                        c={lat:'',lng:''};
                    }
                
                    if((!helper.isEmpty(c.lat)) && (!helper.isEmpty(c.lng)))
                        coordinate.push(new google.maps.LatLng(c.lat,c.lng));
                });
            }
            else
            {
                var option=$self.e('select[name="chbs_route_service_type_3"]>option:selected');
                
                if(option.length===1) 
                {
                    var data=JSON.parse(option.attr('data-coordinate'));

                    for(var i in data)
                    {
                        if((!helper.isEmpty(data[i].lat)) && (!helper.isEmpty(data[i].lng)))
                            coordinate.push(new google.maps.LatLng(data[i].lat,data[i].lng));                    
                    }
                }
            }    
            
            return(coordinate);
        };
        
        /**********************************************************************/
        
        this.googleMapExist=function()
        {
            return(typeof($googleMap)==='undefined' ? false : true); 
        };
        
        /**********************************************************************/
        
        this.googleMapDraggableLocationAllowed=function()
        {
            // var serviceTypeId=$self.getServiceTypeId();
            //
            // var fixedFieldLength=parseInt($self.e('#panel-'+(serviceTypeId)).children('.chbs-form-field-location-fixed').length,10);
            //
            // return((fixedFieldLength===0) && (parseInt($option.gooogle_map_option.draggable_location.enable,10)===1) && (parseInt($self.getServiceTypeId(),10)===1) && ($self.e('[name="chbs_waypoint_location_service_type_'+$self.getServiceTypeId()+'[]"]').length));
        };
    
        /**********************************************************************/
        
        this.googleMapCreateRoute=function(callback)
        { 
            var serviceTypeId=$self.getServiceTypeId();
            
            if(!$self.googleMapExist())
            {
                if(typeof(callback)!=='undefined') callback();
                return;
            }
            
            var request;
            
            var panelField=$self.e('#panel-'+(serviceTypeId)).children('.chbs-form-field-location-autocomplete');
           
            var coordinate=$self.getCoordinate();
            var length=coordinate.length;
        
            if(length===0)
            {
                $self.googleMapReInit();
                
                if(typeof(callback)!=='undefined') callback();
                return;
            }

            if(serviceTypeId===2)
            {
                if(length===2)
                {
                    coordinate=[coordinate[0]];
                    length=1;
                }
            }

            if(length>2)
            {
                var waypoint=new Array();
                
                coordinate.forEach(function(item,i) 
                {
                    if((i>0) && (i<length-1))
                        waypoint.push({location:item,stopover:true});
                });
                
                request= 
                {
                    origin                                                      :   coordinate[0],
                    waypoints                                                   :   waypoint,
                    optimizeWaypoints                                           :   true,
                    destination                                                 :   coordinate[length-1],
                    travelMode                                                  :   google.maps.DirectionsTravelMode.DRIVING
                };                     
            }
            else if(length===2)
            {
                request= 
                {
                    origin                                                      :   coordinate[0],
                    destination                                                 :   coordinate[length-1],
                    travelMode                                                  :   google.maps.DirectionsTravelMode.DRIVING
                };          
            }
            else
            {
                request= 
                {
                    origin                                                      :   coordinate[length-1],
                    destination                                                 :   coordinate[length-1],
                    travelMode                                                  :   google.maps.DirectionsTravelMode.DRIVING
                };              
            }
            
            request.avoidTolls=$.inArray('tolls',$option.gooogle_map_option.route_avoid)>-1 ? true : false;
            request.avoidFerries=$.inArray('ferries',$option.gooogle_map_option.route_avoid)>-1 ? true : false;
            request.avoidHighways=$.inArray('highways',$option.gooogle_map_option.route_avoid)>-1 ? true : false;
            
            $directionsService.route(request,function(response,status)
            {              
                $self.googleMapClearMarker();
                
                if(status===google.maps.DirectionsStatus.OK)
                {
                    if($self.googleMapDraggableLocationAllowed())
                    {
                        var helper=new CHBSHelper();
                        var route=response.routes[0];
                        var routePoints=[];
                        
                        route.legs.forEach(function(item,index) 
                        {
                            if(index===0)
                            {
                                routePoints.push(
                                {
                                    'lat'                                       :   item.start_location.lat(),
                                    'lng'                                       :   item.start_location.lng(),
                                });
                            }
                            item.via_waypoints.forEach(function(item2,index2) 
                            {
                                routePoints.push(
                                {
                                    'lat'                                       :   item2.lat(),
                                    'lng'                                       :   item2.lng()
                                });
                            });
                            routePoints.push(
                            {
                                'lat'                                           :   item.end_location.lat(),
                                'lng'                                           :   item.end_location.lng(),
                            });
                        });

                        var locationFields=[];
                        locationFields.push($self.e('[name="chbs_pickup_location_service_type_'+$self.getServiceTypeId()+'"]'));
                        $self.e('[name="chbs_waypoint_location_service_type_'+$self.getServiceTypeId()+'[]"]').each(function(index,waypointField)
                        {
                            if(index>0) locationFields.push($(waypointField));
                        });
                        
                        locationFields.push($self.e('[name="chbs_dropoff_location_service_type_'+$self.getServiceTypeId()+'"]'));
                        
                        var routePointsIndex=0;
                        locationFields.forEach(function(locationField,index) 
                        {
                            var locationData=locationField.siblings('input[type="hidden"]').val();
                            if(!helper.isEmpty(locationData))
                            {
                                locationData=JSON.parse(locationData);
                                locationData.lat=routePoints[routePointsIndex].lat;
                                locationData.lng=routePoints[routePointsIndex].lng;
                                locationField.siblings('input[type="hidden"]').val(JSON.stringify(locationData));
                                routePointsIndex++;
                            }
                        });
                    }           
                          
                    $directionsRenderer.setDirections(response);
                    
                    $directionsServiceResponse=response;
 
                    for(var i in response.routes[0].legs)
                    {
                        var leg=response.routes[0].legs[i];

                        $self.googleMapCreateMarker(leg.start_location);
                        $self.googleMapCreateMarker(leg.end_location); 
                    }
                
                    $googleMap.setCenter($directionsRenderer.getDirections().routes[0].bounds.getCenter());
                             
                    $self.calculateRoute(response);
                }
                else if(status===google.maps.DirectionsStatus.ZERO_RESULTS)
                {
                    if(serviceTypeId===1)
                    {
                        alert($option.message.designate_route_error);
                        
                        panelField.each(function()
                        {
                            $(this).children('input[type="text"]').val('');
                            $(this).children('input[type="hidden"]').val('');
                        }); 
                        
                        $self.googleMapReInit();
                    }
                }
                
                if(typeof(callback)!=='undefined') callback();
            });            
        };
        
        /**********************************************************************/
        
        this.googleMapClearMarker=function()
        {
            for(var i in $marker)
                $marker[i].setMap(null);
            
            $marker=[];
        };
        
        /**********************************************************************/
        
        this.googleMapCreateMarker=function(position)
        {
            if($self.googleMapDraggableLocationAllowed())
                return;
                        
            for(var i in $marker)
            {
                if(($marker[i].position.lat()==position.lat()) && ($marker[i].position.lng()==position.lng())) {
                    return;
                }
            }
            
            var label=$marker.length+1;
            
            var marker=new google.maps.Marker(
            {
                position                                                        :   position,
                map                                                             :   $googleMap,
                label                                                           :   ''+label
            });        
            
            $marker.push(marker);
        };
        
        /**********************************************************************/
        
        this.googleMapReInit=function()
        {
            if(!$self.googleMapExist()) {
                return;
            }
            
            $directionsRenderer=new google.maps.DirectionsRenderer();
            $directionsService=new google.maps.DirectionsService();
            
            $directionsServiceResponse=null;
            
            $directionsRenderer.setDirections({routes:[]});
            
            $googleMap.setZoom($option.gooogle_map_option.zoom_control.level);
                            
            $self.calculateRoute();
                    
            if($startLocation!==null) {
                $googleMap.setCenter($startLocation);
            }
        };

        /**********************************************************************/
        
        this.googleMapSetAddress=function(field,callback,autosugestionAdressType=-1)
        {
            var coordinate;
            var helper=new CHBSHelper();
            
            if(field.prop('tagName').toLowerCase()==='select')
            {
                callback();
                return;
            }
            else coordinate=JSON.parse(field.val());
            
			if(autosugestionAdressType===-1) {
                autosugestionAdressType = parseInt($option.google_autosugestion_address_type, 10);
            }
			
            if((helper.isEmpty(coordinate.lat)) || (helper.isEmpty(coordinate.lng))) {
                return;
            }

            var geocoder = new google.maps.Geocoder();
            
            geocoder.geocode({'location':new google.maps.LatLng(coordinate.lat,coordinate.lng)},function(result,status) 
            {
                if((status==='OK') && (result[0]))
                {
                    coordinate.address=$self.removeDoubleQuote(result[0].formatted_address);
                    
                    if(helper.isEmpty(coordinate.formatted_address))
                        coordinate.formatted_address=result[0].formatted_address;
            
                    if(autosugestionAdressType===1)
                    {
                        var textField=field.parent('.chbs-form-field-location-autocomplete').children('input[type="text"]');
                        if(textField.length===1) textField.val(coordinate.address);
                    }
                        
                    field.val(JSON.stringify(coordinate));
                    callback();
                }
            });            
        };
        
        /**********************************************************************/
        
        this.calculateRoute=function(response)
        {
            var distance=0;
            var duration=0;
            
            if((typeof(response)!=='undefined') && (typeof(response.routes)!=='undefined'))
            {
                for(var i=0;i<response.routes[0].legs.length;i++) 
                {
                    distance+=response.routes[0].legs[i].distance.value;
                    duration+=response.routes[0].legs[i].duration.value;
                }
            }
            
            distance/=1000;
            duration=Math.ceil(duration/60);
            
            $self.e('input[name="chbs_distance_map"]').val(Math.round(distance*10)/10);
            $self.e('input[name="chbs_duration_map"]').val(duration*$option.ride_time_multiplier);
            
            $self.reCalculateRoute();
        };
        
        /**********************************************************************/
        
        this.reCalculateRoute=function()
        {
            var duration=0;
            var distance=0;
            
            var serviceTypeId=parseInt($self.e('input[name="chbs_service_type_id"]').val(),10);
            
            distance=$self.e('input[name="chbs_distance_map"]').val();
            
            switch(serviceTypeId)
            {
                case 1:
                
                    duration=$self.e('select[name="chbs_extra_time_service_type_1"]').val();
                    if(isNaN(duration)) duration=0; 
                    
                    duration*=($option.extra_time_unit===2 ? 60 : 1);
                    
                break;
                
                case 2:
                    
                    duration=$self.e('select[name="chbs_duration_service_type_2"]').val();
                    if(isNaN(duration)) duration=0;
                    
                    duration*=60;
                    
                break;
                
                case 3:
                    
                    duration=$self.e('select[name="chbs_extra_time_service_type_3"]').val();
                    if(isNaN(duration)) duration=0; 
                    
                    duration*=($option.extra_time_unit===2 ? 60 : 1);
                    
                break;
            }
            
            if($.inArray(serviceTypeId,[1,3])>-1)
            {
                var transferType=$self.e('select[name="chbs_transfer_type_service_type_'+serviceTypeId+'"]');
                // var transferTypeValue=transferType.length===1 ? (parseInt(transferType.val(),10)===1 ? 1 : 2) : 1;
                var transferTypeValue=1;

                duration+=(parseInt($self.e('input[name="chbs_duration_map"]').val(),10)*transferTypeValue);
                distance*=transferTypeValue;
            }
            
            $self.e('input[name="chbs_distance_sum"]').val(distance);
            $self.e('input[name="chbs_duration_sum"]').val(duration);
            
            var sDuration=$self.splitTime(duration);
            
            distance=$self.formatLength(distance);
                
            $self.e('.chbs-ride-info>div:eq(0)>span:eq(2)>span:eq(0)').html(distance);
            $self.e('.chbs-ride-info>div:eq(1)>span:eq(2)>span:eq(0)').html(sDuration[0]);
            $self.e('.chbs-ride-info>div:eq(1)>span:eq(2)>span:eq(2)').html(sDuration[1]);  
            
            $self.calculateBaseLocationDistance();
            $self.googleMapCustomizeHeight();
        };
        
        /**********************************************************************/
        
        this.formatLength=function(length)
        {
            if($option.length_unit===2)
            {   
                length/=1.609344;
                length=Math.round(length*10)/10;
            }
            
            return(length);
        };
        
        /**********************************************************************/
        
        this.splitTime=function(time)
        {
            return([Math.floor(time/60),time%60]);
        };
        
		/**********************************************************************/

        this.createSummaryElement=function()
        {
            $self.preloader(true);
            $self.setAction('create_summary_price_element');

            $self.post($('body').find('form[name="chbs-form"]').serialize(),function(response)
            {
                $self.e('.chbs-summary, .chbs-summary-price-element').remove();
                $self.e('.chbs-layout-column-left').append(response.html);
                $(window).scroll();
                $self.preloader(false);
            });
        };
        
        /**********************************************************************/
        
        this.getGlobalNotice=function()
        {
            var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
            return($self.e('.chbs-main-content-step-'+step+' .chbs-notice'));
        };
        
        /**********************************************************************/
        
        this.calculateBaseLocationDistance=function(callback,coordinate)
        {
            if(typeof(coordinate)=='undefined') coordinate=false;
            
            var helper=new CHBSHelper();
            
            var baseLocation;
            var baseLocationData={distance:0,duration:0,return_distance:0,return_duration:0};
            
            if(coordinate===false)
            {
                $self.e('input[name="chbs_base_location_distance"]').val(0);
                $self.e('input[name="chbs_base_location_duration"]').val(0);
                $self.e('input[name="chbs_base_location_return_distance"]').val(0);
                $self.e('input[name="chbs_base_location_return_duration"]').val(0);

                baseLocation={coordinate:{lat:$option.base_location.coordinate.lat,lng:$option.base_location.coordinate.lng}};

                var vehicleId=$self.e('input[name="chbs_vehicle_id"]').val();
                var vehicle=$self.e('.chbs-vehicle-list .chbs-vehicle[data-id="'+vehicleId+'"]');

                if(vehicle.length===1)
                {
                    if((!helper.isEmpty(vehicle.attr('data-base_location_cooridnate_lat'))) && (!helper.isEmpty(vehicle.attr('data-base_location_cooridnate_lng'))))
                    {
                        baseLocation.coordinate.lat=vehicle.attr('data-base_location_cooridnate_lat');
                        baseLocation.coordinate.lng=vehicle.attr('data-base_location_cooridnate_lng');
                    }
                }
            }
            else
            {
                baseLocationData.id=coordinate.id;
                
                baseLocation={coordinate:{lat:coordinate.lat,lng:coordinate.lng}};
            }
           
            if((helper.isEmpty(baseLocation.coordinate.lat)) || (helper.isEmpty(baseLocation.coordinate.lng)))
            {
                $self.callback(callback,baseLocationData);
                return(baseLocationData);
            }
            
            var request;
            var routeCoordinate=$self.getCoordinate();
            var directionsService=new google.maps.DirectionsService();
            
            /***/
            
            if(parseInt(routeCoordinate.length,10)===0)
            {
                $self.callback(callback,baseLocationData);
                return(baseLocationData);
            }
            
            request= 
            {
                origin                                                          :   routeCoordinate[0],
                destination                                                     :   new google.maps.LatLng(baseLocation.coordinate.lat,baseLocation.coordinate.lng),
                travelMode                                                      :   google.maps.DirectionsTravelMode.DRIVING
            };   
            directionsService.route(request,function(response,status)
            {
                if(status===google.maps.DirectionsStatus.OK)
                {
                    var distance=0;
                    var duration=0;
                    
                    for(var i=0;i<response.routes[0].legs.length;i++) 
                    {
                        distance+=response.routes[0].legs[i].distance.value;
                        duration+=response.routes[0].legs[i].duration.value;
                    }
                    
                    distance/=1000;
                    distance=Math.round(distance*10)/10;
                    
                    duration=Math.ceil(duration/60);
            
                    if(coordinate===false)
                    {
                        $self.e('input[name="chbs_base_location_distance"]').val(distance);
                        $self.e('input[name="chbs_base_location_duration"]').val(duration);
                    }
                    else
                    {
                        baseLocationData.distance=distance;
                        baseLocationData.duration=duration;
                    }
                    
                    if(routeCoordinate.length>1)
                    {
                        var transferTypeId=1;
                        var serviceTypeId=$self.getServiceTypeId();

                        if($.inArray(serviceTypeId,[1,3])>-1)
                        {
                            var transferType=$self.e('select[name="chbs_transfer_type_service_type_'+serviceTypeId+'"]');
                            transferTypeId=transferType.length===1 ? parseInt(transferType.val(),10) : 1;
                        }

                        request= 
                        {
                            origin                                              :   $.inArray(transferTypeId,[1,3])>-1 ? routeCoordinate[routeCoordinate.length-1] : routeCoordinate[0],
                            destination                                         :   new google.maps.LatLng(baseLocation.coordinate.lat,baseLocation.coordinate.lng),
                            travelMode                                          :   google.maps.DirectionsTravelMode.DRIVING
                        };   
                        directionsService.route(request,function(response,status)
                        {
                            if(status===google.maps.DirectionsStatus.OK)
                            {
                                var distance=0;
                                var duration=0;

                                for(var i=0;i<response.routes[0].legs.length;i++) 
                                {
                                    distance+=response.routes[0].legs[i].distance.value;
                                    duration+=response.routes[0].legs[i].duration.value;
                                }

                                distance/=1000;
                                distance=Math.round(distance*10)/10;
                                        
                                duration=Math.ceil(duration/60);

                                if(coordinate===false)
                                {
                                    $self.e('input[name="chbs_base_location_return_distance"]').val(distance);
                                    $self.e('input[name="chbs_base_location_return_duration"]').val(duration);
                                }
                                else
                                {
                                    baseLocationData.return_distance=distance;
                                    baseLocationData.return_duration=duration;
                                }
                            }
                            
                            $self.callback(callback,baseLocationData);
                        });
                    }else {
                        $self.callback(callback,baseLocationData);
                    }
                }else{
                    $self.callback(callback,baseLocationData);
                }
            });
            return(baseLocationData);
            
            /***/
        };
        
        /**********************************************************************/
        
        this.removeDoubleQuote=function(value)
        {
            return(value.replace(/"/g,''));
        };

        this.callback=function(callback,arg)
        {
            if(typeof(callback)!=='undefined') {
                callback(arg);
            }
        };

        this.createButtonRadio=function(selector)
        {
            $self.e(selector).on('click','.chbs-button-radio a',function(e)
            {
                e.preventDefault();
                
                var field=$(this).parent('.chbs-button-radio').find('input[type="hidden"]');
                
                $(this).siblings('a').removeClass('chbs-state-selected');
                
                if($(this).hasClass('chbs-state-selected'))
                {
                    field.val(-1);
                    $(this).removeClass('chbs-state-selected');
                }
                else 
                {    
                    field.val($(this).attr('data-value'));         
                    $(this).addClass('chbs-state-selected');
                }
            });          
        };
	};
	
	$.fn.chauffeurBookingForm=function(option) 
	{
		var form=new ChauffeurBookingForm(this,option);
        return(form);
	};

})(jQuery,document,window);