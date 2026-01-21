<?php
/**
 * STEP 3 – SAVE ALL OFFICERS (BULK)
 * This file MUST be required globally by the plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_ncuk_save_step3_all_officers', 'ncuk_save_step3_all_officers');
add_action('wp_ajax_nopriv_ncuk_save_step3_all_officers', 'ncuk_save_step3_all_officers');

function ncuk_save_step3_all_officers() {
    global $wpdb;

    error_log('STEP3 AJAX: handler reached');

    /* ------------------------------------
       Nonce
    ------------------------------------ */
    if (empty($_POST['nonce'])) {
        error_log('STEP3 ERROR: nonce missing');
        wp_send_json_error(['msg' => 'Nonce missing']);
    }

    if (!wp_verify_nonce($_POST['nonce'], 'ncuk_step3_nonce')) {
        error_log('STEP3 ERROR: nonce invalid');
        wp_send_json_error(['msg' => 'Invalid nonce']);
    }

    /* ------------------------------------
       Session token
    ------------------------------------ */
    if (!session_id()) {
        session_start();
    }

    $token = $_SESSION['companyformation_token'] ?? '';

    if (!$token) {
        error_log('STEP3 ERROR: session token missing');
        wp_send_json_error(['msg' => 'Session token missing']);
    }

    /* ------------------------------------
       Officers payload
    ------------------------------------ */
    if (empty($_POST['officers'])) {
        error_log('STEP3 ERROR: officers payload missing');
        wp_send_json_error(['msg' => 'Officers data missing']);
    }

    $officers = json_decode(stripslashes($_POST['officers']), true);

    if (!is_array($officers)) {
        error_log('STEP3 ERROR: officers JSON invalid');
        wp_send_json_error(['msg' => 'Invalid officers data']);
    }

    $table = $wpdb->prefix . 'companyformation_officers';

    /* ------------------------------------
       🔥 SNAPSHOT REPLACE (THE FIX)
    ------------------------------------ */
    $deleted = $wpdb->delete($table, ['token' => $token]);

    if ($deleted === false) {
        error_log('STEP3 DB ERROR (DELETE): ' . $wpdb->last_error);
        wp_send_json_error(['msg' => 'Failed clearing old officers']);
    }

    error_log("STEP3 INFO: deleted {$deleted} officers for token {$token}");

    /* ------------------------------------
       Insert snapshot
    ------------------------------------ */
    foreach ($officers as $i => $o) {

        error_log('STEP3 INFO: inserting officer #' . ($i + 1));

       $service = $o['service'] ?? [];

$data = [

    'token' => $token,
    'officer_type' => sanitize_text_field($o['officer_type'] ?? ''),

    // ROLES
    'role_director'    => !empty($o['roles']['director']) ? 1 : 0,
    'role_shareholder' => !empty($o['roles']['shareholder']) ? 1 : 0,
    'role_secretary'   => !empty($o['roles']['secretary']) ? 1 : 0,
    'role_psc'         => !empty($o['roles']['psc']) ? 1 : 0,

    // PERSONAL
    'title'       => sanitize_text_field($o['title'] ?? null),
    'first_name'  => sanitize_text_field($o['first'] ?? ''),
    'last_name'   => sanitize_text_field($o['last'] ?? ''),
    'email'       => sanitize_email($o['email'] ?? ''),
    'dob'         => !empty($o['dob']) ? date('Y-m-d', strtotime($o['dob'])) : null,
    'nationality' => sanitize_text_field($o['nationality'] ?? null),

    // CONSENT
    'consent_auth' => !empty($o['consent']) ? 1 : 0,

    // RESIDENTIAL
    'res_line1'    => sanitize_text_field($o['residential']['line1'] ?? ''),
    'res_line2'    => sanitize_text_field($o['residential']['line2'] ?? null),
    'res_line3'    => sanitize_text_field($o['residential']['line3'] ?? null),
    'res_town'     => sanitize_text_field($o['residential']['town'] ?? ''),
    'res_country'  => sanitize_text_field($o['residential']['country'] ?? ''),
    'res_postcode' => sanitize_text_field($o['residential']['postcode'] ?? ''),

    // 🔥 SERVICE ADDRESS (THIS IS THE REAL FIX)
    'service_addr_type' => sanitize_text_field($service['type'] ?? ''),
    'service_line1'     => sanitize_text_field($service['line1'] ?? ''),
    'service_line2'     => sanitize_text_field($service['line2'] ?? null),
    'service_line3'     => sanitize_text_field($service['line3'] ?? null),
    'service_town'      => sanitize_text_field($service['town'] ?? ''),
    'service_country'   => sanitize_text_field($service['country'] ?? ''),
    'service_postcode'  => sanitize_text_field($service['postcode'] ?? ''),

    // SHARES
    'share_class'       => sanitize_text_field($o['shares']['class'] ?? null),
    'share_quantity'    => intval($o['shares']['quantity'] ?? 0),
    'share_price'       => isset($o['shares']['price'])
                            ? number_format((float)$o['shares']['price'], 2, '.', '')
                            : null,
    'share_currency'    => sanitize_text_field($o['shares']['currency'] ?? 'GBP'),
    'share_particulars' => sanitize_textarea_field($o['shares']['particulars'] ?? null),

    // PSC — COMPANY
    'psc_company_shares'    => sanitize_text_field($o['noc']['company_shares'] ?? 'na'),
    'psc_company_voting'    => sanitize_text_field($o['noc']['company_voting'] ?? 'na'),
    'psc_company_directors' => intval($o['noc']['company_directors'] ?? 0),
    'psc_company_other'     => intval($o['noc']['company_other'] ?? 0),

    // PSC — FIRM
    'psc_firm_shares'       => sanitize_text_field($o['noc']['firm_shares'] ?? 'na'),
    'psc_firm_voting'       => sanitize_text_field($o['noc']['firm_voting'] ?? 'na'),
    'psc_firm_directors'    => intval($o['noc']['firm_directors'] ?? 0),
    'psc_firm_other'        => intval($o['noc']['firm_other'] ?? 0),

    // PSC — TRUST
    'psc_trust_shares'      => sanitize_text_field($o['noc']['trust_shares'] ?? 'na'),
    'psc_trust_voting'      => sanitize_text_field($o['noc']['trust_voting'] ?? 'na'),
    'psc_trust_directors'   => intval($o['noc']['trust_directors'] ?? 0),
    'psc_trust_other'       => intval($o['noc']['trust_other'] ?? 0),

    'created_at' => current_time('mysql')
];


        if ($wpdb->insert($table, $data) === false) {
            error_log('STEP3 DB INSERT ERROR: ' . $wpdb->last_error);
            wp_send_json_error(['msg' => 'Database insert failed']);
        }
    }

    error_log('STEP3 SUCCESS: officers snapshot saved');

    wp_send_json_success([
        'saved' => true,
        
    ]);
}

