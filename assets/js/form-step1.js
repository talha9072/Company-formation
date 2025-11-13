document.addEventListener("DOMContentLoaded", function () {

    // ELEMENTS
    const businessSelect = document.getElementById("business_activity");
    const sicContainer = document.getElementById("sic-category-container");
    const selectedContainer = document.getElementById("selected_sic_codes");
    const saveButton = document.querySelector(".button.button-primary");

    const companyType = document.getElementById("company_type");
    const jurisdiction = document.getElementById("jurisdiction");

    // STATE
    const MAX_SIC_CODES = 4;
    let sicData = {};
    let selectedCodes = {};
    let nameAvailable = false; // will be set by name checker JS

    // Disable save initially
    disableSave();

    function disableSave() {
        saveButton.disabled = true;
        saveButton.style.background = "#aaa";
        saveButton.style.cursor = "not-allowed";
        saveButton.style.opacity = "0.7";
    }

    function enableSave() {
        saveButton.disabled = false;
        saveButton.style.background = "#4a3b8f";
        saveButton.style.cursor = "pointer";
        saveButton.style.opacity = "1";
    }

    // VALIDATION CONTROLLER
    function validateStep1() {
        const hasSIC = Object.keys(selectedCodes).length > 0;
        const hasType = companyType.value.trim() !== "";
        const hasJurisdiction = jurisdiction.value.trim() !== "";

        if (nameAvailable && hasSIC && hasType && hasJurisdiction) {
            enableSave();
        } else {
            disableSave();
        }
    }

    // LISTEN FOR NAME CHECKER SUCCESS (we add a global event)
    document.addEventListener("companyNameChecked", function(e){
        nameAvailable = e.detail.available === true;
        validateStep1();
    });

    // LOAD SIC JSON
    if (typeof form1Data !== "undefined" && form1Data.jsonUrl) {
        fetch(form1Data.jsonUrl)
            .then(res => res.json())
            .then(data => { sicData = data; })
            .catch(err => {
                console.error("SIC load error:", err);
                sicContainer.innerHTML = "<p style='color:red;'>Error loading SIC data.</p>";
            });
    }

    // CATEGORY CHANGE
    businessSelect.addEventListener("change", function () {
        const category = this.value;
        sicContainer.innerHTML = "";

        if (!category) {
            sicContainer.style.display = "none";
            return;
        }
        renderSicList(category);
    });

    // RENDER SIC LIST
    function renderSicList(category) {
        if (!sicData[category] && category !== "All") {
            sicContainer.style.display = "none";
            return;
        }

        sicContainer.style.display = "block";

        const codes = category === "All"
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

        for (const [code, desc] of Object.entries(codes)) {
            const div = document.createElement("div");
            div.className = "sic-item";
            div.style.cssText = "display:flex;justify-content:space-between;align-items:center;padding:6px 4px;border-bottom:1px solid #eee;";
            div.innerHTML = `
                <span>${code} - ${desc}</span>
                <button type="button" class="add-sic" data-cat="${category}" data-code="${code}" data-label="${desc}"
                    style="background:#4CAF50;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">+</button>
            `;
            list.appendChild(div);
        }

        // FILTER
        sicContainer.querySelector(".sic-filter").addEventListener("input", function () {
            const search = this.value.toLowerCase();
            list.querySelectorAll(".sic-item").forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(search) ? "flex" : "none";
            });
        });

        // ADD SIC
        list.addEventListener("click", function (e) {
            if (!e.target.classList.contains("add-sic")) return;

            const code = e.target.dataset.code;
            const label = e.target.dataset.label;
            const cat = e.target.dataset.cat;

            if (selectedCodes[code]) return;

            if (Object.keys(selectedCodes).length >= MAX_SIC_CODES) {
                showTempMessage("⚠️ You can only select up to 4 SIC codes.", selectedContainer);
                return;
            }

            selectedCodes[code] = { label, cat };
            e.target.disabled = true;
            e.target.style.opacity = "0.5";

            updateSelectedBox();
            validateStep1();
        });
    }

    // UPDATE SELECTED BOX
    function updateSelectedBox() {
        selectedContainer.innerHTML = "";
        const entries = Object.entries(selectedCodes);

        if (entries.length === 0) {
            selectedContainer.textContent = "None Selected";
            validateStep1();
            return;
        }

        for (const [code, obj] of entries) {
            const div = document.createElement("div");
            div.className = "selected-item";
            div.style.cssText = "display:flex;justify-content:space-between;align-items:center;background:#dff0d8;margin:4px 0;padding:6px 10px;border-radius:4px;";
            div.innerHTML = `
                <span>${code} - ${obj.label}</span>
                <button type="button" class="remove-sic" data-code="${code}"
                    style="background:#e74c3c;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">×</button>
            `;
            selectedContainer.appendChild(div);
        }

        // REMOVE SIC HANDLER
        selectedContainer.querySelectorAll(".remove-sic").forEach(btn => {
            btn.addEventListener("click", function () {
                const code = this.dataset.code;
                const cat = selectedCodes[code].cat;

                delete selectedCodes[code];
                updateSelectedBox();
                validateStep1();

                // Reactivate + button
                const addBtn = document.querySelector(`.add-sic[data-cat="${cat}"][data-code="${code}"]`);
                if (addBtn) {
                    addBtn.disabled = false;
                    addBtn.style.opacity = "1";
                }
            });
        });

        validateStep1();
    }

    // WARNING MESSAGE
    function showTempMessage(msg, container) {
        const note = document.createElement("div");
        note.className = "sic-warning";
        note.textContent = msg;
        note.style.cssText = "margin-top:8px;color:#e67e22;font-weight:500;font-size:14px;";
        container.insertAdjacentElement("afterend", note);
        setTimeout(() => note.remove(), 3000);
    }

    // CLICK OUTSIDE HIDES DROPDOWN
    document.addEventListener("click", function (e) {
        if (!businessSelect.contains(e.target) && !sicContainer.contains(e.target)) {
            sicContainer.style.display = "none";
        }
    });

    // SAVE BUTTON CLICK
    saveButton.addEventListener("click", function () {
        if (saveButton.disabled) return;

        const payload = {
            action: "save_step1",
            company_type: companyType.value,
            jurisdiction: jurisdiction.value,
            business_activity: businessSelect.value,
            sic_codes: selectedCodes
        };

        jQuery.post(ncuk_ajax.ajax_url, payload, function (res) {

            if (res.success) {

                // Unlock Step 2
                const step2 = document.querySelector('.sub-tabs li[data-step="2"]');
                step2.classList.remove("disabled");

                // Auto-click Step 2
                step2.click();
            }
        });
    });

});
