<?php if (!defined('ABSPATH')) exit; ?>

<div id="tab-det" class="step3-tab-content" style="display:none;">

    <h2 style="margin-bottom:25px;">Officer Details</h2>

    <!-- ===================== -->
    <!-- PERSONAL DETAILS -->
    <!-- ===================== -->
    <h3 style="color:#4a3b8f;margin-bottom:10px;">Personal Details</h3>

    <div class="step3-grid">

        <div class="grid-item">
            <label>Title</label>
            <input type="text" id="det_title" placeholder="Title" class="widefat">
        </div>

        <div class="grid-item">
            <label>Date of Birth</label>
            <div class="dob-flex">
                <select id="dob_day" class="dob-input">
                    <option value="">dd</option>
                    <?php for ($i = 1; $i <= 31; $i++) echo "<option>$i</option>"; ?>
                </select>

                <select id="dob_month" class="dob-input">
                    <option value="">mm</option>
                    <?php for ($i = 1; $i <= 12; $i++) echo "<option>$i</option>"; ?>
                </select>

                <select id="dob_year" class="dob-input">
                    <option value="">yyyy</option>
                    <?php for ($y = date('Y') - 16; $y >= 1900; $y--) echo "<option>$y</option>"; ?>
                </select>
            </div>
        </div>

        <div class="grid-item">
            <label>First name</label>
            <input type="text" id="det_first" placeholder="First name" class="widefat">
        </div>

        <div class="grid-item">
            <label>Email</label>
            <input type="email" id="det_email" placeholder="Email" class="widefat">
        </div>

        <div class="grid-item">
            <label>Last name</label>
            <input type="text" id="det_last" placeholder="Last name" class="widefat">
        </div>

        <div class="grid-item">
            <label>Nationality</label>
            <input type="text" id="det_nationality" placeholder="Nationality" class="widefat">
        </div>

        <div class="grid-item-full">
            <label>Personal Verification Code (optional)</label>
            <input type="text" id="det_verification" placeholder="Personal Verification Code" class="widefat">
        </div>

    </div>

    <!-- ===================== -->
    <!-- RESIDENTIAL ADDRESS -->
    <!-- ===================== -->
    <h3 style="color:#4a3b8f;margin:25px 0 10px;">Residential Address</h3>

    <div class="step3-grid">

        <div class="grid-item">
            <label>Name/Number *</label>
            <input type="text" id="addr_line1" placeholder="Name/Number" class="widefat">
        </div>

        <div class="grid-item">
            <label>Street *</label>
            <input type="text" id="addr_line2" placeholder="Street" class="widefat">
        </div>

        <div class="grid-item">
            <label>Locality</label>
            <input type="text" id="addr_line3" placeholder="Locality" class="widefat">
        </div>

        <div class="grid-item">
            <label>Town *</label>
            <input type="text" id="addr_town" placeholder="Town" class="widefat">
        </div>

        <div class="grid-item">
            <label>Country *</label>
            <select id="addr_country" class="widefat">
                <option value="UNITED KINGDOM">UNITED KINGDOM</option>
            </select>
        </div>

        <div class="grid-item">
            <label>Postal Code *</label>
            <input type="text" id="addr_postcode" placeholder="Postcode" class="widefat">
        </div>

    </div>

    <!-- ===================== -->
    <!-- AUTHENTICATION CONSENT -->
    <!-- ===================== -->
    <h3 style="color:#4a3b8f;margin:25px 0 10px;">Authentication Consent</h3>

    <label class="step3-check">
        <input type="checkbox" id="consent_auth">
        The subscriber agrees their name is used to electronically authenticate the memorandum of association.
    </label>

    <!-- CONTINUE -->
    <button id="details-next-btn" class="step3-save-tab" style="margin-top:25px;">
        Continue →
    </button>

</div>
