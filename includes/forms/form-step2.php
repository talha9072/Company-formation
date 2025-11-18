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

<div class="step-form-wrapper step2-wrapper">

    <!-- HEADER -->
    <div style="background:#f5f7fb;border-radius:8px;padding:25px;margin-bottom:35px;">
        <h2 style="font-size:28px;color:#4a3b8f;margin-bottom:10px;">Addresses</h2>

        <p style="color:#555;line-height:1.6;margin:0 0 12px;">
            <strong>Registered Office</strong><br>
            The official address of an incorporated company.
        </p>

        <p style="color:#555;line-height:1.6;margin:0;">
            <strong>Registered Email</strong><br>
            Used for official company correspondence.
        </p>
    </div>


    <!-- AJAX FORM -->
    <form id="step2form">

        <!-- REGISTERED EMAIL -->
        <h3 style="font-size:24px;color:#4a3b8f;margin-bottom:10px;">Registered Email</h3>

        <div class="form-group" style="margin-bottom:25px;">
            <label for="registered_email" style="font-weight:600;">Registered Email</label>
            <input type="email" id="registered_email" name="registered_email"
                   placeholder="Enter registered email"
                   style="width:100%;padding:12px;border:1px solid #ccc;border-radius:6px;font-size:15px;">
        </div>


        <!-- REGISTERED OFFICE OPTIONS -->
        <h3 style="font-size:24px;color:#4a3b8f;margin-bottom:10px;">Registered Office</h3>

        <?php if (!empty($registered_addresses)) : ?>
            <?php foreach ($registered_addresses as $index => $addr) : ?>

                <div class="office-box" style="
                    border:1px solid #ddd;border-radius:8px;padding:20px;
                    margin-bottom:25px;display:flex;align-items:flex-start;gap:20px;">
                    
                    <div>
                        <img src="<?php echo NCUK_URL . 'assets/images/regoffice@High.png'; ?>"
                             style="width:60px;">
                    </div>

                    <div style="flex:1;">
                        <h4 style="font-size:22px;margin:0;"><?php echo esc_html($addr['name']); ?></h4>

                        <p style="margin:10px 0;color:#555;"><?php echo esc_html($addr['desc']); ?></p>

                        <div style="margin-top:15px;">
                            <button type="button"
                                    class="office-buy-btn"
                                    data-id="<?php echo $index; ?>"
                                    data-name="<?php echo esc_attr($addr['name']); ?>"
                                    style="
                                        padding:8px 18px;background:#1a8f4a;
                                        color:white;border:none;border-radius:6px;cursor:pointer;">
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>


        <!-- USE OWN ADDRESS -->
        <div class="address-box"
             style="border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:25px;">
            <p style="margin-bottom:15px;font-weight:600;color:#333;">Use Own Address</p>
            <button type="button" id="choose-address"
                style="background:#ff8a26;color:#fff;border:none;padding:10px 22px;
                       border-radius:6px;cursor:pointer;font-weight:600;">
                Choose Address
            </button>
        </div>


        <!-- ADDRESS ENTRY BOX -->
        <div id="own-address-form"
             style="display:none;border:1px solid #ddd;border-radius:8px;padding:20px;margin-bottom:35px;">

            <h3 style="margin-bottom:20px;color:#333;font-size:22px;" id="address-form-title">
                Enter Address
            </h3>

            <label>Name/Number *</label>
            <input type="text" id="addr_line1"
                   placeholder="Address Line 1"
                   style="width:100%;padding:12px;border:1px solid #ccc;border-radius:6px;margin-bottom:15px;">

            <label>Street *</label>
            <input type="text" id="addr_line2"
                   placeholder="Address Line 2"
                   style="width:100%;padding:12px;border:1px solid #ccc;border-radius:6px;margin-bottom:15px;">

            <label>Locality</label>
            <input type="text" id="addr_line3"
                   placeholder="Address Line 3"
                   style="width:100%;padding:12px;border:1px solid #ccc;border-radius:6px;margin-bottom:15px;">

            <label>Town *</label>
            <input type="text" id="addr_line4"
                   placeholder="Town"
                   style="width:100%;padding:12px;border:1px solid #ccc;border-radius:6px;margin-bottom:15px;">

            <div style="display:flex;gap:15px;">
                <div style="flex:1;">
                    <label>Country *</label>
                    <select id="addr_country"
                            style="width:100%;padding:12px;border-radius:6px;border:1px solid #ccc;">
                        <option value="UNITED KINGDOM">UNITED KINGDOM</option>
                    </select>
                </div>

                <div style="flex:1;">
                    <label>Postal Code *</label>
                    <input type="text" id="addr_postcode"
                           placeholder="Postcode"
                           style="width:100%;padding:12px;border-radius:6px;border:1px solid #ccc;">
                </div>
            </div>

            <div style="text-align:right;margin-top:20px;">
                <button type="button" id="cancel-address"
                    style="background:#ddd;border:none;padding:10px 20px;border-radius:6px;cursor:pointer;">
                    Close
                </button>
            </div>
        </div>


        <!-- SAVE BUTTON -->
        <div style="text-align:right;margin-top:35px;">
            <button type="button" id="step2-save"
                style="background:#ff8a26;color:white;border:none;
                       padding:12px 35px;border-radius:6px;font-size:16px;cursor:pointer;">
                Save & Continue
            </button>
        </div>

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
