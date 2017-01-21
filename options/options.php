<?php
function ssv_add_ssv_events_options()
{
    add_submenu_page('ssv_settings', 'Events Options', 'Events', 'manage_options', __FILE__, 'ssv_events_options_page_content');
}

function ssv_events_options_page_content()
{
    $active_tab = "general";
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    ?>
    <div class="wrap">
        <h1>Events Options</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?= $_GET['page'] ?>&tab=general" class="nav-tab <?= $active_tab == 'general' ? 'nav-tab-active' : '' ?>">General</a>
            <a href="?page=<?= $_GET['page'] ?>&tab=email" class="nav-tab <?= $active_tab == 'email' ? 'nav-tab-active' : '' ?>">Email</a>
            <a href="http://2016.bosso.nl/ssv-events/" target="_blank" class="nav-tab">
                Help <img src="<?= SSV_Users::URL ?>general/images/link-new-tab.png" width="14px" style="vertical-align:middle">
            </a>
        </h2>
        <?php
        switch ($active_tab) {
            case "general":
                require_once "general.php";
                break;
            case "email":
                require_once "email.php";
                break;
        }
        ?>
    </div>
    <?php
}

add_action('admin_menu', 'ssv_add_ssv_events_options');

function ssv_events_general_options_page_content()
{
    ?><h2><a href="?page=<?= __FILE__ ?>">Events Options</a></h2><?php
}

add_action(SSV_General::HOOK_GENERAL_OPTIONS_PAGE_CONTENT, 'ssv_events_general_options_page_content');
