<?php

namespace mp_ssv_events\CustomPostTypes;

use DateTime;
use mp_ssv_events\models\Event;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\base\BaseFunctions;
use mp_ssv_general\base\SSV_Global;
use mp_ssv_general\base\User;
use mp_ssv_general\forms\SSV_Forms;
use WP_Post;
use WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

require_once 'backend-templates/tickets-table.php';
require_once 'backend-templates/ticket.php';

abstract class EventsPostType
{
    public static function archiveTemplate($archiveTemplate): string
    {
        if (is_post_type_archive('ssv_event')) {
            if (get_theme_support('materialize')) {
                $archiveTemplate = SSV_Events::PATH . '/custom-post-type/frontend-templates/materialize/archive.php';
            }
        }
        return $archiveTemplate;
    }

    public static function frontendEventTemplate($single)
    {
        global $post;

        if ($post->post_type === 'ssv_event') {
            if (current_theme_supports('materialize')) {
                return SSV_Events::PATH . 'custom-post-type/frontend-templates/materialize/event-details.php';
            }
        }
        return $single;
    }

    public static function frontendEventContentFilter(string $content): string
    {
        if (is_archive()) {
            return $content;
        }
        /** @var \wpdb $wpdb */
        global $wpdb, $post;
        if ($post->post_type === 'ssv_event') {
            if (BaseFunctions::isValidPOST(SSV_Events::TICKET_FORM_REFERER)) {
                if (is_user_logged_in()) {
                    $json = [
                        'first_name' => User::getCurrent()->first_name,
                        'last_name'  => User::getCurrent()->last_name,
                        'email'      => User::getCurrent()->user_email,
                    ];
                } else {
                    $json = [
                        'first_name' => $_POST['first_name'],
                        'last_name'  => $_POST['last_name'],
                        'email'      => $_POST['email'],
                    ];
                }
                $tableName = SSV_Events::TICKETS_TABLE;
                $ticketId  = $_POST['ticket'];
                $formId    = $wpdb->get_var("SELECT t_f_id FROM $tableName WHERE t_id = $ticketId");
                if ($formId !== -1) {
                    $tableName      = SSV_Forms::SITE_SPECIFIC_FORMS_TABLE;
                    $formFieldNames = json_decode($wpdb->get_var("SELECT f_fields FROM $tableName WHERE f_id = $formId"));
                    foreach ($formFieldNames as $formFieldName) {
                        $json[$formFieldName] = $_POST[$formFieldName];
                    }
                }
                $wpdb->insert(
                    SSV_Events::REGISTRATIONS_TABLE,
                    [
                        'r_t_id'   => $ticketId,
                        'r_userId' => User::getCurrent()->ID,
                        'r_data'   => json_encode($json),
                        'r_status' => 'pending',
                    ]
                );
            }
            if (!current_theme_supports('materialize')) {
                $start     = new DateTime(get_post_meta($post->ID, 'start', true));
                $end       = new DateTime(get_post_meta($post->ID, 'end', true));
                $postId    = $post->ID;
                $tableName = SSV_Events::TICKETS_TABLE;
                $tickets   = $wpdb->get_results("SELECT * FROM $tableName WHERE t_e_id = $postId");
                if ($tickets === null) {
                    $tickets = [];
                }
                ob_start();
                show_event($content, $start, $end, $tickets);
                $content = ob_get_clean();
            }
        }
        return $content;
    }

    public static function saveEvent(int $postId, WP_Post $postAfter): int
    {
        if (get_post_type() !== 'ssv_event') {
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
//        } elseif (empty($event->mailchimpList) && $event->isRegistrationPossible()) {
//            do_action(SSV_Global::HOOK_USERS_NEW_EVENT, $event);
        }
        return $postId;
    }

    public static function updatedMessage(array $messages): array
    {
        global $post, $post_ID;
        if (get_option(SSV_Events::OPTION_PUBLISH_ERROR, false)) {
            /** @noinspection HtmlUnknownTarget */
            $messages['ssv_event'] = array(
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
            $messages['ssv_event'] = array(
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
            'taxonomies'          => array('ssv_event_category'),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-calendar-alt',
            'show_in_nav_menus'   => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'has_archive'         => 'events',
            'query_var'           => true,
            'can_export'          => true,
            'rewrite'             => true,
            'capability_type'     => 'post',
        );

        register_post_type('ssv_event', $args);
    }

    public static function registerCategoryTaxonomy()
    {
        register_taxonomy(
            'ssv_event_category',
            'ssv_event',
            array(
                'hierarchical' => true,
                'label'        => 'Event Categories',
                'query_var'    => true,
                'rewrite'      => array(
                    'slug'       => 'ssv_event_category',
                    'with_front' => false,
                ),
            )
        );
    }

    public static function metaBoxes()
    {
        add_meta_box('ssv_event_date', 'Date', [self::class, 'dateMetaBox'], 'ssv_event', 'side', 'default');
        add_meta_box('ssv_event_tickets', 'Tickets', [self::class, 'ticketsMetaBox'], 'ssv_event', 'advanced', 'high');
        if (is_multisite()) {
            add_meta_box('ssv_event_shared', 'Share', [self::class, 'shareMetaBox'], 'ssv_event', 'side', 'default');
        }
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
        /** @var \wpdb $wpdb */
        global $wpdb, $post;
        $postId    = $post->ID;
        $tableName = SSV_Events::TICKETS_TABLE;
        $tickets   = $wpdb->get_results("SELECT * FROM $tableName WHERE t_e_id = $postId");
        if ($tickets === null) {
            $tickets = [];
        }
        show_tickets_table($tickets);
        ?>
        <div style="margin: 10px;">
            <button onclick="ticketsManager.addNew()" type="button">Add Ticket</button>
        </div>
        <?php
    }

    public static function shareMetaBox()
    {
        global $post;
        $share = get_post_meta($post->ID, 'network_share', true);
        ?>
        <input type="hidden" name="share" value="false">
        <input type="checkbox" id="network_share" name="network_share" value="true" <?= $share ? 'checked="checked"' : '' ?>>
        <label for="network_share">Share this event with other sites on the network</label><br/>
        <?php
    }

    public static function enqueueAdminScripts()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $table   = SSV_Forms::SITE_SPECIFIC_FORMS_TABLE;
        $forms   = $wpdb->get_results("SELECT f_id, f_title FROM $table");
        $formIds = array_column($forms, 'f_id');
        array_unshift($formIds, -1);
        $formTitles = array_column($forms, 'f_title');
        array_unshift($formTitles, '[none]');
        wp_enqueue_style('mp-ssv-event-edit-css', SSV_Events::URL . '/css/admin.css');
        wp_enqueue_script('mp-ssv-event-edit-js', SSV_Events::URL . '/js/event-editor.js');
        wp_localize_script(
            'mp-ssv-event-edit-js',
            'data',
            [
                'ticketsMaxId' => max($formIds),
                'formTitles'   => $formTitles,
                'formKeys'     => $formIds,
                'imageNewTab'  => SSV_Global::URL . '/images/link-new-tab-small.png',
            ]
        );
    }

    public static function saveMeta($postId)
    {
        if (!current_user_can('edit_post', $postId) || empty($_POST)) {
            return $postId;
        }
        /** @var \wpdb $wpdb */
        global $wpdb;
        $ticketIds = implode(',', $_POST['ticketIds']);
        $table     = SSV_Events::TICKETS_TABLE;
        $wpdb->query("DELETE FROM $table WHERE t_e_id = '$postId' AND t_id NOT IN ($ticketIds)");
        foreach ($_POST['ticketIds'] as $id) {
            $ticket = json_decode(stripslashes($_POST['ticket_' . $id]));
            $wpdb->replace(
                SSV_Events::TICKETS_TABLE,
                [
                    't_id'    => $id,
                    't_e_id'  => $postId,
                    't_title' => $ticket->title,
                    't_start' => $ticket->dateTimeStart,
                    't_end'   => $ticket->dateTimeEnd,
                    't_price' => $ticket->price,
                    't_f_id'  => $ticket->form,
                ]
            );
        }
        update_post_meta($postId, 'start', BaseFunctions::sanitize($_POST['start'], 'datetime'));
        update_post_meta($postId, 'end', BaseFunctions::sanitize($_POST['end'], 'datetime'));
        update_post_meta($postId, 'location', BaseFunctions::sanitize($_POST['location'], 'text'));
        return $postId;
    }

    public static function publishDateFilter($the_date, $d, WP_Post $post)
    {
        if (!is_archive() || !is_post_type_archive('ssv_event')) {
            return $the_date;
        }
        $startDate = new DateTime(get_post_meta($post->ID, 'start', true));
        $endDate   = new DateTime(get_post_meta($post->ID, 'end', true));
        if ($startDate->format('Y-m-d') === $endDate->format('Y-m-d')) {
            $date = $startDate->format(get_option('date_format')) . ' ' . $startDate->format(get_option('time_format'));
            if ($startDate->format('H:i') !== $endDate->format('H:i')) {
                $date .= ' - ' . $endDate->format(get_option('time_format'));
            }
        } else {
            $date = $startDate->format(get_option('date_format')) . ' ' . $startDate->format(get_option('time_format')) . ' - ' . $endDate->format(get_option('date_format')) . ' ' . $endDate->format(get_option('time_format'));
        }
        return $date;
    }

    public static function customizeSelectQuery(WP_Query $query)
    {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('ssv_event')) {
            $query->set('meta_key', 'start');
            $query->set('orderby', 'meta_value');
            $query->set('groupby', 'meta_value');
        }
    }
}

add_filter('archive_template', [EventsPostType::class, 'archiveTemplate']);
add_action('single_template', [EventsPostType::class, 'frontendEventTemplate']);
add_filter('the_content', [EventsPostType::class, 'frontendEventContentFilter']);
add_action('save_post', [EventsPostType::class, 'saveEvent'], 10, 2);
add_filter('post_updated_messages', [EventsPostType::class, 'updatedMessage']);
add_action('init', [EventsPostType::class, 'registerPostType']);
add_action('init', [EventsPostType::class, 'registerCategoryTaxonomy']);
add_action('add_meta_boxes', [EventsPostType::class, 'metaBoxes']);
add_action('save_post_ssv_event', [EventsPostType::class, 'saveMeta']);
add_action('admin_enqueue_scripts', [EventsPostType::class, 'enqueueAdminScripts']);
add_filter('get_the_date', [EventsPostType::class, 'publishDateFilter'], 10, 3);
add_action('pre_get_posts', [EventsPostType::class, 'customizeSelectQuery']);
