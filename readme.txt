=== XMPP Notification ===
Contributors: Mako N
Tags: notification, mail, email, e-mail, wp-mail, jabber, XMPP
Donate link: http://example.com/
Requires at least: 3.6
Tested up to: 3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin sends almost all notifications via XMPP. 

== Description ==
This plugin sends XMPP notifications to the users who were registered and set their own Jabber ID.  By default, email notifications to them are suppressed.

This plugin requires [XMPP Enabled](http://wordpress.org/plugins/xmpp-enabled/) plugin.

Link to [WordPress](http://wordpress.org/ \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"Your favorite software\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\") and one to [Markdown\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\'s Syntax Documentation][markdown syntax].


=== To Do ===
* とにかくメールをすべてXMPPに送るので、notification より一般化したほうがよい。例: wp-mail2xmpp, XMPP sender revisited

* ほとんどのメールはXMPPの通知として受け取っても問題ないが、コンタクトフォームの場合は、除外したほうが嬉しいだろう。
対策: 
Jetpack の場合
wp_mail の少し前に
$subject = apply_filters( 'contact_form_subject', $contact_form_subject );
があるのでこれを利用。たとえばここで $contact_form_subject = '[noxmpp]'.$contact_form_subject としておき、こちらでこれを取り除く。

ContactFrom7 の場合
wp_mail の少し前に
$components = compact( 'subject', 'sender', 'body', 'recipient', 'additional_headers', 'attachments' );
$components = apply_filters_ref_array( 'wpcf7_mail_components', array( $components, &$this ) );
があるのでこれを利用。たとえばここで additional_headers に 'X-Mailer: Contact Form 7 on WordPress\n'を追加しておき、こちらでこれを見て判断する。

== Installation ==
1. Upload \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"test-plugin.php\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\" to the \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"/wp-content/plugins/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\" directory.
1. Activate the plugin through the \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"Plugins\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\" menu in WordPress.

== Frequently Asked Questions ==
= A question that someone might have =
An answer to that question.

= What about foo bar? =
Answer to foo bar dilemma.

== Screenshots ==

== Changelog ==
= 0.2 =
* Initial release.

== Upgrade Notice ==
= 0.2 =
* Initial release.
