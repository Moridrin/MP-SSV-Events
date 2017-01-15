<?php

/**
 * Created by PhpStorm.
 * User: moridrin
 * Date: 25-7-16
 * Time: 0:08
 */
class Registration
{
    #region Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DENIED = 'denied';

    const MODE_DISABLED = 'disabled';
    const MODE_MEMBERS_ONLY = 'members_only';
    const MODE_EVERYONE = 'everyone';
    #endregion

    #region Variables
    /** @var int */
    public $registrationID;

    /** @var Event */
    public $event;

    /** @var string One of the STATUS_ constants. */
    public $status;

    /** @var User */
    public $user;
    #endregion

    #region Construct
    /**
     * Registration constructor.
     *
     * @param int       $registrationID
     * @param Event     $event
     * @param string    $status
     * @param User|null $user
     */
    private function __construct($registrationID, $event = null, $status = null, $user = null)
    {
        global $wpdb;
        $tableName = SSV_Events::TABLE_REGISTRATION;

        $this->registrationID = $registrationID;

        if ($event == null) {
            $this->event = Event::getByID($wpdb->get_var("SELECT eventID FROM $tableName WHERE ID = $registrationID"));
        } else {
            $this->event = $event;
        }

        if ($status == null) {
            $this->status = $wpdb->get_var("SELECT registration_status FROM $tableName WHERE ID = $registrationID");
        } else {
            $this->status = $status;
        }

        if ($status == null) {
            $this->user = User::getByID($wpdb->get_var("SELECT userID FROM $tableName WHERE ID = $registrationID"));
        } else {
            $this->user = $user;
        }
    }

    /**
     * @param Event $event
     * @param User  $user
     *
     * @return Registration
     */
    public static function getByEventAndUser($event, $user)
    {
        global $wpdb;
        $tableName      = SSV_Events::TABLE_REGISTRATION;
        $eventID        = $event->getID();
        $registrationID = $wpdb->get_var("SELECT ID FROM $tableName WHERE eventID = $eventID AND userID = $user->ID");
        return new Registration($registrationID);
    }

    /**
     * @param int $registrationID
     *
     * @return Registration
     */
    public static function getByID($registrationID)
    {
        return new Registration($registrationID);
    }
    #endregion

    #region createNew($event, $user, $args)
    /**
     * This function creates the database entries, sends an email to the event author and returns the newly created Registration object.
     *
     * @param Event     $event
     * @param User|null $user
     * @param string[]  $args
     *
     * @return Registration
     */
    public static function createNew($event, $user = null, $args = array())
    {
        $status = get_option(SSV_Events::OPTION_DEFAULT_REGISTRATION_STATUS);
        global $wpdb;
        $wpdb->insert(
            SSV_Events::TABLE_REGISTRATION,
            array(
                'userID'              => $user ? $user->ID : null,
                'eventID'             => $event->getID(),
                'registration_status' => $status,
            ),
            array(
                '%d',
                '%d',
                '%s',
            )
        );
        $registrationID = $wpdb->insert_id;
        foreach ($args as $key => $value) {
            $wpdb->insert(
                SSV_Events::TABLE_REGISTRATION_META,
                array(
                    'registrationID' => $registrationID,
                    'meta_key'       => $key,
                    'meta_value'     => $value,
                ),
                array(
                    '%d',
                    '%s',
                    '%s',
                )
            );
        }

        $registration = new Registration($registrationID, $event, $status, $user);
        if (get_option(SSV_Events::OPTION_EMAIL_AUTHOR)) {
            $eventTitle = Event::getByID($event->getID())->post->post_title;
            $to         = User::getByID(Event::getByID($event->getID())->post->post_author)->user_email;
            $subject    = "New Registration for " . $eventTitle;
            if ($user != null) {
                $message = 'User ' . $user->display_name . ' has registered for ' . $eventTitle . '.';
            } else {
                $message = 'Someone has registered for ' . $eventTitle . ' with the following information:<br/>';
                foreach ($args as $key => $value) {
                    $message .= $key . ': ' . $value;
                }
            }
            wp_mail($to, $subject, $message);
        }
        do_action(SSV_Events::HOOK_REGISTRATION, $registration);

        return $registration;
    }
    #endregion

    #region cancel()
    /**
     * This function removes the database entries and sends an email to the event author (if needed).
     */
    public function cancel()
    {
        global $wpdb;
        $userID  = $this->user->ID;
        $eventID = $this->event->getID();
        $wpdb->delete(SSV_Events::TABLE_REGISTRATION, array('userID' => $userID, 'eventID' => $eventID));
        $wpdb->delete(SSV_Events::TABLE_REGISTRATION_META, array('registrationID' => $this->registrationID));

        if (get_option(SSV_Events::OPTION_EMAIL_AUTHOR)) {
            $eventTitle = $this->event->post->post_title;
            $to         = User::getByID($this->event->post->post_author)->user_email;
            $subject    = "Cancellation for " . $eventTitle;
            $message    = $this->user->display_name . ' has just canceled his/her registration for ' . $eventTitle . '.';
            wp_mail($to, $subject, $message);
        }
    }
    #endregion

    #region getMeta($key, $userMeta)
    /**
     * @param      $key
     *
     * @return null|string with the value matched by the key.
     */
    public function getMeta($key)
    {
        $value = isset($this->user) ? $this->user->getMeta($key) : null;
        if (empty($value)) {
            global $wpdb;
            $tableName = SSV_Events::TABLE_REGISTRATION_META;
            $value     = $wpdb->get_var("SELECT meta_value FROM $tableName WHERE registrationID = $this->registrationID AND meta_key = '$key'");
        }
        return $value;
    }
    #endregion

    #region approve()
    public function approve()
    {
        global $wpdb;
        $table = SSV_Events::TABLE_REGISTRATION;
        $wpdb->replace(
            $table,
            array(
                "ID"                  => $this->registrationID,
                "eventID"             => $this->event->getID(),
                "userID"              => $this->user->ID,
                "registration_status" => self::STATUS_APPROVED,
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%s',
            )
        );
        if (get_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED)) {
            $eventTitle = $this->event->post->post_title;
            $to         = $this->getMeta('email');
            $subject    = "Registration Approved";
            $message    = 'Your registration for ' . $eventTitle . ' has been approved.';
            wp_mail($to, $subject, $message);
        }
    }
    #endregion

    #region deny()
    public function deny()
    {
        global $wpdb;
        $table = SSV_Events::TABLE_REGISTRATION;
        $wpdb->replace(
            $table,
            array(
                "ID"                  => $this->registrationID,
                "eventID"             => $this->event->getID(),
                "userID"              => $this->user->ID,
                "registration_status" => self::STATUS_DENIED,
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%s',
            )
        );
        if (get_option(SSV_Events::OPTION_EMAIL_ON_REGISTRATION_STATUS_CHANGED)) {
            $eventTitle = $this->event->post->post_title;
            $to         = $this->getMeta('email');
            $subject    = "Registration Denied";
            $message    = 'Your registration for ' . $eventTitle . ' has been denied.';
            wp_mail($to, $subject, $message);
        }
    }
    #endregion
}
