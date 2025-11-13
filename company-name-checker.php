<?php
/*
Plugin Name: NameCheck UK
Description: Check the availability of UK company names using the Companies House API.
Version: 2.0
Author: WEB HOSTING GURU (Modified by Talha)
*/

if (!defined('ABSPATH')) exit;

// Define constants
define('NCUK_PATH', plugin_dir_path(__FILE__));
define('NCUK_URL', plugin_dir_url(__FILE__));

// Include required files
require_once NCUK_PATH . 'includes/functions.php';
require_once NCUK_PATH . 'includes/reserved.php';

/* ===============================================================
   ADMIN MENUS
===============================================================*/
add_action('admin_menu', function () {

    add_menu_page(
        'NameCheck UK',
        'NameCheck UK',
        'manage_options',
        'namecheck-uk-settings',
        function () {
            include NCUK_PATH . 'settings.php';
        },
        'dashicons-search',
        25
    );

    // NEW SUBMENU → Wizard Tester
    add_submenu_page(
        'namecheck-uk-settings',
        'NameCheck Form',
        'NameCheck Form',
        'manage_options',
        'namecheck-uk-admin-shortcode',
        'ncuk_render_admin_shortcode_page'
    );
});

/* ===============================================================
   ADMIN PAGE — LOAD WIZARD SHORTCODE WITH ADMIN-SAFE WRAPPER
===============================================================*/
function ncuk_render_admin_shortcode_page() {
    ?>
    <div class="ncuk-admin-wrapper">
        <h1 style="margin-bottom:15px;">NameCheck Form</h1>

        <div class="notice notice-info" style="padding:10px 15px;margin-bottom:20px;">
            <p>You can complete company formation wizard here inside wp-admin.</p>
        </div>

        <!-- Wizard wrapped inside isolated admin-safe container -->
        <div class="ncuk-admin-shortcode-container">
            <?php echo do_shortcode('[company_formation_wizard]'); ?>
        </div>
    </div>
    <?php
}

/* ===============================================================
   ADMIN: LOAD FRONTEND CSS + JS INSIDE ADMIN PAGE
===============================================================*/
add_action('admin_enqueue_scripts', function($hook) {

    // Load ONLY on our custom submenu
    if ($hook !== 'toplevel_page_namecheck-uk-settings' &&
        $hook !== 'namecheck-uk_page_namecheck-uk-admin-shortcode') {
        return;
    }

    // Load all frontend CSS
    wp_enqueue_style(
        'ncuk-wizard-styles',
        NCUK_URL . 'assets/css/index.css',
        [],
        filemtime(NCUK_PATH . 'assets/css/index.css')
    );

    // Load name checker JS
    wp_enqueue_script(
        'ncuk-namechecker-js',
        NCUK_URL . 'assets/js/company-name-checker.js',
        ['jquery'],
        null,
        true
    );

    // Load step-1 JS (important)
    wp_enqueue_script(
        'ncuk-step1-js',
        NCUK_URL . 'assets/js/form-1.js',
        ['jquery'],
        null,
        true
    );

    // Localize AJAX URL
    wp_localize_script('ncuk-step1-js', 'ncuk_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

    // Admin-specific CSS to isolate layout
    wp_add_inline_style('ncuk-wizard-styles', "
        .ncuk-admin-shortcode-container {
            background:#fff;
            padding:30px;
            margin-top:20px;
            border-radius:10px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Prevent WP admin CSS from messing layout */
        .ncuk-admin-shortcode-container .company-formation-wrapper * {
            box-sizing:border-box;
        }

        /* Remove weird WP table row spacing */
        .ncuk-admin-shortcode-container table {
            border-collapse:collapse !important;
        }
    ");

});
