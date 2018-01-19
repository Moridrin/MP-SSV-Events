<?php

use mp_ssv_events\CustomPostTypes\EventsPostType;

list($pastEvents, $upcomingEvents) = EventsPostType::getAllEvents();
get_header(); ?>
<?php if (have_posts()): ?>
    <h1>Upcoming Events</h1>
    <div id="container">
        <?php
        foreach ($upcomingEvents as $upcomingEvent) {
            switch_to_blog($upcomingEvent->blogId);
            global $post, $currentBlogId;
            $post = get_post($upcomingEvent);
            setup_postdata($post);
            ?>
            <div class="blog-grid element<?php consulting_thinkup_input_stylelayout(); ?>">
                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-article'); ?>>
                    <?php if (has_post_thumbnail()) { ?>
                        <header class="entry-header">
                            <?php echo consulting_thinkup_input_blogimage(); ?>
                        </header>
                    <?php } ?>
                    <div class="entry-content">
                        <h2 class="blog-title" style="display: inline;">
                            <a href="<?= esc_url(get_permalink()) ?>" target="<?= $currentBlogId !== get_current_blog_id() ? '_blank' : '_self' ?>" title="<?= get_the_title() ?>"><?= get_the_title() ?></a>
                        </h2>
                        <?php if ($currentBlogId !== get_current_blog_id()) : ?>
                            <div class="entry-meta" style="float: right;">
                                Hosted by <a href="<?= get_home_url() ?>" target="_blank"><?= get_bloginfo() ?></a>
                            </div>
                        <?php endif; ?>
                        <?php consulting_thinkup_input_blogtext(); ?>
                        <?php consulting_thinkup_input_blogmeta(); ?>
                    </div>
                    <div class="clearboth"></div>
                </article><!-- #post-<?php get_the_ID(); ?> -->
            </div>
            <?php
            restore_current_blog();
        }
        ?>
    </div>
    <div class="clearboth"></div>
    <h1>Past Events</h1>
    <div id="container">
        <?php
        foreach ($pastEvents as $pastEvent) {
            switch_to_blog($pastEvent->blogId);
            global $post, $currentBlogId;
            $post = get_post($pastEvent);
            setup_postdata($post);
            ?>
            <div class="blog-grid element<?php consulting_thinkup_input_stylelayout(); ?>">
                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-article'); ?>>
                    <?php if (has_post_thumbnail()) { ?>
                        <header class="entry-header">
                            <?php echo consulting_thinkup_input_blogimage(); ?>
                        </header>
                    <?php } ?>
                    <div class="entry-content">
                        <h2 class="blog-title" style="display: inline;">
                            <a href="<?= esc_url(get_permalink()) ?>" target="<?= $currentBlogId !== get_current_blog_id() ? '_blank' : '_self' ?>" title="<?= get_the_title() ?>"><?= get_the_title() ?></a>
                        </h2>
                        <?php if ($currentBlogId !== get_current_blog_id()) : ?>
                            <div class="entry-meta" style="float: right;">
                                Hosted by <a href="<?= get_home_url() ?>" target="_blank"><?= get_bloginfo() ?></a>
                            </div>
                        <?php endif; ?>
                        <?php consulting_thinkup_input_blogtext(); ?>
                        <?php consulting_thinkup_input_blogmeta(); ?>
                    </div>
                    <div class="clearboth"></div>
                </article><!-- #post-<?php get_the_ID(); ?> -->
            </div>
            <?php
            restore_current_blog();
        }
        ?>
    </div>
    <div class="clearboth"></div>
    <?php the_posts_pagination(
        array(
            'mid_size'  => 2,
            'prev_text' => __('<i class="fa fa-angle-left"></i>', 'consulting'),
            'next_text' => __('<i class="fa fa-angle-right"></i>', 'consulting'),
        )
    ); ?>
<?php else: ?>
    <?php get_template_part('no-results', 'archive'); ?>
<?php endif; ?>
<?php get_footer() ?>
