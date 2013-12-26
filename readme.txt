=== wp_mail to XMPP ===
Contributors: Mako N
Tags: e-mail, e-mails, notification, email, emails, jabber, mail, mails, notifications, send, wp_mail, wp-mail, XMPP
Donate link: http://example.com/
Requires at least: 3.6
Tested up to: 3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
This plugin sends XMPP notifications to the users who were registered and set their own Jabber ID.  By default, emails to them are suppressed.

This plugin requires [XMPP Enabled](http://wordpress.org/plugins/xmpp-enabled/) plugin.

このプラグインは、サイトに登録し、かつ Jabber ID を設定しているユーザーに対して、XMPP で通知を送ります。デフォルトでは、XMPP 通知を行うユーザーにはメール通知を行いません。

このプラグインは、wp-include/pluggable.php にある関数 wp_mail() の、最初の行にあるフック wp_mail で呼び出される関数を提供します。この中で、メールアドレスから JID を同定できるユーザーを抽出して XMPP で通知します。そして JID を見つけることのできなかったメールアドレスを返すことにより、wp_mail()は、それらの残されたユーザーに対してのみメール通知を行います。

このプラグインには、[XMPP Enabled](http://wordpress.org/plugins/xmpp-enabled/) プラグインが必要です。

== Installation ==
0. [XMPP Enabled](http://wordpress.org/extend/plugins/xmpp-enabled/) plugin is required.  Install it and configure.
1. Upload the `wp-mail2xmpp` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin using the `Plugins` menu in WordPress.
3. Go to XMPP Enabled > wp_mail to XMPP, adjust option.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==
= 0.3 =
* Changed plugin name.

= 0.2 =
* Initial release.

== Upgrade Notice ==
= 0.2 =
* Initial release.
