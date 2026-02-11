<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/* ===== HANDLE SETTINGS SAVE ===== */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['namecheck_settings_nonce']) &&
    wp_verify_nonce($_POST['namecheck_settings_nonce'], 'namecheck_settings')
) {

    // Existing API Key (KEEP SAME OPTION NAME)
    if (isset($_POST['uk_api_key'])) {
        update_option('namecheck_uk_api_key', sanitize_text_field($_POST['uk_api_key']));
    }

    // New XML Gateway Credentials
    update_option('ch_presenter_id_test', sanitize_text_field($_POST['ch_presenter_id_test'] ?? ''));
    update_option('ch_auth_code_test', sanitize_text_field($_POST['ch_auth_code_test'] ?? ''));
    update_option('ch_presenter_id_live', sanitize_text_field($_POST['ch_presenter_id_live'] ?? ''));
    update_option('ch_auth_code_live', sanitize_text_field($_POST['ch_auth_code_live'] ?? ''));
    update_option('ch_environment', sanitize_text_field($_POST['ch_environment'] ?? 'test'));

    echo '<div class="updated"><p>Settings saved successfully.</p></div>';
}

/* ===== LOAD SAVED VALUES ===== */
$uk_api_key           = get_option('namecheck_uk_api_key', '');
$ch_presenter_id_test = get_option('ch_presenter_id_test', '');
$ch_auth_code_test    = get_option('ch_auth_code_test', '');
$ch_presenter_id_live = get_option('ch_presenter_id_live', '');
$ch_auth_code_live    = get_option('ch_auth_code_live', '');
$ch_environment       = get_option('ch_environment', 'test');
?>

<div class="wrap">
    <h1>NameCheck UK Settings</h1>

    <form method="POST" action="">
        <?php wp_nonce_field('namecheck_settings', 'namecheck_settings_nonce'); ?>

        <table class="form-table">

            <!-- ===== EXISTING API KEY (UNCHANGED) ===== -->
            <tr>
                <th scope="row">
                    <label for="uk_api_key">Companies House API Key</label>
                </th>
                <td>
                    <input type="text"
                           name="uk_api_key"
                           id="uk_api_key"
                           value="<?php echo esc_attr($uk_api_key); ?>"
                           class="regular-text"
                           style="width:400px;">
                    <p class="description">
                        Enter your Companies House API key for UK name checking.
                    </p>
                </td>
            </tr>

            <!-- ===== TEST CREDENTIALS ===== -->
            <tr>
                <th colspan="2"><h2>XML Gateway – Test</h2></th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="ch_presenter_id_test">Test Presenter ID</label>
                </th>
                <td>
                    <input type="text"
                           name="ch_presenter_id_test"
                           id="ch_presenter_id_test"
                           value="<?php echo esc_attr($ch_presenter_id_test); ?>"
                           class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="ch_auth_code_test">Test Authentication Code</label>
                </th>
                <td>
                    <input type="text"
                           name="ch_auth_code_test"
                           id="ch_auth_code_test"
                           value="<?php echo esc_attr($ch_auth_code_test); ?>"
                           class="regular-text">
                </td>
            </tr>

            <!-- ===== LIVE CREDENTIALS ===== -->
            <tr>
                <th colspan="2"><h2>XML Gateway – Live</h2></th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="ch_presenter_id_live">Live Presenter ID</label>
                </th>
                <td>
                    <input type="text"
                           name="ch_presenter_id_live"
                           id="ch_presenter_id_live"
                           value="<?php echo esc_attr($ch_presenter_id_live); ?>"
                           class="regular-text">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="ch_auth_code_live">Live Authentication Code</label>
                </th>
                <td>
                    <input type="text"
                           name="ch_auth_code_live"
                           id="ch_auth_code_live"
                           value="<?php echo esc_attr($ch_auth_code_live); ?>"
                           class="regular-text">
                </td>
            </tr>

            <!-- ===== ENVIRONMENT SWITCH ===== -->
            <tr>
                <th scope="row">
                    <label for="ch_environment">Environment Mode</label>
                </th>
                <td>
                    <select name="ch_environment" id="ch_environment">
                        <option value="test" <?php selected($ch_environment, 'test'); ?>>Test</option>
                        <option value="live" <?php selected($ch_environment, 'live'); ?>>Live</option>
                    </select>
                    <p class="description">
                        Select whether filings are sent to Test or Live environment.
                    </p>
                </td>
            </tr>

        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>
