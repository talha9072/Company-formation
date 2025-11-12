<div class="step-form-wrapper">

  <form id="step1form">
    <div class="step-grid" style="display:flex;flex-wrap:wrap;gap:20px;">
      <!-- Type of Company -->
      <div class="form-group" style="flex:1 1 48%;">
        <label for="company_type">Type of Company 
          <span class="info-icon" title="Select the type of company you want to register.">ℹ️</span>
        </label>
        <select id="company_type" name="company_type" style="width:100%;">
          <option value="Limited By Shares">Limited By Shares</option>
          <option value="Limited By Guarantee">Limited By Guarantee</option>
          <option value="LLP">LLP</option>
          <option value="PLC">PLC</option>
        </select>
      </div>

      <!-- Jurisdiction -->
      <div class="form-group" style="flex:1 1 48%;">
        <label for="jurisdiction">Jurisdiction 
          <span class="info-icon" title="Choose your registration region.">ℹ️</span>
        </label>
        <select id="jurisdiction" name="jurisdiction" style="width:100%;">
          <option value="England & Wales">England & Wales</option>
          <option value="Scotland">Scotland</option>
          <option value="Northern Ireland">Northern Ireland</option>
        </select>
      </div>

      <!-- Business Activity + SIC Codes -->
      <div class="form-group" style="flex:1 1 48%;position:relative;">
        <label for="business_activity">
          What are your business activities
          <span class="info-icon" title="Select your primary business activity.">ℹ️</span>
        </label>
        <select id="business_activity" name="business_activity" style="width:100%;padding:8px;border:1px solid #ccc;">
  <option value="">Select a category:</option>
  <option value="All">All</option>
  <option value="A">A: Agriculture, Forestry and Fishing</option>
  <option value="B">B: Mining and Quarrying</option>
  <option value="C">C: Manufacturing</option>
  <option value="D">D: Electricity, gas, steam and air conditioning supply</option>
  <option value="E">E: Water supply, sewerage, waste management and remediation activities</option>
  <option value="F">F: Construction</option>
  <option value="G">G: Wholesale and retail trade; repair of motor vehicles and motorcycles</option>
  <option value="H">H: Transportation and storage</option>
  <option value="I">I: Accommodation and food service activities</option>
  <option value="J">J: Information and communication</option>
  <option value="K">K: Financial and insurance activities</option>
  <option value="L">L: Real estate activities</option>
  <option value="M">M: Professional, scientific and technical activities</option>
  <option value="N">N: Administrative and support service activities</option>
  <option value="O">O: Public administration and defence; compulsory social security</option>
  <option value="P">P: Education</option>
  <option value="Q">Q: Human health and social work activities</option>
  <option value="R">R: Arts, entertainment and recreation</option>
  <option value="S">S: Other service activities</option>
  <option value="T">T: Activities of households as employers; undifferentiated goods and services producing activities of households for own use</option>
  <option value="U">U: Activities of extraterritorial organisations and bodies</option>
</select>

        <!-- SIC Codes dropdown -->
        <div id="sic-category-container"
          style="width:100%;border:1px solid #ccc;border-top:none;border-radius:0 0 6px 6px;max-height:260px;overflow-y:auto;display:none;background:#fff;position:relative;margin-top:-1px;z-index:5;">
        </div>
      </div>

      <!-- Selected SIC Codes -->
      <div class="form-group" style="flex:1 1 48%;">
        <label for="sic_codes">
          Selected SIC codes
          <span class="info-icon" title="SIC codes represent your company’s type of business.">ℹ️</span>
        </label>
        <div id="selected_sic_codes"
          style="border:1px solid #ccc;border-radius:6px;padding:10px;min-height:45px;background:#f9f9f9;">
          None Selected
        </div>
      </div>
    </div>

    <!-- Save Button Aligned to End -->
    <div class="form-footer" style="display:flex;justify-content:flex-end;margin-top:20px;">
      <button type="button" class="button button-primary" 
        style="background:#4a3b8f;color:white;border:none;padding:10px 25px;border-radius:6px;cursor:pointer;">
        Save & Continue
      </button>
    </div>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const businessSelect = document.getElementById("business_activity");
  const sicContainer = document.getElementById("sic-category-container");
  const selectedContainer = document.getElementById("selected_sic_codes");

  const MAX_SIC_CODES = 4;
  let sicData = {};
  let selectedCodes = {};

  // ✅ Load JSON file dynamically
  fetch("<?php echo NCUK_URL; ?>includes/forms/form-include/sic-data.json")
    .then((res) => res.json())
    .then((data) => {
      sicData = data;
    })
    .catch((err) => {
      console.error("Failed to load SIC data:", err);
      sicContainer.innerHTML = "<p style='color:red;'>Error loading SIC data.</p>";
    });

  // When user selects a business category
  businessSelect.addEventListener("change", function () {
    const category = this.value;
    sicContainer.innerHTML = "";
    if (!category) {
      sicContainer.style.display = "none";
      return;
    }
    renderSicList(category);
  });

  // Render list below dropdown
  function renderSicList(category) {
    if (!sicData[category]) {
      sicContainer.style.display = "none";
      return;
    }

    sicContainer.style.display = "block";
    const codes = category === "All"
  ? Object.assign({}, ...Object.values(sicData)) // merge all categories
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
      const item = document.createElement("div");
      item.className = "sic-item";
      item.style.cssText =
        "display:flex;justify-content:space-between;align-items:center;padding:6px 4px;border-bottom:1px solid #eee;";
      item.innerHTML = `
        <span>${code} - ${desc}</span>
        <button type="button" class="add-sic" data-cat="${category}" data-code="${code}" data-label="${desc}"
          style="background:#4CAF50;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">+</button>
      `;
      list.appendChild(item);
    }

    const filterInput = sicContainer.querySelector(".sic-filter");
    filterInput.addEventListener("input", function () {
      const term = this.value.toLowerCase();
      list.querySelectorAll(".sic-item").forEach((item) => {
        item.style.display = item.textContent.toLowerCase().includes(term)
          ? "flex"
          : "none";
      });
    });

    // Add SIC codes
    list.addEventListener("click", function (e) {
      if (e.target.classList.contains("add-sic")) {
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
      }
    });
  }

  // Update selected codes box
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
        "display:flex;justify-content:space-between;align-items:center;background:#dff0d8;margin:4px 0;padding:6px 10px;border-radius:4px;";
      div.innerHTML = `
        <span>${code} - ${obj.label}</span>
        <button type="button" class="remove-sic" data-code="${code}"
          style="background:#e74c3c;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">×</button>
      `;
      selectedContainer.appendChild(div);
    });

    selectedContainer.querySelectorAll(".remove-sic").forEach((btn) => {
      btn.addEventListener("click", function () {
        const code = this.dataset.code;
        const cat = selectedCodes[code]?.cat;
        delete selectedCodes[code];
        updateSelectedBox();

        const addBtn = document.querySelector(
          `.add-sic[data-cat="${cat}"][data-code="${code}"]`
        );
        if (addBtn) {
          addBtn.disabled = false;
          addBtn.style.opacity = "1";
        }
      });
    });
  }

  // Temporary inline warning
  function showTempMessage(msg, container) {
    const existing = document.querySelector(".sic-warning");
    if (existing) existing.remove();

    const note = document.createElement("div");
    note.className = "sic-warning";
    note.textContent = msg;
    note.style.cssText =
      "margin-top:8px;color:#e67e22;font-weight:500;font-size:14px;";
    container.insertAdjacentElement("afterend", note);

    setTimeout(() => note.remove(), 3000);
  }

  // Hide dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (!businessSelect.contains(e.target) && !sicContainer.contains(e.target)) {
      sicContainer.style.display = "none";
    }
  });
});
</script>

