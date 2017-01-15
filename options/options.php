<?php
function ssv_add_ssv_events_options()
{
    add_submenu_page('ssv_settings', 'Events Options', 'Events', 'manage_options', __FILE__, 'ssv_events_options_page_content');
}

function ssv_events_options_page_content()
{
    if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_OPTIONS)) {
        if (isset($_POST['reset'])) {
            SSV_Events::CLEAN_INSTALL();
//            SSV_Events::resetOptions();
        } else {
            update_option(SSV_Events::OPTION_DEFAULT_REGISTRATION_STATUS, $_POST['default_registration_status']);
            update_option(SSV_Events::OPTION_REGISTRATION_MESSAGE, $_POST['registration_message']);
            update_option(SSV_Events::OPTION_REGISTRATION_VERIFICATION_MESSAGE, $_POST['registration_verification_message']);
            update_option(SSV_Events::OPTION_CANCELLATION_MESSAGE, $_POST['cancellation_message']);
            update_option(SSV_Events::OPTION_EMAIL_AUTHOR, filter_var($_POST['email_on_registration'], FILTER_VALIDATE_BOOLEAN));
            update_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED, filter_var($_POST['email_on_registration_status_changed'], FILTER_VALIDATE_BOOLEAN));
        }
    }
    ?>
    <div class="wrap">
        <h1>Events Options</h1>
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
                    <td><textarea name="registration_message" class="large-text" title="Registration Message"><?= esc_attr(stripslashes(get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE))); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Registration Message</th>
                    <td><textarea name="registration_verification_message" class="large-text" title="Registration Verification Message"><?= esc_attr(stripslashes(get_option(SSV_Events::OPTION_REGISTRATION_VERIFICATION_MESSAGE))); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Cancellation Message</th>
                    <td><textarea name="cancellation_message" class="large-text" title="cancellation Message"><?= esc_attr(stripslashes(get_option(SSV_Events::OPTION_CANCELLATION_MESSAGE))); ?></textarea></td>
                </tr>
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
            <?php SSV_General::formSecurityFields(SSV_Events::ADMIN_REFERER_OPTIONS); ?>
        </form>
    </div>
    <?php
}

add_action('admin_menu', 'ssv_add_ssv_events_options');

function ssv_events_general_options_page_content()
{
    ?><h2><a href="?page=<?= __FILE__ ?>">Events Options</a></h2><?php
}

add_action(SSV_General::HOOK_GENERAL_OPTIONS_PAGE_CONTENT, 'ssv_events_general_options_page_content');
