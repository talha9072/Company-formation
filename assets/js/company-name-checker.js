jQuery(document).ready(function ($) {

    $("#companyNameCheckerForm").on("submit", function (e) {
        e.preventDefault();

        const search = $("#search").val().trim();
        if (!search) return;

        $("#responseContainer").html("<p style='padding:10px;'>Checking...</p>");

        $.post(
            ncuk_ajax.ajax_url,
            {
                action: "company_name_checker",
                search: search
            },
            function (response) {

                // Response format check
                if (!response || !response.data) {
                    $("#responseContainer").html(
                        "<p style='color:red;'>Unexpected response.</p>"
                    );
                    return;
                }

                // Render results box (green/red)
                if (response.data.html) {
                    $("#responseContainer").html(response.data.html);
                }

                // Name availability result
                const isAvailable =
                    response.success &&
                    response.data.available === true;

                // -------------------------------------------------------
                // SYNC WITH STEP-1 (IMPORTANT FIX)
                // -------------------------------------------------------

                // Autofill hidden company_name inside Step-1 form
                const hiddenInput = document.querySelector("#company_name");
                if (hiddenInput) {
                    hiddenInput.value = search;
                }

                // Fire event for Step-1 validator
                document.dispatchEvent(
                    new CustomEvent("companyNameChecked", {
                        detail: { available: isAvailable }
                    })
                );

                // If unavailable → empty the hidden field
                if (!isAvailable && hiddenInput) {
                    hiddenInput.value = "";
                }
            }
        );
    });

});
