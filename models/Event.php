<?php

/**
 * Created by PhpStorm.
 * User: Jeroen Berkvens
 * Date: 16-7-16
 * Time: 8:21
 */
class Event
{
    #region Variables
    /** @var WP_Post */
    public $post;

    /** @var DateTime */
    private $start;

    /** @var DateTime */
    private $end;

    /** @var string */
    private $location;

    /** @var string */
    private $registration;

    /** @var array */
    private $registrations;
    #endregion

    #region Construct
    /**
     * Event constructor.
     *
     * @param WP_Post $post
     */
    public function __construct($post)
    {
        $this->post         = $post;
        $this->start        = DateTime::createFromFormat('Y-m-d H:i', get_post_meta($post->ID, 'start', true));
        $this->end          = DateTime::createFromFormat('Y-m-d H:i', get_post_meta($post->ID, 'end', true));
        $this->location     = get_post_meta($post->ID, 'location', true);
        $this->registration = get_post_meta($post->ID, 'registration', true);
    }

    /**
     * @param $id
     *
     * @return Event
     */
    public static function getByID($id)
    {
        return new Event(get_post($id));
    }
    #endregion

    #region getID()
    /**
     * @return int
     */
    public function getID()
    {
        return $this->post->ID;
    }
    #endregion

    #region getTitle()
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->post->post_title;
    }
    #endregion

    #region getStart($format)
    /**
     * @param null|string $format
     *
     * @return null|string
     */
    public function getStart($format = null)
    {
        if (!$this->start) {
            return null;
        }
        if ($this->start->format('H:i') != '00:00') {
            $format = $format ?: 'Y-m-d H:i';
        } else {
            $format = $format ?: 'Y-m-d';
        }
        return $this->start->format($format);
    }
    #endregion

    #region getEnd($format)
    /**
     * @param null|string $format
     *
     * @return null|string
     */
    public function getEnd($format = null)
    {
        if (!$this->end) {
            return null;
        }
        if ($this->start->format('H:i') != '00:00') {
            $format = $format ?: 'Y-m-d H:i';
        } else {
            $format = $format ?: 'Y-m-d';
        }
        return $this->end->format($format);
    }
    #endregion

    #region getLocation()
    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
    #endregion

    #region getGoogleCalendarURL()
    /**
     * @return string URL to create Google Calendar Event.
     */
    public function getGoogleCalendarURL()
    {
        $URL = 'https://www.google.com/calendar/render?action=TEMPLATE';
        $URL .= '&text=' . get_the_title($this->post->ID);
        $URL .= '&dates=' . $this->start->format('Ymd\\THi00');
        if ($this->end != false) {
            $URL .= '/' . $this->end->format('Ymd\\THi00');
        } else {
            $URL .= '/' . $this->start->format('Ymd\\THi00');
        }
        if (!empty($this->location)) {
            $URL .= '&location=' . $this->location;
        }
        return $URL;
    }
    #endregion

    #region getLiveCalendarURL()
    /**
     * @return string URL to create Live (Hotmail) Event.
     */
    public function getLiveCalendarURL()
    {
        /** @noinspection SpellCheckingInspection */
        $URL = 'http://calendar.live.com/calendar/calendar.aspx?rru=addevent';
        /** @noinspection SpellCheckingInspection */
        $URL .= '&dtstart=' . $this->start->format('Ymd\\THi00');
        if ($this->end != false) {
            /** @noinspection SpellCheckingInspection */
            $URL .= '$dtend=' . $this->end->format('Ymd\\THi00');
        }
        $URL .= '&summary=' . get_the_title($this->post->ID);
        if (!empty($this->location)) {
            $URL .= '&location=' . $this->location;
        }
        return $URL;
    }
    #endregion

    #region isValid()
    /**
     * @return bool true if the Event is valid (all mandatory fields are filled).
     */
    public function isValid()
    {
        if ($this->start == false) {
            return false;
        }
        return true;
    }
    #endregion

    #region isPublished()
    /**
     * @return bool true if the event is published
     */
    public function isPublished()
    {
        return $this->post->post_status == 'publish';
    }
    #endregion

    #region isRegistrationEnabled()
    /**
     * @return bool
     */
    public function isRegistrationEnabled()
    {
        return $this->registration != Registration::MODE_DISABLED;
    }
    #endregion

    #region isRegistrationMembersOnly()
    /**
     * @return bool
     */
    public function isRegistrationMembersOnly()
    {
        return $this->registration == Registration::MODE_MEMBERS_ONLY;
    }
    #endregion

    #region isRegistrationPossible()
    /**
     * This function returns if it is currently possible for someone to register.
     *
     * @return bool
     */
    public function isRegistrationPossible()
    {
        switch ($this->registration) {
            case Registration::MODE_EVERYONE:
            case Registration::MODE_MEMBERS_ONLY:
                return $this->start > new DateTime();
                break;
            case Registration::MODE_DISABLED:
            default:
                return false;
                break;
        }
    }
    #endregion

    #region canRegister()
    /**
     * This method returns if you can currently register (or unregister).
     *
     * @return bool true if you currently can register or unregister.
     */
    public function canRegister()
    {
        switch ($this->registration) {
            case Registration::MODE_EVERYONE:
                return $this->start > new DateTime();
                break;
            case Registration::MODE_MEMBERS_ONLY:
                return is_user_logged_in() && $this->start > new DateTime();
                break;
            case Registration::MODE_DISABLED:
            default:
                return false;
                break;
        }
    }
    #endregion

    #region isRegistered($user)
    /**
     * @param int|WP_User|null $user use null to test with the current user.
     *
     * @return bool
     */
    public function isRegistered($user = null)
    {
        if ($user == null && !is_user_logged_in()) {
            return false;
        }
        $userID = null;
        if (is_int($user)) {
            $userID = $user;
        } elseif ($user instanceof WP_User) {
            $userID = $user->ID;
        } else {
            $userID = get_current_user_id();
        }
        $this->updateRegistrations();
        if (count($this->registrations) > 0) {
            foreach ($this->registrations as $registration) {
                if ($registration->user != null && $registration->user->ID == $userID) {
                    return true;
                }
            }
        }
        return false;
    }
    #endregion

    #region getRegistrations()
    /**
     * @param bool $update set to false if you don't require a new update from the database.
     *
     * @return array of registrations
     */
    public function getRegistrations($update = true)
    {
        if ($update) {
            $this->updateRegistrations();
        }
        return $this->registrations;
    }

    #endregion

    public function updateRegistrations()
    {
        global $wpdb;
        $eventID   = $this->getID();
        $tableName = SSV_Events::TABLE_REGISTRATION;
        if (is_user_logged_in() && User::getCurrent()->isBoard()) {
            $eventRegistrations = $wpdb->get_results("SELECT ID FROM $tableName WHERE eventID = $eventID");
        } else {
            $eventRegistrations = $wpdb->get_results("SELECT ID FROM $tableName WHERE eventID = $eventID AND registration_status != 'denied'");
        }
        $this->registrations = array();
        foreach ($eventRegistrations as $eventRegistration) {
            $this->registrations[] = Registration::getByID($eventRegistration->ID);
        }
    }

    /**
     * @param bool $includeBase
     *
     * @return array
     */
    public function getRegistrationFieldNames($includeBase = true)
    {
        if ($includeBase) {
            $fieldNames = array('first_name', 'last_name', 'email');
        } else {
            $fieldNames = array();
        }
        $fieldIDs = get_post_meta($this->post->ID, Field::ID_TAG, true);
        $fieldIDs = is_array($fieldIDs) ? $fieldIDs : array();
        foreach ($fieldIDs as $id) {
            $field = get_post_meta($this->post->ID, Field::PREFIX . $id, true);
            $field = Field::fromJSON($field);
            if ($field instanceof InputField) {
                /** @var InputField $field */
                $fieldNames[] = $field->name;
            }
        }
        return $fieldNames;
    }

    public function showRegistrationForm()
    {
        ?>
        <h1>Register</h1>
        <?php
        /** @var InputField $actionField */
        $actionField = Field::fromJSON(
            json_encode(
                array(
                    'id'            => '-1',
                    'title'         => '',
                    'field_type'    => 'input',
                    'input_type'    => 'hidden',
                    'name'          => 'action',
                    'default_value' => 'register',
                    'class'         => '',
                    'style'         => '',
                )
            )
        );
        $fields      = Field::fromMeta();
        $values      = User::getCurrent();
        if (!is_user_logged_in()) {
            $fields = array_merge(Registration::getDefaultFields(), $fields);
        }
        $fields = array_merge(array($actionField), $fields);
        foreach ($fields as $field) {
            if ($field instanceof InputField) {
                $field->setValue($values);
            }
        }
        echo SSV_General::getCustomFieldsHTML($fields, SSV_Events::ADMIN_REFERER_REGISTRATION, 'Register');
    }

    public function showRegistrations($update = true)
    {
        if ($update) {
            $this->updateRegistrations();
        }
        if (!get_theme_support('materialize')) {
            ?>
            <h3>Registrations</h3>
            <ul>
                <?php foreach ($this->registrations as $event_registration) : ?>
                    <?php /* @var Registration $event_registration */ ?>
                    <li><?= $event_registration->getMeta('first_name') . ' ' . $event_registration->getMeta('last_name') ?></li>
                <?php endforeach; ?>
            </ul>
            <?php
        } else {
            ?>
            <h3>Registrations</h3>
            <ul class="collection with-header collapsible popout" data-collapsible="expandable">
                <?php foreach ($this->registrations as $event_registration) : ?>
                    <?php /* @var Registration $event_registration */ ?>
                    <li>
                        <div class="collapsible-header collection-item avatar">
                            <img src="<?= get_avatar_url($event_registration->getMeta('email')); ?>" alt='' class="circle">
                            <span class="title"><?= $event_registration->getMeta('first_name') . ' ' . $event_registration->getMeta('last_name') ?></span>
                            <p><?= $event_registration->status ?></p>
                        </div>
                        <div class="collapsible-body row" style="padding: 5px 10px;">
                            <table class="striped">
                                <?php foreach ($this->getRegistrationFieldNames() as $name): ?>
                                    <?php $value = $event_registration->getMeta($name); ?>
                                    <?php $value = empty($value) ? '' : $value; ?>
                                    <tr>
                                        <th><?= $name ?></th>
                                        <td><?= $value ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                            <?php if ($event_registration->status == Registration::STATUS_PENDING
                                      && is_user_logged_in()
                                      && User::getCurrent()->isBoard()
                                      && !is_archive()
                            ): ?>
                                <div class="card-action">
                                    <a href="<?= get_permalink() ?>?approve=<?= $event_registration->registrationID ?>">Approve</a>
                                    <a href="<?= get_permalink() ?>?deny=<?= $event_registration->registrationID ?>">Deny</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }
    }
}
