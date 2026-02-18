<?php
/*
Plugin Name: NameCheck UK
Description: Check the availability of UK company names using the Companies House API.
Version: 2.0
Author: WEB HOSTING GURU (Modified by Talha)
*/

if (!defined('ABSPATH')) exit;

/* ===============================================================
   CONSTANTS
===============================================================*/
define('NCUK_PATH', plugin_dir_path(__FILE__));
define('NCUK_URL', plugin_dir_url(__FILE__));

/* ===============================================================
   CHECK IF WOOCOMMERCE IS ACTIVE
===============================================================*/
function ncuk_is_woocommerce_active() {
    return class_exists('WooCommerce');
}

/* ===============================================================
   INCLUDE REQUIRED FILES
===============================================================*/
require_once NCUK_PATH . 'includes/functions.php';
require_once NCUK_PATH . 'includes/reserved.php';
require_once NCUK_PATH . 'includes/wizard-step1-storage.php';
require_once NCUK_PATH . 'includes/wizard-step2-storage.php'; // ✅ IMPORTANT
require_once NCUK_PATH . 'includes/wizard-step3-storage.php'; // ✅ IMPORTANT

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

    // Submenu: Wizard
    add_submenu_page(
        'namecheck-uk-settings',
        'NameCheck Form',
        'NameCheck Form',
        'manage_options',
        'namecheck-uk-admin-shortcode',
        'ncuk_render_admin_shortcode_page'
    );

    // Submenu: Registered Address
    add_submenu_page(
        'namecheck-uk-settings',
        'Registered Address',
        'Registered Address',
        'manage_options',
        'namecheck-uk-registered-address',
        'ncuk_render_registered_address_page'
    );

    // NEW Submenu: Submissions
    add_submenu_page(
        'namecheck-uk-settings',
        'Submissions',
        'Submissions',
        'manage_options',
        'namecheck-uk-submissions',
        function () {
            include NCUK_PATH . 'submissions.php';
        }
    );
});

/* ===============================================================
   REGISTERED ADDRESS PAGE
===============================================================*/
function ncuk_render_registered_address_page() {
    include NCUK_PATH . 'registered-address.php';
}

/* ===============================================================
   ADMIN PAGE — WOO-STYLED CONTAINER
===============================================================*/
function ncuk_render_admin_shortcode_page() {
    ?>
    <div class="wrap woocommerce">

        <h1>NameCheck Form</h1>

        <div class="notice notice-info">
            <p>You can complete the company formation wizard here inside wp-admin.</p>
        </div>

        <div class="ncuk-admin-shortcode-container">
            <?php echo do_shortcode('[company_formation_wizard]'); ?>
        </div>

    </div>
    <?php
}

/* ===============================================================
   ADMIN: LOAD STYLES & SCRIPTS (WOO + PLUGIN)
===============================================================*/
add_action('admin_enqueue_scripts', function ($hook) {

    // Only load on our plugin pages
    if (
        $hook !== 'toplevel_page_namecheck-uk-settings' &&
        $hook !== 'namecheck-uk_page_namecheck-uk-admin-shortcode'
    ) {
        return;
    }

    /* --------------------------
       WOOCOMMERCE ADMIN STYLES
    --------------------------- */
    if (ncuk_is_woocommerce_active()) {
        wp_enqueue_style('woocommerce_admin_styles');
        wp_enqueue_style('woocommerce_admin_menu_styles');
        wp_enqueue_script('woocommerce_admin');
    }

    /* --------------------------
       PLUGIN FRONTEND CSS
    --------------------------- */
    wp_enqueue_style(
        'ncuk-wizard-styles',
        NCUK_URL . 'assets/css/index.css',
        [],
        filemtime(NCUK_PATH . 'assets/css/index.css')
    );

    /* --------------------------
       PLUGIN JS FILES
    --------------------------- */

    // Company name checker
    wp_enqueue_script(
        'ncuk-namechecker-js',
        NCUK_URL . 'assets/js/company-name-checker.js',
        ['jquery'],
        filemtime(NCUK_PATH . 'assets/js/company-name-checker.js'),
        true
    );

    // Step 1 wizard logic
    wp_enqueue_script(
        'ncuk-step1-js',
        NCUK_URL . 'assets/js/form-1.js',
        ['jquery'],
        filemtime(NCUK_PATH . 'assets/js/form-1.js'),
        true
    );

    // AJAX URL
    wp_localize_script('ncuk-step1-js', 'ncuk_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

    /* --------------------------
       ADMIN SAFETY FIXES
    --------------------------- */
    wp_add_inline_style('ncuk-wizard-styles', "
       

        .ncuk-admin-shortcode-container .company-formation-wrapper * {
            box-sizing: border-box !important;
        }

        .ncuk-admin-shortcode-container table {
            border-collapse: collapse !important;
        }

        .ncuk-admin-shortcode-container input,
        .ncuk-admin-shortcode-container select {
            max-width: 100% !important;
        }

        .ncuk-admin-shortcode-container {
            font-family: inherit;
        }
    ");
});
