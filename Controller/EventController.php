<?php

namespace Vibby\Bundle\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Vibby\Bundle\BookingBundle\Entity\Event;
use Vibby\Bundle\BookingBundle\Form\EventType;
use Vibby\Bundle\BookingBundle\Form\ContactType;
use Vibby\Bundle\BookingBundle\Form\EventAdminType;
use Knp\Bundle\PaginatorBundle;
use \DateTime;
use \DateInterval;
use \DatePeriod;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event controller.
 *
 * @Route("")
 */
class EventController extends Controller
{
    /**
     * Lists all Event entities.
     *
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('sentDatesEventIds', array());
      
        $entity = new Event();
        $form   = $this->createForm(new EventType(), $entity);

        $contactForm = $this->createForm(new ContactType());

        $period = new DateInterval("P3M");
        $dates= $this->get('booking')->getBookedDates($period);

        $now = new DateTime;
        $data = array(
            'entity'      => $entity,
            'form'        => $form->createView(),
            'contactForm' => $contactForm->createView(),
            'bookedDates' => json_encode($dates),
            'askedDates'  => json_encode(
                      array(
                            (int)$now->format('Ym')
                      )
                    )
        );
        
        return $this->render('VibbyBookingBundle:Event:index.html.twig', $data);
    }
    

    /**
     * Creates a new Event entity.
     *
     * @Route("/quick_create", name="event_quick_create")
     * @Method("post")
     */
    public function quickCreateAction()
    {
        
        $entity  = new Event();
        $request = $this->getRequest();
        $form    = $this->createForm(new EventType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //Send a mail 
            $message = \Swift_Message::newInstance()
                ->setSubject('Reservation')
                ->setFrom('pierreblanche@beauvivre.fr')
                ->setTo('vincent.beauvivre@gmail.com')
                ->setBody(
                    $this->renderView(
                        'VibbyBookingBundle:Mails:booking.html.twig',
                        array('booking' => $entity)
                    )
                )
                ->setContentType('text/html')
            ;
            $this->get('mailer')->send($message);

            return $this->render('VibbyBookingBundle:Event:quickNewSuccess.html.twig');
        } 
          
        return $this->render('VibbyBookingBundle:Event:quickNewError.html.twig');

    }
    
    /**
     * Treate message form
     *
     * @Route("/message", name="message")
     * @ Method("post")
     */
    public function messageAction(Request $request)
    {

        $contactForm = $this->createForm(new ContactType());
        $contactForm->bind($request);
        if ($contactForm->isValid()) {

          //Send a mail 
          $passed = array( 
            'ip' => $request->getClientIp(),
            'name' => $contactForm->get('name')->getData(),
            'email' => $contactForm->get('email')->getData(),
            'message' => $contactForm->get('message')->getData(),
          );

          $message = \Swift_Message::newInstance()
              ->setSubject('Reservation')
              ->setFrom('pierreblanche@beauvivre.fr')
              ->setTo('vincent.beauvivre@gmail.com')
              ->setBody(
                  $this->renderView(
                      'VibbyBookingBundle:Mails:message.html.twig',
                      $passed                )
              )
              ->setContentType('text/html')
          ;
          $this->get('mailer')->send($message);

          $return = $this->renderView(
            'VibbyBookingBundle:Form:success.html.twig'
          );

        } else {

          $return = $this->renderView(
            'VibbyBookingBundle:Form:contact.html.twig',
            array('contactForm' => $contactForm->createView())
          );
        }
        
        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
        
    }    

    /**
     * Shows a calendar
     *
     * @Route("/list/{dateFrom}/{period}", defaults={"period" = 3}, name="limitedList", options={"expose"=true})
     */
    public function limitedListAction($dateFrom, $period = 3)
    {
        $period = new DateInterval("P".$period."M");

        $dates= $this->get('booking')->getBookedDates($period, $dateFrom);

        $response = new Response(json_encode($dates));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        
    }
    
}
