document.addEventListener("DOMContentLoaded", function () {

    console.log("🔥 officer-person.js loaded");

    const box = document.getElementById("officer-person-form");
    if (!box) return;

    /* ----------------------------------------------
       INTERNAL TAB SWITCHING
    -----------------------------------------------*/
    const tabs = box.querySelectorAll(".step3-tab");
    const contents = box.querySelectorAll(".step3-tab-content");

    function switchTab(id) {
        contents.forEach(c => c.style.display = "none");
        box.querySelector(id).style.display = "block";

        tabs.forEach(t => t.classList.remove("active"));
        box.querySelector(`.step3-tab[data-target="${id}"]`).classList.add("active");
    }

    tabs.forEach(t => {
        t.addEventListener("click", function () {
            switchTab(this.dataset.target);
        });
    });


    /* ----------------------------------------------
       ROLE LOGIC AND DYNAMIC TABS
    -----------------------------------------------*/
    const roleDirector = box.querySelector("#role_director");
    const roleShareholder = box.querySelector("#role_shareholder");
    const roleSecretary = box.querySelector("#role_secretary");
    const rolePSC = box.querySelector("#role_psc");

    const consentBox = box.querySelector("#consent-box");
    const tabNocBtn = box.querySelector("#tab-noc-btn");
    const tabShareBtn = box.querySelector("#tab-share-btn");

    function refreshRoleLogic() {
        consentBox.style.display = "none";
        tabNocBtn.style.display = "none";
        tabShareBtn.style.display = "none";

        if (roleDirector.checked || roleSecretary.checked)
            consentBox.style.display = "block";

        if (rolePSC.checked)
            tabNocBtn.style.display = "inline-block";

        if (roleShareholder.checked)
            tabShareBtn.style.display = "inline-block";
    }

    [roleDirector, roleShareholder, roleSecretary, rolePSC].forEach(cb => {
        cb.addEventListener("change", refreshRoleLogic);
    });


    /* ----------------------------------------------
       OFFICER FORM WIZARD
    -----------------------------------------------*/
    const officerNextBtn = box.querySelector("#officer-next-btn");
    const detailsNextBtn = box.querySelector("#details-next-btn");
    const saveBtn = box.querySelector("#address-save-btn");

    // Local officer array for PERSON only
    let officers = [];
    let editIndex = null;

    officerNextBtn.addEventListener("click", function () {
        switchTab("#tab-det");
    });

    detailsNextBtn.addEventListener("click", function () {
        switchTab("#tab-addr");
    });


    /* ----------------------------------------------
       SAVE OFFICER (PERSON ONLY)
    -----------------------------------------------*/
    saveBtn.addEventListener("click", function () {

        const officer = {
            type: "person",
            roles: {
                director: roleDirector.checked,
                shareholder: roleShareholder.checked,
                secretary: roleSecretary.checked,
                psc: rolePSC.checked
            },
            first: box.querySelector("#det_first").value,
            last: box.querySelector("#det_last").value,
            dob: box.querySelector("#det_dob").value,

            // address
            line1: box.querySelector("#addr_line1").value,
            city: box.querySelector("#addr_city").value,
            postcode: box.querySelector("#addr_postcode").value
        };

        if (editIndex !== null) {
            officers[editIndex] = officer;
            editIndex = null;
        } else {
            officers.push(officer);
        }

        renderOfficers();
        switchTab("#tab-pos");
    });


    /* ----------------------------------------------
       RENDER LOCAL OFFICER LIST
    -----------------------------------------------*/
    const listContainer = document.getElementById("officer-list");

    function renderOfficers() {
        listContainer.innerHTML = "";

        officers.forEach((o, i) => {
            const div = document.createElement("div");
            div.className = "officer-box";
            div.style.border = "1px solid #ddd";
            div.style.padding = "12px";
            div.style.marginBottom = "10px";

            let roles = [];
            if (o.roles.director) roles.push("Director");
            if (o.roles.shareholder) roles.push("Shareholder");
            if (o.roles.secretary) roles.push("Secretary");
            if (o.roles.psc) roles.push("PSC");

            div.innerHTML = `
                <strong>${o.first} ${o.last}</strong><br>
                <small>${roles.join(", ")}</small><br>
                <button class="edit-btn" data-id="${i}">Edit</button>
                <button class="delete-btn" data-id="${i}">Delete</button>
            `;

            listContainer.appendChild(div);
        });

        bindActions();
    }


    /* ----------------------------------------------
       BIND EDIT + DELETE ACTIONS
    -----------------------------------------------*/
    function bindActions() {

        listContainer.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", function () {
                const i = this.dataset.id;
                loadOfficer(i);
            });
        });

        listContainer.querySelectorAll(".delete-btn").forEach(btn => {
            btn.addEventListener("click", function () {
                officers.splice(this.dataset.id, 1);
                renderOfficers();
            });
        });
    }


    /* ----------------------------------------------
       LOAD OFFICER INTO FORM FOR EDITING
    -----------------------------------------------*/
    function loadOfficer(i) {

        const o = officers[i];
        editIndex = i;

        roleDirector.checked = o.roles.director;
        roleShareholder.checked = o.roles.shareholder;
        roleSecretary.checked = o.roles.secretary;
        rolePSC.checked = o.roles.psc;
        refreshRoleLogic();

        box.querySelector("#det_first").value = o.first;
        box.querySelector("#det_last").value = o.last;
        box.querySelector("#det_dob").value = o.dob;

        box.querySelector("#addr_line1").value = o.line1;
        box.querySelector("#addr_city").value = o.city;
        box.querySelector("#addr_postcode").value = o.postcode;

        box.style.display = "block";
        switchTab("#tab-pos");
    }


    /* ----------------------------------------------
       INIT
    -----------------------------------------------*/
    switchTab("#tab-pos");
    refreshRoleLogic();
});
