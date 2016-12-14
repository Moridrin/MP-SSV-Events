<?php
function ssv_add_ssv_events_options()
{
    add_submenu_page('ssv_settings', 'Events Options', 'Events', 'manage_options', __FILE__, 'ssv_events_settings_page');
}

function ssv_events_settings_page()
{
    $active_tab = "general";
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    ?>
    <div class="wrap">
        <h1>Events Options</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=<?php echo __FILE__; ?>&tab=general" class="nav-tab <?php if ($active_tab == "general") {
                echo "nav-tab-active";
            } ?>">General</a>
            <a href="?page=<?php echo __FILE__; ?>&tab=email" class="nav-tab <?php if ($active_tab == "email") {
                echo "nav-tab-active";
            } ?>">Email</a>
            <a href="http://studentensurvival.com/ssv/ssv-events/" target="_blank" class="nav-tab">Help <img src="<?php echo plugin_dir_url(__DIR__); ?>general/images/link-new-tab.png" width="14px" style="vertical-align:middle"></a>
        </h2>
        <?php
        if ($active_tab == "general") {
            require_once "general-tab.php";
        } else {
            if ($active_tab == "email") {
                require_once "email-tab.php";
            }
        }
        ?>
    </div>
    <?php
}

add_action('admin_menu', 'ssv_add_ssv_events_options');
