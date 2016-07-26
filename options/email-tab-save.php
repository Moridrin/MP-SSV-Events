<?php
global $options;
if (isset($_POST['mp_ssv_event_email_registration_confirmation'])) {
    update_option('mp_ssv_event_email_registration_confirmation', 'true');
} else {
    update_option('mp_ssv_event_email_registration_confirmation', 'false');
}
if (isset($_POST['mp_ssv_event_email_registration_status_changed'])) {
    update_option('mp_ssv_event_email_registration_status_changed', 'true');
} else {
    update_option('mp_ssv_event_email_registration_status_changed', 'false');
}
if (isset($_POST['mp_ssv_event_email_registration_verify'])) {
    update_option('mp_ssv_event_email_registration_verify', 'true');
} else {
    update_option('mp_ssv_event_email_registration_verify', 'false');
}
