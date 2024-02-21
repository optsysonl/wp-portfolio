<div class="chbs-layout-25x75 chbs-clear-fix">



<!--    <div class="chbs-layout-column-left"></div>-->

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
$BookingFormElement = new CHBSBookingFormElement();
echo $BookingFormElement->createAgreement($this->data['meta']);
?>

<div class="chbs-clear-fix chbs-main-content-navigation-button">
    <a href="#" class="chbs-button chbs-button-style-2 chbs-button-step-prev">
        <span class="chbs-meta-icon-arrow-horizontal-large"></span>
        <?php echo esc_html($this->data['step']['dictionary'][3]['button']['prev']); ?>
    </a>
    <a href="#" class="chbs-button chbs-button-style-1 chbs-button-step-next">
        <?php echo esc_html($this->data['step']['dictionary'][3]['button']['next']); ?>
        <span class="chbs-meta-icon-arrow-horizontal-large"></span>
    </a>
</div>