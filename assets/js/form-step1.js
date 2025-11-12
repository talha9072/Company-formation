
document.addEventListener("DOMContentLoaded", function () {
  const businessSelect = document.getElementById("business_activity");
  const sicContainer = document.getElementById("sic-category-container");
  const selectedContainer = document.getElementById("selected_sic_codes");
  const saveButton = document.querySelector(".button.button-primary");

  const MAX_SIC_CODES = 4;
  let sicData = {};
  let selectedCodes = {};

  //  Disable Save button initially
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

  function validateSaveState() {
    if (Object.keys(selectedCodes).length > 0) {
      enableSave();
    } else {
      disableSave();
    }
  }

  //  Load JSON dynamically (URL injected from PHP)
  if (typeof form1Data !== "undefined" && form1Data.jsonUrl) {
    console.log("📦 Fetching JSON:", form1Data.jsonUrl);
    fetch(form1Data.jsonUrl)
      .then((res) => res.json())
      .then((data) => {
        sicData = data;
      })
      .catch((err) => {
        console.error("❌ Failed to load SIC data:", err);
        sicContainer.innerHTML =
          "<p style='color:red;'>Error loading SIC data.</p>";
      });
  } else {
    console.warn("⚠️ form1Data.jsonUrl missing");
  }

  // Category selection
  businessSelect.addEventListener("change", function () {
    const category = this.value;
    sicContainer.innerHTML = "";
    if (!category) {
      sicContainer.style.display = "none";
      return;
    }
    renderSicList(category);
  });

  // Render list
  function renderSicList(category) {
    if (!sicData[category]) {
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

    // Filter input
    const filterInput = sicContainer.querySelector(".sic-filter");
    filterInput.addEventListener("input", function () {
      const term = this.value.toLowerCase();
      list.querySelectorAll(".sic-item").forEach((item) => {
        item.style.display = item.textContent.toLowerCase().includes(term)
          ? "flex"
          : "none";
      });
    });

    // Add SIC code
    list.addEventListener("click", function (e) {
      if (e.target.classList.contains("add-sic")) {
        const code = e.target.dataset.code;
        const label = e.target.dataset.label;
        const cat = e.target.dataset.cat;

        if (selectedCodes[code]) return;
        if (Object.keys(selectedCodes).length >= MAX_SIC_CODES) {
          showTempMessage(
            "⚠️ You can only select up to 4 SIC codes.",
            selectedContainer
          );
          return;
        }

        selectedCodes[code] = { label, cat };
        e.target.disabled = true;
        e.target.style.opacity = "0.5";
        updateSelectedBox();
        validateSaveState(); // ✅ Update button state
      }
    });
  }

  // Update selected box
  function updateSelectedBox() {
    selectedContainer.innerHTML = "";
    const entries = Object.entries(selectedCodes);

    if (entries.length === 0) {
      selectedContainer.textContent = "None Selected";
      validateSaveState();
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

    // Remove SIC
    selectedContainer.querySelectorAll(".remove-sic").forEach((btn) => {
      btn.addEventListener("click", function () {
        const code = this.dataset.code;
        const cat = selectedCodes[code]?.cat;
        delete selectedCodes[code];
        updateSelectedBox();
        validateSaveState(); // ✅ Update button state

        const addBtn = document.querySelector(
          `.add-sic[data-cat="${cat}"][data-code="${code}"]`
        );
        if (addBtn) {
          addBtn.disabled = false;
          addBtn.style.opacity = "1";
        }
      });
    });

    validateSaveState();
  }

  // Inline message
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

  // Hide dropdown
  document.addEventListener("click", function (e) {
    if (!businessSelect.contains(e.target) && !sicContainer.contains(e.target)) {
      sicContainer.style.display = "none";
    }
  });
});
