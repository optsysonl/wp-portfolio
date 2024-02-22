<h1><?php _ex('OSO Super Cache &rsaquo; Cache Indexing', 'Cache Indexing - Top headline', 'oso-super-cache'); ?></h1>

<h2 class="nav-tab-wrapper">
    <a href="?page=oso-super-cache-indexing" class="nav-tab nav-tab-active"><?php _ex('Cache Indexing', 'Cache Indexing - Tab title', 'oso-super-cache'); ?></a>
<!--    <a href="--><?php //_ex('', 'Support URL', 'oso-super-cache'); ?><!--" class="nav-tab">--><?php //_ex('Support', 'Tab title', 'oso-super-cache'); ?><!-- <span class="dashicons dashicons-external"></span></a>-->
</h2>
<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/cache-indexing.svg" alt="">-->
<!--    <h3>--><?php //_ex('Cache Indexing', 'Cache Indexing - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->
<div class="content">
    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <p><?php _ex('Cache indexing allows you to easily add all your pages to OSO Super Cache index by reading the URLs from your XML Sitemap.', 'Cache Indexing - Setting page description', 'oso-super-cache'); ?></p>


    <form method="post">
        <fieldset>
            <legend><?php _ex('XML Sitemap', 'Cache Indexing - Headline of a fieldset', 'oso-super-cache'); ?></legend>
            <div class="form-group">
                <div class="form-title">
                    <label for="xmlSitemapURL"><?php _ex('XML Sitemap URL', 'Cache Indexing - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputXMLSitemapURL; ?>" type="text" name="xmlSitemapURL" id="xmlSitemapURL" class="regular-text">
                    <span class="description"><?php _ex('The URL to your XML Sitemap. Sitemap index files or compressed sitemaps are not supported but you can enter your sitemap index file URL to get a list of all your XML Sitemaps.', 'Cache Indexing - Setting description', 'oso-super-cache'); ?></span>
                    <?php
                    if (!empty($xmlSitemapIndexURLs)) {
                        ?>
                        <span class="description"><?php _ex('These sitemaps were detected in your sitemap index file. Click on a sitemap to automatically populate the XML Sitemap URL field.', 'Cache Indexing - Setting description', 'oso-super-cache'); ?></span>
                        <ul class="block">
                        <?php
                        foreach ($xmlSitemapIndexURLs as $sitemapIndexURL) {
                            ?>
                            <li data-oso-super-cache-sitemap-url="<?php echo $sitemapIndexURL; ?>" class="cursor"><?php echo basename($sitemapIndexURL); ?></li>
                            <?php
                        }
                        ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <?php
            if (!empty($xmlSitemapIndexURLs)) {
                ?>
                <div class="form-group">
                    <div class="form-title"><?php _ex('Reset Sitemap Index info', 'Cache Indexing - Setting title', 'oso-super-cache'); ?></div>
                    <div class="form-field">
                        <label for="xmlSitemapIndexReset">
                            <input type="checkbox" name="xmlSitemapIndexReset" id="xmlSitemapIndexReset" value="1"> <span class="option-title"><?php _ex('Reset', 'Setting checkbox', 'oso-super-cache'); ?></span>
                        </label>
                        <span class="description"><?php _ex("This option resets the information about of all found XML Sitemaps.", 'Cache Indexing - Setting description', 'oso-super-cache'); ?></span>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            if (!empty($xmlSitemapURLs)) {
                ?>
                <div class="form-group">
                    <div class="form-title"><?php _ex('Reset Sitemap info', 'Cache Indexing - Setting title', 'oso-super-cache'); ?></div>
                    <div class="form-field">
                        <label for="xmlSitemapReset">
                            <input type="checkbox" name="xmlSitemapReset" id="xmlSitemapReset" value="1"> <span class="option-title"><?php _ex('Reset', 'Setting checkbox', 'oso-super-cache'); ?></span>
                        </label>
                        <span class="description"><?php _ex("This option removes all URLs from the cache indexing list which were found in your last XML Sitemap.", 'Cache Indexing - Setting description', 'oso-super-cache'); ?></span>
                    </div>
                </div>
                <?php
            }
            ?>

            <hr>

            <?php wp_nonce_field('oso_super_cache_cache_indexing'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Apply', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>

    <?php
    if (!empty($xmlSitemapURLs)) {
        if (!empty($xmlSitemapURLs['urls'])) {
            ?>
            <fieldset>
                <legend><?php printf(_x('URLs of sitemap &#8222;%s&#8220;', 'Sub headline', 'oso-super-cache'), $xmlSitemapURLs['xml-sitemap']); ?></legend>

                <p><?php _ex('Click on the button to start adding pages to your cache. If the system gets stuck just reload the current page and start again.', 'Cache Indexing - Setting page description', 'oso-super-cache'); ?><br>
                <?php _ex('Successfully added pages will not be shown on the list after the reload.', 'Cache Indexing - Setting page description', 'oso-super-cache'); ?></p>

                <p class="align-center">
                    <button class="button-primary" type="button" name="cacheIndexingProcessStart"><span><?php _ex('Start', 'Button title part 1', 'oso-super-cache'); ?></span><span class="hide"><?php _ex('Resume', 'Button title part 1', 'oso-super-cache'); ?></span> <?php _ex('indexing and caching', 'Button title part 2', 'oso-super-cache'); ?></button>
                    <button class="button-secondary" type="button" name="cacheIndexingProcessPause" disabled><?php _ex('Pause', 'Button title', 'oso-super-cache'); ?></button>
                </p>

                <div id="oso-super-cache-indexing-process" class="hide">
                    <p class="align-center no-margin"><span class="dashicons dashicons-update"></span> <?php printf(_x('Processing page %s of %s', 'Status', 'oso-super-cache'), '<span class="current"></span>', '<span class="total"></span>'); ?></p>
                </div>

                <table class="full-width">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php _ex('URL', 'Table head', 'oso-super-cache'); ?></th>
                            <th><?php _ex('Time to create cache', 'Table head', 'oso-super-cache'); ?></th>
                            <th><?php _ex('Status', 'Table head', 'oso-super-cache'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                $counter = 1;
                foreach ($xmlSitemapURLs['urls'] as $url) {
                    ?>
                    <tr data-oso-super-cache-url="<?php echo $url; ?>" data-oso-super-cache-url-processed="false">
                        <td><?php echo $counter; ?></td>
                        <td><?php echo $url; ?></td>
                        <td class="align-right" data-oso-super-cache-url-ttcc></td>
                        <td class="align-center" data-oso-super-cache-url-status>
                            <span class="dashicons dashicons-no"></span>
                        </td>
                    </tr>
                    <?php
                    $counter++;
                }
                ?>
                    </tbody>
                </table>
                <p>
                    <span class="dashicons dashicons-yes"></span> <?php _ex('Page was added to cache', 'Status', 'oso-super-cache'); ?><br>
                    <span class="dashicons dashicons-update"></span> <?php _ex('Page is currently processed', 'Status', 'oso-super-cache'); ?><br>
                    <span class="dashicons dashicons-no"></span> <?php _ex('Page was not added to the cache yet', 'Status', 'oso-super-cache'); ?><br>
                    <span class="dashicons dashicons-warning"></span> <?php _ex('Page could not be added to the cache', 'Status', 'oso-super-cache'); ?>
                </p>
            </fieldset>
            <?php
        }
    }
    ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {

    var buttonStart = $("button[name='cacheIndexingProcessStart']");
    var buttonPause = $("button[name='cacheIndexingProcessPause']");
    var ttccSuffix = "<?php _ex('sec', 'Datetime', 'oso-super-cache'); ?>";
    var iterator = 0;
    var total = $("[data-oso-super-cache-url-processed='false']").length;
    var indexingStatus = false;
    var isProcessing = false;

    $("[data-oso-super-cache-sitemap-url]").on("click", function () {
        $("#xmlSitemapURL").val(jQuery(this)[0].dataset.osoSuperCacheSitemapUrl);
    });

    buttonStart.on("click", function () {
        $(this).prop("disabled", true);

        if ($(this).children("span:first-child").hasClass("hide") == false) {
            $(this).children("span:first-child").addClass("hide");
            $(this).children("span:last-child").removeClass("hide");
        }

        buttonPause.prop("disabled", false);

        indexingStatus = true;

        $("#oso-super-cache-indexing-process span.dashicons-update").addClass("spin");
        $("#oso-super-cache-indexing-process span.total").html(total);

        osoSuperCacheIndexingHandler();
    });

    buttonPause.on("click", function () {
        $(this).prop("disabled", true);
        buttonStart.prop("disabled", false);

        $("#oso-super-cache-indexing-process span.dashicons-update").removeClass("spin");

        indexingStatus = false;
    });

    osoSuperCacheIndexingHandler = function () {

        if ($("#oso-super-cache-indexing-process").css("display") === "none") {
            $("#oso-super-cache-indexing-process").slideDown();
        }

        // Check if processing is not paused and no other process is running
        if (indexingStatus == true && isProcessing == false) {

            var preloadItems = $("[data-oso-super-cache-url-processed='false']");

            if (preloadItems.length) {
                var nextPreloadItem = preloadItems[0].dataset;

                // Update item
                $("[data-oso-super-cache-url='"+nextPreloadItem.osoSuperCacheUrl+"']").children("[data-oso-super-cache-url-status]").children("span.dashicons").removeClass("dashicons-no").addClass("dashicons-update spin");

                iterator++;

                $("#oso-super-cache-indexing-process span.current").html(iterator);

                isProcessing = true;

                $.ajax(
                    ajaxurl,
                    {
                        type: "POST",
                        data: {
                            action: "oso_super_cache_handler",
                            type: "osoSuperCacheIndexing",
                            _ajax_nonce: "<?php echo wp_create_nonce("oso-super-cache-indexing"); ?>",
                            url: nextPreloadItem.osoSuperCacheUrl
                        }
                    }
                ).done(function (data) {

                    isProcessing = false;

                    data = $.parseJSON(data);

                    $("[data-oso-super-cache-url='"+nextPreloadItem.osoSuperCacheUrl+"']").attr({"data-oso-super-cache-url-processed": "true"});

                    if (data.success) {
                        $("[data-oso-super-cache-url='"+nextPreloadItem.osoSuperCacheUrl+"']").children("[data-oso-super-cache-url-ttcc]").html(data.ttcc+" "+ttccSuffix);
                        $("[data-oso-super-cache-url='"+nextPreloadItem.osoSuperCacheUrl+"']").children("[data-oso-super-cache-url-status]").children("span.dashicons").removeClass("dashicons-update spin").addClass("dashicons-yes");

                        osoSuperCacheIndexingHandler();
                    } else {
                        $("[data-oso-super-cache-url='"+nextPreloadItem.osoSuperCacheUrl+"']").children("[data-oso-super-cache-url-status]").children("span.dashicons").removeClass("dashicons-update spin").addClass("dashicons-warning");
                    }
                }).fail(function (data) {
                    isProcessing = false;
                    $(buttonPause).trigger("click");
                });
            } else {
                isProcessing = false;
                $(buttonPause).trigger("click");
            }
        }
    }
});
</script>