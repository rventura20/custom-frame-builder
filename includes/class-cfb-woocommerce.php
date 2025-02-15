<?php
class CFB_WooCommerce {
    public function __construct() {
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_frame_customization_to_cart'), 10, 2);
        add_filter('woocommerce_get_item_data', array($this, 'display_frame_customization_in_cart'), 10, 2);
        add_action('woocommerce_add_order_item_meta', array($this, 'save_customization_to_order'), 10, 3);
        add_filter('woocommerce_order_item_display_meta_key', array($this, 'rename_meta_keys'), 10, 2);
    }

    public function add_frame_customization_to_cart($cart_item_data, $product_id) {
        if (isset($_POST['cfb_frame_color'])) {
            $cart_item_data['cfb_frame_color'] = sanitize_text_field($_POST['cfb_frame_color']);
        }
        if (isset($_POST['cfb_uploaded_image'])) {
            $cart_item_data['cfb_uploaded_image'] = esc_url($_POST['cfb_uploaded_image']);
        }
        return $cart_item_data;
    }

    public function display_frame_customization_in_cart($item_data, $cart_item) {
        if (isset($cart_item['cfb_frame_color'])) {
            $item_data[] = array(
                'name' => __('Frame Color', 'custom-frame-builder'),
                'value' => sanitize_text_field($cart_item['cfb_frame_color']),
            );
        }
        if (isset($cart_item['cfb_uploaded_image'])) {
            $encoded_img_url = esc_url($cart_item['cfb_uploaded_image']);
            $item_data[] = array(
                'name' => __('Uploaded Image', 'custom-frame-builder'),
                'value' => '<img src="'.$encoded_img_url.'" width="50">',
            );
        }
        return $item_data;
    }

    public function save_customization_to_order($item_id, $values, $cart_item_key) {
        if (isset($values['cfb_frame_color'])) {
            wc_add_order_item_meta($item_id, '_cfb_frame_color', $values['cfb_frame_color']);
        }
        if (isset($values['cfb_uploaded_image'])) {
            wc_add_order_item_meta($item_id, '_cfb_uploaded_image', $values['cfb_uploaded_image']);
        }
    }

    public function rename_meta_keys($display_key, $meta) {
        if ($display_key == '_cfb_frame_color') {
            return __('Frame Color', 'custom-frame-builder');
        }
        if ($display_key == '_cfb_uploaded_image') {
            return __('Uploaded Image', 'custom-frame-builder');
        }
        return $display_key;
    }
}
new CFB_WooCommerce();
