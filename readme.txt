=== Force Strong Passwords ===
Contributors: gyrus
Donate link: http://www.babyloniantimes.co.uk/index.php?page=donate
Tags: passwords, security, users, profile
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 1.0

Forces users with executive capabilities to use something strong when updating their passwords.

== Description ==
The WordPress user profile includes a JavaScript-powered indicator as a guide to the strength of a password being entered. However, there is nothing to stop users entering weak passwords.

Often, users granted Administrator or Editor roles, who change their password to something very weak, is the most vulnerable aspect of a WordPress installation. This plugin duplicates the WordPress JavaScript password strength check in PHP, and forces users with executive powers to use a strong password.

The check is enforced unless the user being edited can't `publish_posts`, can't `upload_files`, and can't `edit_published_posts` - see [Roles and Capabilities]:http://codex.wordpress.org/Roles_and_Capabilities

The rationale here is:

1. The capabilities assigned to roles may have been altered by a role management plugin, so check on roles is unsafe.
1. There's no need to check for all executive capabilities; it's assumed that if a user can't do any of the above three things, they won't be able to `update_core` or `manage_options`.

Future version may include settings for greater flexibility in enforcing the check.

Development code hosted at [GitHub](https://github.com/gyrus/Force-Strong-Passwords).

== Installation ==
1. Upload the `force-strong-passwords` directory into the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==
= 1.0 =
* First version