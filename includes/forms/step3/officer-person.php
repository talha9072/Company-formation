<?php if (!defined('ABSPATH')) exit; ?>

<div id="officer-person-form" class="step3-form-box" style="display:none;">

    <?php include __DIR__ . '/officer-person/tabs.php'; ?>

    <?php include __DIR__ . '/officer-person/tab-position.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-noc.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-share.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-details.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-addressing.php'; ?>

</div>

<?php
// ===============================
// STEP 3 – ONLY ONE JS FILE
// ===============================

wp_enqueue_script(
    'form-step3-js',
    NCUK_URL . 'assets/js/form-step3.js',
    ['jquery'], // agar jQuery use ho rahi hai
    filemtime(NCUK_PATH . 'assets/js/form-step3.js'),
    true
);

 // 🔑 MAKE NCUK_STEP3 AVAILABLE TO JS
    wp_localize_script(
        'form-step3-js',     // ⚠ MUST MATCH HANDLE ABOVE
        'NCUK_STEP3',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('ncuk_step3_nonce')
        ]
    );