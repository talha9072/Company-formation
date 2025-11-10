jQuery(document).ready(function($) {
    $('#input_GFFORMID_SEARCH').on('input', function() {
        var searchQuery = $(this).val();
        $.ajax({
            type: 'POST',
            url: cccAjax.ajaxurl,
            data: { action: 'ccc_search_company', search: searchQuery },
            success: function(response) {
                $('#response-container').html(response);
            }
        });
    });
});