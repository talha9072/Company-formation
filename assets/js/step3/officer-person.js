document.addEventListener("DOMContentLoaded", function () {

    console.log("🔥 officer-person.js loaded (dynamic save button v2)");

    const formBox = document.getElementById("officer-person-form");
    if (!formBox) return;

    // ───────────────────────────────────────────────
    //  Tab management
    // ───────────────────────────────────────────────
    const tabsContainer  = formBox.querySelector(".step3-tabs");
    const tabButtons     = () => formBox.querySelectorAll(".step3-tab");
    const tabContents    = () => formBox.querySelectorAll(".step3-tab-content");

    function getVisibleTabIds() {
        return Array.from(tabButtons())
            .filter(btn => btn.offsetParent !== null)   // is visible in DOM
            .map(btn => btn.dataset.target);
    }

    function getCurrentlyActiveTabId() {
        const activeBtn = tabsContainer.querySelector(".step3-tab.active");
        return activeBtn ? activeBtn.dataset.target : null;
    }

    function getLastVisibleTabId() {
        const visibleIds = getVisibleTabIds();
        return visibleIds.length > 0 ? visibleIds[visibleIds.length - 1] : null;
    }

    function switchTab(targetId) {
        // Hide all contents
        tabContents().forEach(el => el.style.display = "none");

        // Show target
        const content = formBox.querySelector(targetId);
        if (content) content.style.display = "block";

        // Update active class on tabs
        tabButtons().forEach(btn => btn.classList.remove("active"));
        const btn = tabsContainer.querySelector(`[data-target="${targetId}"]`);
        if (btn) btn.classList.add("active");

        // Move / show Save button if needed
        updateSaveButtonLocation();
    }

    // ───────────────────────────────────────────────
    //  Save Officer button — only in LAST visible tab
    // ───────────────────────────────────────────────
    let saveButton = null;

    function createSaveButtonIfNeeded() {
        if (saveButton) return;

        saveButton = document.createElement("button");
        saveButton.id = "save-officer-step3";
        saveButton.type = "button";
        saveButton.className = "btn-primary";
        saveButton.textContent = "Save Officer";
       

        saveButton.addEventListener("click", saveOfficer);
    }

    function updateSaveButtonLocation() {
        if (!saveButton) createSaveButtonIfNeeded();

        const lastVisibleId = getLastVisibleTabId();
        if (!lastVisibleId) {
            saveButton.style.display = "none";
            return;
        }

        const targetContent = formBox.querySelector(lastVisibleId);
        if (!targetContent) {
            saveButton.style.display = "none";
            return;
        }

        // Remove from previous location if any
        if (saveButton.parentElement) {
            saveButton.parentElement.removeChild(saveButton);
        }

        // Append to the end of the last visible tab content
        targetContent.appendChild(saveButton);
        saveButton.style.display = "inline-block";
    }

    // ───────────────────────────────────────────────
    //  Role logic → visibility of dynamic tabs
    // ───────────────────────────────────────────────
    const roleDirector   = formBox.querySelector("#role_director");
    const roleShareholder = formBox.querySelector("#role_shareholder");
    const roleSecretary  = formBox.querySelector("#role_secretary");
    const rolePSC        = formBox.querySelector("#role_psc");

    const consentBox   = formBox.querySelector("#consent-box");
    const tabNocBtn    = formBox.querySelector("#tab-noc-btn");
    const tabShareBtn  = formBox.querySelector("#tab-share-btn");

    function refreshRoleLogic() {
        consentBox.style.display = "none";
        tabNocBtn.style.display  = "none";
        tabShareBtn.style.display = "none";

        if (roleDirector.checked || roleSecretary.checked) {
            consentBox.style.display = "block";
        }

        if (rolePSC.checked) {
            tabNocBtn.style.display = "inline-block";
        }

        if (roleShareholder.checked) {
            tabShareBtn.style.display = "inline-block";
        }

        // Very important: dynamic tabs appeared/disappeared → move save button
        setTimeout(updateSaveButtonLocation, 0);
    }

    [roleDirector, roleShareholder, roleSecretary, rolePSC].forEach(el => {
        el.addEventListener("change", refreshRoleLogic);
    });

    // ───────────────────────────────────────────────
    //  Tab click handlers
    // ───────────────────────────────────────────────
    tabButtons().forEach(btn => {
        btn.addEventListener("click", function () {
            switchTab(this.dataset.target);
        });
    });

    // ───────────────────────────────────────────────
    //  Next buttons
    // ───────────────────────────────────────────────
    const officerNextBtn = formBox.querySelector("#officer-next-btn");
    const detailsNextBtn = formBox.querySelector("#details-next-btn");

    if (officerNextBtn) {
        officerNextBtn.addEventListener("click", () => switchTab("#tab-det"));
    }

    if (detailsNextBtn) {
        detailsNextBtn.addEventListener("click", () => switchTab("#tab-addr"));
    }

    // ───────────────────────────────────────────────
    //  Save logic (you can expand fields later)
    // ───────────────────────────────────────────────
    function saveOfficer() {
        // Collect data (expand as needed)
        const data = {
            type: "person",
            roles: {
                director:   roleDirector.checked,
                shareholder: roleShareholder.checked,
                secretary:  roleSecretary.checked,
                psc:        rolePSC.checked
            },
            first:   formBox.querySelector("#det_first")?.value   || "",
            last:    formBox.querySelector("#det_last")?.value    || "",
            dob:     collectDob() || "",
            email:   formBox.querySelector("#det_email")?.value   || "",
            address: {
                line1:    formBox.querySelector("#addr_line1")?.value    || "",
                postcode: formBox.querySelector("#addr_postcode")?.value || "",
                // ... add more fields
            }
        };

        console.log("Saving officer:", data);

        // ── Your real save logic here ──
        // e.g. push to array, send to server, show success message...

        // Optional: go back to list / reset form
        // switchTab("#tab-pos");
    }

    function collectDob() {
        const d = formBox.querySelector("#dob_day")?.value;
        const m = formBox.querySelector("#dob_month")?.value;
        const y = formBox.querySelector("#dob_year")?.value;
        if (!d || !m || !y) return "";
        return `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
    }

    // ───────────────────────────────────────────────
    //  Initial setup
    // ───────────────────────────────────────────────
    refreshRoleLogic();           // set initial tab visibility
    updateSaveButtonLocation();   // place save button correctly at start

    // If you have a default starting tab, activate it
    const initialActive = getCurrentlyActiveTabId() || "#tab-pos";
    switchTab(initialActive);
});