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
     * Event constructor.
     *
     * @param WP_Post $post
     */
    public function __construct($post)
    {
        $this->post = $post;
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
        return $this->postId;
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
     * @return bool
     */
    public function isRegistrationEnabled()
    {
        return $this->registration_enabled;
    }

    /**
     * @return string URL to create Google Calendar Event.
     */
    public function getGoogleCalendarURL()
    {
        $URL = 'https://www.google.com/calendar/render?action=TEMPLATE';
        $URL .= '&text=' . get_the_title($this->post->postId);
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
        $URL = 'http://calendar.live.com/calendar/calendar.aspx?rru=addevent';
        $URL .= '&dtstart=' . $this->startDate->format('Ymd\\THi00');
        if ($this->endDate != false) {
            $URL .= '$dtend=' . $this->endDate->format('Ymd\\THi00');
        }
        $URL .= '&summary=' . get_the_title($this->post->postId);
        if (!empty($this->location)) {
            $URL .= '&location=' . $this->location;
        }
        return $URL;
    }

    public function isValid() {
        if ($this->startDate == false) {
            return false;
        }
        return true;
    }

    public function isPublished() {
        if ($this->post->post_status == 'publish') {
            return true;
        }
        return false;
    }
}