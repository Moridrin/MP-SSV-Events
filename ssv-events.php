<?php
/**
 * Plugin Name: SSV Events
 * Plugin URI: http://moridrin.com/ssv-events
 * Description: SSV Events is a plugin that allows you to create events for the Students Sports Club and allows all members from that club to join the event.
 * Version: 1.3.0
 * Author: Jeroen Berkvens
 * Author URI: http://nl.linkedin.com/in/jberkvens/
 * License: WTFPL
 * License URI: http://www.wtfpl.net/txt/copying/
 */
if (!defined('ABSPATH')) {
    exit;
}
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

#region Require Once
require_once 'general/general.php';

require_once "options/options.php";

require_once "models/Event.php";
require_once "models/Registration.php";

require_once "custom-post-type/post-type.php";
require_once "custom-post-type/event-views/page-full.php";

require_once "widgets/category-widget.php";
#endregion

#region SSV_Events class
global $wpdb;
define('SSV_EVENTS_PATH', plugin_dir_path(__FILE__));
define('SSV_EVENTS_URL', plugins_url() . '/ssv-events/');
define('SSV_EVENTS_REGISTRATION_TABLE', $wpdb->prefix . "ssv_event_registration");
define('SSV_EVENTS_REGISTRATION_META_TABLE', $wpdb->prefix . "ssv_event_registration_meta");

class SSV_Events
{
    #region Constants
    const PATH = SSV_EVENTS_PATH;
    const URL = SSV_EVENTS_URL;

    const TABLE_REGISTRATION = SSV_EVENTS_REGISTRATION_TABLE;
    const TABLE_REGISTRATION_META = SSV_EVENTS_REGISTRATION_META_TABLE;

    const HOOK_REGISTRATION = 'mp_ssv_event__hook_registration';

    const OPTION_DEFAULT_REGISTRATION_STATUS = 'ssv_events__default_registration_status';
    const OPTION_REGISTRATION_MESSAGE = 'ssv_events__registration_message';
    const OPTION_REGISTRATION_VERIFICATION_MESSAGE = 'ssv_events__registration_verification_message';
    const OPTION_CANCELLATION_MESSAGE = 'ssv_events__cancellation_message';
    const OPTION_EMAIL_AUTHOR = 'ssv_events__email_author';
    const OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED = 'ssv_events__email_on_registration_status_changed';
    const OPTION_VERIFY_REGISTRATION_BY_EMAIL = 'ssv_events__verify_registration_by_email';
    const OPTION_PUBLISH_ERROR = 'ssv_events__publish_error';

    const ADMIN_REFERER_OPTIONS = 'ssv_events__admin_referer_options';
    const ADMIN_REFERER_REGISTRATION = 'ssv_events__admin_referer_registration';
    #endregion

    #region resetOptions()
    /**
     * This function sets all the options for this plugin back to their default value
     */
    public static function resetOptions()
    {
        update_option(self::OPTION_DEFAULT_REGISTRATION_STATUS, 'pending');
        update_option(self::OPTION_REGISTRATION_MESSAGE, 'Your registration is pending.');
        update_option(self::OPTION_REGISTRATION_VERIFICATION_MESSAGE, 'An email has been send to verify your registration.');
        update_option(self::OPTION_CANCELLATION_MESSAGE, 'Your registration is canceled.');
        update_option(self::OPTION_EMAIL_AUTHOR, true);
        update_option(self::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED, false);
        update_option(self::OPTION_VERIFY_REGISTRATION_BY_EMAIL, false);
        update_option(self::OPTION_PUBLISH_ERROR, false);
    }

    #endregion

    public static function CLEAN_INSTALL() {
        mp_ssv_uninstall_ssv_events();
        mp_ssv_events_register_plugin();
    }
}

#endregion

#region Register
function mp_ssv_events_register_plugin()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();

    #region Registration Table
    $table_name = SSV_Events::TABLE_REGISTRATION;
    $sql
                = "
		CREATE TABLE $table_name (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			eventID bigint(20) NOT NULL,
			userID bigint(20),
			registration_status VARCHAR(15) NOT NULL DEFAULT 'pending',
			PRIMARY KEY (ID)
		) $charset_collate;";
    $wpdb->query($sql);
    #endregion

    #region Registration Meta Table
    $table_name = SSV_Events::TABLE_REGISTRATION_META;
    $sql
                = "
		CREATE TABLE $table_name (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			registrationID bigint(20) NOT NULL,
			meta_key VARCHAR(255) NOT NULL,
			meta_value VARCHAR(255),
			PRIMARY KEY (ID)
		) $charset_collate;";
    $wpdb->query($sql);
    #endregion

    SSV_Events::resetOptions();
}

register_activation_hook(__FILE__, 'mp_ssv_events_register_plugin');
#endregion

#region Unregister
function mp_ssv_unregister_ssv_events()
{
    //Nothing to do here.
}

register_deactivation_hook(__FILE__, 'mp_ssv_unregister_ssv_events');
#endregion

#region UnInstall
function mp_ssv_uninstall_ssv_events()
{
    global $wpdb;
    $wpdb->show_errors();
    $table_name = SSV_Events::TABLE_REGISTRATION;
    $sql        = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
    $table_name = SSV_Events::TABLE_REGISTRATION_META;
    $sql        = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}

register_uninstall_hook(__FILE__, 'mp_ssv_uninstall_ssv_events');
#endregion

#region Reset Options
/**
 * This function will reset the events options if the admin referer originates from the SSV Events plugin.
 *
 * @param $admin_referer
 */
function mp_ssv_events_reset_options($admin_referer)
{
    if (!starts_with($admin_referer, 'ssv_events__')) {
        return;
    }
    SSV_Events::resetOptions();
}

add_filter(SSV_General::HOOK_RESET_OPTIONS, 'mp_ssv_events_reset_options');
#endregion
