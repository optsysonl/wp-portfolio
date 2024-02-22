<h1><?php _ex('OSO Super Cache &rsaquo; CDN', 'CDN - Top headline', 'oso-super-cache'); ?></h1>

<h2 class="nav-tab-wrapper">
    <a href="?page=oso-super-cache-cdn" class="nav-tab nav-tab-active"><?php _ex('CDN', 'CDN - Tab title', 'oso-super-cache'); ?></a>
<!--    <a href="--><?php //_ex('', 'Support URL', 'oso-super-cache'); ?><!--" class="nav-tab">--><?php //_ex('Support', 'Tab title', 'oso-super-cache'); ?><!-- <span class="dashicons dashicons-external"></span></a>-->
</h2>
<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/cdn.svg" alt="">-->
<!--    <h3>--><?php //_ex('CDN', 'CDN - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->
<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('CDN', 'CDN - Headline of a fieldset', 'oso-super-cache'); ?></legend>

             <div class="form-group">
                <div class="form-title"><?php _ex('CDN', 'CDN - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cdn">
                        <input<?php echo $checkboxCDN; ?> type="checkbox" name="cdn" id="cdn" value="yes"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Activate to deliver your static assets through a CDN.', 'CDN - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cdnProvider"><?php _ex('CDN Provider', 'CDN - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="cdnProvider" id="cdnProvider">
                        <option<?php echo $optionCDNProviderStackPath; ?> value="CDNStackPath"><?php _ex('StackPath (MaxCDN)', 'CDN - Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCDNProviderOther; ?> value="CDNOther"><?php _ex('Other', 'CDN - Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('Select your CDN provider.', 'CDN - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cdnURL"><?php _ex('CDN URL', 'CDN - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputCDNURL; ?>" type="text" name="cdnURL" id="cdnURL" class="regular-text">
                    <span class="description"><?php _ex('Enter your CDN URL.', 'CDN - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>

            <?php wp_nonce_field('oso_super_cache_cdn'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

    </form>
</div>