<?php
/**
 * Plugin Name: XMPP Notification
 * Plugin URI: https://pasero.net/~mako/
 * Description: Almost all notifications are sent via XMPP. This plugin requires "XMPP Enabled" plugin.
 * Version: 0.2
 * Author: Mako N
 * Author URI: https://pasero.net/~mako/
 * Text Domain: xmpp-notification
 * Domain Path: /languages
 * License: GPLv2 or later
 */
new Xmpp_Notification();

class Xmpp_Notification {

	public function __construct () {

		if( $this->is_active( 'xmpp-enabled/xmpp-enabled.php' ) ) {
			/* notify via XMPP */
			add_filter( 'wp_mail', array( &$this, 'xmpp_notification' ) );
		}

		/* Internationalize the text strings used */
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

        /* Add jabber ID field */
        add_filter('user_contactmethods', array( &$this, 'add_xmpp_field' ) );

		/* Settings */
		add_action( 'admin_menu', array( &$this, 'menu' ), 0 );
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
		load_plugin_textdomain( 'xmpp-notification', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/** 
	 * Create Jabber ID field
	 * 
	 * @param user_contact 
	 * @since  0.2
	 * @return array
	 */
	public function add_xmpp_field( $user_contact ){
		$user_contact['jabber'] = __('Jabber ID', 'xmpp-notification'); 
		return $user_contact;
	}

	/** 
	 * Notify via XMPP
	 * 
	 * @param parameters 
	 * @since  0.1
	 * @return object
	 */
	public function xmpp_notification ( $parameters ) {
		extract( $parameters ); // 'to', 'subject', 'message', 'headers', 'attachments'
		$emails = array();

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

			// xmpp_send
            $jid = get_user_by( 'email', $email )->jabber;
			if ( $jid ) {
				xmpp_send( $jid, $message, $subject );
			} else { // user not exist or jabber field is null
				$emails[] = $recipient;
			}
		}

		if ( get_option( 'xmpp_email_also' ) ) {
			return $parameters; // which received as it is.
		} else {
			$to = $emails; // not sent via XMPP
			return compact( 'to', 'subject', 'message', 'headers', 'attachments' );
		}
	}

/* ----- settings section -------- */

	public function menu() {
		if( $this->is_active( 'xmpp-enabled/xmpp-enabled.php' ) ) {
			$parent = 'xmpp-enabled';
		} else {
			$parent = 'plugins.php';
		}
		add_submenu_page( $parent, _x('XMPP Notification', 'menu', 'xmpp-notification'), _x('XMPP Notification', 'menu', 'xmpp-notification'), 'administrator', 'xmpp-notification', array ( &$this, 'settings_page' ) );
		add_action( 'admin_init', array ( &$this, 'settings' ) );
	}

	public function settings () {
		register_setting( 'xmpp-notification', 'xmpp_email_also' );
	}

	public function settings_page () {
?>
    <div class="wrap">
    <h2><?php _e('XMPP Notification Settings', 'xmpp-notification') ?></h2>
<?php if( $this->is_active( 'xmpp-enabled/xmpp-enabled.php' ) ) { ?>
    <form method="post" action="options.php">
        <?php settings_fields('xmpp-notification'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <?php _e('This plugin sends XMPP notifications to the users who were registered at this site and set their own Jabber ID.  By default, email notifications are not sent to them.  To send not only XMPP but also email, check this option.', 'xmpp-notification') ?></br>
                    <input type="checkbox" value="1" name="xmpp_email_also" id="xmpp_email_also"
                        <?php if(get_option('xmpp_email_also', true)) echo 'checked="checked"' ?>
                    /> <label for="xmpp_email_also"><?php _e('Send email also', 'xmpp-notification') ?></label>
                </th>
            </tr>
        </table>
    <?php submit_button(); ?>
    </form>
<?php } else { ?>
    <p><?php printf( __('This plugin requires "<a href="%1$s">XMPP Enabled</a>" plugin.', 'xmpp-notification'), 'http://wordpress.org/plugins/xmpp-enabled/') ?></br>
       <?php _e('Set up "XMPP Enabled" before you visit here.', 'xmpp-notification') ?></p>
<?php } ?>
    </div>
<?php
	}
}
?>
