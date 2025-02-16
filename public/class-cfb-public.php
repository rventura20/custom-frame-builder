<?php
class CFB_Public {
    public function __construct() {
        add_shortcode('cfb_frame_builder', array($this, 'cfb_display_frame_builder'));
        add_action('wp_enqueue_scripts', array($this, 'cfb_enqueue_assets'));

        // AJAX actions
        add_action('wp_ajax_cfb_get_frames', array($this, 'cfb_get_frames'));
        add_action('wp_ajax_nopriv_cfb_get_frames', array($this, 'cfb_get_frames'));
        add_action('wp_ajax_cfb_get_price', array($this, 'cfb_get_price'));
        add_action('wp_ajax_nopriv_cfb_get_price', array($this, 'cfb_get_price'));
        add_action('wp_ajax_cfb_add_to_cart', array($this, 'cfb_add_to_cart'));
        add_action('wp_ajax_nopriv_cfb_add_to_cart', array($this, 'cfb_add_to_cart'));
    }

    // ✅ Load CSS & JavaScript
    public function cfb_enqueue_assets() {
        wp_enqueue_style('cfb-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');
        wp_enqueue_script('cfb-script', plugin_dir_url(__FILE__) . '../assets/js/customizer.js', array('jquery'), null, true);
        wp_localize_script('cfb-script', 'customizer_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cfb_ajax_nonce')
        ));
    }

    // ✅ Display Frame Builder with Category Selection
    public function cfb_display_frame_builder() {
        global $wpdb;
        $categories = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cfb_categories");

        ob_start();
        ?>
        <h2>Choose Your Frame Category</h2>
        <div id="frame-category-selector">
            <?php foreach ($categories as $category): ?>
                <button class="frame-category-btn" data-category="<?php echo esc_attr($category->category_slug); ?>">
                    <img src="<?php echo esc_url($category->image_url); ?>" class="category-icon">
                    <span><?php echo esc_html($category->category_name); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <h3>Select a Frame</h3>
        <select id="frame-selector"></select>

        <h3>Select Size</h3>
        <select id="frame-size">
            <option value="8x10">8x10</option>
            <option value="11x14">11x14</option>
            <option value="16x20">16x20</option>
        </select>

        <h3>Select Plexiglass (Only for Black & Gold Metal)</h3>
        <select id="plexiglass-type">
            <option value="none">No Plexiglass</option>
            <option value="regular">Regular Plexiglass</option>
            <option value="non-glare">Non-Glare Plexiglass</option>
        </select>

        <h3>Price: $<span id="frame-price">0</span></h3>

        <h3>Upload Your Image</h3>
        <input type="file" id="user-image" accept="image/*">
        <canvas id="preview-canvas" width="500" height="500"></canvas>
        <button id="add-to-cart">Add to Cart</button>

        <?php return ob_get_clean();
    }

    // ✅ AJAX: Get Frames Based on Selected Category
    public function cfb_get_frames() {
        check_ajax_referer('cfb_ajax_nonce', 'security');

        if (!isset($_POST['category'])) {
            wp_send_json_error('Missing category');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'cfb_frames';
        $category = sanitize_text_field($_POST['category']);

        $frames = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE category = %s", $category));

        ob_start();
        foreach ($frames as $frame) {
            echo '<option value="'.esc_url($frame->image_url).'">'.esc_html($frame->name).'</option>';
        }
        wp_send_json_success(ob_get_clean());
    }

    // ✅ AJAX: Get Price Based on Frame, Size, and Plexiglass
    public function cfb_get_price() {
        check_ajax_referer('cfb_ajax_nonce', 'security');

        if (!isset($_POST['frame_id']) || !isset($_POST['size'])) {
            wp_send_json_error('Missing parameters');
        }

        global $wpdb;
        $frame_table = $wpdb->prefix . 'cfb_frames';
        $frame_id = sanitize_text_field($_POST['frame_id']);
        $size = sanitize_text_field($_POST['size']);
        $plexiglass = sanitize_text_field($_POST['plexiglass']);

        $frame = $wpdb->get_row($wpdb->prepare("SELECT * FROM $frame_table WHERE image_url = %s", $frame_id));

        if (!$frame) {
            wp_send_json_error('Frame not found');
        }

        // Select size pricing
        $price = 0;
        if ($size == "8x10") $price = $frame->price_8x10;
        if ($size == "11x14") $price = $frame->price_11x14;
        if ($size == "16x20") $price = $frame->price_16x20;

        // Add plexiglass pricing
        if ($plexiglass == "regular") {
            if ($size == "8x10") $price += $frame->plexi_price_8x10;
            if ($size == "11x14") $price += $frame->plexi_price_11x14;
            if ($size == "16x20") $price += $frame->plexi_price_16x20;
        } elseif ($plexiglass == "non-glare") {
            if ($size == "8x10") $price += $frame->plexi_ng_price_8x10;
            if ($size == "11x14") $price += $frame->plexi_ng_price_11x14;
            if ($size == "16x20") $price += $frame->plexi_ng_price_16x20;
        }

        wp_send_json_success($price);
    }

    // ✅ AJAX: Add to WooCommerce Cart
    public function cfb_add_to_cart() {
        check_ajax_referer('cfb_ajax_nonce', 'security');

        if (!isset($_POST['frame_id']) || !isset($_POST['size'])) {
            wp_send_json_error('Missing parameters');
        }

        $product_id = 123; // Replace with actual WooCommerce product ID

        $cart_item_data = array(
            'frame_image' => sanitize_text_field($_POST['frame_id']),
            'size' => sanitize_text_field($_POST['size']),
            'plexiglass' => sanitize_text_field($_POST['plexiglass']),
            'user_image' => esc_url_raw($_POST['user_image'])
        );

        WC()->cart->add_to_cart($product_id, 1, '', array(), $cart_item_data);
        wp_send_json_success();
    }
}
