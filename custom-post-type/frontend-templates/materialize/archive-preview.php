<?php

use mp_ssv_events\models\Event;
use mp_ssv_general\base\BaseFunctions;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

#region setup variables
global $post, $currentBlogId;
$event               = Event::getByID($post->ID);
$event_registrations = [];
$content             = apply_filters('the_content', $post->post_content);
#endregion
?>
<article id="post-<?php the_ID(); ?>">
    <div class="card">
        <div class="card-image">
            <?php mp_ssv_post_thumbnail(); ?>
        </div>
        <div class="card-content">
            <div class="post-title">
                <h2>
                    <?= the_title() ?>
                    <?php if ($currentBlogId !== get_current_blog_id()) : ?>
                        <a href="<?= get_home_url() ?>" target="_blank"><span class="new badge" data-badge-caption=""><?= get_bloginfo() ?></span></a>
                    <?php endif; ?>
                </h2>
                <?php if (false) : ?>
                    <span class="new badge" data-badge-caption="Registrations"><?= esc_html(count($event_registrations)) ?></span>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col s12 m8">
                    <?= $content ?>
                </div>
                <div class="col s12 m4">
                    <div class="row" style="border-left: solid">
                        <div class="col s3">From:</div>
                        <div class="col s9"><?= esc_html($event->getStart()) ?></div>
                        <?php if ($event->getEnd()) : ?>
                            <div class="col s3">Till:</div>
                            <div class="col s9"><?= esc_html($event->getEnd()) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-action">
            <a href="<?= get_permalink() ?>" title="Read More" class="read-more" target="<?= $currentBlogId !== get_current_blog_id() ? '_blank' : '_self' ?>">
                View Event<?= $currentBlogId !== get_current_blog_id() ? ' (on ' . get_bloginfo() . ')' : '' ?> <i class="tiny material-icons right">arrow_forward</i>
            </a>
        </div>
    </div>
</article>
