<?php
//namespace mp_ssv_events;
use mp_ssv_events\models\Event;
use mp_ssv_events\models\Registration;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\Form;
use mp_ssv_general\SSV_General;

if (!defined('ABSPATH')) {
    exit;
}
#region Template
/**
 * This function sets the correct template file for events.
 *
 * @param $archive_template
 *
 * @return string
 */
function mp_ssv_events_template($archive_template)
{
    if (is_post_type_archive('events') && get_theme_support('materialize')) {
        $archive_template = SSV_Events::PATH . '/custom-post-type/archive-events.php';
    }
    return $archive_template;
}

add_filter('archive_template', 'mp_ssv_events_template');
#endregion

#region Save Event
/**
 * @param $post_ID
 * @param $post_after
 *
 * @return mixed
 */
function mp_ssv_events_save($post_ID, $post_after)
{
    if (get_post_type() != 'events') {
        return $post_ID;
    }
    $event = new Event($post_after);
    if ($event->isPublished() && !$event->isValid()) {
        wp_update_post(
            array(
                'ID'          => $post_ID,
                'post_status' => 'draft',
            )
        );
        update_option(SSV_Events::OPTION_PUBLISH_ERROR, true);
    } elseif (empty($event->mailchimpList) && $event->isRegistrationPossible()) {
        do_action(SSV_General::HOOK_USERS_NEW_EVENT, $event);
    }
    return $post_ID;
}

add_action('save_post', 'mp_ssv_events_save', 10, 2);
#endregion

#region Admin Notice
/**
 * This function displays the error message thrown by the Save or Update actions of an Event.
 */
function mp_ssv_events_admin_notice()
{
    $screen = get_current_screen();
    if ('events' != $screen->post_type || 'post' != $screen->base) {
        return;
    }
    if (get_option(SSV_Events::OPTION_PUBLISH_ERROR, false)) {
        ?>
        <div class="notice notice-error">
            <p>You cannot publish an event without a start date and time!</p>
        </div>
        <?php
    }
    update_option(SSV_Events::OPTION_PUBLISH_ERROR, false);
}

add_action('admin_notices', 'mp_ssv_events_admin_notice');
#endregion

#region Updated Messages
/**
 * @param string[] $messages is an array of messages displayed after an event is updated.
 *
 * @return string[] the messages.
 */
function mp_ssv_events_updated_messages($messages)
{
    global $post, $post_ID;
    if (get_option(SSV_Events::OPTION_PUBLISH_ERROR, false)) {
        /** @noinspection HtmlUnknownTarget */
        $messages['events'] = array(
            0  => '',
            1  => 'Event updated. <a href="' . esc_url(get_permalink($post_ID)) . '">View Event</a>',
            2  => 'Custom field updated.',
            3  => 'Custom field deleted.',
            4  => 'Event updated.',
            5  => isset($_GET['revision']) ? 'Event restored to revision from ' . wp_post_revision_title((int)$_GET['revision'], false) : false,
            6  => '',
            7  => 'Event saved.',
            8  => 'Event submitted. <a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', esc_url(get_permalink($post_ID)))) . '">Preview event</a>',
            9  => 'Event scheduled for: <strong>' . strtotime($post->post_date) . '</strong>. <a target="_blank" href="' . esc_url(get_permalink($post_ID)) . '">Preview event</a>',
            10 => 'Event draft updated. <a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) . '">Preview event</a>',
        );
    } else {
        /** @noinspection HtmlUnknownTarget */
        $messages['events'] = array(
            0  => '',
            1  => 'Event updated. <a href="' . esc_url(get_permalink($post_ID)) . '">View Event</a>',
            2  => 'Custom field updated.',
            3  => 'Custom field deleted.',
            4  => 'Event updated.',
            5  => isset($_GET['revision']) ? 'Event restored to revision from ' . wp_post_revision_title((int)$_GET['revision'], false) : false,
            6  => 'Event published. <a href="' . esc_url(get_permalink($post_ID)) . '">View event</a>',
            7  => 'Event saved.',
            8  => 'Event submitted. <a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) . '">Preview event</a>',
            9  => 'Event scheduled for: <strong>' . strtotime($post->post_date) . '</strong>. <a target="_blank" href="' . esc_url(get_permalink($post_ID)) . '">Preview event</a>',
            10 => 'Event draft updated. <a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', get_permalink($post_ID))) . '">Preview event</a>',
        );
    }

    return $messages;
}

add_filter('post_updated_messages', 'mp_ssv_events_updated_messages');
#endregion

#region Post Category
/**
 * This method initializes the post category functionality for Events
 */
function mp_ssv_events_post_category()
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

add_action('init', 'mp_ssv_events_post_category');
#endregion

#region Category Taxonomy
/**
 * This function registers a taxonomy for the categories.
 */
function mp_ssv_events_category_taxonomy()
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

add_action('init', 'mp_ssv_events_category_taxonomy');
#endregion

#region Meta Boxes
function mp_ssv_edit_form_after_title()
{
    global $post, $wp_meta_boxes;
    do_meta_boxes('events', 'ssv_after_title', $post);
    unset($wp_meta_boxes['events']['ssv_after_title']);
}

add_action('edit_form_after_title', 'mp_ssv_edit_form_after_title');

/**
 * This method adds the custom Meta Boxes
 */
function mp_ssv_events_meta_boxes()
{
    add_meta_box('ssv_events_event_details', 'Event Details', 'ssv_events_details', 'events', 'ssv_after_title', 'high');
    add_meta_box('ssv_events_tickets', 'Tickets', 'ssv_events_tickets', 'events', 'normal', 'high');
}

add_action('add_meta_boxes', 'mp_ssv_events_meta_boxes');

function ssv_events_details()
{
    global $post;
    $start       = get_post_meta($post->ID, 'start', true);
    $start       = $start ?: get_post_meta($post->ID, 'start_date', true) . ' ' . get_post_meta($post->ID, 'start_time', true);
    $end         = get_post_meta($post->ID, 'end', true);
    $end         = $end ?: get_post_meta($post->ID, 'end_date', true) . ' ' . get_post_meta($post->ID, 'end_time', true);
    $placeholder = (new DateTime('now'))->format('Y-m-d H:i');
    $location    = get_post_meta($post->ID, 'location', true);
    ?>
    <table width="100%">
        <tr>
            <td style="width: auto; white-space: nowrap;">
                Start Date<br/>
                <input type="text" class="datetimepicker" name="start" id="event_start_date" onchange="mp_ssv_update_event_start_date()" value="<?= esc_html($start) ?>" placeholder="<?= esc_html($placeholder) ?>" title="Start Date" required><br/>
                End Date<br/>
                <input type="text" class="datetimepicker" name="end" id="event_end_date" onchange="mp_ssv_update_event_end_date()" value="<?= esc_html($end) ?>" placeholder="<?= esc_html($placeholder) ?>" title="End Date" required><br/>
                Location<br/>
                <input id="pac-input" type="text" name="location" value="<?= $location ?>" onkeypress="return event.keyCode !== 13;" placeholder="Enter a location" autocomplete="off"><br/>
            </td>
            <td height="100%" width="99%">
                <div id="map" style="height: 100%; margin: 10px 0;"></div>
            </td>
        </tr>
    </table>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= get_option(SSV_Events::OPTION_MAPS_API_KEY) ?>&libraries=places&callback=initMapSearch" async defer></script>
    <?php
}

function ssv_events_tickets()
{
    ?>
    <style>
        button.ssv-accordion {
            background-color: #eee;
            color: #444;
            cursor: pointer;
            padding: 18px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
            transition: 0.4s;
        }

        button.ssv-accordion.active, button.ssv-accordion:hover {
            background-color: #ddd;
        }

        div.panel {
            padding: 0 18px;
            background-color: white;
            display: none;
        }
    </style>
    <div id="test" style="margin: 10px 0;">
        <div id="tickets-placeholder" class="sortable"></div>
    </div>
    <button type="button" onclick="mp_ssv_add_new_ticket()">Add Ticket</button>
    <!--suppress JSUnusedLocalSymbols -->
    <script>
        var fieldID = 0;
        function mp_ssv_add_new_ticket() {
            mp_ssv_add_ticket(fieldID, "", "", "");
            fieldID++;
        }

        var customFieldIDs = [];
        function mp_ssv_add_new_custom_field_to_container(container) {
            var i = customFieldIDs[container];
            if (!i) {
                i = 0;
            }
            mp_ssv_add_custom_field(container, 'input', 'text', i, {"override_right": ""}, false);
            i++;
            customFieldIDs[container] = i;
        }
    </script>
    <?php
}

function ssv_events_registration()
{
    global $post;
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Enable Registration</th>
            <td>
                <select name="registration" title="Enable Registration">
                    <option value="disabled" <?= get_post_meta($post->ID, 'registration', true) == 'disabled' ? 'selected' : '' ?>>Disabled</option>
                    <option value="members_only" <?= get_post_meta($post->ID, 'registration', true) == 'members_only' ? 'selected' : '' ?>>Members Only</option>
                    <option value="everyone" <?= get_post_meta($post->ID, 'registration', true) == 'everyone' ? 'selected' : '' ?>>Everypne</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function ssv_events_registrations()
{
    /** @var WP_Post $post */
    global $post;
    /** @var wpdb $wpdb */
    global $wpdb;
    $event      = new Event($post);
    $table      = SSV_Events::TABLE_REGISTRATION;
    $sql        = "SELECT * FROM $table WHERE eventID = $post->ID";
    $rows       = $wpdb->get_results($sql);
    $fieldNames = $event->getRegistrationFieldNames();
    ?>
    <table cellspacing="5" border="1">
        <tr>
            <?php foreach ($fieldNames as $fieldName): ?>
                <td><?= esc_html($fieldName) ?></td>
            <?php endforeach; ?>
            <th>Status</th>
        </tr>
        <?php
        $i = 0;
        foreach ($rows as $row) {
            /** @var Registration $registration */
            $registration = Registration::getByID($row->ID);
            ?>
            <tr>
                <?php foreach ($fieldNames as $fieldName): ?>
                    <td><?= esc_html($registration->getMeta($fieldName)) ?></td>
                <?php endforeach; ?>
                <td>
                    <input type="hidden" name="<?= esc_html($i) ?>_post" value="<?= esc_html($post->ID) ?>">
                    <input type="hidden" name="<?= esc_html($i) ?>_action" value="edit">
                    <input type="hidden" name="<?= esc_html($i) ?>_registrationID" value="<?= esc_html($registration->registrationID) ?>">
                    <select name="<?= esc_html($i) ?>_status" title="Status">
                        <option value="pending" <?= $registration->status == 'pending' ? 'selected' : '' ?>>pending</option>
                        <option value="approved" <?= $registration->status == 'approved' ? 'selected' : '' ?>>approved</option>
                        <option value="denied" <?= $registration->status == 'denied' ? 'selected' : '' ?>>denied</option>
                    </select>
                </td>
            </tr>
            <?php
            $i++;
        }
        ?>
    </table>
    <?php
}

function ssv_events_registration_fields()
{
    echo Form::fromDatabase(SSV_Events::CAPABILITY_MANAGE_EVENT_REGISTRATIONS, false)->getEditor(false);
}

#endregion

#region Save Meta
/**
 * @param $post_id
 *
 * @return int the post_id
 */
function mp_ssv_events_save_meta($post_id)
{
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
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
        update_post_meta($post_id, 'registration', SSV_General::sanitize($_POST['registration'], array('disabled', 'members_only', 'everyone',)));
    }
    if (isset($_POST['start'])) {
        update_post_meta($post_id, 'start', SSV_General::sanitize($_POST['start'], 'datetime'));
    }
    if (isset($_POST['end'])) {
        update_post_meta($post_id, 'end', SSV_General::sanitize($_POST['end'], 'datetime'));
    }
    if (isset($_POST['location'])) {
        update_post_meta($post_id, 'location', SSV_General::sanitize($_POST['location'], 'text'));
    }

    Form::saveEditorFromPost(); //TODO do this action for all tickets.
    return $post_id;
}

add_action('save_post_events', 'mp_ssv_events_save_meta');
#endregion
