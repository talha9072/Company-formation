<?php
if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------------
   Load JS + pass JSON path
------------------------------------------------------------- */
wp_enqueue_script(
  'form-step1-js',
  NCUK_URL . 'assets/js/form-step1.js',
  ['jquery'],
  filemtime(NCUK_PATH . 'assets/js/form-step1.js'),
  true
);

wp_localize_script('form-step1-js', 'form1Data', [
  'jsonUrl' => esc_url(NCUK_URL . 'includes/forms/form-include/sic-data.json'),
]);
?>

<div class="step-form-wrapper">
  <form id="step1form" class="woocommerce">

    <!-- HIDDEN COMPANY NAME (auto-set by name checker) -->
    <input type="hidden" id="company_name" name="company_name" value="">

    <!-- ===================== -->
    <!-- COMPANY DETAILS BOX -->
    <!-- ===================== -->
    <div class="postbox">

        <div class="postbox-header">
            <h2 class="hndle">Company Details</h2>
        </div>

        <div class="inside">

            <table class="form-table">

                <!-- Company Type -->
                <tr class="form-group">
                    <th scope="row">
                        <label for="company_type">Type of Company</label>
                    </th>
                    <td>
                        <select id="company_type" name="company_type" class="regular-text">
                            <option value="">Select type</option>
                            <option value="Limited By Shares">Limited By Shares</option>
                            <option value="Limited By Guarantee">Limited By Guarantee</option>
                            <option value="LLP">LLP</option>
                            <option value="PLC">PLC</option>
                        </select>
                    </td>
                </tr>

                <!-- Jurisdiction -->
                <tr class="form-group">
                    <th scope="row">
                        <label for="jurisdiction">Jurisdiction</label>
                    </th>
                    <td>
                        <select id="jurisdiction" name="jurisdiction" class="regular-text">
                            <option value="">Select jurisdiction</option>
                            <option value="England & Wales">England & Wales</option>
                            <option value="Scotland">Scotland</option>
                            <option value="Northern Ireland">Northern Ireland</option>
                        </select>
                    </td>
                </tr>

            </table>

        </div>
    </div>

    <!-- ===================== -->
    <!-- BUSINESS ACTIVITY BOX -->
    <!-- ===================== -->
    <div class="postbox">

        <div class="postbox-header">
            <h2 class="hndle">Business Activity</h2>
        </div>

        <div class="inside">

            <table class="form-table">

                <!-- Business Activity -->
                <tr class="form-group">
                    <th scope="row">
                        <label for="business_activity">Business Activity</label>
                    </th>
                    <td style="position:relative;">

                        <select id="business_activity"
                                name="business_activity"
                                class="regular-text">
                            <option value="">Select</option>
                            <option value="All">All</option>

                            <?php
                            $letters = range('A', 'U');
                            $labels = [
                                "Agriculture, Forestry and Fishing",
                                "Mining and Quarrying",
                                "Manufacturing",
                                "Electricity & Energy",
                                "Water supply and Waste",
                                "Construction",
                                "Wholesale & Retail",
                                "Transport & Storage",
                                "Accommodation & Food",
                                "Information & Communication",
                                "Financial Services",
                                "Real Estate",
                                "Professional Services",
                                "Admin & Support",
                                "Public Administration",
                                "Education",
                                "Health & Social Work",
                                "Arts & Entertainment",
                                "Other Services",
                                "Household Activities",
                                "Extraterritorial"
                            ];

                            foreach ($letters as $i => $l) {
                                echo "<option value='{$l}'>{$l}: {$labels[$i]}</option>";
                            }
                            ?>
                        </select>

                        <!-- SIC DROPDOWN (JS CONTROLLED) -->
                        <div id="sic-category-container"
                             style="
                                width:100%;
                                border:1px solid #ccd0d4;
                                border-top:none;
                                background:#fff;
                                display:none;
                                position:absolute;
                                z-index:999;
                             ">
                        </div>

                    </td>
                </tr>

                <!-- Selected SIC Codes -->
                <tr class="form-group">
                    <th scope="row">
                        <label>Selected SIC Codes</label>
                    </th>
                    <td>
                        <div id="selected_sic_codes"
                             style="
                                min-height:45px;
                                background:#f6f7f7;
                                border:1px solid #ccd0d4;
                                padding:8px;
                                border-radius:4px;
                             ">
                            None Selected
                        </div>
                    </td>
                </tr>

            </table>

        </div>
    </div>

    <!-- ===================== -->
    <!-- FOOTER / ACTION -->
    <!-- ===================== -->
    <p class="submit" style="text-align:right;">
        <button type="button"
                id="step1-save"
                class="button button-primary button-large">
            Save & Continue
        </button>
    </p>

</form>

</div>


<script>
/* --------------------------------------------------------------------
   This inline JS ONLY handles:
   - Rendering SIC dropdown
   - Filter
   - Add/remove code
   - Notify form-step1.js via events
   -------------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", function () {

  const businessSelect = document.getElementById("business_activity");
  const sicContainer = document.getElementById("sic-category-container");
  const selectedContainer = document.getElementById("selected_sic_codes");

  let sicData = {};         // Loaded JSON
  let selectedCodes = {};   // Local temp - final state lives in external JS
  const MAX_SIC_CODES = 4;

  /* Load JSON */
  fetch("<?php echo esc_url(NCUK_URL . 'includes/forms/form-include/sic-data.json'); ?>")
    .then(r => r.json())
    .then(d => {
      sicData = d;

      // Restore previously selected SIC codes from external JS (DB restore)
      if (window.step1SelectedCodes) {
        selectedCodes = window.step1SelectedCodes;
        updateSelectedBox();
      }
    });

  /* Open dropdown when category changes */
  businessSelect.addEventListener("change", function () {
    const cat = this.value;

    if (!cat) {
      sicContainer.style.display = "none";
      return;
    }

    renderSicList(cat);
  });

  /* Render list */
  function renderSicList(category) {
    if (!sicData[category] && category !== "All") {
      sicContainer.style.display = "none";
      return;
    }

    const codes = (category === "All")
      ? Object.assign({}, ...Object.values(sicData))
      : sicData[category];

    sicContainer.style.display = "block";

    sicContainer.innerHTML = `
      <div style="padding:10px;">
        <input type="text" class="sic-filter" placeholder="Filter..."
          style="width:100%;padding:6px;border:1px solid #ccc;border-radius:4px;margin-bottom:8px;">
        <div class="sic-list"></div>
      </div>
    `;

    const list = sicContainer.querySelector(".sic-list");

    Object.entries(codes).forEach(([code, label]) => {
      const item = document.createElement("div");
      item.className = "sic-item";
      item.style.cssText =
        "display:flex;justify-content:space-between;align-items:center;padding:6px 3px;border-bottom:1px solid #eee;";

      item.innerHTML = `
        <span>${code} - ${label}</span>
        <button type="button" class="add-sic"
          data-code="${code}" data-label="${label}"
          style="background:#4CAF50;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">+</button>
      `;

      list.appendChild(item);
    });

    /* Filter */
    sicContainer.querySelector(".sic-filter").addEventListener("input", function () {
      const term = this.value.toLowerCase();
      list.querySelectorAll(".sic-item").forEach(div => {
        div.style.display = div.textContent.toLowerCase().includes(term) ? "flex" : "none";
      });
    });

    /* Adding SIC */
    list.addEventListener("click", function (e) {
      if (!e.target.classList.contains("add-sic")) return;

      const code = e.target.dataset.code;
      const label = e.target.dataset.label;

      if (selectedCodes[code]) return;
      if (Object.keys(selectedCodes).length >= MAX_SIC_CODES) {
        alert("⚠️ You can select up to 4 codes.");
        return;
      }

      selectedCodes[code] = { label };

      window.step1SelectedCodes = selectedCodes;
      document.dispatchEvent(new Event("sicUpdated"));
    });
  }

  /* Update selected box */
  function updateSelectedBox() {
    selectedContainer.innerHTML = "";

    const entries = Object.entries(selectedCodes);

    if (entries.length === 0) {
      selectedContainer.textContent = "None Selected";
      return;
    }

    entries.forEach(([code, obj]) => {
      const div = document.createElement("div");
      div.style.cssText =
        "display:flex;justify-content:space-between;align-items:center;padding:6px 10px;margin:4px 0;background:#dff0d8;border-radius:4px;";

      div.innerHTML = `
        <span>${code} - ${obj.label}</span>
        <button type="button" class="remove-sic" data-code="${code}"
          style="background:#e74c3c;color:white;border:none;padding:3px 8px;border-radius:4px;cursor:pointer;">×</button>
      `;

      selectedContainer.appendChild(div);

      /* Remove */
      div.querySelector(".remove-sic").addEventListener("click", function () {
        delete selectedCodes[code];
        window.step1SelectedCodes = selectedCodes;
        updateSelectedBox();
        document.dispatchEvent(new Event("sicUpdated"));
      });
    });
  }
});
</script>
