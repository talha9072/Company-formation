<?php if (!defined('ABSPATH')) exit; ?>

<div id="tab-addr" class="step3-tab-content" style="display:none;">

    <h2 style="margin-bottom:25px;">Service Address</h2>

    <!-- INFO BOX -->
    <div style="background:#f5f7ff;padding:25px;border-radius:6px;margin-bottom:25px;display:flex;gap:20px;align-items:center;">
        <div style="font-size:40px;">📨</div>
        <div>
            <h3 style="margin:0;">Service Address</h3>
            <p style="margin:5px 0 0;">
                The service address is a location where official documents and notices can be delivered for this officer.
            </p>
        </div>
    </div>

    <!-- ===================== -->
    <!-- ADDRESS OPTIONS -->
    <!-- ===================== -->
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

        <!-- Own -->
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

    <!-- ===================== -->
    <!-- OWN ADDRESS BOX -->
    <!-- ===================== -->
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
