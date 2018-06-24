<?php

namespace Infogold\KonsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Infogold\AccountBundle\Entity\Produkt;
use Infogold\AccountBundle\Form\ProduktyType;

class ProduktController extends Controller {

    /**
     * Lists all Produkty entities.
     *
     */
    public function indexAction(Request $request) {



        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $userId = $loggedUser->getFirma()->getId();

        $em = $this->getDoctrine()->getManager();

        $ar = array(
            'nazwaproduktu' => 'nazwa produktu',
            'nrproduktu' => 'nr produktu',
            'ceny' => 'cena od'
                //'wmagazyniedo' => 'w magazynie do' // do poprawy
        );

        if ($loggedUser->getFirma()->getenableMagazyn() == true) {
            $ar['wmagazyniedo'] = "w magazynie do";
        }
        $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => true))
                ->add('wedlug', 'choice', array(
                    'choices' => $ar,
                    'multiple' => false,
                ))
                ->getForm();

        $entities = $em->getRepository('InfogoldAccountBundle:Produkt')->findBy(
                array('userproduktu' => $userId), array('nrproduktu' => 'ASC'), 30
        );



        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $entities, $request->query->get('page', 1)/* page number */, 20 /* limit per page */);
        return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView(),
                    'magazyn' => $loggedUser->getFirma()->getenableMagazyn()
        ));
    }

    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Produkty entity.');
        }


        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');
        $query2 = $qb
                ->select('p.id, c.id, c.imie, c.nazwisko, c.peselklienta, c.nazwaklienta, c.nipklienta, c.miasto, c.ulica, d.cenaProduktu, d.created, d.ilosc ')
                ->leftJoin('p.produkt', 'd')
                ->leftJoin('d.ProduktyKlienta', 'c')
                ->where('p.id=' . $id)
                ->getQuery();


        $entities2 = $query2->getResult();

        return $this->render('InfogoldKonsultantBundle:Produkty:show.html.twig', array(
                    'entity' => $entity,
                    'klienci' => $entities2,
        ));
    }

}
