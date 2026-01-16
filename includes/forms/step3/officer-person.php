<?php if (!defined('ABSPATH')) exit; ?>

<div id="officer-person-form" class="step3-form-box" style="display:none;">

    <?php include __DIR__ . '/officer-person/tabs.php'; ?>

    <?php include __DIR__ . '/officer-person/tab-position.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-noc.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-share.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-details.php'; ?>
    <?php include __DIR__ . '/officer-person/tab-addressing.php'; ?>

</div>

<?php include __DIR__ . '/officer-person/scripts.php'; ?>