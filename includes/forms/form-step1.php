<div class="step-form-wrapper">
  <h3 class="form-title">Particulars</h3>

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
          <option value="Manufacturing">C: Manufacturing</option>
          <option value="Technology">Technology</option>
          <option value="Retail">Retail</option>
          <option value="Finance">Finance</option>
          <option value="Construction">Construction</option>
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

  const sicData = {
    "Manufacturing": {
      "10110": "Processing and preserving of meat",
      "10120": "Processing and preserving of poultry meat",
      "10130": "Production of meat and poultry meat products",
      "10200": "Processing and preserving of fish, crustaceans and molluscs",
      "10310": "Processing and preserving of potatoes",
      "10320": "Manufacture of fruit and vegetable juice",
      "10390": "Other processing and preserving of fruit and vegetables"
    },
    "Technology": {
      "62010": "Computer programming activities",
      "62020": "Computer consultancy activities",
      "62030": "Computer facilities management activities",
      "63110": "Data processing and hosting activities"
    },
    "Retail": {
      "47110": "Retail sale in non-specialised stores",
      "47210": "Retail sale of food in specialised stores",
      "47410": "Retail sale of computers and software"
    },
    "Finance": {
      "64110": "Central banking",
      "64205": "Holding companies",
      "64999": "Financial intermediation n.e.c."
    },
    "Construction": {
      "41201": "Construction of commercial buildings",
      "41202": "Construction of domestic buildings",
      "42990": "Civil engineering projects n.e.c.",
      "43999": "Other construction activities n.e.c."
    }
  };

  let selectedCodes = {};

  // Load SIC list when category changes
  businessSelect.addEventListener("change", function () {
    const category = this.value;
    sicContainer.innerHTML = "";
    if (!category) {
      sicContainer.style.display = "none";
      return;
    }
    renderSicList(category);
  });

  // Render SIC list
  function renderSicList(category) {
    sicContainer.style.display = "block";
    const codes = sicData[category];
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

    list.addEventListener("click", function (e) {
      if (e.target.classList.contains("add-sic")) {
        const code = e.target.dataset.code;
        const label = e.target.dataset.label;
        const cat = e.target.dataset.cat;

        if (selectedCodes[code]) return;
        selectedCodes[code] = { label, cat };
        e.target.disabled = true;
        e.target.style.opacity = "0.5";
        updateSelectedBox();
      }
    });
  }

  // Update selected codes
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

  // Hide list if clicked outside
  document.addEventListener("click", function (e) {
    if (!businessSelect.contains(e.target) && !sicContainer.contains(e.target)) {
      sicContainer.style.display = "none";
    }
  });
});
</script>
