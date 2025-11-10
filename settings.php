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

    echo '<div class="updated"><p>Settings saved successfully.</p></div>';
}

// Get the current value
$uk_api_key = get_option('namecheck_uk_api_key', '');
?>

<div class="wrap">
    <h1>NameCheck UK Settings</h1>
    <form method="POST" action="">
        <?php wp_nonce_field('namecheck_settings', 'namecheck_settings_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="uk_api_key">Companies House API Key</label>
                </th>
                <td>
                    <input type="text" name="uk_api_key" id="uk_api_key" 
                           value="<?php echo esc_attr($uk_api_key); ?>" 
                           class="regular-text" style="width:400px;">
                    <p class="description">Enter your Companies House API key for UK name checking.</p>
                </td>
            </tr>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>
