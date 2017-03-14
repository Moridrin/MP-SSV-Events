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
            $content .= '<a href="' . esc_url(get_permalink($post->ID)) . '">View Event</a>';
        }
        return $content;
    }
    #endregion

    #region Update Registration Status
    if (current_user_can('manage_events')) { //TODO make this work
        if (isset($_GET['approve'])) {
            Registration::getByID(SSV_General::sanitize($_GET['approve']))->approve();
            SSV_General::redirect(get_permalink());
        } elseif (isset($_GET['deny'])) {
            Registration::getByID(SSV_General::sanitize($_GET['deny']))->deny();
            SSV_General::redirect(get_permalink());
        }
    }
    #endregion

    #region Save POST Request
    if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_REGISTRATION)) {
        if ($_POST['action'] == 'register') {
            $form = Form::fromDatabase();
            if (!is_user_logged_in()) {
                $form->addFields(Registration::getDefaultFields(), false);
            }
            $form->setValues($_POST);
            $response = $form->isValid();
            if ($response === true) {
                $response = Registration::createNew($event, User::getCurrent(), $form->getInputFields());
            }
            if (is_array($response)) {
                /** @var Message $error */
                foreach ($response as $error) {
                    $content = $error->getHTML() . $content;
                }
            } else {
                $content = '<div class="card-panel primary">' . esc_html(get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE)) . '</div>' . $content;
            }
        } elseif ($_POST['action'] == 'cancel') {
            Registration::getByEventAndUser($event, new User(wp_get_current_user()))->cancel();
            $content = '<div class="card-panel primary">' . esc_html(get_option(SSV_Events::OPTION_CANCELLATION_MESSAGE)) . '</div>' . $content;
        }
        $event_registrations = $event->getRegistrations();
    }
    #endregion

    #region Page Content
    ob_start();
    ?>
    <div class="row">
        <div class="col s12 <?= count($event_registrations) > 0 ? 'xl3' : 'xl4' ?>">
            <h3>When</h3>
            <div class="row" style="border-left: solid; margin-left: 0; margin-right: 0;">
                <?php if ($event->getEnd() != false && $event->getEnd() != $event->getStart()): ?>
                    <div class="col s3">From:</div>
                    <div class="col s9"><?= esc_html($event->getStart()) ?></div>
                    <div class="col s3">Till:</div>
                    <div class="col s9"><?= esc_html($event->getEnd()) ?></div>
                <?php else : ?>
                    <div class="col s3">Start:</div>
                    <div class="col s9"><?= esc_html($event->getStart()) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col s12 <?= count($event_registrations) > 0 ? 'xl6' : 'xl8' ?>">
            <?= $content ?>
        </div>
        <?php if (count($event_registrations) > 0): ?>
            <div class="col s12 xl3">
                <?php $event->showRegistrations(); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    #endregion

    #region Add registration button
    if ($event->isRegistrationPossible()) {
        if ($event->canRegister()) {
            if (is_user_logged_in() && $event->isRegistered()) {
                ?>
                <form action="<?= esc_url(get_permalink()) ?>" method="POST">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" name="submit" class="btn waves-effect">Cancel Registration</button>
                    <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                </form>
                <?php
            } else {
                $event->showRegistrationForm();
            }
        } else {
            ?>
            <a href="<?= SSV_General::getLoginURL() ?>" class="btn waves-effect waves-light">Login to Register</a>
            <?php
        }
    }
    $content = ob_get_clean();
    #endregion

    return $content;
}

add_filter('the_content', 'mp_ssv_events_add_registrations_to_content');
#endregion
