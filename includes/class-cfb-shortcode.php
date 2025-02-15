<?php
class CFB_Shortcode {
    public function __construct() {
        add_shortcode('cfb_display', array($this, 'render_shortcode'));
    }

    public function render_shortcode() {
        return '<div id="cfb-customizer-container">' . do_shortcode('[cfb_live_preview]') . '</div>';
    }
}
