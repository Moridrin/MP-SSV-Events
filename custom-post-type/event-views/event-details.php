<?php
use mp_ssv_events\models\Event;
use mp_ssv_events\models\Registration;
use mp_ssv_events\SSV_Events;
use mp_ssv_general\Form;
use mp_ssv_general\Message;
use mp_ssv_general\SSV_General;
use mp_ssv_general\User;

get_header();
$event               = Event::getByID($post->ID);
$event_registrations = $event->getRegistrations();
?>
<div id="page" class="container <?= is_admin_bar_showing() ? 'wpadminbar' : '' ?>">
    <div class="row">
        <div class="col s12 <?= is_dynamic_sidebar() ? 'm7 l8 xxl9' : '' ?>">
            <div id="primary" class="content-area card">
                <div class="card-image">
                    <?php if (has_post_thumbnail()): ?>
                        <img src="<?php the_post_thumbnail_url() ?>"/>
                    <?php else: ?>
                        <div class="thumbnail-placeholder"></div>
                    <?php endif ?>
                    <div class="card-overlay hide-on-small valign-wrapper">
                        <div class="page-title" style="margin: 0;">
                            <h1><?= the_title() ?></h1>
                            <h3 style="text-transform: none;"><?= $event->getStart('F j<\s\up>S</\s\up> @ H:i') ?> <?= $event->getEnd() != false && $event->getEnd() != $event->getStart() ? ' - ' . $event->getEnd('F j<\s\up>S</\s\up> @ H:i') : '' ?></h3>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                        <?php
                        the_post();
                        the_content();
                        ?>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <h3>Details</h3>
                    <div class="row">
                        <div class="col s12 xl6">
                            <h5>Date and time</h5>
                            <div class="row" style="border-left: solid; margin-left: 0; margin-right: 0;">
                                <?php if ($event->getEnd() != false && $event->getEnd() != $event->getStart()): ?>
                                    <div class="col s3">From:</div>
                                    <div class="col s9"><?= esc_html($event->getStart()) ?></div>
                                    <div class="col s3">Till:</div>
                                    <div class="col s9"><?= esc_html($event->getEnd()) ?></div>
                                <?php else : ?>
                                    <div class="col s3">Start:</div>
                                    <div class="col s9"><?= esc_html($event->getStart()) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($event->getLocation())): ?>
                                <h5>Organizer</h5>
                                <div class="row" style="border-left: solid; margin-left: 0; margin-right: 0;">
                                    <div class="col s12"><?= get_the_author() ?></div>
                                    <div class="col s12"><a href="mailto:<?= get_the_author_meta('email') ?>"><?= get_the_author_meta('email') ?></a></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($event->getLocation())): ?>
                            <div class="col s12 xl6">
                                <h5>Location</h5>
                                <div class="row" style="border-left: solid; margin-left: 0; margin-right: 0;">
                                    <div class="col s12">
                                        <?= $event->getLocation() ?>
                                        <div id="map" style="height: 300px;"></div>
                                        <input type="hidden" id="map_location" value="<?= $event->getLocation() ?>"/>
                                        <script src="https://maps.googleapis.com/maps/api/js?key=<?= get_option(SSV_Events::OPTION_MAPS_API_KEY) ?>&libraries=places&callback=initMap" async defer></script>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="col s12 xl6">
                                <h5>Organizer</h5>
                                <div class="row" style="border-left: solid; margin-left: 0; margin-right: 0;">
                                    <div class="col s12"><?= get_the_author() ?></div>
                                    <div class="col s12"><a href="mailto:<?= get_the_author_meta('email') ?>"><?= get_the_author_meta('email') ?></a></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
            if ($event->isRegistrationPossible()) { ?>
                <div class="card">
                    <div class="card-content">
                        <div class="col s12 xl8" style="padding-bottom: 20px">
                            <h3>Register</h3>
                            <?php
                            if ($event->canRegister()) {
                                if (is_user_logged_in() && $event->isRegistered()) {
                                    ?>
                                    You already are registered for this event. Click the button below to cancel your registration.
                                    <form action="<?= esc_url(get_permalink()) ?>" method="POST">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" name="submit" class="btn waves-effect">Cancel Registration</button>
                                        <?= SSV_General::getFormSecurityFields(SSV_Events::ADMIN_REFERER_REGISTRATION, false, false); ?>
                                    </form>
                                    <?php
                                } else {
                                    $event->showRegistrationForm();
                                }
                            } else {
                                ?>
                                <a href="<?= SSV_General::getLoginURL() ?>" class="btn waves-effect waves-light">Login to Register</a>
                                <?php
                            } ?>
                        </div>
                        <?php if (count($event_registrations) > 0): ?>
                            <div class="col s12 xl4">
                                <?php $event->showRegistrations(); ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php }?>
            <?php if (comments_open() || get_comments_number()): ?>
                <?php comments_template(); ?>
            <?php endif; ?>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>
