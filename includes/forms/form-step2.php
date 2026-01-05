<?php
if (!defined('ABSPATH')) exit;

/* Load JS */
wp_enqueue_script(
  'form-step2-js',
  NCUK_URL . 'assets/js/form-step2.js',
  ['jquery'],
  filemtime(NCUK_PATH . 'assets/js/form-step2.js'),
  true
);

/* NONCE FOR SECURITY */
wp_localize_script('form-step2-js', 'step2_ajax', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('ncuk_step2_nonce')
]);

/* Fetch addresses saved in admin */
$registered_addresses = get_option('ncuk_registered_addresses', []);
?>

<div class="wrap woocommerce step-form-wrapper step2-wrapper">

    <!-- HEADER -->
    <div class="postbox" style="margin-bottom:30px;">
        <div class="postbox-header">
            <h2 class="hndle">Addresses</h2>
        </div>
        <div class="inside">
            <p>
                <strong>Registered Office</strong><br>
                The official address of an incorporated company.
            </p>
            <p>
                <strong>Registered Email</strong><br>
                Used for official company correspondence.
            </p>
        </div>
    </div>

    <!-- FORM -->
    <form id="step2form">

        <!-- REGISTERED EMAIL -->
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle">Registered Email</h2>
            </div>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="registered_email">Registered Email</label>
                        </th>
                        <td>
                            <input type="email"
                                   id="registered_email"
                                   name="registered_email"
                                   class="regular-text"
                                   placeholder="Enter registered email">
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- REGISTERED OFFICES (MOSTLY SAME STYLE) -->
        <h2 style="margin:30px 0 15px;">Registered Office</h2>

        <?php if (!empty($registered_addresses)) : ?>
            <?php foreach ($registered_addresses as $index => $addr) : ?>

                <div class="office-box"
                     style="border:1px solid #ddd;border-radius:6px;padding:20px;margin-bottom:20px;
                            display:flex;gap:20px;background:#fff;">

                    <div>
                        <img src="<?php echo NCUK_URL . 'assets/images/regoffice@High.png'; ?>"
                             style="width:60px;">
                    </div>

                    <div style="flex:1;">
                        <h4 style="margin:0 0 8px;">
                            <?php echo esc_html($addr['name']); ?>
                        </h4>

                        <p style="margin:0 0 12px;color:#555;">
                            <?php echo esc_html($addr['desc']); ?>
                        </p>

                        <button type="button"
                                class="button button-primary office-buy-btn"
                                data-id="<?php echo $index; ?>"
                                data-name="<?php echo esc_attr($addr['name']); ?>">
                            Buy Now
                        </button>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>

        <!-- USE OWN ADDRESS -->
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle">Use Own Address</h2>
            </div>
            <div class="inside">
                <button type="button"
                        id="choose-address"
                        class="button button-secondary">
                    Choose Address
                </button>
            </div>
        </div>

        <!-- OWN ADDRESS FORM -->
        <div id="own-address-form" class="postbox" style="display:none;">
            <div class="postbox-header">
                <h2 class="hndle" id="address-form-title">Enter Address</h2>
            </div>
            <div class="inside">

                <table class="form-table">
                    <tr>
                        <th><label>Name / Number *</label></th>
                        <td><input type="text" id="addr_line1" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label>Street *</label></th>
                        <td><input type="text" id="addr_line2" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label>Locality</label></th>
                        <td><input type="text" id="addr_line3" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label>Town *</label></th>
                        <td><input type="text" id="addr_line4" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label>Country *</label></th>
                        <td>
                            <select id="addr_country">
                                <option value="UNITED KINGDOM">UNITED KINGDOM</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Postal Code *</label></th>
                        <td><input type="text" id="addr_postcode" class="regular-text"></td>
                    </tr>
                </table>

                <p style="text-align:right;">
                    <button type="button"
                            id="cancel-address"
                            class="button">
                        Close
                    </button>
                </p>

            </div>
        </div>

        <!-- SAVE BUTTON -->
        <p class="submit" style="text-align:right;">
            <button type="button"
                    id="step2-save"
                    class="button button-primary button-large">
                Save & Continue
            </button>
        </p>

    </form>
</div>



<!-- KEEP JS -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const ownBox = document.getElementById("own-address-form");
    const chooseBtn = document.getElementById("choose-address");
    const cancelBtn = document.getElementById("cancel-address");

    chooseBtn?.addEventListener("click", () => {
        document.getElementById("address-form-title").innerText = "Enter Own Address";
        ownBox.style.display = "block";
    });

    cancelBtn?.addEventListener("click", () => {
        ownBox.style.display = "none";
    });

    document.querySelectorAll(".office-buy-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const name = this.dataset.name;
            document.getElementById("address-form-title").innerText =
                "Forwarding Address for: " + name;

            ownBox.style.display = "block";
        });
    });

});
</script>
