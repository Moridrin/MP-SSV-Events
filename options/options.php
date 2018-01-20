<?php

use mp_ssv_general\base\SSV_Global;

if (!defined('ABSPATH')) {
    exit;
}
function ssv_add_ssv_events_options()
{
    add_submenu_page('ssv_settings', 'Events Options', 'Events', 'manage_options', 'ssv-events-settings', 'ssv_events_options_page_content');
}

function ssv_events_options_page_content()
{
    $activeTab = "general";
    if (isset($_GET['tab'])) {
        $activeTab = $_GET['tab'];
    }
    ?>
    <div class="wrap">
        <h1>Events Options</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?= esc_html($_GET['page']) ?>&tab=general" class="nav-tab <?= $activeTab === 'general' ? 'active' : '' ?>">General</a>
            <a href="?page=<?= esc_html($_GET['page']) ?>&tab=email" class="nav-tab <?= $activeTab === 'email' ? 'active' : '' ?>">Email</a>
            <a href="http://bosso.nl/ssv-events/" target="_blank" class="nav-tab">
                Help <img src="<?= esc_url(SSV_Global::URL) ?>/images/link-new-tab-small.png" width="14" style="vertical-align:middle">
            </a>
        </h2>
        <?php
        /** @noinspection PhpIncludeInspection */
        require_once $activeTab . '.php';
        ?>
    </div>
    <?php
}

add_action('admin_menu', 'ssv_add_ssv_events_options');
