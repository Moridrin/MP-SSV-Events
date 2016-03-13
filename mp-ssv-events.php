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
	require_once('mp-ssv-events-timezone-table.php');
}
register_activation_hook(__FILE__, 'register_mp_ssv_events');

function mp_ssv_unregister_mp_ssv_events() {
	$page = get_page_by_title('Events');
	wp_delete_post($page->ID, true);
}
register_deactivation_hook(__FILE__, 'unregister_mp_ssv_events');

function mp_ssv_events_template( $archive_template ) {
     global $post;

     if ( is_post_type_archive ( 'events' ) ) {
          $archive_template = dirname( __FILE__ ) . '/archive-events.php';
     }
     return $archive_template;
}
add_filter( 'archive_template', 'events_template' ) ;

function mp_ssv_get_user_name($user_ID) {
	$user = get_user_by( 'ID', $user_ID );
	return $user->display_name;
}

function mp_ssv_get_local_time_string($time_string) {
	global $wpdb;
	$time = DateTime::createFromFormat('H:i', $time_string);
	$table_name = $wpdb->prefix."mp_ssv_event_timezone";
	$mp_ssv_event_time_zone = get_option('mp_ssv_event_time_zone');
	$result = $wpdb->get_row("SELECT * FROM $table_name WHERE `id` = $mp_ssv_event_time_zone");
	$gmt_adjustment = $result->gmt_adjustment;
	if ($gmt_adjustment[0] == '+') {
		$time->sub(new DateInterval('PT'.$gmt_adjustment[1].$gmt_adjustment[2].'H'.$gmt_adjustment[4].$gmt_adjustment[5].'M'));
	} else {
		$time->add(new DateInterval('PT'.$gmt_adjustment[1].$gmt_adjustment[2].'H'.$gmt_adjustment[4].$gmt_adjustment[5].'M'));
	}
	return $time->format('H:i');
}

function mp_ssv_get_local_datetime($time) {
	global $wpdb;
	$table_name = $wpdb->prefix."mp_ssv_event_timezone";
	$mp_ssv_event_time_zone = get_option('mp_ssv_event_time_zone');
	$result = $wpdb->get_row("SELECT * FROM $table_name WHERE `id` = $mp_ssv_event_time_zone");
	$gmt_adjustment = $result->gmt_adjustment;
	if ($gmt_adjustment[0] == '+') {
		$time->sub(new DateInterval('PT'.$gmt_adjustment[1].$gmt_adjustment[2].'H'.$gmt_adjustment[4].$gmt_adjustment[5].'M'));
	} else {
		$time->add(new DateInterval('PT'.$gmt_adjustment[1].$gmt_adjustment[2].'H'.$gmt_adjustment[4].$gmt_adjustment[5].'M'));
	}
	return $time;
}
?>