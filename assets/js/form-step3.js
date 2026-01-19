document.addEventListener("DOMContentLoaded", function () {

    console.log("🔥 Step-3 JS Loaded (Fixed)");

    const officerForm = document.getElementById("officer-person-form");
    if (!officerForm) return;

    const radios = document.querySelectorAll('input[name="officer_type"]');

    /* ----------------------------------------------
       ENSURE HIDDEN INPUT FOR TYPE
    -----------------------------------------------*/
    let hiddenTypeInput = officerForm.querySelector('input[name="officer_type_hidden"]');
    if (!hiddenTypeInput) {
        hiddenTypeInput = document.createElement("input");
        hiddenTypeInput.type = "hidden";
        hiddenTypeInput.name = "officer_type_hidden";
        officerForm.appendChild(hiddenTypeInput);
    }

    /* ----------------------------------------------
       FUNCTION: SHOW FORM
    -----------------------------------------------*/
    function showOfficerForm(type) {

        // show form
        officerForm.style.display = "block";

        // set type
        hiddenTypeInput.value = type;

        console.log("👤 Officer type:", type);

        // scroll to form
        officerForm.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    /* ----------------------------------------------
       RADIO CHANGE HANDLER
    -----------------------------------------------*/
    radios.forEach(radio => {
        radio.addEventListener("change", function () {

            // reset old data
            officerForm.reset?.();

            showOfficerForm(this.value);
        });
    });

    /* ----------------------------------------------
       AUTO SHOW ON PAGE LOAD (DEFAULT RADIO)
    -----------------------------------------------*/
    const defaultRadio = document.querySelector('input[name="officer_type"]:checked');
    if (defaultRadio) {
        showOfficerForm(defaultRadio.value);
    }

});


document.addEventListener("DOMContentLoaded", function () {

    /* =================================================
       SHARED HELPERS
    ================================================= */

    function setupInternalLogic({ shares, voting, directors, other }) {
        if (!shares || !voting || !directors || !other) return;

        const rowDirectors = directors.closest("tr");
        const rowOther = other.closest("tr");

        function updateLogic() {
            const sharesVal = shares.value;
            const votingVal = voting.value;
            const directorsVal = directors.value;

            // Rule 1: Shares OR Voting != N/A → hide Directors
            if (sharesVal !== "na" || votingVal !== "na") {
                rowDirectors.style.display = "none";
                directors.value = "0";
            } else {
                rowDirectors.style.display = "";
            }

            // Rule 2: Directors = Yes → hide Other
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
                internalSetup && internalSetup();
            } else {
                section.style.display = "none";
                resetFields.forEach(f => f && (f.value = f.dataset.reset));
            }
        }

        yes.addEventListener("change", toggle);
        no.addEventListener("change", toggle);

        toggle();
    }

    /* =================================================
       COMPANY (Always Visible)
    ================================================= */

    setupInternalLogic({
        shares: document.querySelector('[name="psc_company_shares"]'),
        voting: document.querySelector('[name="psc_company_voting"]'),
        directors: document.querySelector('[name="psc_company_directors"]'),
        other: document.querySelector('[name="psc_company_other"]')
    });

    /* =================================================
       FIRM
    ================================================= */

    const firmSection = document.getElementById("psc-firm-section");

    const firmFields = {
        shares: firmSection?.querySelector('[name="psc_firm_shares"]'),
        voting: firmSection?.querySelector('[name="psc_firm_voting"]'),
        directors: firmSection?.querySelector('[name="psc_firm_directors"]'),
        other: firmSection?.querySelector('[name="psc_firm_other"]')
    };

    // default reset values
    Object.values(firmFields).forEach(f => {
        if (!f) return;
        f.dataset.reset = f.name.includes("shares") || f.name.includes("voting") ? "na" : "0";
    });

    setupToggleSection({
        yes: document.querySelector('input[name="psc_as_firm"][value="1"]'),
        no: document.querySelector('input[name="psc_as_firm"][value="0"]'),
        section: firmSection,
        resetFields: Object.values(firmFields),
        internalSetup: () => setupInternalLogic(firmFields)
    });

    /* =================================================
       TRUST
    ================================================= */

    const trustSection = document.getElementById("psc-trust-section");

    const trustFields = {
        shares: trustSection?.querySelector('[name="psc_trust_shares"]'),
        voting: trustSection?.querySelector('[name="psc_trust_voting"]'),
        directors: trustSection?.querySelector('[name="psc_trust_directors"]'),
        other: trustSection?.querySelector('[name="psc_trust_other"]')
    };

    Object.values(trustFields).forEach(f => {
        if (!f) return;
        f.dataset.reset = f.name.includes("shares") || f.name.includes("voting") ? "na" : "0";
    });

    setupToggleSection({
        yes: document.querySelector('input[name="psc_as_trust"][value="1"]'),
        no: document.querySelector('input[name="psc_as_trust"][value="0"]'),
        section: trustSection,
        resetFields: Object.values(trustFields),
        internalSetup: () => setupInternalLogic(trustFields)
    });

});

