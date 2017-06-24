<?php

namespace Infogold\AccountBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ProduktyControllerTest extends WebTestCase
{
    
    public function testIndexAction()
    {

     $client = static::createClient();

     $crawler = $client->request('GET', '/');
     
   $form = $crawler->selectButton('_submit')->form(array(
       '_username'  => "DanielK1980",
       '_password'  => "fiodor11",
       ));     
   $client->submit($form);

 //  $this->assertTrue($this->client->getResponse()->isRedirect());

  // $crawler = $this->client->followRedirect();
   
   $crawler = $client->request('GET', '/user/produkty');
   
   
   $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /produkty/");
  
    $this->assertRegExp('/Lista/', $client->getResponse()->getContent());

// $this->assertTrue($this->client->getResponse()->isRedirect());
        /*
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'infogold_accountbundle_produktytype[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Edit')->form(array(
            'infogold_accountbundle_produktytype[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
         * 
         */
    }

    
}

