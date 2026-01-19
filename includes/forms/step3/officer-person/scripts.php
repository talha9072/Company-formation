<?php if (!defined('ABSPATH')) exit; ?>

<?php
wp_enqueue_script(
    'officer-person-js',
    NCUK_URL . 'assets/js/step3/officer-person.js',
    ['jquery'],
    filemtime(NCUK_PATH . 'assets/js/step3/officer-person.js'),
    true
);

wp_enqueue_script(
    'officer-storage-js',
    NCUK_URL . 'assets/js/step3/officer-storage.js',
    ['officer-person-js'], // dependency
    filemtime(NCUK_PATH . 'assets/js/step3/officer-storage.js'),
    true
);