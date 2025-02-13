<?php
class CFB_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'cfb_add_admin_page'));
        add_action('admin_post_cfb_save_category_image', array($this, 'cfb_save_category_image'));
        add_action('admin_post_cfb_save_frame', array($this, 'cfb_save_frame_molding'));
    }

    // ✅ Add "Frame Builder" Page in WordPress Dashboard
    public function cfb_add_admin_page() {
        add_menu_page(
            'Frame Builder',
            'Frame Builder',
            'manage_options',
            'cfb_frame_builder',
            array($this, 'cfb_admin_page_content'),
            'dashicons-format-image',
            25
        );
    }

    // ✅ Admin Page: Upload Category Images & Frames
    public function cfb_admin_page_content() {
        global $wpdb;
        $categories = ['black_metal' => 'Black Metal', 'gold_metal' => 'Gold Metal', 'stretcher' => 'Stretcher'];
        $existing_images = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cfb_categories", OBJECT_K);

        ?>
        <div class="wrap">
            <h1>Frame Builder</h1>

            <!-- ✅ Frame Category Image Upload Section -->
            <h2>Upload Frame Category Images</h2>
            <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('cfb_save_category_image', 'cfb_category_image_nonce'); ?>
                <input type="hidden" name="action" value="cfb_save_category_image">
                <table class="form-table">
                    <?php foreach ($categories as $slug => $label): ?>
                        <tr>
                            <th><label><?php echo esc_html($label); ?>:</label></th>
                            <td>
                                <input type="file" name="category_<?php echo esc_attr($slug); ?>" accept="image/*">
                                <?php if (isset($existing_images[$slug])): ?>
                                    <br>
                                    <img src="<?php echo esc_url($existing_images[$slug]->image_url); ?>" width="80" style="border-radius:50%;">
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php submit_button('Save Category Images'); ?>
            </form>

            <!-- ✅ Frame Upload Section -->
            <h2>Upload Frame Moldings</h2>
            <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('cfb_save_frame', 'cfb_frame_nonce'); ?>
                <input type="hidden" name="action" value="cfb_save_frame">
                <table class="form-table">
                    <tr>
                        <th>Frame Name:</th>
                        <td><input type="text" name="frame_name" required></td>
                    </tr>
                    <tr>
                        <th>Frame Category:</th>
                        <td>
                            <select name="frame_category" id="frame_category">
                                <?php foreach ($categories as $slug => $label): ?>
                                    <option value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Upload Frame Image:</th>
                        <td><input type="file" name="frame_image" accept="image/*" required></td>
                    </tr>
                    
                    <!-- ✅ Pricing Per Size -->
                    <tr><th colspan="2"><h3>Frame Pricing</h3></th></tr>
                    <tr>
                        <th>Pricing for 8x10:</th>
                        <td><input type="number" name="price_8x10" step="0.01" required></td>
                    </tr>
                    <tr>
                        <th>Pricing for 11x14:</th>
                        <td><input type="number" name="price_11x14" step="0.01" required></td>
                    </tr>
                    <tr>
                        <th>Pricing for 16x20:</th>
                        <td><input type="number" name="price_16x20" step="0.01" required></td>
                    </tr>

                    <!-- ✅ Plexiglass Pricing (Only for Black & Gold Metal) -->
                    <tr id="plexiglass-section"><th colspan="2"><h3>Plexiglass Options</h3></th></tr>
                    <tr>
                        <th>Regular Plexiglass (8x10):</th>
                        <td><input type="number" name="plexi_price_8x10" step="0.01"></td>
                    </tr>
                    <tr>
                        <th>Regular Plexiglass (11x14):</th>
                        <td><input type="number" name="plexi_price_11x14" step="0.01"></td>
                    </tr>
                    <tr>
                        <th>Regular Plexiglass (16x20):</th>
                        <td><input type="number" name="plexi_price_16x20" step="0.01"></td>
                    </tr>
                    <tr>
                        <th>Non-Glare Plexiglass (8x10):</th>
                        <td><input type="number" name="plexi_ng_price_8x10" step="0.01"></td>
                    </tr>
                    <tr>
                        <th>Non-Glare Plexiglass (11x14):</th>
                        <td><input type="number" name="plexi_ng_price_11x14" step="0.01"></td>
                    </tr>
                    <tr>
                        <th>Non-Glare Plexiglass (16x20):</th>
                        <td><input type="number" name="plexi_ng_price_16x20" step="0.01"></td>
                    </tr>
                </table>
                <?php submit_button('Save Frame'); ?>
            </form>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                function togglePlexiglassOptions() {
                    let selectedCategory = document.getElementById("frame_category").value;
                    let plexiglassSection = document.getElementById("plexiglass-section");
                    
                    if (selectedCategory === "black_metal" || selectedCategory === "gold_metal") {
                        plexiglassSection.style.display = "table-row-group";
                    } else {
                        plexiglassSection.style.display = "none";
                    }
                }

                document.getElementById("frame_category").addEventListener("change", togglePlexiglassOptions);
                togglePlexiglassOptions(); // Run on page load
            });
        </script>
        <?php
    }
}
