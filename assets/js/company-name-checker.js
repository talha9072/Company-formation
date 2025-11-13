jQuery(document).ready(function ($) {

    $("#companyNameCheckerForm").on("submit", function (e) {
        e.preventDefault();

        const search = $("#search").val().trim();
        if (!search) return;

        // Clear old content (helps UX)
        $("#responseContainer").html("<p style='padding:10px;'>Checking...</p>");

        $.post(
            ncuk_ajax.ajax_url,
            {
                action: "company_name_checker",
                search: search
            },
            function (response) {

                // Response format MUST be JSON
                if (!response || !response.data) {
                    $("#responseContainer").html(
                        "<p style='color:red;'>Unexpected response.</p>"
                    );
                    return;
                }

                // Display response HTML
                if (response.data.html) {
                    $("#responseContainer").html(response.data.html);
                }

                // Determine availability
                const isAvailable =
                    response.success &&
                    response.data.available === true;

                // Notify Step-1 validator (form-step1.js listens)
                document.dispatchEvent(
                    new CustomEvent("companyNameChecked", {
                        detail: { available: isAvailable }
                    })
                );
            }
        );
    });

});
