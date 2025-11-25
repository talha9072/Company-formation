<?php
if (!defined('ABSPATH')) exit;

/* Load Step-3 JS */
wp_enqueue_script(
  'form-step3-js',
  NCUK_URL . 'assets/js/form-step3.js',
  ['jquery'],
  filemtime(NCUK_PATH . 'assets/js/form-step3.js'),
  true
);
?>

<div class="step-form-wrapper step3-wrapper">

    <h2 class="step-title">Appointments</h2>

    <div class="step-description" style="background:#f5f7fb;border-radius:8px;padding:25px;margin-bottom:35px;">
        <h3 style="font-size:28px;color:#4a3b8f;margin-bottom:10px;">Directors, Shareholders & Secretaries</h3>
        <p style="color:#555;">A company must have at least one person director and one shareholder.</p>
    </div>

    <h3 style="font-size:24px;color:#4a3b8f;margin-bottom:15px;">Add Another Officer</h3>

    <div class="officer-btn-group" style="display:flex;gap:15px;margin-bottom:35px;">
        <button class="officer-add-btn" data-target="#officer-person-form"
            style="background:#ff8a26;color:white;padding:12px 25px;border:none;border-radius:6px;">👤 Add Person</button>

        <button class="officer-add-btn" data-target="#officer-corporate-form"
            style="background:#ff8a26;color:white;padding:12px 25px;border:none;border-radius:6px;">🏢 Corporate</button>

        <button class="officer-add-btn" data-target="#officer-entity-form"
            style="background:#fff;color:#ff8a26;padding:12px 25px;border:2px solid #ff8a26;border-radius:6px;">⚖️ Other Legal Entity</button>
    </div>

    <!-- ALL FORMS LOADED HERE BUT HIDDEN -->
    <?php include NCUK_PATH . "includes/forms/step3/officer-person.php"; ?>
    <?php include NCUK_PATH . "includes/forms/step3/officer-corporate.php"; ?>
    <?php include NCUK_PATH . "includes/forms/step3/officer-entity.php"; ?>

    <h3 style="font-size:24px;color:#4a3b8f;margin-top:40px;">Current Appointments</h3>
    <?php include NCUK_PATH . "includes/forms/step3/officer-table.php"; ?>

    <div style="margin-top:40px;text-align:right;">
        <button id="step3-save" style="background:#1a8f4a;color:white;padding:12px 35px;border:none;border-radius:6px;">
            Save & Continue →
        </button>
    </div>

</div>
