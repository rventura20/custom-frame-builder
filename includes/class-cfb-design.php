<?php
class CFB_Design {
    public function __construct() {
        add_action('wp_ajax_cfb_save_design', array($this, 'save_design'));
    }

    public function save_design() {
        $user_id = get_current_user_id();
        $design_data = sanitize_text_field($_POST['design_data']);
        update_user_meta($user_id, 'cfb_saved_design', $design_data);
        wp_send_json_success(['message' => 'Design saved!']);
    }
}
