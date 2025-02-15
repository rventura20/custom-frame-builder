<?php
class CFB_Pricing {
    public function __construct() {
        add_filter('woocommerce_product_get_price', array($this, 'adjust_custom_price'), 10, 2);
    }

    public function adjust_custom_price($price, $product) {
        $frame_color = get_post_meta($product->get_id(), 'cfb_frame_color', true);
        return !empty($frame_color) ? $price + 10 : $price;
    }
}
