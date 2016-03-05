<?php
function add_event_content($content) {
	global $post;
	global $wpdb;
	$user_registered = false;
	$registration_message = "";
	/* Return */
	if ($post->post_type != 'events') {
		return $content;
	} else if (get_post_meta($post->ID, 'registration', true) == 'false') {
		return $content;
	}
	
	/* Save POST */
	if (isset($_POST['submit'])) {
		if ($_POST['action'] == 'register') {
			$table_name = $wpdb->prefix."mp_ssv_event_registration";
			$event_ID = get_the_ID();
			if (is_user_logged_in()) {
				$userID = wp_get_current_user()->ID;
				$wpdb->insert(
					$table_name,
					array(
						'userID' => $userID,
						'eventID' => $event_ID,
						'status' => get_option('mp_ssv_event_default_registration_status')
					),
					array(
						'%d',
						'%d',
						'%s'
					) 
				);
			} else {
				$wpdb->insert(
					$table_name,
					array(
						'eventID' => $event_ID,
						'status' => get_option('mp_ssv_event_default_registration_status'),
						'first_name' => $_POST['first_name'],
						'last_name' => $_POST['last_name']
					),
					array(
						'%d',
						'%s',
						'%s',
						'%s'
					) 
				);
			}
			$registration_message = '<div class="notification">'.stripslashes(get_option('mp_ssv_event_registration_message')).'</div>';
			$to = get_the_author_meta('user_email');
			$subject = "New Registration for ".get_the_title();
			$display_name = "";
			if (is_user_logged_in()) {
				$display_name = wp_get_current_user()->display_name;
			} else {
				$display_name = $_POST['first_name']." ".$_POST['last_name'];
			}
			$message = $display_name.' has just registered for '.get_the_title().'.';
			wp_mail($to, $subject, $message);
		} else if ($_POST['action'] == 'cancel') {
			$user = wp_get_current_user();
			$table_name = $wpdb->prefix."mp_ssv_event_registration";
			$user_ID = get_current_user_id();
			$event_ID = get_the_ID();
			$wpdb->delete($table_name,array('userID' => $user_ID, 'eventID' => $event_ID));
			$registration_message = '<div class="notification">'.stripslashes(get_option('mp_ssv_event_cancelation_message')).'</div>';
			$to = get_the_author_meta('user_email');
			$subject = "Cancelation for ".get_the_title();
			$message = $user->display_name.' has just canceled his/her registration for '.get_the_title().'.';
			wp_mail($to, $subject, $message);
		}
	}


	/* Content */
	if (strpos($content, '<h3>') === false) {
		$content = '<h3>About</h3>'.$content;
	}

	/* Guest List */
	$event_ID = get_the_ID();
	$table_name = $wpdb->prefix."mp_ssv_event_registration";
	$event_registrations = $wpdb->get_results("SELECT * FROM $table_name WHERE `eventID` = $event_ID");
	if (!empty($event_registrations)) {
		$content .= '<h3>Guest List</h3>';
		$content .= '<ul>';
		foreach ($event_registrations as $event_registration) {
			if ($event_registration->status == 'pending') {
				$content .= '<li>';
				if ($event_registration->userID != null) {
					$content .= get_user_name($event_registration->userID).'<p class="note"> (pending)</p>';
				} else {
					$content .= $event_registration->first_name." ".$event_registration->last_name.'<p class="note"> (pending)</p>';
				}
				$content .= '</li>';
			} else if ($event_registration->status == 'approved') {
				$content .= '<li>';
				if ($event_registration->userID != null) {
					$content .= get_user_name($event_registration->userID);
				} else {
					$content .= $event_registration->first_name." ".$event_registration->last_name;
				}
				$content .= '</li>';
			}
			if ($event_registration->userID == get_current_user_id()) {
				$user_registered = true;
			}
		}	
		$content .= '</ul>';
	}
	/* Registration */
	if ($registration_message != "") {
		$content .= $registration_message;
	}
	if (is_user_logged_in()) {
		if ($user_registered) {
			$content .= '<form action="#" method="POST">';
			$content .= '<input type="hidden" name="action" value="cancel">';
			$content .= '<button type="submit" name="submit" class="mui-btn mui-btn--danger mui-btn--small">Cancel Registration</button>';
			$content .= '</form>';
		} else {
			$content .= '<form action="#" method="POST">';
			$content .= '<input type="hidden" name="action" value="register">';
			$content .= '<button type="submit" name="submit" class="mui-btn mui-btn--primary">Register</button>';
			$content .= '</form>';
		}
	} else if (get_option('mp_ssv_event_guest_registration') == 'true') {
		if (strpos($content, '<h3>') === false) {
			$content .= '<h3>Registration</h3>';
		}
		$content .= '<form action="#" method="POST">';
		$content .= '<input type="hidden" name="action" value="register">';
		$content .= '<table class="form-table">';
		$content .= '<tr valign="top">';
		$content .= '<th scope="row">First Name</th>';
		$content .= '<td><input type="text" name="first_name"></td>';
		$content .= '</tr>';
		$content .= '<tr valign="top">';
		$content .= '<th scope="row">Last Name</th>';
		$content .= '<td><input type="text" name="last_name"></td>';
		$content .= '</tr>';
		$content .= '<tr valign="top">';
		$content .= '<th scope="row"/>';
		$content .= '<td><button type="submit" name="submit" class="mui-btn mui-btn--primary">Register</button></td>';
		$content .= '</tr>';
		$content .= '</table>';
		$content .= '</form>';
	} else {
		$content .= 'You must sign in to register';
	}
	$content = get_date_and_time($post).$content;
	return $content;
}
add_filter( 'the_content', 'add_event_content' );


function get_date_and_time($post) {
	$start_date = get_post_meta($post->ID, 'start_date', true);
	$start_time = get_post_meta($post->ID, 'start_time', true);
	$end_date = get_post_meta($post->ID, 'end_date', true);
	$end_time = get_post_meta($post->ID, 'end_time', true);
	$date_and_time = '<h3>When</h3>';
	$date_and_time .= '<table>';
	$date_and_time .= '<tr>';
	$date_and_time .= '<th>';
	$date_and_time .= 'Start:';
	$date_and_time .= '</th>';
	$date_and_time .= '<td>';
	$date_and_time .= $start_date.' '.$start_time;
	$date_and_time .= '</td>';
	$date_and_time .= '</tr>';
	if ($start_date != $end_date || $start_time != $end_time) {
		$date_and_time .= '<tr>';
		$date_and_time .= '<th>';
		$date_and_time .= 'End:';
		$date_and_time .= '</th>';
		$date_and_time .= '<td>';
		$date_and_time .= $end_date.' '.$end_time;
		$date_and_time .= '</td>';
		$date_and_time .= '</tr>';
	}
	$date_and_time .= '</table>';
	return $date_and_time;
}
?>