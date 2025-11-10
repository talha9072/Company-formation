<?php
if (!defined('ABSPATH')) exit;

/**
 * Remove common suffixes like LTD, PLC, etc.
 */
function ncuk_remove_suffixes($name) {
    $ignoreSuffixes = [
        " LTD", " CO", " UK", " PCL", " LIMITED", " PLC", " LLP", " GROUP",
        " INTERNATIONAL", " SERVICES", " HOLDINGS", " CORPORATION", " CORP",
        " INC", " LLC", " PARTNERSHIP", " AND CO", " & CO", " AND COMPANY",
        " & COMPANY", " TRUST", " ASSOCIATES", " ASSOCIATION", " CHAMBERS",
        " FOUNDATION", " FUND", " INSTITUTE", " SOCIETY", " UNION", " SYNDICATE",
        " GMBH", " AG", " KG", " OHG", " e.V.", " gGmbH", " K.K.", " S.A.",
        " S.P.A.", " S.L.", " B.V.", " N.V.", " S.A.R.L.", " OY", " AB"
    ];
    return preg_replace('/\b(' . implode('|', $ignoreSuffixes) . ')\b/i', '', $name);
}

/**
 * Enqueue JS
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('jquery');
    wp_enqueue_script('ncuk-checker', NCUK_URL . 'assets/js/company-name-checker.js', ['jquery'], null, true);
    wp_localize_script('ncuk-checker', 'ncuk_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
});

/**
 * AJAX Handler
 */
function ncuk_ajax_handler() {
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $apiKey = get_option('namecheck_uk_api_key', '');

    if (!$search) {
        echo '<div class="response-box" style="background:#ff4f4f;color:white;padding:20px;border-radius:10px;">Please enter a company name.</div>';
        wp_die();
    }

    // Reserved keyword check
    $reservedResponse = isReservedKeyword($search);
    $reservedPhraseResponse = containsReservedPhrase($search);

    if ($reservedResponse || $reservedPhraseResponse) {
        echo ncuk_build_response('#E67000', 'checklist.png', $search, $reservedResponse ?: $reservedPhraseResponse);
        wp_die();
    }

    if (empty($apiKey)) {
        echo ncuk_build_response('#ff4f4f', '', 'Error', 'API Key not configured. Add it in the NameCheck settings.');
        wp_die();
    }

    // Call Companies House API
    $url = "https://api.companieshouse.gov.uk/search/companies?q=" . urlencode(ncuk_remove_suffixes($search));
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Basic ' . base64_encode($apiKey . ':')]
    ]);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code == 200) {
        $data = json_decode($response, true);
        $exists = false;
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                if (strcasecmp(ncuk_remove_suffixes($item['title']), ncuk_remove_suffixes($search)) === 0) {
                    $exists = true;
                    break;
                }
            }
        }

        if ($exists) {
            echo ncuk_build_response('#ff4f4f', 'remove.png', $search, 'This name is not available for registration.');
        } else {
            echo ncuk_build_response('#28a745', 'success-icon.png', $search, 'This name is available for registration!');
        }
    } else {
        echo ncuk_build_response('#ff4f4f', '', 'Error', 'Unable to retrieve results. HTTP Code ' . intval($code));
    }

    wp_die();
}
add_action('wp_ajax_company_name_checker', 'ncuk_ajax_handler');
add_action('wp_ajax_nopriv_company_name_checker', 'ncuk_ajax_handler');

/**
 * Build consistent styled response boxes
 */
function ncuk_build_response($color, $icon, $title, $message) {
    $img = $icon ? '<img src="' . NCUK_URL . 'assets/images/' . $icon . '" style="width:50px;height:50px;margin-bottom:15px;">' : '';
    return '<div class="response-box" style="background-color:' . esc_attr($color) . ';color:white;padding:20px;margin-top:20px;border-radius:10px;text-align:center;">' .
           $img . '<h2>' . esc_html($title) . '</h2><p>' . esc_html($message) . '</p></div>';
}

/**
 * Shortcode [company_name_checker]
 */
function ncuk_shortcode() {
    ob_start(); ?>
    <style>
        .input-group { display:flex; justify-content:center; margin-top:20px; }
        .search-query {
            flex:1; padding:15px; border:2px solid #E67000;
            border-radius:50px 0 0 50px; font-size:16px; outline:none;
        }
        .btn {
            padding:15px 25px; background:#E67000; color:white;
            border:none; border-radius:0 50px 50px 0; cursor:pointer;
            font-size:16px;
        }
        .btn:hover { background:#cc5900; }
        #responseContainer h2 { color:white; font-weight:700; }
    </style>

    <form id="companyNameCheckerForm" style="text-align:center;">
        <div class="input-group">
            <input type="text" name="search" id="search" class="search-query" placeholder="Enter company name" />
            <button type="submit" class="btn">Check</button>
        </div>
    </form>
    <div id="responseContainer"></div>
    <?php return ob_get_clean();
}
add_shortcode('company_name_checker', 'ncuk_shortcode');
