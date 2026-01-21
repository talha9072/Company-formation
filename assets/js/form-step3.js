document.addEventListener("DOMContentLoaded", function () {

    console.log("🔥 STEP 3 – Officer Form + Tabs + PSC + Storage LOADED (2025 fixed version)");

    // ────────────────────────────────────────────────────────────────
    //  COMMON ELEMENTS
    // ────────────────────────────────────────────────────────────────
    const officerForm = document.getElementById("officer-person-form");
    const listBox     = document.getElementById("officer-list");
    const continueBtn = document.getElementById("step3-save");

    if (!officerForm) {
        console.warn("Officer form (#officer-person-form) not found");
        return;
    }

    // ────────────────────────────────────────────────────────────────
    //  PART 1 – Officer Type Radio → Show Form + Hidden Type
    // ────────────────────────────────────────────────────────────────
    const radios = document.querySelectorAll('input[name="officer_type"]');

    let hiddenTypeInput = officerForm.querySelector('input[name="officer_type_hidden"]');
    if (!hiddenTypeInput) {
        hiddenTypeInput = document.createElement("input");
        hiddenTypeInput.type = "hidden";
        hiddenTypeInput.name = "officer_type_hidden";
        officerForm.appendChild(hiddenTypeInput);
    }

    function showOfficerForm(type) {
        officerForm.style.display = "block";
        hiddenTypeInput.value = type;
        console.log("👤 Officer type selected →", type);
        officerForm.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    radios.forEach(radio => {
        radio.addEventListener("change", function () {
            officerForm.reset?.();
            showOfficerForm(this.value);
            refreshRoleLogic();         // important: re-evaluate roles/tabs
            updateSaveButtonLocation(); // important: button may need to move
        });
    });

    const defaultRadio = document.querySelector('input[name="officer_type"]:checked');
    if (defaultRadio) {
        showOfficerForm(defaultRadio.value);
    }

    // ────────────────────────────────────────────────────────────────
    //  PART 2 – PSC logic (Company / Firm / Trust)
    // ────────────────────────────────────────────────────────────────
    function setupInternalLogic({ shares, voting, directors, other }) {
        if (!shares || !voting || !directors || !other) return;

        const rowDirectors = directors.closest("tr");
        const rowOther     = other.closest("tr");

        function updateLogic() {
            const sharesVal   = shares.value;
            const votingVal   = voting.value;
            const directorsVal = directors.value;

            if (sharesVal !== "na" || votingVal !== "na") {
                rowDirectors.style.display = "none";
                directors.value = "0";
            } else {
                rowDirectors.style.display = "";
            }

            if (directorsVal === "1") {
                rowOther.style.display = "none";
                other.value = "0";
            } else {
                rowOther.style.display = "";
            }
        }

        shares.addEventListener("change", updateLogic);
        voting.addEventListener("change", updateLogic);
        directors.addEventListener("change", updateLogic);

        updateLogic();
    }

    function setupToggleSection({ yes, no, section, resetFields, internalSetup }) {
        if (!yes || !no || !section) return;

        function toggle() {
            if (yes.checked) {
                section.style.display = "block";
                if (internalSetup) internalSetup();
            } else {
                section.style.display = "none";
                resetFields.forEach(f => { if (f) f.value = f.dataset.reset || ""; });
            }
        }

        yes.addEventListener("change", toggle);
        no.addEventListener("change", toggle);
        toggle();
    }

    // Always-visible company PSC
    setupInternalLogic({
        shares:    document.querySelector('[name="psc_company_shares"]'),
        voting:    document.querySelector('[name="psc_company_voting"]'),
        directors: document.querySelector('[name="psc_company_directors"]'),
        other:     document.querySelector('[name="psc_company_other"]')
    });

    // Firm section
    const firmSection = document.getElementById("psc-firm-section");
    if (firmSection) {
        const firmFields = {
            shares:    firmSection.querySelector('[name="psc_firm_shares"]'),
            voting:    firmSection.querySelector('[name="psc_firm_voting"]'),
            directors: firmSection.querySelector('[name="psc_firm_directors"]'),
            other:     firmSection.querySelector('[name="psc_firm_other"]')
        };
        Object.values(firmFields).forEach(f => {
            if (f) f.dataset.reset = f.name.includes("shares") || f.name.includes("voting") ? "na" : "0";
        });
        setupToggleSection({
            yes: document.querySelector('input[name="psc_as_firm"][value="1"]'),
            no:  document.querySelector('input[name="psc_as_firm"][value="0"]'),
            section: firmSection,
            resetFields: Object.values(firmFields),
            internalSetup: () => setupInternalLogic(firmFields)
        });
    }

    // Trust section
    const trustSection = document.getElementById("psc-trust-section");
    if (trustSection) {
        const trustFields = {
            shares:    trustSection.querySelector('[name="psc_trust_shares"]'),
            voting:    trustSection.querySelector('[name="psc_trust_voting"]'),
            directors: trustSection.querySelector('[name="psc_trust_directors"]'),
            other:     trustSection.querySelector('[name="psc_trust_other"]')
        };
        Object.values(trustFields).forEach(f => {
            if (f) f.dataset.reset = f.name.includes("shares") || f.name.includes("voting") ? "na" : "0";
        });
        setupToggleSection({
            yes: document.querySelector('input[name="psc_as_trust"][value="1"]'),
            no:  document.querySelector('input[name="psc_as_trust"][value="0"]'),
            section: trustSection,
            resetFields: Object.values(trustFields),
            internalSetup: () => setupInternalLogic(trustFields)
        });
    }

    // ────────────────────────────────────────────────────────────────
    //  PART 3 – Tabs + Dynamic Save Button
    // ────────────────────────────────────────────────────────────────
    const tabsContainer = officerForm.querySelector(".step3-tabs");
    if (!tabsContainer) console.warn("Tabs container .step3-tabs not found");

    const tabButtons  = () => officerForm.querySelectorAll(".step3-tab");
    const tabContents = () => officerForm.querySelectorAll(".step3-tab-content");

    function getVisibleTabIds() {
        return Array.from(tabButtons())
            .filter(btn => btn.offsetParent !== null)
            .map(btn => btn.dataset.target);
    }

    function getLastVisibleTabId() {
        const ids = getVisibleTabIds();
        return ids.length ? ids[ids.length - 1] : null;
    }

    function switchTab(targetId) {
        tabContents().forEach(el => el.style.display = "none");
        const content = officerForm.querySelector(targetId);
        if (content) content.style.display = "block";

        tabButtons().forEach(btn => btn.classList.remove("active"));
        const btn = tabsContainer?.querySelector(`[data-target="${targetId}"]`);
        if (btn) btn.classList.add("active");

        updateSaveButtonLocation();
    }

    // Save button (created once, moved dynamically)
    let saveButton = null;

    function createSaveButton() {
        if (saveButton) return;
        saveButton = document.createElement("button");
        saveButton.id = "save-officer-step3";
        saveButton.type = "button";
        saveButton.className = "btn-primary";
        saveButton.textContent = "Save Officer";
        saveButton.style.marginTop = "20px";
        saveButton.style.padding = "10px 20px";
        // Real listener will be attached below in storage part
    }

    function updateSaveButtonLocation() {
        if (!saveButton) createSaveButton();

        const lastId = getLastVisibleTabId();
        if (!lastId) {
            saveButton.style.display = "none";
            return;
        }

        const target = officerForm.querySelector(lastId);
        if (!target) {
            saveButton.style.display = "none";
            return;
        }

        if (saveButton.parentElement) saveButton.parentElement.removeChild(saveButton);
        target.appendChild(saveButton);
        saveButton.style.display = "inline-block";
    }

    // Role logic → show/hide consent + NOC/Share tabs
    const roleDirector   = officerForm.querySelector("#role_director");
    const roleShareholder = officerForm.querySelector("#role_shareholder");
    const roleSecretary  = officerForm.querySelector("#role_secretary");
    const rolePSC        = officerForm.querySelector("#role_psc");

    const consentBox  = officerForm.querySelector("#consent-box");
    const tabNocBtn   = officerForm.querySelector("#tab-noc-btn");
    const tabShareBtn = officerForm.querySelector("#tab-share-btn");

    function refreshRoleLogic() {
        if (consentBox)  consentBox.style.display = "none";
        if (tabNocBtn)   tabNocBtn.style.display  = "none";
        if (tabShareBtn) tabShareBtn.style.display = "none";

        if (roleDirector?.checked || roleSecretary?.checked) {
            if (consentBox) consentBox.style.display = "block";
        }
        if (rolePSC?.checked) {
            if (tabNocBtn) tabNocBtn.style.display = "inline-block";
        }
        if (roleShareholder?.checked) {
            if (tabShareBtn) tabShareBtn.style.display = "inline-block";
        }

        setTimeout(updateSaveButtonLocation, 50);
    }

    [roleDirector, roleShareholder, roleSecretary, rolePSC]
        .filter(Boolean)
        .forEach(el => el.addEventListener("change", refreshRoleLogic));

    // Tab navigation
    tabButtons().forEach(btn => {
        btn.addEventListener("click", function () {
            switchTab(this.dataset.target);
        });
    });

    officerForm.querySelector("#officer-next-btn")?.addEventListener("click", () => switchTab("#tab-det"));
    officerForm.querySelector("#details-next-btn")?.addEventListener("click", () => switchTab("#tab-addr"));

    // Initial setup
    refreshRoleLogic();
    const initialTab = officerForm.querySelector(".step3-tab.active")?.dataset.target || "#tab-pos";
    switchTab(initialTab);

    // ────────────────────────────────────────────────────────────────
    //  PART 4 – STORAGE + REAL SAVE + LIST + VALIDATION
    // ────────────────────────────────────────────────────────────────
    const STORAGE_KEY = "ncuk_company_officers";

    function getOfficers() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]"); }
        catch { return []; }
    }

    function setOfficers(arr) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(arr));
    }

    function getOfficerType() {
        return document.querySelector('input[name="officer_type"]:checked')?.value || "";
    }

    function collectDob() {
        const d = officerForm.querySelector("#dob_day")?.value;
        const m = officerForm.querySelector("#dob_month")?.value;
        const y = officerForm.querySelector("#dob_year")?.value;
        if (!d || !m || !y) return "";
        return `${y}-${String(m).padStart(2,"0")}-${String(d).padStart(2,"0")}`;
    }

    function collectServiceAddress() {
    const type =
        document.querySelector('input[name="service_addr_type"]:checked')?.value || "";

    // ── OWN ADDRESS ─────────────────────────
    if (type === "own") {
        return {
            type,
            line1: document.getElementById("own_name_number")?.value.trim() || "",
            line2: document.getElementById("own_street")?.value.trim() || "",
            line3: document.getElementById("own_line2")?.value.trim() || "",
            town: document.getElementById("own_town")?.value.trim() || "",
            country: document.getElementById("own_country")?.value.trim() || "",
            postcode: document.getElementById("own_postcode")?.value.trim() || ""
        };
    }

    // ── CAMBRIDGE ADDRESS ───────────────────
    if (type === "cambridge") {
        return {
            type,
            line1: document.getElementById("cambridge_address")?.value.trim() || "",
            line2: document.getElementById("cambridge_street")?.value.trim() || "",
            line3: "",
            town: document.getElementById("cambridge_town")?.value.trim() || "",
            country: document.getElementById("cambridge_country")?.value.trim() || "",
            postcode: document.getElementById("cambridge_postcode")?.value.trim() || ""
        };
    }

    // ── LONDON ADDRESS ──────────────────────
    if (type === "london") {
        return {
            type,
            line1: document.getElementById("london_address")?.value.trim() || "",
            line2: document.getElementById("london_street")?.value.trim() || "",
            line3: "",
            town: document.getElementById("london_town")?.value.trim() || "",
            country: document.getElementById("london_country")?.value.trim() || "",
            postcode: document.getElementById("london_postcode")?.value.trim() || ""
        };
    }

    return { type };
}

function collectOfficerData() {
  return {
    id: Date.now().toString(),
    officer_type: getOfficerType(),

    // ── ROLES ─────────────────────────
    roles: {
      director: !!roleDirector?.checked,
      shareholder: !!roleShareholder?.checked,
      secretary: !!roleSecretary?.checked,
      psc: !!rolePSC?.checked
    },

    // ── PERSONAL ─────────────────────
    title: officerForm.querySelector("#det_title")?.value.trim() || "",
    first: officerForm.querySelector("#det_first")?.value.trim() || "",
    last: officerForm.querySelector("#det_last")?.value.trim() || "",
    email: officerForm.querySelector("#det_email")?.value.trim() || "",
    dob: collectDob(),
    nationality: officerForm.querySelector("#det_nationality")?.value.trim() || "",
    consent: !!officerForm.querySelector("#consent_auth")?.checked,

    // ── RESIDENTIAL ADDRESS ───────────
    residential: {
      line1: officerForm.querySelector("#addr_line1")?.value.trim() || "",
      line2: officerForm.querySelector("#addr_line2")?.value.trim() || "",
      line3: officerForm.querySelector("#addr_line3")?.value.trim() || "",
      town: officerForm.querySelector("#addr_town")?.value.trim() || "",
      country: officerForm.querySelector("#addr_country")?.value.trim() || "",
      postcode: officerForm.querySelector("#addr_postcode")?.value.trim() || ""
    },

    // ── SERVICE ADDRESS (🔥 FIXED) ─────
    service: collectServiceAddress(),

    // ── SHARES ────────────────────────
    shares: {
      class: officerForm.querySelector("#share_class")?.value.trim() || "",
      quantity: officerForm.querySelector("#share_quantity")?.value.trim() || "",
      price: officerForm.querySelector("#share_price")?.value.trim() || "",
      currency: "GBP",
      particulars: officerForm.querySelector("#share_particulars")?.value.trim() || ""
    },

    // ── PSC (COMPANY) ─────────────────
    noc: {
      company_shares:
        officerForm.querySelector('[name="psc_company_shares"]')?.value || "na",
      company_voting:
        officerForm.querySelector('[name="psc_company_voting"]')?.value || "na",
      company_directors:
        officerForm.querySelector('[name="psc_company_directors"]')?.value || "0",
      company_other:
        officerForm.querySelector('[name="psc_company_other"]')?.value || "0",

      // ── FIRM ────────────────────────
      firm_shares:
        document.querySelector('input[name="psc_as_firm"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_firm_shares"]')?.value || "na"
          : "na",

      firm_voting:
        document.querySelector('input[name="psc_as_firm"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_firm_voting"]')?.value || "na"
          : "na",

      firm_directors:
        document.querySelector('input[name="psc_as_firm"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_firm_directors"]')?.value || "0"
          : "0",

      firm_other:
        document.querySelector('input[name="psc_as_firm"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_firm_other"]')?.value || "0"
          : "0",

      // ── TRUST ───────────────────────
      trust_shares:
        document.querySelector('input[name="psc_as_trust"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_trust_shares"]')?.value || "na"
          : "na",

      trust_voting:
        document.querySelector('input[name="psc_as_trust"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_trust_voting"]')?.value || "na"
          : "na",

      trust_directors:
        document.querySelector('input[name="psc_as_trust"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_trust_directors"]')?.value || "0"
          : "0",

      trust_other:
        document.querySelector('input[name="psc_as_trust"]:checked')?.value === "1"
          ? officerForm.querySelector('[name="psc_trust_other"]')?.value || "0"
          : "0"
    }
  };
}


    function validateOfficer(data) {
        const errors = [];

        if (!data.officer_type) errors.push("Please select officer type");
        if (!Object.values(data.roles).some(Boolean)) errors.push("Select at least one role");

        if (data.officer_type === "person") {
            if (!data.first)  errors.push("First name is required");
            if (!data.last)   errors.push("Last name is required");
            if (!data.dob)    errors.push("Date of birth is required");
            if (!data.residential.line1)    errors.push("Address line 1 is required");
            if (!data.residential.town)     errors.push("Town/city is required");
            if (!data.residential.postcode) errors.push("Postcode is required");
        }

        if ((data.roles.director || data.roles.secretary) && !data.consent) {
            errors.push("Consent is required for Director/Secretary");
        }

        if (data.roles.shareholder) {
            const qty = Number(data.shares.quantity);
            if (isNaN(qty) || qty <= 0) errors.push("Valid number of shares (>0) is required");
        }

        if (data.roles.psc) {
            const c = data.noc;
            const hasControl = (
                c.company_shares !== "na" ||
                c.company_voting !== "na" ||
                c.company_directors === "1" ||
                c.company_other === "1"
            );
            if (!hasControl) errors.push("At least one nature of control must be selected for PSC");
        }

        return errors;
    }

    function checkMandatoryCompanyRoles() {
        const officers = getOfficers();
        const hasPersonDirector = officers.some(o => o.officer_type === "person" && o.roles.director);
        const hasShareholder    = officers.some(o => o.roles.shareholder);
        return hasPersonDirector && hasShareholder;
    }

    function updateContinueButton() {
        if (!continueBtn) return;
        const allowed = checkMandatoryCompanyRoles();
        continueBtn.disabled = !allowed;
        continueBtn.style.opacity = allowed ? "1" : "0.5";
    }

    function renderList() {
        const officers = getOfficers();
        listBox.innerHTML = "";

        if (!checkMandatoryCompanyRoles()) {
            listBox.innerHTML = `
                <div style="background:#fff3cd;border:1px solid #ffeeba;padding:12px;border-radius:6px;margin-bottom:16px;">
                    <strong>Required before continuing:</strong>
                    <ul style="margin:8px 0 0 20px;">
                        <li>At least one <b>Person Director</b></li>
                        <li>At least one <b>Shareholder</b></li>
                    </ul>
                </div>`;
        }

        if (officers.length === 0) {
            listBox.innerHTML += "<p style='color:#555;'><em>No officers added yet.</em></p>";
        } else {
            officers.forEach(o => {
                const item = document.createElement("div");
                item.style.cssText = "padding:12px; margin-bottom:8px; background:#f9f9f9; border:1px solid #ddd; border-radius:4px;";
                item.innerHTML = `
                    <strong>${o.first || "(non-person)"} ${o.last || ""}</strong>
                    <small> — ${o.officer_type}</small><br>
                    Roles: ${Object.entries(o.roles).filter(([,v])=>v).map(([k])=>k).join(", ") || "none"}<br>
                    <button type="button" class="delete-officer" data-id="${o.id}"
                        style="color:#c62828; border:none; background:none; cursor:pointer; margin-top:6px;">
                        Delete
                    </button>
                `;
                listBox.appendChild(item);
            });
        }

        listBox.querySelectorAll(".delete-officer").forEach(btn => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                setOfficers(getOfficers().filter(o => o.id !== id));
                renderList();
                updateContinueButton();
            });
        });

        updateContinueButton();
    }

    // ────────────────────────────────────────────────────────────────
//  FINAL SUBMIT – SAVE ALL OFFICERS TO DB (Save & Continue)
// ────────────────────────────────────────────────────────────────
const finalSaveBtn = document.getElementById("step3-save");

if (finalSaveBtn) {
    // extra safety
    finalSaveBtn.type = "button";

    finalSaveBtn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const officers = getOfficers();

        if (!officers.length) {
            alert("Please add at least one officer before continuing.");
            return;
        }

        if (!checkMandatoryCompanyRoles()) {
            alert("You must add at least:\n• One Person Director\n• One Shareholder");
            return;
        }

        // prevent double click
        if (finalSaveBtn.dataset.saving === "1") return;
        finalSaveBtn.dataset.saving = "1";

        finalSaveBtn.disabled = true;
        finalSaveBtn.textContent = "Saving...";

        // 🔑 WORDPRESS-COMPATIBLE PAYLOAD
        const payload = new URLSearchParams({
            action: "ncuk_save_step3_all_officers",
            nonce: NCUK_STEP3.nonce,
            officers: JSON.stringify(officers)
        });

        fetch(NCUK_STEP3.ajax_url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: payload.toString()
        })
        .then(r => r.json())
        .then(resp => {
            if (!resp || !resp.success) {
                console.error("STEP3 AJAX ERROR:", resp);
                alert("Failed to save officers:\n" + (resp?.data?.msg || "Unknown error"));

                // 🔁 RESET BUTTON ON FAILURE
                finalSaveBtn.disabled = false;
                finalSaveBtn.textContent = "Save & Continue →";
                finalSaveBtn.dataset.saving = "0";
                return;
            }

            

            // 🔁 ALLOW FURTHER EDITS (NO FREEZE)
            renderList();
            updateContinueButton();

            // 🔁 RESET SAVE BUTTON (CRITICAL FIX)
            finalSaveBtn.disabled = false;
            finalSaveBtn.textContent = "Save & Continue →";
            finalSaveBtn.dataset.saving = "0";

            // ✅ ENABLE & MOVE TO STEP 4 (SAFE)
            const step4 = document.querySelector('.step-item[data-step="4"]');
            if (step4) {
                step4.classList.remove("disabled");
                step4.click(); // ✅ REAL CLICK ONLY
            }
        })
        .catch(err => {
            console.error("STEP3 NETWORK ERROR:", err);
            alert("Network error while saving officers.");

            finalSaveBtn.disabled = false;
            finalSaveBtn.textContent = "Save & Continue →";
            finalSaveBtn.dataset.saving = "0";
        });
    });
}



    // ── REAL SAVE ─────────────────────────────────────────────────────
    function handleSave() {
        const data = collectOfficerData();
        const errors = validateOfficer(data);

        if (errors.length > 0) {
            alert("Cannot save officer:\n\n• " + errors.join("\n• "));
            console.table(errors);
            return;
        }

        const officers = getOfficers();
        officers.push(data);
        setOfficers(officers);

        alert("Officer saved successfully!");
        renderList();

        // Reset & go back to start
        officerForm.reset?.();
        if (defaultRadio) defaultRadio.checked = true;
        showOfficerForm(defaultRadio?.value || "");
        switchTab("#tab-pos");
    }

    // Attach save listener
    function tryAttachSaveListener() {
        if (!saveButton) createSaveButton();
        if (saveButton && !saveButton.dataset.listener) {
            saveButton.addEventListener("click", handleSave);
            saveButton.dataset.listener = "attached";
            console.log("Save button listener attached");
        }
    }

    // Try immediately + watch for changes
    tryAttachSaveListener();
    setInterval(tryAttachSaveListener, 600); // fallback

    // Initial render
    renderList();

    


   

});


