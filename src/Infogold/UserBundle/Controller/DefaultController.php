<?php

namespace Infogold\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        
        return $this->render('InfogoldUserBundle:Default:index.html.twig');
        
    }
}
