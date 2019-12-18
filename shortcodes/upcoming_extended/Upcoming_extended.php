<?php
/* PARTS 
 *
 * 1. Slots         - This is the first slide with all of the calendar entries on it.
 * 2. Details       - This contains all of each particular slots details.
 * 3. Reservation   - Confirm reversation details
 * 4. Payment       - Card details
 * 5. Confirmation  - Confirmation page with success / fail message.
 * 
 * */


// Register CSS File (footer - stop render blocking)
function register_css_upcoming_extended_css_new() {
    wp_register_style( 'tb-upcoming-extended-css-new', plugins_url( './css/style.css', __FILE__ ) );
    wp_enqueue_style('tb-upcoming-extended-css-new');
}
add_action( 'get_footer', 'register_css_upcoming_extended_css_new' );


/* 1. Slots page
*
* team-booking/src/TeamBooking/Shortcodes/Upcoming.php
* links to
* team-booking-extended/shortcodes/upcoming_extended/src/TeamBooking/Shortcodes/Upcoming.php
*
*/

/* 2. Details page. 
*
* 2.1. Customise the form itself.
* team-booking/src/TeamBooking/Frontend/Form.php
* links to
* team-booking-extended/shortcodes/upcoming_extended/src/TeamBooking/Frontend/Form.php
*
* 
* 2.2. Customise the textfield component.
* 
* team-booking/src/TeamBooking/FormElements/TextField.php
* links to
* team-booking-extended/shortcodes/upcoming_extended/src/TeamBooking/FormElements/TextField.php
*/


 /* 3. SKIP Reservation pages
 *
 * http://stroheimdesign.com/docs/2_dev.html#actions
 * 2.2.1 - third example.
 */
add_action('tbk_reservation_before_processing', 'example_three_func');

function example_three_func($reservation_data){
    // that's quite simple...
    $reservation_data->skip_review = TRUE;
}

/* 4. Payment / stripe page
*
* team-booking/src/TeamBooking/PaymentGateways/Stripe/Settings.php
* links to
* team-booking-extended/shortcodes/upcoming_extended/overrides/src/TeamBooking/PaymentGateways/Stripe/Settings.php
*
*/

// 5. Thank you Page.