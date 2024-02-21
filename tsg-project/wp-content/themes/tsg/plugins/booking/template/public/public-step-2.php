        
        <div class="chbs-layout-25x75 chbs-clear-fix">


<!--            <div class="chbs-layout-column-left"></div>-->

            <div class="chbs-layout-column-right">

                <div class="chbs-booking-route-title-wrapper"></div>

                <div class="chbs-notice chbs-hidden"></div>

                <div class="chbs-booking-vehicles">
                    <h4 class="chbs-booking-extra-header">
                        <span class="chbs-circle"><i class="fa fa-bus"></i></span>
                        <span>Vehicles</span>
                    </h4>

                    <div class="chbs-vehicle-list"></div>
                </div>
            
                <div class="chbs-booking-extra"></div>
                
            </div>

        </div>

        <?php
        $BookingFormElement=new CHBSBookingFormElement();
        echo $BookingFormElement->createAgreement($this->data['meta']);

        ?>

        <div class="chbs-clear-fix chbs-main-content-navigation-button">
            <a href="#" class="chbs-button chbs-button-style-2 chbs-button-step-prev">
                <span class="chbs-meta-icon-arrow-horizontal-large"></span>
                <?php echo esc_html($this->data['step']['dictionary'][2]['button']['prev']); ?>
            </a> 

                <?php if(is_array($this->data['step']['dictionary'][2]['button']['next'])): ?>
                    <?php foreach($this->data['step']['dictionary'][2]['button']['next'] as $key => $next_btn): ?>
                        <a href="#" class="chbs-button chbs-button-style-1 chbs-button-step-next <?php echo $key; ?>" style="display: none;">
                            <?php echo esc_html($next_btn); ?>
                            <span class="chbs-meta-icon-arrow-horizontal-large"></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a href="#" class="chbs-button chbs-button-style-1 chbs-button-step-next">
                        <?php echo esc_html($this->data['step']['dictionary'][2]['button']['next']); ?>
                        <span class="chbs-meta-icon-arrow-horizontal-large"></span>
                    </a>
                <?php endif; ?>

        </div>