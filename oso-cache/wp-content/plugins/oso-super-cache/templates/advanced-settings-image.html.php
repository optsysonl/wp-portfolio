<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('Image', 'AS Image - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Optimization', 'AS Image - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Lazy Load', 'AS Image - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="imagesLazyLoad">
                        <input<?php echo $checkboxImagesLazyLoad; ?> type="checkbox" name="imagesLazyLoad" id="imagesLazyLoad" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>

                    <span class="description"><?php _ex("Images will be loaded as soon as they enter the browser's viewport.", 'AS Image - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="imagesLazyLoadExclude"><?php _ex("Exclude CSS classes from Lazy Loading", 'AS Image - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <textarea name="imagesLazyLoadExclude" id="imagesLazyLoadExclude" cols="80" rows="5"><?php echo esc_textarea($textareaImagesLazyLoadExclude); ?></textarea>
                    <span class="description"><?php _ex('One CSS class per line. An image having one of the excluded CSS classes will not used for Lazy Loading.', 'AS Image - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>


            <?php wp_nonce_field('oso_super_cache_advanced_settings_image'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>
