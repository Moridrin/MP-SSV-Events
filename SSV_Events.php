<?php

namespace mp_ssv_events;

use wpdb;

if (!defined('ABSPATH')) {
    exit;
}

abstract class SSV_Events
{
    const PATH = SSV_EVENTS_PATH;
    const URL = SSV_EVENTS_URL;

//    const ALL_FORMS_ADMIN_REFERER = 'ssv_forms__all_forms_admin_referer';
//    const EDIT_FORM_ADMIN_REFERER = 'ssv_forms__edit_form_admin_referer';

    const OPTION_PUBLISH_ERROR = 'ssv_events__options__event_publish_error';
    const OPTION_MAPS_API_KEY = 'ssv_events__options__google_maps_api_key';

    const REGISTRATIONS_TABLE = SSV_EVENTS_REGISTRATIONS_TABLE;

    public static function setup($networkEnable)
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        if (is_multisite() && $networkEnable) {
            $blogIds = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        } else {
            $blogIds = [get_current_blog_id()];
        }
        foreach ($blogIds as $blogId) {
            switch_to_blog($blogId);
            $table_name = $wpdb->prefix . "ssv_event_registrations";
            $sql
                        = "
		        CREATE TABLE IF NOT EXISTS $table_name (
		        	ID bigint(20) NOT NULL AUTO_INCREMENT,
		        	eventID bigint(20) NOT NULL,
		        	userID bigint(20),
		        	registration_status VARCHAR(15) NOT NULL DEFAULT 'pending',
		        	PRIMARY KEY (ID)
		        ) $charset_collate;";
            $wpdb->query($sql);
            restore_current_blog();
        }
    }

    public static function enqueueScripts()
    {
        wp_enqueue_script('ssv_events_maps', SSV_Events::URL . '/js/maps.js', array('jquery'));
        wp_enqueue_style('ssv_events_main_css', SSV_Events::URL . '/css/ssv-events.css');
    }

    public static function enqueueAdminScripts()
    {
        wp_enqueue_script('ssv_events_datetimepicker', SSV_Events::URL . '/js/jquery.datetimepicker.full.js', 'jquery-ui-datepicker');
        wp_enqueue_script('ssv_events_datetimepicker_admin_init', SSV_Events::URL . '/js/admin-init.js', 'ssv_events_datetimepicker');
        wp_enqueue_style('ssv_events_datetimepicker_admin_css', SSV_Events::URL . '/css/jquery.datetimepicker.css');
    }

    public static function showMapsApiKeyMissingMessage()
    {
        if (empty(get_option(self::OPTION_MAPS_API_KEY))) {
            ?>
            <div class="update-nag notice">
                <p>You still need to set the Google Maps API Key in order for the maps to work.</p>
                <p><a href="<?= admin_url('admin.php') ?>?page=ssv-events-settings&tab=general">Set Now</a></p>
            </div>
            <?php
        }
    }

    public static function CLEAN_INSTALL($networkWide)
    {
        /** @var wpdb $wpdb */
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $tableName = self::REGISTRATIONS_TABLE;
        $wpdb->query("DROP TABLE $tableName;");
        self::setup($networkWide);
    }
}

register_activation_hook(SSV_FORMS_ACTIVATOR_PLUGIN, [SSV_Events::class, 'setup']);
add_action('admin_enqueue_scripts', [SSV_Events::class, 'enqueueAdminScripts']);
add_action('wp_enqueue_scripts', [SSV_Events::class, 'enqueueScripts']);
