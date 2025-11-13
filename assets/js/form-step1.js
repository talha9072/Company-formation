document.addEventListener("DOMContentLoaded", function () {
  initStep1();
  document.addEventListener("step1Loaded", function () { initStep1(); });
});

function initStep1() {
  const businessSelect = document.getElementById("business_activity");
  if (!businessSelect) return;

  console.log("✅ Step-1 Initialized");

  const sicContainer = document.getElementById("sic-category-container");
  const selectedContainer = document.getElementById("selected_sic_codes");
  const saveButton = document.getElementById("step1-save");
  const companyType = document.getElementById("company_type");
  const jurisdiction = document.getElementById("jurisdiction");
  const companyNameInput = document.getElementById("search");

  const MAX_SIC_CODES = 4;
  let sicData = {};
  let selectedCodes = {};
  let nameAvailable = false;
  const LS_KEY = "companyformation_step1";

  disableSave();
  function disableSave() { saveButton.disabled = true; saveButton.style.opacity = "0.6"; }
  function enableSave() { saveButton.disabled = false; saveButton.style.opacity = "1"; }

  function validateStep1() {
    const okSic = Object.keys(selectedCodes).length > 0;
    const okType = companyType.value.trim() !== "";
    const okJur = jurisdiction.value.trim() !== "";
    if (nameAvailable && okSic && okType && okJur) enableSave();
    else disableSave();
  }

  // Listen for company name check event
  document.addEventListener("companyNameChecked", function (e) {
    nameAvailable = e.detail.available === true;
    validateStep1();
  });

  // Load SIC JSON
  if (typeof form1Data !== "undefined" && form1Data.jsonUrl) {
    fetch(form1Data.jsonUrl)
      .then(res => res.json())
      .then(data => {
        sicData = data;
        restoreLocalStorage();
        restoreFromDB();
      });
  }

  function restoreLocalStorage() {
    const saved = localStorage.getItem(LS_KEY);
    if (!saved) return;
    const d = JSON.parse(saved);
    if (d.company_name) companyNameInput.value = d.company_name;
    if (d.company_type) companyType.value = d.company_type;
    if (d.jurisdiction) jurisdiction.value = d.jurisdiction;
    if (d.business_activity) businessSelect.value = d.business_activity;
    if (d.sic_codes) selectedCodes = d.sic_codes;
    updateSelectedBox();
  }

  function restoreFromDB() {
    jQuery.post(ncuk_ajax.ajax_url, { action: "ncuk_load_step1" }, function (res) {
      if (!res.success || !res.data) return;
      const data = res.data;
      if (!data) return;

      // Fill fields
      if (data.company_name) companyNameInput.value = data.company_name;
      if (data.company_type) companyType.value = data.company_type;
      if (data.jurisdiction) jurisdiction.value = data.jurisdiction;
      if (data.business_activity) businessSelect.value = data.business_activity;
      if (data.sic_codes) selectedCodes = data.sic_codes;

      updateSelectedBox();

      // auto unlock step2
      if (data.company_name && data.company_type && data.jurisdiction && data.business_activity) {
        document.querySelector('.sub-tabs li[data-step="2"]').classList.remove("disabled");
      }
    });
  }

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

  // --- Save & Continue ---
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
      if (res.success) {
        saveToLocal();
        const s2 = document.querySelector('.sub-tabs li[data-step="2"]');
        if (s2) {
          s2.classList.remove("disabled");
          s2.click();
        }
      }
    });
  });

  function updateSelectedBox() {
    selectedContainer.innerHTML = "";
    const entries = Object.entries(selectedCodes);
    if (entries.length === 0) { selectedContainer.textContent = "None Selected"; return; }
    entries.forEach(([code, obj]) => {
      const div = document.createElement("div");
      div.className = "selected-item";
      div.innerHTML = `
        <span>${code} - ${obj.label}</span>
        <button type="button" class="remove-sic" data-code="${code}">×</button>
      `;
      selectedContainer.appendChild(div);
    });
  }
}
