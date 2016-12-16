<?php
function mp_ssv_add_registrations_to_content($content)
{
    #region set variables or return
    global $post;
    if ($post->post_type != 'events') {
        return $content;
    }
    $event               = Event::get_by_id($post->ID);
    $event_registrations = $event->getRegistrations();
    #endregion

    #region add 'View Event' link when no "<!--more-->" tag is added
    if ($post->post_type == 'events' && is_archive()) {
        if (strpos($content, 'class="more-link"') === false) {
            $content .= '<a href="' . get_permalink($post->ID) . '">View Event</a>';
        }
        return $content;
    }
    #endregion

    #region Save POST Request
    if (isset($_POST['submit']) && check_admin_referer('ssv_events_register_for_event')) {
        if ($_POST['action'] == 'register') {
            Registration::createNew(get_the_ID(), new FrontendMember(wp_get_current_user()));
            $content = '<div class="card-panel primary">' . stripslashes(get_option('ssv_event_registration_message')) . '</div>' . $content;
        } elseif ($_POST['action'] == 'cancel') {
            Registration::delete(get_the_ID(), new FrontendMember(wp_get_current_user()));
            $content = '<div class="card-panel primary">' . stripslashes(get_option('ssv_event_cancellation_message')) . '</div>' . $content;
        }
        $event               = Event::get_by_id($post->ID);
        $event_registrations = $event->getRegistrations();
    }
    #endregion

    #region Add event registrations to content (if any)
    ob_start();
    ?>
    <div class="row">
        <div class="col s8">
            <?= $content ?>
        </div>
        <div class="col s4">
            <h3>When</h3>
            <div class="row" style="border-left: solid; margin-left: 0; margin-right: 0;">
                <?php if ($event->getEnd() != false && $event->getEnd() != $event->getStart()): ?>
                    <div class="col s3">From:</div>
                    <div class="col s9"><?= $event->getStart() ?></div>
                    <div class="col s3">Till:</div>
                    <div class="col s9"><?= $event->getEnd() ?></div>
                <?php else : ?>
                    <div class="col s3">Start:</div>
                    <div class="col s9"><?= $event->getStart() ?></div>
                <?php endif; ?>
            </div>
            <?php #endregion
            ?>
            <?php if (count($event_registrations) > 0): ?>
                <h3>Registrations</h3>
                <ul class="collection with-header">
                    <?php foreach ($event_registrations as $event_registration) : ?>
                        <?php /* @var $event_registration Registration */ ?>
                        <li class="collection-item avatar">
                            <img src="<?= get_avatar_url($event_registration->email); ?>" alt="" class="circle">
                            <span class="title"><?= $event_registration->firstName . ' ' . $event_registration->lastName ?></span>
                            <p><?= $event_registration->status ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php
    #endregion

    #region Add registration button
    if ($event->canRegister()) {
        if (is_user_logged_in()) {
            ?>
            <form action="<?= get_permalink() ?>" method="POST">
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
            <?php
        } else {
            ?>
            <a href="/login" class="btn waves-effect waves-light">Login to Register</a>
            <?php
        }
    }
    $content = ob_get_clean();
    #endregion

    return $content . '<hr/>';
}

function ssv_get_date_time_and_location_block($post)
{
    $event = new Event($post);
    ob_start();
    ?>
    <h1>When</h1>
    <div class="row">
        <div class="col s3">From</div>
        <div class="col s9"><?php $event->echoStartDate(); ?></div>
        <?php if ($event->getEnd() != false && $event->getEnd() != $event->getStart()): ?>
            <div class="col s3">Till</div>
            <div class="col s9"><?php $event->echoStartDate(); ?></div>
        <?php endif; ?>
    </div>
    <a target="_blank" href="<?= $event->getGoogleCalendarURL(); ?>">Google Calendar</a>
    <br/>
    <a target="_blank" href="<?= $event->getLiveCalendarURL(); ?>">Live Calendar</a>
    <?php if (!empty($event->getLocation())): ?>
    <h1>Where</h1>
    <?= $event->getLocation() ?>
<?php endif; ?>
    <?php
    return ob_get_clean();
}

add_filter('the_content', 'mp_ssv_add_registrations_to_content');

function mp_ssv_event_more_tag($more)
{
    global $post;
    if ($post->post_type != 'events') {
        return $more;
    }
    $more = 'ESAC';
    return $more;
}

add_filter('excerpt_more', 'mp_ssv_event_more_tag');

function mp_ssv_custom_excerpt_length($length)
{
    global $post;
    if ($post->post_type != 'events') {
        return $length;
    }
    return 200;
}

add_filter('excerpt_length', 'mp_ssv_custom_excerpt_length', 999);
