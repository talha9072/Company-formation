<div id="officer-person-form" class="step3-form-box" style="display:none;">

    <!-- FIXED MAIN TABS -->
    <div class="step3-tabs">
        <button class="step3-tab active" data-target="#tab-pos">Position</button>
        <button class="step3-tab" data-target="#tab-off">Officer</button>
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
     TAB: OFFICER
========================= -->
<div id="tab-off" class="step3-tab-content">

    <h3>Officer</h3>

    <p>Select officer type:</p>

    <div class="step3-checkbox-list">

        <label class="step3-check">
            <input type="radio" name="officer_type" value="person" checked>
            Individual Person
        </label>

        <label class="step3-check">
            <input type="radio" name="officer_type" value="corporate">
            Corporate Officer
        </label>

        <label class="step3-check">
            <input type="radio" name="officer_type" value="entity">
            Other Legal Entity
        </label>

    </div>

    <button id="officer-next-btn" class="step3-save-tab">Continue →</button>

</div>


    <!-- =========================
     TAB: DETAILS
========================= -->
<div id="tab-det" class="step3-tab-content">

    <h3>Officer Details</h3>

    <label>First Name</label>
    <input type="text" id="det_first" class="widefat">

    <label>Last Name</label>
    <input type="text" id="det_last" class="widefat">

    <label>Date of Birth</label>
    <input type="date" id="det_dob" class="widefat">

    <button id="details-next-btn" class="step3-save-tab">Continue →</button>
</div>


    <!-- =========================
     TAB: ADDRESSING
========================= -->
<div id="tab-addr" class="step3-tab-content">

    <h3>Address</h3>

    <label>Address Line 1</label>
    <input type="text" id="addr_line1" class="widefat">

    <label>City</label>
    <input type="text" id="addr_city" class="widefat">

    <label>Postcode</label>
    <input type="text" id="addr_postcode" class="widefat">

    <button id="address-save-btn" class="step3-save-tab">Save Officer</button>
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