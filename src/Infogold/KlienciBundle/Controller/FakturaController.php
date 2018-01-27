<?php

namespace Infogold\KlienciBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Infogold\KlienciBundle\Entity\Faktura;

/**
 * Faktura controller.
 *
 */
class FakturaController extends Controller {

    /**
     * Lists all Faktura entities.
     *
     */
    public function indexoryginalAction(Request $request, $search = null) {
        
        if (!$search) {
            $loggedUser = $this->get('my.main.admin')->getMainAdmin();
            $userId = $loggedUser->getId();
            $em = $this->getDoctrine()->getManager();

            $req = $em->getRepository('InfogoldKlienciBundle:Faktura');

            $qb = $req->createQueryBuilder('p');
            $search = $qb
                    ->leftJoin('p.userfaktury', 'c')
                    ->where('c=' . $userId)
                    ->andwhere('p.rodzaj = :rodzaj')
                    ->add('orderBy', 'p.datafaktury DESC')
                    ->setParameter('rodzaj', 1)
                    ->setMaxResults(40)
                    ->getQuery();
        }
        $ar = array(
            'nrfaktury' => 'nr faktury',
            'nazwisko' => 'nazwisko',            
            'nip' => 'NIP',         
        );

           $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('szukajfaktur', array('type'=>1)))
                ->setMethod('GET')
                
                ->add('szukaj', 'text', array('required' => false))
                ->add('wedlug', 'choice', array(
                    'choices' => $ar,
                    'multiple' => false,
                    'required' => false
                ))
                ->add('datazakonczenia', 'text', array(                
                    'required' => false,
                    'mapped'=> true,
                    'read_only' => true
                ))
                ->add('datarozpoczecia', 'text', array(                    
                    'required' => false,
                    'read_only' => true,
                    'mapped'=> true
                ))             
                ->getForm();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $search, $request->query->get('page', 1)/* page number */, 25/* limit per page */
        );


        return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView()
        ));
    }

    public function indexproformaAction(Request $request, $search = null) {
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getId();
        $em = $this->getDoctrine()->getManager();

        $req = $em->getRepository('InfogoldKlienciBundle:Faktura');

        $qb2 = $req->createQueryBuilder('p');
        if (!$search) {
        $search = $qb2
                ->leftJoin('p.userfaktury', 'c')
                ->where('c=' . $userId)
                ->andwhere('p.rodzaj = :rodzaj')
                ->add('orderBy', 'p.datafaktury DESC')
                ->setParameter('rodzaj', 2)
                ->setMaxResults(40)
                ->getQuery();
        }
             $ar = array(
            'nrfaktury' => 'nr faktury',
            'nazwisko' => 'nazwisko',            
            'nip' => 'NIP',         
        );

               $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => true))
                ->add('wedlug', 'choice', array(
                    'choices' => $ar,
                    'multiple' => false,
                    'required' => true
                ))
                ->add('datazakonczenia', 'text', array(                
                    'required' => false,
                    'read_only' => true
                ))
                ->add('datarozpoczecia', 'text', array(                    
                    'required' => false,
                    'read_only' => true
                ))             
                ->getForm();

        $paginator2 = $this->get('knp_paginator');
        $pagination = $paginator2->paginate(
                $search, $request->query->get('page', 1)/* page number */, 25/* limit per page */
        );
        return $this->render('InfogoldKlienciBundle:Faktura:indexproforma.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a Faktura entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $faktura = $em->getRepository('InfogoldKlienciBundle:Faktura')->find($id);

        if (!$faktura) {
            throw $this->createNotFoundException('Unable to find Faktura entity.');
        }
        $trvat = $this->tablevat($faktura);

        return $this->render('InfogoldKlienciBundle:Faktura:show.html.twig', array(
                    'faktura' => $faktura,
                    'trvat' => $trvat
        ));
    }

    protected function tablevat($faktura) {
        $trvat = "";
        $stawka = array();
        $i = 0;
        foreach ($faktura->getSprzedaz() as $value) {
            if ($i == 0) {
                $stawka[$value->getProdukty()->getVat()] = $value->getCenabrutto() - ($value->getIlosc() * $value->getCenaProduktu());
            } else if ($i > 0 && array_key_exists($value->getProdukty()->getVat(), $stawka)) {
                $stawka[$value->getProdukty()->getVat()] = $stawka[$value->getProdukty()->getVat()] + ($value->getCenabrutto() - ($value->getIlosc() * $value->getCenaProduktu()));
            } else {
                $stawka[$value->getProdukty()->getVat()] = $value->getCenabrutto() - ($value->getIlosc() * $value->getCenaProduktu());
            }
            $i++;
        }
        foreach ($stawka as $key => $str) {
            $trvat .= "<tr><th>Vat ( $key % ):</th><td>" . number_format($str, 2, ",", "") . " zł</td></tr>";
        }
        return $trvat;
    }

    public function fakturapdfAction(Request $request, $id) {


        $em = $this->getDoctrine()->getManager();
        $faktura = $em->getRepository('InfogoldKlienciBundle:Faktura')->find($id);

        if ($faktura) {

            $trvat = $this->tablevat($faktura);
            $nrfaktury = $faktura->getNrfaktury();  
            $rodzaj = $faktura->getRodzaj() == 1 ? "FV" : "ProForma";
            $html = $this->renderView('InfogoldAccountBundle:Baza:pdfdoc.html.twig', array(
                'faktura' => $faktura,
                'trvat' => $trvat
            ));

            return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$rodzaj.$nrfaktury.'.pdf"'
                    )
            );
        }


        return $this->redirect($this->generateUrl('faktura_show', array('id' => $id)));
    }

    public function nrfakturAction(Request $request, $id) {
        
    }

}
