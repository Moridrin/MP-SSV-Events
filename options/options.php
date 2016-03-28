<?php
include_once "mp-ssv-mailchimp-options.php";
include_once "mailchimp-tab.php";

function mp_ssv_add_mp_ssv_events_options() {
	add_submenu_page( 'mp_ssv_settings', 'Events Options', 'Events', 'manage_options', __FILE__, 'mp_ssv_events_settings_page' );
}

function mp_ssv_events_settings_page() {
	global $options;
	$active_tab = "general";
	if(isset($_GET['tab'])) {
		$active_tab = $_GET['tab'];
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ($active_tab == "general") {
			include_once "general-tab-save.php";
		} else if ($active_tab == "email") {
			include_once "email-tab-save.php";
		}
	}
	?>
	<div class="wrap">
		<h1>Events Options</h1>
		<h2 class="nav-tab-wrapper">
			<a href="?page=<?php echo __FILE__; ?>&tab=general" class="nav-tab <?php if ($active_tab == "general") { echo "nav-tab-active"; } ?>">General</a>
			<a href="?page=<?php echo __FILE__; ?>&tab=email" class="nav-tab <?php if ($active_tab == "email") { echo "nav-tab-active"; } ?>">Email</a>
		</h2>
		<?php
		if ($active_tab == "general") {
			include_once "general-tab.php";
		} else if ($active_tab == "email") {
			include_once "email-tab.php";
		}
		?>
	</div>
	<?php
}
add_action('admin_menu', 'mp_ssv_add_mp_ssv_events_options');
?>