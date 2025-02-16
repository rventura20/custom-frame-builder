<?php
class CFB_Helper {
    /**
     * Create the necessary database tables on plugin activation
     */
    public static function create_db_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table: Frame Categories
        $table_categories = $wpdb->prefix . 'cfb_categories';
        $sql_categories = "CREATE TABLE IF NOT EXISTS $table_categories (
            id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
            category_name VARCHAR(50) NOT NULL,
            category_slug VARCHAR(50) NOT NULL UNIQUE,
            image_url VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Table: Frame Moldings
        $table_frames = $wpdb->prefix . 'cfb_frames';
        $sql_frames = "CREATE TABLE IF NOT EXISTS $table_frames (
            id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            price_8x10 FLOAT NOT NULL,
            price_11x14 FLOAT NOT NULL,
            price_16x20 FLOAT NOT NULL,
            plexi_price_8x10 FLOAT DEFAULT NULL,
            plexi_price_11x14 FLOAT DEFAULT NULL,
            plexi_price_16x20 FLOAT DEFAULT NULL,
            plexi_ng_price_8x10 FLOAT DEFAULT NULL,
            plexi_ng_price_11x14 FLOAT DEFAULT NULL,
            plexi_ng_price_16x20 FLOAT DEFAULT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (category) REFERENCES $table_categories(category_slug) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_categories);
        dbDelta($sql_frames);
    }

    /**
     * Insert default categories on activation
     */
    public static function insert_default_categories() {
        global $wpdb;
        $table_categories = $wpdb->prefix . 'cfb_categories';

        $default_categories = [
            ['Black Metal', 'black_metal'],
            ['Gold Metal', 'gold_metal'],
            ['Stretcher', 'stretcher']
        ];

        foreach ($default_categories as $category) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_categories WHERE category_slug = %s", $category[1]));

            if ($exists == 0) {
                $wpdb->insert($table_categories, [
                    'category_name' => $category[0],
                    'category_slug' => $category[1],
                    'image_url' => ''  // Admin will upload images later
                ]);
            }
        }
    }

    /**
     * Delete database tables on plugin uninstall
     */
    public static function delete_db_tables() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}cfb_frames");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}cfb_categories");
    }
}

// Run function on plugin activation
register_activation_hook(__FILE__, function() {
    CFB_Helper::create_db_tables();
    CFB_Helper::insert_default_categories();
});

// Run function on plugin uninstall
register_uninstall_hook(__FILE__, function() {
    CFB_Helper::delete_db_tables();
});
