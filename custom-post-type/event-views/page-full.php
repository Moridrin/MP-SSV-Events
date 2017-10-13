<?php
use mp_ssv_events\models\Event;
use mp_ssv_events\models\Registration;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\Form;
use mp_ssv_general\Message;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

#region Add Registrations to Content
function mp_ssv_events_add_registrations_to_content($content)
{
    #region Init
    global $post;
    if ($post->post_type != 'events') {
        return $content;
    }
    $event               = Event::getByID($post->ID);
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
    if (current_user_can(SSV_Events::CAPABILITY_MANAGE_EVENTS) && (isset($_GET['approve']) || isset($_GET['deny']))) {
        if (isset($_GET['approve'])) {
            Registration::getByID(SSV_General::sanitize($_GET['approve'], 'int'))->approve();
        } else {
            Registration::getByID(SSV_General::sanitize($_GET['deny'], 'int'))->deny();
        }
        SSV_General::redirect(get_permalink());
    }
    #endregion

    #region Save POST Request
    if (SSV_General::isValidPOST(SSV_Events::ADMIN_REFERER_REGISTRATION)) {
        if ($_POST['action'] == 'register') {
            $form = Form::fromDatabase(SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS);
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
    }
    #endregion

    return $content;
}

function mp_ssv_events_event_template($single) {
    global $post;

    if ($post->post_type == "events"){
        if(file_exists(ABSPATH . '/wp-content/plugins/ssv-events/custom-post-type/event-views/event-details.php'))
            return ABSPATH . '/wp-content/plugins/ssv-events/custom-post-type/event-views/event-details.php';
    }
    return $single;
}

add_filter('the_content', 'mp_ssv_events_add_registrations_to_content');

add_filter('single_template', 'mp_ssv_events_event_template');


#endregion
