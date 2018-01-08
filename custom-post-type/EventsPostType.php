<?php

namespace mp_ssv_events\CustomPostTypes;

use DateTime;
use mp_ssv_events\models\Event;
use mp_ssv_events\models\Registration;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\base\BaseFunctions;
use mp_ssv_general\base\SSV_Global;
use WP_Post;

if (!defined('ABSPATH')) {
    exit;
}

abstract class EventsPostType
{
    public static function archiveTemplate($archiveTemplate): string
    {
        if (is_post_type_archive('events') && get_theme_support('materialize')) {
            $archiveTemplate = SSV_Events::PATH . '/custom-post-type/archive-events.php';
        }
        return $archiveTemplate;
    }

    public static function saveEvent(int $postId, WP_Post $postAfter): int
    {
        if (get_post_type() != 'events') {
            return $postId;
        }
        $event = new Event($postAfter);
        if ($event->isPublished() && !$event->isValid()) {
            wp_update_post(
                array(
                    'ID'          => $postId,
                    'post_status' => 'draft',
                )
            );
            update_option(SSV_Events::OPTION_PUBLISH_ERROR, true);
        } elseif (empty($event->mailchimpList) && $event->isRegistrationPossible()) {
            do_action(SSV_Global::HOOK_USERS_NEW_EVENT, $event);
        }
        return $postId;
    }

    public static function updatedMessage(array $messages): array
    {
        global $post, $post_ID;
        if (get_option(SSV_Events::OPTION_PUBLISH_ERROR, false)) {
            /** @noinspection HtmlUnknownTarget */
            $messages['events'] = array(
                0  => '',
                1  => sprintf('Event updated. <a href="%s">View Event</a>', esc_url(get_permalink($post_ID))),
                2  => 'Custom field updated.',
                3  => 'Custom field deleted.',
                4  => 'Event updated.',
                5  => isset($_GET['revision']) ? 'Event restored to revision from ' . wp_post_revision_title((int)$_GET['revision'], false) : false,
                6  => '',
                7  => 'Event saved.',
                8  => sprintf('Event submitted. <a target="_blank" href="%s">Preview event</a>', esc_url(add_query_arg('preview', 'true', esc_url(get_permalink($post_ID))))),
                9  => sprintf('Event scheduled for: <strong>' . strtotime($post->post_date) . '</strong>. <a target="_blank" href="%s">Preview event</a>', esc_url(get_permalink($post_ID))),
                10 => sprintf(
                    'Event draft updated. <a target="_blank" href="%s">Preview event</a>',
                    esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
                ),
            );
        } else {
            /** @noinspection HtmlUnknownTarget */
            $messages['events'] = array(
                0  => '',
                1  => sprintf('Event updated. <a href="%s">View Event</a>', esc_url(get_permalink($post_ID))),
                2  => 'Custom field updated.',
                3  => 'Custom field deleted.',
                4  => 'Event updated.',
                5  => isset($_GET['revision']) ? 'Event restored to revision from ' . wp_post_revision_title((int)$_GET['revision'], false) : false,
                6  => sprintf('Event published. <a href="%s">View event</a>', esc_url(get_permalink($post_ID))),
                7  => 'Event saved.',
                8  => sprintf('Event submitted. <a target="_blank" href="%s">Preview event</a>', esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
                9  => sprintf('Event scheduled for: <strong>' . strtotime($post->post_date) . '</strong>. <a target="_blank" href="%s">Preview event</a>', esc_url(get_permalink($post_ID))),
                10 => sprintf('Event draft updated. <a target="_blank" href="%s">Preview event</a>', esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
            );
        }

        return $messages;
    }

    public static function registerPostType()
    {
        $labels = array(
            'name'               => 'Events',
            'singular_name'      => 'Event',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Event',
            'edit_item'          => 'Edit Event',
            'new_item'           => 'New Event',
            'view_item'          => 'View Event',
            'search_items'       => 'Search Events',
            'not_found'          => 'No Events found',
            'not_found_in_trash' => 'No Events found in Trash',
            'parent_item_colon'  => 'Parent Event:',
            'menu_name'          => 'Events',
        );

        $args = array(
            'labels'              => $labels,
            'hierarchical'        => true,
            'description'         => 'Events filterable by category',
            'supports'            => array('title', 'editor', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes'),
            'taxonomies'          => array('event_category'),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-calendar-alt',
            'show_in_nav_menus'   => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'has_archive'         => true,
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => true,
            'capability_type'     => 'post',
        );

        register_post_type('events', $args);
    }

    public static function registerCategoryTaxonomy()
    {
        register_taxonomy(
            'event_category',
            'events',
            array(
                'hierarchical' => true,
                'label'        => 'Event Categories',
                'query_var'    => true,
                'rewrite'      => array(
                    'slug'       => 'event_category',
                    'with_front' => false,
                ),
            )
        );
    }

    public static function metaBoxes()
    {
        add_meta_box('ssv_events_date', 'Date', [self::class, 'dateMetaBox'], 'events', 'side', 'default');
        add_meta_box('ssv_events_tickets', 'Tickets', [self::class, 'ticketsMetaBox'], 'events', 'advanced', 'high');
    }

    public static function dateMetaBox()
    {
        global $post;
        $start       = get_post_meta($post->ID, 'start', true);
        $start       = $start ?: get_post_meta($post->ID, 'start_date', true) . ' ' . get_post_meta($post->ID, 'start_time', true);
        $end         = get_post_meta($post->ID, 'end', true);
        $end         = $end ?: get_post_meta($post->ID, 'end_date', true) . ' ' . get_post_meta($post->ID, 'end_time', true);
        $placeholder = (new DateTime('now'))->format('Y-m-d H:i');
        ?>
        Event Start<br/>
        <input type="text" class="datetimepicker" name="start" value="<?= esc_html($start) ?>" placeholder="<?= esc_html($placeholder) ?>" title="Start Date" required><br/>
        Event End<br/>
        <input type="text" class="datetimepicker" name="end" value="<?= esc_html($end) ?>" placeholder="<?= esc_html($placeholder) ?>" title="End Date" required>
        <?php
    }

    public static function ticketsMetaBox()
    {
        global $post;
        ?>
        <button type="button">Add Ticket</button>
        <?php
    }

    public static function saveMeta($postId)
    {
        if (!current_user_can('edit_post', $postId)) {
            return $postId;
        }
        $i = 0;
        while (isset($_POST[$i . '_post'])) {
            $registration = Registration::getByID($_POST[$i . '_registrationID']);
            $statusNew    = SSV_General::sanitize($_POST[$i . '_status'], array('pending', 'approved', 'denied'));
            if ($registration->status == $statusNew) {
                $i++;
                continue;
            }
            switch ($statusNew) {
                case Registration::STATUS_PENDING:
                    $registration->makePending();
                    break;
                case Registration::STATUS_APPROVED:
                    $registration->approve();
                    break;
                case Registration::STATUS_DENIED:
                    $registration->deny();
                    break;
            }
            $i++;
        }
        if (isset($_POST['registration'])) {
            update_post_meta($postId, 'registration', SSV_General::sanitize($_POST['registration'], array('disabled', 'members_only', 'everyone',)));
        }
        if (isset($_POST['start'])) {
            update_post_meta($postId, 'start', SSV_General::sanitize($_POST['start'], 'datetime'));
        }
        if (isset($_POST['end'])) {
            update_post_meta($postId, 'end', SSV_General::sanitize($_POST['end'], 'datetime'));
        }
        if (isset($_POST['location'])) {
            update_post_meta($postId, 'location', SSV_General::sanitize($_POST['location'], 'text'));
        }

        Form::saveEditorFromPost();
        return $postId;
    }
}

add_filter('archive_template', [EventsPostType::class, 'archiveTemplate']);
add_action('save_post', [EventsPostType::class, 'saveEvent'], 10, 2);
add_filter('post_updated_messages', [EventsPostType::class, 'updatedMessage']);
add_action('init', [EventsPostType::class, 'registerPostType']);
add_action('init', [EventsPostType::class, 'registerCategoryTaxonomy']);
add_action('add_meta_boxes', [EventsPostType::class, 'metaBoxes']);
add_action('save_post_events', [EventsPostType::class, 'saveMeta']);