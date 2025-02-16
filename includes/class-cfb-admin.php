<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('CFB_Admin_Settings')) {
    class CFB_Admin_Settings {
        private static $instance = null;

        public static function get_instance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
            add_action('admin_menu', array($this, 'cfb_register_frame_settings'));
        }

        public function cfb_register_frame_settings() {
            add_menu_page(
                'Frame Settings',
                'Frame Settings',
                'manage_options',
                'cfb-frame-settings',
                array($this, 'cfb_frame_settings_page')
            );
        }

        public function cfb_frame_settings_page() {
            ?>
            <div class="wrap">
                <h1>Upload Frame Moldings</h1>
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('cfb_upload_frames', 'cfb_frame_nonce'); ?>
                    <input type="file" name="frame_molding" accept="image/svg+xml, image/png, image/jpeg">
                    <input type="submit" name="upload_frame" value="Upload Frame" class="button button-primary">
                </form>
            </div>
            <?php

            // Ensure upload handling only runs once per form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_frame'])) {
                if (!isset($_SESSION['cfb_frame_uploaded'])) {
                    $_SESSION['cfb_frame_uploaded'] = true;
                    $this->cfb_handle_frame_upload();
                }
            }
        }

        private function cfb_handle_frame_upload() {
            if (check_admin_referer('cfb_upload_frames', 'cfb_frame_nonce')) {
                if (!empty($_FILES['frame_molding']['name'])) {
                    $uploaded = wp_upload_bits(
                        $_FILES['frame_molding']['name'],
                        null,
                        file_get_contents($_FILES['frame_molding']['tmp_name'])
                    );

                    if (!$uploaded['error']) {
                        $frame_urls = get_option('cfb_frame_moldings', []);
                        $frame_urls[] = $uploaded['url'];
                        update_option('cfb_frame_moldings', $frame_urls);
                        echo '<p style="color:green;">Frame uploaded successfully!</p>';
                    } else {
                        echo '<p style="color:red;">Error uploading frame: ' . esc_html($uploaded['error']) . '</p>';
                    }
                } else {
                    echo '<p style="color:red;">Please select a file to upload.</p>';
                }
            }
        }
    }

    // Ensure only one instance of the class is initialized
    add_action('plugins_loaded', function () {
        CFB_Admin_Settings::get_instance();
    });
}
