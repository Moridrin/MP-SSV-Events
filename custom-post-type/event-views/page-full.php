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
        Registration::createNew($event, null, $args);
        $content             = '<div class="card-panel primary">' . stripslashes(get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE)) . '</div>' . $content;
        $event_registrations = $event->getRegistrations();
    }
    #endregion

    #region Save POST Request
    if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_REGISTRATION)) {
        if ($_POST['action'] == 'register') {
            if (is_user_logged_in()) {
                $args = array();
                foreach ($event->getInputFieldNames() as $fieldName) {
                    $args[$fieldName] = $_POST[$fieldName];
                }
                Registration::createNew($event, User::getCurrent(), $args);
                $content = '<div class="card-panel primary">' . stripslashes(get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE)) . '</div>' . $content;
            } elseif (get_option(SSV_Events::OPTION_VERIFY_REGISTRATION_BY_EMAIL)) {
                $eventTitle = Event::getByID($event->getID())->post->post_title;
                $subject    = "New Registration for " . $eventTitle;
                $email      = $_POST['email'];
                $firstName  = $_POST['first_name'];
                $lastName   = $_POST['last_name'];
                $url        = get_permalink($event->getID()) . '?verification=' . $email . '&first_name=' . $firstName . '&last_name=' . $lastName;
                ob_start();
                ?>Dear <?= $firstName . ' ' . $lastName ?>lick <a href="<?= $url ?>">here</a> to verify your registration for <?= $eventTitle ?>.<?php
                wp_mail($email, $subject, ob_get_clean());
                $content = '<div class="card-panel primary">' . stripslashes(get_option(SSV_Events::OPTION_REGISTRATION_VERIFICATION_MESSAGE)) . '</div>' . $content;
            } else {
                $args = array(
                    'first_name' => $_POST['first_name'],
                    'last_name'  => $_POST['last_name'],
                    'email'      => $_POST['email'],
                );
                Registration::createNew($event, null, $args);
                $content = '<div class="card-panel primary">' . stripslashes(get_option(SSV_Events::OPTION_REGISTRATION_MESSAGE)) . '</div>' . $content;
            }
        } elseif ($_POST['action'] == 'cancel') {
            Registration::getByEventAndUser($event, new User(wp_get_current_user()))->cancel();
            $content = '<div class="card-panel primary">' . stripslashes(get_option(SSV_Events::OPTION_CANCELLATION_MESSAGE)) . '</div>' . $content;
        }
        $event_registrations = $event->getRegistrations();
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
            ?>
            <?php if (count($event_registrations) > 0): ?>
                <h3>Registrations</h3>
                <ul class="collection with-header">
                    <?php foreach ($event_registrations as $event_registration) : ?>
                        <?php /* @var Registration $event_registration */ ?>
                        <a href="#modal_<?= $event_registration->registrationID ?>">
                            <li class="collection-item avatar">
                                <img src="<?= get_avatar_url($event_registration->getMeta('email')); ?>" alt="" class="circle">
                                <span class="title"><?= $event_registration->getMeta('first_name') . ' ' . $event_registration->getMeta('last_name') ?></span>
                                <p><?= $event_registration->status ?></p>
                            </li>
                        </a>
                        <div id="modal_<?= $event_registration->registrationID ?>" class="modal">
                            <div class="modal-content">
                                <h4><?= $event_registration->getMeta('first_name') . ' ' . $event_registration->getMeta('last_name') ?></h4>
                                <p>A bunch of text</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php
    #endregion

    #region Add registration button
    if ($event->isRegistrationPossible()) {
        if ($event->canRegister()) {
            if (is_user_logged_in()) {
                ?>
                <form action="<?= get_permalink() ?>" method="POST">
                    <?php if (!$event->isRegistered()) : ?>
                        <?= $event->getRegistrationFields(); ?>
                        <?php SSV_General::formSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                    <?php else : ?>
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--primary">Cancel Registration</button>
                        <?php SSV_General::formSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                    <?php endif; ?>
                </form>
                <?php
            } else {
                ?>
                <form action="<?= get_permalink() ?>" method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="input-field">
                        <input type="text" id="first_name" name="first_name" class="validate" required>
                        <label for="first_name">First Name <span class="required">*</span></label>
                    </div>
                    <div class="input-field">
                        <input type="text" id="last_name" name="last_name" class="validate" required>
                        <label for="last_name">Last Name <span class="required">*</span></label>
                    </div>
                    <div class="input-field">
                        <input type="email" id="email" name="email" class="validate" required>
                        <label for="email">Email <span class="required">*</span></label>
                    </div>
                    <button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--primary">Register</button>
                    <?php SSV_General::formSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                </form>
                <?php
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

#region Custom Expert Length (disabled)
function mp_ssv_custom_excerpt_length($length)
{
    global $post;
    if ($post->post_type != 'events') {
        return $length;
    }
    return 200;
}

//add_filter('excerpt_length', 'mp_ssv_custom_excerpt_length', 999);
#endregion
