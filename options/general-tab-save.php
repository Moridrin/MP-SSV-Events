<?php
global $options;
if (isset($_POST['ssv_show_registrations_in_profile'])) {
    update_option('ssv_show_registrations_in_profile', 'true');
} else {
    update_option('ssv_show_registrations_in_profile', 'false');
}
if (isset($_POST['ssv_event_guest_registration'])) {
    update_option('ssv_event_guest_registration', 'true');
} else {
    update_option('ssv_event_guest_registration', 'false');
}
update_option('ssv_event_default_registration_status', $_POST['ssv_event_default_registration_status']);
update_option('ssv_event_registration_message', $_POST['ssv_event_registration_message']);
update_option('ssv_event_cancellation_message', $_POST['ssv_event_cancellation_message']);
update_option('ssv_event_default_start_time', $_POST['ssv_event_default_start_time']);
update_option('ssv_event_default_end_time', $_POST['ssv_event_default_end_time']);
