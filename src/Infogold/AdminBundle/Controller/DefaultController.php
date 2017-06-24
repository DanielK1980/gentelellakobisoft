<?php

namespace Infogold\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('InfogoldAdminBundle:Default:index.html.twig', array('name' => $name));
    }
}
