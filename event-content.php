<?php
function ssv_add_event_content($content)
{
    global $post;
    $user_registered      = false;
    $registration_message = "";
    /* Return */
    if ($post->post_type != 'events') {
        return $content;
    }
    $event = Event::get_by_id($post->ID);

    #region Save POST Request
    if (isset($_POST['submit']) && check_admin_referer('ssv_events_register_for_event')) {
        if ($_POST['action'] == 'register') {
            if (is_user_logged_in()) {
                Registration::createNew(get_the_ID(), new FrontendMember(wp_get_current_user()));
            } else {
                Registration::createNew(get_the_ID(), null, $_POST['first_name'], $_POST['last_name'], $_POST['email']);
            }
            $registration_message = '<div class="mui-panel notification">' . stripslashes(
                    get_option('ssv_event_registration_message')
                ) . '</div>';
        } elseif ($_POST['action'] == 'cancel') {
            Registration::delete(get_the_ID(), new FrontendMember(wp_get_current_user()));
            $registration_message = '<div class="mui-panel notification">' . stripslashes(
                    get_option('ssv_event_cancellation_message')
                ) . '</div>';
        }
    }
    #endregion

    #region Save AJAX Request
    if (isset($_GET['register'])) {
        $member = FrontendMember::get_by_id($_GET['register']);
        if ($member->goesToEvent(get_the_ID())) {
            Registration::delete(get_the_ID(), $member);
            return 'Registered=No';
        } else {
            Registration::createNew(get_the_ID(), $member);
            return 'Registered=Yes';
        }
    }
    #endregion

    #region Content
    if (strpos($content, '<h1>') === false) {
        $content = '<h1>About</h1>' . $content;
    }

    #region Guest List
    $event_registrations = $event->getRegistrations();
    if (!empty($event_registrations)) {
        $content .= '<h1>Guest List</h1>';
        $content .= '<ul>';
        foreach ($event->getRegistrations(false) as $event_registration) {
            /* @var $event_registration Registration */
            if ($event_registration->status == 'pending') {
                $content .= '<li>';
                if ($event_registration->member != null) {
                    $content .= $event_registration->member->display_name . '<p class="note"> (pending)</p>';
                } else {
                    $content .= $event_registration->firstName . " " . $event_registration->lastName
                                . '<p class="note"> (pending)</p>';
                }
                $content .= '</li>';
            } else {
                if ($event_registration->status == 'approved') {
                    $content .= '<li>';
                    if ($event_registration->member != null) {
                        $content .= $event_registration->member->display_name;
                    } else {
                        $content .= $event_registration->firstName . " " . $event_registration->lastName;
                    }
                    $content .= '</li>';
                }
            }
            if ($event_registration->member != null && $event_registration->member->ID == get_current_user_id()) {
                $user_registered = true;
            }
        }
        $content .= '</ul>';
    }
    #endregion

    #region Registration
    if ($event->canRegister()) {
        if ($registration_message != "") {
            $content .= $registration_message;
        }
        if (is_user_logged_in()) {
            if ($user_registered) {
                $content .= '<form action="#" method="POST">';
                $content .= '<input type="hidden" name="action" value="cancel">';
                $content .= '<button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--danger btn waves-effect waves-light--small">Cancel Registration</button>';
                ob_start();
                wp_nonce_field('ssv_events_register_for_event');
                $content .= ob_get_clean();
                $content .= '</form>';
            } else {
                $content .= '<form action="#" method="POST">';
                $content .= '<input type="hidden" name="action" value="register">';
                $content .= '<button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--primary">Register</button>';
                ob_start();
                wp_nonce_field('ssv_events_register_for_event');
                $content .= ob_get_clean();
                $content .= '</form>';
            }
        } else {
            if (get_option('ssv_event_guest_registration') == 'true') {
                if (strpos($content, '<h1>') === false) {
                    $content .= '<h1>Registration</h1>';
                }
                $content .= '<form action="#" method="POST">';
                $content .= '<input type="hidden" name="action" value="register">';
                $content .= '<table class="form-table">';
                $content .= '<tr valign="top">';
                $content .= '<th scope="row">First Name</th>';
                $content .= '<td><input type="text" name="first_name"></td>';
                $content .= '</tr>';
                $content .= '<tr valign="top">';
                $content .= '<th scope="row">Last Name</th>';
                $content .= '<td><input type="text" name="last_name"></td>';
                $content .= '</tr>';
                $content .= '<tr valign="top">';
                $content .= '<th scope="row">Email</th>';
                $content .= '<td><input type="email" name="email"></td>';
                $content .= '</tr>';
                $content .= '<tr valign="top">';
                $content .= '<th scope="row"></th>';
                ob_start();
                wp_nonce_field('ssv_events_register_for_event');
                $content .= ob_get_clean();
                $content .= '<td><button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--primary">Register</button></td>';
                $content .= '</tr>';
                $content .= '</table>';
                $content .= '</form>';
            } else {
                $content .= 'You must sign in to register';
            }
        }
    }
    #endregion

    $content = ssv_get_date_time_and_location($post) . $content;
    #endregion

    return $content;
}

//add_filter('the_content', 'ssv_add_event_content');

function ssv_get_date_time_and_location($post)
{
    $event = new Event($post);
    ob_start();
    ?>
    <h1>When</h1>
    <table>
        <tr>
            <th style="padding-right: 10px;">Start:</th>
            <td style="padding-left: 0; padding-right:5px; white-space: nowrap;"
                class="mui--hidden-sm mui--hidden-md">
                <?php $event->echoStartDate(); ?>
            </td>
        </tr>
        <?php if ($event->getEndDate() != false && $event->getEndDate() != $event->getStartDate()) { ?>
            <tr>
                <th>End:</th>
                <td>
                    <?php $event->echoEndDate(); ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <a target="_blank" href="<?php echo $event->getGoogleCalendarURL(); ?>">Google Calendar</a>
    <br/>
    <a target="_blank" href="<?php echo $event->getLiveCalendarURL(); ?>">Live Calendar</a>
    <h1>Where</h1>
    <?= $event->getLocation() ?>
    <?php
    return ob_get_clean();
}

function mp_ssv_add_registrations_to_content($content)
{
    global $post;
    if ($post->post_type != 'events' || is_archive()) {
//        ssv_print($content);
        if (strpos($content, 'class="more-link"') === false) {
            $content .= '<a href="'.get_permalink($post->ID).'">View Event</a>';
        }
        return $content;
    }
    $event               = Event::get_by_id($post->ID);
    $event_registrations = $event->getRegistrations();
    ob_start();
    if (count($event_registrations) > 0) {
        ?>
        <div class="row">
            <div class="col s8">
                <?= $content ?>
            </div>
            <div class="col s4">
                <ul class="collection with-header">
                    <li class="collection-header"><h3>Registrations</h3></li>
                    <?php foreach ($event_registrations as $event_registration) : ?>
                        <?php /* @var $event_registration Registration */ ?>
                        <li class="collection-item avatar">
                            <img src="<?= get_avatar_url($event_registration->email); ?>" alt="" class="circle">
                            <span class="title"><?= $event_registration->firstName . ' ' . $event_registration->lastName ?></span>
                            <p><?= $event_registration->status ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php
    } else {
        echo $content;
    }
    return ob_get_clean();
}

add_filter('the_content', 'mp_ssv_add_registrations_to_content');

function mp_ssv_event_more_tag($more)
{
    global $post;
    if ($post->post_type != 'events') {
        return $more;
    }
    $more = 'ESAC';
    return $more;
}

add_filter('excerpt_more', 'mp_ssv_event_more_tag');

function mp_ssv_custom_excerpt_length($length)
{
    global $post;
    if ($post->post_type != 'events') {
        return $length;
    }
    return 200;
}

add_filter('excerpt_length', 'mp_ssv_custom_excerpt_length', 999);
