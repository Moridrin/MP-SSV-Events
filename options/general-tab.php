<form method="post" action="#">
	<table class="form-table">
		<?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); ?>
		<?php if (is_plugin_active('mp-ssv-frontend-members/mp-ssv-frontend-members.php')) { ?>
		<tr valign="top">
			<th scope="row">Show Registrations in Profile Page</th>
			<td>
				<input type="checkbox" name="mp_ssv_show_registrations_in_profile" value="true" <?php if (get_option('mp_ssv_show_registrations_in_profile') == 'true') { echo "checked"; } ?>/>
				If the theme supports <a href="https://www.muicss.com/" target="_blank">MUI</a> it adds a tab called "Registrations". Else it will add it at the bottom of the page.<br/>
				You can also add it at a custom location by adding a component with value <xmp style="display: inline;">[mp-ssv-events-registrations]</xmp>.
			</td>
		</tr>
		<?php } ?>
		
		<tr valign="top">
			<th scope="row">Enable Guest Registration</th>
			<td><input type="checkbox" name="mp_ssv_event_guest_registration" value="true" <?php if (get_option('mp_ssv_event_guest_registration') == 'true') { echo "checked"; } ?>/></td>
		</tr>
		 
		<tr valign="top">
			<th scope="row">Default Registration Status</th>
			<td>
				<select name="mp_ssv_event_default_registration_status">
					<option value="pending" <?php if (esc_attr(stripslashes(get_option('mp_ssv_event_default_registration_status'))) == 'pending') { echo "selected"; } ?>>Pending</option>
					<option value="approved"<?php if (esc_attr(stripslashes(get_option('mp_ssv_event_default_registration_status'))) == 'approved') { echo "selected"; } ?>>Approved</option>
					<option value="denied"<?php if (esc_attr(stripslashes(get_option('mp_ssv_event_default_registration_status'))) == 'denied') { echo "selected"; } ?>>Denied</option>
				</select>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">Registration Message</th>
			<td><textarea name="mp_ssv_event_registration_message" class="large-text"><?php echo esc_attr(stripslashes(get_option('mp_ssv_event_registration_message'))); ?></textarea></td>
		</tr>
		 
		<tr valign="top">
			<th scope="row">Cancelation Message</th>
			<td><textarea name="mp_ssv_event_cancelation_message" class="large-text"><?php echo esc_attr(stripslashes(get_option('mp_ssv_event_cancelation_message'))); ?></textarea></td>
		</tr>
		 
		<tr valign="top">
			<th scope="row">Default Start Time</th>
			<td><input type="time" name="mp_ssv_event_default_start_time" value="<?php echo esc_attr(stripslashes(get_option('mp_ssv_event_default_start_time'))); ?>"/></td>
		</tr>
		 
		<tr valign="top">
			<th scope="row">Default Start Time</th>
			<td><input type="time" name="mp_ssv_event_default_end_time" value="<?php echo esc_attr(stripslashes(get_option('mp_ssv_event_default_end_time'))); ?>"/></td>
		</tr>
	</table>
	
	<?php submit_button(); ?>
</form>
