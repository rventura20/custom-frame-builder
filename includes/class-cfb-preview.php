<?php
class CFB_Preview {
    public function __construct() {
        add_action('wp_ajax_cfb_generate_preview', array($this, 'generate_preview'));
    }

    public function generate_preview() {
        $design_data = sanitize_text_field($_POST['design_data']);
        $image_url = $this->create_image_from_design($design_data);
        wp_send_json_success(['preview_url' => $image_url]);
    }
}
