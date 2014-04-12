=== Force Strong Passwords ===
Contributors: gyrus, simonwheatley, sparanoid, jpry
Donate link: http://www.babyloniantimes.co.uk/index.php?page=donate
Tags: passwords, security, users, profile
Requires at least: 3.5
Tested up to: 3.8.2
Stable tag: 1.3.3

Forces users to enter something strong when updating their passwords.

== Description ==
The WordPress user profile includes a JavaScript-powered indicator as a guide to the strength of a password being entered. However, there is nothing to stop users entering weak passwords. Often, users changing their password to something very weak is the most vulnerable aspect of a WordPress installation.

**IMPORTANT:** As of WordPress 3.7, the password strength meter is based on the [Dropbox "zxcvbn" script](https://tech.dropbox.com/2012/04/zxcvbn-realistic-password-strength-estimation/). This is a far better check, but extensive and quite a job to port to PHP, which is the way this plugin worked prior to 3.7. For 3.7 and above, this plugin simply passes the results of the client-side zxcvbn check for the server to decide if an error should be thrown. Beware that a tech-savvy user *could* disable this check in the browser.

Strong passwords are enforced for all users who have any of a specified array of capabilities. The default list is: `publish_posts`, `upload_files`, `edit_published_posts` (see [Roles and Capabilities](http://codex.wordpress.org/Roles_and_Capabilities)). If the user whose password is being edited holds any of these capabilities, the strong password enforcement will be triggered. To customize this list, use the `slt_fsp_caps_check` filter (see below).

Development code hosted at [GitHub](https://github.com/gyrus/Force-Strong-Passwords).

= Filters =

**`slt_fsp_caps_check` (should return an array)**
Modifies the array of capabilities that, if any one is held by the user whose password is being edited, the strong password enforcement will be triggered.

To make sure users who can update the core require strong passwords:

	add_filter( 'slt_fsp_caps_check', 'my_caps_check' );
	function my_caps_check( $caps ) {
		$caps[] = 'update_core';
		return $caps;
	}

To trigger the strong password enforcement for all users:

	add_filter( 'slt_fsp_caps_check', __return_empty_array() );

**`slt_fsp_error_message` (should return a string)**
Modifies the default error message.

**`slt_fsp_weak_roles` (should return an array)**
Modifies the array of roles that are considered "weak", and for which the strong password enforcement is skipped when creating a new user. In this situation, the user object has yet to be created, so there are no capabilities to go by, just the role that has been set on the New Users form. The default array includes: `subscriber` and `contributor`.

== Installation ==
1. Upload the `force-strong-passwords` directory into the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.3.3 =
* zxcvbn password hints.
* Now allows for non-Latin character set encoding when comparing zxcvbn meter result (thanks jpry!)

= 1.3.2 =
* Added Serbo-Croatian translation (thanks Borisa Djuraskovic!)

= 1.3.1 =
* Fixed so zxcvbn check respects localization (thanks lakrisgubben!)

= 1.3 =
* Switched to JS-aided enforcement of new zxcvbn check in WP 3.7+
* Added Japanese translation (thanks Fumito Mizuno!)

= 1.2.2 =
* Added Chinese Simplified Language support (thanks sparanoid!)

= 1.2.1 =
* Fixed bug that triggered enforcement on profile update even when no password is being set

= 1.2 =
* Added `slt_fsp_error_message` filter to customize error message
* Deprecated `SLT_FSP_CAPS_CHECK` constant; added `slt_fsp_caps_check` filter
* Added `slt_fsp_weak_roles` filter

= 1.1 =
* Used new `validate_password_reset` 3.5 hook to implement checking on reset password form (thanks simonwheatley!)
* PHPDoc for callable functions
* Improved function naming
* Added control over capabilities that trigger strong password enforcement via `SLT_FSP_CAPS_CHECK` constant

= 1.0 =
* First version