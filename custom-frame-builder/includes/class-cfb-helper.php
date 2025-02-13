<?php
class CFB_Helper {
    public static function create_db_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cfb_frames';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            category_name varchar(255) NOT NULL,
            category_slug varchar(50) NOT NULL UNIQUE,
            category varchar(50) NOT NULL,
            has_plexiglass tinyint(1) DEFAULT 0,
            price_8x10 float NOT NULL,
            price_11x14 float NOT NULL,
            price_16x20 float NOT NULL,
            plexi_price_8x10 float DEFAULT NULL,
            plexi_price_11x14 float DEFAULT NULL,
            plexi_price_16x20 float DEFAULT NULL,
            plexi_ng_price_8x10 float DEFAULT NULL,
            plexi_ng_price_11x14 float DEFAULT NULL,
            plexi_ng_price_16x20 float DEFAULT NULL,
            image_url varchar(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Run function on plugin activation
register_activation_hook(__FILE__, array('CFB_Helper', 'create_db_table'));
