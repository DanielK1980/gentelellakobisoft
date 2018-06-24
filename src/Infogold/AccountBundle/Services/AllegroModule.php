<?php

namespace Infogold\AccountBundle\Services;

use Infogold\AccountBundle\Services\MainAdmin;
use Symfony\Component\HttpFoundation\Session\Session;

class AllegroModule {

    protected $user;

    public function __construct(MainAdmin $user) {
        $this->user = $user;
        define('LINK', "http://webapi.allegro.pl.allegrosandbox.pl/service.php?wsdl"); //nowy wsdl,
        define('COUNTRY', 1);
    }
    
    public function getModuleAllegro($name, $arr) {                 
        $user = $this->user->getMainAdmin();      
            try {               
                $client = new \SoapClient(LINK);
                $argumenty = array(         
                     'webapiKey' => $user->getAllegro()->getAllegroKeyWebApi(), 'countryId' => COUNTRY);
                
                 foreach ($arr as $key => $addargument){
                     
                     $argumenty[$key] = $addargument;
                 }               
                $items = $client->{"$name"}($argumenty); 
                
            } catch (\SoapFault $error){
                $items = $this->get('session')->getFlashBag()->add('error', "ModuleAllegro: $error->faultstring");               
            }       
        return $items;
    }

   

}
