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
                ->select('MAX(p.id)')            
                ->leftJoin('p.KonsultantaCzasy', 'c')
                ->andwhere('c.id= :konsultantid')              
                ->setParameter('konsultantid',$IdUser)
                ->getQuery();

        $konsultant = $q->getSingleScalarResult(); //pobierz najwieksze id z ostatnim zalogowaniem konsultanta
 
/*
                $em2 = $this->getDoctrine()->getManager();
                $repository2 = $em2->getRepository('InfogoldKonsultantBundle:CzasPracy');
                $q2 = $repository2->createQueryBuilder('p')
                        ->where('p.wylogowanie is null')
                        ->leftJoin('p.KonsultantaCzasy', 'c')
                        ->andwhere('c.id= :konsultantid')
                        ->setParameter('konsultantid', $IdUser)
                        ->getQuery();

                $konsultanttonull = $q2->getResult();
                if ($konsultanttonull) {
                    foreach ($konsultanttonull as $nule) {

                        $LoginTimeWithoutLogout = $nule->getZalogowanie();
                        $nule->setWylogowanie($LoginTimeWithoutLogout);
                        $em2->flush();
                    }
                }
                */
                if ($konsultant) {
                     $timeend = $repository->find($konsultant);
                    $timeend->setWylogowanie($czas);
                    $em->flush();

                    $error = 1;
                    return new Response($error);
                } else {
                    return new Response($error);
                }
            }
            return new Response($error);
        }
        return new Response($error);
    }

}
