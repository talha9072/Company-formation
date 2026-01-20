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
// STEP 3 JS – TEST ENQUEUE (DIRECT)
// ===============================

// officer person (UI logic)
wp_enqueue_script(
    'officer-person-js',
    NCUK_URL . 'assets/js/step3/officer-person.js',
    ['jquery'],
    filemtime(NCUK_PATH . 'assets/js/step3/officer-person.js'),
    true
);

// officer storage (localStorage + save logic)
wp_enqueue_script(
    'officer-storage-js',
    NCUK_URL . 'assets/js/step3/officer-storage.js',
    ['jquery', 'officer-person-js'],
    filemtime(NCUK_PATH . 'assets/js/step3/officer-storage.js'),
    true
);

// step 3 controller
wp_enqueue_script(
    'form-step3-js',
    NCUK_URL . 'assets/js/form-step3.js',
    ['jquery', 'officer-person-js', 'officer-storage-js'],
    filemtime(NCUK_PATH . 'assets/js/form-step3.js'),
    true
);