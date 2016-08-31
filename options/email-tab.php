<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_admin_referer('ssv_save_events_email_options')) {
    global $options;
    if (isset($_POST['ssv_event_email_registration_confirmation'])) {
        update_option('ssv_event_email_registration_confirmation', 'true');
    } else {
        update_option('ssv_event_email_registration_confirmation', 'false');
    }
    if (isset($_POST['ssv_event_email_registration_status_changed'])) {
        update_option('ssv_event_email_registration_status_changed', 'true');
    } else {
        update_option('ssv_event_email_registration_status_changed', 'false');
    }
    if (isset($_POST['ssv_event_email_registration_verify'])) {
        update_option('ssv_event_email_registration_verify', 'true');
    } else {
        update_option('ssv_event_email_registration_verify', 'false');
    }
}
?>
    <form method="post" action="#">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Registration Confirmation</th>
                <td>
                    <label>
                        <input type="checkbox" name="ssv_event_email_registration_confirmation" value="true" <?php if (get_option('ssv_event_email_registration_confirmation') == 'true') {
                            echo "checked";
                        } ?> />
                        When someone registers, the registrant will receive an email confirm their registration.
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Verify Email</th>
                <td>
                    <label>
                        <input type="checkbox" name="ssv_event_email_registration_verify" value="true" <?php if (get_option('ssv_event_email_registration_verify') == 'true') {
                            echo "checked";
                        } ?>/>
                        The registrant gets an email with an unique link. The registrant needs to follow this link to finish his/her registration.
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Registration Status Changed</th>
                <td>
                    <label>
                        <input type="checkbox" name="ssv_event_email_registration_status_changed" value="true" <?php if (get_option('ssv_event_email_registration_status_changed') == 'true') {
                            echo "checked";
                        } ?>/>
                        When an event admin changes someones registration, the registrant will receive and email on the status change.
                    </label>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field('ssv_save_events_email_options'); ?>
        <?php submit_button(); ?>
    </form>
<?php ?>