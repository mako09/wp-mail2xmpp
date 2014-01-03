=== wp_mail to XMPP ===
Contributors: mako09
Tags: e-mail, e-mails, email, emails, jabber, mail, mails, notification, notifications, send, wp-mail, wp_mail, XMPP
Requires at least: 3.6
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send almost all notifications via XMPP instead of email

== Description ==
This plugin sends XMPP notifications to the users who are registered and set their own Jabber ID.  Emails to them are able to be suppressed.

This plugin provides a function to be added 'wp_mail' hook in `wp_mail()` located in `wp-includes/pluggable.php`.

This plugin requires [XMPP Enabled](http://wordpress.org/plugins/xmpp-enabled/) plugin.

= Filter Hook =
The function `xmpp_sender()` in this plugin has two filter hooks.

Using 'abort_xmpp_sender' hook allow abort all processing.  For example, you want to send email independent of user or JID when the subject has a particular keyword.  You may add a function which checks the subject and returns `true` if keyword is found.

Using 'email_to_jid' hook allow set/unset JID correspond to email address.  Default function of this hook, `email2jid()` returns JID when the user is registered to the site and his/her JID is set.  If you want to send email but XMPP to a particular user, add a function which returns `false`.

== Installation ==
0. [XMPP Enabled](http://wordpress.org/extend/plugins/xmpp-enabled/) plugin is required.  Install it and configure.
1. Upload the `wp-mail2xmpp` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin using the `Plugins` menu in WordPress.
3. Go to XMPP Enabled -> wp_mail to XMPP, adjust option.
4. Set each user's JID to Jabber ID field in his/har profile page.

== Changelog ==
= 0.4 =
* Created hooks.

= 0.3 =
* Public release
* Changed plugin name.

= 0.2 =
* Initial release.
