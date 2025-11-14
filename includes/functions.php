<?php
if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------------
   1. REMOVE SUFFIXES (Helper for Name Check)
------------------------------------------------------------- */
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

/* -------------------------------------------------------------
   2. ENQUEUE SCRIPTS
------------------------------------------------------------- */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'ncuk-checker',
        NCUK_URL . 'assets/js/company-name-checker.js',
        ['jquery'],
        filemtime(NCUK_PATH . 'assets/js/company-name-checker.js'),
        true
    );

    wp_localize_script('ncuk-checker', 'ncuk_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);

    wp_enqueue_style(
        'ncuk-styles',
        NCUK_URL . 'assets/css/index.css',
        [],
        filemtime(NCUK_PATH . 'assets/css/index.css')
    );
});

require_once NCUK_PATH . 'includes/wizard-step1-storage.php';

/* -------------------------------------------------------------
   3. COMPANY NAME CHECK AJAX
------------------------------------------------------------- */
function ncuk_ajax_handler() {

    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $apiKey = get_option('namecheck_uk_api_key', '');

    if (!$search) {
        wp_send_json_error([
            'html' => '<div class="response-box" style="background:#ff4f4f;color:white;padding:20px;border-radius:10px;">Enter a name.</div>'
        ]);
    }

    $cleaned = ncuk_remove_suffixes($search);

    $reservedResponse = function_exists('isReservedKeyword') ? isReservedKeyword($cleaned) : false;
    $reservedPhraseResponse = function_exists('containsReservedPhrase') ? containsReservedPhrase($cleaned) : false;

    if ($reservedResponse || $reservedPhraseResponse) {
        wp_send_json_success([
            'available' => false,
            'html' => ncuk_build_response('#E67000', 'checklist.png', $search, $reservedResponse ?: $reservedPhraseResponse)
        ]);
    }

    if (empty($apiKey)) {
        wp_send_json_error([
            'html' => ncuk_build_response('#ff4f4f', '', 'Error', 'API Key missing.')
        ]);
    }

    $url = "https://api.companieshouse.gov.uk/search/companies?q=" . urlencode($cleaned);
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
            'html' => ncuk_build_response('#ff4f4f', '', 'Error', 'API request failed.')
        ]);
    }

    $data = json_decode($response, true);
    $exists = false;

    if (!empty($data['items'])) {
        foreach ($data['items'] as $item) {
            if (strcasecmp(ncuk_remove_suffixes($item['title']), $cleaned) === 0) {
                $exists = true;
                break;
            }
        }
    }

    if ($exists) {
        wp_send_json_success([
            'available' => false,
            'html' => ncuk_build_response('#ff4f4f', 'remove.png', $search, 'Name already exists.')
        ]);
    }

    wp_send_json_success([
        'available' => true,
        'html' => ncuk_build_response('#28a745', 'success-icon.png', $search, 'Name is available!')
    ]);
}

add_action('wp_ajax_company_name_checker', 'ncuk_ajax_handler');
add_action('wp_ajax_nopriv_company_name_checker', 'ncuk_ajax_handler');

/* -------------------------------------------------------------
   4. NAME CHECK RESPONSE BOX BUILDER
------------------------------------------------------------- */
function ncuk_build_response($color, $icon, $title, $message) {
    $img = $icon
        ? '<img src="' . NCUK_URL . 'assets/images/' . $icon . '" style="width:50px;height:50px;margin-bottom:15px;">'
        : '';
    return '<div class="response-box" style="background:' . esc_attr($color) . ';color:white;padding:20px;border-radius:10px;text-align:center;">'
            . $img . '<h2>' . esc_html($title) . '</h2><p>' . esc_html($message) . '</p></div>';
}

/* -------------------------------------------------------------
   5. SHORTCODE — NAME CHECKER
------------------------------------------------------------- */
function ncuk_shortcode() {
    ob_start(); ?>
    <style>
        .input-group { display:flex; margin-top:20px; }
        .search-query {
            flex:1; padding:15px; border:2px solid #E67000;
            border-radius:50px 0 0 50px; font-size:16px;
        }
        .btn {
            padding:15px 25px; background:#E67000; color:white;
            border:none; border-radius:0 50px 50px 0; cursor:pointer;
        }
        .btn:hover { background:#cc5900; }
    </style>

    <form id="companyNameCheckerForm">
        <div class="input-group">
            <input type="text" name="search" id="search" class="search-query" placeholder="Enter company name">
            <button type="submit" class="btn">Check</button>
        </div>
    </form>
    <div id="responseContainer"></div>
    <?php return ob_get_clean();
}
add_shortcode('company_name_checker', 'ncuk_shortcode');

/* -------------------------------------------------------------
   6. WIZARD SHORTCODE — NO AJAX (STATIC FORMS)
------------------------------------------------------------- */
function ncuk_wrapper_shortcode() {
    ob_start(); ?>

    <style>
        .sub-tabs {
            display:flex; gap:15px; margin:20px 0;
            padding:0; list-style:none;
        }
        .step-item {
            display:flex; gap:5px; align-items:center;
            padding:12px 20px; border:2px solid #ccc;
            border-radius:10px; cursor:pointer; transition:.3s;
        }
        .step-item .step-circle {
            width:32px; height:32px; background:#ccc; color:white;
            border-radius:50%; display:flex; justify-content:center; align-items:center;
        }
        .step-item.active { border-color:#4a3b8f; }
        .step-item.active .step-circle { background:#E67000; }
        .step-item.disabled { opacity:.4; pointer-events:none; }

        .step-item.completed { border-color:#28a745; }
        .step-item.completed .step-circle {
            background:#28a745; color:transparent; position:relative;
        }
        .step-item.completed .step-circle::before {
            content:"✔"; color:white; font-size:16px; position:absolute;
        }

        .step-form { display:none; }
        .step-form.active { display:block; }
    </style>

    <div class="company-formation-wrapper">

        <h2 class="main-tab-title">Company Formation</h2>
        <p class="main-tab-subtitle">Complete your registration in simple steps</p>

        <ul class="sub-tabs">
            <li class="step-item active" data-step="1"><span class="step-circle">1</span> Particulars</li>
            <li class="step-item disabled" data-step="2"><span class="step-circle">2</span> Addresses</li>
            <li class="step-item disabled" data-step="3"><span class="step-circle">3</span> Appointments</li>
            <li class="step-item disabled" data-step="4"><span class="step-circle">4</span> Documents</li>
        </ul>

        <div id="step1-name-checker">
            <?php echo do_shortcode('[company_name_checker]'); ?>
        </div>

        <div id="all-steps">
            <div id="step1" class="step-form active">
                <?php ncuk_render_step_form(1); ?>
            </div>
            <div id="step2" class="step-form">
                <?php ncuk_render_step_form(2); ?>
            </div>
            <div id="step3" class="step-form">
                <?php ncuk_render_step_form(3); ?>
            </div>
            <div id="step4" class="step-form">
                <?php ncuk_render_step_form(4); ?>
            </div>
        </div>

    </div>

<script>
jQuery(document).ready(function($){
    $(".step-item").on("click", function(){

        if($(this).hasClass("disabled")) return;

        const step = $(this).data("step");

        $(".step-item").removeClass("active");
        $(this).addClass("active");

        // Complete previous
        $(".step-item").each(function(){
            if($(this).data("step") < step){
                $(this).addClass("completed");
            }
        });

        // Show correct form
        $(".step-form").removeClass("active");
        $("#step"+step).addClass("active");

        // Only Step1 shows name checker
        if(step == 1) $("#step1-name-checker").show();
        else $("#step1-name-checker").hide();
    });
});
</script>

    <?php return ob_get_clean();
}
add_shortcode('company_formation_wizard', 'ncuk_wrapper_shortcode');

/* -------------------------------------------------------------
   7. STEP FORM RENDERER
------------------------------------------------------------- */
function ncuk_render_step_form($step) {
    $base = NCUK_PATH . 'includes/forms/';

    $file = $base . "form-step{$step}.php";
    if (file_exists($file)) include $file;
    else echo "<p>Form file missing for step {$step}</p>";
}
