<?php ?>
    <form method="post" action="#">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Registration Confirmation</th>
                <td>
                    <label>
                        <input type="checkbox" name="mp_ssv_event_email_registration_confirmation" value="true" <?php if (get_option('mp_ssv_event_email_registration_confirmation') == 'true') {
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
                        <input type="checkbox" name="mp_ssv_event_email_registration_verify" value="true" <?php if (get_option('mp_ssv_event_email_registration_verify') == 'true') {
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
                        <input type="checkbox" name="mp_ssv_event_email_registration_status_changed" value="true" <?php if (get_option('mp_ssv_event_email_registration_status_changed') == 'true') {
                            echo "checked";
                        } ?>/>
                        When an event admin changes someones registration, the registrant will receive and email on the status change.
                    </label>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
<?php ?>