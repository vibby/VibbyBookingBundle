<?php

namespace Vibby\Bundle\BookingBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Vibby\Bundle\BookingBundle\Entity\Event;
use \DateTime;
use \DateInterval;

class LoadUserData implements FixtureInterface {

  public function load(ObjectManager $manager) {

    $date = new DateTime();

    for ($i=0; $i < 3; $i++) {
      $date->add(New \DateInterval('P3D'));

      $event = new Event();
      $event->setFirstname('prénom'.$i);
      $event->setLastname('nom'.$i);
      $event->setEmail('test'.$i.'@vibby.fr');
      $event->setPhone('0123'.printf('000000',$i));
      $event->setDateFrom($date);
      $event->validate();
      $event->setDateTo($date->add(New \DateInterval('P3D')));

      $manager->persist($event);
    }
    
    $manager->flush();
    
  }
  
  
}  