<?php
function mp_ssv_add_mp_ssv_events_options()
{
    add_submenu_page('mp_ssv_settings', 'Events Options', 'Events', 'manage_options', __FILE__, 'mp_ssv_events_settings_page');
}

function mp_ssv_events_settings_page()
{
    $active_tab = "general";
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($active_tab == "general") {
            require_once "general-tab-save.php";
        } else {
            if ($active_tab == "email") {
                require_once "email-tab-save.php";
            }
        }
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
            <a href="http://studentensurvival.com/mp-ssv/mp-ssv-events/" target="_blank" class="nav-tab">Help <img src="<?php echo plugin_dir_url('mp-ssv-general/images/link-new-tab.png'); ?>link-new-tab.png" width="14px" style="vertical-align:middle"></a>
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

add_action('admin_menu', 'mp_ssv_add_mp_ssv_events_options');
?>