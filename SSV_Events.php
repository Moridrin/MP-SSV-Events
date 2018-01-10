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

    const TICKETS_TABLE = SSV_EVENTS_TICKETS_TABLE;
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
            $formsTableName = $wpdb->prefix . 'ssv_forms';
            $tableName      = $wpdb->prefix . "ssv_event_tickets";
            $sql
                            = "
		        CREATE TABLE IF NOT EXISTS $tableName (
		        	t_id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		        	t_e_id bigint(20) NOT NULL,
		        	t_title VARCHAR(255) NOT NULL,
		        	t_start VARCHAR(255) NOT NULL,
		        	t_end VARCHAR(255) NOT NULL,
		        	t_price DECIMAL(6,2) NOT NULL,
		        	t_f_id BIGINT(20) NOT NULL,
		        	UNIQUE KEY (`t_e_id`, `t_title`)
		        ) $charset_collate;";
            $wpdb->query($sql);
            $tableName = $wpdb->prefix . "ssv_event_registrations";
            $sql
                       = "
		        CREATE TABLE IF NOT EXISTS $tableName (
		        	r_id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		        	r_eventId bigint(20) NOT NULL,
		        	r_userId bigint(20),
		        	r_status VARCHAR(15) NOT NULL DEFAULT 'pending'
		        ) $charset_collate;";
            $wpdb->query($sql);
        }
        restore_current_blog();
    }

    public static function enqueueScripts()
    {
        wp_enqueue_script('ssv_events_maps', SSV_Events::URL . '/js/maps.js', array('jquery'));
        wp_enqueue_style('ssv_events_main_css', SSV_Events::URL . '/css/ssv-events.css');
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
        $tableName = self::TICKETS_TABLE;
        $wpdb->query("DROP TABLE $tableName;");
        self::setup($networkWide);
    }
}

register_activation_hook(SSV_FORMS_ACTIVATOR_PLUGIN, [SSV_Events::class, 'setup']);
add_action('wp_enqueue_scripts', [SSV_Events::class, 'enqueueScripts']);
