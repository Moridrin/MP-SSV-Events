<?php

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 25-7-16
 * Time: 0:08
 */

global $wpdb;
define(
    'TABLE_NAME_TMP',
    $wpdb->prefix . "mp_ssv_event_registration"
);

class Registration
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'denied';
    const TABLE_NAME = TABLE_NAME_TMP;

    /**
     * @var string One of the STATUS_ constants.
     */
    public $status;

    /**
     * @var FrontendMember
     */
    public $member;

    /**
     * @var null|string
     */
    public $firstName;

    /**
     * @var null|string
     */
    public $lastName;

    /**
     * @var null|string
     */
    public $email;

    /**
     * Registration constructor.
     *
     * @param int                 $eventId
     * @param string              $status
     * @param FrontendMember|null $userID
     * @param string|null         $first_name
     * @param string|null         $last_name
     * @param string|null         $email
     */
    public function __construct($eventId, $status, $member = null, $first_name = null, $last_name = null, $email = null)
    {
        $this->status = $status;
        if ($member != null) {
            $this->member = $member;
        } else {
            $this->firstName = $first_name;
            $this->lastName = $last_name;
            $this->email = $email;
        }
    }

    /**
     * This function creates the database entries, sends an email to the event author and returns the newly created Registration object.
     *
     * @param int                 $eventId
     * @param FrontendMember|null $member
     * @param string|null         $first_name
     * @param string|null         $last_name
     * @param string|null         $email
     *
     * @return Registration
     */
    public static function createNew($eventId, $member = null, $first_name = null, $last_name = null, $email = null)
    {
        $status = get_option('mp_ssv_event_default_registration_status');
        global $wpdb;
        if ($member != null) {
            $wpdb->insert(
                Registration::TABLE_NAME,
                array(
                    'userID'  => $member->ID,
                    'eventID' => $eventId,
                    'status'  => $status
                ),
                array(
                    '%d',
                    '%d',
                    '%s'
                )
            );
        } elseif ($first_name != null && $last_name != null && $email != null) {
            $wpdb->insert(
                Registration::TABLE_NAME,
                array(
                    'eventID'    => $eventId,
                    'status'     => $status,
                    'first_name' => $_POST['first_name'],
                    'last_name'  => $_POST['last_name'],
                    'email'      => $_POST['email']
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );
        }

        $registration = new Registration($eventId, $status, $member, $first_name, $last_name, $email);

        $eventTitle = Event::get_by_id($eventId)->post->post_title;
        $to = FrontendMember::get_by_id(Event::get_by_id(1)->post->post_author)->user_email;
        $subject = "New Registration for " . $eventTitle;
        if ($member != null) {
            $display_name = $member->display_name;
        } else {
            $display_name = $_POST['first_name'] . " " . $_POST['last_name'];
        }
        $message = $display_name . ' has just registered for ' . $eventTitle . '.';
        wp_mail($to, $subject, $message);

        return $registration;
    }

    /**
     * This function removes the database entries and sends an email to the event author.
     *
     * @param int            $eventId
     * @param FrontendMember $member
     */
    public static function delete($eventId, $member)
    {
        global $wpdb;
        $wpdb->delete(Registration::TABLE_NAME, array('userID' => $member->ID, 'eventID' => $eventId));

        $eventTitle = Event::get_by_id($eventId)->post->post_title;
        $to = FrontendMember::get_by_id(Event::get_by_id(1)->post->post_author)->user_email;
        $subject = "Cancelation for " . $eventTitle;
        $message = $member->display_name . ' has just canceled his/her registration for ' . $eventTitle . '.';
        wp_mail($to, $subject, $message);
    }

    /**
     * @param $event_registration object the database entry
     *
     * @return Registration
     */
    public static function fromDatabase($eventId, $event_registration)
    {
        return new Registration(
            $eventId,
            $event_registration->status,
            $event_registration->userID,
            $event_registration->first_name,
            $event_registration->last_name,
            $event_registration->email
        );
    }
}