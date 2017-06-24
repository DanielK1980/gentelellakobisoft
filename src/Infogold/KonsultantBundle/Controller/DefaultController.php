<?php

namespace Infogold\KonsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller  {
    
    public function indexAction(Request $request) {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $konsultantid = $user->getId();
        $em = $this->getDoctrine()->getManager();
              $req = $em->getRepository('InfogoldKlienciBundle:Kontakty');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->leftJoin('p.PrzypisanyDo', 'c')
                ->where('c=' . $konsultantid)
                ->andwhere('p.status=1')
                ->orderBy('p.czasKontaktu', 'ASC')
                ->getQuery();
      //  $ent = $query->getResult(); 
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query, $request->query->get('page', 1)/* page number */, 25/* limit per page */);
        
        
        $forms = array();
        foreach ($pagination as $path) {
            //edycja kontaktu
            $editForm = $this->createForm("infogold_kliencibundle_kontaktytype", $path);
            $form = $editForm->createView();
            $forms[] = $form;
         
        }
              return $this->render('InfogoldKonsultantBundle:Konsultant:glowna.html.twig', array(         
                        'pagination' => $pagination,
                        'edit_form' => $forms
                     ));
    }
   
}