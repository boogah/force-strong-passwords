<?php

/*
Plugin Name: Force Strong Passwords
Description: Forces users with executive capabilities to use something strong when updating their passwords.
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

// Hook onto profile update
add_action( 'user_profile_update_errors', 'slt_user_profile_update_errors', 0, 3 );

// Check user profile update and throw an error if the password isn't strong
// Note that because $errors is passed by reference, and doesn't *need* to be returned
function slt_user_profile_update_errors( $errors, $update, $user_data ) {
	$user_id = isset( $user_data->ID ) ? $user_data->ID : false;

	$password = isset( $_POST[ 'pass1' ] ) ? $_POST[ 'pass1' ]: false;
	
	if ( false === $password )
		return $errors;

	$role = isset( $_POST[ 'role' ] ) ? $_POST[ 'role' ]: false;

	return slt_strong_passwords( $errors, $password, $user_data, $role );
}

// Check a password for a user
function slt_strong_passwords( $errors, $password, $user_data, $role = false ) {
	if ( ! isset( $user_data->ID ) && $role ) {
		// No user yet, probably adding new user - omit check for "weaker" roles
		if ( in_array( $role, array( 'subscriber', 'contributor' ) ) )
			return $errors; // Returning early simplifies the IF statement below
	} else {
		// User specified - test on basic capabilities that can compromise a site
		// Doesn't check on higher capabilities - it's assumed the someone who can't publish_posts won't be able to update_core!
		$user = new WP_User( $user_data->ID );
		if (
				! user_can( $user, 'publish_posts' ) &&
				! user_can( $user, 'upload_files' ) &&
				! user_can( $user, 'edit_published_posts' )
			)
			return $errors;
	}
	
	if ( slt_password_strength( $password, $user_data->user_login ) == 4 )
		return $errors;

	if ( $errors->get_error_data("pass") )
		return $errors;

	// If we've got here then checks have failed, so add the error
	$errors->add( 'pass', __( '<strong>ERROR</strong>: Please make the password a strong one.' ) );
	return $errors;
}

add_action( 'validate_password_reset', 'slt_validate_password_reset', 10, 2 );

// Check the reset password
// Note that because $errors is an object, it is passed by reference so doesn't *need* to be returned
function slt_validate_password_reset( $errors, $user ) {
	$password = isset( $_POST[ 'pass1' ] ) ? $_POST[ 'pass1' ]: false;
	if ( false === $password )
		return $errors;

	return slt_strong_passwords( $errors, $password, $user );
}

// Check for password strength
// Copied from JS function in WP core: /wp-admin/js/password-strength-meter.js
function slt_password_strength( $i, $f ) {
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