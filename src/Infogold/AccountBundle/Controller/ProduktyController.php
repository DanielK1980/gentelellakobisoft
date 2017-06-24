<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\AccountBundle\Entity\Produkt;
use Infogold\AccountBundle\Form\ProduktyType;

/**
 * Produkty controller.
 *
 */
class ProduktyController extends Controller {

    /**
     * Lists all Produkty entities.
     *
     */
    public function indexAction(Request $request) {



        $User = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $User->getNrklienta();
        
        $ar = array(
                        'nazwaproduktu' => 'nazwa produktu',
                        'nrproduktu' => 'nr produktu',
                        'ceny' => 'cena od'
                        //'wmagazyniedo' => 'w magazynie do' // do poprawy
                    );
        
            if($User->getenableMagazyn() == true){              
                        $ar['wmagazyniedo'] = "w magazynie do";                      
            } 
            
       $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => true))
                ->add('wedlug', 'choice', array(
                    'choices' => $ar,
                    'multiple' => false,
                ))
                ->getForm();

        
                $em = $this->getDoctrine()->getManager();
                $req = $em->getRepository('InfogoldAccountBundle:Produkt');

                $qb = $req->createQueryBuilder('p');
                $entities = $qb
                        ->leftJoin('p.userproduktu', 'c')
                        ->where('c.Nrklienta=' . $nrklienta)
                        ->getQuery();


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $entities, $request->query->get('page', 1)/* page number */,15 /* limit per page */);
        return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView(),
                    'magazyn' => $User->getenableMagazyn()
        ));
    }

    /**
     * Creates a new Produkty entity.
     *
     */
    public function createAction(Request $request) {
        
        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $entity = new Produkt();
        $form = $this->createForm(new ProduktyType($nrklienta,$user->getenableMagazyn()), $entity);
        $form->bind($request);
        $cenabrutto = $form->get('cenaProduktu')->getData() + $form->get('cenaProduktu')->getData() * ( $form->get('vat')->getData() / 100);

        if ($form->isValid()) {
            $entity->setUserproduktu($user); // produkt firmy  $user
            $entity->setCenabrutto($cenabrutto);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('produkty_show', array('id' => $entity->getId())));
        }

        return $this->render('InfogoldAccountBundle:Produkty:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Produkty entity.
     *
     */
    public function newAction() {
           
        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $entity = new Produkt();
        $form = $this->createForm(new ProduktyType($nrklienta,$user->getenableMagazyn()), $entity);

        return $this->render('InfogoldAccountBundle:Produkty:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Produkty entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Produkty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');
        $query2 = $qb
                ->select('p.id, c.id, c.imie, c.nazwisko, c.peselklienta, c.nazwaklienta, c.nipklienta, c.miasto, c.ulica, d.cenaProduktu,d.ilosc, d.created ')
                ->leftJoin('p.produkt', 'd')
                ->leftJoin('d.ProduktyKlienta', 'c')
                ->where('p.id=' . $id)
                ->getQuery();


        $entities2 = $query2->getResult();

        return $this->render('InfogoldAccountBundle:Produkty:show.html.twig', array(
                    'entity' => $entity,
                    'klienci' => $entities2,
                    'delete_form' => $deleteForm->createView(),));
    }



    public function editAction($id) {
        
        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Produkty entity.');
        }
        
        $editForm = $this->createForm(new ProduktyType($nrklienta,$user->getenableMagazyn(),$entity->getNrproduktu()), $entity, array(
                'validation_groups' => array('edit')));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldAccountBundle:Produkty:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Produkty entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Produkty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProduktyType($nrklienta,$user->getenableMagazyn(),$entity->getNrproduktu()), $entity, array(
                'validation_groups' => array('edit')));
        $editForm->bind($request);
        $cenabrutto = $editForm->get('cenaProduktu')->getData() + $editForm->get('cenaProduktu')->getData() * ( $editForm->get('vat')->getData() / 100);
        if ($editForm->isValid()) {
            $entity->setCenabrutto($cenabrutto);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('produkty_show', array('id' => $id)));
        }

        return $this->render('InfogoldAccountBundle:Produkty:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Produkty entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Produkty entity.');
            }
            $entity2 = $em->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
            $qb = $entity2->createQueryBuilder('p');
            $query2 = $qb
                    ->select('p')
                    ->leftJoin('p.produkty', 'c')
                    ->where('c=' . $id)
                    ->getQuery();
            $produktyklienta = $query2->getResult();
            foreach ($produktyklienta as $value) {
                $value->setProdukty(null);
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('produkty'));
    }

    /**
     * Creates a form to delete a Produkty entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
