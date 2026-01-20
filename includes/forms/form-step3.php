<?php
if (!defined('ABSPATH')) exit;


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
    <!-- OFFICER TYPE (RADIO) -->
    <!-- ===================== -->
    <div class="postbox">

        <div class="postbox-header">
            <h2 class="hndle">Add Officer</h2>
        </div>

        <div class="inside">

            <p class="description" style="margin-bottom:12px;">
                Select the type of officer you want to appoint.
            </p>

            <div class="officer-type-group" style="display:flex;gap:20px;flex-wrap:wrap;">

                <label style="display:flex;align-items:center;gap:6px;">
                    <input type="radio"
                           name="officer_type"
                           value="person"
                           checked>
                    👤 Person
                </label>

                <label style="display:flex;align-items:center;gap:6px;">
                    <input type="radio"
                           name="officer_type"
                           value="corporate">
                    🏢 Corporate
                </label>

                <label style="display:flex;align-items:center;gap:6px;">
                    <input type="radio"
                           name="officer_type"
                           value="legalentity">
                    ⚖️ Other Legal Entity
                </label>

            </div>

        </div>
    </div>

    <!-- ===================== -->
    <!-- SINGLE OFFICER FORM -->
    <!-- ===================== -->
    <?php
    // SAME FORM for all officer types
    include NCUK_PATH . "includes/forms/step3/officer-person.php";
    ?>

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
