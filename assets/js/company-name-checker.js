jQuery(document).ready(function ($) {

    $("#companyNameCheckerForm").on("submit", function (e) {
        e.preventDefault();

        const search = $("#search").val().trim();
        if (!search) return;

        $.post(ncuk_ajax.ajax_url, {
            action: "company_name_checker",
            search: search
        }, function (response) {

            // ---- THE FIX: HTML is inside response.data.html ----
            if (response && response.data && response.data.html) {
                $("#responseContainer").html(response.data.html);
            } else {
                $("#responseContainer").html("<p style='color:red;'>Unexpected response.</p>");
            }

            // Step-1 validation trigger
            let isAvailable = false;

            if (response.success && response.data && response.data.available === true) {
                isAvailable = true;
            }

            document.dispatchEvent(new CustomEvent("companyNameChecked", {
                detail: { available: isAvailable }
            }));
        });

    });

});
