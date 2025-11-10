<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['namecheck_settings_nonce']) && wp_verify_nonce($_POST['namecheck_settings_nonce'], 'namecheck_settings')) {
    // Save the UK API Key
    if (isset($_POST['uk_api_key'])) {
        update_option('namecheck_uk_api_key', sanitize_text_field($_POST['uk_api_key']));
    }

    // Save the Ireland API Key
    if (isset($_POST['ireland_api_key'])) {
        update_option('namecheck_ireland_api_key', sanitize_text_field($_POST['ireland_api_key']));
    }

    echo '<div class="updated"><p>Settings saved successfully.</p></div>';
}

// Get the current values
$uk_api_key = get_option('namecheck_uk_api_key', '');
$ireland_api_key = get_option('namecheck_ireland_api_key', '');
?>

<div class="wrap">
    <h1>NameCheck Settings</h1>
    <form method="POST" action="">
        <?php wp_nonce_field('namecheck_settings', 'namecheck_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="uk_api_key">UK API Key</label>
                </th>
                <td>
                    <input type="text" name="uk_api_key" id="uk_api_key" value="<?php echo esc_attr($uk_api_key); ?>" class="regular-text">
                    <p class="description">Enter your UK API key for NameCheck integration.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="ireland_api_key">Ireland API Key</label>
                </th>
                <td>
                    <input type="text" name="ireland_api_key" id="ireland_api_key" value="<?php echo esc_attr($ireland_api_key); ?>" class="regular-text">
                    <p class="description">Enter your Ireland API key for NameCheck integration.</p>
                </td>
            </tr>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>
