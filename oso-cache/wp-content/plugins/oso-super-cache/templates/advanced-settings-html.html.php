<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/advanced-settings.svg" alt="">-->
<!--    <h3>--><?php //_ex('HTML', 'AS HTML - Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->

<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Optimization', 'AS HTML - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title"><?php _ex('Remove HTML Comments', 'AS HTML - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="minifyRemoveHTMLComments">
                        <input<?php echo $checkboxMinifyRemoveHTMLComments; ?> type="checkbox" name="minifyRemoveHTMLComments" id="minifyRemoveHTMLComments" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex("Removes all HTML comments like &lt;!-- html comment --&gt;. If you need to comment something in your code, but don't want to deactivate this option you can use [ ], e.g. <strong>&lt;!--[ this comment will not be removed ]--&gt;</strong>.", 'AS HTML - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="form-title"><?php _ex('Remove Whitespace', 'AS HTML - Setting title', 'oso-super-cache'); ?></div>
                <div class="form-field">
                    <label for="minifyRemoveWhitespace">
                        <input<?php echo $checkboxMinifyRemoveWhitespace; ?> type="checkbox" name="minifyRemoveWhitespace" id="minifyRemoveWhitespace" value="1"> <span class="option-title"><?php _ex('Activate', 'Setting checkbox', 'oso-super-cache'); ?></span>
                    </label>
                    <span class="description"><?php _ex('Removes all unnecessary whitespace except for whitespace inside &lt;pre&gt;, &lt;script&gt;, or &lt;style&gt;.', 'AS HTML - Setting description', 'oso-super-cache'); ?></span>
                </div>
            </div>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_html'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>
