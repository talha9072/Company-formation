/*************************************************************
 STEP 1 — DB ONLY MODE (NO LOCAL STORAGE, NO AJAX FORM LOAD)
*************************************************************/
document.addEventListener("DOMContentLoaded", initStep1);

function initStep1() {

  const businessSelect = document.getElementById("business_activity");
  if (!businessSelect) return; // Step 1 not visible

  console.log("🔥 Step-1 JS Initialized (DB only, NON-AJAX)");


  /* -----------------------------------------------------------
     ELEMENTS
  ----------------------------------------------------------- */
  const companyNameInput = document.querySelector('#step1form input[name="company_name"]');
  const companyType = document.getElementById("company_type");
  const jurisdiction = document.getElementById("jurisdiction");
  const selectedContainer = document.getElementById("selected_sic_codes");
  const saveButton = document.getElementById("step1-save");

  if (!companyNameInput) {
    console.error("❌ Hidden company_name field missing!");
    return;
  }


  /* -----------------------------------------------------------
     INTERNAL STATE (shared with inline SIC script)
  ----------------------------------------------------------- */
  let selectedCodes = {};
  let nameAvailable = false;

  window.step1SelectedCodes = selectedCodes;


  /* -----------------------------------------------------------
     BUTTON CONTROL
  ----------------------------------------------------------- */
  disableSave();

  function disableSave() {
    saveButton.disabled = true;
    saveButton.style.opacity = "0.5";
  }

  function enableSave() {
    saveButton.disabled = false;
    saveButton.style.opacity = "1";
  }


  /* -----------------------------------------------------------
     VALIDATION
  ----------------------------------------------------------- */
  function validateStep1() {

    const okName = companyNameInput.value.trim() !== "";
    const okNameAvailable = nameAvailable === true;
    const okType = companyType.value !== "";
    const okJur = jurisdiction.value !== "";
    const okSic = Object.keys(selectedCodes).length > 0;

    if (okName && okNameAvailable && okType && okJur && okSic) {
      enableSave();
    } else {
      disableSave();
    }
  }


  /* -----------------------------------------------------------
     EVENT FROM NAME CHECKER
  ----------------------------------------------------------- */
  document.addEventListener("companyNameChecked", function (e) {

    nameAvailable = e.detail.available === true;

    if (!nameAvailable) {
      companyNameInput.value = "";
    }

    validateStep1();
  });


  /* -----------------------------------------------------------
     EVENT FROM INLINE SIC SCRIPT
  ----------------------------------------------------------- */
  document.addEventListener("sicUpdated", function () {
    selectedCodes = window.step1SelectedCodes;
    updateSelectedBox();
    validateStep1();
  });


  /* -----------------------------------------------------------
     RESTORE DATA FROM DB (ONLY ONE TRUE SOURCE)
  ----------------------------------------------------------- */
  restoreFromDB();

  function restoreFromDB() {

    jQuery.post(ncuk_ajax.ajax_url, { action: "ncuk_load_step1" }, function (res) {

      if (!res.success || !res.data) return;

      const d = res.data;

      console.log("🔄 Restored from DB:", d);

      if (d.company_name) {
        companyNameInput.value = d.company_name;
        nameAvailable = true;
      }

      if (d.company_type) companyType.value = d.company_type;
      if (d.jurisdiction) jurisdiction.value = d.jurisdiction;
      if (d.business_activity) businessSelect.value = d.business_activity;

      if (d.sic_codes) {
        selectedCodes = d.sic_codes;
        window.step1SelectedCodes = selectedCodes;
      }

      updateSelectedBox();
      validateStep1();

      // Unlock step 2 if restored data is complete
      if (d.company_name && d.company_type && d.jurisdiction && d.business_activity) {
        const s2 = document.querySelector('.step-item[data-step="2"]');
        if (s2) s2.classList.remove("disabled");
      }
    });
  }


  /* -----------------------------------------------------------
     SAVE IN DB ONLY (NO LOCAL STORAGE)
  ----------------------------------------------------------- */
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

      console.log("💾 DB Save Response:", res);

      if (res.success) {

        // Unlock step 2
        const step2 = document.querySelector('.step-item[data-step="2"]');
        if (step2) {
          step2.classList.remove("disabled");
          step2.click(); // Go to step 2
        }

      } else {
        console.error("❌ ERROR saving Step-1:", res);
      }
    });
  });


  /* -----------------------------------------------------------
     RENDER SELECTED SIC CODES
  ----------------------------------------------------------- */
  function updateSelectedBox() {
    selectedContainer.innerHTML = "";

    const entries = Object.entries(selectedCodes);

    if (entries.length === 0) {
      selectedContainer.textContent = "None Selected";
      return;
    }

    entries.forEach(([code, obj]) => {

      const div = document.createElement("div");
      div.className = "selected-item";
      div.style.cssText =
        "display:flex;justify-content:space-between;align-items:center;" +
        "margin:4px 0;padding:6px 10px;background:#dff0d8;border-radius:4px;";

      div.innerHTML = `
        <span>${code} - ${obj.label}</span>
        <button type="button" class="remove-sic" data-code="${code}">×</button>
      `;

      selectedContainer.appendChild(div);

      div.querySelector(".remove-sic").addEventListener("click", function () {
        delete selectedCodes[code];
        window.step1SelectedCodes = selectedCodes;
        document.dispatchEvent(new Event("sicUpdated"));
      });
    });
  }
}
