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
    <header class="entry-header">
        <?php if (is_sticky() && is_home() && !is_paged()) : ?>
            <span class="sticky-post">Featured</span>
        <?php endif; ?>
        <h2>
            <?= the_title() ?>
            <?php if ($event->isRegistrationEnabled()) : ?>
                <p><?= esc_html(count($event_registrations)) ?></p>
            <?php endif; ?>
        </h2>
    </header>
    <?php the_post_thumbnail(); ?>
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
    <div>
        <?= $content ?>
    </div>
    <footer class="card-action" style="background-color: #E6E6E6;">
        <a href="<?= esc_url(get_permalink()) ?>" class="btn waves-effect waves-light">Full Post</a>
    </footer>
</article>
