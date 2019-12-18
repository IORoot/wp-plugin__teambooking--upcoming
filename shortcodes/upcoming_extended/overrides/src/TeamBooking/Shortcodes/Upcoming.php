<?php
namespace TeamBooking\Shortcodes;

use TeamBooking\Cart;
use TeamBooking\Database\Services,
    TeamBooking\Toolkit,
    TeamBooking\Slot,
    TeamBooking\Frontend\Schedule,
    TeamBooking\Functions,
    TeamBooking\Frontend\Components,
    TeamBooking\Actions,
    TeamBooking\Database,
    TeamBooking\RenderParameters;

/**
 *  ANDYP Upcoming - EXTENDED Version.
 * 
 * NOTE - THIS IS A SYMBOLIC LINK TO THE TEAM-BOOKING-EXTENDED PLUGIN.
 * 
 * Upcoming.php -> ../../../../andyp_teambooking_upcoming/shortcodes/upcoming_extended/overrides/src/TeamBooking/Shortcodes/Upcoming.php
 * 
 * 
 * This version of the 'Upcoming' shortcode is extended to include new DOM structures
 * More customising and SVGs.
 *
 * @since  1.0.0
 * @author AndyPearson
 */


class Upcoming
{
    /**
     * TeamBooking Upcoming Shortcode
     *
     * @param $atts
     *
     * @return mixed
     * @throws \Exception
     */
    public static function render($atts)
    {
        extract(shortcode_atts(array(
            'service'        => NULL,
            'coworker'       => NULL,
            'read_only'      => FALSE,
            'logged_only'    => FALSE,
            'shown'          => 4,
            'limit'          => 0,
            'more'           => FALSE,
            'slot_style'     => Functions\getSettings()->getSlotStyle(),
            'notimezone'     => FALSE,
            'hide_same_days' => TRUE,
            'descriptions'   => FALSE,
        ), $atts, 'tb-upcoming'));

        if (!defined('TBK_CALENDAR_SHORTCODE_FOUND')) {
            define('TBK_CALENDAR_SHORTCODE_FOUND', TRUE);
        }

        if (!wp_script_is('tb-frontend-script', 'registered')) {
            Functions\registerFrontendResources();
        }

        Functions\enqueueFrontendResources();

        // Read-only mode is identified by length of instance id
        $unique_id = !$read_only ? Toolkit\randomNumber(8) : Toolkit\randomNumber(6);

        if (!$logged_only || ($logged_only && is_user_logged_in())) {
            if (NULL !== $service && !empty($service)) {
                $services = array_map('trim', explode(',', $service));
                foreach ($services as $key => $booking) {
                    try {
                        // Remove inactive services
                        if (!Services::get($booking)->isActive() || Services::get($booking)->getClass() === 'unscheduled') {
                            unset($services[ $key ]);
                        }
                    } catch (\Exception $exc) {
                        unset($services[ $key ]);
                        continue;
                    }
                }
                if (empty($services)) {
                    return esc_html__('WARNING: service ID(s) not found. Please check the shortcode syntax and ensure at least one of the specified services is active.', 'team-booking');
                }
            } else {
                // Service(s) not specified, picking all of them
                $services = Functions\getSettings()->getServiceIdList();
                // Remove inactive services
                foreach ($services as $key => $booking) {
                    if (!Services::get($booking)->isActive() || Services::get($booking)->getClass() === 'unscheduled') {
                        unset($services[ $key ]);
                    }
                }
                if (count($services) <= 0) {
                    // Service(s) not specified, but no service available
                    return esc_html__('No active services', 'team-booking');
                }
            }
            $coworkers = NULL !== $coworker ? array_map('trim', explode(',', $coworker)) : array();
            $parameters = new \TeamBooking\RenderParameters();
            $parameters->setServiceIds($services);
            $parameters->setRequestedServiceIds($services);
            $parameters->setCoworkerIds($coworkers);
            $parameters->setRequestedCoworkerIds($coworkers);
            $parameters->setInstance($unique_id);
            $parameters->setTimezone(Toolkit\getTimezone(Functions\parse_timezone_aliases(Cart::getPreference('timezone'))));
            $parameters->setIsAjaxCall(FALSE);
            $parameters->setNoTimezone($notimezone);
            $parameters->setAltSlotStyle($slot_style);
            $parameters->setSlotsShown($shown);
            $parameters->setSlotsLimit($limit);
            $parameters->setShowMore($more);
            $parameters->setHideSameDaysLittleCal(filter_var($hide_same_days, FILTER_VALIDATE_BOOLEAN));
            $parameters->setShowServiceDescriptions($descriptions);
            Functions\parse_query_params($parameters);
            ob_start();
            ?>
            <div class="ui calendar_main_container tbk-upcoming" id="tbk-container-<?= $parameters->getInstance() ?>"
                 aria-live="polite"
                 data-postid="<?= get_the_ID() ?>">
                <?= static::getView($parameters, $read_only) ?>
            </div>
            <script>
                if (typeof tbkLoadInstance === "function") {
                    tbkLoadInstance(jQuery('#tbk-container-<?= $parameters->getInstance() ?>'));
                }
            </script>
            <?php
            return ob_get_clean();
        }
    }




    /* ANDYP - getView is used to output the DOM structure of each slot.
     *
     */
    public static function getView(\TeamBooking\RenderParameters $parameters, $read_only = FALSE)
    {
        $calendar = new \TeamBooking\Calendar();
        $slots = $calendar->getSlots($parameters->getServiceIds(), $parameters->getCoworkerIds(), NULL, NULL, FALSE, $parameters->getTimezone());
        $slots = $slots->getAllSlotsRawSortedByTime();
        if (count($slots) < 1) {
            ob_start();
            echo '<p>' . esc_html__('There are no upcoming events', 'team-booking') . '</p>';

            return ob_get_clean();
        }
        /** @var $slots Slot[] */
        $all_slots_num = count($slots);
        $slots = array_slice($slots, 0, $parameters->getSlotsShown());
        $picked_slots_num = count($slots);
        $timezone_identifier = NULL === Cart::getPreference('timezone') ? $parameters->getTimezone()->getName() : Cart::getPreference('timezone');
        ob_start();

        // ANDYP - CUSTOM - call new method below to generate Schema.
        do_action( 'tb_calendar_inject_header');
        
        // If this is NOT an AJAX call, then...
        if (!$parameters->getIsAjaxCall()) {
            ?>
            <div class="tbk-main-calendar-settings tbk-noselection">
                <?php
                echo \TeamBooking\Frontend\Calendar::getCalendarStyle();
                if (Functions\getSettings()->allowCart()) {
                    echo Components\Cart::getCartButton($parameters->getIsWidget());
                }
                if (!$parameters->getNoTimezone()) { ?>
                    <div>
                        <?php if (!$parameters->getNoTimezone() && in_array(TRUE, Functions\getSettings()->getContinentsAllowed(), TRUE)) { ?>
                            <div class="tbk-setting-button tbk-timezones" tabindex="0" style="margin: 0"
                                 title="<?= esc_html__('Timezone', 'team-booking') ?>"
                                 aria-label="<?= esc_html__('Timezone', 'team-booking') ?>">
                                <i class="world tb-icon"></i>
                                <?= Functions\timezone_list($timezone_identifier, $parameters->getIsWidget()) ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>




        <?php if (!$parameters->getIsAjaxCall()) { ?>
        <div
        class="<?= ($parameters->getIsWidget() === TRUE) ? 'tb-widget' : '' ?> tb-frontend-calendar tbk-slide-container"
        data-params="<?= $parameters->encode() ?>" data-instance='<?= $parameters->getInstance() ?>'>
        <?= Components\Dimmer::getMarkup() ?>
        <div class="tbk-slide-canvas tbk-slide-0">
        <div class="tbk-slide">

    <?php } ?>
        <ul>
                <?php
                $prev_day = '';
                foreach ($slots as $slot) {
                    try {
                        $service = Services::get($slot->getServiceId());
                    } catch (\Exception $e) {
                        continue;
                    }

                    $slot->setTimezone($parameters->getTimezone()->getName());

                    /* Set all Classes and Attributes
                    * For main section of each slot.
                    */
                    $classes = 'box-front tbk-upcoming-slot tbk-alt-' . $parameters->getAltSlotStyle();
                    $classes .= ' slot-' . $slot->getServiceId();
                    if (Functions\getSettings()->allowCart() && Cart::isSlotIn($slot)) {
                        $classes .= ' tbk-in-cart';
                    }
                    $location = $slot->getLocation();
                    $attributes_to_add = ' data-address="' . $location . '" ';

                    if ($service->getSettingsFor('bookable') === 'logged_only' && !is_user_logged_in() && !$slot->isSoldout()) {
                        $attributes_to_add .= 'class="' . $classes . ' '
                            . 'tb-book-advice' . '" '
                            . 'data-event="' . $slot->getEventId() . '" ';
                    } else {


                        if (!$read_only && !$slot->isSoldout() && !Functions\getSettings()->allowCart()) {
                            $classes .= ' tb-book';
                        } elseif ($read_only || $slot->isSoldout()) {
                            $classes .= ' tbk-read-only';
                        }


                        $attributes_to_add .= 'class="' . $classes . '"';
                        if (!$read_only) {
                            $attributes_to_add .= 'data-slot="' . Toolkit\objEncode($slot, TRUE, $slot->getUniqueId()) . '" ';
                            $attributes_to_add .= 'data-slot-id="' . Toolkit\objEncode($slot->getUniqueId()) . '" ';
                            // Map logic
                            $style = '';
                            if (!empty($location) && !Functions\getSettings()->getMapStyleUseDefault()) {
                                $style = htmlentities(json_encode(Functions\getSettings()->getMapStyle()));
                            }
                            $attributes_to_add .= 'data-mapstyle="' . $style . '" ';
                        }
                        if ($service->getSettingsFor('show_coworker')) {
                            $attributes_to_add .= 'data-coworker="' . $slot->getCoworkerId() . '" ';
                        }
                        if ($slot->isSoldout()) {
                            $attributes_to_add .= ' tabindex="0"';
                        }
                    }


                    /*  Each List Item
                    *
                    */
                    echo '<li>';

                            echo '<div class="tbk-slot-wrapper">';

                                    /*
                                    * ANDYP - CUSTOM - Pass the serviceId through the ACTION.
                                    */ 
                                    do_action( 'tb_slot_inside_wrapper_start');
                                    
                                    /* Main details of each slot.
                                    *
                                    */
                                    echo '<div ' . $attributes_to_add . '>';

                                            //  ┌────────────────────────────────────────────────┐
                                            //  │                     TODAY!                     │
                                            //  └────────────────────────────────────────────────┘
                                            if ($slot->getDateFormatted('Ymd') == date('Ymd')  ){ 
                                                echo '<div class="tbk-alert tbk-alert__'.$slot->getServiceId().'"><div class="tbk-alert__icon"></div>Today! </div>';
                                            }

                                            echo '<div class="tbk-slot-info">';

                                                /*
                                                * ANDYP - CUSTOM - Pass the serviceId through the ACTION.
                                                */ 
                                                do_action( 'tb_slot_inside_info_start', $slot->getServiceId());

                                                
                                                //  ┌────────────────────────────────────────────────┐
                                                //  │                  Date / Time                   │
                                                //  └────────────────────────────────────────────────┘
                                                echo '<p class="tbk-date">';

                                                    // Weekday - coloured
                                                    echo '<span class="tbk-weekday">'
                                                        . date_i18n(
                                                            (($parameters->getIsWidget() === TRUE) ? 'D' : 'l jS'),
                                                            strtotime($slot->getDateFormatted('Y')
                                                                . '-' . $slot->getDateFormatted('m')
                                                                . '-' . $slot->getDateFormatted('d')
                                                            ))
                                                        . ' </span>';
                                                    // Time
                                                    echo '<span class="tbk-times"><i class="wait tb-icon"></i>' . $slot->getTimesString() . '</span>';

                                                echo '</p>';

                                                //  ┌────────────────────────────────────────────────┐
                                                //  │                  Service Name                  │
                                                //  └────────────────────────────────────────────────┘
                                                echo '<h4 class="tbk-service-name tbk-service-name__'.$slot->getServiceId().'">' . $slot->getServiceName(TRUE) . '</h4>';

                                                
                                                //  ┌────────────────────────────────────────────────┐
                                                //  │                    Location                    │
                                                //  └────────────────────────────────────────────────┘
                                                // Location of Service (regex first part)
                                                if ($slot->getLocation() != NULL) {
                                                    // remove everything after first comma. 
                                                    $location = preg_replace('/^([^,]*).*$/', '$1', $slot->getLocation());
                                                    // output
                                                    echo '<p class="tbk-location"><i class="marker tb-icon"></i>' 
                                                        . esc_html(ucwords($location)) 
                                                        . '</p>';
                                                }

                                                // Custom WordPress hook - runs action with the current $slot - do_action('tbk_schedule_slot_render', $slot);
                                                \TeamBooking\Actions\schedule_slot_render($slot);


                                                //  ┌────────────────────────────────────────────────┐
                                                //  │                  Descriptions                  │
                                                //  └────────────────────────────────────────────────┘
                                                if ($parameters->getShowServiceDescriptions()) {
                                                    echo '<div class="tbk-service-desc">' . $service->getDescription(TRUE) . '</div>';
                                                }

                                            echo '</div>';

                                            /*
                                            * ANDYP - CUSTOM - Pass the serviceId through the ACTION.
                                            */ 
                                            echo '<div class="tbk-class-colour-background tbk-class-colour-background__'.$slot->getServiceId().'">';
                                                echo '<div class="tbk-class-image-background tbk-class-image-background__'.$slot->getServiceId().'" >';
                                                    echo '<div class="tbk-class-image tbk-class-image__'.$slot->getServiceId().'"></div>';
                                                    echo '<div class="tbk-arrow__icon"></div>';
                                                echo '</div>';
                                            echo '</div>';
                                            do_action( 'tb_slot_inside_info_end', $slot->getServiceId());
                                           
                                            

                                    echo '</div>';

                                    /** 
                                    * ANDYP - CUSTOM - Pass the serviceId through the ACTION.
                                    */ 
                                    do_action( 'tb_slot_inside_wrapper_end', $slot->getServiceId());

                            echo '</div>';
                    echo '</li>';

                    $prev_day = $slot->getDateFormatted('Ymd');
                }
                ?>
        </ul>
        <?php 
        
        /* Show more button.
         *
        */
        if ($all_slots_num !== $picked_slots_num
        && $parameters->getShowMore()
        && ($picked_slots_num < $parameters->getSlotsLimit() || $parameters->getSlotsLimit() === 0)
    ) { ?>
        <div class="tbk-button tbk-show-more" data-increment="6"
             data-limit="<?= $parameters->getSlotsLimit() ?>">
            <?= esc_html__('Show more', 'team-booking') ?>
        </div>
    <?php } ?> 
        <?php if (!$parameters->getIsAjaxCall()) { ?>
        </div>
        </div>
        </div>

        <?php
        // ANDYP - CUSTOM - call new method below to generate Schema.
        do_action( 'tb_calendar_inject_footer', $slots);
        // ANDYP - CUSTOM - Need to use the class services to render.
        self::getUpcomingSchema($slots);
        ?>


    <?php } ?>
        <?php
        return ob_get_clean();
    }

    /** Schema Creator for each slot
     *
     * ANDYP - CUSTOM - Render Schema
     * Creates a JSON-LD Structured data for each rendered slot in the calendar.
     * Schema template from https://developers.google.com/search/docs/data-types/event
     *
     *
     * @return
     */
    public static function getUpcomingSchema($slots_obj)
    {

        foreach($slots_obj as $slot) {  // This contains all of the slots for the month.

            $service = Services::get($slot->getServiceId());

            $script_tag = '<script type="application/ld+json">';

            $script_tag .= '{';

            $script_tag .= '"@context": "http://schema.org",'; 
            $script_tag .= '"name": "Parkour Class - ' . $slot->getServiceName() . '",';
            $script_tag .= '"@type": "Event",';
            $script_tag .= '"startDate": "' . $slot->getDateFormatted(DATE_ATOM, 'start') . '",';
            $script_tag .= '"endDate": "' . $slot->getDateFormatted(DATE_ATOM, 'end') . '",';
            $script_tag .= '"description": "' . strip_tags($slot->getServiceInfo()) . '",';
            $script_tag .= '"url": "https://londonparkour.com/classes",';
            $script_tag .= '"image": [
                                        "https://londonparkour.com/wp-content/uploads/2018/05/Eliza_LDNPK_Classes_1920x1920.jpg",
                                        "https://londonparkour.com/wp-content/uploads/2018/05/Eliza_LDNPK_Classes_1920x1440.jpg",
                                        "https://londonparkour.com/wp-content/uploads/2018/05/Eliza_LDNPK_Classes_1920x1080.jpg"
                                        ],';

            $script_tag .= '"offers": {';
            $script_tag .= '"@type": "Offer",';
            $script_tag .= '"availability": "http://schema.org/InStock",';
            $script_tag .= '"price": "'. $service->getPrice() .'",';
            $script_tag .= '"priceCurrency": "GBP",';
            $script_tag .= '"url": "https://londonparkour.com/classes",';
            $script_tag .= '"validFrom": "'. date("Y-m-d", time() - 86400) .'"';
            $script_tag .= '},';

            $script_tag .= '"performer": {';
            $script_tag .= '"@type": "Person",';
            $script_tag .= '"name": "LondonParkour"';
            $script_tag .= '},';

            $script_tag .= '"location": {';
            $script_tag .= '"@type": "Place",';
            $script_tag .= '"name": "Class Location",';
            $script_tag .= '"address": {';
            $script_tag .= '"@type": "PostalAddress",';

            if ($slot->getLocation()) {
                $address = explode(",", $slot->getLocation());
            } else {
                $address = array('','','');
            }

            $script_tag .= '"streetAddress": "' . $address[0] . '",';
            $script_tag .= '"addressRegion": "London",';
            $script_tag .= '"addressCountry": "UK"';
            $script_tag .= '}';
            $script_tag .= '}';

            $script_tag .= '}';


            $script_tag .= "</script>";

            echo $script_tag;
        }

        return;
    }
}