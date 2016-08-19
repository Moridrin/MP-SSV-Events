<?php ?>
    <div class="wrap">
        <h2>Events Options</h2>

        <form method="post" action="#">
            <?php settings_fields('ssv-events-options-group'); ?>
            <?php do_settings_sections('ssv-events-options-group'); ?>
            <table class="form-table">
                <?php /** @noinspection PhpIncludeInspection */
                require_once(ABSPATH . 'wp-admin/includes/plugin.php'); ?>
                <?php if (is_plugin_active('ssv-frontend-members/ssv-frontend-members.php')) { ?>
                    <tr valign="top">
                        <th scope="row">Show Registrations in Profile Page</th>
                        <td>
                            <label>
                                <input type="checkbox" name="ssv_show_registrations_in_profile" value="true" <?php if (get_option('ssv_show_registrations_in_profile') == 'true') {
                                    echo "checked";
                                } ?>/>
                                If the theme supports <!--suppress SpellCheckingInspection -->
                                <a href="https://www.muicss.com/" target="_blank">MUI</a> it adds a tab called "Registrations". Else it will add it at the bottom of the page.<br/>
                                You can also add it at a custom location by adding a component with value
                                <xmp style="display: inline;">[ssv-events-registrations]</xmp>
                            </label>
                            .
                        </td>
                    </tr>
                <?php } ?>

                <tr valign="top">
                    <th scope="row">Enable Guest Registration</th>
                    <td><input type="checkbox" name="ssv_event_guest_registration" value="true" <?php if (get_option('ssv_event_guest_registration') == 'true') {
                            echo "checked";
                        } ?> title="Enable Guest Registration"/></td>
                </tr>

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
                    <th scope="row">cancellation Message</th>
                    <td><textarea name="ssv_event_cancellation_message" class="large-text" title="cancellation Message"><?php echo esc_attr(stripslashes(get_option('ssv_event_cancellation_message'))); ?></textarea></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Default Start Time</th>
                    <td><input type="time" name="ssv_event_default_start_time" value="<?php echo esc_attr(stripslashes(get_option('ssv_event_default_start_time'))); ?>" title="Default Start Time"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Default End Time</th>
                    <td><input type="time" name="ssv_event_default_end_time" value="<?php echo esc_attr(stripslashes(get_option('ssv_event_default_end_time'))); ?>" title="Default End Time"/></td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
<?php ?>