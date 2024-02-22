<?php
/*
 *
 */

namespace OSOSuperCache\Cache\Backend;

use OSOSuperCache\Factory;

class OptimizeDatabase
{

    private static $instance;

    private $imagePath;

    public static function getInstance()
    {

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    protected function __construct()
    {
    }

    public function display()
    {

        global $wpdb;

        $this->imagePath = plugins_url('images', realpath(__DIR__.'/../../'));

        if (!empty($_POST['formSend']) && !empty($_POST['tables']) && check_admin_referer('oso_super_cache_optimize_database')) {
            $this->optimizeTables($_POST['tables']);
        }

        $wordPressTables    = [];
        $otherTables        = [];

        $totalWordPressTableSize = 0;
        $totalWordPressTableFreeSize = 0;
        $totalOtherTableSize = 0;
        $totalOtherTableFreeSize = 0;

        $totalSavedBytes = get_option('OSOSuperCacheOptimizedDatabaseSavedBytes', 0);

        $allTables = $wpdb->get_results('
            SELECT
                *
            FROM
                INFORMATION_SCHEMA.`TABLES`
            WHERE
                `TABLE_SCHEMA`="'.$wpdb->dbname.'"
            ORDER BY
                `TABLE_NAME` ASC
        ');

        foreach ($allTables as $key => $tableData) {
            if ($tableData->ENGINE != 'MyISAM') {
                $tableData->DATA_FREE = 0;
            }

            $sizeDataTotal      = $tableData->DATA_LENGTH+$tableData->INDEX_LENGTH+$tableData->DATA_FREE;
            $sizeData           = $sizeDataTotal;
            $sizeDataUnit       = 'Bytes';
            $sizeDataFree       = $tableData->DATA_FREE;
            $sizeDataFreeUnit   = 'Bytes';

            // Make sizes readable
            if ($sizeDataTotal > 0) {
                $unitAndDivisor = $this->getUnitAndDivisor($sizeDataTotal);
                $sizeData = $sizeData/$unitAndDivisor['divisor'];
                $sizeDataUnit = $unitAndDivisor['unit'];
            }

            if ($sizeDataFree > 0) {
                $unitAndDivisor = $this->getUnitAndDivisor($sizeDataTotal-$sizeDataFree);
                $sizeDataFree = ($sizeDataTotal-$sizeDataFree)/$unitAndDivisor['divisor'];
                $sizeDataFreeUnit = $unitAndDivisor['unit'];
            }

            $tableData->sizeData = number_format_i18n($sizeData, 2).' '.$sizeDataUnit;

            if ($sizeDataFree) {
                $tableData->sizeDataFree = number_format_i18n($sizeDataFree, 2).' '.$sizeDataFreeUnit;
                $tableData->optimizable = true;
            } else {
                $tableData->sizeDataFree = _x('<em>Already optimized</em>', 'Table status', 'oso-super-cache');
                $tableData->optimizable = false;
            }

            // Detect if table is part of wordpress
            if (substr($tableData->TABLE_NAME, 0, strlen($wpdb->prefix)) == $wpdb->prefix) {
                $wordPressTables[] = $tableData;

                $totalWordPressTableSize += $sizeDataTotal;
                $totalWordPressTableFreeSize += $tableData->DATA_FREE;
            } else {
                $otherTables[] = $tableData;

                $totalOtherTableSize += $sizeDataTotal;
                $totalOtherTableFreeSize += $tableData->DATA_FREE;
            }

            unset($allTables[$key]);
        }

        // WordPress total datasize
        $unitAndDivisor = $this->getUnitAndDivisor($totalWordPressTableSize);
        $readableTotalWordPressTableSize = number_format_i18n($totalWordPressTableSize/$unitAndDivisor['divisor'], 2).' '.$unitAndDivisor['unit'];

        if ($totalWordPressTableFreeSize > 0) {
            $unitAndDivisor = $this->getUnitAndDivisor($totalWordPressTableSize-$totalWordPressTableFreeSize);
            $readableTotalWordPressTableFreeSize = number_format_i18n(($totalWordPressTableSize-$totalWordPressTableFreeSize)/$unitAndDivisor['divisor'], 2).' '.$unitAndDivisor['unit'];
        } else {
            $readableTotalWordPressTableFreeSize = _x('<em>Already optimized</em>', 'Table status', 'oso-super-cache');
            ;
        }

        // Other total datasize
        if ($totalOtherTableSize > 0) {
            $unitAndDivisor = $this->getUnitAndDivisor($totalOtherTableSize);
            $readableTotalOtherTableSize = number_format_i18n($totalOtherTableSize/$unitAndDivisor['divisor'], 2).' '.$unitAndDivisor['unit'];

            if ($totalOtherTableFreeSize > 0) {
                $unitAndDivisor = $this->getUnitAndDivisor($totalOtherTableSize-$totalOtherTableFreeSize);
                $readableTotalOtherTableFreeSize = number_format_i18n(($totalOtherTableSize-$totalOtherTableFreeSize)/$unitAndDivisor['divisor'], 2).' '.$unitAndDivisor['unit'];
            } else {
                $readableTotalOtherTableFreeSize = _x('<em>Already optimized</em>', 'Table status', 'oso-super-cache');
                ;
            }
        }

        // Total table size
        $totalTableSize = $totalWordPressTableSize+$totalOtherTableSize;

        $unitAndDivisor = $this->getUnitAndDivisor($totalTableSize);
        $readableTotalTableSize = number_format_i18n($totalTableSize/$unitAndDivisor['divisor'], 2).' '.$unitAndDivisor['unit'];

        // Total saved bytes
        $unitAndDivisor = $this->getUnitAndDivisor($totalSavedBytes);
        $readableTotalSavedBytes = number_format_i18n($totalSavedBytes/$unitAndDivisor['divisor'], 2).' '.$unitAndDivisor['unit'];

        include Factory::get('Cache\Backend\Backend')->templatePath.'/optimize-database.html.php';
    }

    public function optimizeTables($tables)
    {
        global $wpdb;

        $totalSavedBytes = get_option('OSOSuperCacheOptimizedDatabaseSavedBytes', 0);
        $savedBytes = 0;

        foreach ($tables as $key => $value) {
            $tableName = stripslashes($value);

            $tableInformation = $this->getTableInformation($tableName);

            if (!empty($tableInformation)) {
                $savedBytes += $tableInformation->DATA_FREE;

                $wpdb->query('OPTIMIZE TABLE `'.$tableInformation->TABLE_NAME.'`');
            }
        }

        $totalSavedBytes += $savedBytes;

        update_option('OSOSuperCacheOptimizedDatabaseSavedBytes', $totalSavedBytes, 'no');

        Factory::get('Cache\Backend\Backend')->addMessage(_x('Tables successfully optimized.', 'Status message', 'oso-super-cache'), 'success');
    }

    public function getTableInformation($tableName)
    {
        global $wpdb;

        $tableInformation = $wpdb->get_results('
            SELECT
                *
            FROM
                INFORMATION_SCHEMA.`TABLES`
            WHERE
                `TABLE_SCHEMA`="'.$wpdb->dbname.'"
                AND
                `TABLE_NAME`="'.$wpdb->_escape($tableName).'"
        ');

        return !empty($tableInformation[0]) ? $tableInformation[0] : false;
    }

    public function getUnitAndDivisor($size)
    {
        if ($size/1024 >= 1 || empty($size)) {
            $divisor = 1024;
            $unit = 'KiB';
        }

        if ($size/1048576 > 1) {
            $divisor = 1048576;
            $unit = 'MiB';
        }

        if ($size/1073741824 > 1) {
            $divisor = 1073741824;
            $unit = 'GiB';
        }

        return [
            'divisor'=>$divisor,
            'unit'=>$unit,
        ];
    }
}
