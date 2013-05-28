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
use Knp\Bundle\PaginatorBundle;
use \DateTime;
use \DateInterval;
use \DatePeriod;

/**
 * Event controller.
 *
 * @Route("")
 */
class AdminController extends Controller
{    
    /**
     * Lists all Event entities.
     *
     * @Route("/list", name="event_list")
     * @Template()
     */
    public function listAction($dateFrom = null, $dateTo = null)
    {
      
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('VibbyBookingBundle:Event')->listAllQuery();
        
        $limit = 16;
        $page = $this->get('request')->query->get('page', 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );

        $dateFrom = $dateTo = new DateTime();
        foreach ($pagination->getItems() as $date) {
            if (!$dateTo) $dateTo = $date->getDateTo();
            if (!$dateFrom) $dateFrom = $date->getDateFrom();
            if ($date->getDateFrom() < $dateFrom) $dateFrom = clone $date->getDateFrom();
            if ($date->getDateTo()   > $dateTo  ) $dateTo   = clone $date->getDateTo();
        }            
        $period = new DatePeriod(
            $dateFrom,
            new DateInterval('P1D'),
            $dateTo->add(new DateInterval('P1D'))
        );

        return compact('pagination','period');
    }

    /**
     * Lists all Event entities.
     *
     * @Route("/", name="calendar")
     * @Template("VibbyBookingBundle:Admin:calendar.html.twig")
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('sentDatesEventIds', array());
      
        $entity = new Event();
        $form   = $this->createForm(new EventType(), $entity);

        $period = new DateInterval("P9M");
        $dates= $this->get('booking')->getBookedDates($period);

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
     * Finds and displays a Event entity.
     *
     * @Route("/{id}/show", name="event_show")
     * @Template("VibbyBookingBundle:Admin:show.html.twig")
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
     * @Template("VibbyBookingBundle:Admin:new.html.twig")
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
     * @Template("VibbyBookingBundle:Admin:edit.html.twig")
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

        return $this->redirect($this->generateUrl('event_list'));
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

        $otherBooking = $em->getRepository('VibbyBookingBundle:Event')->findByDates(
          $entity->getDateFrom()->add(new \DateInterval('P1D')),
          $entity->getDateTo()->sub(new \DateInterval('P1D')),
          array($entity->getId())
        );
        if (count($otherBooking)) {
          $this->get('session')->setFlash('error', 'Impossible de valider. Il y a peut-être une autre réservation à ces dates.');
        } else {
          $entity->validate();
          $em->flush();
        }

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
