<?php

/**
 * Created by PhpStorm.
 * User: Jeroen Berkvens
 * Date: 16-7-16
 * Time: 8:21
 */
class Event
{
    /**
     * @var WP_Post
     */
    public $post;

    /**
     * @var int the ID of the WP_Post
     */
    public $ID;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $endDate;

    /**
     * @var string
     */
    private $location;

    /**
     * @var bool
     */
    private $registration_enabled;

    /**
     * @var array
     */
    private $registrations;

    /**
     * Event constructor.
     *
     * @param WP_Post $post
     */
    public function __construct($post)
    {
        $this->post = $post;
        $this->ID = $post->ID;
        $this->startDate = DateTime::createFromFormat(
            'Y-m-dH:i', get_post_meta($post->ID, 'start_date', true) . get_post_meta($post->ID, 'start_time', true)
        );
        $this->endDate = DateTime::createFromFormat(
            'Y-m-dH:i', get_post_meta($post->ID, 'end_date', true) . get_post_meta($post->ID, 'end_time', true)
        );
        $this->location = get_post_meta(get_the_ID(), 'location', true);
        $this->registration_enabled = get_post_meta(get_the_ID(), 'registration', true) == 'true';
    }

    /**
     * @param $id
     *
     * @return Event
     */
    public static function get_by_id($id)
    {
        return new Event(get_post($id));
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->post->ID;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param bool $newline set false if you don't want to echo <br/> at the end of the line.
     */
    public function echoStartDate($newline = true)
    {
        if (!$this->startDate) {
            return;
        }
        if ($this->startDate->format('Hi') != '00:00') {
            echo $this->startDate->format('Y-m-d H:i');
        } else {
            echo $this->startDate->format('Y-m-d');
        }
        if ($newline) {
            echo '<br/>';
        }
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param bool $newline set false if you don't want to echo <br/> at the end of the line.
     */
    public function echoEndDate($newline = true)
    {
        if (!$this->endDate) {
            return;
        }
        if ($this->endDate->format('Hi') != '00:00') {
            echo $this->endDate->format('Y-m-d H:i');
        } else {
            echo $this->endDate->format('Y-m-d');
        }
        if ($newline) {
            echo '<br/>';
        }
    }

    /**
     * @return DateTime
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string URL to create Google Calendar Event.
     */
    public function getGoogleCalendarURL()
    {
        $URL = 'https://www.google.com/calendar/render?action=TEMPLATE';
        $URL .= '&text=' . get_the_title($this->post->ID);
        $URL .= '&dates=' . $this->startDate->format('Ymd\\THi00');
        if ($this->endDate != false) {
            $URL .= '/' . $this->endDate->format('Ymd\\THi00');
        } else {
            $URL .= '/' . $this->startDate->format('Ymd\\THi00');
        }
        if (!empty($this->location)) {
            $URL .= '&location=' . $this->location;
        }
        return $URL;
    }

    /**
     * @return string URL to create Live (Hotmail) Event.
     */
    public function getLiveCalendarURL()
    {
        /** @noinspection SpellCheckingInspection */
        $URL = 'http://calendar.live.com/calendar/calendar.aspx?rru=addevent';
        /** @noinspection SpellCheckingInspection */
        $URL .= '&dtstart=' . $this->startDate->format('Ymd\\THi00');
        if ($this->endDate != false) {
            /** @noinspection SpellCheckingInspection */
            $URL .= '$dtend=' . $this->endDate->format('Ymd\\THi00');
        }
        $URL .= '&summary=' . get_the_title($this->post->ID);
        if (!empty($this->location)) {
            $URL .= '&location=' . $this->location;
        }
        return $URL;
    }

    /**
     * @return bool true if the Event is valid (all mandatory fields are filled).
     */
    public function isValid()
    {
        if ($this->startDate == false) {
            return false;
        }
        return true;
    }

    /**
     * @return bool true if the event is published
     */
    public function isPublished()
    {
        if ($this->post->post_status == 'publish') {
            return true;
        }
        return false;
    }

    public function canRegister()
    {
        return $this->isRegistrationEnabled() && $this->startDate > new DateTime();
    }

    /**
     * @return bool
     */
    public function isRegistrationEnabled()
    {
        return $this->registration_enabled;
    }

    /**
     * @param int|WP_User|FrontendMember|null $user use null to test with the current user.
     *
     * @return bool
     */
    public function isRegistered($user = null)
    {
        $userID = null;
        if (is_int($user)) {
            $userID = $user;
        } elseif ($user instanceof WP_User || $user instanceof FrontendMember) {
            $userID = $user->ID;
        } else {
            $userID = get_current_user_id();
        }
        if (count($this->registrations) > 0) {
            foreach ($this->registrations as $registration) {
                if ($registration->member->ID == $userID) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return array of registrations
     */
    public function getRegistrations()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "mp_ssv_event_registration";
        $event_registrations = $wpdb->get_results("SELECT * FROM $table_name WHERE eventID = $this->ID");
        if (!empty($event_registrations)) {
            foreach ($event_registrations as $event_registration) {
                $this->registrations[] = Registration::fromDatabase($this->ID, $event_registration);
            }
        }
        return $this->registrations;
    }
}