<h1><?php _ex('OSO Super Cache &rsaquo; Optimize Database', 'Top headline', 'oso-super-cache'); ?></h1>

<h2 class="nav-tab-wrapper">
    <a href="?page=oso-super-cache-optimize-database" class="nav-tab nav-tab-active"><?php _ex('Optimize Database', 'Tab title', 'oso-super-cache'); ?></a>
<!--    <a href="--><?php //_ex('', 'Support URL', 'oso-super-cache'); ?><!--" class="nav-tab">--><?php //_ex('Support', 'Tab title', 'oso-super-cache'); ?><!-- <span class="dashicons dashicons-external"></span></a>-->
</h2>
<!--<div class="page-headline">-->
<!--    <img src="--><?php //echo $this->imagePath; ?><!--/icons/optimize-database.svg" alt="">-->
<!--    <h3>--><?php //_ex('Optimize Database', 'Headline right of icon', 'oso-super-cache'); ?><!--</h3>-->
<!--</div>-->
<div class="content">

    <div class="messages">
    <?php echo \OSOSuperCache\Factory::get('Cache\Backend\Backend')->getMessages(); ?>
    </div>

    <h4><?php _ex('Statistics', 'Headline', 'oso-super-cache'); ?></h4>

    <table>
        <tr>
            <th><?php _ex('Total database size', 'Table head', 'oso-super-cache'); ?></th>
            <td class="align-right"><?php echo $readableTotalTableSize; ?></td>
        </tr>
        <tr>
            <th><?php _ex('Total optimized', 'Table head', 'oso-super-cache'); ?></th>
            <td class="align-right"><?php echo $readableTotalSavedBytes; ?></td>
        </tr>
    </table>

    <p><strong><?php _ex('Total database size', 'Table head', 'oso-super-cache'); ?>:</strong> <?php _ex('The cumulative size of all of the tables in the database.', 'Description', 'oso-super-cache'); ?><br>
        <strong><?php _ex('Total optimized', 'Table head', 'oso-super-cache'); ?>:</strong> <?php _ex('The size OSO Super Cache could save by optimizing tables of the database to date.', 'Description', 'oso-super-cache'); ?>
    </p>

    <form method="post">

        <fieldset>

            <legend><?php _ex('WordPress Tables', 'Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" name="checkAll" value="0"></th>
                        <th><?php _ex('Table', 'Table head', 'oso-super-cache'); ?></th>
                        <th><?php _ex('Size', 'Table head', 'oso-super-cache'); ?></th>
                        <th><?php _ex('Size after optimization', 'Table head', 'oso-super-cache'); ?></th>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($wordPressTables as $tableData) {
                ?>
                <tr>
                    <td class="align-center">
                        <?php
                        if (!empty($tableData->optimizable)) {
                            ?>
                            <input type="checkbox" name="tables[]" value="<?php echo esc_html($tableData->TABLE_NAME); ?>">
                            <?php
                        } else {
                            ?>-<?php
                        }
                        ?>
                    </td>
                    <td><?php echo $tableData->TABLE_NAME; ?></td>
                    <td class="align-right"><?php echo $tableData->sizeData; ?></td>
                    <td class="align-right"><?php echo $tableData->sizeDataFree; ?></td>
                </tr>
                <?php
            }
            ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td  colspan="2" class="align-right"><?php _ex('Total', 'Table foot', 'oso-super-cache'); ?></td>
                        <td class="align-right"><?php echo $readableTotalWordPressTableSize; ?></td>
                        <td class="align-right"><?php echo $readableTotalWordPressTableFreeSize; ?></td>
                    </tr>
                </tfoot>
            </table>

            <hr>
            <?php wp_nonce_field('oso_super_cache_optimize_database'); ?>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Optimize tables', 'Button title', 'oso-super-cache'); ?>">


        </fieldset>

        <?php
        if (!empty($otherTables)) {
            ?>
        <fieldset>

            <legend><?php _ex('Other Tables', 'Headline of a fieldset', 'oso-super-cache'); ?></legend>

            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" name="checkAll" value="0"></th>
                        <th><?php _ex('Table', 'Table head', 'oso-super-cache'); ?></th>
                        <th><?php _ex('Size', 'Table head', 'oso-super-cache'); ?></th>
                        <th><?php _ex('Size after optimization', 'Table head', 'oso-super-cache'); ?></th>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($otherTables as $tableData) {
                ?>
                <tr>
                    <td class="align-center">
                        <?php
                        if (!empty($tableData->optimizable)) {
                            ?>
                            <input type="checkbox" name="tables[]" value="<?php echo esc_html($tableData->TABLE_NAME); ?>">
                            <?php
                        } else {
                            ?>-<?php
                        }
                        ?>
                    </td>
                    <td><?php echo $tableData->TABLE_NAME; ?></td>
                    <td class="align-right"><?php echo $tableData->sizeData; ?></td>
                    <td class="align-right"><?php echo $tableData->sizeDataFree; ?></td>
                </tr>
                <?php
            }
            ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="align-right"><?php _ex('Total', 'Table foot', 'oso-super-cache'); ?></td>
                        <td class="align-right"><?php echo $readableTotalOtherTableSize; ?></td>
                        <td class="align-right"><?php echo $readableTotalOtherTableFreeSize; ?></td>
                    </tr>
                </tfoot>
            </table>

            <hr>
            <input type="hidden" name="formSend" value="1">
            <input class="button-primary" type="submit" value="<?php _ex('Optimize tables', 'Button title', 'oso-super-cache'); ?>">

        </fieldset>
            <?php
        }
        ?>

    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery('input[type="checkbox"][name="checkAll"]').on('change', function () {

        var currentCheckbox = jQuery(this);

        jQuery(this).closest('table').find('input[type="checkbox"]').each(function () {
            jQuery(this).prop('checked', currentCheckbox.prop('checked'));
        });
    });
});
</script>