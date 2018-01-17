<?php

use mp_ssv_events\SSV_Events;

$start = new DateTime();
$end = new DateTime();
$location = null;
$tickets = [];
$event_registrations = [];

get_header();
?>
<div id="page" class="container <?= is_admin_bar_showing() ? 'wpadminbar' : '' ?>">
    <div>
        <div>
            <div id="primary" class="content-area">
                <div>
                    <?php if (has_post_thumbnail()): ?>
                        <img src="<?php the_post_thumbnail_url() ?>"/>
                    <?php else: ?>
                        <div class="thumbnail-placeholder"></div>
                    <?php endif ?>
                    <div>
                        <div class="page-title">
                            <h1><?= the_title() ?></h1>
                            <h3 style="text-transform: none;"><?= $start->format('F j<\s\up>S</\s\up> @ H:i') ?> <?= $end->getTimestamp() !== $end->getTimestamp() ? ' - ' . $end->format('F j<\s\up>S</\s\up> @ H:i')
                                    : '' ?></h3>
                        </div>
                    </div>
                </div>
                <div>
                    <?php
                    the_post();
                    the_content();
                    ?>
                </div>
            </div>
            <div>
                <div>
                    <h3>Details</h3>
                    <div>
                        <div>
                            <h5>Date and time</h5>
                            <div>
                                    <div>From:</div>
                                    <div><?= esc_html($start->format('Y-m-d : H:i')) ?></div>
                                <?php if ($end->getTimestamp() !== $start->getTimestamp()): ?>
                                    <div>Till:</div>
                                    <div><?= esc_html($end->format('Y-m-d : H:i')) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($location)): ?>
                                <h5>Organizer</h5>
                                <div>
                                    <div><?= get_the_author() ?></div>
                                    <div><a href="mailto:<?= get_the_author_meta('email') ?>"><?= get_the_author_meta('email') ?></a></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($location)): ?>
                            <div>
                                <h5>Location</h5>
                                <div>
                                    <div>
                                        <?= $location ?>
                                        <div id="map" style="height: 300px;"></div>
                                        <input type="hidden" id="map_location" value="<?= $location ?>"/>
                                        <script src="https://maps.googleapis.com/maps/api/js?key=<?= get_option(SSV_Events::OPTION_MAPS_API_KEY) ?>&libraries=places&callback=initMap" async defer></script>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div>
                                <h5>Organizer</h5>
                                <div>
                                    <div><?= get_the_author() ?></div>
                                    <div><a href="mailto:<?= get_the_author_meta('email') ?>"><?= get_the_author_meta('email') ?></a></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
            if (!empty($tickets)) { ?>
                <div>
                    <div>
                        <div>
                            <h3>Register</h3>
                            <?php
                            if (false) {
//                            if ($event->canRegister()) {
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
<!--                        --><?php //if (count($event_registrations) > 0): ?>
                        <?php if (false): ?>
                            <div>
                                <?php $event->showRegistrations(); ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php } ?>
            <?php if (comments_open() || get_comments_number()): ?>
                <?php comments_template(); ?>
            <?php endif; ?>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>
