<?php

/*
Plugin Name: Force Strong Passwords
Description: Forces users to use something strong when updating their passwords.
Version: 1.2
Author: Steve Taylor
Author URI: http://sltaylor.co.uk
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	_e( "Hi there! I'm just a plugin, not much I can do when called directly." );
	exit;
}

// Initialize
if ( ! defined( 'SLT_FSP_CAPS_CHECK' ) ) {
	/**
	 * The default capabilities that will be checked for to trigger strong password enforcement
	 *
	 * @deprecated	Please use the slt_fsp_caps_check filter to customize the capabilities check for enforcement
	 * @since		1.1
	 */
	define( 'SLT_FSP_CAPS_CHECK', 'publish_posts,upload_files,edit_published_posts' );
}

// Hook onto profile update to check user profile update and throw an error if the password isn't strong
add_action( 'user_profile_update_errors', 'slt_fsp_validate_profile_update', 0, 3 );
function slt_fsp_validate_profile_update( $errors, $update, $user_data ) {
	return slt_fsp_validate_strong_password( $errors, $user_data );
}

// Hook onto password reset screen
add_action( 'validate_password_reset', 'slt_fsp_validate_strong_password', 10, 2 );

// Functionality used by both user profile and reset password validation
function slt_fsp_validate_strong_password( $errors, $user_data ) {
	$password = isset( $_POST[ 'pass1' ] ) ? $_POST[ 'pass1' ] : false;
	$role = isset( $_POST[ 'role' ] ) ? $_POST[ 'role' ] : false;
	$user_id = isset( $user_data->ID ) ? $user_data->ID : false;
	$username = isset( $_POST["user_login"] ) ? $_POST["user_login"] : $user_data->user_login;

	// No password set?
	if ( false === $password )
		return $errors;

	// Already got a password error?
	if ( $errors->get_error_data("pass") )
		return $errors;

	// Should a strong password be enforced for this user?
	$enforce = true;
	if ( $user_id ) {
		// User ID specified
		$enforce = slt_fsp_enforce_for_user( $user_id );
	} else {
		// No ID yet, adding new user - omit check for "weaker" roles
		if ( $role && in_array( $role, array( "subscriber", "contributor" ) ) )
			$enforce = false;
	}

	// If enforcing and the strength check fails, add error
	if ( $enforce && slt_fsp_password_strength( $password, $username ) != 4 )
		$errors->add( 'pass', apply_filters( 'slt_fsp_error_message', __( '<strong>ERROR</strong>: Please make the password a strong one.' ) ) );

	return $errors;
}


/**
 * Check whether the given WP user should be forced to have a strong password
 *
 * Tests on basic capabilities that can compromise a site. Doesn't check on higher capabilities.
 * It's assumed the someone who can't publish_posts won't be able to update_core!
 *
 * @since	1.1
 * @uses	SLT_FSP_CAPS_CHECK
 * @uses	apply_filters()
 * @uses	user_can()
 * @param	$user	int			A user ID
 * @return			boolean
 */
function slt_fsp_enforce_for_user( $user_id ) {
	$enforce = true;
	$check_caps = explode( ',', SLT_FSP_CAPS_CHECK );
	$check_caps = apply_filters( 'slt_fsp_caps_check', $check_caps );
	$check_caps = (array) $check_caps;
	if ( ! empty( $check_caps ) ) {
		$enforce = false; // Now we won't enforce unless the user has one of the caps specified
		foreach ( $check_caps as $cap ) {
			if ( user_can( $user_id, $cap ) ) {
				$enforce = true;
				break;
			}
		}
	}
	return $enforce;
}

/**
 * Check for password strength - based on JS function in WP core: /wp-admin/js/password-strength-meter.js
 *
 * @since	1.0
 * @param	$i	string	The password
 * @param	$f	string	The user's username
 * @return		integer	1 = very weak; 2 = weak; 3 = medium; 4 = strong
 */
function slt_fsp_password_strength( $i, $f ) {
	$h = 1; $e = 2; $b = 3; $a = 4; $d = 0; $g = null; $c = null;
	if ( strlen( $i ) < 4 )
		return $h;
	if ( strtolower( $i ) == strtolower( $f ) )
		return $e;
	if ( preg_match( "/[0-9]/", $i ) )
		$d += 10;
	if ( preg_match( "/[a-z]/", $i ) )
		$d += 26;
	if ( preg_match( "/[A-Z]/", $i ) )
		$d += 26;
	if ( preg_match( "/[^a-zA-Z0-9]/", $i ) )
		$d += 31;
	$g = log( pow( $d, strlen( $i ) ) );
	$c = $g / log( 2 );
	if ( $c < 40 )
		return $e;
	if ( $c < 56 )
		return $b;
	return $a;
}
