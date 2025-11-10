<?php
/*
Plugin Name: NameCheck UK & Ireland
Description: A plugin to check the availability of company names.
Version: 1.0
Author: WEB HOSTING GURU
*/

// Add menu with only a "Settings" page
add_action('admin_menu', function () {
    // Main menu item
    add_menu_page(
        'NameCheck',          // Page title
        'NameCheck',          // Menu title
        'manage_options',     // Capability
        'namecheck-settings', // Menu slug
        function () {
            // Include the settings page content from settings.php
            include plugin_dir_path(__FILE__) . 'settings.php';
        },
        'dashicons-admin-generic', // Icon
        20                         // Position
    );
});


// Include the reserved keywords file
include 'reserved.php';

// Include the Ireland Name Checker file
include plugin_dir_path(__FILE__) . 'ireland.php';

// Function to remove common suffixes
function removeSuffixes($name) {
    $ignoreSuffixes = array(
        " LTD", " CO", " UK", " PCL", " LIMITED", " PLC", " LLP", " GROUP", 
        " INTERNATIONAL", " SERVICES", " HOLDINGS", " CORPORATION", " CORP", 
        " INC", " LLC", " PARTNERSHIP", " AND CO", " & CO", " AND COMPANY", 
        " & COMPANY", " TRUST", " ASSOCIATES", " ASSOCIATION", " CHAMBERS", 
        " FOUNDATION", " FUND", " INSTITUTE", " SOCIETY", " UNION", " SYNDICATE", 
        " GMBH", " AG", " KG", " OHG", " e.V.", " gGmbH", " K.K.", " S.A.", 
        " S.P.A.", " S.L.", " B.V.", " N.V.", " S.A.R.L.", " OY", " AB"
    ); // Add other suffixes as needed
    return preg_replace('/\b(' . implode('|', $ignoreSuffixes) . ')\b/i', '', $name);
}

// Enqueue scripts
function company_name_checker_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('company-name-checker', plugin_dir_url(__FILE__) . 'company-name-checker.js', array('jquery'), null, true);
    wp_localize_script('company-name-checker', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'company_name_checker_enqueue_scripts');

// AJAX handler
function company_name_checker_ajax_handler() {
    $searchQuery = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    // Fetch the API key from the WordPress database
    $apiKey = get_option('namecheck_uk_api_key', ''); // Default to an empty string if not set

    // Check if the search query is a reserved keyword or contains a reserved phrase
    $reservedResponse = isReservedKeyword($searchQuery);
    $reservedPhraseResponse = containsReservedPhrase($searchQuery);

    $responseText = '';

    if ($reservedResponse || $reservedPhraseResponse) {
        $responseText .= '<div class="response-box" style="background-color: #E67000; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">';
        $responseText .= '<img src="' . plugin_dir_url(__FILE__) . 'images/checklist.png" alt="Reserved" style="width: 50px; height: 50px; margin-bottom: 15px;">';
        $responseText .= '<h2>' . htmlspecialchars($searchQuery) . '</h2>';
        $responseText .= '<p>' . htmlspecialchars($reservedResponse ?: $reservedPhraseResponse) . '</p>';
        $responseText .= '</div>';
    } else {
        if (empty($apiKey)) {
            $responseText .= '<div class="response-box" style="background-color: #ff4f4f; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">';
            $responseText .= '<p>Error: UK API Key is not configured. Please update it in the NameCheck settings.</p>';
            $responseText .= '</div>';
        } else {
            $apiUrl = "https://api.companieshouse.gov.uk/search/companies?q=" . urlencode(removeSuffixes($searchQuery));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Basic ' . base64_encode($apiKey . ':')
            ));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 200) {
                $responseData = json_decode($response, true);
                $companyExists = false;

                foreach ($responseData['items'] as $item) {
                    $itemTitleWithoutSuffix = removeSuffixes($item['title']);
                    if (strcasecmp($itemTitleWithoutSuffix, removeSuffixes($searchQuery)) === 0) {
                        $companyExists = true;
                        break;
                    }
                }

                if ($companyExists) {
                    $responseText .= '<div class="response-box" style="background-color: #ff4f4f; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">';
                    $responseText .= '<img src="' . plugin_dir_url(__FILE__) . 'images/remove.png" alt="Error" style="width: 50px; height: 50px; margin-bottom: 15px;">';
                    $responseText .= '<h2>' . htmlspecialchars($searchQuery) . '</h2>';
                    $responseText .= '<p>This name is not available for registration.</p>';
                    $responseText .= '</div>';
                } else {
                    $responseText .= '<div class="response-box" style="background-color: #28a745; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">';
                    $responseText .= '<img src="' . plugin_dir_url(__FILE__) . 'images/success-icon.png" alt="Success" style="width: 50px; height: 50px; margin-bottom: 15px;">';
                    $responseText .= '<h2>' . htmlspecialchars($searchQuery) . '</h2>';
                    $responseText .= '<p>This name is available for registration!</p>';
                    $responseText .= '</div>';
                    $responseText .= '<div style="text-align: center; margin-top: 20px;">';
                    $responseText .= '<button id="choosePackageBtn" class="choose-package-btn ukbtn" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Choose a Package</button>';
                    $responseText .= '</div>';
                    $responseText .= '<script>';
                    $responseText .= 'document.addEventListener("click", function(event) {';
                    $responseText .= '    if (event.target && event.target.id === "choosePackageBtn") {';    
                    $responseText .= '        const urlParams = new URLSearchParams(window.location.search);';
                    $responseText .= '        const companyType = urlParams.get("Company-type");';
                    $responseText .= '        const ukName = urlParams.get("ukname");';
                    $responseText .= '        const country = urlParams.get("Country");';
                
                    // Build the redirect URL logic
                    $responseText .= '        let redirectUrl = "";';
                    $responseText .= '        if (companyType === "Company limited by Shares" && country === "England") {'; 
                    $responseText .= '            redirectUrl = "/package/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        } else if (companyType === "Company limited by Guarantee" && country === "England") {'; 
                    $responseText .= '            redirectUrl = "/package-gurrentee-england/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        } else if (companyType === "LLP" && country === "England") {'; 
                    $responseText .= '            redirectUrl = "/package-llp-england/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        } else if (companyType === "Company limited by Shares" && country === "Scotland") {'; 
                    $responseText .= '            redirectUrl = "/company-limited-by-shares-scotland/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        } else if (companyType === "Company limited by Guarantee" && country === "Scotland") {'; 
                    $responseText .= '            redirectUrl = "/company-limited-by-guarantee-scotland/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        } else if (companyType === "LLP" && country === "Scotland") {'; 
                    $responseText .= '            redirectUrl = "/llp-scotland/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        }
                    else if (companyType === "Company limited by Shares" && country === "Northern-Ireland") {'; 
                    $responseText .= '            redirectUrl = "/company-limited-by-shares-northern-ireland/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        } else if (companyType === "Company limited by Guarantee" && country === "Northern-Ireland") {'; 
                    $responseText .= '            redirectUrl = "/company-limited-by-guarantee-northern-ireland/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        } else if (companyType === "LLP" && country === "Northern-Ireland") {'; 
                    $responseText .= '            redirectUrl = "/llp-northern-ireland/?Company-type=" + encodeURIComponent(companyType) + "&Country=" + encodeURIComponent(country);';
                    $responseText .= '        }
                    ';
                
                    // Append the `ukname` parameter if it exists
                    $responseText .= '        if (redirectUrl && ukName) {'; 
                    $responseText .= '            redirectUrl += "&ukname=" + encodeURIComponent(ukName);'; 
                    $responseText .= '        }';
                
                    // Redirect if a valid URL is found
                    $responseText .= '        if (redirectUrl) {'; 
                    $responseText .= '            window.location.href = redirectUrl;'; 
                    $responseText .= '        } else {'; 
                    $responseText .= '            alert("Invalid selection or parameters missing.");'; 
                    $responseText .= '        }';
                    $responseText .= '    }';
                    $responseText .= '});';
                    $responseText .= '</script>';
                }
                
                
                
            } else {
                $responseText .= '<div class="response-box" style="background-color: #ff4f4f; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">';
                $responseText .= '<p>Error: Unable to retrieve results. HTTP Code ' . $httpCode . '</p>';
                $responseText .= '</div>';
            }

            curl_close($ch);
        }
    }

    echo $responseText;
    wp_die();
}

add_action('wp_ajax_company_name_checker', 'company_name_checker_ajax_handler');
add_action('wp_ajax_nopriv_company_name_checker', 'company_name_checker_ajax_handler');

// Shortcode function
function company_name_checker_shortcode() {
    ob_start();
    ?>
    <style>
        .input-group {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-top: 20px;
        }

        .search-query {
            flex: 1;
            padding: 15px;
            border: 2px solid #E67000;
            border-radius: 50px 0 0 50px;
            font-size: 16px;
            outline: none;
        }

        .btn {
            padding: 15px 25px;
            background-color: #E67000;
            color: white;
            border: none;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #cc5900;
        }

        .response-box img {
            margin-bottom: 15px;
        }
        #responseContainer h2{
            color: white;
            font-weight: 700;
        }
    </style>
    <form method="post" id="companyNameCheckerForm" style="text-align: center;">
        <div class="input-group">
            <input type="text" name="search" id="search" class="search-query"style="flex: 1; padding: 15px; border: 2px solid #E67000; border-radius: 50px 0 0 50px; font-size: 16px; outline: none;" placeholder="Enter company name">
            <button type="submit" class="btn">Check</button>
        </div>
    </form>
    <div id="responseContainer"></div>
    <script>
        jQuery(document).ready(function ($) {
            $('#companyNameCheckerForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.post(ajax_object.ajax_url, formData + '&action=company_name_checker', function (response) {
                    $('#responseContainer').html(response);
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.ukbtn').addEventListener('click', function() {
        window.location.href = '/uk-test';
    });
});

        document.addEventListener('DOMContentLoaded', function () {
    const inputField = document.getElementById('search'); // Replace 'companyName' with your input field ID

    inputField.addEventListener('input', function () {
        this.value = this.value.toUpperCase(); // Convert the input value to uppercase
    });
});

    </script>



    <?php
    return ob_get_clean();
}
add_shortcode('company_name_checker', 'company_name_checker_shortcode');
?>
