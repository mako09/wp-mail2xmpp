<?php
/**
 * Plugin Name: wp_mail to XMPP
 * Plugin URI: http://wordpress.org/plugins/wp-mail2xmpp/
 * Description: Almost all notifications are sent via XMPP. This plugin requires "XMPP Enabled" plugin.
 * Version: 0.4
 * Author: Mako N
 * Author URI: http://pasero.net/~mako/
 * Text Domain: wp-mail2xmpp
 * Domain Path: /languages
 * License: GPLv2 or later
 */
new Wp_mail2xmpp();

class Wp_mail2xmpp {

	public function __construct () {

		if( $this->is_active( 'xmpp-enabled/xmpp-enabled.php' ) ) {
			add_filter( 'wp_mail', array( &$this, 'xmpp_sender' ) );
		}

		/* Internationalize the text strings used */
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

		/* Add jabber ID field */
		add_filter( 'user_contactmethods', array( &$this, 'add_xmpp_field' ) );

		/* get JID from email address */
		add_filter( 'email_to_jid', array( &$this, 'email2jid' ), 10 );

		/* Settings */
		add_action( 'admin_menu', array( &$this, 'menu' ), 11 ); // after "XMPP Enabled"
	}

	/** 
	 * Checks if a plugin is activated. 
	 * 
	 * from https://gist.github.com/miya0001/7480577#file-gistfile1-php
	 *
	 * @param plugin 
	 * @since  0.2
	 * @return bool
	 */
	private function is_active( $plugin ) {
		if (function_exists('is_plugin_active')) {
			return is_plugin_active($plugin);
		} else {
			return in_array( $plugin, get_option('active_plugins') );
		}
	}

	/**
	 * Loads the translation files
	 *
	 * @since  0.2
	 * @return void
	 */
	public function i18n() {
		load_plugin_textdomain( 'wp-mail2xmpp', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/** 
	 * Create Jabber ID field
	 * 
	 * @param user_contact 
	 * @since  0.2
	 * @return array
	 */
	public function add_xmpp_field( $user_contact ){
		$user_contact['jabber'] = __('Jabber ID', 'wp-mail2xmpp'); 
		return $user_contact;
	}

	/** 
	 * Send massages via XMPP
	 * 
	 * Using 'abort_xmpp_sender' hook allow abort all processing.
	 * For example, you want to send email independent of user or
	 * JID when the subject has a particular keyword.  You may add
	 * a function which checks the subject and returns true if
	 * keyword is found.
	 *
	 * Using 'email_to_jid' hook allow set/unset JID correspond to
	 * email address.
	 * Default function of this hook, email2jid() returns JID when
	 * the user is registered to the site and his/her JID is set.
	 * If you want to send email but XMPP to a particular user, add
	 * a function which returns false.
	 *
	 * @param parameters 
	 * @since  0.1
	 * @uses apply_filters() Calls 'abort_xmpp_sender'
	 * @uses apply_filters() Calls 'email_to_jid'
	 * @return
	 */
	public function xmpp_sender ( $parameters ) {
		extract( $parameters ); // 'to', 'subject', 'message', 'headers', 'attachments'
		$emails = array();

		$abort = false;
		if ( apply_filters( 'abort_xmpp_sender', $abort, $patameters) ) {
			return $parameters;
		}

		// Set destination addresses
		if ( ! is_array( $to ) )
			$to = explode( ',', $to );

		foreach ( (array) $to as $recipient ) {
			// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
			if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) && count( $matches ) == 3 ) {
				$email = $matches[2];
			} else {
				$email = $recipient;
			}

			unset( $jid );
			$jid = apply_filters( 'email_to_jid', $email, $jid, $subject, $message, $headers, $attachments );

			if ( $jid ) {
				xmpp_send( $jid, $message, $subject ); // in "XMPP Enabled" plugin
			} else {
				$emails[] = $recipient; // not registered or don't have jabber ID
			}
		}

		if ( get_option( 'xmpp_email_also' ) ) {
			return $parameters;
		} else {
			$to = $emails;
			return compact( 'to', 'subject', 'message', 'headers', 'attachments' );
		}
	}

	/** 
	 * Search JID from email address
	 *
	 * return false if the user is not registered or his/her JID is not set
	 * 
	 * @param email 
	 * @since  0.4
	 * @return text
	 */
	public function email2jid( $email ) {
		return get_user_by( 'email', $email )->jabber;
	}

/* ----- settings section -------- */

	public function menu() {
		if( $this->is_active( 'xmpp-enabled/xmpp-enabled.php' ) ) {
			$parent = 'xmpp-enabled';
		} else {
			$parent = 'plugins.php';
		}
		add_submenu_page( $parent, _x('wp_mail to XMPP', 'menu', 'wp-mail2xmpp'), _x('wp_mail to XMPP', 'menu', 'wp-mail2xmpp'), 'administrator', 'wp-mail2xmpp', array ( &$this, 'settings_page' ) );
		add_action( 'admin_init', array ( &$this, 'settings' ) );
	}

	public function settings () {
		register_setting( 'wp-mail2xmpp', 'xmpp_email_also' );
	}

	public function settings_page () {
?>
    <div class="wrap">
    <h2><?php _e('wp_mail to XMPP Settings', 'wp-mail2xmpp') ?></h2>
	<?php if( $this->is_active( 'xmpp-enabled/xmpp-enabled.php' ) ) { ?>
    <form method="post" action="options.php">
        <?php settings_fields('wp-mail2xmpp'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <?php _e('This plugin sends XMPP notifications to the users who were registered at this site and set their own Jabber ID.  By default, email notifications are not sent to them.  To send not only XMPP but also email, check this option.', 'wp-mail2xmpp') ?></br>
                    <input type="checkbox" value="1" name="xmpp_email_also" id="xmpp_email_also"
                        <?php if(get_option('xmpp_email_also', true)) echo 'checked="checked"' ?>
                    /> <label for="xmpp_email_also"><?php _e('Send email also', 'wp-mail2xmpp') ?></label>
                </th>
            </tr>
        </table>
    <?php submit_button(); ?>
    </form>
<?php } else { ?>
    <p><?php printf( __('This plugin requires "<a href="%1$s">XMPP Enabled</a>" plugin.', 'wp-mail2xmpp'), 'http://wordpress.org/plugins/xmpp-enabled/') ?></br>
       <?php _e('Set up "XMPP Enabled" before you visit here.', 'wp-mail2xmpp') ?></p>
<?php } ?>
    </div>
<?php
	}
}
?>
