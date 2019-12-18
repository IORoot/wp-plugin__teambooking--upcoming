#!/bin/bash

# Remove links and move backups.

# TextField.php
./remove_symbolic_link.sh \
    ../team-booking/src/TeamBooking/FormElements/TextField.php 

# Form.php
./remove_symbolic_link.sh \
    ../team-booking/src/TeamBooking/Frontend/Form.php 

# Upcoming.php
./remove_symbolic_link.sh \
    ../team-booking/src/TeamBooking/Shortcodes/Upcoming.php 