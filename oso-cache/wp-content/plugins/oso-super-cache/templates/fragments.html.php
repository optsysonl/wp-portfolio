<h1><?php _ex('OSO Super Cache &rsaquo; Fragments', 'Fragments - Top headline', 'oso-super-cache'); ?></h1>

<h2 class="nav-tab-wrapper">
    <a href="?page=oso-super-cache-fragments" class="nav-tab nav-tab-active"><?php _ex('Fragments', 'Fragments - Tab title', 'oso-super-cache'); ?></a>
</h2>
<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/fragments.svg" alt="">-->
<!--    <h3>--><?php //_ex('Fragments', 'Fragments - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->
<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <p><?php _ex('Fragment caching allows you to have dynamic code in your cached static files.', 'Fragments - Setting page description', 'oso-super-cache'); ?><br>
    <?php _ex('The number of WordPress functions available depends upon your <strong>Late Initialization</strong> (see <a href="?page=oso-super-cache-advanced-settings">Advanced Settings</a>) settings.', 'Fragments - Setting page description', 'oso-super-cache'); ?></p>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Fragment Settings', 'Fragments - Headline of a fieldset', 'oso-super-cache'); ?></legend>

             <div class="form-group">
                <div class="form-title"><?php _ex('Fragment Caching', 'Fragments - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="fragmentCaching">
                        <input<?php echo $checkboxFragmentCaching; ?> type="checkbox" name="fragmentCaching" id="fragmentCaching" value="yes"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Activate to parse PHP code between <strong>&lt;!--[oso-super-cache start: ...]--&gt;</strong> and <strong>&lt;!--[oso-super-cache end: ...]--&gt;</strong>.', 'Fragments - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title">
                    <label for="fragmentCachingMaskPhrase"><?php _ex('Fragment Mask Phrase', 'Fragments - Setting title', 'oso-super-cache'); ?></label>
                </div>
                <div class="form-field">
                    <input value="<?php echo $inputFragmentCachingMaskPhrase; ?>" type="text" name="fragmentCachingMaskPhrase" id="fragmentCachingMaskPhrase" class="regular-text">
                    <span class="description"><?php _ex('A phrase with a min length of 14 tokens. Allowed: a-z A-Z 0-9', 'Fragments - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <hr>
            <?php wp_nonce_field('oso_super_cache_fragments'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>

    </form>

    <h4><?php _ex('Example', 'Fragments - Headline', 'oso-super-cache'); ?></h4>
    <p><?php _ex('Place this code in your theme, e.g. in sidebar.php', 'Fragments - Setting description', 'oso-super-cache'); ?></p>
    <code>
        &lt;!--[oso-super-cache start: <?php echo $inputFragmentCachingMaskPhrase; ?>]--&gt;
        <br>
        <br>
        echo date('Y-m-d H:i:s');
        <br>
        <br>
        // Get some information about the current page. Helpful when you can't use is_archive() or $post.
        <br>
        $cachedData = \OSOSuperCache\Factory::get('Cache\Frontend\Resolver')-&gt;getCachedPageData();
        <br>
        <br>
        echo &quot;&lt;pre&gt;&quot;;
        <br>
        print_r($cachedData);
        <br>
        echo &quot;&lt;/pre&gt;&quot;;
        <br>
        <br>
        // end of php code
        <br>
        {/<?php echo $inputFragmentCachingMaskPhrase; ?>}
        <br>
        <br>
        &lt;p&gt;Regular html-code outside of php&lt;/p&gt;
        <br>
        <br>
        {<?php echo $inputFragmentCachingMaskPhrase; ?>}
        <br>
        // start of php code again
        <br>
        <br>
        echo date('Y-m-d H:i:s');
        <br>
        <br>
        &lt;!--[oso-super-cache cache end: <?php echo $inputFragmentCachingMaskPhrase; ?>]--&gt;
    </code>
</div>