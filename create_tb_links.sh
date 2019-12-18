#!/bin/bash

# Create links and backups.

# TextField.php
./create_symbolic_link.sh \
    ../team-booking/src/TeamBooking/FormElements/TextField.php \
    ../../../../team-booking-extended/shortcodes/upcoming_extended/overrides/src/TeamBooking/FormElements/TextField.php

# Form.php
./create_symbolic_link.sh \
    ../team-booking/src/TeamBooking/Frontend/Form.php \
    ../../../../team-booking-extended/shortcodes/upcoming_extended/overrides/src/TeamBooking/Frontend/Form.php

# Upcoming.php
./create_symbolic_link.sh \
    ../team-booking/src/TeamBooking/Shortcodes/Upcoming.php \
    ../../../../team-booking-extended/shortcodes/upcoming_extended/overrides/src/TeamBooking/Shortcodes/Upcoming.php