<?php
/**
 * Plugin Name: Custom Frame Builder
 * Description: A WooCommerce plugin for customizing picture frames with live preview, dynamic pricing, and order integration.
 * Version: 1.0.0
 * Author: Roberto Ventura
 * Text Domain: custom-frame-builder
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Plugin Constants
define('CFB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CFB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include Core Files
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-init.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-customizer.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-shortcode.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-woocommerce.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-pricing.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-design.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-preview.php';

// Initialize the Plugin
function cfb_initialize_plugin() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>Custom Frame Builder</strong> requires WooCommerce to be installed and activated.</p></div>';
        });
        return;
    }
    
    new CFB_Init();
    new CFB_Customizer();
    new CFB_Shortcode();
    new CFB_WooCommerce();
    new CFB_Pricing();
    new CFB_Design();
    new CFB_Preview();
}
add_action('plugins_loaded', 'cfb_initialize_plugin');
