<?php
if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------------
   AJAX HANDLER — SAVE STEP 2
------------------------------------------------------------- */
add_action("wp_ajax_ncuk_save_step2", "ncuk_save_step2");
add_action("wp_ajax_nopriv_ncuk_save_step2", "ncuk_save_step2");

function ncuk_save_step2() {
    global $wpdb;

    error_log("STEP2 DEBUG: Handler started.");

    /* Security */
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ncuk_step2_nonce')) {
        error_log("STEP2 DEBUG: Nonce failed.");
        wp_send_json_error(["msg" => "Invalid nonce"]);
    }

    /* Start session */
    if (!session_id()) session_start();

    /* Token check */
    $token = $_SESSION['companyformation_token'] ?? '';
    if (empty($token)) {
        error_log("STEP2 DEBUG: No token found in session.");
        wp_send_json_error(["msg" => "Token missing"]);
    }

    $table = $wpdb->prefix . "companyformation";

    /* Collect POST data */
    $email          = sanitize_email($_POST['registered_email'] ?? '');

    $office_id      = sanitize_text_field($_POST['step2_office_id'] ?? '');
    $office_name    = sanitize_text_field($_POST['step2_office_name'] ?? '');

    $addr1          = sanitize_text_field($_POST['step2_addr_line1'] ?? '');
    $addr2          = sanitize_text_field($_POST['step2_addr_line2'] ?? '');
    $addr3          = sanitize_text_field($_POST['step2_addr_line3'] ?? '');
    $addr4          = sanitize_text_field($_POST['step2_addr_line4'] ?? '');
    $country        = sanitize_text_field($_POST['step2_addr_country'] ?? '');
    $postcode       = sanitize_text_field($_POST['step2_addr_postcode'] ?? '');

    error_log("STEP2 DEBUG: POST data received:");
    error_log(print_r($_POST, true));

    /* Build update array */
    $update_data = [
        "step2_email"          => $email,
        "step2_office_id"      => $office_id ?: null,
        "step2_office_name"    => $office_name ?: null,
        "step2_addr_line1"     => $addr1,
        "step2_addr_line2"     => $addr2,
        "step2_addr_line3"     => $addr3,
        "step2_addr_line4"     => $addr4,
        "step2_addr_country"   => $country,
        "step2_addr_postcode"  => $postcode,
        "updated_at"           => current_time('mysql')
    ];

    error_log("STEP2 DEBUG: Update array:");
    error_log(print_r($update_data, true));

    /* Update database row */
    $result = $wpdb->update(
        $table,
        $update_data,
        ["token" => $token],
        ['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'],  // formats
        ['%s']
    );

    error_log("STEP2 DEBUG: wpdb->update result = " . print_r($result, true));
    error_log("STEP2 DEBUG: Last SQL error: " . $wpdb->last_error);

    if ($result === false) {
        wp_send_json_error([
            "msg" => "DB update failed",
            "sql_error" => $wpdb->last_error
        ]);
    }

    /* SUCCESS */
    wp_send_json_success([
        "saved" => true,
        "msg"   => "Step 2 data saved successfully.",
        "token" => $token
    ]);
}
