<?php
if (!defined('ABSPATH')) exit;

/*--------------------------------------------------------------
# CREATE TABLE ONLY IF MISSING
--------------------------------------------------------------*/
function ncuk_maybe_create_companyformation_table() {
    global $wpdb;

    $table = $wpdb->prefix . "companyformation";

    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") === $table) {
        return; // Already created
    }

    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        token VARCHAR(80) NOT NULL,
        data LONGTEXT NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        INDEX (token)
    ) $charset;";

    require_once ABSPATH . "wp-admin/includes/upgrade.php";
    dbDelta($sql);
}

/*--------------------------------------------------------------
# GET OR CREATE TOKEN
--------------------------------------------------------------*/
function ncuk_get_companyformation_token() {

    if (!empty($_COOKIE['companyformation_token'])) {
        return sanitize_text_field($_COOKIE['companyformation_token']);
    }

    $token = wp_generate_uuid4();

    // save 1 year
    setcookie("companyformation_token", $token, time() + (365 * 86400), "/");

    return $token;
}

/*--------------------------------------------------------------
# SAVE STEP 1 DATA
--------------------------------------------------------------*/
add_action('wp_ajax_ncuk_save_step1', 'ncuk_save_step1');
add_action('wp_ajax_nopriv_ncuk_save_step1', 'ncuk_save_step1');

function ncuk_save_step1() {
    global $wpdb;

    ncuk_maybe_create_companyformation_table();
    $table = $wpdb->prefix . "companyformation";

    $token = ncuk_get_companyformation_token();

    // Only saving minimal fields (no HTML)
    $data = [
        'company_name'      => sanitize_text_field($_POST['company_name']),
        'company_type'      => sanitize_text_field($_POST['company_type']),
        'jurisdiction'      => sanitize_text_field($_POST['jurisdiction']),
        'business_activity' => sanitize_text_field($_POST['business_activity']),
        'sic_codes'         => isset($_POST['sic_codes']) ? $_POST['sic_codes'] : []
    ];

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

/*--------------------------------------------------------------
# LOAD STEP 1 DATA
--------------------------------------------------------------*/
add_action('wp_ajax_ncuk_load_step1', 'ncuk_load_step1');
add_action('wp_ajax_nopriv_ncuk_load_step1', 'ncuk_load_step1');

function ncuk_load_step1() {
    global $wpdb;

    ncuk_maybe_create_companyformation_table();
    $table = $wpdb->prefix . "companyformation";

    if (empty($_COOKIE['companyformation_token'])) {
        wp_send_json_success(['data' => []]);
    }

    $token = sanitize_text_field($_COOKIE['companyformation_token']);

    $row = $wpdb->get_row(
        $wpdb->prepare("SELECT data FROM $table WHERE token = %s", $token),
        ARRAY_A
    );

    if (!$row) {
        wp_send_json_success(['data' => []]);
    }

    wp_send_json_success([
        'data' => maybe_unserialize($row['data'])
    ]);
}
