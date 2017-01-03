<?php
/**
 * The template part for displaying content
 *
 * @package    Moridrin
 * @subpackage SSV
 * @since      SSV 1.0
 */

#region setup variables
global $post;
$event               = Event::getByID($post->ID);
$event_registrations = $event->getRegistrations();
$content             = get_the_content('');
#endregion
?>
<article id="post-<?php the_ID(); ?>">
    <div class="card hoverable large">
        <div class="card-image waves-effect waves-block waves-light">
            <?php mp_ssv_post_thumbnail(true, array('class' => 'activator')); ?>
        </div>
        <div class="card-content">
            <header class="entry-header">
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>
                <h2 class="card-title activator">
                    <?= the_title() ?>
                    <?php if ($event->isRegistrationEnabled()) : ?>
                        <span class="new badge" data-badge-caption="Registrations"><?= count($event_registrations) ?></span>
                    <?php endif; ?>
                </h2>
            </header>
            <div class="row">
                <div class="col s12 m8">
                    <?= $content ?>
                </div>
                <div class="col s12 m4">
                    <div class="row" style="border-left: solid">
                        <div class="col s3">From:</div>
                        <div class="col s9"><?= $event->getStart() ?></div>
                        <?php if ($event->getEnd()) : ?>
                            <div class="col s3">Till:</div>
                            <div class="col s9"><?= $event->getEnd() ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-action">
            <a href="<?= get_permalink() ?>">View Event</a>
        </div>
        <div class="card-reveal" style="overflow: hidden;">
            <header class="entry-header">
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>
                <h2 class="card-title activator"><?= the_title() ?><i class="material-icons right">close</i></h2>
            </header>
            <?php if (count($event_registrations) > 0) : ?>
                <div class="row" style="max-height: <?= $event->canRegister() ? '435px' : '413px' ?>; overflow: auto">
                    <div class="col s12 m8">
                        <?= $content ?>
                    </div>
                    <div class="col s12 m4">
                        <ul class="collection with-header">
                            <li class="collection-header"><h3>Registrations</h3></li>
                            <?php foreach ($event_registrations as $event_registration) : ?>
                                <?php /* @var $event_registration Registration */ ?>
                                <li class="collection-item avatar">
                                    <img src="<?= get_avatar_url($event_registration->getMeta('email')); ?>" alt="" class="circle">
                                    <span class="title"><?= $event_registration->getMeta('first_name') . ' ' . $event_registration->getMeta('last_name') ?></span>
                                    <p><?= $event_registration->status ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php else : ?>
                <div class="row" style="max-height: <?= $event->canRegister() ? '435px' : '515px' ?>; overflow: auto">
                    <?= $content ?>
                </div>
            <?php endif; ?>
            <?php if ($event->isRegistrationPossible()) : ?>
                <div class="card-action">
                    <?php if (is_user_logged_in()) : ?>
                        <form action="<?= get_permalink() ?>" method="POST" style="margin: 0">
                            <?php if ($event->isRegistered(User::getCurrent())) : ?>
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--danger btn waves-effect waves-light--small">Cancel Registration</button>
                                <?php SSV_General::formSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                            <?php else : ?>
                                <input type="hidden" name="action" value="register">
                                <button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--primary">Register</button>
                                <?php SSV_General::formSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                            <?php endif; ?>
                        </form>
                    <?php elseif ($event->isRegistrationMembersOnly() && !is_user_logged_in()) : ?>
                        <a href="/login">Login</a>
                    <?php else : ?>
                        <a href="<?= get_permalink() ?>">Open Event to Register</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>
