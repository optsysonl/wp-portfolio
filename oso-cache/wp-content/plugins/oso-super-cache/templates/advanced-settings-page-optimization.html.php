
<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <form method="post">

        <fieldset>

            <legend><?php _ex('Exclude Scripts', 'AS JavaScript - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title">
                    <?php _ex('Page Rule', 'AS JavaScript - Setting title', 'oso-super-cache'); ?>
                </div>
                <div class="form-field page-optimization-group" data-index="<?php echo count($excludedScripts); ?>">
                    <?php $scriptIndex = 0; ?>
                    <?php foreach($excludedScripts as $pageId => $excludedScript): ?>
                        <div class="form-row">
                            <div class="form-col-4">
                                <label for="pageId-<?php echo $scriptIndex; ?>">Page ID</label>
                                <input value="<?php echo $pageId; ?>" type="text" name="excludedScripts[<?php echo $scriptIndex; ?>][pageId]" id="pageId-<?php echo $scriptIndex; ?>" class="regular-text">
                            </div>
                            <div class="form-col-7">
                                <label for="itemId-<?php echo $scriptIndex; ?>">Script ID</label>
                                <textarea name="excludedScripts[<?php echo $scriptIndex; ?>][itemId]" id="itemId-<?php echo $scriptIndex; ?>" cols="80" rows="5"><?php echo $excludedScript; ?></textarea>
                            </div>
                            <div class="form-col-1">
                                <div class="page-optimization-group_button page-optimization-group_remove">Remove Group</div>
                            </div>
                        </div>
                    <?php $scriptIndex++; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="page-optimization-group_button page-optimization-group_add-new-script-group">Add new page group</div>
        </fieldset>

        <fieldset>

            <legend><?php _ex('Exclude Styles', 'AS JavaScript - Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <div class="form-group">
                <div class="form-title">
                    <?php _ex('Page Rule', 'AS JavaScript - Setting title', 'oso-super-cache'); ?>
                </div>
                <div class="form-field page-optimization-group" data-index="<?php echo count($excludedStyles); ?>">
                    <?php $styleIndex = 0; ?>
                    <?php foreach($excludedStyles as $pageId => $excludedStyle): ?>
                        <div class="form-row">
                            <div class="form-col-4">
                                <label for="pageId-<?php echo $styleIndex; ?>">Page ID</label>
                                <input value="<?php echo $pageId; ?>" type="text" name="excludedStyles[<?php echo $styleIndex; ?>][pageId]" id="pageId-<?php echo $styleIndex; ?>" class="regular-text">
                            </div>
                            <div class="form-col-7">
                                <label for="itemId-<?php echo $styleIndex; ?>">Style ID</label>
                                <textarea name="excludedStyles[<?php echo $styleIndex; ?>][itemId]" id="itemId-<?php echo $styleIndex; ?>" cols="80" rows="5"><?php echo $excludedStyle; ?></textarea>
                            </div>
                            <div class="form-col-1">
                                <div class="page-optimization-group_button page-optimization-group_remove">Remove Group</div>
                            </div>
                        </div>
                        <?php $styleIndex++; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="page-optimization-group_button page-optimization-group_add-new-style-group">Add new page group</div>
        </fieldset>

        <fieldset>

            <?php wp_nonce_field('oso_super_cache_advanced_settings_page_optimization'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Save settings', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
    </form>
</div>

<script type="text/javascript">
    (function($){
        $(document).ready(function(){
            $('body').on('click','.page-optimization-group_remove',function(){
                $(this).closest('.form-row').remove();
            });

            $('.page-optimization-group_add-new-script-group').on('click', function(){
                var group = $(this).closest('fieldset').find('.page-optimization-group');
                var ind = group.data('index');
                ind = ind - 0 + 1;
                var html = '<div class="form-row"><div class="form-col-4"><label for="pageId-'+ind+'">Page ID</label>'+
                    '<input value="" type="text" name="excludedScripts['+ind+'][pageId]" id="pageId-'+ind+'" class="regular-text">'+
                    '</div><div class="form-col-7"><label for="itemId-'+ind+'">Script ID</label>'+
                    '<textarea name="excludedScripts['+ind+'][itemId]" id="itemId-'+ind+'" cols="80" rows="5"></textarea>'+
                    '</div><div class="form-col-1"><div class="page-optimization-group_remove">Remove Group</div></div></div>';

                group.data('index', ind);
                group.append(html);
            });
            $('.page-optimization-group_add-new-style-group').on('click', function(){
                var group = $(this).closest('fieldset').find('.page-optimization-group');
                var ind = group.data('index');
                ind = ind - 0 + 1;
                var html = '<div class="form-row"><div class="form-col-4"><label for="pageId-'+ind+'">Page ID</label>' +
                    '<input value="" type="text" name="excludedStyles['+ind+'][pageId]" id="pageId-'+ind+'" class="regular-text">' +
                    '</div><div class="form-col-7"><label for="itemId-'+ind+'">Style ID</label>' +
                    '<textarea name="excludedStyles['+ind+'][itemId]" id="itemId-'+ind+'" cols="80" rows="5"></textarea>' +
                    '</div><div class="form-col-1"><div class="page-optimization-group_button page-optimization-group_remove">Remove Group</div></div></div>';

                group.data('index', ind);
                group.append(html);
            });
        });
    })(jQuery);
</script>
