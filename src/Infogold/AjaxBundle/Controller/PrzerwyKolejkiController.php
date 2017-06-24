<?php


namespace Infogold\AjaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PrzerwyKolejkiController extends Controller {

    public function limitAction() {
       
                $loggedUser = $this->get('security.context')->getToken()->getUser();
                $limit = $loggedUser->getLimit();
                


                     return new Response( $limit);
            }
        
    
}