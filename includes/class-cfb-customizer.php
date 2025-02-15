<?php
class CFB_Customizer {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'load_customizer_scripts'));
        add_shortcode('cfb_live_preview', array($this, 'render_live_preview'));
    }

    public function load_customizer_scripts() {
        wp_enqueue_script('cfb-customizer', CFB_PLUGIN_URL . 'assets/customizer.js', array('jquery', 'jquery-ui-draggable', 'jquery-ui-resizable'), null, true);
        wp_enqueue_style('cfb-style', CFB_PLUGIN_URL . 'assets/style.css');
    }

   
    
    public function render_live_preview() {
        return '<div id="cfb-customizer-container">
            <div id="cfb-live-preview">
                <canvas id="frame-preview-canvas"></canvas>
            </div>
            <div id="cfb-buttons">
                <input type="file" id="frame-image-upload" accept="image/*" />
                <button id="download-frame-design">Download</button>
            </div>
        </div>';
    }
    
}
