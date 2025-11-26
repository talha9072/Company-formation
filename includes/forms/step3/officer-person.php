<div id="officer-person-form" class="step3-form-box" style="display:none;">

    <!-- FIXED MAIN TABS -->
    <div class="step3-tabs">
        <button class="step3-tab active" data-target="#tab-pos">Position</button>
        <button class="step3-tab" data-target="#tab-det">Details</button>
        <button class="step3-tab" data-target="#tab-addr">Addressing</button>

        <!-- Dynamic extra tabs (hidden initially) -->
        <button id="tab-noc-btn" class="step3-tab dynamic-tab" data-target="#tab-noc" style="display:none;">
            Nature of Control
        </button>

        <button id="tab-share-btn" class="step3-tab dynamic-tab" data-target="#tab-share" style="display:none;">
            Share Holdings
        </button>
    </div>


    <!-- =======================
         TAB: POSITION
    ======================= -->
    <div id="tab-pos" class="step3-tab-content" style="display:block;">

        <h3>Choose Position</h3>

        <div class="step3-checkbox-list">

            <label class="step3-check">
                <input type="checkbox" id="role_director"> Director
            </label>

            <label class="step3-check">
                <input type="checkbox" id="role_shareholder"> Shareholder
            </label>

            <label class="step3-check">
                <input type="checkbox" id="role_secretary"> Secretary
            </label>

            <label class="step3-check">
                <input type="checkbox" id="role_psc"> Person of Significant Control
            </label>

        </div>

        <!-- CONSENT BOX -->
        <div id="consent-box" class="step3-submenu-panel" style="display:none;">
            <label class="step3-check">
                <input type="checkbox"> The shareholders confirm that the named officer has consented to act as a Director or Secretary
            </label>
        </div>

    </div>



    <!-- =========================
         TAB: NATURE OF CONTROL
    ========================= -->
    <div id="tab-noc" class="step3-tab-content" style="display:none;">

        <h3>Nature of Control</h3>

        <label class="step3-check"><input type="checkbox"> 25%+ Share Ownership</label>
        <label class="step3-check"><input type="checkbox"> 25%+ Voting Rights</label>
        <label class="step3-check"><input type="checkbox"> Right to Appoint Directors</label>

    </div>


    <!-- =========================
         TAB: SHAREHOLDINGS
    ========================= -->
    <div id="tab-share" class="step3-tab-content" style="display:none;">

        <h3>Share Holdings</h3>

        <label class="step3-check"><input type="checkbox"> Ordinary Shares</label>
        <label class="step3-check"><input type="checkbox"> Preference Shares</label>
        <label class="step3-check"><input type="checkbox"> Redeemable Shares</label>

    </div>




    <!-- =========================
     TAB: DETAILS (PERSON)
========================= -->
<div id="tab-det" class="step3-tab-content" style="display:none;">

    <h2 style="margin-bottom:25px;">Officer Details</h2>

    <!-- PERSONAL DETAILS -->
    <h3 style="color:#4a3b8f;margin-bottom:10px;">Personal Details</h3>

    <div class="step3-grid">
        <!-- Title -->
        <div class="grid-item">
            <label>Title</label>
            <input type="text" id="det_title" placeholder="Title" class="widefat">
        </div>

        <!-- DOB -->
        <div class="grid-item">
            <label>Date of Birth</label>
            <div class="dob-flex">
                <select id="dob_day" class="dob-input">
                    <option value="">dd</option>
                    <?php for($i=1;$i<=31;$i++) echo "<option>$i</option>"; ?>
                </select>

                <select id="dob_month" class="dob-input">
                    <option value="">mm</option>
                    <?php for($i=1;$i<=12;$i++) echo "<option>$i</option>"; ?>
                </select>

                <select id="dob_year" class="dob-input">
                    <option value="">yyyy</option>
                    <?php for($y=date('Y')-16;$y>=1900;$y--) echo "<option>$y</option>"; ?>
                </select>
            </div>
        </div>

        <!-- First Name -->
        <div class="grid-item">
            <label>First name</label>
            <input type="text" id="det_first" placeholder="First name" class="widefat">
        </div>

        <!-- Email -->
        <div class="grid-item">
            <label>Email</label>
            <input type="email" id="det_email" placeholder="Email" class="widefat">
        </div>

        <!-- Last Name -->
        <div class="grid-item">
            <label>Last name</label>
            <input type="text" id="det_last" placeholder="Last name" class="widefat">
        </div>

        <!-- Nationality -->
        <div class="grid-item">
            <label>Nationality</label>
            <input type="text" id="det_nationality" placeholder="Nationality" class="widefat">
        </div>

        <!-- Personal Verification Code -->
        <div class="grid-item-full">
            <label>Personal Verification Code (optional)</label>
            <input type="text" id="det_verification" placeholder="Personal Verification Code" class="widefat">
        </div>
    </div>



    <!-- RESIDENTIAL ADDRESS -->
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
                <option>UNITED KINGDOM</option>
            </select>
        </div>

        <div class="grid-item">
            <label>Postal Code *</label>
            <input type="text" id="addr_postcode" placeholder="Postcode" class="widefat">
        </div>
    </div>



    <!-- CONSENT -->
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



    <!-- =========================
     TAB: ADDRESSING (SERVICE ADDRESS)
========================= -->
<div id="tab-addr" class="step3-tab-content" style="display:none;">

    <h2 style="margin-bottom:25px;">Service Address</h2>

    <!-- BOX TITLE -->
    <div style="background:#f5f7ff;padding:25px;border-radius:6px;margin-bottom:25px;display:flex;gap:20px;align-items:center;">
        <div style="font-size:40px;">📨</div>
        <div>
            <h3 style="margin:0;">Service Address</h3>
            <p style="margin:5px 0 0;">
                The service address is a location where official documents and notices can be delivered for this officer.
            </p>
        </div>
    </div>

    <!-- SELECTION -->
    <h3 style="color:#4a3b8f;">Choose an Address</h3>

    <div id="service-address-options">

        <!-- Cambridge -->
        <div class="addr-option" data-value="cambridge">
            <div class="addr-header">
                <input type="radio" name="service_addr_type" value="cambridge">
                <span>Cambridge Directors Service Address</span>
            </div>
            <p class="addr-desc">
                Use of our Cambridge address as your service address for up to 2 officers, for 1 year.
            </p>
        </div>

        <!-- London -->
        <div class="addr-option" data-value="london">
            <div class="addr-header">
                <input type="radio" name="service_addr_type" value="london">
                <span>London Directors Service Address</span>
            </div>
            <p class="addr-desc">
                Use of our London address as your service address for up to 2 officers, for 1 year.
            </p>
        </div>

        <!-- Own address -->
        <div class="addr-option" data-value="own">
            <div class="addr-header">
                <input type="radio" name="service_addr_type" value="own">
                <span>Use Your Own Address</span>
            </div>
            <p class="addr-desc">
                Provide your residential service address manually.
            </p>
        </div>

    </div>


    <!-- OWN ADDRESS FIELDS (HIDDEN UNTIL SELECTED) -->
    <div id="own-address-box" style="display:none;margin-top:25px;">

        <h3 style="color:#4a3b8f;">Your Residential Address</h3>

        <div class="step3-grid">

            <div class="grid-item">
                <label>Name/Number *</label>
                <input type="text" id="own_name_number" class="widefat">
            </div>

            <div class="grid-item">
                <label>Street *</label>
                <input type="text" id="own_street" class="widefat">
            </div>

            <div class="grid-item">
                <label>Locality</label>
                <input type="text" id="own_locality" class="widefat">
            </div>

            <div class="grid-item">
                <label>Address Line 2</label>
                <input type="text" id="own_line2" class="widefat">
            </div>

            <div class="grid-item">
                <label>Address Line 3</label>
                <input type="text" id="own_line3" class="widefat">
            </div>

            <div class="grid-item">
                <label>Town *</label>
                <input type="text" id="own_town" class="widefat">
            </div>

            <div class="grid-item">
                <label>Address Line 4</label>
                <input type="text" id="own_line4" class="widefat">
            </div>

            <div class="grid-item">
                <label>Country *</label>
                <select id="own_country" class="widefat">
                    <option value="UNITED KINGDOM">UNITED KINGDOM</option>
                </select>
            </div>

            <div class="grid-item">
                <label>Postal Code *</label>
                <input type="text" id="own_postcode" class="widefat">
            </div>

        </div>
    </div>

    <!-- SAVE -->
    <button id="address-save-btn" class="step3-save-tab" style="margin-top:30px;">
        Save Officer
    </button>

</div>


</div>


<?php
wp_enqueue_script(
    'officer-person-js',
    NCUK_URL . 'assets/js/step3/officer-person.js',
    ['jquery'],
    filemtime(NCUK_PATH . 'assets/js/step3/officer-person.js'),
    true
);
?>