<?php

namespace Infogold\KlienciBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('InfogoldKlienciBundle:Default:index.html.twig', array('name' => $name));
    }
}
