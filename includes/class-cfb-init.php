<?php
class CFB_Init {
    public function __construct() {
        add_action('init', array($this, 'register_assets'));
    }

    public function register_assets() {
        wp_enqueue_style('cfb-style', CFB_PLUGIN_URL . 'assets/style.css');
        wp_enqueue_script('cfb-script', CFB_PLUGIN_URL . 'assets/customizer.js', array('jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'), null, true);
    }
}
