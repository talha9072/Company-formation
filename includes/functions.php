<?php
if (!defined('ABSPATH')) exit;

/*--------------------------------------------------------------
# 1. Helper: Remove common suffixes like LTD, PLC, etc.
--------------------------------------------------------------*/
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

/*--------------------------------------------------------------
# 2. Enqueue Scripts & Styles
--------------------------------------------------------------*/
add_action('wp_enqueue_scripts', function () {
    // jQuery (needed for AJAX)
    wp_enqueue_script('jquery');

    // Plugin JS
    wp_enqueue_script(
        'ncuk-checker',
        NCUK_URL . 'assets/js/company-name-checker.js',
        ['jquery'],
        null,
        true
    );

    // Localize (for AJAX URLs)
    wp_localize_script('ncuk-checker', 'ncuk_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);

    // Enqueue your main CSS
    wp_enqueue_style(
        'ncuk-styles',
        NCUK_URL . 'assets/css/index.css',
        [],
        filemtime(NCUK_PATH . 'assets/css/index.css') // cache-busting on changes
    );
});

/*--------------------------------------------------------------
# 3. AJAX Handler: Company Name Check
--------------------------------------------------------------*/
function ncuk_ajax_handler() {
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $apiKey = get_option('namecheck_uk_api_key', '');

    if (!$search) {
        wp_send_json_error(['html' => '<div class="response-box" style="background:#ff4f4f;color:white;padding:20px;border-radius:10px;">Please enter a company name.</div>']);
    }

    // Reserved keyword check
    $reservedResponse = function_exists('isReservedKeyword') ? isReservedKeyword($search) : false;
    $reservedPhraseResponse = function_exists('containsReservedPhrase') ? containsReservedPhrase($search) : false;

    if ($reservedResponse || $reservedPhraseResponse) {
        wp_send_json_success([
            'available' => false,
            'html' => ncuk_build_response('#E67000', 'checklist.png', $search, $reservedResponse ?: $reservedPhraseResponse)
        ]);
    }

    if (empty($apiKey)) {
        wp_send_json_error([
            'html' => ncuk_build_response('#ff4f4f', '', 'Error', 'API Key not configured.')
        ]);
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

    if ($code != 200) {
        wp_send_json_error([
            'html' => ncuk_build_response('#ff4f4f', '', 'Error', 'Unable to retrieve results.')
        ]);
    }

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
        wp_send_json_success([
            'available' => false,
            'html' => ncuk_build_response('#ff4f4f', 'remove.png', $search, 'This name is not available.')
        ]);
    } else {
        wp_send_json_success([
            'available' => true,
            'html' => ncuk_build_response('#28a745', 'success-icon.png', $search, 'This name is available!')
        ]);
    }
}
add_action('wp_ajax_company_name_checker', 'ncuk_ajax_handler');
add_action('wp_ajax_nopriv_company_name_checker', 'ncuk_ajax_handler');

/*--------------------------------------------------------------
# 4. Helper: Styled Response Box
--------------------------------------------------------------*/
function ncuk_build_response($color, $icon, $title, $message) {
    $img = $icon ? '<img src="' . NCUK_URL . 'assets/images/' . $icon . '" style="width:50px;height:50px;margin-bottom:15px;">' : '';
    return '<div class="response-box" style="background-color:' . esc_attr($color) . ';color:white;padding:20px;margin-top:20px;border-radius:10px;text-align:center;">' .
           $img . '<h2>' . esc_html($title) . '</h2><p>' . esc_html($message) . '</p></div>';
}

/*--------------------------------------------------------------
# 5. Company Name Checker Shortcode [company_name_checker]
--------------------------------------------------------------*/
function ncuk_shortcode() {
    ob_start(); ?>
    <style>
        .input-group { display:flex; justify-content:center; margin-top:20px; }
        .search-query {
            flex:1; padding:15px !important; border:2px solid #E67000;
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

/*--------------------------------------------------------------
# 6. Wrapper Shortcode [company_formation_wizard]
--------------------------------------------------------------*/
function ncuk_wrapper_shortcode() {
    ob_start(); ?>
    
    <style>
        .sub-tabs li.disabled {
            pointer-events: none;
            opacity: 0.4;
        }
    </style>

    <div class="company-formation-wrapper">

        <div class="main-tab-header">
            <h2 class="main-tab-title">Company Formation</h2>
            <p class="main-tab-subtitle">Complete your company registration in a few easy steps</p>
        </div>

        <!-- Tabs -->
        <ul class="sub-tabs">
            <li class="active" data-step="1">1. Particulars</li>
            <li class="disabled" data-step="2">2. Addresses</li>
            <li class="disabled" data-step="3">3. Appointments</li>
            <li class="disabled" data-step="4">4. Documents</li>
        </ul>

        <div id="step-content">
            <!-- Step 1 name checker only -->
            <div id="step1-name-checker">
                <?php echo do_shortcode('[company_name_checker]'); ?>
            </div>

            <div id="step-form">
                <?php ncuk_render_step_form(1); ?>
            </div>
        </div>
    </div>

<script>
jQuery(document).ready(function($){

    // Prevent clicking disabled steps
    $('.sub-tabs li').on('click', function(e){
        if($(this).hasClass('disabled')){
            e.preventDefault();
            return false;
        }

        const step = $(this).data('step');

        $('.sub-tabs li').removeClass('active');
        $(this).addClass('active');

        $.post(ncuk_ajax.ajax_url, {
            action: 'load_step_form',
            step: step
        }, function(response){
            $('#step-form').html(response);

            // Hide name checker on step > 1
            if(step > 1){
                $('#step1-name-checker').hide();
            } else {
                $('#step1-name-checker').show();
            }
        });
    });

});
</script>

    <?php
    return ob_get_clean();
}
add_shortcode('company_formation_wizard', 'ncuk_wrapper_shortcode');


/*--------------------------------------------------------------
# 7. AJAX Loader for Step Forms
--------------------------------------------------------------*/
add_action('wp_ajax_load_step_form', 'ncuk_load_step_form');
add_action('wp_ajax_nopriv_load_step_form', 'ncuk_load_step_form');

function ncuk_load_step_form() {
    $step = isset($_POST['step']) ? intval($_POST['step']) : 1;
    ncuk_render_step_form($step);
    wp_die();
}

/*--------------------------------------------------------------
# 8. Step Form Renderer (includes external files)
--------------------------------------------------------------*/
function ncuk_render_step_form($step) {
    $base_path = NCUK_PATH . 'includes/forms/'; // Folder where all step files are stored

    switch ($step) {
        case 1:
            $file = $base_path . 'form-step1.php'; // Particulars
            break;

        case 2:
            $file = $base_path . 'form-step2.php'; // Addresses
            break;

        case 3:
            $file = $base_path . 'form-step3.php'; // Appointments
            break;

        case 4:
            $file = $base_path . 'form-step4.php'; // Documents
            break;

        default:
            echo '<p>Invalid step.</p>';
            return;
    }

    // Safely include the file
    if (file_exists($file)) {
        include $file;
    } else {
        echo '<p>Form file not found for step ' . intval($step) . '.</p>';
    }
}
/*--------------------------------------------------------------
# NEW — Save Step 1 Data
--------------------------------------------------------------*/
add_action('wp_ajax_save_step1', 'ncuk_save_step1');
add_action('wp_ajax_nopriv_save_step1', 'ncuk_save_step1');

function ncuk_save_step1() {

    if(!session_id()){
        session_start();
    }

    $_SESSION['step1'] = [
        'company_type' => sanitize_text_field($_POST['company_type']),
        'jurisdiction' => sanitize_text_field($_POST['jurisdiction']),
        'business_activity' => sanitize_text_field($_POST['business_activity']),
        'sic_codes' => $_POST['sic_codes'],
    ];

    wp_send_json_success(['message' => 'Step 1 saved']);
}
