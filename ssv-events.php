<?php
/**
 * Plugin Name: SSV Events
 * Plugin URI: http://moridrin.com/ssv-events
 * Description: SSV Events is a plugin that allows you to create events for the Students Sports Club and allows all members from that club to join the event.
 * Version: 1.0
 * Author: Jeroen Berkvens
 * Author URI: http://nl.linkedin.com/in/jberkvens/
 * License: WTFPL
 * License URI: http://www.wtfpl.net/txt/copying/
 */

require_once 'general/general.php';

require_once "options/options.php";

require_once "models/Event.php";
require_once "models/Registration.php";

require_once 'custom-post-type/functions.php';
require_once "custom-post-type/post-type.php";
require_once "custom-post-type/event-views/page-full.php";

require_once "widgets/category-widget.php";

require_once "ssv-integration/ssv-frontend-members/profile-page-content.php";

define('SSV_EVENTS_PATH', plugin_dir_path(__FILE__));

function mp_ssv_register_ssv_events()
{
    /* Database */
    global $wpdb;
    /** @noinspection PhpIncludeInspection */
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();
    $table_name      = $wpdb->prefix . "ssv_event_registration";
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

    /* Options */
    update_option('ssv_event_guest_registration', 'false');
    update_option('ssv_event_default_registration_status', 'pending');
    update_option('ssv_event_registration_message', 'Your registration is pending.');
    update_option('ssv_event_cancellation_message', 'Your registration is canceled.');
    update_option('ssv_event_email_registration_confirmation', 'true');
    update_option('ssv_event_email_registration_status_changed', 'true');
    update_option('ssv_event_email_registration_verify', 'true');
}

register_activation_hook(__FILE__, 'mp_ssv_register_ssv_events');

function mp_ssv_unregister_ssv_events()
{
    //Nothing to do here.
}

register_deactivation_hook(__FILE__, 'mp_ssv_unregister_ssv_events');

function mp_ssv_uninstall_ssv_events()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ssv_event_registration";
    $sql        = "DROP TABLE IF_EXISTS $table_name;";
    $wpdb->query($sql);
}

register_uninstall_hook(__FILE__, 'mp_ssv_uninstall_ssv_events');

function mp_ssv_events_enquire_scripts()
{
//    wp_enqueue_script('ssv_events_init', plugin_dir_path(__FILE__) . '/js/init.js', array('jquery'));
}

add_action('wp_enqueue_scripts', 'mp_ssv_events_enquire_scripts');
