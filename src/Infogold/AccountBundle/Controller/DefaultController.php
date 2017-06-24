<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {

        return $this->render('InfogoldAccountBundle:Default:index.html.twig');
    }

    protected function adminContacts() {
        $user = $this->get('my.main.admin')->getMainAdmin();

        $adminid = $user->getId();
        $em = $this->getDoctrine()->getManager();
        $req = $em->getRepository('InfogoldKlienciBundle:Kontakty');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->leftJoin('p.PrzypisanyDo', 'c')
                ->leftJoin('c.firma', 'u')
                ->where('u.Nrklienta=' . $adminid)
                ->andwhere('p.PrzypisanyDo IS NULL')
                ->andwhere('p.status=1')
                ->orderBy('p.czasKontaktu', 'ASC')
                ->getQuery();
        //  $ent = $query->getResult(); 

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->get('page', 1)/* page number */, 25/* limit per page */);
    }

    protected function loggedin() {

        $user = $this->get('my.main.admin')->getMainAdmin();

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('InfogoldKonsultantBundle:CzasPracy');
        $q = $repository->createQueryBuilder('p')
                ->where('p.wylogowanie is null')
                ->leftJoin('p.KonsultantaCzasy', 'c')
                ->andwhere('c.id= :konsultantid')
                ->setParameter('konsultantid', $IdUser)
                ->getQuery();

        $konsultant = $q->getOneOrNullResult();
    }

}
