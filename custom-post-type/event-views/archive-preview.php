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
$event               = Event::get_by_id($post->ID);
$event_registrations = $event->getRegistrations();
#endregion
?>
<article id="post-<?php the_ID(); ?>">
    <div class="card hoverable large">
        <div class="card-image waves-effect waves-block waves-light">
            <?php #region image ?>
            <?php mp_ssv_post_thumbnail(true, array('class' => 'activator')); ?>
            <?php #endregion ?>
        </div>
        <div class="card-content">
            <header class="entry-header">
                <?php #region title ?>
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>
                <h2 class="card-title activator">
                    <?= the_title() ?>
                    <?php if ($event->isRegistrationEnabled()) : ?>
                        <span class="new badge" data-badge-caption="Registrations"><?= count($event_registrations) ?></span>
                    <?php endif; ?>
                </h2>
                <?php #endregion ?>
            </header>
            <div class="row">
                <div class="col s8">
                    <?php #region content_preview ?>
                    <?php the_content('View Event'); ?>
                    <?php #endregion ?>
                </div>
                <div class="col s4">
                    <?php #region date_time ?>
                    <div class="row" style="border-left: solid">
                        <div class="col s3">From:</div>
                        <div class="col s9"><?= $event->getStart() ?></div>
                        <?php if ($event->getEnd()) : ?>
                            <div class="col s3">Till:</div>
                            <div class="col s9"><?= $event->getEnd() ?></div>
                        <?php endif; ?>
                    </div>
                    <?php #endregion ?>
                </div>
            </div>
        </div>
        <div class="card-action">
            <a href="<?= get_permalink() ?>">View Event</a>
        </div>
        <div class="card-reveal" style="overflow: hidden;">
            <header class="entry-header">
                <?php #region title ?>
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>
                <h2 class="card-title activator"><?= the_title() ?><i class="material-icons right">close</i></h2>
                <?php #endregion ?>
            </header>
            <?php if (count($event_registrations) > 0) : ?>
                <?php #region with_registrations ?>
                <div class="row" style="max-height: <?= $event->canRegister() ? '335px' : '413px' ?>; overflow: auto">
                    <div class="col s8">
                        <?php #region content ?>
                        <?php the_content('View Event'); ?>
                        <?php #endregion ?>
                    </div>
                    <div class="col s4">
                        <ul class="collection with-header">
                            <li class="collection-header"><h3>Registrations</h3></li>
                            <?php #region registrations ?>
                            <?php foreach ($event_registrations as $event_registration) : ?>
                                <?php /* @var $event_registration Registration */ ?>
                                <li class="collection-item avatar">
                                    <img src="<?= get_avatar_url($event_registration->email); ?>" alt="" class="circle">
                                    <span class="title"><?= $event_registration->firstName . ' ' . $event_registration->lastName ?></span>
                                    <p><?= $event_registration->status ?></p>
                                </li>
                            <?php endforeach; ?>
                            <?php #endregion ?>
                        </ul>
                    </div>
                </div>
                <?php #endregion ?>
            <?php else : ?>
                <?php #region without_registrations ?>
                <div class="row" style="max-height: <?= $event->canRegister() ? '335px' : '413px' ?>; overflow: auto">
                    <?php the_content('View Event', true); ?>
                </div>
                <?php #endregion ?>
            <?php endif; ?>
            <?php if ($event->canRegister()) : ?>
                <div class="card-action">
                    <?php if (is_user_logged_in()) : ?>
                        <?php #region 'Register' / 'Cancel Registration' button ?>
                        <form action="<?= get_permalink() ?>" method="POST" style="margin: 0">
                            <?php if ($event->isRegistered(FrontendMember::get_current_user())) : ?>
                                <?php #region 'Cancel Registration' button ?>
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--danger btn waves-effect waves-light--small">Cancel Registration</button>
                                <?php wp_nonce_field('ssv_events_register_for_event'); ?>
                                <?php #endregion ?>
                            <?php else : ?>
                                <?php #region 'Register' button ?>
                                <input type="hidden" name="action" value="register">
                                <button type="submit" name="submit" class="btn waves-effect waves-light btn waves-effect waves-light--primary">Register</button>
                                <?php wp_nonce_field('ssv_events_register_for_event'); ?>
                                <?php #endregion ?>
                            <?php endif; ?>
                        </form>
                        <?php #endregion ?>
                    <?php else : ?>
                        <?php #region 'Login' button ?>
                        <a href="/login">Login</a>
                        <?php #endregion ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>
