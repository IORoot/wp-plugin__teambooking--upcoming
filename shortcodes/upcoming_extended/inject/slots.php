<?php

//  ┌───────────────────────────────────────────┐ 
//  │                                           │░
//  │           Insert into the slots           │░
//  │      see readme.md for all actions.       │░
//  │                                           │░
//  └───────────────────────────────────────────┘░
//   ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░


    /**
     * ANDYP - Test class passing
     */
    function tbk_test_info($serviceID = null) {
    
    }

    add_action( 'tb_calendar_inject_header', 'tbk_test_info', 10, 1);



    /**
     * ANDYP - Use the 'tb_slot_inside_info_start' action to inject into the info slot.
     */
    function tbk_insert_pre_info($slot_object) {
       
    }

    add_action( 'tb_slot_inside_info_start', 'tbk_insert_pre_info', 10, 1);


    /**
     * ANDYP - Use the 'tb_slot_inside_info_end' action to inject into the info slot.
     */
    function tbk_insert_post_info($serviceID = null) {

    }

    add_action( 'tb_slot_inside_info_end', 'tbk_insert_post_info', 10, 1);