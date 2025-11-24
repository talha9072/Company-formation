document.addEventListener("DOMContentLoaded", function () {
    console.log("🔥 Step-2 JS Loaded");

    /* -------------------------------------------------------
       Inject CSS from JS
    ------------------------------------------------------- */
    const style = document.createElement("style");
    style.innerHTML = `
        .office-box,
        .address-box {
            border: 1px solid #ddd !important;
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .office-box.selected {
            border-color:#1a8f4a !important;
            background:#e9f8ef !important;
            box-shadow:0 0 0 2px rgba(26,143,74,0.25);
        }

        .address-box.selected {
            border-color:#ff8a26 !important;
            background:#fff3e8 !important;
            box-shadow:0 0 0 2px rgba(255,138,38,0.25);
        }
    `;
    document.head.appendChild(style);


    /* -------------------------------------------------------
       Elements
    ------------------------------------------------------- */
    const saveBtn = document.getElementById("step2-save");
    const emailField = document.getElementById("registered_email");

    const addr1 = document.getElementById("addr_line1");
    const addr2 = document.getElementById("addr_line2");
    const addr3 = document.getElementById("addr_line3");
    const addr4 = document.getElementById("addr_line4");
    const addrCountry = document.getElementById("addr_country");
    const addrPostcode = document.getElementById("addr_postcode");

    const ownForm = document.getElementById("own-address-form");
    const ownSelectorBox = document.querySelector(".address-box");

    const addressTitle = document.getElementById("address-form-title");

    let selectedOfficeID = null;
    let selectedOfficeName = null;

    /* -------------------------------------------------------
       Helpers
    ------------------------------------------------------- */
    function clearSelections() {
        document.querySelectorAll(".office-box").forEach(box =>
            box.classList.remove("selected")
        );
        ownSelectorBox.classList.remove("selected");
    }

    function clearAddressFields() {
        addr1.value = "";
        addr2.value = "";
        addr3.value = "";
        addr4.value = "";
        addrPostcode.value = "";
    }


    /* -------------------------------------------------------
       OFFICE SELECTED (Buy Now)
    ------------------------------------------------------- */
    document.querySelectorAll(".office-buy-btn").forEach(btn => {
        btn.addEventListener("click", function () {

            selectedOfficeID = this.dataset.id;
            selectedOfficeName = this.dataset.name;

            console.log("🏢 Office selected:", selectedOfficeName);

            // Highlight correct office
            clearSelections();
            this.closest(".office-box").classList.add("selected");

            // Show forwarding address form
            ownForm.style.display = "block";

            // Set correct title
            addressTitle.innerText = "Forwarding Address for: " + selectedOfficeName;

            // Clear fields (fresh forwarding address input)
            clearAddressFields();
        });
    });


    /* -------------------------------------------------------
       OWN ADDRESS OPTION SELECTED
    ------------------------------------------------------- */
    document.getElementById("choose-address")?.addEventListener("click", function () {

        selectedOfficeID = null;
        selectedOfficeName = "Own Address";

        console.log("🏠 Own Address selected");

        // Highlight only own address box
        clearSelections();
        ownSelectorBox.classList.add("selected");

        // Show form
        ownForm.style.display = "block";

        // Correct title
        addressTitle.innerText = "Enter Own Address";

        // Clear fields
        clearAddressFields();
    });


    /* -------------------------------------------------------
       SAVE BUTTON
    ------------------------------------------------------- */
    saveBtn.addEventListener("click", function () {

        console.log("💾 Saving Step-2...");

        const payload = {
            action: "ncuk_save_step2",
            nonce: step2_ajax.nonce,

            registered_email: emailField.value.trim(),

            step2_office_id: selectedOfficeID,
            step2_office_name: selectedOfficeName,

            step2_addr_line1: addr1.value.trim(),
            step2_addr_line2: addr2.value.trim(),
            step2_addr_line3: addr3.value.trim(),
            step2_addr_line4: addr4.value.trim(),
            step2_addr_country: addrCountry.value,
            step2_addr_postcode: addrPostcode.value.trim()
        };

        console.log("📤 Payload:", payload);

        jQuery.post(step2_ajax.ajax_url, payload, function (res) {

            console.log("📥 AJAX Response:", res);

            if (!res || !res.success) {
                alert("❌ Error saving Step-2. Check console.");
                return;
            }

            console.log("✅ Step-2 Saved!");

            const step3 = document.querySelector('.step-item[data-step="3"]');
            if (step3) {
                step3.classList.remove("disabled");
                step3.click();
            }
        });
    });

});
