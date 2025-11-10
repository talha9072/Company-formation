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

// Add admin menu
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
});
