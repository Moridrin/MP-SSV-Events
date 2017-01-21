<?php
#region Add Registrations to Content
function mp_ssv_events_add_registrations_to_content($content)
{
    #region Init
    global $post;
    if ($post->post_type != 'events') {
        return $content;
    }
    $event               = Event::getByID($post->ID);
    $event_registrations = $event->getRegistrations();
    #endregion

    #region Add 'View Event' Link to Archive
    if ($post->post_type == 'events' && is_archive()) {
        if (strpos($content, 'class="more-link"') === false) {
            $content .= '<a href="' . get_permalink($post->ID) . '">View Event</a>';
        }
        return $content;
    }
    #endregion

    #region Confirm email
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['verification'])) {
        $args = array(
            'email'      => $_GET['email'],
            'first_name' => $_GET['first_name'],
            'last_name'  => $_GET['last_name'],
        );
        if (Registration::createNew($event, null, $args) != null) {
            $content = '<div class="card-panel primary">' . get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE) . '</div>' . $content;
        } else {
            $content = '<div class="card-panel warning">Already registered.</div>' . $content;
        }
        $event_registrations = $event->getRegistrations();
    }
    #endregion

    #region Update Registration Status
    if (is_user_logged_in() && User::getCurrent()->isBoard()) {
        if (isset($_GET['approve'])) {
            Registration::getByID($_GET['approve'])->approve();
            SSV_General::redirect(get_permalink());
        } elseif (isset($_GET['deny'])) {
            Registration::getByID($_GET['deny'])->deny();
            SSV_General::redirect(get_permalink());
        }
    }
    #endregion

    #region Save POST Request
    if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_REGISTRATION)) {
        if ($_POST['action'] == 'register') {
            $customFields = is_user_logged_in() ? array() : Registration::getDefaultFields($_POST);
            $fieldIDs     = get_post_meta($post->ID, 'event_registration_field_ids', true);
            foreach ($fieldIDs as $fieldID) {
                $field = Field::fromJSON(get_post_meta($post->ID, 'event_registration_fields_' . $fieldID, true));
                if ($field instanceof InputField) {
                    /** @var InputField $field */
                    $field->value               = SSV_General::sanitize($_POST[$field->name]);
                    $customFields[$field->name] = $field;
                }
            }
            Registration::createNew($event, User::getCurrent(), $customFields);
            $content = '<div class="card-panel primary">' . get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE) . '</div>' . $content;
        } elseif ($_POST['action'] == 'cancel') {
            Registration::getByEventAndUser($event, new User(wp_get_current_user()))->cancel();
            $content = '<div class="card-panel primary">' . get_option(SSV_Events::OPTION_CANCELLATION_MESSAGE) . '</div>' . $content;
        }
        SSV_General::redirect(get_permalink());
    }
    #endregion

    #region Page Content
    ob_start();
    ?>
    <div class="row">
        <div class="col s8">
            <?= $content ?>
        </div>
        <div class="col s4">
            <h3>When</h3>
            <div class="row" style="border-left: solid; margin-left: 0; margin-right: 0;">
                <?php if ($event->getEnd() != false && $event->getEnd() != $event->getStart()): ?>
                    <div class="col s3">From:</div>
                    <div class="col s9"><?= $event->getStart() ?></div>
                    <div class="col s3">Till:</div>
                    <div class="col s9"><?= $event->getEnd() ?></div>
                <?php else : ?>
                    <div class="col s3">Start:</div>
                    <div class="col s9"><?= $event->getStart() ?></div>
                <?php endif; ?>
            </div>
            <?php
            if (count($event_registrations) > 0) {
                $event->showRegistrations();
            }
            ?>
        </div>
    </div>
    <?php
    #endregion

    #region Add registration button
    if ($event->isRegistrationPossible()) {
        if ($event->canRegister()) {
            if (is_user_logged_in() && $event->isRegistered()) {
                ?>
                <form action="<?= get_permalink() ?>" method="POST">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--primary">Cancel Registration</button>
                    <?php SSV_General::formSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                </form>
                <?php
            } else {
                $event->showRegistrationForm();
            }
        } else {
            ?>
            <a href="/login" class="btn waves-effect waves-light">Login to Register</a>
            <?php
        }
    }
    $content = ob_get_clean();
    #endregion

    return $content;
}

add_filter('the_content', 'mp_ssv_events_add_registrations_to_content');
#endregion
