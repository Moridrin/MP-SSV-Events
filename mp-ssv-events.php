<?php
/**
 * Plugin Name: SSV Events
 * Plugin URI: http://moridrin.com/mp-ssv-events
 * Description: SSV Events is a plugin that allows you to create events for the Students Sports Club and allows all members from that club to join the event.
 * Version: 1.0
 * Author: Jeroen Berkvens
 * Author URI: http://nl.linkedin.com/in/jberkvens/
 * License: WTFPL
 * License URI: http://www.wtfpl.net/txt/copying/
 */

require_once 'general/general.php';

require_once "models/Event.php";
require_once "models/Registration.php";
require_once "profile-content.php";
require_once "location-widget.php";
require_once "category-widget.php";
require_once "post-type.php";
require_once "event-content.php";
require_once "options/options.php";

function mp_ssv_register_mp_ssv_events()
{
    /* Database */
    global $wpdb;
    /** @noinspection PhpIncludeInspection */
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . "mp_ssv_event_registration";
    $sql
        = "
		CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			userID bigint(20),
			eventID bigint(20) NOT NULL,
			status text NOT NULL,
			first_name varchar(30),
			last_name varchar(30),
			email varchar(30),
			UNIQUE KEY id (id)
		) $charset_collate;";
    $wpdb->query($sql);
}

register_activation_hook(__FILE__, 'mp_ssv_register_mp_ssv_events');

function mp_ssv_unregister_mp_ssv_events()
{
    //Nothing to do here.
}

register_deactivation_hook(__FILE__, 'mp_ssv_unregister_mp_ssv_events');

function mp_ssv_uninstall_mp_ssv_events()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "mp_ssv_event_registration";
    $sql = "DROP TABLE IF_EXISTS $table_name;";
    $wpdb->query($sql);
}

register_uninstall_hook(__FILE__, 'mp_ssv_uninstall_mp_ssv_events');

function mp_ssv_events_template($archive_template)
{
    if (is_post_type_archive('events')) {
        $archive_template = dirname(__FILE__) . '/archive-events.php';
    }
    return $archive_template;
}

add_filter('archive_template', 'mp_ssv_events_template');

function mp_ssv_save_event(
    $post_ID,
    $post_after,
    /** @noinspection PhpUnusedParameterInspection */
    $post_before
) {
    if (get_post_type() != 'events') {
        return $post_ID;
    }
    $event = new Event($post_after);
    if (!$event->isValid() && $event->isPublished()) {
        $updateArguments = array();
        $updateArguments['ID'] = $post_ID;
        $updateArguments['post_status'] = 'draft';
        wp_update_post($updateArguments);
        update_option('mp_ssv_is_publish_error', 1);
    }
    return $post_ID;
}

add_action('save_post', 'mp_ssv_save_event', 10, 3);

function mp_ssv_events_admin_notice()
{
    $screen = get_current_screen();
    if ('events' != $screen->post_type || 'post' != $screen->base) {
        return;
    }
    $publish_error = get_option('mp_ssv_is_publish_error', true);
    $save_notice = get_option('mp_ssv_is_save_warning', true);
    if ($publish_error) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('You cannot publish an event without a start date and time!', 'mp-ssv'); ?></p>
        </div>
        <?php
    } elseif ($save_notice) {
        ?>
        <div class="notice notice-warning">
            <p><?php _e('You cannot publish an event without a start date and time!', 'mp-ssv'); ?></p>
        </div>
        <?php
    }
    update_option('mp_ssv_is_publish_error', 0);
    update_option('mp_ssv_is_save_warning', 0);
}

add_action('admin_notices', 'mp_ssv_events_admin_notice');

function mp_ssv_events_updated_messages($messages)
{
    global $post, $post_ID;
    $publish_error = get_option('mp_ssv_is_publish_error', true);
    if ($publish_error) {

        $messages['events'] = array(
            0  => '',
            1  => sprintf(__('Event updated. <a href="%s">View Event</a>'), esc_url(get_permalink($post_ID))),
            2  => __('Custom field updated.'),
            3  => __('Custom field deleted.'),
            4  => __('Event updated.'),
            /* translators: %s: date and time of the revision */
            5  => isset($_GET['revision']) ? sprintf(
                __('Event restored to revision from %s'), wp_post_revision_title((int)$_GET['revision'], false)
            ) : false,
            6  => '', //Send a blank string to prevent it from posting that it has been published correctly.
            7  => __('Event saved.'),
            8  => sprintf(
                __('Event submitted. <a target="_blank" href="%s">Preview event</a>'),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
            9  => sprintf(
                __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'),
                // translators: Publish box date format, see http://php.net/date
                date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))
            ),
            10 => sprintf(
                __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
        );
    } else {
        $messages['events'] = array(
            0  => '',
            1  => sprintf(__('Event updated. <a href="%s">View Event</a>'), esc_url(get_permalink($post_ID))),
            2  => __('Custom field updated.'),
            3  => __('Custom field deleted.'),
            4  => __('Event updated.'),
            /* translators: %s: date and time of the revision */
            5  => isset($_GET['revision']) ? sprintf(
                __('Event restored to revision from %s'), wp_post_revision_title((int)$_GET['revision'], false)
            ) : false,
            6  => sprintf(__('Event published. <a href="%s">View event</a>'), esc_url(get_permalink($post_ID))),
            7  => __('Event saved.'),
            8  => sprintf(
                __('Event submitted. <a target="_blank" href="%s">Preview event</a>'),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
            9  => sprintf(
                __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'),
                // translators: Publish box date format, see http://php.net/date
                date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)), esc_url(get_permalink($post_ID))
            ),
            10 => sprintf(
                __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
        );
    }

    return $messages;
}

add_filter('post_updated_messages', 'mp_ssv_events_updated_messages');
