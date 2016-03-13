<?php
include_once "mp-ssv-general-options.php";
include_once "mp-ssv-mailchimp-options.php";
include_once "mailchimp-tab.php";

function mp_ssv_add_mp_ssv_events_options() {
	add_submenu_page( 'mp_ssv_settings', 'Events Options', 'Events', 'manage_options', __FILE__, 'mp_ssv_events_settings_page' );
}

function mp_ssv_events_settings_page() {
	global $options;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		mp_ssv_settings_save();
	}
	include_once "options-page.php";
}
add_action('admin_menu', 'add_mp_ssv_events_options');

function mp_ssv_settings_save() {
	global $options;
	if (isset($_POST['mp_ssv_event_guest_registration'])) {
		update_option('mp_ssv_event_guest_registration', 'true');
	} else {
		update_option('mp_ssv_event_guest_registration', 'false');
	}
	update_option('mp_ssv_event_default_registration_status', $_POST['mp_ssv_event_default_registration_status']);
	update_option('mp_ssv_event_registration_message', $_POST['mp_ssv_event_registration_message']);
	update_option('mp_ssv_event_cancelation_message', $_POST['mp_ssv_event_cancelation_message']);
	update_option('mp_ssv_event_default_start_time', $_POST['mp_ssv_event_default_start_time']);
	update_option('mp_ssv_event_default_end_time', $_POST['mp_ssv_event_default_end_time']);
	update_option('mp_ssv_event_time_zone', $_POST['mp_ssv_event_time_zone']);
}
?>