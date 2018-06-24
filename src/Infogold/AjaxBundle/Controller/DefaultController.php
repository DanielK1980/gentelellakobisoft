<?php

namespace Infogold\AjaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    public function indexAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $login = $request->get('login');
            $action = $request->get('action');

            if ($action == 'checklogin') {

                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('InfogoldUserBundle:User');

                $user = $repository->findOneBy(array('username' => $login));

                if ($user) {
                    $error = 0;
                } else {
                    $error = 1;
                }
                return new Response($error);
            }
        }
    }

    public function indexprofileditAction(Request $request) {
        if ($request->getMethod() == 'POST') {
            $login = $request->get('login');
            $action = $request->get('action');
            $log = $request->get('log');

            if ($action == 'checklogin') {

                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('InfogoldUserBundle:User');

                $user = $repository->findOneBy(array('username' => $login));


                if (($user) && ($login !== $log)) {
                    $error = 0;
                } else {
                    $error = 1;
                }
                return new Response($error);
            }
        }
    }

}
