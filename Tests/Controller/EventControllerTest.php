<?php

namespace Vibby\Bundle\BookingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
  
    private $count;
    private $client;
    
    private function fillRequestForm($data = array(), $entryString = "")
    {
        
        $phone = "012345678";
        if (!$entryString) {
          $entryString = sprintf("Test%1$04d_",$this->count);
          $phone = sprintf("012345%1$04d",$this->count);
          $this->count++;
        }
        
        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/');
        if (!$this->count) $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        $baseData = array(
            'booking[date_from]'  =>  '2012-01-03',
            'booking[date_to]'   =>   '2012-01-14',
            'booking[firstname]'  =>  $entryString.'FN',
            'booking[lastname]'   =>  $entryString.'LN',
            'booking[email]'   =>     $entryString.'@test.com',
            'booking[phone]'   =>     $phone,
        );
        $data = array_merge($baseData, $data);
        
        $form = $crawler->selectButton('Envoyer la demande')->form($data);
        $this->client->submit($form);
        
        return $entryString;
    }
    
    public function testFillRequestForm()
    { 
        // Create a new client to browse the application
        $this->client = static::createClient();
        $this->count = 1;

        // Creation
        $entryString = $this->fillRequestForm();
        $crawler = $this->client->request('GET', '/list');
        $this->assertEquals(1, $crawler->filter('table:contains("'.$entryString.'")')->count());
        
        // Empty «date from»from
        $data['booking[date_from]']  = '';
        $entryString = $this->fillRequestForm($data);
        $crawler = $this->client->request('GET', '/list');
        $this->assertEquals(0, $crawler->filter('table:contains("'.$entryString.'")')->count());
        

    }
    
}