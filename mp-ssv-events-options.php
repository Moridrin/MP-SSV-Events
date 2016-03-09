<?php
if (!function_exists("add_mp_ssv_menu")) {
	function add_mp_ssv_menu() {
		add_menu_page('MP SSV Options', 'MP-SSV Options', 'manage_options', 'mp_ssv_settings', 'mp_ssv_settings_page');
		add_submenu_page( 'mp_ssv_settings', 'General', 'General', 'manage_options', 'mp_ssv_settings');
	}
	function mp_ssv_settings_page() {
		include_once "mp-ssv-general-options.php";
	}
	add_action('admin_menu', 'add_mp_ssv_menu');
}

if (!function_exists("add_mp_ssv_mailchimp_menu")) {
	function add_mp_ssv_mailchimp_menu() {
		add_submenu_page( 'mp_ssv_settings', 'MailChimp Options', 'MailChimp', 'manage_options', "mailchimp_options", 'mp_ssv_mailchimp_settings_page' );
	}
	function mp_ssv_mailchimp_settings_page() {
		include_once "mp-ssv-mailchimp-options.php";
	}
	add_action('admin_menu', 'add_mp_ssv_mailchimp_menu');
}


function addMPSSVEventsOptions() {
	add_submenu_page( 'mp_ssv_settings', 'Events Options', 'Events', 'manage_options', __FILE__, 'mp_ssv_events_settings_page' );
}
function mp_ssv_events_settings_page() {
	global $options;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		mp_ssv_settings_save();
	}
	?>
	<div class="wrap">
	<h2>Events Options</h2>

	<form method="post" action="#">
		<?php settings_fields( 'mp-ssv-events-options-group' ); ?>
		<?php do_settings_sections( 'mp-ssv-events-options-group' ); ?>
		<table class="form-table">
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
			 
			<tr valign="top">
				<th scope="row">Time Zone</th>
				<td>
					<select name="mp_ssv_event_time_zone">
						<option value="1" gmtAdjustment="GMT-12:00" useDaylightTime="0" timeZoneOffset="-12" <?php if (get_option('mp_ssv_event_time_zone') == "1") { echo "selected"; } ?>>(GMT-12:00) International Date Line West</option>
						<option value="2" gmtAdjustment="GMT-11:00" useDaylightTime="0" timeZoneOffset="-11" <?php if (get_option('mp_ssv_event_time_zone') == "2") { echo "selected"; } ?>>(GMT-11:00) Midway Island, Samoa</option>
						<option value="3" gmtAdjustment="GMT-10:00" useDaylightTime="0" timeZoneOffset="-10" <?php if (get_option('mp_ssv_event_time_zone') == "3") { echo "selected"; } ?>>(GMT-10:00) Hawaii</option>
						<option value="4" gmtAdjustment="GMT-09:00" useDaylightTime="1" timeZoneOffset="-9" <?php if (get_option('mp_ssv_event_time_zone') == "4") { echo "selected"; } ?>>(GMT-09:00) Alaska</option>
						<option value="5" gmtAdjustment="GMT-08:00" useDaylightTime="1" timeZoneOffset="-8" <?php if (get_option('mp_ssv_event_time_zone') == "5") { echo "selected"; } ?>>(GMT-08:00) Pacific Time (US & Canada)</option>
						<option value="6" gmtAdjustment="GMT-08:00" useDaylightTime="1" timeZoneOffset="-8" <?php if (get_option('mp_ssv_event_time_zone') == "6") { echo "selected"; } ?>>(GMT-08:00) Tijuana, Baja California</option>
						<option value="7" gmtAdjustment="GMT-07:00" useDaylightTime="0" timeZoneOffset="-7" <?php if (get_option('mp_ssv_event_time_zone') == "7") { echo "selected"; } ?>>(GMT-07:00) Arizona</option>
						<option value="8" gmtAdjustment="GMT-07:00" useDaylightTime="1" timeZoneOffset="-7" <?php if (get_option('mp_ssv_event_time_zone') == "8") { echo "selected"; } ?>>(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
						<option value="9" gmtAdjustment="GMT-07:00" useDaylightTime="1" timeZoneOffset="-7" <?php if (get_option('mp_ssv_event_time_zone') == "9") { echo "selected"; } ?>>(GMT-07:00) Mountain Time (US & Canada)</option>
						<option value="10" gmtAdjustment="GMT-06:00" useDaylightTime="0" timeZoneOffset="-6" <?php if (get_option('mp_ssv_event_time_zone') == "10") { echo "selected"; } ?>>(GMT-06:00) Central America</option>
						<option value="11" gmtAdjustment="GMT-06:00" useDaylightTime="1" timeZoneOffset="-6" <?php if (get_option('mp_ssv_event_time_zone') == "11") { echo "selected"; } ?>>(GMT-06:00) Central Time (US & Canada)</option>
						<option value="12" gmtAdjustment="GMT-06:00" useDaylightTime="1" timeZoneOffset="-6" <?php if (get_option('mp_ssv_event_time_zone') == "12") { echo "selected"; } ?>>(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
						<option value="13" gmtAdjustment="GMT-06:00" useDaylightTime="0" timeZoneOffset="-6" <?php if (get_option('mp_ssv_event_time_zone') == "13") { echo "selected"; } ?>>(GMT-06:00) Saskatchewan</option>
						<option value="14" gmtAdjustment="GMT-05:00" useDaylightTime="0" timeZoneOffset="-5" <?php if (get_option('mp_ssv_event_time_zone') == "14") { echo "selected"; } ?>>(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
						<option value="15" gmtAdjustment="GMT-05:00" useDaylightTime="1" timeZoneOffset="-5" <?php if (get_option('mp_ssv_event_time_zone') == "15") { echo "selected"; } ?>>(GMT-05:00) Eastern Time (US & Canada)</option>
						<option value="16" gmtAdjustment="GMT-05:00" useDaylightTime="1" timeZoneOffset="-5" <?php if (get_option('mp_ssv_event_time_zone') == "16") { echo "selected"; } ?>>(GMT-05:00) Indiana (East)</option>
						<option value="17" gmtAdjustment="GMT-04:00" useDaylightTime="1" timeZoneOffset="-4" <?php if (get_option('mp_ssv_event_time_zone') == "17") { echo "selected"; } ?>>(GMT-04:00) Atlantic Time (Canada)</option>
						<option value="18" gmtAdjustment="GMT-04:00" useDaylightTime="0" timeZoneOffset="-4" <?php if (get_option('mp_ssv_event_time_zone') == "18") { echo "selected"; } ?>>(GMT-04:00) Caracas, La Paz</option>
						<option value="19" gmtAdjustment="GMT-04:00" useDaylightTime="0" timeZoneOffset="-4" <?php if (get_option('mp_ssv_event_time_zone') == "19") { echo "selected"; } ?>>(GMT-04:00) Manaus</option>
						<option value="20" gmtAdjustment="GMT-04:00" useDaylightTime="1" timeZoneOffset="-4" <?php if (get_option('mp_ssv_event_time_zone') == "20") { echo "selected"; } ?>>(GMT-04:00) Santiago</option>
						<option value="21" gmtAdjustment="GMT-03:30" useDaylightTime="1" timeZoneOffset="-3.5" <?php if (get_option('mp_ssv_event_time_zone') == "21") { echo "selected"; } ?>>(GMT-03:30) Newfoundland</option>
						<option value="22" gmtAdjustment="GMT-03:00" useDaylightTime="1" timeZoneOffset="-3" <?php if (get_option('mp_ssv_event_time_zone') == "22") { echo "selected"; } ?>>(GMT-03:00) Brasilia</option>
						<option value="23" gmtAdjustment="GMT-03:00" useDaylightTime="0" timeZoneOffset="-3" <?php if (get_option('mp_ssv_event_time_zone') == "23") { echo "selected"; } ?>>(GMT-03:00) Buenos Aires, Georgetown</option>
						<option value="24" gmtAdjustment="GMT-03:00" useDaylightTime="1" timeZoneOffset="-3" <?php if (get_option('mp_ssv_event_time_zone') == "24") { echo "selected"; } ?>>(GMT-03:00) Greenland</option>
						<option value="25" gmtAdjustment="GMT-03:00" useDaylightTime="1" timeZoneOffset="-3" <?php if (get_option('mp_ssv_event_time_zone') == "25") { echo "selected"; } ?>>(GMT-03:00) Montevideo</option>
						<option value="26" gmtAdjustment="GMT-02:00" useDaylightTime="1" timeZoneOffset="-2" <?php if (get_option('mp_ssv_event_time_zone') == "26") { echo "selected"; } ?>>(GMT-02:00) Mid-Atlantic</option>
						<option value="27" gmtAdjustment="GMT-01:00" useDaylightTime="0" timeZoneOffset="-1" <?php if (get_option('mp_ssv_event_time_zone') == "27") { echo "selected"; } ?>>(GMT-01:00) Cape Verde Is.</option>
						<option value="28" gmtAdjustment="GMT-01:00" useDaylightTime="1" timeZoneOffset="-1" <?php if (get_option('mp_ssv_event_time_zone') == "28") { echo "selected"; } ?>>(GMT-01:00) Azores</option>
						<option value="29" gmtAdjustment="GMT+00:00" useDaylightTime="0" timeZoneOffset="0" <?php if (get_option('mp_ssv_event_time_zone') == "29") { echo "selected"; } ?>>(GMT+00:00) Casablanca, Monrovia, Reykjavik</option>
						<option value="30" gmtAdjustment="GMT+00:00" useDaylightTime="1" timeZoneOffset="0" <?php if (get_option('mp_ssv_event_time_zone') == "30") { echo "selected"; } ?>>(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
						<option value="31" gmtAdjustment="GMT+01:00" useDaylightTime="1" timeZoneOffset="1" <?php if (get_option('mp_ssv_event_time_zone') == "31") { echo "selected"; } ?>>(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
						<option value="32" gmtAdjustment="GMT+01:00" useDaylightTime="1" timeZoneOffset="1" <?php if (get_option('mp_ssv_event_time_zone') == "32") { echo "selected"; } ?>>(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
						<option value="33" gmtAdjustment="GMT+01:00" useDaylightTime="1" timeZoneOffset="1" <?php if (get_option('mp_ssv_event_time_zone') == "33") { echo "selected"; } ?>>(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
						<option value="34" gmtAdjustment="GMT+01:00" useDaylightTime="1" timeZoneOffset="1" <?php if (get_option('mp_ssv_event_time_zone') == "34") { echo "selected"; } ?>>(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
						<option value="35" gmtAdjustment="GMT+01:00" useDaylightTime="1" timeZoneOffset="1" <?php if (get_option('mp_ssv_event_time_zone') == "35") { echo "selected"; } ?>>(GMT+01:00) West Central Africa</option>
						<option value="36" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "36") { echo "selected"; } ?>>(GMT+02:00) Amman</option>
						<option value="37" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "37") { echo "selected"; } ?>>(GMT+02:00) Athens, Bucharest, Istanbul</option>
						<option value="38" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "38") { echo "selected"; } ?>>(GMT+02:00) Beirut</option>
						<option value="39" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "39") { echo "selected"; } ?>>(GMT+02:00) Cairo</option>
						<option value="40" gmtAdjustment="GMT+02:00" useDaylightTime="0" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "40") { echo "selected"; } ?>>(GMT+02:00) Harare, Pretoria</option>
						<option value="41" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "41") { echo "selected"; } ?>>(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
						<option value="42" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "42") { echo "selected"; } ?>>(GMT+02:00) Jerusalem</option>
						<option value="43" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "43") { echo "selected"; } ?>>(GMT+02:00) Minsk</option>
						<option value="44" gmtAdjustment="GMT+02:00" useDaylightTime="1" timeZoneOffset="2" <?php if (get_option('mp_ssv_event_time_zone') == "44") { echo "selected"; } ?>>(GMT+02:00) Windhoek</option>
						<option value="45" gmtAdjustment="GMT+03:00" useDaylightTime="0" timeZoneOffset="3" <?php if (get_option('mp_ssv_event_time_zone') == "45") { echo "selected"; } ?>>(GMT+03:00) Kuwait, Riyadh, Baghdad</option>
						<option value="46" gmtAdjustment="GMT+03:00" useDaylightTime="1" timeZoneOffset="3" <?php if (get_option('mp_ssv_event_time_zone') == "46") { echo "selected"; } ?>>(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
						<option value="47" gmtAdjustment="GMT+03:00" useDaylightTime="0" timeZoneOffset="3" <?php if (get_option('mp_ssv_event_time_zone') == "47") { echo "selected"; } ?>>(GMT+03:00) Nairobi</option>
						<option value="48" gmtAdjustment="GMT+03:00" useDaylightTime="0" timeZoneOffset="3" <?php if (get_option('mp_ssv_event_time_zone') == "48") { echo "selected"; } ?>>(GMT+03:00) Tbilisi</option>
						<option value="49" gmtAdjustment="GMT+03:30" useDaylightTime="1" timeZoneOffset="3.5" <?php if (get_option('mp_ssv_event_time_zone') == "49") { echo "selected"; } ?>>(GMT+03:30) Tehran</option>
						<option value="50" gmtAdjustment="GMT+04:00" useDaylightTime="0" timeZoneOffset="4" <?php if (get_option('mp_ssv_event_time_zone') == "50") { echo "selected"; } ?>>(GMT+04:00) Abu Dhabi, Muscat</option>
						<option value="51" gmtAdjustment="GMT+04:00" useDaylightTime="1" timeZoneOffset="4" <?php if (get_option('mp_ssv_event_time_zone') == "51") { echo "selected"; } ?>>(GMT+04:00) Baku</option>
						<option value="52" gmtAdjustment="GMT+04:00" useDaylightTime="1" timeZoneOffset="4" <?php if (get_option('mp_ssv_event_time_zone') == "52") { echo "selected"; } ?>>(GMT+04:00) Yerevan</option>
						<option value="53" gmtAdjustment="GMT+04:30" useDaylightTime="0" timeZoneOffset="4.5" <?php if (get_option('mp_ssv_event_time_zone') == "53") { echo "selected"; } ?>>(GMT+04:30) Kabul</option>
						<option value="54" gmtAdjustment="GMT+05:00" useDaylightTime="1" timeZoneOffset="5" <?php if (get_option('mp_ssv_event_time_zone') == "54") { echo "selected"; } ?>>(GMT+05:00) Yekaterinburg</option>
						<option value="55" gmtAdjustment="GMT+05:00" useDaylightTime="0" timeZoneOffset="5" <?php if (get_option('mp_ssv_event_time_zone') == "55") { echo "selected"; } ?>>(GMT+05:00) Islamabad, Karachi, Tashkent</option>
						<option value="56" gmtAdjustment="GMT+05:30" useDaylightTime="0" timeZoneOffset="5.5" <?php if (get_option('mp_ssv_event_time_zone') == "56") { echo "selected"; } ?>>(GMT+05:30) Sri Jayawardenapura</option>
						<option value="57" gmtAdjustment="GMT+05:30" useDaylightTime="0" timeZoneOffset="5.5" <?php if (get_option('mp_ssv_event_time_zone') == "57") { echo "selected"; } ?>>(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
						<option value="58" gmtAdjustment="GMT+05:45" useDaylightTime="0" timeZoneOffset="5.75" <?php if (get_option('mp_ssv_event_time_zone') == "58") { echo "selected"; } ?>>(GMT+05:45) Kathmandu</option>
						<option value="59" gmtAdjustment="GMT+06:00" useDaylightTime="1" timeZoneOffset="6" <?php if (get_option('mp_ssv_event_time_zone') == "59") { echo "selected"; } ?>>(GMT+06:00) Almaty, Novosibirsk</option>
						<option value="60" gmtAdjustment="GMT+06:00" useDaylightTime="0" timeZoneOffset="6" <?php if (get_option('mp_ssv_event_time_zone') == "60") { echo "selected"; } ?>>(GMT+06:00) Astana, Dhaka</option>
						<option value="61" gmtAdjustment="GMT+06:30" useDaylightTime="0" timeZoneOffset="6.5" <?php if (get_option('mp_ssv_event_time_zone') == "61") { echo "selected"; } ?>>(GMT+06:30) Yangon (Rangoon)</option>
						<option value="62" gmtAdjustment="GMT+07:00" useDaylightTime="0" timeZoneOffset="7" <?php if (get_option('mp_ssv_event_time_zone') == "62") { echo "selected"; } ?>>(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
						<option value="63" gmtAdjustment="GMT+07:00" useDaylightTime="1" timeZoneOffset="7" <?php if (get_option('mp_ssv_event_time_zone') == "63") { echo "selected"; } ?>>(GMT+07:00) Krasnoyarsk</option>
						<option value="64" gmtAdjustment="GMT+08:00" useDaylightTime="0" timeZoneOffset="8" <?php if (get_option('mp_ssv_event_time_zone') == "64") { echo "selected"; } ?>>(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
						<option value="65" gmtAdjustment="GMT+08:00" useDaylightTime="0" timeZoneOffset="8" <?php if (get_option('mp_ssv_event_time_zone') == "65") { echo "selected"; } ?>>(GMT+08:00) Kuala Lumpur, Singapore</option>
						<option value="66" gmtAdjustment="GMT+08:00" useDaylightTime="0" timeZoneOffset="8" <?php if (get_option('mp_ssv_event_time_zone') == "66") { echo "selected"; } ?>>(GMT+08:00) Irkutsk, Ulaan Bataar</option>
						<option value="67" gmtAdjustment="GMT+08:00" useDaylightTime="0" timeZoneOffset="8" <?php if (get_option('mp_ssv_event_time_zone') == "67") { echo "selected"; } ?>>(GMT+08:00) Perth</option>
						<option value="68" gmtAdjustment="GMT+08:00" useDaylightTime="0" timeZoneOffset="8" <?php if (get_option('mp_ssv_event_time_zone') == "68") { echo "selected"; } ?>>(GMT+08:00) Taipei</option>
						<option value="69" gmtAdjustment="GMT+09:00" useDaylightTime="0" timeZoneOffset="9" <?php if (get_option('mp_ssv_event_time_zone') == "69") { echo "selected"; } ?>>(GMT+09:00) Osaka, Sapporo, Tokyo</option>
						<option value="70" gmtAdjustment="GMT+09:00" useDaylightTime="0" timeZoneOffset="9" <?php if (get_option('mp_ssv_event_time_zone') == "70") { echo "selected"; } ?>>(GMT+09:00) Seoul</option>
						<option value="71" gmtAdjustment="GMT+09:00" useDaylightTime="1" timeZoneOffset="9" <?php if (get_option('mp_ssv_event_time_zone') == "71") { echo "selected"; } ?>>(GMT+09:00) Yakutsk</option>
						<option value="72" gmtAdjustment="GMT+09:30" useDaylightTime="0" timeZoneOffset="9.5" <?php if (get_option('mp_ssv_event_time_zone') == "72") { echo "selected"; } ?>>(GMT+09:30) Adelaide</option>
						<option value="73" gmtAdjustment="GMT+09:30" useDaylightTime="0" timeZoneOffset="9.5" <?php if (get_option('mp_ssv_event_time_zone') == "73") { echo "selected"; } ?>>(GMT+09:30) Darwin</option>
						<option value="74" gmtAdjustment="GMT+10:00" useDaylightTime="0" timeZoneOffset="10" <?php if (get_option('mp_ssv_event_time_zone') == "74") { echo "selected"; } ?>>(GMT+10:00) Brisbane</option>
						<option value="75" gmtAdjustment="GMT+10:00" useDaylightTime="1" timeZoneOffset="10" <?php if (get_option('mp_ssv_event_time_zone') == "75") { echo "selected"; } ?>>(GMT+10:00) Canberra, Melbourne, Sydney</option>
						<option value="76" gmtAdjustment="GMT+10:00" useDaylightTime="1" timeZoneOffset="10" <?php if (get_option('mp_ssv_event_time_zone') == "76") { echo "selected"; } ?>>(GMT+10:00) Hobart</option>
						<option value="77" gmtAdjustment="GMT+10:00" useDaylightTime="0" timeZoneOffset="10" <?php if (get_option('mp_ssv_event_time_zone') == "77") { echo "selected"; } ?>>(GMT+10:00) Guam, Port Moresby</option>
						<option value="78" gmtAdjustment="GMT+10:00" useDaylightTime="1" timeZoneOffset="10" <?php if (get_option('mp_ssv_event_time_zone') == "78") { echo "selected"; } ?>>(GMT+10:00) Vladivostok</option>
						<option value="79" gmtAdjustment="GMT+11:00" useDaylightTime="1" timeZoneOffset="11" <?php if (get_option('mp_ssv_event_time_zone') == "79") { echo "selected"; } ?>>(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
						<option value="80" gmtAdjustment="GMT+12:00" useDaylightTime="1" timeZoneOffset="12" <?php if (get_option('mp_ssv_event_time_zone') == "80") { echo "selected"; } ?>>(GMT+12:00) Auckland, Wellington</option>
						<option value="81" gmtAdjustment="GMT+12:00" useDaylightTime="0" timeZoneOffset="12" <?php if (get_option('mp_ssv_event_time_zone') == "81") { echo "selected"; } ?>>(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
						<option value="82" gmtAdjustment="GMT+13:00" useDaylightTime="0" timeZoneOffset="13" <?php if (get_option('mp_ssv_event_time_zone') == "82") { echo "selected"; } ?>>(GMT+13:00) Nuku'alofa</option>
					</select>		
				</td>
			</tr>
		</table>
		
		<?php submit_button(); ?>

	</form>
	</div>
	<?php
}
add_action('admin_menu', 'addMPSSVEventsOptions');

function mp_ssv_settings_save() {
	global $options;
	if (isset($_POST['mp_ssv_event_guest_registration'])) {
		update_option('mp_ssv_event_guest_registration', 'true');
	} else {
		update_option('mp_ssv_event_guest_registration', 'false');
	}
	update_option('mp_ssv_event_default_registration_status', $_POST['mp_ssv_event_default_registration_status']);
	update_option('mp_ssv_event_registration_message', $_POST['mp_ssv_event_registration_message']);
	update_option('mp_ssv_event_cancelation_message', $_POST['mp_ssv_event_cancelation_message']);
	update_option('mp_ssv_event_default_start_time', $_POST['mp_ssv_event_default_start_time']);
	update_option('mp_ssv_event_default_end_time', $_POST['mp_ssv_event_default_end_time']);
	update_option('mp_ssv_event_time_zone', $_POST['mp_ssv_event_time_zone']);
}

function register_my_cool_plugin_settings() {
	//register our settings
	register_setting( 'mp-ssv-events-options-group', 'mp_ssv_event_guest_registration' );
	register_setting( 'mp-ssv-events-options-group', 'mp_ssv_event_default_registration_status' );
	register_setting( 'mp-ssv-events-options-group', 'mp_ssv_event_registration_message' );
	register_setting( 'mp-ssv-events-options-group', 'mp_ssv_event_cancelation_message' );
	register_setting( 'mp-ssv-events-options-group', 'mp_ssv_event_default_start_time' );
	register_setting( 'mp-ssv-events-options-group', 'mp_ssv_event_default_end_time' );
	register_setting( 'mp-ssv-events-options-group', 'mp_ssv_event_time_zone' );
}
add_action( 'admin_init', 'register_my_cool_plugin_settings' );
?>