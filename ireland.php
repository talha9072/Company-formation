<?php
// Function to handle Ireland company name search

function irelandCompanyNameSearch() {
    $companyName = sanitize_text_field($_POST['company_name'] ?? '');
    $responseText = '';

    // Define the stripSuffixesIreland function
    function stripSuffixesIreland($name) {
        $ignoreSuffixes = array(
            " LTD", " LIMITED", " PLC", " LLP", " GROUP", " HOLDINGS", " CORPORATION",
            " CORP", " INC", " LLC", " PARTNERSHIP", " AND CO", " & CO", " AND COMPANY",
            " & COMPANY", " TRUST", " ASSOCIATES", " ASSOCIATION", " FOUNDATION", " FUND",
            " INSTITUTE", " SOCIETY", " UNION", " SYNDICATE", " CHAMBERS", " ENTERPRISES",
            " DEVELOPMENTS", " PROPERTIES", " INVESTMENTS", " TRADING", " SERVICES",
            " SOLUTIONS", " CONSULTANTS", " CONSULTANCY", " RESOURCES", " PROJECTS",
            " VENTURES", " COMMERCIAL", " FINANCE", " TECHNOLOGIES", " GLOBAL",
            " IRELAND", " INTERNATIONAL", " EUROPE", " PARTNERS", " UNLIMITED", " MANAGEMENT",
            " CAPITAL", " INDUSTRIES", " CO-OPERATIVE", ".COM", " COOPERATIVE", " WHOLESALE",
            " WHOLESALE SUPPLIES"
        );
        return preg_replace('/\b(' . implode('|', $ignoreSuffixes) . ')\b/i', '', $name);
    }

    if (!empty($companyName)) {
        $encodedQuery = http_build_query(array("company_name" => $companyName, "htmlEnc" => "1"));
        $url = "https://services.cro.ie/cws/companies?" . $encodedQuery;
    
        // Fetch the Ireland API key from the WordPress database
        $irelandApiKey = get_option('namecheck_ireland_api_key', ''); // Default to an empty string if not set
    
        if (empty($irelandApiKey)) {
            $responseText = '<div class="response-box" style="background-color: #ff4f4f; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">
                                <p>Error: Ireland API Key is not configured. Please update it in the NameCheck settings.</p>
                             </div>';
            echo $responseText;
            exit;
        }
    
        // Setup cURL for the API request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Basic " . base64_encode($irelandApiKey),
            "Content-Type: application/json",
            "charset: utf-8"
        ));
    

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 401) {
            $responseText = '<div class="response-box" style="background-color: #ff4f4f; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">
                                <p>Error: Authorization credentials are not valid.</p>
                             </div>';
            echo $responseText;
            exit;
        }

        $resultsArray = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $responseText = '<div class="response-box" style="background-color: #ff4f4f; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">
                                <p>Error: Invalid JSON response.</p>
                             </div>';
            echo $responseText;
            exit;
        }

        $companyExists = false;
        $cleanQuery = stripSuffixesIreland($companyName);

        if (is_array($resultsArray) && count($resultsArray) > 0) {
            foreach ($resultsArray as $item) {
                $itemTitleWithoutSuffix = stripSuffixesIreland($item['company_name']);
                if (strcasecmp($item['company_name'], $companyName) === 0 ||
                    strcasecmp($itemTitleWithoutSuffix, $companyName) === 0 ||
                    strcasecmp($itemTitleWithoutSuffix, $cleanQuery) === 0 ||
                    strcasecmp($itemTitleWithoutSuffix, $cleanQuery . 's') === 0 ||
                    strcasecmp($itemTitleWithoutSuffix, rtrim($cleanQuery, 's')) === 0) {
                    $companyExists = true;
                    break;
                }
            }
        }

        if ($companyExists) {
            $responseText = '<div class="response-box" style="background-color: #ff4f4f; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">
                                <img src="' . plugin_dir_url(__FILE__) . 'images/remove.png" alt="Error" style="width: 50px; height: 50px; margin-bottom:15px;">
                                <h2>' . htmlspecialchars($companyName) . '</h2>
                                <p>This name is not available for registration.</p>
                             </div>';
        } else {
            $responseText = '<div class="response-box" style="background-color: #28a745; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">
                                <img src="' . plugin_dir_url(__FILE__) . 'images/success-icon.png" alt="Success" style="width: 50px; height: 50px; margin-bottom:15px;">
                                <h2>' . htmlspecialchars($companyName) . '</h2>
                                <p>This name is available for registration!</p>
                             </div>';
                             $responseText .= '<div style="text-align: center; margin-top: 20px;">';
                $responseText .= '<button id="choosePackageBtn" class="choose-package-btn" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">Choose a Package</button>';
                $responseText .= '<script>
                    document.getElementById("choosePackageBtn").addEventListener("click", function() {
                        window.location.href = "/package-ireland";
                    });
                </script>';
        }
    } else {
        $responseText = '<div class="response-box" style="background-color: #ffc107; color: white; padding: 20px; margin-top: 20px; border-radius: 10px; text-align: center;">
                            <p>Please enter a company name to check availability.</p>
                         </div>';
    }

    echo $responseText;
    exit;
}

// AJAX request handler for Ireland company name check
add_action('wp_ajax_ireland_company_name_checker', 'irelandCompanyNameSearch');
add_action('wp_ajax_nopriv_ireland_company_name_checker', 'irelandCompanyNameSearch');

// Shortcode function
function ireland_name_checker_shortcode() {
    ob_start();
    ?>
    <form id="irelandNameCheckerForm" method="post" style="text-align: center;">
        <div class="input-group" style="display: flex; justify-content: center; margin-top: 20px;">
            <input type="text" name="company_name" id="companyName" placeholder="Enter company name" style="flex: 1; padding: 15px; border: 2px solid #E67000; border-radius: 50px 0 0 50px; font-size: 16px; outline: none;">
            <button type="button" id="checkCompanyName" style="padding: 15px 25px; background-color: #E67000; color: white; border: none; border-radius: 0 50px 50px 0; cursor: pointer; font-size: 16px;">Check</button>
        </div>
    </form>
    <div id="irelandResponseContainer" style="margin-top: 20px; text-align: center;"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('checkCompanyName').addEventListener('click', function () {
            var companyName = document.getElementById('companyName').value;
            var responseContainer = document.getElementById('irelandResponseContainer');

            // Define the additional parameters
            var companyType = "Company limited by Shares"; // Example, you can dynamically set this value
            var country = "Ireland";

            // Update the URL with the parameters
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('irelandname', companyName);
            currentUrl.searchParams.set('Company-type', companyType);
            currentUrl.searchParams.set('Country', country);
            window.history.replaceState({}, '', currentUrl); // Update the browser URL without reloading

            // Create and send the AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    responseContainer.innerHTML = xhr.responseText;

                    // Add functionality for the Choose Package button
                    var choosePackageBtn = document.getElementById('choosePackageBtn');
                    if (choosePackageBtn) {
                        choosePackageBtn.addEventListener('click', function () {
                            var targetUrl = "/package-ireland";
                            targetUrl += "?irelandname=" + encodeURIComponent(companyName); // Append irelandname
                            targetUrl += "&Company-type=" + encodeURIComponent(companyType); // Append company type
                            targetUrl += "&Country=" + encodeURIComponent(country); // Append country
                            window.location.href = targetUrl; // Redirect to the new page
                        });
                    }
                } else {
                    responseContainer.innerHTML = '<div style="color: red;">An error occurred while processing your request. Please try again later.</div>';
                }
            };

            // Send the AJAX request with the company name
            xhr.send('action=ireland_company_name_checker&company_name=' + encodeURIComponent(companyName));
        });
    });
</script>

<style>
h2{
    color: white;
    font-weight: 700;
}

</style>

    <?php
    return ob_get_clean();
}
add_shortcode('ireland_name_checker', 'ireland_name_checker_shortcode');
