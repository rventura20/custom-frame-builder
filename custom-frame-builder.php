<?php
/**
 * Plugin Name: Custom Frame Builder for WooCommerce
 * Description: A custom framing plugin that allows customers to upload images, select frames, and preview their final product before purchasing.
 * Version: 1.0
 * Author: Agilecode LLC
 * Text Domain: custom-frame-builder
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include core functionality
require_once plugin_dir_path(__FILE__) . 'admin/class-cfb-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/class-cfb-public.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cfb-helper.php';

// Initialize Plugin
function cfb_run_plugin() {
    new CFB_Admin();
    new CFB_Public();
}
add_action('plugins_loaded', 'cfb_run_plugin');
