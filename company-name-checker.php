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
require_once NCUK_PATH . 'includes/wizard-step1-storage.php';   //  ✅ IMPORTANT


/* ===============================================================
   ADMIN MENUS
===============================================================*/
add_action('admin_menu', function () {

    // Main menu
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
   ADMIN PAGE — LOAD SHORTCODE WIZARD INSIDE CLEAN CONTAINER
===============================================================*/
function ncuk_render_admin_shortcode_page() {
    ?>
    <div class="ncuk-admin-wrapper">

        <h1 style="margin-bottom:15px;">NameCheck Form</h1>

        <div class="notice notice-info" style="padding:10px 15px;margin-bottom:20px;">
            <p>You can complete company formation wizard here inside wp-admin.</p>
        </div>

        <!-- Fully isolated container -->
        <div class="ncuk-admin-shortcode-container">
            <?php echo do_shortcode('[company_formation_wizard]'); ?>
        </div>

    </div>
    <?php
}


/* ===============================================================
   ADMIN: LOAD ALL FRONTEND CSS/JS SO WIZARD WORKS 100%
===============================================================*/
add_action('admin_enqueue_scripts', function ($hook) {

    // Load ONLY on our pages
    if (
        $hook !== 'toplevel_page_namecheck-uk-settings' &&
        $hook !== 'namecheck-uk_page_namecheck-uk-admin-shortcode'
    ) {
        return;
    }

    /* --------------------------
       LOAD FRONTEND CSS
    --------------------------- */
    wp_enqueue_style(
        'ncuk-wizard-styles',
        NCUK_URL . 'assets/css/index.css',
        [],
        filemtime(NCUK_PATH . 'assets/css/index.css')
    );

    /* --------------------------
       LOAD FRONTEND JS FILES
    --------------------------- */

    // Name check live search JS
    wp_enqueue_script(
        'ncuk-namechecker-js',
        NCUK_URL . 'assets/js/company-name-checker.js',
        ['jquery'],
        null,
        true
    );

    // Step 1 wizard logic
    wp_enqueue_script(
        'ncuk-step1-js',
        NCUK_URL . 'assets/js/form-1.js',
        ['jquery'],
        null,
        true
    );

    // Pass AJAX URL
    wp_localize_script('ncuk-step1-js', 'ncuk_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

    /* --------------------------
       FIX ADMIN STYLING ISSUES
    --------------------------- */
    wp_add_inline_style('ncuk-wizard-styles', "
        .ncuk-admin-shortcode-container {
            background:#fff;
            padding:30px;
            margin-top:20px;
            border-radius:10px;
            box-shadow:0px 3px 15px rgba(0,0,0,0.1);
        }

        /* Prevent WP admin styles from breaking layout */
        .ncuk-admin-shortcode-container .company-formation-wrapper * {
            box-sizing:border-box !important;
        }

        /* Remove WP table spacing */
        .ncuk-admin-shortcode-container table {
            border-collapse:collapse !important;
        }

        /* Fix form width inside admin */
        .ncuk-admin-shortcode-container input,
        .ncuk-admin-shortcode-container select {
            max-width:100% !important;
        }

        /* Prevent WP themes from overriding wizard fonts */
        .ncuk-admin-shortcode-container {
            font-family:inherit;
        }
    ");

});
