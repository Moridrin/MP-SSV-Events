<?php
/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 21-1-17
 * Time: 8:02
 */

if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_OPTIONS)) {
    if (isset($_POST['reset'])) {
        SSV_Events::resetGeneralOptions();
    } else {
        update_option(SSV_Events::OPTION_DEFAULT_REGISTRATION_STATUS, SSV_General::sanitize($_POST['default_registration_status']));
        update_option(SSV_Events::OPTION_REGISTRATION_MESSAGE, SSV_General::sanitize($_POST['registration_message']));
        update_option(SSV_Events::OPTION_CANCELLATION_MESSAGE, SSV_General::sanitize($_POST['cancellation_message']));
    }
}
?>
<form method="post" action="#">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Default Registration Status</th>
            <td>
                <?php $defaultRegistrationStatus = get_option(SSV_Events::OPTION_DEFAULT_REGISTRATION_STATUS); ?>
                <select name="default_registration_status" title="Default Registration Status">
                    <option value="pending" <?= $defaultRegistrationStatus == Registration::STATUS_PENDING ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $defaultRegistrationStatus == Registration::STATUS_APPROVED ? 'selected' : '' ?>>Approved</option>
                    <option value="denied" <?= $defaultRegistrationStatus == Registration::STATUS_DENIED ? 'selected' : '' ?>>Denied</option>
                </select>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Registration Message</th>
            <td><textarea name="registration_message" class="large-text" title="Registration Message"><?= esc_html(get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE)) ?></textarea></td>
        </tr>
        <tr valign="top">
            <th scope="row">Cancellation Message</th>
            <td><textarea name="cancellation_message" class="large-text" title="cancellation Message"><?= esc_html(get_option(SSV_Events::OPTION_CANCELLATION_MESSAGE)) ?></textarea></td>
        </tr>
    </table>
    <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_OPTIONS); ?>
</form>
