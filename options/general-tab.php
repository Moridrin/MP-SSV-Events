<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_admin_referer('ssv_save_events_general_options')) {
    global $options;
    if (isset($_POST['ssv_event_guest_registration'])) {
        update_option('ssv_event_guest_registration', 'true');
    } else {
        update_option('ssv_event_guest_registration', 'false');
    }
    update_option('ssv_event_default_registration_status', $_POST['ssv_event_default_registration_status']);
    update_option('ssv_event_registration_message', $_POST['ssv_event_registration_message']);
    update_option('ssv_event_cancellation_message', $_POST['ssv_event_cancellation_message']);
}
?>
    <form method="post" action="#">
        <table class="form-table">

            <tr valign="top">
                <th scope="row">Default Registration Status</th>
                <td>
                    <select name="ssv_event_default_registration_status" title="Default Registration Status">
                        <option value="pending" <?php if (esc_attr(stripslashes(get_option('ssv_event_default_registration_status'))) == 'pending') {
                            echo "selected";
                        } ?>>Pending
                        </option>
                        <option value="approved"<?php if (esc_attr(stripslashes(get_option('ssv_event_default_registration_status'))) == 'approved') {
                            echo "selected";
                        } ?>>Approved
                        </option>
                        <option value="denied"<?php if (esc_attr(stripslashes(get_option('ssv_event_default_registration_status'))) == 'denied') {
                            echo "selected";
                        } ?>>Denied
                        </option>
                    </select>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Registration Message</th>
                <td><textarea name="ssv_event_registration_message" class="large-text" title="Registration Message"><?php echo esc_attr(stripslashes(get_option('ssv_event_registration_message'))); ?></textarea></td>
            </tr>

            <tr valign="top">
                <th scope="row">Cancellation Message</th>
                <td><textarea name="ssv_event_cancellation_message" class="large-text" title="cancellation Message"><?php echo esc_attr(stripslashes(get_option('ssv_event_cancellation_message'))); ?></textarea></td>
            </tr>

            <tr valign="top">
                <th scope="row">Enable Guest Registration</th>
                <td><input type="checkbox" name="ssv_event_guest_registration" value="true" <?php if (get_option('ssv_event_guest_registration') == 'true') {
                        echo "checked";
                    } ?> title="Enable Guest Registration"/></td>
            </tr>

        </table>

        <?php wp_nonce_field('ssv_save_events_general_options'); ?>
        <?php submit_button(); ?>
    </form>
<?php ?>