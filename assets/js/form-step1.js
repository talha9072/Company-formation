document.addEventListener("DOMContentLoaded", function () {

    initStep1(); // first time

    // When step1 HTML reloads via AJAX
    document.addEventListener("step1Loaded", function () {
        initStep1();
    });

});

/* ---------------------------------------------------------
   MAIN INITIALIZER
--------------------------------------------------------- */
function initStep1() {

    if (!document.getElementById("business_activity")) return;

    console.log("Step-1 Initialised");

    // ELEMENTS
    const businessSelect = document.getElementById("business_activity");
    const sicContainer = document.getElementById("sic-category-container");
    const selectedContainer = document.getElementById("selected_sic_codes");
    const saveButton = document.querySelector(".button.button-primary");
    const companyType = document.getElementById("company_type");
    const jurisdiction = document.getElementById("jurisdiction");
    const companyNameInput = document.getElementById("search");

    // STATE
    const MAX_SIC_CODES = 4;
    let sicData = {};
    let selectedCodes = {};
    let nameAvailable = false;

    const LS_KEY = "companyformation_step1";

    disableSave();

    function disableSave() {
        saveButton.disabled = true;
        saveButton.style.opacity = "0.6";
        saveButton.style.cursor = "not-allowed";
    }

    function enableSave() {
        saveButton.disabled = false;
        saveButton.style.opacity = "1";
        saveButton.style.cursor = "pointer";
    }

    /* -----------------------------------------------------
       VALIDATION
    ----------------------------------------------------- */
    function validateStep1() {
        const okSic = Object.keys(selectedCodes).length > 0;
        const okType = companyType.value.trim() !== "";
        const okJur = jurisdiction.value.trim() !== "";

        if (nameAvailable && okSic && okType && okJur) {
            enableSave();
        } else {
            disableSave();
        }
    }

    /* -----------------------------------------------------
       NAME CHECK EVENT HANDLER
    ----------------------------------------------------- */
    document.addEventListener("companyNameChecked", function (e) {
        nameAvailable = e.detail.available === true;
        validateStep1();
    });

    /* -----------------------------------------------------
       LOAD SIC JSON → RESTORE LOCAL → RESTORE DB
    ----------------------------------------------------- */
    if (typeof form1Data !== "undefined" && form1Data.jsonUrl) {
        fetch(form1Data.jsonUrl)
            .then(res => res.json())
            .then(data => {
                sicData = data;

                restoreLocalStorage();
                restoreCompanyNameFromDB();
            });
    }

    /* -----------------------------------------------------
       RESTORE LOCAL STORAGE
    ----------------------------------------------------- */
    function restoreLocalStorage() {
        const saved = localStorage.getItem(LS_KEY);
        if (!saved) return;

        const data = JSON.parse(saved);

        /* Restore Company Name & Auto-run Checker */
        if (data.company_name) {
            companyNameInput.value = data.company_name;

            jQuery.post(ncuk_ajax.ajax_url, {
                action: "company_name_checker",
                search: data.company_name
            }, function (response) {

                if (response?.data?.html) {
                    jQuery("#responseContainer").html(response.data.html);

                    document.dispatchEvent(
                        new CustomEvent("companyNameChecked", {
                            detail: { available: response.data.available }
                        })
                    );
                }
            });
        }

        if (data.company_type) companyType.value = data.company_type;
        if (data.jurisdiction) jurisdiction.value = data.jurisdiction;

        if (data.business_activity) {
            businessSelect.value = data.business_activity;
            renderSicList(data.business_activity);
        }

        if (data.sic_codes) {
            selectedCodes = data.sic_codes;
            updateSelectedBox();
        }

        validateStep1();
    }

    /* -----------------------------------------------------
       RESTORE COMPANY NAME FROM DB
    ----------------------------------------------------- */
    function restoreCompanyNameFromDB() {

        jQuery.post(ncuk_ajax.ajax_url, { action: "ncuk_load_step1" }, function (res) {

            if (!res.success || !res.data) return;

            const data = res.data;

            if (data.company_name) {
                companyNameInput.value = data.company_name;

                jQuery.post(
                    ncuk_ajax.ajax_url,
                    { action: "company_name_checker", search: data.company_name },
                    function (response) {

                        if (response?.data?.html) {
                            jQuery("#responseContainer").html(response.data.html);

                            document.dispatchEvent(
                                new CustomEvent("companyNameChecked", {
                                    detail: { available: response.data.available }
                                })
                            );
                        }
                    }
                );
            }

            // 🔥 Auto unlock step 2 if DB shows completed data
            if (
                data.company_name &&
                data.company_type &&
                data.jurisdiction &&
                data.business_activity
            ) {
                const nextStep = document.querySelector('.sub-tabs li[data-step="2"]');
                if (nextStep) nextStep.classList.remove("disabled");
            }

        });
    }

    /* -----------------------------------------------------
       SAVE TO LOCALSTORAGE
    ----------------------------------------------------- */
    function saveToLocal() {
        const payload = {
            company_name: companyNameInput.value.trim(),
            company_type: companyType.value,
            jurisdiction: jurisdiction.value,
            business_activity: businessSelect.value,
            sic_codes: selectedCodes
        };
        localStorage.setItem(LS_KEY, JSON.stringify(payload));
    }

    /* -----------------------------------------------------
       CATEGORY CHANGE
    ----------------------------------------------------- */
    businessSelect.addEventListener("change", function () {
        const category = this.value;
        sicContainer.innerHTML = "";

        if (!category) {
            sicContainer.style.display = "none";
            return;
        }

        renderSicList(category);
    });

    /* -----------------------------------------------------
       RENDER SIC LIST
    ----------------------------------------------------- */
    function renderSicList(category) {

        if (!sicData[category] && category !== "All") {
            sicContainer.style.display = "none";
            return;
        }

        sicContainer.style.display = "block";

        const codes =
            category === "All"
                ? Object.assign({}, ...Object.values(sicData))
                : sicData[category];

        sicContainer.innerHTML = `
            <div style="padding:10px;">
                <input type="text" class="sic-filter" placeholder="Filter..."
                    style="width:100%;padding:6px;border:1px solid #ccc;border-radius:4px;margin-bottom:8px;">
                <div class="sic-list"></div>
            </div>
        `;

        const list = sicContainer.querySelector(".sic-list");

        Object.entries(codes).forEach(([code, desc]) => {
            const div = document.createElement("div");
            div.className = "sic-item";
            div.style.cssText =
                "display:flex;justify-content:space-between;align-items:center;padding:6px 4px;border-bottom:1px solid #eee;";

            div.innerHTML = `
                <span>${code} - ${desc}</span>
                <button type="button" class="add-sic" data-cat="${category}" data-code="${code}" data-label="${desc}"
                    style="background:#4CAF50;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">+</button>
            `;

            list.appendChild(div);

            if (selectedCodes[code]) {
                const btn = div.querySelector(".add-sic");
                btn.disabled = true;
                btn.style.opacity = "0.5";
            }
        });

        // Filter
        sicContainer.querySelector(".sic-filter").addEventListener("input", function () {
            const search = this.value.toLowerCase();
            list.querySelectorAll(".sic-item").forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(search)
                    ? "flex"
                    : "none";
            });
        });

        // + buttons
        list.addEventListener("click", function (e) {
            if (!e.target.classList.contains("add-sic")) return;

            const code = e.target.dataset.code;
            const label = e.target.dataset.label;
            const cat = e.target.dataset.cat;

            selectedCodes[code] = { label, cat };
            saveToLocal();

            e.target.disabled = true;
            e.target.style.opacity = "0.5";

            updateSelectedBox();
            validateStep1();
        });

        // 💥 INSURE RESTORED SELECTED SIC SHOWS CORRECTLY
        updateSelectedBox();
    }

    /* -----------------------------------------------------
       UPDATE SELECTED SIC BOX
    ----------------------------------------------------- */
    function updateSelectedBox() {
        selectedContainer.innerHTML = "";
        const entries = Object.entries(selectedCodes);

        if (entries.length === 0) {
            selectedContainer.textContent = "None Selected";
            validateStep1();
            return;
        }

        entries.forEach(([code, obj]) => {
            const div = document.createElement("div");
            div.className = "selected-item";
            div.style.cssText =
                "display:flex;justify-content:space-between;align-items:center;background:#dff0d8;margin:4px 0;padding:6px 10px;border-radius:4px;";

            div.innerHTML = `
                <span>${code} - ${obj.label}</span>
                <button type="button" class="remove-sic" data-code="${code}"
                    style="background:#e74c3c;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">×</button>
            `;

            selectedContainer.appendChild(div);
        });

        selectedContainer.querySelectorAll(".remove-sic").forEach(btn => {
            btn.addEventListener("click", function () {
                const code = this.dataset.code;
                const cat = selectedCodes[code].cat;

                delete selectedCodes[code];
                saveToLocal();

                updateSelectedBox();
                validateStep1();

                const addBtn = document.querySelector(`.add-sic[data-cat="${cat}"][data-code="${code}"]`);
                if (addBtn) {
                    addBtn.disabled = false;
                    addBtn.style.opacity = "1";
                }
            });
        });

        validateStep1();
    }

    /* -----------------------------------------------------
       SAVE BUTTON → DB SAVE → GO TO STEP 2
    ----------------------------------------------------- */
    saveButton.addEventListener("click", function () {

        const payload = {
            action: "ncuk_save_step1",
            company_name: companyNameInput.value.trim(),
            company_type: companyType.value,
            jurisdiction: jurisdiction.value,
            business_activity: businessSelect.value,
            sic_codes: selectedCodes
        };

        jQuery.post(ncuk_ajax.ajax_url, payload, function (res) {

            saveToLocal();

            const step2 = document.querySelector('.sub-tabs li[data-step="2"]');
            step2.classList.remove("disabled");
            step2.click();
        });
    });


    /* -----------------------------------------------------
       AUTO-UNLOCK STEP 2 FROM LOCAL STORAGE ALSO
    ----------------------------------------------------- */
    (function autoUnlockSteps() {
        const saved = localStorage.getItem("companyformation_step1");
        if (!saved) return;

        const d = JSON.parse(saved);

        if (
            d.company_name &&
            d.company_type &&
            d.jurisdiction &&
            d.business_activity
        ) {
            const s2 = document.querySelector('.sub-tabs li[data-step="2"]');
            if (s2) s2.classList.remove("disabled");
        }
    })();

}
