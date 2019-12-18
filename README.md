# wp-plugin__teambooking--upcoming

- [wp-plugin__teambooking--upcoming](#wp-pluginteambooking--upcoming)
  - [Description](#description)
  - [How it works](#how-it-works)
  - [FormElements/Textfield.php](#formelementstextfieldphp)
  - [Frontend/Form.php](#frontendformphp)
  - [Shortcodes/Upcoming.php](#shortcodesupcomingphp)
  - [Skipping the Reservation pages.](#skipping-the-reservation-pages)
  - [Extra - Injection actions](#extra---injection-actions)

## Description

This is a custom replacement of the 'upcoming' shortcode for the TeamBooking plugin that allows me to customise the look and feel of the output.

## How it works

By running the `./create_tb_links.sh` script, you'll be renaming the original files with a `.bak` extension and then creating symbolic links in the `/wp-content/plugins/team-booking/` folder to point to the new custom versions in the `wp-content/plugins/wp-plugin__teambooking--upcoming/` directory.

The files that are replaced are listed in the `/overrides/src/TeamBooking` directory because this is where they are held in the `/wp-content/plugins/team-booking/` directory.

-   FormElements/Textfield.php
-   Frontend/Form.php
-   Shortcodes/Upcoming.php

  
## FormElements/Textfield.php

The changes are as follows:
1. Line 66 - The Input now has a 'placeholder' attribute for a hint on what to enter.
2. Line 69 - The description field has a new specific class to target it better and remove it without interferring with other description fields.

## Frontend/Form.php

This script is used for the input form to take details from the customer. This has been HEAVILY CHANGED.

The changes are as follows:
1. Remove reservation details header. (232-235)
2. Add a CUSTOM Wrapper DIV with the service ID in it. So we can target and style depending on the service.
3. Split into LEFT / RIGHT side panels. Left = Details, Right = Booking Form.
4. Create class methods for each part of the form.
   1. `output_back_arrow()`
   2. `output_day_date()`
   3. `output_start_finish()`
   4. `output_today_alert()`
   5. `output_image()`
   6. `output_location()`
   7. `form_description()`
   8. `output_commandotemple()`
   9. `output_price()`
   10. `output_title()`
5. Remove the 'grouping' of fields.
6. Move the coupon eentry to BELOW the pricee box.
7. If class is FREE, skip the JQuery script.


## Shortcodes/Upcoming.php



## Skipping the Reservation pages.

There is also an action in the main script `shortcodes/upcoming_extended/Upcoming_extended.php` that will skip the reservation page of the booking process because we don't use that.

## Extra - Injection actions

There are three actions built into the teambooking 'slots' part that can be hooked into:

- add_action( 'tb_calendar_inject_header', 'run_my_functionname_to_inject_a_header', 10, 1);
- add_action( 'tb_slot_inside_info_start', 'run_my_functionname_to_inject_info_at_the_start', 10, 1);
- add_action( 'tb_slot_inside_info_end', 'run_my_functionname_to_inject_info_at_the_end', 10, 1);

