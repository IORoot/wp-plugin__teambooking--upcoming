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

## Frontend/Form.php

## Shortcodes/Upcoming.php



## Skipping the Reservation pages.

There is also an action in the main script `shortcodes/upcoming_extended/Upcoming_extended.php` that will skip the reservation page of the booking process because we don't use that.

## Extra - Injection actions

There are three actions built into the teambooking 'slots' part that can be hooked into:

- add_action( 'tb_calendar_inject_header', 'run_my_functionname_to_inject_a_header', 10, 1);
- add_action( 'tb_slot_inside_info_start', 'run_my_functionname_to_inject_info_at_the_start', 10, 1);
- add_action( 'tb_slot_inside_info_end', 'run_my_functionname_to_inject_info_at_the_end', 10, 1);

