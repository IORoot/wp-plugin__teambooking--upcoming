<?php
/*
Plugin Name: _ANDYP - Team Booking - Custom 'Upcoming' Shortcode
Plugin URI: https://londonparkour.com
Description: Custom TeamBooking Upcoming Shortcode - ANDYP - MAKE SURE YOU RUN THE create_symbolic_links.sh script!!!
Version: 1.0.0
Author: Andy Pearson
Author URI: https://londonparkour.com
*/

/* CHECK 1 - TeamBooking Dependency
 *
 * IMPORTANT!!!
 * 
 * Please make the following symbolic links to override standard files.
 * run the ./create_tb_links.sh script to create them.
 * 
 * Ensure that the TeamBooking Plugin is running and activated too.
*/


// CUSTOM 1 - New Upcoming Shortcode.
include "shortcodes/upcoming_extended/Upcoming_extended.php";