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

<div class="step-form-wrapper step3-wrapper woocommerce">

    <!-- ===================== -->
    <!-- HEADER -->
    <!-- ===================== -->
    <div class="postbox">

        <div class="postbox-header">
            <h2 class="hndle">Appointments</h2>
        </div>

        <div class="inside">
            <p class="description">
                A company must have at least one <strong>person director</strong> and one
                <strong>shareholder</strong>.
            </p>
        </div>

    </div>

    <!-- ===================== -->
    <!-- ADD OFFICERS -->
    <!-- ===================== -->
    <div class="postbox">

        <div class="postbox-header">
            <h2 class="hndle">Add Another Officer</h2>
        </div>

        <div class="inside">

            <p class="description" style="margin-bottom:15px;">
                Choose the type of officer you want to appoint.
            </p>

            <div class="officer-btn-group" style="display:flex;gap:12px;flex-wrap:wrap;">

                <button type="button"
                        class="button button-primary officer-add-btn"
                        data-target="#officer-person-form">
                    👤 Add Person
                </button>

                <button type="button"
                        class="button button-primary officer-add-btn"
                        data-target="#officer-corporate-form">
                    🏢 Corporate
                </button>

                <button type="button"
                        class="button officer-add-btn"
                        data-target="#officer-entity-form">
                    ⚖️ Other Legal Entity
                </button>

            </div>

        </div>
    </div>

    <!-- ===================== -->
    <!-- OFFICER FORMS (HIDDEN / JS CONTROLLED) -->
    <!-- ===================== -->
    <?php include NCUK_PATH . "includes/forms/step3/officer-person.php"; ?>
    <?php include NCUK_PATH . "includes/forms/step3/officer-corporate.php"; ?>
    <?php include NCUK_PATH . "includes/forms/step3/officer-entity.php"; ?>

    <!-- ===================== -->
    <!-- CURRENT APPOINTMENTS -->
    <!-- ===================== -->
    <div class="postbox">

        <div class="postbox-header">
            <h2 class="hndle">Current Appointments</h2>
        </div>

        <div class="inside">

            <p class="description">
                Below is a list of officers currently assigned to your company.
            </p>

            <div id="officer-list"
                 style="
                    margin-top:15px;
                    background:#f6f7f7;
                    border:1px solid #ccd0d4;
                    padding:12px;
                    border-radius:4px;
                 ">
            </div>

        </div>
    </div>

    <!-- ===================== -->
    <!-- FOOTER ACTION -->
    <!-- ===================== -->
    <p class="submit" style="text-align:right;">
        <button type="button"
                id="step3-save"
                class="button button-primary button-large">
            Save & Continue →
        </button>
    </p>

</div>
