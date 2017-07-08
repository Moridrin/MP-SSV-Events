<?php
use mp_ssv_events\models\Event;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

if (!defined('ABSPATH')) {
    exit;
}

#region setup variables
global $post;
$event               = Event::getByID($post->ID);
$event_registrations = $event->getRegistrations();
$content             = get_the_content('');
#endregion
?>
<article id="post-<?php the_ID(); ?>">
    <div class="card">
        <div class="card-image waves-effect waves-block waves-light">
            <?php mp_ssv_post_thumbnail(true, array('class' => 'activator')); ?>
        </div>
        <div class="card-content">
            <div class="post-title">
                <h3><?= the_title() ?></h3>
                <?php if ($event->isRegistrationEnabled()) : ?>
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
            <a href="<?= get_permalink() ?>" title="Read More" class="read-more">
                View Event <i style="padding-top:3px" class="fa fa-long-arrow-right right"></i>
            </a>
        </div>
    </div>
</article>
