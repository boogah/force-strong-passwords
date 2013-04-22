=== Force Strong Passwords ===
Contributors: gyrus, simonwheatley
Donate link: http://www.babyloniantimes.co.uk/index.php?page=donate
Tags: passwords, security, users, profile
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 1.2

Forces users to enter something strong when updating their passwords.

== Description ==
The WordPress user profile includes a JavaScript-powered indicator as a guide to the strength of a password being entered. However, there is nothing to stop users entering weak passwords.

Often, users changing their password to something very weak is the most vulnerable aspect of a WordPress installation. This plugin duplicates the WordPress JavaScript password strength check in PHP, and forces users with executive powers to use a strong password.

Strong passwords are enforced for all users who have any of specified array of capabilities. The default list is: `publish_posts`, `upload_files`, `edit_published_posts` (see [Roles and Capabilities](http://codex.wordpress.org/Roles_and_Capabilities)). If the user whose password is being edited holds any of these capabilities, the strong password enforcement will be triggered. To customize this list, use the `slt_fsp_caps_check` filter (see below).

Development code hosted at [GitHub](https://github.com/gyrus/Force-Strong-Passwords).

=== Filters ===

`slt_fsp_caps_check`
Modifies the array of capabilities that, if any one is held by the user whose password is being edited, the strong password enforcement will be triggered.

To make sure users who can update the core require strong passwords:

	add_filter( 'slt_fsp_caps_check', 'my_caps_check' );
	function my_caps_check( $caps ) {
		$caps[] = 'update_core';
		return $caps;
	}

To trigger the strong password enforcement for all users:

	add_filter( 'slt_fsp_caps_check', function() { return array(); } );

`slt_fsp_error_message`
Modifies the default error message.

== Installation ==
1. Upload the `force-strong-passwords` directory into the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==
= 1.2 =
* Added `slt_fsp_error_message` filter to customize error message
* Deprecated `SLT_FSP_CAPS_CHECK` constant; added `slt_fsp_caps_check` filter

= 1.1 =
* Used new `validate_password_reset` 3.5 hook to implement checking on reset password form (thanks simonwheatley!)
* PHPDoc for callable functions
* Improved function naming
* Added control over capabilities that trigger strong password enforcement via `SLT_FSP_CAPS_CHECK` constant

= 1.0 =
* First version