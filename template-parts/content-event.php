<?php
/**
 * The template part for displaying content
 *
 * @package    Moridrin
 * @subpackage SSV
 * @since      SSV 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('card panel panel-with-header'); ?>>
    <div class="card large">
        <div class="card-image waves-effect waves-block waves-light">
            <?php mp_ssv_post_thumbnail(true, array('class' => 'activator')); ?>
        </div>
        <div class="card-content">
            <header class="entry-header">
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>
                <h2 class="card-title activator"><?= the_title() ?></h2>
            </header>
            <div class="row">
                <div class="col s8">
                    <?php the_content('View Event'); ?>
                </div>
                <div class="col s4">
                    <div class="row">
                        <div class="col s6">Start Date:</div>
                        <div class="col s6">2017-02-05</div>
                        <div class="col s6">Start Time:</div>
                        <div class="col s6">20:00</div>
                        <div class="col s6">End Date:</div>
                        <div class="col s6">2017-02-05</div>
                        <div class="col s6">End Time:</div>
                        <div class="col s6">22:00</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-reveal" style="overflow: hidden;">
            <header class="entry-header">
                <?php if (is_sticky() && is_home() && !is_paged()) : ?>
                    <span class="sticky-post">Featured</span>
                <?php endif; ?>

                <h2 class="card-title activator"><?= the_title() ?><i class="material-icons right">close</i></h2>
            </header>
            <?php
            global $post;
            $event               = Event::get_by_id($post->ID);
            $event_registrations = $event->getRegistrations();
            if (count($event_registrations) > 0) : ?>
                <div class="row" style="max-height: 350px; overflow: auto">
                    <div class="col s8">
                        <?php the_content('View Event'); ?>
                    </div>
                    <div class="col s4">
                        <ul class="collection with-header">
                            <li class="collection-header"><h3>Registrations</h3></li>
                            <?php foreach ($event_registrations as $event_registration) : ?>
                                <?php /* @var $event_registration Registration */ ?>
                                <li class="collection-item avatar">
                                    <img src="<?= get_avatar_url($event_registration->email); ?>" alt="" class="circle">
                                    <span class="title"><?= $event_registration->firstName . ' ' . $event_registration->lastName ?></span>
                                    <p><?= $event_registration->status ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php else : ?>
                <?php the_content('View Event', true); ?>
            <?php endif; ?>
            <div class="card-action">
                <?php if (is_user_logged_in()) : ?>
                    <div>
                        <?php if (FrontendMember::get_current_user()->goesToEvent(get_the_ID())) : ?>
                            <a href="<?= get_permalink() . '?register=' . get_current_user_id() ?>" class="register_link">Cancel Registration</a>
                        <?php else : ?>
                            <a href="<?= get_permalink() . '?register=' . get_current_user_id() ?>" class="register_link">Register</a>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <a href="/login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>

