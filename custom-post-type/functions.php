<?php
/**
 * Created by PhpStorm.
 * User: jeroen
 * Date: 9-12-16
 * Time: 16:02
 */

function mp_ssv_events_template($archive_template)
{
    if (is_post_type_archive('events')) {
        $archive_template = plugin_dir_path(__FILE__) . '/archive-events.php';
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
        $updateArguments                = array();
        $updateArguments['ID']          = $post_ID;
        $updateArguments['post_status'] = 'draft';
        wp_update_post($updateArguments);
        update_option('ssv_is_publish_error', 1);
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
    $publish_error = get_option('ssv_is_publish_error', true);
    $save_notice   = get_option('ssv_is_save_warning', true);
    if ($publish_error) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('You cannot publish an event without a start date and time!', 'ssv'); ?></p>
        </div>
        <?php
    } elseif ($save_notice) {
        ?>
        <div class="notice notice-warning">
            <p><?php _e('You cannot publish an event without a start date and time!', 'ssv'); ?></p>
        </div>
        <?php
    }
    update_option('ssv_is_publish_error', 0);
    update_option('ssv_is_save_warning', 0);
}

add_action('admin_notices', 'mp_ssv_events_admin_notice');

function mp_ssv_events_updated_messages($messages)
{
    global $post, $post_ID;
    $publish_error = get_option('ssv_is_publish_error', true);
    if ($publish_error) {

        $messages['events'] = array(
            0  => '',
            1  => sprintf(__('Event updated. <a href="%s">View Event</a>'), esc_url(get_permalink($post_ID))),
            2  => __('Custom field updated.'),
            3  => __('Custom field deleted.'),
            4  => __('Event updated.'),
            /* translators: %s: date and time of the revision */
            5  => isset($_GET['revision']) ? sprintf(
                __('Event restored to revision from %s'),
                wp_post_revision_title((int)$_GET['revision'], false)
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
                date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)),
                esc_url(get_permalink($post_ID))
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
                __('Event restored to revision from %s'),
                wp_post_revision_title((int)$_GET['revision'], false)
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
                date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)),
                esc_url(get_permalink($post_ID))
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