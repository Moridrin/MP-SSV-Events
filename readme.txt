=== SSV Events ===
Contributors: moridrin
Tags: ssv, mp-ssv, events, event management, moridrin, sportvereniging, sports club,
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: trunk
License: WTFPL
License URI: http://www.wtfpl.net/txt/copying/

SSV Events is a plugin that allows you to create events for your Students Sports Club the way you want to.

== Description ==
SSV Events is a plugin that allows you to create events for your Students Sports Club the way you want to. With this plugin you can:
* Make events
* Let users register
 * One Click registration
 * Cancel registration
* Etc.
This plugin is fully compatible with the SSV library which can add functionality like: MailChimp, Frontend Members, etc.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/ssv-events` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the SSV Options->MailChimp screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==
= How do I request a feature? =
The best way is to add an issue on GitHub (https://github.com/Moridrin/ssv-events/issues). But you can also send an email to J.Berkvens@Moridrin.com (the lead developer).
= How do I report a bug? =
The best way is to add an issue on GitHub (https://github.com/Moridrin/ssv-events/issues). But you can also send an email to J.Berkvens@Moridrin.com (the lead developer).

== Changelog ==

= 3.3.7 =
* Settings added
* Support for consulting theme added
* Added Profile Values to form fields
* Updated for PHP 7.2
* Error Handling improved
* Customizing added for base fields
* Added Database shell for catching errors

= 3.3.6 =
* Settings added
* Support for consulting theme added

= 3.3.5 =
* Events Archive support for 'consulting'.

= 3.3.4 =
* Shared Events Updated

= 3.3.3 =
* Events Archive mapped to 'events' (instead of 'ssv_event').
* Share meta box added if is_multisite() for sharing events with other sites.
  * Implemented in Materialized events archive view.
    * With Notification that it is an event from a different site and the link opens the event on the correct site in a new tab.
* Materialize frontend-templates updated (old templates removed).
  * This does not yet work with registrations or tickets.

= 3.3.2 =
* Form select added to Tickets

= 3.3.1 =
* Complete Re-do
* Working Components
  * Start and End DateTime
  * Tickets
    * Add
    * Inline Edit
      * Title
      * Price
      * From
      * Till
    * Delete
* General updated

= 3.3.0 =
* Opening registration details disabled for non logged in users
* Widget Design improved
* Updating name fields disabled (due to possible bugs)
* Even more Widget Design improvements (styled by Guy Dubois)
* Widget Design improved (styled by Guy Dubois)
* Ready for WordPress
* Working with other themes
  * No custom Events Archive for other themes
* Create List on Event Create
* Add Registrants to List

= 3.2.7 =
* Opening registration details disabled for non logged in users

= 3.2.6 =
* Widget Design improved

= 3.2.5 =
* Updating name fields disabled (due to possible bugs)

= 3.2.4 =
* Even more Widget Design improvements (styled by Guy Dubois)

= 3.2.3 =
* Widget Design improved (styled by Guy Dubois)

= 3.2.2 =
* Ready for WordPress
* Working with other themes
  * No custom Events Archive for other themes

= 3.2.1 =
* Create List on Event Create
* Add Registrants to List

= 3.2.0 =
* Namespaces added

= 3.1.0 =
* esc_html(), esc_attr() and other sanitation implemented
* isBoard() replaced with current_user_can()
  * This might still be buggy because it is difficult to specify what right the user needs.

= 3.0.0 =
* Rebuild from the ground up

= 2.1.0 =
* Support for Materialize
* Complete code refactor

= 2.0.2 =
* Cancellation for signed in users

= 1.2.1 =
* Registration form
* Registration button for signed in users

= 1.2.0 =
* Support for MUI
* Custom archive
* Custom post layout

= 1.1.2 =
* Mandatory Custom Fields

= 1.1.1 =
* Create Event
  * Location field added
  * Start Date field added
  * Start Time field added
  * End Date field added
  * End Time field added