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
        <p>Officer fields...</p>
    </div>


    <!-- =========================
         TAB: DETAILS
    ========================= -->
    <div id="tab-det" class="step3-tab-content">
        <h3>Details</h3>
        <p>Details fields...</p>
    </div>


    <!-- =========================
         TAB: ADDRESSING
    ========================= -->
    <div id="tab-addr" class="step3-tab-content">
        <h3>Addressing</h3>
        <p>Address fields...</p>
    </div>

</div>
