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


document.addEventListener("DOMContentLoaded", function () {

    let officers = [];
    let editingIndex = null;

    // SWITCH TO A TAB
    function openTab(selector) {
        document.querySelectorAll(".step3-tab-content").forEach(c => c.style.display = "none");
        document.querySelector(selector).style.display = "block";

        document.querySelectorAll(".step3-tab").forEach(t => t.classList.remove("active"));
        document.querySelector(`.step3-tab[data-target="${selector}"]`).classList.add("active");
    }

    // NEXT BUTTON FROM OFFICER -> DETAILS
    document.getElementById("officer-next-btn").addEventListener("click", function () {
        openTab("#tab-det");
    });

    // NEXT BUTTON FROM DETAILS -> ADDRESS
    document.getElementById("details-next-btn").addEventListener("click", function () {
        openTab("#tab-addr");
    });

    // FINAL SAVE BUTTON
    document.getElementById("address-save-btn").addEventListener("click", function () {

        const officer = {
            type: document.querySelector("input[name='officer_type']:checked").value,
            first: document.getElementById("det_first").value,
            last: document.getElementById("det_last").value,
            dob: document.getElementById("det_dob").value,
            line1: document.getElementById("addr_line1").value,
            city: document.getElementById("addr_city").value,
            postcode: document.getElementById("addr_postcode").value,
            roles: getRoles()
        };

        if (editingIndex !== null) {
            officers[editingIndex] = officer;
            editingIndex = null;
        } else {
            officers.push(officer);
        }

        renderOfficers();
        openTab("#tab-pos"); // go back to Position tab
    });

    // GET SELECTED ROLES FROM POSITION TAB
    function getRoles() {
        return {
            director: document.getElementById("role_director").checked,
            shareholder: document.getElementById("role_shareholder").checked,
            secretary: document.getElementById("role_secretary").checked,
            psc: document.getElementById("role_psc").checked
        };
    }

    // RENDER OFFICER CARDS
    function renderOfficers() {

        const container = document.getElementById("officer-list");
        container.innerHTML = "";

        officers.forEach((o, index) => {

            let roles = [];
            if (o.roles.director) roles.push("Director");
            if (o.roles.shareholder) roles.push("Shareholder");
            if (o.roles.secretary) roles.push("Secretary");
            if (o.roles.psc) roles.push("PSC");

            const box = document.createElement("div");
            box.className = "officer-box";
            box.style.padding = "15px";
            box.style.border = "1px solid #ddd";
            box.style.marginBottom = "10px";

            box.innerHTML = `
                <strong>${o.first} ${o.last}</strong><br>
                <small>${roles.join(", ")}</small><br><br>
                <button class="edit-officer" data-id="${index}">Edit</button>
                <button class="delete-officer" data-id="${index}">Remove</button>
            `;

            container.appendChild(box);
        });

        bindOfficerActions();
    }

    // EDIT / DELETE BUTTONS
    function bindOfficerActions() {

        document.querySelectorAll(".edit-officer").forEach(btn => {
            btn.addEventListener("click", function () {
                const i = this.dataset.id;
                loadOfficer(i);
            });
        });

        document.querySelectorAll(".delete-officer").forEach(btn => {
            btn.addEventListener("click", function () {
                officers.splice(this.dataset.id, 1);
                renderOfficers();
            });
        });
    }

    // LOAD OFFICER BACK TO FORM
    function loadOfficer(i) {

        const o = officers[i];
        editingIndex = i;

        // Officer
        document.querySelector(`input[name='officer_type'][value='${o.type}']`).checked = true;

        // Details
        document.getElementById("det_first").value = o.first;
        document.getElementById("det_last").value = o.last;
        document.getElementById("det_dob").value = o.dob;

        // Address
        document.getElementById("addr_line1").value = o.line1;
        document.getElementById("addr_city").value = o.city;
        document.getElementById("addr_postcode").value = o.postcode;

        // Roles
        document.getElementById("role_director").checked = o.roles.director;
        document.getElementById("role_shareholder").checked = o.roles.shareholder;
        document.getElementById("role_secretary").checked = o.roles.secretary;
        document.getElementById("role_psc").checked = o.roles.psc;

        // Go to first tab to start editing
        openTab("#tab-pos");
    }

});
