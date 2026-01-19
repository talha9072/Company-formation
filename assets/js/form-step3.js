document.addEventListener("DOMContentLoaded", function () {

    console.log("🔥 Step-3 JS Loaded (Fixed)");

    const officerForm = document.getElementById("officer-person-form");
    if (!officerForm) return;

    const radios = document.querySelectorAll('input[name="officer_type"]');

    /* ----------------------------------------------
       ENSURE HIDDEN INPUT FOR TYPE
    -----------------------------------------------*/
    let hiddenTypeInput = officerForm.querySelector('input[name="officer_type_hidden"]');
    if (!hiddenTypeInput) {
        hiddenTypeInput = document.createElement("input");
        hiddenTypeInput.type = "hidden";
        hiddenTypeInput.name = "officer_type_hidden";
        officerForm.appendChild(hiddenTypeInput);
    }

    /* ----------------------------------------------
       FUNCTION: SHOW FORM
    -----------------------------------------------*/
    function showOfficerForm(type) {

        // show form
        officerForm.style.display = "block";

        // set type
        hiddenTypeInput.value = type;

        console.log("👤 Officer type:", type);

        // scroll to form
        officerForm.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }

    /* ----------------------------------------------
       RADIO CHANGE HANDLER
    -----------------------------------------------*/
    radios.forEach(radio => {
        radio.addEventListener("change", function () {

            // reset old data
            officerForm.reset?.();

            showOfficerForm(this.value);
        });
    });

    /* ----------------------------------------------
       AUTO SHOW ON PAGE LOAD (DEFAULT RADIO)
    -----------------------------------------------*/
    const defaultRadio = document.querySelector('input[name="officer_type"]:checked');
    if (defaultRadio) {
        showOfficerForm(defaultRadio.value);
    }

});
