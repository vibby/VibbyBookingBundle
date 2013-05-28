<?php

namespace Vibby\Bundle\BookingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection;

class RequestInjector{

    protected $container;

    public function __construct($container){

         $this->container = $container;
   }

    public function getRequest(){

        return $this->container->get('request');
    }
}