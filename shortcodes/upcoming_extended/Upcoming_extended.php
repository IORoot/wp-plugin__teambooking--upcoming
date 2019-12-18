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



 /* SKIP Reservation pages
 *
 * http://stroheimdesign.com/docs/2_dev.html#actions
 * 2.2.1 - third example.
 */
add_action('tbk_reservation_before_processing', 'example_three_func');

function example_three_func($reservation_data){
    $reservation_data->skip_review = TRUE;
}