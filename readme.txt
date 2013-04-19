=== Force Strong Passwords ===
Contributors: gyrus, simonwheatley
Donate link: http://www.babyloniantimes.co.uk/index.php?page=donate
Tags: passwords, security, users, profile
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 1.0

Forces users to enter something strong when updating their passwords.

== Description ==
The WordPress user profile includes a JavaScript-powered indicator as a guide to the strength of a password being entered. However, there is nothing to stop users entering weak passwords.

Often, users changing their password to something very weak is the most vulnerable aspect of a WordPress installation. This plugin duplicates the WordPress JavaScript password strength check in PHP, and forces users with executive powers to use a strong password.

As of version 1.1, strong passwords are enforced for all users who have any of the capabilities defined in the `SLT_FSP_CAPS_CHECK` constant. If this constant isn't defined, it defaults to checking for any of the following capabilities: `publish_posts`, `upload_files`, `edit_published_posts` (see [Roles and Capabilities]:http://codex.wordpress.org/Roles_and_Capabilities). If the constant is defined to anything that evaluates to `false`, all users are forced to use a strong password.

So, to extend strong password enforcement to users who can edit posts (even if they're not published), add the following line to `wp-config.php`:

 `define( 'SLT_FSP_CAPS_CHECK', 'publish_posts,upload_files,edit_published_posts,edit_posts' );`

 To force all users to use a strong password:

 `define( 'SLT_FSP_CAPS_CHECK', false );`

Development code hosted at [GitHub](https://github.com/gyrus/Force-Strong-Passwords).

== Installation ==
1. Upload the `force-strong-passwords` directory into the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==
= 1.1 =
* Used new `validate_password_reset` 3.5 hook to implement checking on reset password form (thanks simonwheatley!)
* PHPDoc for callable functions
* Improved function naming
* Added control over capabilities that trigger strong password enforcement via `SLT_FSP_CAPS_CHECK` constant

= 1.0 =
* First version