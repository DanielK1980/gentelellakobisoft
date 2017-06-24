<?php

namespace Infogold\KonsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CloseController extends Controller {

    public function indexAction(Request $request) {

        $error = 0;
        $User = $this->get('security.context')->getToken()->getUser();
        $IdUser = $User->getId();
        if ($request->getMethod() == 'POST') {

            $action = $request->get('action');

            if ($action == 'checklogin') {

                $czas = new \DateTime('now');
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('InfogoldKonsultantBundle:CzasPracy');
                $q = $repository->createQueryBuilder('p')
                        ->where('p.wylogowanie is null')
                        ->leftJoin('p.KonsultantaCzasy', 'c')
                        ->andwhere('c.id= :konsultantid')
                        ->setParameter('konsultantid', $IdUser)
                        ->getQuery();

                $konsultant = $q->getOneOrNullResult();

                if ($konsultant) {

                    $konsultant->setWylogowanie($czas);
                    $em->flush();

                    $error = 1;

                    return new Response($error);
                }
                else {
                    return new Response($error);
                }
                
            }
            return new Response($error);
        }
        return new Response($error);
    }

}
