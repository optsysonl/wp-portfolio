<h1><?php _ex('OSO Super Cache &rsaquo; Dashboard', 'Top headline', 'oso-super-cache'); ?></h1>

<h2 class="nav-tab-wrapper">
    <a href="?page=oso-super-cache-dashboard" class="nav-tab nav-tab-active"><?php _ex('Dashboard', 'Tab title', 'oso-super-cache'); ?></a>
<!--    <a href="--><?php //_ex('', 'Support URL', 'oso-super-cache'); ?><!--" class="nav-tab">--><?php //_ex('Support', 'Tab title', 'oso-super-cache'); ?><!-- <span class="dashicons dashicons-external"></span></a>-->
</h2>

<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/dashboard.svg" alt="">-->
<!--    <h3>--><?php //_e('Dashboard', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">
        <fieldset>

            <legend><?php _ex('Simple Setup', 'Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Activate OSO Super Cache', 'Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheActivated">
                        <input<?php echo $checkboxCacheActivated; ?> type="checkbox" name="cacheActivated" id="cacheActivated" value="yes"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?> <i class="dashicons dashicons-lightbulb <?php echo !empty($checkboxCacheActivated) ? 'text-green' : 'text-red'; ?>"></i></span>
                    </label>
                    <span class="description"><?php _ex('When activated, OSO Super Cache starts caching your website.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('If deactivated, the site behaves as before.', 'Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="cachePreset"><?php _ex('Cache Presets', 'Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <select name="cachePreset" id="cachePreset">
                        <option<?php echo $optionCachePresetCustom; ?> value="custom"><?php _ex('Custom', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCachePresetDefault; ?> value="default"><?php _ex('Default', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCachePresetOnlyPages; ?> value="only-pages"><?php _ex('Only pages', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCachePresetEcommerce; ?> value="ecommerce"><?php _ex('E-commerce', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCachePresetMagazine; ?> value="magazine"><?php _ex('Magazine / Blog', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCachePresetCorporate; ?> value="corporate"><?php _ex('Corporate', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCachePresetTestCSS; ?> value="test-css"><?php _ex('Test CSS', 'Select option', 'oso-super-cache'); ?></option>
                        <option<?php echo $optionCachePresetTestJS; ?> value="test-js"><?php _ex('Test JS', 'Select option', 'oso-super-cache'); ?></option>
                    </select>
                    <span class="description"><?php _ex('<strong>Custom</strong>: Your custom settings. This preset will be created/updated everytime you update the settings.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Default</strong>: Default cache settings for most sites.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Only pages</strong>: Only pages are cached, no merging/caching of JavaScript or CSS.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>E-commerce</strong>: Special &quot;do not cache&quot; rules for ecommerce sites.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Magazine / Blog</strong>: Optimized cache lifetimes for magazines / blogs that are continually updated with new content.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Corporate</strong>: Optimized cache lifetimes for corporate sites.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Test CSS</strong>: Only CSS is merged.', 'Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex('<strong>Test JS</strong>: Only JS is merged.', 'Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>
            <?php wp_nonce_field('oso_super_cache_dashboard_setup'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save all settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>

    <fieldset>
        <legend><?php _ex('Statistics &amp; Information', 'Dashboard - Headline of a fieldset', 'oso-super-cache'); ?></legend>

        <?php

            if (!empty($preloadedStats)) {
            ?>
            <div class="chart-container">
                <div class="chart-wrap">
                    <canvas id="preload-chart"></canvas>
                </div>
            </div>
            <?php
            }

        ?>

        <table>
            <tr>
                <th><?php _ex('Total cached pages', 'Dashboard - Table head', 'oso-super-cache'); ?></th>
                <td class="align-right"><?php echo $totalCachedPages; ?></td>
            </tr>
            <tr>
                <th><?php _ex('Runtime without cache', 'Dashboard - Table head', 'oso-super-cache'); ?></th>
                <td class="align-right">&Oslash; <?php echo $averageRuntimeWithoutCache; ?> <?php _ex('sec', 'Datetime', 'oso-super-cache'); ?></td>
            </tr>
            <tr>
                <th><?php _ex('Runtime with cache', 'Dashboard - Table head', 'oso-super-cache'); ?></th>
                <td class="align-right">&Oslash; <?php echo $averageRuntimeWithCache; ?> <?php _ex('sec', 'Datetime', 'oso-super-cache'); ?></td>
            </tr>
            <tr>
                <th><?php _ex('Performance increased', 'Dashboard - Table head', 'oso-super-cache'); ?></th>
                <td class="align-right">&Oslash; <?php echo $averagePerformanceIncreased; ?> <?php _ex('times faster', 'Table column', 'oso-super-cache'); ?></td>
            </tr>

        </table>
    </fieldset>

    <form method="post">
        <fieldset>

            <legend><?php _ex('Cache Maintenance', 'Dashboard - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Select a maintenance option', 'Dashboard - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheMaintenanceRefresh">
                        <input type="radio" name="cacheMaintenance" id="cacheMaintenanceRefresh" value="refresh"> <span class="option-title"><?php _ex('Refresh', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <label for="cacheMaintenanceReset">
                        <input type="radio" name="cacheMaintenance" id="cacheMaintenanceReset" value="reset"> <span class="option-title"><?php _ex('Reset', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <label for="cacheMaintenanceClearStylesPreCache">
                        <input type="radio" name="cacheMaintenance" id="cacheMaintenanceClearStylesPreCache" value="clearStylesPreCache"> <span class="option-title"><?php _ex('Clear CSS pre-cache files', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('<strong>Refresh</strong>: Mark all pages to refresh their cache.', 'Dashboard - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex("<strong>Reset</strong>: Resets OSO Super Cache index. All <strong>Cache storage</strong> settings and runtime statistics will be lost.", 'Dashboard - Setting description', 'oso-super-cache'); ?></span>
                    <span class="description"><?php _ex("<strong>Clear CSS pre-cache files</strong>: This clears all CSS pre-cache files. Recommended when you have modified one of your CSS files.", 'Dashboard - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Confirmation', 'Dashboard - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="cacheMaintenanceConfirmation">
                        <input type="checkbox" name="cacheMaintenanceConfirmation" id="cacheMaintenanceConfirmation" value="yes"> <span class="option-title"><?php _ex('Confirmed', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Please confirm that you want to execute the selected maintenance option.', 'Dashboard - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>
            <?php wp_nonce_field('oso_super_cache_dashboard_maintenance'); ?>
            <input type="hidden" name="formSendCacheMaintenance" value="1">
            <input disabled id="executeCacheMaintenance" class="button-primary red-button" type="submit" value="<?php _ex('Execute Cache Maintenance', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>

</div>

<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery('input[type="radio"][name="cacheMaintenance"]').on('change', function () {

        // Reset confirmation checkbox
        jQuery('#cacheMaintenanceConfirmation').prop('checked', false);
        jQuery('#executeCacheMaintenance').prop('disabled', true);

    });

    jQuery('#cacheMaintenanceConfirmation').on('change', function () {

        if (jQuery(this).prop('checked')) {
            jQuery('#executeCacheMaintenance').prop('disabled', false);
        } else {
            jQuery('#executeCacheMaintenance').prop('disabled', true);
        }
    });
});
</script>

<?php
//if (empty(\OSOSuperCache\Factory::get('Cache\Backend\License')->getLicenseData()->noLicense) && !empty($preloadedStats)) {
    ?>
    <script src="<?php echo $this->chartJS; ?>"></script>
    <script type="text/javascript">
    jQuery(document).ready(function () {

        var ctx = document.getElementById("preload-chart");
        var preloadChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: [<?php echo implode(',', $chartLabels); ?>],
                datasets: [{
                    label: "<?php _ex('Preloads', 'Chart Label', 'oso-super-cache'); ?>",
                    data: [<?php echo implode(',', $chartValues); ?>],
                    backgroundColor: [
                        "rgba(54, 162, 235, 0.2)",
                    ],
                    borderColor: [
                        "rgba(54, 162, 235, 1)",
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });
    </script>
    <?php
//}
?>