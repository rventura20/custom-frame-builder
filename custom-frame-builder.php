<?php
/*
Plugin Name: Custom Frame Builder
Plugin URI: https://yourwebsite.com
Description: A WooCommerce plugin that allows customers to customize their picture frames with uploaded images and dynamic moldings.
Version: 1.0.0
Author: Roberto Ventura
Text Domain: custom-frame-builder
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define Plugin Path
define('CFB_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include Necessary Files
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-init.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-customizer.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-design.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-preview.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-pricing.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-shortcode.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-woocommerce.php';
require_once CFB_PLUGIN_DIR . 'includes/class-cfb-admin.php'; // Admin frame upload panel

// Initialize Plugin
function cfb_initialize_plugin() {
    new CFB_Init();
    new CFB_Customizer();
    new CFB_Design();
    new CFB_Preview();
    new CFB_Pricing();
    new CFB_Shortcode();
    new CFB_WooCommerce();
    CFB_Admin_Settings()::get_instance(); // Initialize Admin Panel
}
add_action('plugins_loaded', 'cfb_initialize_plugin');

// Enqueue Scripts and Styles
function cfb_enqueue_scripts() {
    wp_enqueue_style('cfb-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-resizable');
    wp_enqueue_script('cfb-customizer', plugin_dir_url(__FILE__) . 'assets/js/customizer.js', array('jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'), null, true);
}
add_action('wp_enqueue_scripts', 'cfb_enqueue_scripts');

?>
