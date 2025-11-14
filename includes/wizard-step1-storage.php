<?php
if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------------
   1. CREATE TABLE (runs automatically if missing)
------------------------------------------------------------- */
function ncuk_maybe_create_companyformation_table() {
    global $wpdb;

    $table = $wpdb->prefix . "companyformation";

    // Already exists?
    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {
        return;
    }

    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        token VARCHAR(80) NOT NULL,
        data LONGTEXT NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (token)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

/* -------------------------------------------------------------
   2. SESSION TOKEN (one per visitor)
------------------------------------------------------------- */
function ncuk_get_companyformation_token() {

    if (!session_id()) {
        session_start();
    }

    // Already have token? use same
    if (!empty($_SESSION['companyformation_token'])) {
        return sanitize_text_field($_SESSION['companyformation_token']);
    }

    // Create new token only once
    $token = wp_generate_uuid4();
    $_SESSION['companyformation_token'] = $token;

    return $token;
}

/* -------------------------------------------------------------
   3. SAVE STEP-1 DATA — DB ONLY
------------------------------------------------------------- */
add_action('wp_ajax_ncuk_save_step1', 'ncuk_save_step1');
add_action('wp_ajax_nopriv_ncuk_save_step1', 'ncuk_save_step1');

function ncuk_save_step1() {
    global $wpdb;

    ncuk_maybe_create_companyformation_table();
    $table = $wpdb->prefix . "companyformation";

    $token = ncuk_get_companyformation_token();

    // Clean POST safely
    $data = [
        'company_name'      => sanitize_text_field($_POST['company_name'] ?? ''),
        'company_type'      => sanitize_text_field($_POST['company_type'] ?? ''),
        'jurisdiction'      => sanitize_text_field($_POST['jurisdiction'] ?? ''),
        'business_activity' => sanitize_text_field($_POST['business_activity'] ?? ''),
        'sic_codes'         => (isset($_POST['sic_codes']) && is_array($_POST['sic_codes'])) ? array_map('sanitize_text_field', $_POST['sic_codes']) : []
    ];

    // IMPORTANT — ALWAYS 1 ROW PER TOKEN
    $wpdb->replace(
        $table,
        [
            'token'      => $token,
            'data'       => maybe_serialize($data),
            'updated_at' => current_time('mysql')
        ],
        ['%s', '%s', '%s']
    );

    wp_send_json_success([
        'saved' => true,
        'token' => $token
    ]);
}

/* -------------------------------------------------------------
   4. LOAD STEP-1 DATA
------------------------------------------------------------- */
add_action('wp_ajax_ncuk_load_step1', 'ncuk_load_step1');
add_action('wp_ajax_nopriv_ncuk_load_step1', 'ncuk_load_step1');

function ncuk_load_step1() {
    global $wpdb;

    ncuk_maybe_create_companyformation_table();
    $table = $wpdb->prefix . "companyformation";

    if (!session_id()) {
        session_start();
    }

    // No session token yet → return empty
    if (empty($_SESSION['companyformation_token'])) {
        wp_send_json_success(['data' => []]);
        return;
    }

    $token = sanitize_text_field($_SESSION['companyformation_token']);

    // Fetch one single row
    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT data FROM $table WHERE token = %s LIMIT 1", $token),
        ARRAY_A
    );

    // Deserialize stored data
    $result = $row ? maybe_unserialize($row['data']) : [];

    wp_send_json_success([
        'data' => $result
    ]);
}
