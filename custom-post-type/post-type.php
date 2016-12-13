<?php
define('EVENTS_REGISTRATION', $wpdb->prefix . "ssv_event_registration");
function ssv_register_events_post_category()
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
        'capability_type'     => 'post',
    );

    register_post_type('events', $args);
}

add_action('init', 'ssv_register_events_post_category');

function ssv_register_event_category_taxonomy()
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

add_action('init', 'ssv_register_event_category_taxonomy');

function ssv_add_events_metaboxes()
{
    add_meta_box('ssv_events_registration', 'Registration', 'ssv_events_registration', 'events', 'side', 'default');
    add_meta_box('ssv_events_date', 'Date', 'ssv_events_date', 'events', 'side', 'default');
    add_meta_box('ssv_events_location', 'Location', 'ssv_events_location', 'events', 'side', 'default');
    add_meta_box('ssv_events_registrations', 'Registrations', 'ssv_events_registrations', 'events', 'advanced', 'default');
}

add_action('add_meta_boxes', 'ssv_add_events_metaboxes');

function ssv_events_registration()
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

function ssv_events_date()
{
    global $post;
    $start = get_post_meta($post->ID, 'start', true);
    $start = $start ?: get_post_meta($post->ID, 'start_date', true) . ' ' . get_post_meta($post->ID, 'start_time', true);
    $end   = get_post_meta($post->ID, 'end', true);
    $end   = $end ?: get_post_meta($post->ID, 'end_date', true) . ' ' . get_post_meta($post->ID, 'end_time', true);
    ?>
    Start Date<br/>
    <input type="text" class="datetimepicker" name="start" value="<?= $start ?>" title="Start Date" required><br/>
    End Date<br/>
    <input type="text" class="datetimepicker" name="end" value="<?= $end ?>" title="End Date" required>
    <?php
}

function ssv_events_location()
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

function ssv_events_registrations()
{
    global $post;
    global $wpdb;
    $table = EVENTS_REGISTRATION;

    if (isset($_GET['registrationID'])) {
        $wpdb->update(
            $table,
            array("status" => $_GET['status']),
            array("id" => $_GET['registrationID']),
            array('%s')
        );
    }

    $sql  = "SELECT * FROM $table WHERE eventID = $post->ID";
    $rows = json_decode(json_encode($wpdb->get_results($sql)), true);
    ?>
    <table cellspacing="5" border="1">
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
        <?php
        foreach ($rows as $row) {
            $event      = get_post($row['eventID']);
            $status     = $row['status'];
            $first_name = $row['first_name'];
            $last_name  = $row['last_name'];
            $email      = $row['email'];
            if (isset($row['userID'])) {
                $user       = new FrontendMember(get_user_by('ID', $row['userID']));
                $first_name = $user->first_name;
                $last_name  = $user->last_name;
                $email      = $user->user_email;
            }
            ?>
            <tr>
                <td><?php echo $first_name; ?></td>
                <td><?php echo $last_name; ?></td>
                <td><?php echo $email; ?></td>
                <td>
                    <form type="GET">
                        <input type="hidden" name="post" value="<?= $post->ID ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="registrationID" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="eventID" value="<?php echo $event->ID; ?>">
                        <select name="status" onchange="this.form.submit()" title="<?= $this->title ?>">
                            <option value="pending" <?php if ($status == 'pending') {
                                echo 'selected';
                            } ?>>pending
                            </option>
                            <option value="approved" <?php if ($status == 'approved') {
                                echo 'selected';
                            } ?>>approved
                            </option>
                            <option value="denied" <?php if ($status == 'denied') {
                                echo 'selected';
                            } ?>>denied
                            </option>
                        </select>
                    </form>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}

/**
 * @param $post_id
 * @param $post
 *
 * @return int the post_id
 */
function ssv_save_events_meta($post_id, $post)
{
    if (!current_user_can('edit_post', $post->ID)) {
        return $post_id;
    }
    if (isset($_POST['registration'])) {
        update_post_meta($post->ID, 'registration', $_POST['registration']);
    }
    if (isset($_POST['start'])) {
        update_post_meta($post->ID, 'start', $_POST['start']);
    }
    if (isset($_POST['end'])) {
        update_post_meta($post->ID, 'end', $_POST['end']);
    }
    if (isset($_POST['location'])) {
        update_post_meta($post->ID, 'location', $_POST['location']);
    }
    return $post_id;
}

add_action('save_post', 'ssv_save_events_meta', 1, 2);
?>