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
