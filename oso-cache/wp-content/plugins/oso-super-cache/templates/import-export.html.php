<h1><?php _ex('OSO Super Cache &rsaquo; Import &amp; Export', 'Top headline', 'oso-super-cache'); ?></h1>

<h2 class="nav-tab-wrapper">
    <a href="?page=oso-super-cache-import-export" class="nav-tab nav-tab-active"><?php _ex('Import &amp; Export', 'Tab title', 'oso-super-cache'); ?></a>
<!--    <a href="--><?php //_ex('', 'Support URL', 'oso-super-cache'); ?><!--" class="nav-tab">--><?php //_ex('Support', 'Tab title', 'oso-super-cache'); ?><!-- <span class="dashicons dashicons-external"></span></a>-->
</h2>
<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/import-export.svg" alt="">-->
<!--    <h3>--><?php //_ex('Import &amp; Export', 'Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->
<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Import', 'Import - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title">
                    <label for="importSettings"><?php _ex('New settings', 'Import Form', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <textarea name="importSettings" id="importSettings" cols="80" rows="5"></textarea>
                    <span class="description"><?php _ex('Paste your settings into the text area and click on <strong>Import settings</strong>.', 'Import - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>
            <?php wp_nonce_field('oso_super_cache_import_export'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Import settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

    </form>

    <fieldset>

        <legend><?php _ex('Export', 'Export - Headline of a fieldset', 'oso-super-cache'); ?></legend>

        <div class="form-group">
            <div class="form-title">
                <label for="exportSettings"><?php _ex('Current settings', 'Export - Form', 'oso-super-cache'); ?></label>
            </div>
            <div class="form-field">
                <textarea name="exportSettings" id="exportSettings" cols="80" rows="5"><?php echo $textareaExportSettings; ?></textarea>
                <span class="description"><?php _ex('Copy the content of the text area and insert it into the import field of another WordPress instance where you want to apply your OSO Super Cache settings.', 'Export - Setting description', 'oso-super-cache'); ?></span>
            </div>
        </div>
    </fieldset>

</div>