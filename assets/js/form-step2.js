document.addEventListener("DOMContentLoaded", function () {
    console.log("🔥 Step-2 JS Loaded");

    const saveBtn = document.getElementById("step2-save");

    const emailField = document.getElementById("registered_email");

    // Own address fields
    const addr1 = document.getElementById("addr_line1");
    const addr2 = document.getElementById("addr_line2");
    const addr3 = document.getElementById("addr_line3");
    const addr4 = document.getElementById("addr_line4");
    const addrCountry = document.getElementById("addr_country");
    const addrPostcode = document.getElementById("addr_postcode");

    let selectedOfficeID = null;
    let selectedOfficeName = null;
    let usingOwnAddress = false;

    /* -------------------------------------------------------
       REGISTERED OFFICE BUTTON CLICK
    ------------------------------------------------------- */
    document.querySelectorAll(".office-buy-btn").forEach(btn => {
        btn.addEventListener("click", function () {

            selectedOfficeID = this.dataset.id;
            selectedOfficeName = this.dataset.name;
            usingOwnAddress = false;

            console.log("🏢 Registered office selected:", selectedOfficeID, selectedOfficeName);
        });
    });

    /* -------------------------------------------------------
       OWN ADDRESS BUTTON CLICK
    ------------------------------------------------------- */
    document.getElementById("choose-address")?.addEventListener("click", function () {
        usingOwnAddress = true;

        selectedOfficeID = null;
        selectedOfficeName = "Own Address";

        console.log("🏠 Own Address chosen");
    });


    /* -------------------------------------------------------
       SAVE BUTTON CLICK
    ------------------------------------------------------- */
    saveBtn.addEventListener("click", function () {

        console.log("💾 Saving Step-2 via AJAX...");

        const payload = {
            action: "ncuk_save_step2",
            nonce: step2_ajax.nonce,

            registered_email: emailField.value.trim(),

            // Office
            step2_office_id: selectedOfficeID,
            step2_office_name: selectedOfficeName,

            // Own address fields
            step2_addr_line1: addr1.value.trim(),
            step2_addr_line2: addr2.value.trim(),
            step2_addr_line3: addr3.value.trim(),
            step2_addr_line4: addr4.value.trim(),
            step2_addr_country: addrCountry.value,
            step2_addr_postcode: addrPostcode.value.trim()
        };

        console.log("📤 Sending payload:", payload);

        jQuery.post(step2_ajax.ajax_url, payload, function (res) {
            console.log("📥 AJAX Response:", res);

            if (!res || !res.success) {
                alert("❌ Error saving Step-2. Check console.");
                return;
            }

            console.log("✅ Step-2 saved successfully.");

            // Unlock Step-3
            const step3 = document.querySelector('.step-item[data-step="3"]');
            if (step3) {
                step3.classList.remove("disabled");
                step3.click();
            }
        });

    });

});
