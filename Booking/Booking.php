<?php

namespace Vibby\Bundle\BookingBundle\Booking;

use \DateTime;
use \DateInterval;

class Booking
{    

    protected $requestInjector;
    protected $doctrine;

    public function __construct($requestInjector, $doctrine){

        $this->requestInjector = $requestInjector;
        $this->doctrine = $doctrine;

    }

    /**
     * Get the dates at which the booked elements start and finish
     */
    public function getBookedDates(DateInterval $period, $dateFrom = false) {

        $session = $this->requestInjector->getRequest()->getSession();
        
        if (!$dateFrom) {
            $date1 = new DateTime();
        } else {
          try {
            $date1 = new DateTime($dateFrom);
          } catch (Exception $e) {
            throw new \Exception('Invalid date information');;
          }
        }
        $date2 = clone($date1);

        $sendDatesEventIds = $session->get('sentDatesEventIds');
        if (!$sendDatesEventIds) $sendDatesEventIds = array();

        $em = $this->doctrine->getManager();
        $events = $em
          ->getRepository('VibbyBookingBundle:Event')
          ->findByDates(
                  $date1,
                  $date2->add($period),
                  $sendDatesEventIds
               );
        $dates = $em
          ->getRepository('VibbyBookingBundle:Event')
          ->getBookedIntervals($events);

        $doneIds = array();
        foreach($events as $event) $doneIds[] = $event['id'];
        $session->set('sentDatesEventIds', array_merge($sendDatesEventIds,$doneIds));
        
        return ($dates);
      
    }
}