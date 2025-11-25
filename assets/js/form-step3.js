document.addEventListener("DOMContentLoaded", function () {

    console.log("🔥 Step-3 JS Loaded");

    const buttons = document.querySelectorAll(".officer-add-btn");

    buttons.forEach(btn => {
        btn.addEventListener("click", function () {

            const target = this.dataset.target;
            const form = document.querySelector(target);

            // Hide all forms first
            document.querySelectorAll("#officer-person-form, #officer-corporate-form, #officer-entity-form")
                .forEach(f => f.style.display = "none");

            // Show the selected form
            form.style.display = "block";

            // Scroll smoothly
            form.scrollIntoView({ behavior: "smooth" });

        });
    });

});


document.addEventListener("DOMContentLoaded", function () {

    const tabs = document.querySelectorAll(".step3-tab");
    const contents = document.querySelectorAll(".step3-tab-content");

    function openTab(target) {
        tabs.forEach(t => t.classList.remove("active"));
        contents.forEach(c => c.style.display = "none");

        document.querySelector(`button[data-target="${target}"]`).classList.add("active");
        document.querySelector(target).style.display = "block";
    }

    // Tab click handlers
    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            openTab(this.dataset.target);
        });
    });

    // Role logic
    const roleDirector = document.getElementById("role_director");
    const roleShare = document.getElementById("role_shareholder");
    const roleSec = document.getElementById("role_secretary");
    const rolePSC = document.getElementById("role_psc");

    const nocTab = document.getElementById("tab-noc-btn");
    const shareTab = document.getElementById("tab-share-btn");
    const consentBox = document.getElementById("consent-box");

    function updateDynamicTabs() {

        // Reset
        consentBox.style.display = "none";
        nocTab.style.display = "none";
        shareTab.style.display = "none";

        // Director / Secretary → Show consent
        if (roleDirector.checked || roleSec.checked) {
            consentBox.style.display = "block";
        }

        // PSC → Show nature of control tab
        if (rolePSC.checked) {
            nocTab.style.display = "inline-block";
        }

        // Shareholder → Show shareholdings tab
        if (roleShare.checked) {
            shareTab.style.display = "inline-block";
        }
    }

    [roleDirector, roleShare, roleSec, rolePSC].forEach(cb => {
        cb.addEventListener("change", updateDynamicTabs);
    });

});
