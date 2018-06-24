<?php

namespace Infogold\StronaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {  
        return $this->render('InfogoldStronaBundle:Default:index.html.twig');
    }
}
