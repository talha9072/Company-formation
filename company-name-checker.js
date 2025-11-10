jQuery(document).ready(function($) {
    $('#searchForm').submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        var searchQuery = $('#search').val(); // Get the search query

        $.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: {
                action: 'company_name_checker',
                search: searchQuery
            },
            success: function(response) {
                $('#responseContainer').html(response); // Display the response
            }
        });
    });
});


jQuery(document).ready(function ($) {
    $('#companyNameCheckerForm').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const searchQuery = $('#search').val(); // Get the search input value
        const formData = $(this).serialize(); // Serialize the form data

        // Add the `ukname` parameter to the URL without reloading the page
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('ukname', searchQuery); // Set the `ukname` parameter
        window.history.replaceState({}, '', currentUrl); // Update the URL

        // Send the AJAX request
        $.post(ajax_object.ajax_url, formData + '&action=company_name_checker', function (response) {
            $('#responseContainer').html(response); // Display the response in the container
        });
    });
});


