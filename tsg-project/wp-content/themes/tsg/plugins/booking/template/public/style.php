<?php
		if(isset($this->data['color'][1]))
		{
		    $color = ($this->data['color'][1] == 'transparent') ? 'transparent' : '#'.$this->data['color'][1];
?>  
            <?php echo $this->data['main_css_class']; ?> .chbs-location-add:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-remove:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field .chbs-quantity-section .chbs-quantity-section-button:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-1,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2.chbs-state-selected,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2.chbs-state-selected:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-payment>li>a .chbs-meta-icon-tick,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-header>a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-main-navigation-default>ul>li.chbs-state-selected>a>span:first-child
            {
				background-color:<?php echo $color; ?>;
            }
            
            html <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-panel
            {
                border-top-color:<?php echo $color; ?>;
            }
            
            .ui-datepicker td a.ui-state-hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-checkbox,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-add:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field .chbs-quantity-section .chbs-quantity-section-button:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-remove:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-payment>li>a.chbs-state-selected,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-header>a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-1,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2.chbs-state-selected,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2.chbs-state-selected:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active>a,
			body.rtl <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active>a
            {
                border-color:<?php echo $color; ?>;
            }
            
            rs-module-wrap <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active>a
            {
                border-color:<?php echo $color; ?> !important;
            }
            
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active+li>a
            {
                border-left-color:<?php echo $color; ?>;
            }
            
			body.rtl <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active+li>a
            {
                border-right-color:<?php echo $color; ?>;
            }
            
            .ui-datepicker td a.ui-state-hover,
            .ui-menu .ui-menu-item.ui-state-focus,   
            .ui-timepicker-wrapper>.ui-timepicker-list>li:hover,
            .ui-timepicker-wrapper>.ui-timepicker-list>li.ui-timepicker-selected,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-add,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-remove,
            <?php echo $this->data['main_css_class']; ?> .iti__country.iti__highlight,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field .chbs-quantity-section .chbs-quantity-section-button,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-price>span>span:first-child,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-checkbox.chbs-state-selected>.chbs-meta-icon-tick,
            <?php echo $this->data['main_css_class']; ?> .ui-selectmenu-button .chbs-meta-icon-arrow-vertical-large,
            <?php echo $this->data['main_css_class']; ?> .chbs-ride-info>div>span:first-child,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-list>ul>li>div.chbs-column-1>.booking-form-extra-price,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-complete .chbs-meta-icon-tick,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-header>span:first-child
            {
                color:<?php echo $color; ?>;
            }
<?php
		}    
        
		if(isset($this->data['color'][2]))
		{
            $color2 = ($this->data['color'][2] == 'transparent') ? 'transparent' : '#'.$this->data['color'][2];
?>  
            <?php echo $this->data['main_css_class']; ?> .chbs-summary,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-complete .chbs-meta-icon-tick>div:first-child+div,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content .chbs-vehicle-content-description>div>.chbs-vehicle-content-description-attribute,
            <?php echo $this->data['main_css_class']; ?> #payment ul.payment_methods
			{
				background-color:<?php echo $color2; ?>;
			}
            
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-complete .chbs-meta-icon-tick>div:first-child+div
            {
                border-color:<?php echo $color2; ?>;
            }
<?php
		}   
        
		if(isset($this->data['color'][3]))
		{
            $color3 = ($this->data['color'][3] == 'transparent') ? 'transparent' : '#'.$this->data['color'][3];
?>
            <?php echo $this->data['main_css_class']; ?> .chbs-qtip,
            <?php echo $this->data['main_css_class']; ?> .chbs-notice,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-add:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-remove:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field .chbs-quantity-section .chbs-quantity-section-button:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-1,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2.chbs-state-selected,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2.chbs-state-selected:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-main-navigation-default>ul>li.chbs-state-selected>a>span:first-child,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-header>a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-payment>li>a .chbs-meta-icon-tick
			{
				color:<?php echo $color3; ?>;
			}

            <?php echo $this->data['main_css_class']; ?> .chbs-form-field,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-selected.chbs-button-style-2,
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li>a,
            <?php echo $this->data['main_css_class']; ?>.chbs-widget.chbs-widget-style-2 .chbs-main-content-step-1 .chbs-tab.ui-tabs .ui-tabs-panel>div:last-child
            {
            background-color:<?php echo $color3; ?>;
            }
<?php
		}          
        
		if(isset($this->data['color'][4]))
		{
            $color4 = ($this->data['color'][4] == 'transparent') ? 'transparent' : '#'.$this->data['color'][4];
?>  
            .ui-datepicker th,
            .ui-datepicker .ui-datepicker-prev,
            .ui-datepicker .ui-datepicker-next,
            <?php echo $this->data['main_css_class']; ?> .chbs-tooltip,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-label-group,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field>label,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field>label a,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field>label a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2,
			<?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-3,
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-ride-info>div>span:first-child+span,
            <?php echo $this->data['main_css_class']; ?> .chbs-main-navigation-default>ul>li>a>span,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-field .chbs-summary-field-name,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-header>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-list>ul>li>div.chbs-column-1>.booking-form-extra-description,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content .chbs-vehicle-content-description>div>.chbs-vehicle-content-description-attribute>ul>li>div:first-child,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta a,
            <?php echo $this->data['main_css_class']; ?> .chbs-pagination a.chbs-pagination-prev,
            <?php echo $this->data['main_css_class']; ?> .chbs-pagination a.chbs-pagination-next
			{
				color:<?php echo $color4; ?>;
			}
            .ui-menu,
            .ui-datepicker,
            .ui-timepicker-wrapper,
            .ui-timepicker-wrapper>.ui-timepicker-list>li:hover,
            .ui-timepicker-wrapper>.ui-timepicker-list>li.ui-timepicker-selected
            {
                background-color:<?php echo $color4; ?>;
            }

            <?php echo $this->data['main_css_class']; ?> .chbs-form-checkbox,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-1:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-3:hover
            {
                background-color:<?php echo $color4; ?>;
            }
<?php
		}  
        
		if(isset($this->data['color'][5]))
		{
            $color5 = ($this->data['color'][5] == 'transparent') ? 'transparent' : '#'.$this->data['color'][5];
?>
            rs-module <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li>a
            {
                border-color:<?php echo $color5; ?> !important;
            }
            
            .ui-datepicker .ui-datepicker-prev.ui-state-hover,
            .ui-datepicker .ui-datepicker-next.ui-state-hover,       
            <?php echo $this->data['main_css_class']; ?> .chbs-pagination a.chbs-pagination-prev:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-pagination a.chbs-pagination-next:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-label-group,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2,
			<?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-3,
            <?php echo $this->data['main_css_class']; ?> .chbs-main-navigation-default>ul>li>a>span:first-child,
            <?php echo $this->data['main_css_class']; ?> .chbs-main-navigation-default>ul>li>div,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta>div>div.chbs-vehicle-content-meta-button a:hover>span.chbs-circle,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta>div>div.chbs-vehicle-content-meta-button a.chbs-state-selected>span.chbs-circle
			{
				background-color:<?php echo $color5; ?>;
			}
<?php
        }
        
		if(isset($this->data['color'][6]))
		{
            $color6 = ($this->data['color'][6] == 'transparent') ? 'transparent' : '#'.$this->data['color'][6];
?>
            .ui-datepicker td a,
            .ui-datepicker .ui-datepicker-title,
            .ui-datepicker .ui-datepicker-prev.ui-state-hover,
            .ui-datepicker .ui-datepicker-next.ui-state-hover, 
            <?php echo $this->data['main_css_class']; ?> .chbs-agreement,
            <?php echo $this->data['main_css_class']; ?> .chbs-agreement a,
            <?php echo $this->data['main_css_class']; ?> .chbs-agreement a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-pagination a.chbs-pagination-prev:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-pagination a.chbs-pagination-next:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-category-list>div>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-category-list>div>a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-payment>li>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-payment>li>a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-payment-header,
            <?php echo $this->data['main_css_class']; ?> .chbs-agreement-header,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field input,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary-price-element span,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-header>h4,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-field .chbs-summary-field-value,            
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-complete h3,
            <?php echo $this->data['main_css_class']; ?> .ui-selectmenu-button .ui-selectmenu-text,
            <?php echo $this->data['main_css_class']; ?> .chbs-ride-info>div>span:first-child+span+span,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-list>ul>li>div.chbs-column-1>.booking-form-extra-name,
            <?php echo $this->data['main_css_class']; ?> .chbs-main-navigation-default>ul>li.chbs-state-selected>a>span:first-child+span,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-header>span:first-child+span,
			<?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>div.chbs-vehicle-content-header>span,
			<?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-price-bid>div+div>input,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta>div>div.chbs-vehicle-content-meta-button a:hover,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content .chbs-vehicle-content-description>div>.chbs-vehicle-content-description-attribute>ul>li>div:first-child+div
			{
				color:<?php echo $color6; ?>;
			}
            .ui-datepicker,
            .ui-datepicker thead,
            .ui-datepicker .ui-datepicker-prev,
            .ui-datepicker .ui-datepicker-next,
            .ui-autocomplete,
            .ui-selectmenu-menu,
            .ui-menu .ui-menu-item,
            .ui-timepicker-wrapper,
            .ui-timepicker-wrapper>.ui-timepicker-list>li,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-category-list>div,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field,
            <?php echo $this->data['main_css_class']; ?> .chbs-payment>li>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-ride-info,
            <?php echo $this->data['main_css_class']; ?> .chbs-ride-info>div:first-child,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-add,
            <?php echo $this->data['main_css_class']; ?> .chbs-location-remove,
            <?php echo $this->data['main_css_class']; ?> .chbs-form-field .chbs-quantity-section .chbs-quantity-section-button,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-2,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-3,
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-panel,
            <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li>a,
            body.rtl <?php echo $this->data['main_css_class']; ?> .chbs-tab.ui-tabs .ui-tabs-nav>li>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-list>ul>li>div,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle-list>ul>li,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-extra-header>span:first-child,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta .chbs-circle,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-complete .chbs-meta-icon-tick>div:first-child,
            <?php echo $this->data['main_css_class']; ?>.chbs-width-300 .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active+li>a,
            body.rtl <?php echo $this->data['main_css_class']; ?>.chbs-width-300 .chbs-tab.ui-tabs .ui-tabs-nav>li.ui-tabs-active+li>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-price-bid>div+div>input,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta>div>div.chbs-vehicle-content-meta-button a:hover>span.chbs-circle,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta>div>div.chbs-vehicle-content-meta-button a.chbs-state-selected>span.chbs-circle,
            <?php echo $this->data['main_css_class']; ?>.chbs-width-480.chbs-widget.chbs-widget-style-2 .chbs-main-content-step-1 .chbs-tab.ui-tabs .ui-tabs-panel .chbs-form-field,
            <?php echo $this->data['main_css_class']; ?>.chbs-width-300.chbs-widget.chbs-widget-style-2 .chbs-main-content-step-1 .chbs-tab.ui-tabs .ui-tabs-panel .chbs-form-field,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary-price-element>div.chbs-summary-price-element-total>span:first-child+span
            {
                border-color:<?php echo $color6; ?>;
            }
<?php
        }
        
        if(isset($this->data['color'][7]))
		{
            $color7 = ($this->data['color'][7] == 'transparent') ? 'transparent' : '#'.$this->data['color'][7];
?>  
            .ui-datepicker td.ui-datepicker-unselectable,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta .chbs-meta-icon-bag,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-meta .chbs-meta-icon-people
			{
				color:<?php echo $color7; ?>;
			}
            
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-field,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary .chbs-summary-header>a,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary-price-element>div.chbs-summary-price-element-total,
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content .chbs-vehicle-content-description>div>.chbs-vehicle-content-description-attribute>ul>li
			{
				border-color:<?php echo $color7; ?>;
			}            
<?php
        } 
        
        if(isset($this->data['color'][8]))
		{
            $color8 = ($this->data['color'][8] == 'transparent') ? 'transparent' : '#'.$this->data['color'][8];
?>  
            <?php echo $this->data['main_css_class']; ?> .chbs-vehicle .chbs-vehicle-content>.chbs-vehicle-content-price>span>span:first-child+span,
            <?php echo $this->data['main_css_class']; ?> .chbs-summary-price-element>div.chbs-summary-price-element-pay>span:first-child>span
			{
				color:<?php echo $color8; ?>;
			}
<?php
        }         
        
        if(isset($this->data['color'][9]))
		{
            $color9 = ($this->data['color'][9] == 'transparent') ? 'transparent' : '#'.$this->data['color'][9];
?>  
            .ui-menu .ui-menu-item,
            .ui-timepicker-wrapper>.ui-timepicker-list>li,
            <?php echo $this->data['main_css_class']; ?> .chbs-booking-complete p,
            <?php echo $this->data['main_css_class']; ?> .chbs-button.chbs-button-style-1:hover
			{
				color:<?php echo $color9; ?>;
			}
            
            <?php echo $this->data['main_css_class']; ?> .chbs-qtip,
            <?php echo $this->data['main_css_class']; ?> .chbs-notice
			{
				background-color:<?php echo $color9; ?>;
			}
            
            <?php echo $this->data['main_css_class']; ?> .chbs-qtip,
            <?php echo $this->data['main_css_class']; ?> .chbs-notice
			{
				border-color:<?php echo $color9; ?>;
			}  
<?php
        }   