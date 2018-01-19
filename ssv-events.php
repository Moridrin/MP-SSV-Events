<?php
/**
 * Plugin Name: SSV Events
 * Plugin URI: https://bosso.nl/ssv-events/
 * Description: SSV Events is a plugin that allows you to create events for the Students Sports Club and allows all members from that club to join the event.
 * Version: 3.3.5
 * Author: moridrin
 * Author URI: http://nl.linkedin.com/in/jberkvens/
 * License: WTFPL
 * License URI: http://www.wtfpl.net/txt/copying/
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once 'general/base/base.php';
require_once 'general/forms/forms.php';

global $wpdb;
define('SSV_EVENTS_PATH', plugin_dir_path(__FILE__));
define('SSV_EVENTS_URL', plugins_url() . '/ssv-events/');
define('SSV_EVENTS_TICKETS_TABLE', $wpdb->prefix . "ssv_event_tickets");
define('SSV_EVENTS_REGISTRATIONS_TABLE', $wpdb->prefix . "ssv_event_registrations");

require_once 'SSV_Events.php';
require_once 'models/Event.php';
require_once 'custom-post-type/EventsPostType.php';
