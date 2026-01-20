<?php
/**
 * STEP 3 – SAVE ALL OFFICERS (BULK)
 * This file MUST be required globally by the plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register AJAX actions
 */
add_action('wp_ajax_ncuk_save_step3_all_officers', 'ncuk_save_step3_all_officers');
add_action('wp_ajax_nopriv_ncuk_save_step3_all_officers', 'ncuk_save_step3_all_officers');

function ncuk_save_step3_all_officers() {
    global $wpdb;

    // --------------------------------------------------
    // BASIC LOG – confirms handler is reached
    // --------------------------------------------------
    error_log('STEP3 AJAX: handler reached');

    // --------------------------------------------------
    // ACTION SAFETY CHECK (WordPress already routed here,
    // but we log anyway for sanity)
    // --------------------------------------------------
    if (!isset($_POST['action']) || $_POST['action'] !== 'ncuk_save_step3_all_officers') {
        error_log('STEP3 ERROR: action missing or incorrect');
        wp_send_json_error(['msg' => 'Invalid AJAX action']);
    }

    // --------------------------------------------------
    // NONCE CHECK
    // --------------------------------------------------
    if (empty($_POST['nonce'])) {
        error_log('STEP3 ERROR: nonce missing');
        wp_send_json_error(['msg' => 'Nonce missing']);
    }

    if (!wp_verify_nonce($_POST['nonce'], 'ncuk_step3_nonce')) {
        error_log('STEP3 ERROR: nonce invalid');
        wp_send_json_error(['msg' => 'Invalid nonce']);
    }

    // --------------------------------------------------
    // SESSION / TOKEN CHECK
    // --------------------------------------------------
    if (!session_id()) {
        session_start();
    }

    $token = $_SESSION['companyformation_token'] ?? '';

    if (!$token) {
        error_log('STEP3 ERROR: session token missing');
        wp_send_json_error(['msg' => 'Session token missing']);
    }

    // --------------------------------------------------
    // OFFICERS PAYLOAD CHECK
    // --------------------------------------------------
    if (empty($_POST['officers'])) {
        error_log('STEP3 ERROR: officers payload missing');
        wp_send_json_error(['msg' => 'Officers data missing']);
    }

    $officers = json_decode(stripslashes($_POST['officers']), true);

    if (!is_array($officers) || empty($officers)) {
        error_log('STEP3 ERROR: officers JSON invalid');
        wp_send_json_error(['msg' => 'Invalid officers data']);
    }

    // --------------------------------------------------
    // DB TABLE
    // --------------------------------------------------
    $table = $wpdb->prefix . 'companyformation_officers';

    // --------------------------------------------------
    // INSERT EACH OFFICER
    // --------------------------------------------------
    foreach ($officers as $index => $o) {

        error_log('STEP3 INFO: inserting officer #' . ($index + 1));

        $data = [
            'token' => $token,
            'officer_type' => sanitize_text_field($o['officer_type'] ?? ''),

            'role_director'    => !empty($o['roles']['director']) ? 1 : 0,
            'role_shareholder' => !empty($o['roles']['shareholder']) ? 1 : 0,
            'role_secretary'   => !empty($o['roles']['secretary']) ? 1 : 0,
            'role_psc'         => !empty($o['roles']['psc']) ? 1 : 0,

            'first_name' => sanitize_text_field($o['first'] ?? ''),
            'last_name'  => sanitize_text_field($o['last'] ?? ''),
            'email'      => sanitize_email($o['email'] ?? ''),
            'dob'        => sanitize_text_field($o['dob'] ?? ''),
            'consent_auth' => !empty($o['consent']) ? 1 : 0,

            'res_line1'    => sanitize_text_field($o['residential']['line1'] ?? ''),
            'res_town'     => sanitize_text_field($o['residential']['town'] ?? ''),
            'res_postcode' => sanitize_text_field($o['residential']['postcode'] ?? ''),

            'share_quantity' => intval($o['shares']['quantity'] ?? 0),

            'psc_company_shares'    => sanitize_text_field($o['noc']['company_shares'] ?? 'na'),
            'psc_company_voting'    => sanitize_text_field($o['noc']['company_voting'] ?? 'na'),
            'psc_company_directors' => intval($o['noc']['company_directors'] ?? 0),
            'psc_company_other'     => intval($o['noc']['company_other'] ?? 0),
        ];

        $result = $wpdb->insert($table, $data);

        if ($result === false) {
            error_log('STEP3 DB ERROR: ' . $wpdb->last_error);
            wp_send_json_error([
                'msg' => 'Database insert failed',
                'db_error' => $wpdb->last_error
            ]);
        }
    }

    // --------------------------------------------------
    // SUCCESS
    // --------------------------------------------------
    error_log('STEP3 SUCCESS: all officers saved');

    wp_send_json_success([
        'saved' => true,
        'next_url' => site_url('/company-formation/step-4/')
    ]);
}
