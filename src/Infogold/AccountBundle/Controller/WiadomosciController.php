<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GrafikController
 *
 * @author Magda
 */
namespace Infogold\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class WiadomosciController extends Controller{
    //put your code here
    public function indexAction() {
        $je = "jestes w wiadomosciach";
        
        return $this->render('InfogoldAccountBundle:Wiadomosci:wiadomosci.html.twig', array(
                    'ok' => $je,
                 ));
        
    }
}
