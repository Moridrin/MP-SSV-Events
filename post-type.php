<?php
function mp_ssv_register_events_post_category()
{

    $labels = array(
        'name'               => _x('Events', 'events'),
        'singular_name'      => _x('Event', 'events'),
        'add_new'            => _x('Add New', 'events'),
        'add_new_item'       => _x('Add New Event', 'events'),
        'edit_item'          => _x('Edit Event', 'events'),
        'new_item'           => _x('New Event', 'events'),
        'view_item'          => _x('View Event', 'events'),
        'search_items'       => _x('Search Events', 'events'),
        'not_found'          => _x('No Events found', 'events'),
        'not_found_in_trash' => _x('No Events found in Trash', 'events'),
        'parent_item_colon'  => _x('Parent Event:', 'events'),
        'menu_name'          => _x('Events', 'events'),
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
        'capability_type'     => 'post'
    );

    register_post_type('events', $args);
}

add_action('init', 'mp_ssv_register_events_post_category');

function mp_ssv_register_event_category_taxonomy()
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
                'with_front' => false
            )
        )
    );
}

add_action('init', 'mp_ssv_register_event_category_taxonomy');

function mp_ssv_add_events_metaboxes()
{
    add_meta_box('mp_ssv_events_registration', 'Registration', 'mp_ssv_events_registration', 'events', 'side', 'default');
    add_meta_box('mp_ssv_events_date', 'Date', 'mp_ssv_events_date', 'events', 'side', 'default');
    add_meta_box('mp_ssv_events_location', 'Location', 'mp_ssv_events_location', 'events', 'side', 'default');
}

add_action('add_meta_boxes', 'mp_ssv_add_events_metaboxes');

function mp_ssv_events_registration()
{
    global $post;
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Enable Registration</th>
            <td><input type="checkbox" name="registration" value="true" <?php if (get_post_meta($post->ID, 'registration', true) == 'true') {
                    echo "checked";
                } ?> title="Enable Registration"/></td>
        </tr>
    </table>
    <?php
}

function mp_ssv_events_date()
{
    global $post;
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Start Date</th>
            <td><input type="date" name="start_date" value="<?php echo get_post_meta($post->ID, 'start_date', true); ?>" title="Start Date"></td>
        </tr>
        <tr valign="top">
            <th scope="row">Start Time</th>
            <?php
            $start_time = get_post_meta($post->ID, 'start_time', true);
            if ($start_time == null) {
                $start_time = get_option('mp_ssv_event_default_start_time');
            } ?>
            <td><input type="time" name="start_time" value="<?php echo $start_time; ?>" title="Start Time"></td>
        </tr>
        <tr valign="top">
            <th scope="row">End Date</th>
            <td><input type="date" name="end_date" value="<?php echo get_post_meta($post->ID, 'end_date', true); ?>" title="End Date"></td>
        </tr>
        <tr valign="top">
            <th scope="row">End Time</th>
            <?php
            $end_time = get_post_meta($post->ID, 'end_time', true);
            if ($end_time == null) {
                $end_time = get_option('mp_ssv_event_default_end_time');
            } ?>
            <td><input type="time" name="end_time" value="<?php echo $end_time; ?>" title="End Time"></td>
        </tr>
    </table>
    <?php
}

function mp_ssv_events_location()
{
    global $post;
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Location</th>
            <td><input type="text" name="location" value="<?php echo get_post_meta($post->ID, 'location', true); ?>" title="Location"/></td>
        </tr>
    </table>
    <?php
}

/**
 * @param $post_id
 * @param $post
 *
 * @return int the post_id
 */
function mp_ssv_save_events_meta($post_id, $post)
{
    if (!current_user_can('edit_post', $post->ID)) {
        return $post_id;
    }
    if (isset($_POST['registration'])) {
        update_post_meta($post->ID, 'registration', $_POST['registration']);
    }
    if (isset($_POST['start_date'])) {
        update_post_meta($post->ID, 'start_date', $_POST['start_date']);
    }
    if (isset($_POST['start_time'])) {
        update_post_meta($post->ID, 'start_time', $_POST['start_time']);
    }
    if (isset($_POST['end_date'])) {
        update_post_meta($post->ID, 'end_date', $_POST['end_date']);
    }
    if (isset($_POST['end_time'])) {
        update_post_meta($post->ID, 'end_time', $_POST['end_time']);
    }
    if (isset($_POST['location'])) {
        update_post_meta($post->ID, 'location', $_POST['location']);
    }
    return $post_id;
}

add_action('save_post', 'mp_ssv_save_events_meta', 1, 2);
?>