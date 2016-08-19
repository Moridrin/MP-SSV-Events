<?php
function ssv_add_event_content($content)
{
    global $post;
    $user_registered = false;
    $registration_message = "";
    /* Return */
    if ($post->post_type != 'events') {
        return $content;
    }
    $event = Event::get_by_id($post->ID);

    #region Save POST
    if (isset($_POST['submit'])) {
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
            if ($event_registration->member->ID == get_current_user_id()) {
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
                $content .= '<button type="submit" name="submit" class="mui-btn mui-btn--danger mui-btn--small">Cancel Registration</button>';
                $content .= '</form>';
            } else {
                $content .= '<form action="#" method="POST">';
                $content .= '<input type="hidden" name="action" value="register">';
                $content .= '<button type="submit" name="submit" class="mui-btn mui-btn--primary">Register</button>';
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
                $content .= '<td><button type="submit" name="submit" class="mui-btn mui-btn--primary">Register</button></td>';
                $content .= '</tr>';
                $content .= '</table>';
                $content .= '</form>';
            } else {
                $content .= 'You must sign in to register';
            }
        }
    }
    #endregion

    $content = ssv_get_date_and_time($post) . $content;
    #endregion

    return $content;
}

add_filter('the_content', 'ssv_add_event_content');


function ssv_get_date_and_time($post)
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
    <?php
    return ob_get_clean();
}