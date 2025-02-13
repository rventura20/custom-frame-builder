<?php
class CFB_Public {
    public function __construct() {
        add_shortcode('cfb_frame_builder', array($this, 'cfb_display_frame_builder'));
        add_action('wp_enqueue_scripts', array($this, 'cfb_enqueue_assets'));
    }

    // ✅ Load CSS & JavaScript
    public function cfb_enqueue_assets() {
        wp_enqueue_style('cfb-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');
        wp_enqueue_script('cfb-script', plugin_dir_url(__FILE__) . '../assets/js/customizer.js', array('jquery'), null, true);
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

        <script>
            jQuery(document).ready(function($) {
                // ✅ Handle Frame Category Selection
                $(".frame-category-btn").click(function() {
                    let selectedCategory = $(this).data("category");

                    $(".frame-category-btn").removeClass("active");
                    $(this).addClass("active");

                    $.post("<?php echo admin_url('admin-ajax.php'); ?>", {
                        action: "cfb_get_frames",
                        category: selectedCategory
                    }, function(response) {
                        $("#frame-selector").html(response);
                        $("#frame-selector").trigger("change");
                    });

                    // Show Plexiglass selection only for Black Metal & Gold Metal
                    if (selectedCategory === "black_metal" || selectedCategory === "gold_metal") {
                        $("#plexiglass-type").show();
                    } else {
                        $("#plexiglass-type").hide();
                    }
                });

                // ✅ Handle Frame & Size Selection
                $("#frame-selector, #frame-size, #plexiglass-type").change(function() {
                    let frameId = $("#frame-selector").val();
                    let frameSize = $("#frame-size").val();
                    let plexiglassType = $("#plexiglass-type").val();

                    $.post("<?php echo admin_url('admin-ajax.php'); ?>", {
                        action: "cfb_get_price",
                        frame_id: frameId,
                        size: frameSize,
                        plexiglass: plexiglassType
                    }, function(response) {
                        $("#frame-price").text(response);
                    });
                });

                $(".frame-category-btn").first().trigger("click");
            });
        </script>

        <style>
            .frame-category-btn {
                border: none;
                background: none;
                cursor: pointer;
                text-align: center;
                margin: 10px;
            }
            .category-icon {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                border: 2px solid #000;
            }
            .frame-category-btn.active .category-icon {
                border: 3px solid #f00;
            }
            #plexiglass-type {
                display: none;
            }
        </style>
        <?php
        return ob_get_clean();
    }
}

// ✅ AJAX to Fetch Frames by Category
add_action('wp_ajax_cfb_get_frames', 'cfb_get_frames');
add_action('wp_ajax_nopriv_cfb_get_frames', 'cfb_get_frames');

function cfb_get_frames() {
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

// ✅ AJAX to Fetch Price Based on Frame, Size, and Plexiglass
add_action('wp_ajax_cfb_get_price', 'cfb_get_price');
add_action('wp_ajax_nopriv_cfb_get_price', 'cfb_get_price');

function cfb_get_price() {
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
