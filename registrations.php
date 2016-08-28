<?php
define('EVENTS_REGISTRATION', $wpdb->prefix . "ssv_event_registration");
function ssv_add_registrations_page()
{
    add_submenu_page('edit.php?post_type=events', 'Registrations', 'Registrations', 'manage_options', __FILE__, 'ssv_events_registration_page');
}

function ssv_events_registration_page()
{
    ?>
    <h1>Registrations</h1>
    <?php
    global $wpdb;
    $table = EVENTS_REGISTRATION;

    if (isset($_GET['registrationID'])) {
        $tmp = $wpdb->update(
            $table,
            array("status" => $_GET['status']),
            array("id" => $_GET['registrationID']),
            array('%s')
        );
    }

    $sql = "SELECT * FROM $table";
    $rows = json_decode(json_encode($wpdb->get_results($sql)), true);
    ?>
    <table cellspacing="5" border="1">
        <tr>
            <th>Event</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
        <?php
        foreach ($rows as $row) {
            $event = get_post($row['eventID']);
            $status = $row['status'];
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $email = $row['email'];
            if (isset($row['userID'])) {
                $user = new FrontendMember(get_user_by('ID', $row['userID']));
                $first_name = $user->first_name;
                $last_name = $user->last_name;
                $email = $user->user_email;
            }
            ?>
            <tr>
                <form type="GET">
                    <input type="hidden" name="post_type" value="events">
                    <input type="hidden" name="page" value="ssv-events/registrations.php">
                    <input type="hidden" name="registrationID" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="eventID" value="<?php echo $event->ID; ?>">
                    <td><a href="<?php echo get_permalink($event->ID); ?>"><?php echo $event->post_title ?></a></td>
                    <td><?php echo $first_name; ?></td>
                    <td><?php echo $last_name; ?></td>
                    <td><?php echo $email; ?></td>
                    <td>
                        <select name="status" onchange="this.form.submit()">
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
                    </td>
                </form>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}

add_action('admin_menu', 'ssv_add_registrations_page');
