<?php
if (!defined('ABSPATH')) exit;

add_action("wp_ajax_ncuk_save_step3_officer", "ncuk_save_step3_officer");
add_action("wp_ajax_nopriv_ncuk_save_step3_officer", "ncuk_save_step3_officer");

function ncuk_save_step3_officer() {
    global $wpdb;

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ncuk_step3_nonce')) {
        wp_send_json_error(["msg" => "Invalid nonce"]);
    }

    if (!session_id()) session_start();
    $token = $_SESSION['companyformation_token'] ?? '';
    if (!$token) {
        wp_send_json_error(["msg" => "Token missing"]);
    }

    $table = $wpdb->prefix . "companyformation_officers";

    $data = [
        "token" => $token,
        "officer_type" => sanitize_text_field($_POST['officer_type']),

        "role_director" => intval($_POST['role_director']),
        "role_shareholder" => intval($_POST['role_shareholder']),
        "role_secretary" => intval($_POST['role_secretary']),
        "role_psc" => intval($_POST['role_psc']),

        "title" => sanitize_text_field($_POST['title']),
        "first_name" => sanitize_text_field($_POST['first']),
        "last_name" => sanitize_text_field($_POST['last']),
        "email" => sanitize_email($_POST['email']),
        "dob" => sanitize_text_field($_POST['dob']),
        "nationality" => sanitize_text_field($_POST['nationality']),
        "verification_code" => sanitize_text_field($_POST['verification_code']),
        "consent_auth" => intval($_POST['consent_auth']),

        "res_line1" => sanitize_text_field($_POST['res_line1']),
        "res_town" => sanitize_text_field($_POST['res_town']),
        "res_postcode" => sanitize_text_field($_POST['res_postcode']),
        "res_country" => sanitize_text_field($_POST['res_country']),

        "service_addr_type" => sanitize_text_field($_POST['service_addr_type']),
        "service_line1" => sanitize_text_field($_POST['service_line1']),
        "service_town" => sanitize_text_field($_POST['service_town']),
        "service_postcode" => sanitize_text_field($_POST['service_postcode']),

        "share_quantity" => intval($_POST['share_quantity']),
        "psc_company_shares" => sanitize_text_field($_POST['psc_company_shares']),
        "psc_company_voting" => sanitize_text_field($_POST['psc_company_voting']),
        "psc_company_directors" => intval($_POST['psc_company_directors']),
        "psc_company_other" => intval($_POST['psc_company_other']),
    ];

    $wpdb->insert($table, $data);

    if ($wpdb->last_error) {
        wp_send_json_error(["msg" => "DB insert failed"]);
    }

    wp_send_json_success(["saved" => true]);
}
