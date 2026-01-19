document.addEventListener("DOMContentLoaded", function () {

    console.log("💾 officer-storage.js loaded (FULL + step3-save lock)");

    const STORAGE_KEY = "ncuk_company_officers";

    const saveBtn = document.getElementById("save-officer-step3");
    const continueBtn = document.getElementById("step3-save");
    const listBox = document.getElementById("officer-list");
    const formBox = document.getElementById("officer-person-form");

    if (!saveBtn || !listBox || !formBox) return;

    /* =================================================
       Local Storage
    ================================================= */
    function getOfficers() {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || "[]");
    }

    function setOfficers(list) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    }

    /* =================================================
       Officer Type (RADIO – MUST)
    ================================================= */
    function getOfficerType() {
        const checked = document.querySelector('input[name="officer_type"]:checked');
        return checked ? checked.value : "";
    }

    /* =================================================
       Helpers
    ================================================= */
    function collectDob() {
        const d = formBox.querySelector("#dob_day")?.value;
        const m = formBox.querySelector("#dob_month")?.value;
        const y = formBox.querySelector("#dob_year")?.value;
        if (!d || !m || !y) return "";
        return `${y}-${m.padStart(2, "0")}-${d.padStart(2, "0")}`;
    }

    function collectOfficerData() {
        return {
            id: Date.now().toString(),

            officer_type: getOfficerType(), // person | corporate | legalentity

            roles: {
                director: formBox.querySelector("#role_director")?.checked || false,
                shareholder: formBox.querySelector("#role_shareholder")?.checked || false,
                secretary: formBox.querySelector("#role_secretary")?.checked || false,
                psc: formBox.querySelector("#role_psc")?.checked || false
            },

            /* PERSON DETAILS */
            first: formBox.querySelector("#det_first")?.value.trim() || "",
            last: formBox.querySelector("#det_last")?.value.trim() || "",
            email: formBox.querySelector("#det_email")?.value || "",
            dob: collectDob(),
            consent: formBox.querySelector("#consent_auth")?.checked || false,

            residential: {
                line1: formBox.querySelector("#addr_line1")?.value || "",
                town: formBox.querySelector("#addr_town")?.value || "",
                postcode: formBox.querySelector("#addr_postcode")?.value || ""
            },

            shares: {
                quantity: formBox.querySelector("#share_quantity")?.value || ""
            },

            noc: {
                company_shares: formBox.querySelector('[name="psc_company_shares"]')?.value || "na",
                company_voting: formBox.querySelector('[name="psc_company_voting"]')?.value || "na",
                company_directors: formBox.querySelector('[name="psc_company_directors"]')?.value || "0",
                company_other: formBox.querySelector('[name="psc_company_other"]')?.value || "0"
            }
        };
    }

    /* =================================================
       VALIDATION
    ================================================= */
    function validateOfficer(data) {
        const errors = [];

        if (!data.officer_type) {
            errors.push("Please select officer type (Person / Corporate / Legal Entity).");
        }

        if (!Object.values(data.roles).some(v => v)) {
            errors.push("Please select at least one officer role.");
        }

        if (data.officer_type === "person") {
            if (!data.first) errors.push("First name is required.");
            if (!data.last) errors.push("Last name is required.");
            if (!data.dob) errors.push("Date of birth is required.");
            if (!data.residential.line1) errors.push("Residential address is required.");
            if (!data.residential.town) errors.push("Town is required.");
            if (!data.residential.postcode) errors.push("Postcode is required.");
        }

        if (
            (data.roles.director || data.roles.secretary) &&
            !data.consent
        ) {
            errors.push("Authentication consent is required.");
        }

        if (data.roles.shareholder) {
            if (!data.shares.quantity || Number(data.shares.quantity) <= 0) {
                errors.push("Share quantity is required.");
            }
        }

        if (data.roles.psc) {
            const c = data.noc;
            const hasControl =
                c.company_shares !== "na" ||
                c.company_voting !== "na" ||
                c.company_directors === "1" ||
                c.company_other === "1";

            if (!hasControl) {
                errors.push("At least one Nature of Control must be selected.");
            }
        }

        return errors;
    }

    /* =================================================
       COMPANY RULE
       → 1 PERSON DIRECTOR + 1 SHAREHOLDER
    ================================================= */
    function checkMandatoryCompanyRoles() {
        const officers = getOfficers();

        const hasPersonDirector = officers.some(o =>
            o.officer_type === "person" && o.roles.director
        );

        const hasShareholder = officers.some(o =>
            o.roles.shareholder
        );

        return hasPersonDirector && hasShareholder;
    }

    /* =================================================
       SAVE & CONTINUE BUTTON LOCK
    ================================================= */
    function updateContinueButton() {
        if (!continueBtn) return;

        const allowed = checkMandatoryCompanyRoles();
        continueBtn.disabled = !allowed;
        continueBtn.style.opacity = allowed ? "1" : "0.5";
    }

    /* =================================================
       RENDER + DELETE
    ================================================= */
    function renderList() {
        const officers = getOfficers();
        listBox.innerHTML = "";

        if (!checkMandatoryCompanyRoles()) {
            listBox.insertAdjacentHTML(
                "beforeend",
                `<div style="background:#fff3cd;border:1px solid #ffeeba;padding:8px;border-radius:4px;margin-bottom:10px;">
                    <strong>Required before continuing:</strong>
                    <ul style="margin:6px 0 0 18px;">
                        <li>At least one <b>Person Director</b></li>
                        <li>At least one <b>Shareholder</b></li>
                    </ul>
                </div>`
            );
        }

        if (!officers.length) {
            listBox.insertAdjacentHTML("beforeend", "<em>No officers added yet.</em>");
        } else {
            listBox.insertAdjacentHTML(
                "beforeend",
                officers.map(o => `
                    <div style="padding:10px;border-bottom:1px solid #ddd;">
                        <strong>${o.first || "(Non-person entity)"} ${o.last || ""}</strong>
                        <small> — ${o.officer_type}</small><br>
                        Roles: ${Object.keys(o.roles).filter(r => o.roles[r]).join(", ")}<br>
                        <button type="button"
                                class="button-link-delete delete-officer"
                                data-id="${o.id}">
                            Delete
                        </button>
                    </div>
                `).join("")
            );
        }

        listBox.querySelectorAll(".delete-officer").forEach(btn => {
            btn.addEventListener("click", function () {
                const id = this.dataset.id;
                setOfficers(getOfficers().filter(o => o.id !== id));
                renderList();
            });
        });

        updateContinueButton();
    }

    /* =================================================
       SAVE OFFICER
    ================================================= */
    saveBtn.addEventListener("click", function () {

        const data = collectOfficerData();
        const errors = validateOfficer(data);

        if (errors.length) {
            alert("Please fix the following:\n\n" + errors.join("\n"));
            return;
        }

        const officers = getOfficers();
        officers.push(data);
        setOfficers(officers);

        console.log("✅ Officer saved", data);
        renderList();
    });

    /* =================================================
       INIT
    ================================================= */
    renderList();
    updateContinueButton();

});
