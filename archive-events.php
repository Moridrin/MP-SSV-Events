<?php

$args = array(
    'posts_per_page' => 10,
    'paged'          => get_query_var('paged'),
    'post_type'      => 'events',
    'meta_key'       => 'start_date',
    'meta_value'     => date("Y-m-d", time()),
    'orderby'        => 'meta_value',
);

$args['meta_compare'] = '>=';
$args['order']        = 'ASC';
$upcomingEvents       = new WP_Query($args);

$args['posts_per_page'] = 10 - $upcomingEvents->post_count;
$args['meta_compare']   = '<';
$args['order']          = 'DESC';
$pastEvents             = new WP_Query($args);

get_header() ?>
    <div id="page" class="container">
        <div class="row">
            <div class="col s12 <?= is_dynamic_sidebar() ? 'col m8 l9' : '' ?>">
                <div id="primary" class="content-area">
                    <main id="main" class="site-main" role="main">
                        <?php
                        if (get_theme_support('materialize')) {
                            mp_ssv_events_content_theme_default($upcomingEvents, $pastEvents);
                        } else {
                            mp_ssv_events_content_custom($upcomingEvents, $pastEvents);
                        }
                        ?>
                    </main>
                </div>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
<?php get_footer(); ?>

<?php
/**
 * @param WP_Query $upcomingEvents
 * @param WP_Query $pastEvents
 */
function mp_ssv_events_content_theme_default($upcomingEvents, $pastEvents)
{
    $hasUpcomingEvents = $upcomingEvents->have_posts();
    $hasPastEvents     = $pastEvents->have_posts();
    if ($hasPastEvents || $hasPastEvents) {
        if ($hasUpcomingEvents) {
            echo '<div class="panel"><h1>Upcoming</h1></div>';
            while ($upcomingEvents->have_posts()) {
                $upcomingEvents->the_post();
                require 'template-parts/content-event.php';
            }
        }
        if ($hasPastEvents) {
            echo '<div class="panel"><h1>Past</h1></div>';
            while ($pastEvents->have_posts()) {
                $pastEvents->the_post();
                require 'template-parts/content-event.php';
            }
        }
        echo mp_ssv_get_pagination();
    } else {
        get_template_part('template-parts/content', 'none');
    }
}

function mp_ssv_events_content_custom($hasUpcomingEvents, $hasPastEvents)
{
    if (have_posts()) {
        ?>
        <div style="padding: 10px;" class="mui--hidden-xs">

            <table style="width: 100%;" class="sortable">
                <?php
                if ($hasUpcomingEvents) {
                    ?>
                    <tr>
                        <th colspan="3"><h1>Upcoming Events</h1></th>
                    </tr>
                    <tr>
                        <th>Title</th>
                        <th class="mui--hidden-sm mui--hidden-md">When</th>
                        <th style="text-align: center;">Banner</th>
                    </tr>
                    <?php

                    $upcomingEvents = array();
                    $args           = array(
                        'posts_per_page' => 10,
                        'paged'          => get_query_var('page'),
                        'post_type'      => 'events',
                        'meta_key'       => 'start_date',
                        'orderby'        => 'meta_value',
                    );
                    $my_query       = new WP_Query($args);
                    while ($my_query->have_posts()) {
                        $my_query->the_post();
                        if (get_post_meta(get_the_ID(), 'start_date', true) >= date("Y-m-d")) {
                            ob_start();
                            ssv_get_event();
                            $upcomingEvents[] = ob_get_clean();
                        }
                    }
                    $upcomingEvents = array_reverse($upcomingEvents);
                    echo implode('', $upcomingEvents);
                }
                if ($hasPastEvents) {
                    ?>
                    <tr>
                        <th colspan="3"><h1>Past Events</h1></th>
                    </tr>
                    <tr>
                        <th>Title</th>
                        <th class="mui--hidden-sm mui--hidden-md">When</th>
                        <th style="text-align: center;">Banner</th>
                    </tr>
                    <?php
                    while (have_posts()) {
                        the_post();
                        if (get_post_meta(get_the_ID(), 'start_date', true) < date("Y-m-d")) {
                            ssv_get_event();
                        }
                    }
                }
                ?>
            </table>
        </div>
        <div class="mui--visible-xs-block">
            <?php
            if ($hasUpcomingEvents) {
                ?>
                <h1>Upcoming Events</h1>
                <?php
                $upcomingEvents = array();
                while (have_posts()) {
                    the_post();
                    if (get_post_meta(get_the_ID(), 'start_date', true) >= date("Y-m-d")) {
                        ob_start();
                        ssv_get_xs_event();
                        $upcomingEvents[] = ob_get_clean();
                    }
                }
                $upcomingEvents = array_reverse($upcomingEvents);
                echo implode('', $upcomingEvents);
            }
            if ($hasPastEvents) {
                ?>
                <h1>Past Events</h1>
                <?php
                while (have_posts()) {
                    the_post();
                    if (get_post_meta(get_the_ID(), 'start_date', true) < date("Y-m-d")) {
                        ssv_get_xs_event();
                    }
                }
            }
            ?>
        </div>
        <?php

        // Previous/next page navigation.
        the_posts_pagination(
            array(
                'prev_text'          => __('Previous page', 'ssv'),
                'next_text'          => __('Next page', 'ssv'),
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __(
                        'Page',
                        'ssv'
                    ) . ' </span>',
            )
        );
    } else {
        get_template_part('template-parts/content', 'none');
    }
}

function ssv_get_event()
{
    $event = Event::get_by_id(get_the_ID());
    ?>
    <tr>
        <th style="padding-right:5px;">
            <a href="<?php echo get_permalink(get_the_ID()); ?>"><?php the_title(); ?></a><br/>
        </th>
        <th style="padding-left: 0; padding-right:5px; white-space: nowrap;" class="mui--hidden-sm mui--hidden-md">
            <?php $event->echoStartDate(); ?>
            <?php $event->echoEndDate(); ?>
            <a target="_blank" href="<?php echo $event->getGoogleCalendarURL(); ?>">Google Calendar</a>
            <br/>
            <a target="_blank" href="<?php echo $event->getLiveCalendarURL(); ?>">Live Calendar</a>
        </th>
        <td class="mobile-hide" style="padding: 5px;">
            <?php
            if (has_post_thumbnail(get_the_ID())) {
                echo '<a href="' . get_permalink(get_the_ID()) . '">';
                echo get_the_post_thumbnail(get_the_ID(), 'ssv-banner-m');
                echo "</a>";
            } else {
                /** @noinspection SpellCheckingInspection */
                echo '<a href="' . get_permalink(get_the_ID())
                     . '"><img src="https://placeholdit.imgix.net/~text?txtsize=150&txt=No%20Banner%20Set&w=1920&h=480"/></a>';
            } ?>
        </td>
    </tr>
    <?php
}

function ssv_get_xs_event()
{
    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'ssv-banner-s');
    $url   = $thumb[0];
    ?>
    <a href="<?php echo get_permalink(get_the_ID()); ?>">
        <div style="text-align: center;
                margin: 10px;
                padding-bottom: 25%;
                position: relative;
                background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url(<?php echo $url; ?>);
                background-size: cover;">
            <h1 style="position: absolute; top: 50%; margin-top: -1.6em; width: 100%;  color: #ffffff;">
                <?php the_title(); ?>
            </h1>
        </div>
    </a>
    <?php
}
