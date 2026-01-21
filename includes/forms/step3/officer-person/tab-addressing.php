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

        <label class="addr-option">
            <input type="radio" name="service_addr_type" value="cambridge">
            <strong>Cambridge Directors Service Address</strong>
        </label>

        <label class="addr-option">
            <input type="radio" name="service_addr_type" value="london">
            <strong>London Directors Service Address</strong>
        </label>

        <label class="addr-option">
            <input type="radio" name="service_addr_type" value="own">
            <strong>Use Your Own Address</strong>
        </label>

    </div>

    <!-- ===================== -->
    <!-- CAMBRIDGE -->
    <!-- ===================== -->
    <div id="cambridge-address-box" class="service-box" style="display:none;margin-top:25px;">
    <h3 style="color:#4a3b8f;">Cambridge Service Address</h3>

    <div class="step3-grid">
        <div class="grid-item">
            <label>Address</label>
            <input type="text" id="cambridge_address" class="widefat"
                   value="St Johns Innovation Centre">
        </div>

        <div class="grid-item">
            <label>Street</label>
            <input type="text" id="cambridge_street" class="widefat"
                   value="Cowley Road">
        </div>

        <div class="grid-item">
            <label>Town</label>
            <input type="text" id="cambridge_town" class="widefat"
                   value="Cambridge">
        </div>

        <div class="grid-item">
            <label>Postcode</label>
            <input type="text" id="cambridge_postcode" class="widefat"
                   value="CB4 0WS">
        </div>

        <div class="grid-item">
            <label>Country</label>
            <input type="text" id="cambridge_country" class="widefat"
                   value="UNITED KINGDOM">
        </div>
    </div>
</div>

    <!-- ===================== -->
    <!-- LONDON -->
    <!-- ===================== -->
    <div id="london-address-box" class="service-box" style="display:none;margin-top:25px;">
    <h3 style="color:#4a3b8f;">London Service Address</h3>

    <div class="step3-grid">
        <div class="grid-item">
            <label>Address</label>
            <input type="text" id="london_address" class="widefat"
                   value="Kemp House">
        </div>

        <div class="grid-item">
            <label>Street</label>
            <input type="text" id="london_street" class="widefat"
                   value="152–160 City Road">
        </div>

        <div class="grid-item">
            <label>Town</label>
            <input type="text" id="london_town" class="widefat"
                   value="London">
        </div>

        <div class="grid-item">
            <label>Postcode</label>
            <input type="text" id="london_postcode" class="widefat"
                   value="EC1V 2NX">
        </div>

        <div class="grid-item">
            <label>Country</label>
            <input type="text" id="london_country" class="widefat"
                   value="UNITED KINGDOM">
        </div>
    </div>
</div>

    <!-- ===================== -->
    <!-- OWN ADDRESS -->
    <!-- ===================== -->
    <div id="own-address-box" class="service-box" style="display:none;margin-top:25px;">
        <h3 style="color:#4a3b8f;">Your Residential Address</h3>

        <div class="step3-grid">
            <div class="grid-item">
                <label>Name / Number *</label>
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

</div>

<!-- ===================== -->
<!-- JS TO TOGGLE ADDRESS FORMS -->
<!-- ===================== -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const radios = document.querySelectorAll('input[name="service_addr_type"]');

    const boxes = {
        cambridge: document.getElementById("cambridge-address-box"),
        london: document.getElementById("london-address-box"),
        own: document.getElementById("own-address-box")
    };

    function hideAll() {
        Object.values(boxes).forEach(box => {
            if (box) box.style.display = "none";
        });
    }

    radios.forEach(radio => {
        radio.addEventListener("change", function () {
            hideAll();
            const box = boxes[this.value];
            if (box) box.style.display = "block";
        });
    });

});
</script>
