<?php
/*
 *
 */

namespace OSOSuperCache\Cache;

use OSOSuperCache\Factory;

class Install
{

    private static $instance;

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

    public function __construct()
    {
    }

    public function installPlugin()
    {
        global $wpdb;

        $tableName = $wpdb->base_prefix.'oso_super_cache_pages';
        $charsetCollate = $wpdb->get_charset_collate();

        $sqlCreateTable = $this->getCreateTableStatement($tableName, $charsetCollate);

        require_once ABSPATH.'wp-admin/includes/upgrade.php';

        dbDelta($sqlCreateTable);

        // Check if key exists
        $checkPrimaryKey = $wpdb->query("SHOW INDEXES FROM `".$tableName."` WHERE `Key_name`='PRIMARY'");

        if (!$checkPrimaryKey) {
            // Set primary key
            $sqlAddPrimaryKey = "ALTER TABLE `".$tableName."` ADD PRIMARY KEY (`hash`, `https`)";
            $wpdb->query($sqlAddPrimaryKey);
        }

        // Update table structure
        $this->updateTable($tableName);

        update_option('OSOSuperCacheVersion', OSO_SUPER_CACHE_VERSION, 'no');

        if (is_multisite()) {
            $allBlogs = $wpdb->get_results('
                SELECT
                    `blog_id`
                FROM
                    `'.$wpdb->base_prefix.'blogs`
            ');

            if (!empty($allBlogs)) {
                $originalBlogId = get_current_blog_id();

                foreach ($allBlogs as $blogData) {
                    if ($blogData->blog_id != 1) {
                        switch_to_blog($blogData->blog_id);

                        $tableName = $wpdb->prefix.'oso_super_cache_pages'; // ->prefix contains base_prefix + blog id

                        $sqlCreateTable = $this->getCreateTableStatement($tableName, $charsetCollate);

                        dbDelta($sqlCreateTable);

                        $this->updateTable($tableName);

                        // Check if key exists
                        $checkPrimaryKey = $wpdb->query("SHOW INDEXES FROM `".$tableName."` WHERE `Key_name`='PRIMARY'");

                        if (!$checkPrimaryKey) {
                            // Set primary key
                            $sqlAddPrimaryKey = "ALTER TABLE `".$tableName."` ADD PRIMARY KEY (`hash`, `https`)";
                            $wpdb->query($sqlAddPrimaryKey);
                        }

                        update_option('OSOSuperCacheVersion', OSO_SUPER_CACHE_VERSION, 'no');
                    }
                }

                switch_to_blog($originalBlogId);
            }
        }
    }

    public function getCreateTableStatement($tableName, $charsetCollate)
    {
        // Yes, a post_type is limited to 20 chars, but some devs change this value...
        return "CREATE TABLE IF NOT EXISTS ".$tableName." (
            domain VARCHAR(63) NOT NULL,
            hash VARCHAR(40) NOT NULL,
            https INT(1) unsigned DEFAULT 0,
            prefix VARCHAR(16) DEFAULT '',
            url TEXT NOT NULL,
            post_id INT(10) unsigned DEFAULT 0,
            post_type VARCHAR(255) DEFAULT '',
            taxonomy VARCHAR(255) DEFAULT '',
            term VARCHAR(255) DEFAULT '',
            preload_data TEXT DEFAULT '',
            conditions TEXT DEFAULT '',
            dont_cache INT(1) unsigned DEFAULT 0,
            is_home INT(1) unsigned DEFAULT 0,
            is_archive INT(1) unsigned DEFAULT 0,
            is_feed INT(1) unsigned DEFAULT 0,
            is_404 INT(1) unsigned DEFAULT 0,
            runtime_without_cache DECIMAL(12,8) DEFAULT '0.00000000',
            runtime_with_cache DECIMAL(12,8) DEFAULT '0.00000000',
            last_updated DATETIME NOT NULL,
            next_update DATETIME NOT NULL
        ) ".$charsetCollate.";";
    }

    public function updateTable($tableName)
    {
        global $wpdb;

        if (!$this->checkIfColumnExists($tableName, 'https')) {
            $wpdb->query('
                ALTER TABLE
                    `'.$tableName.'`
                ADD
                    `https` INT(1) unsigned DEFAULT 0
                    AFTER `hash`
            ');
        }

        // Check primary key
        $primaryKeyResult = $wpdb->get_results('
            SHOW INDEXES FROM
                `'.$tableName.'`
            WHERE `Key_name`="PRIMARY"
        ');

        if (!$this->checkIfColumnExists($tableName, 'prefix')) {
            $wpdb->query('
                ALTER TABLE
                    `'.$tableName.'`
                ADD
                    `prefix` VARCHAR(16) DEFAULT ""
                    AFTER `https`
            ');
        }

        // Check if only one PRIMARY key exists
        if (count($primaryKeyResult) == 1) {
            // Remove old PRIMARY key and add new PRIMARY key
            $wpdb->query('
                ALTER TABLE
                    `'.$tableName.'`
                DROP PRIMARY KEY,
                ADD PRIMARY KEY(`hash`, `https`)
            ');
        }
    }

    public function checkIfColumnExists($tableName, $column)
    {
        global $wpdb;

        $columnResult = $wpdb->get_results('
            SHOW COLUMNS FROM
                `'.$tableName.'`
            WHERE
                `Field`="'.$column.'"
        ');

        if (!empty($columnResult[0]->Field)) {
            return true;
        } else {
            return false;
        }
    }
}
