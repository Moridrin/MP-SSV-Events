<?php

namespace mp_ssv_events;

use mp_ssv_general\base\BaseFunctions;
use mp_ssv_general\base\SSV_Global;

if (!defined('ABSPATH')) {
    exit;
}

abstract class Options
{
    public static function setupForBlog()
    {
        SSV_Global::addSettingsPage('SSV Events', 'Events', 'edit_posts', [self::class, 'showPage']);
        add_action('admin_init', [self::class, 'registerSettings']);
    }

    public static function registerSettings()
    {
        register_setting('ssv-events', 'show_shared_events', ['type' => 'boolean']);
        register_setting('ssv-events', 'some_other_option');
        register_setting('ssv-events', 'option_etc');
    }

    public static function showPage()
    {
        ?>
        <div class="wrap">
            <h1>SSV Events</h1>
            <form method="post" action="options.php">
                <?php settings_fields('ssv-events'); ?>
                <?php do_settings_sections('ssv-events'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="show_shared_events_id">Show Shared Events</label></th>
                        <td><input type="checkbox" name="show_shared_events" value="1" id="show_shared_events_id" <?= checked(get_option('show_shared_events')); ?>/></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

add_action('admin_menu', [Options::class, 'setupForBlog']);
