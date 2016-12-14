<?php

function ssv_profile_page_registrations_table_content() {
	global $wpdb;
	$current_user = wp_get_current_user();
	ob_start();
?>
<table style="width: 100%;">
	<tr>
		<th><h1>Upcoming Events</h1></th>
	</tr>
	<tr>
		<th>Event</th>
		<th>Status</th>
	</tr>
	<?php
	$content = ob_get_clean();
	$sql = 'SELECT event_post.post_title, registration.status, event_post.ID
		FROM wp_ssv_event_registration AS registration
		INNER JOIN wp_posts AS event_post
		ON registration.eventID = event_post.ID
		INNER JOIN wp_postmeta AS event_meta
		ON event_post.ID = event_meta.post_id
		WHERE event_meta.meta_key = "start_date"
		AND event_meta.meta_value >= "'.date("Y-m-d").'"
		AND registration.userID = '.$current_user->ID;
		$upcoming_events = $wpdb->get_results($sql);
	foreach ($upcoming_events as $upcoming_event) {
		ob_start(); ?>
		<tr>
			<td><a href="<?php echo get_permalink($upcoming_event->ID); ?>"><?php echo $upcoming_event->post_title; ?></a></td>
			<td><?php echo $upcoming_event->status; ?></td>
		</tr>
		<?php
		$content .= ob_get_clean();
	}
	ob_start();
	?>
	<tr>
		<th><h1>Past Events</h1></th>
	</tr>
	<tr>
		<th>Event</th>
		<th>Status</th>
	</tr>
	<?php
	$content .= ob_get_clean();
	$sql = 'SELECT event_post.post_title, registration.status, event_post.ID
		FROM wp_ssv_event_registration AS registration
		INNER JOIN wp_posts AS event_post
		ON registration.eventID = event_post.ID
		INNER JOIN wp_postmeta AS event_meta
		ON event_post.ID = event_meta.post_id
		WHERE event_meta.meta_key = "start_date"
		AND event_meta.meta_value < "'.date("Y-m-d").'"
		AND registration.userID = '.$current_user->ID;
		$upcoming_events = $wpdb->get_results($sql);
	foreach ($upcoming_events as $upcoming_event) {
		ob_start(); ?>
		<tr>
			<td><a href="<?php echo get_permalink($upcoming_event->ID); ?>"><?php echo $upcoming_event->post_title; ?></a></td>
			<td><?php echo $upcoming_event->status; ?></td>
		</tr>
		<?php
		$content .= ob_get_clean();
	}
	$content .= "</table>";
	return $content;
}
