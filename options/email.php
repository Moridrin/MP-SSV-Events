<?php
/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 21-1-17
 * Time: 8:02
 */

if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_OPTIONS)) {
    if (isset($_POST['reset'])) {
        SSV_Events::resetEmailOptions();
    } else {
        update_option(SSV_Events::OPTION_EMAIL_AUTHOR, filter_var($_POST['email_on_registration'], FILTER_VALIDATE_BOOLEAN));
        update_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED, filter_var($_POST['email_on_registration_status_changed'], FILTER_VALIDATE_BOOLEAN));
    }
}
?>
<form method="post" action="#">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Email Author</th>
            <td>
                <label>
                    <input type="hidden" name="email_on_registration" value="false"/>
                    <input type="checkbox" name="email_on_registration" value="true" <?= get_option(SSV_Events::OPTION_EMAIL_AUTHOR) ? 'checked' : '' ?> />
                    When someone registers or cancels the event author will receive an email.
                </label>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Registration Status Changed</th>
            <td>
                <label>
                    <input type="hidden" name="email_on_registration_status_changed" value="false"/>
                    <input type="checkbox" name="email_on_registration_status_changed" value="true" <?= get_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED) ? 'checked' : '' ?>/>
                    When an event admin changes someones registration, the registrant will receive and email on the status change.
                </label>
            </td>
        </tr>
    </table>
    <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_OPTIONS); ?>
</form>
