<?php

namespace mp_ssv_events;

use mp_ssv_events\CustomPostTypes\EventsPostType;

if (!defined('ABSPATH')) {
    exit;
}

list($pastEvents, $upcomingEvents) = EventsPostType::getAllEvents();

get_header();
?>
    <div id="page" class="container <?= is_admin_bar_showing() ? 'wpadminbar' : '' ?>">
        <div class="row">
            <div class="col s12 <?= is_dynamic_sidebar() ? 'm7 l8 xxl9' : '' ?>">
                <div id="primary" class="content-area">
                    <main id="main" class="site-main" role="main">
                        <?php mp_ssv_events_content_theme_default($upcomingEvents, $pastEvents); ?>
                    </main>
                </div>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
    <?php
get_footer();

/**
 * This function prints the default event preview lists (only for themes with support for "materialize").
 *
 * @param array $upcomingEvents
 * @param array $pastEvents
 */
function mp_ssv_events_content_theme_default(array $upcomingEvents, array $pastEvents)
{
    $hasUpcomingEvents = count($upcomingEvents) > 0;
    $hasPastEvents     = count($pastEvents) > 0;
    if ($hasUpcomingEvents || $hasPastEvents) {
        if ($hasUpcomingEvents) {
            ?>
            <header class="full-width-entry-header" style="margin: 15px 0;">
                <div class="parallax-container primary" style="height: 75px;">
                    <div class="shade darken-1 valign-wrapper" style="height: 100%">
                        <h1 class="entry-title center-align white-text valign events-archive-header">Upcoming Events</h1>
                    </div>
                </div>
            </header>
            <?php
            foreach ($upcomingEvents as $upcomingEvent) {
                switch_to_blog($upcomingEvent->blogId);
                global $post;
                $post = get_post($upcomingEvent);
                require 'archive-preview.php';
                restore_current_blog();
            }
        }
        if ($hasPastEvents) {
            ?>
            <header class="full-width-entry-header" style="margin: 15px 0;">
                <div class="parallax-container primary" style="height: 75px;">
                    <div class="shade darken-1 valign-wrapper" style="height: 100%">
                        <h1 class="entry-title center-align white-text valign events-archive-header">Past Events</h1>
                    </div>
                </div>
            </header>
            <?php
            foreach ($pastEvents as $pastEvent) {
                switch_to_blog($pastEvent->blogId);
                global $post;
                $post = get_post($pastEvent);
                require 'archive-preview.php';
                restore_current_blog();
            }
        }
        if (function_exists('mp_ssv_get_pagination')) {
            echo mp_ssv_get_pagination();
        } else {
            echo paginate_links();
        }
    } else {
        get_template_part('template-parts/content', 'none');
    }
}
