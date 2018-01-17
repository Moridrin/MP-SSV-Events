<?php

use mp_ssv_events\models\Event;

if (!defined('ABSPATH')) {
    exit;
}

#region setup variables
global $post;
$event = Event::getByID($post->ID);
#endregion
?>
<article id="post-<?php the_ID(); ?>">
    <div class="card-content">
        <div class="post-title">
            <h2><?= the_title() ?></h2>
        </div>
        <div class="row">
            <div class="col s12 m8">
                <?php the_content('View Event') ?>
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
        <a href="<?= get_permalink() ?>" title="Read More" class="read-more">
            View Event <i class="tiny material-icons right">arrow_forward</i>
        </a>
    </div>
</article>
