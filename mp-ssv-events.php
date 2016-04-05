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

include_once "profile-content.php";
include_once "location-widget.php";
include_once "post-type.php";
include_once "event-content.php";
include_once "options/options.php";

function mp_ssv_register_mp_ssv_events() {
	if (!is_plugin_active('mp-ssv-general/mp-ssv-general.php')) {
		wp_die('Sorry, but this plugin requires <a href="http://studentensurvival.com/plugins/mp-ssv-general">SSV General</a> to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
	}
	/* Database */
	global $wpdb;
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix."mp_ssv_event_registration";
	$sql = "
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
	dbDelta($sql);
	$table_name = $wpdb->prefix."mp_ssv_event_timezone";
	$sql = "
		CREATE TABLE $table_name (
			id bigint(20) NOT NULL,
			gmt_adjustment varchar(20) NOT NULL,
			use_daylight_saving_time tinyint(1) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";
	dbDelta($sql);
}
register_activation_hook(__FILE__, 'mp_ssv_register_mp_ssv_events');

function mp_ssv_unregister_mp_ssv_events() {
	$page = get_page_by_title('Events');
	wp_delete_post($page->ID, true);
}
register_deactivation_hook(__FILE__, 'mp_ssv_unregister_mp_ssv_events');

function mp_ssv_events_template( $archive_template ) {
     global $post;

     if ( is_post_type_archive ( 'events' ) ) {
          $archive_template = dirname( __FILE__ ) . '/archive-events.php';
     }
     return $archive_template;
}
add_filter( 'archive_template', 'mp_ssv_events_template' ) ;
?>