<?php

namespace Vibby\Bundle\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Vibby\Bundle\BookingBundle\Entity\Event;
use Vibby\Bundle\BookingBundle\Form\EventType;
use Vibby\Bundle\BookingBundle\Form\EventAdminType;
use \DateTime;
use \DateInterval;

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
     * @Route("/", name="event")
     * @Template()
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('sentDatesEventIds', array());
      
        $entity = new Event();
        $form   = $this->createForm(new EventType(), $entity);

        $dates= $this->getBookedDates();
        
        $now = new DateTime;
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'bookedDates' => json_encode($dates),
            'askedDates'  => json_encode(
                      array(
                            (int)$now->format('Ym')
                      )
                    )
        );
      
      return array();
    }
    

    /**
     * Creates a new Event entity.
     *
     * @Route("/quick_create", name="event_quick_create")
     * @Method("post")
     * @Template("VibbyBookingBundle:Event:quickNewError.html.twig")
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

            return $this->render('VibbyBookingBundle:Event:quickNewSuccess.html.twig');
        } 
          
        return array();

    }

    
    /**
     * Shows a calendar
     *
     * @Route("/list/{dateFrom}", name="limitedList")
     * @Template()
     */
    public function limitedListAction($dateFrom)
    {

        $dates= $this->getBookedDates($dateFrom);
      
        $response = new Response(json_encode($dates));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
        
    }
    
    /**
     * Get the dates at which the booked elements start and finish
     */
    private function getBookedDates($dateFrom = false) {

        $session = $this->getRequest()->getSession();
        
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

        $em = $this->getDoctrine()->getManager();
        $events = $em
          ->getRepository('VibbyBookingBundle:Event')
          ->findByDates(
                  $date1,
                  $date2->add(new DateInterval("P3M")),
                  $sendDatesEventIds
               );
        $dates = $em
          ->getRepository('VibbyBookingBundle:Event')
          ->getBookedIntervals($events);

        $doneIds = array();
        foreach($events as $event) $doneIds[] = $event['id'];
//var_dump($event);
        $session->set('sentDatesEventIds', array_merge($sendDatesEventIds,$doneIds));
        
        return ($dates);
      
    }
    
    /**
     * Lists all Event entities.
     *
     * @Route("/list", name="event_list")
     * @Template()
     */
    public function listAction($dateFrom = null, $dateTo = null)
    {
      
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VibbyBookingBundle:Event')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}/show", name="event_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VibbyBookingBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Event entity.
     *
     * @Route("/new", name="event_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Event();
        $form   = $this->createForm(new EventAdminType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    
    /**
     * Creates a new Event entity.
     *
     * @Route("/create", name="event_create")
     * @Method("post")
     * @Template("VibbyBookingBundle:Event:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Event();
        $request = $this->getRequest();
        $form    = $this->createForm(new EventType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VibbyBookingBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createForm(new EventAdminType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Event entity.
     *
     * @Route("/{id}/update", name="event_update")
     * @Method("post")
     * @Template("VibbyBookingBundle:Event:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VibbyBookingBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm   = $this->createForm(new EventAdminType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}/delete", name="event_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('VibbyBookingBundle:Event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Event entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}/validate", name="event_validate")
     * @Method("get")
     */
    public function validateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('VibbyBookingBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $entity->validate(); 
        $em->flush(); 

        return $this->redirect($this->generateUrl('event_list'));
    }    
    
    /**
     * Deletes a Event entity.
     * 
    * @Route("/{id}/unvalidate", name="event_unvalidate")
     * @Method("get")
     */
    public function unvalidateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('VibbyBookingBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $entity->unvalidate(); 
        $em->flush(); 

        return $this->redirect($this->generateUrl('event_list'));
    }     
}
