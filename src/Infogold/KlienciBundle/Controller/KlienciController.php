<?php

namespace Infogold\KlienciBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\KlienciBundle\Entity\Kontakty;
use Infogold\KlienciBundle\Entity\Klienci;
use Infogold\KlienciBundle\Form\KlienciType;
use Infogold\KlienciBundle\Form\KlienciFirmowiType;
use Doctrine\ORM\EntityRepository;

/**
 * Klienci controller.
 *
 */
class KlienciController extends Controller {

    /**
     * Lists all Klienci entities.
     *
     */
    public function indywidualniAction(Request $request) {

        //pokazuje wszystkich klientów indywidualnych stworzonych przez zalogowanego konsultanta
        
        $loggedUser = $this->get('security.context')->getToken()->getUser();
               
        $userId = $loggedUser->getId();
        $user = $loggedUser->getFirma()->getId();

          $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => false))
                ->add('konsultant', 'entity', array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($user) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.firma', 'c')
                                ->where('c=' . $user);
                    },
                    'required' => false,
                    'label' => 'Według Konsultanta',
                    'empty_value' => 'Wybierz',
                ))
                ->getForm();

        $em = $this->getDoctrine()->getManager();
        $req = $em->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->leftJoin('p.KlientKonsultanta', 'c')
                ->where('c=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->add('orderBy', 'p.updated DESC')
                ->setMaxResults(30 )
                ->getQuery();

     $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query,
        $request->query->get('page', 1)/*page number*/,
        25/*limit per page*/
    );

        return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                    'form' => $form->createView(),
                    'pagination' =>  $pagination,
        ));
    }

    public function firmowiAction(Request $request) {
      
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $userId = $loggedUser->getId();
        $user = $loggedUser->getFirma()->getId();
        $em = $this->getDoctrine()->getManager();
        $req = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->leftJoin('p.KlientKonsultanta', 'c')
                ->where('c=' . $userId)
                ->andWhere('p.nipklienta IS NOT NULL')
                ->add('orderBy', 'p.created DESC')               
                ->setMaxResults(30 )
                ->getQuery();

               $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $query,
        $request->query->get('page', 1)/*page number*/,
        25/*limit per page*/
    );

             $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => false))
                ->add('konsultant', 'entity', array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($user) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.firma', 'c')
                                ->where('c=' . $user);
                    },
                    'required' => false,
                    'label' => 'Według Konsultanta',
                    'empty_value' => 'Wybierz',
                ))
                ->getForm();

        return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView()
        ));
    }

    /**
     * Creates a new Klienci entity.
     *
     */
    public function createAction(Request $request) {

        $entity = new Klienci();
        $form = $this->createForm(new KlienciType(), $entity);
        $form->bind($request);

        //zalogowany konsultant
        $konsultant = $this->get('security.context')->getToken()->getUser();
        //nazleżący do firmy
        $firma = $konsultant->getFirma();

        $entity->setKlientKonsultanta($konsultant);
        $entity->setUser($firma);
        
        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
               ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'u')
                ->where ('u='.$firma->getId())
                ->getQuery();


         $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('klienci_show', array('id' => $entity->getId())));
        }

        return $this->render('InfogoldKlienciBundle:Klienci:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'nrklienta' => $numerklienta
        ));
    }

    public function createfirmaAction(Request $request) {
        
        $entity = new Klienci();
        $form = $this->createForm(new KlienciFirmowiType(), $entity);
        $form->bind($request);

         //zalogowany konsultant
        $konsultant = $this->get('security.context')->getToken()->getUser();
        //nazleżący do firmy
        $firma = $konsultant->getFirma();

        $entity->setKlientKonsultanta($konsultant);
        $entity->setUser($firma);
        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
               ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'u')
                ->where ('u='.$firma->getId())
                ->getQuery();


         $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('klienci_show', array('id' => $entity->getId())));
        }

        return $this->render('InfogoldKlienciBundle:Klienci:newfirmowy.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                     'nrklienta' => $numerklienta
        ));
    }

    /**
     * Displays a form to create a new Klienci entity.
     *
     */
    public function newAction() {
        $konsultant = $this->get('security.context')->getToken()->getUser();
        //nazleżący do firmy
        $firma = $konsultant->getFirma()->getId();
        $entity = new Klienci();
        
        $form = $this->createForm(new KlienciType(), $entity);
        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'u')
                ->where ('u='.$firma)
                ->getQuery();

        $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");
     
                
              
        return $this->render('InfogoldKlienciBundle:Klienci:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'nrklienta' => $numerklienta
        ));
    }

    public function newfirmowyAction() {
        $konsultant = $this->get('security.context')->getToken()->getUser();
        //nazleżący do firmy
        $firma = $konsultant->getFirma()->getId();
        
        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'u')
                ->where ('u='.$firma)
                ->getQuery();

        $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");
        $entity = new Klienci();
        $form = $this->createForm(new KlienciFirmowiType(), $entity);

        return $this->render('InfogoldKlienciBundle:Klienci:newfirmowy.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                     'nrklienta' => $numerklienta
        ));
    }

    /**
     * Finds and displays a Klienci entity oraz wyświetla kontakty.
     *
     */
    public function showAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        # pobierz klienta o $id
        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        $req = $em->getRepository('InfogoldKlienciBundle:Kontakty');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->leftJoin('p.Klient', 'c')
                ->where('c=' . $id)
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        $ent = $query->getResult();

        # pobierz jego kontakty

        $user = $this->get('security.context')->getToken()->getUser();
            $login = $this->get('security.context')->getToken()->getUsername(); 
        $konsultantid = $user->getId();

        $req2 = $em->getRepository('InfogoldKlienciBundle:Kontakty');
        $qb2 = $req2->createQueryBuilder('p');
        $query2 = $qb2
                ->leftJoin('p.Klient', 'c')
                ->leftJoin('p.PrzypisanyDo', 'd')
                ->where('c= :id')
                ->andwhere('d= :konsultantid')
                ->setParameter('konsultantid', $konsultantid)
                ->setParameter('id', $id)
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        $ent2 = $query2->getResult();

        $forms = array();
        
        foreach ($ent2 as $path) {
            //edycja kontaktu
            $editForm = $this->createForm("infogold_kliencibundle_kontaktytype", $path);
            $form = $editForm->createView();
            $forms[] = $form;
            
            
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }
        $editForm = $this->createForm("infogold_kliencibundle_kontaktytype");
        $deleteForm = $this->createDeleteForm($id);

        #utwórz kontakt dla klienta o $id 
        $enti = new Kontakty();
        $enti->setKlient($entity);
        $enti->setUtworzonyPrzez($login);
        $enti->setStatus(true);

        $formkont = $this->createForm("infogold_kliencibundle_kontaktytype", $enti);
        $formkont->bind($request);

        if ($formkont->isValid()) {
            $er = $this->getDoctrine()->getManager();
            $er->persist($enti);
            $er->flush();
            # zapisz kontakt i przekieruj na stronę show
            return $this->redirect($this->generateUrl('klienci_show', array('id' => $id)));
        }

        if ($ent2) {

            return $this->render('InfogoldKlienciBundle:Klienci:show.html.twig', array(
                        'entity' => $entity,
                        #pokaż kontakty klienta w ent
                        'edit_form' => $forms,
                       
                        'ent' => $ent,
                        'edycja' => true,
                        'user' => $user,
                        'form' => $formkont->createView(),
                        
                        'delete_form' => $deleteForm->createView(),));
        } else {
            return $this->render('InfogoldKlienciBundle:Klienci:show.html.twig', array(
                        'entity' => $entity,
                        #pokaż kontakty klienta w ent
                        
                        'ent' => $ent,
                        'edycja' => false,
                        'user' => $user,
                        'form' => $formkont->createView(),
                        'delete_form' => $deleteForm->createView(),));
        }
    }

    /**
     * Displays a form to edit an existing Klienci entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }

        $editForm = $this->createForm(new KlienciType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldKlienciBundle:Klienci:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /*
      public function editKontaktAction($id) {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('InfogoldKlienciBundle:Kontakty')->find($id);

      if (!$entity) {
      throw $this->createNotFoundException('Unable to find Kontakty entity.');
      }

      $editForm = $this->createForm("infogold_kliencibundle_kontaktytype", $entity);
      $deleteForm = $this->createDeleteForm($id);

      return $this->render('InfogoldKlienciBundle:Klienci:editkontakty.html.twig', array(
      'entity' => $entity,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
      ));
      }
     */

    public function editFirmowyAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }

        $editForm = $this->createForm(new KlienciFirmowiType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldKlienciBundle:Klienci:editfirmowy.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Klienci entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        if ($entity->getPeselKlienta()) {
            $editForm = $this->createForm(new KlienciType(), $entity, array(
                'validation_groups' => array('editprywatni')               
                ));
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('klienci_show', array('id' => $id)));
            }
        } else {
            $editForm = $this->createForm(new KlienciFirmowiType(), $entity, array(
                'validation_groups' => array('editfirma')));
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('klienci_show', array('id' => $id)));
            }
        }

        return $this->render('InfogoldKlienciBundle:Klienci:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    public function updateKontaktAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Kontakty')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }

        $req = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->select('p.id')
                ->innerJoin('p.KontaktKlienta', 'c')
                ->where('c=' . $id)
                ->getQuery();
        $ent = $query->getSingleResult();
        foreach ($ent as $wartosc) {
            $wartosc;
        }


        $deleteForm = $this->createDeleteForm($id);

        $editForm = $this->createForm("infogold_kliencibundle_kontaktytype", $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('klienci_show', array('id' => $wartosc)));
        }





        return $this->render('InfogoldKlienciBundle:Klienci:editkontakty.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Klienci entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $formdel = $this->createDeleteForm($id);


        $formdel->bind($request);

        if ($formdel->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Klienci entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('klienci'));
    }

    public function deletekontaktAction(Request $request, $id) {
       $user = $this->get('security.context')->getToken()->getUser();
        $er = $this->getDoctrine()->getManager();

        $req = $er->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $req->createQueryBuilder('p');

        $query = $qb
                ->select('p.id')
                ->innerJoin('p.KontaktKlienta', 'c')
                ->where('c=' . $id)
                ->getQuery();
        $ent = $query->getSingleResult();
        foreach ($ent as $klientaid) {
            $klientaid;
        }
        if ($request->getMethod() == 'POST') {
            $komentarz = $request->get('komentarz');
            
            $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InfogoldKlienciBundle:Kontakty')->find($id);
        $entity->setStatus(false);
        $entity->setPowodzamkniecia($komentarz);
        $entity->setKontaktzamknietyprzez($user);
        $entity->setDatazamkniecia(new \DateTime ('today'));
        $em->flush();
        }
    
        return $this->redirect($this->generateUrl('klienci_show', array('id' => $klientaid)));
    }

    /**
     * Creates a form to delete a Klienci entity by id.
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
