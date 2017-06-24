<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\KlienciBundle\Entity\Kontakty;
use Infogold\KlienciBundle\Entity\Klienci;
use Infogold\KlienciBundle\Entity\ProduktyKlienta;
use Symfony\Component\HttpFoundation\Response;
use Infogold\KlienciBundle\Form\KlienciType;
use Infogold\KlienciBundle\Form\FakturaType;
use Infogold\AccountBundle\Form\KontaktyBazaType;
use Infogold\KlienciBundle\Entity\Faktura;
use Symfony\Component\Form\FormError;
use Infogold\KlienciBundle\Form\KlienciFirmowiType;
use Doctrine\ORM\EntityRepository;

class BazaController extends Controller {

    public function bazaklientowAction(Request $request) {

        //pokazuje wszystkich klientów indywidualnych stworzonych w danej firmie przez jej konsultantów
        $checkuser = $this->get('security.context')->getToken()->getUser();

        if (get_class($checkuser) == "Infogold\UserBundle\Entity\User") {
            $loggedUser = $this->get('my.main.admin')->getMainAdmin();
            $userId = $loggedUser->getNrklienta();
        } else {
            $userId = $checkuser->getFirma()->getId();
        }

        $em2 = $this->getDoctrine()->getManager();
        $em3 = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => false))
                ->add('konsultant', 'entity', array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($userId) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.firma', 'c')
                                ->where('c.Nrklienta=' . $userId);
                    },
                    'required' => false,
                    'label' => 'Według Konsultanta',
                    'empty_value' => 'Wybierz Konsultanta',
                    'attr' => ['data-select' => 'true']
                ))
                ->getForm();

        $req = $em2->getRepository('InfogoldKlienciBundle:Klienci');
        $req3 = $em3->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $req->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->add('orderBy', 'p.updated DESC')
                //  ->setMaxResults(30)
                ->getQuery();
        $qb3 = $req3->createQueryBuilder('p');

        $query3 = $qb3
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NOT NULL')
                ->getQuery();

        $countfirma = count($query3->getResult());


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query2, $request->query->get('page', 1)/* page number */, 10/* limit per page */
        );
        if (get_class($checkuser) == "Infogold\UserBundle\Entity\User") {

            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'pagination' => $pagination,
                        'form' => $form->createView(),
                        'countfirma' => $countfirma
            ));
        } else {
            return $this->render('InfogoldKlienciBundle:Baza:bazaklientow.html.twig', array(
                        'pagination' => $pagination,
                        'form' => $form->createView(),
            ));
        }
    }

    public function bazaklientowfirmowychAction(Request $request) {

        //pokazuje wszystkich klientów indywidualnych stworzonych w danej firmie przez jej konsultantów

        $checkuser = $this->get('security.context')->getToken()->getUser();

        if (get_class($checkuser) == "Infogold\UserBundle\Entity\User") {
            $loggedUser = $this->get('my.main.admin')->getMainAdmin();
            $userId = $loggedUser->getNrklienta();
        } else {
            $userId = $checkuser->getFirma()->getId();
        }

        $em2 = $this->getDoctrine()->getManager();
        $em3 = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => false))
                ->add('konsultant', 'entity', array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($userId) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.firma', 'c')
                                ->where('c.Nrklienta=' . $userId);
                    },
                    'required' => false,
                    'label' => 'Według Konsultanta',
                    'empty_value' => 'Wybierz Konsultanta',
                    'attr' => ['data-select' => 'true']
                ))
                ->getForm();



        $req = $em2->getRepository('InfogoldKlienciBundle:Klienci');
        $req3 = $em3->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $req->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NOT NULL')
                ->add('orderBy', 'p.created DESC')
                //  ->setMaxResults(30)
                ->getQuery();

        $qb3 = $req3->createQueryBuilder('p');
        $query3 = $qb3
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                //  ->add('orderBy', 'p.created DESC')
                //  ->setMaxResults(30)
                ->getQuery();
        $countind = count($query3->getResult());

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query2, $request->query->get('page', 1)/* page number */, 10/* limit per page */
        );
        if (get_class($checkuser) == "Infogold\UserBundle\Entity\User") {
            return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                        'pagination' => $pagination,
                        'form' => $form->createView(),
                        'countind' => $countind
            ));
        } else {
            return $this->render('InfogoldKlienciBundle:Baza:bazaklientowfirmowych.html.twig', array(
                        'pagination' => $pagination,
                        'form' => $form->createView()
            ));
        }
    }

    public function createbazaAction(Request $request) {
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getNrklienta();
        $entity = new Klienci();
        $form = $this->createForm(new KlienciType(), $entity);
        $form->bind($request);

        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->getQuery();
        $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");

        //zalogowany konsultant
        $firma = $this->get('my.main.admin')->getMainAdmin();
        //nazleżący do firmy
        $entity->setUser($firma);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $entity->getId())));
        }

        return $this->render('InfogoldAccountBundle:Baza:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'nrklienta' => $numerklienta
        ));
    }

    public function createfirmabazaAction(Request $request) {
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getNrklienta();
        $entity = new Klienci();
        $form = $this->createForm(new KlienciFirmowiType(), $entity);

        $form->bind($request);
        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->getQuery();


        $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");
        $firma = $this->get('my.main.admin')->getMainAdmin();
        $entity->setUser($firma);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $entity->getId())));
        }

        return $this->render('InfogoldAccountBundle:Baza:newfirmowy.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'nrklienta' => $numerklienta
        ));
    }

    /**
     * Displays a form to create a new Klienci entity.
     *
     */
    public function newbazaAction() {
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getNrklienta();
        $entity = new Klienci();

        $form = $this->createForm(new KlienciType(), $entity);
        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->getQuery();


        $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");
        return $this->render('InfogoldAccountBundle:Baza:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'nrklienta' => $numerklienta
        ));
    }

    public function newfirmowybazaAction() {
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getNrklienta();
        $entity = new Klienci();
        $form = $this->createForm(new KlienciFirmowiType(), $entity);
        $em = $this->getDoctrine()->getManager();
        $request2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request2->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.numerklienta) AS max_score')
                ->leftJoin('s.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->getQuery();


        $numer = $query->getSingleResult();
        $numerklienta = ($numer["max_score"] ? $numer["max_score"] : "10000000");
        return $this->render('InfogoldAccountBundle:Baza:newfirmowy.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'nrklienta' => $numerklienta
        ));
    }

    public function zapiszkontaktAction(Request $request, $id) {

        $user = $this->get('my.main.admin')->getMainAdmin();
        $em = $this->getDoctrine()->getManager();
        # pobierz klienta o $id
        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        $enti = new Kontakty();

        $enti->setStatus(true);
        $enti->setKlient($entity);
        $enti->setUtworzonyPrzez($user);

        $formkont = $this->createForm(new KontaktyBazaType($user->getNrklienta()), $enti);
        $formkont->bind($request);

        if ($formkont->isValid()) {
            $er = $this->getDoctrine()->getManager();
            $er->persist($enti);
            $er->flush();
            # zapisz kontakt i przekieruj na stronę show
            return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)) . '#tab_content2');
        }
        return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)) . '#tab_content2');
    }

    public function zapiszsprzedazAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        # pobierz klienta o $id
        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);
        # stworz formularz sprzedaży

        $produkty = new ProduktyKlienta();
        $produkty->addProduktyKlienta($entity);
        $formprodukt = $this->createForm("infogold_produkty_klienta", $produkty);
        $formprodukt->bind($request);
        $jednostkamiary = $formprodukt->get('jednostkamiary')->getData();
        $vat = $formprodukt->get('cenaProduktu')->getData() * ( $formprodukt->get('vat')->getData() / 100);
        $cenabrutto = $formprodukt->get('ilosc')->getData() * ($formprodukt->get('cenaProduktu')->getData() + $vat);
        $produktynullname = $formprodukt->get('produkty')->getData();
        if ($formprodukt->isValid()) {
            $produkty->setJednostkamiary($jednostkamiary);
            $produkty->setCenabrutto($cenabrutto);
            $produkty->setProduktynullname($produktynullname);
            //odejmujemy z magazynu tylko po wystawieniu faktury orygianłu    $produkty->getProdukty()->setMagazyn($produkty->getProdukty()->getMagazyn() - $formprodukt->get('ilosc')->getData());
            $er = $this->getDoctrine()->getManager();
            $er->persist($produkty);
            $er->flush();
            # zapisz produkt i przekieruj na stronę show
            return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)));
        }
        return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)));
    }

    /**
     * Finds and displays a Klienci entity oraz wyświetla kontakty.
     *
     */
    public function showbazaAction(Request $request, $id) {


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

        $user = $this->get('my.main.admin')->getMainAdmin();

        $produkty = new ProduktyKlienta();
        $produkty->addProduktyKlienta($entity);

        $formprodukt = $this->createForm("infogold_produkty_klienta", $produkty);

        $enti = new Kontakty();
        $enti->setStatus(true);
        $enti->setKlient($entity);
        $enti->setUtworzonyPrzez($user);

        $formkont = $this->createForm(new KontaktyBazaType($user->getNrklienta()), $enti);
        # stworz formularze edycji kontaktow
        $forms = array();

        foreach ($ent as $path) {

            $editForm = $this->createForm(new KontaktyBazaType($user->getNrklienta()), $path);
            $form = $editForm->createView();
            $forms[] = $form;
        }

        # stworz tabele sprzedanych produktów

        $req3 = $em->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
        $qb3 = $req3->createQueryBuilder('p');
        $query3 = $qb3
                ->leftJoin('p.ProduktyKlienta', 'c')
                ->where('c= :id')
                ->setParameter('id', $id)
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        $zakupione = $query3->getResult();

        if ($zakupione) {
            $faktura = new Faktura;
            $formfaktura = $this->createForm(new FakturaType(), $faktura);
            $forrmularzfaktury = $formfaktura->createView();
        } else {

            $forrmularzfaktury = false;
        }
        #sprawdź czy klient ma jakieś faktury
        $faktury = $em->getRepository('InfogoldKlienciBundle:Faktura')
                ->findBy(
                array('dlaklienta' => $id), array('datafaktury' => 'DESC')
        );



        if ($faktury) {
            $fakturapdf = true;
        } else {
            $fakturapdf = false;
        }


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }
        $editForm = $this->createForm(new KontaktyBazaType($user->getNrklienta()));
        $deleteForm = $this->createDeleteForm($id);

        #utwórz kontakt dla klienta o $id 
        #sprawdzamy czy klient ma włączoną obsługę magazynu

        $nrklienta = $user->getNrklienta();
        $em2 = $this->getDoctrine()->getManager();

        $reqmagazyn = $em2->getRepository('InfogoldUserBundle:User');

        $qbmagazyn = $reqmagazyn->createQueryBuilder('p');

        $query2 = $qbmagazyn
                ->select('p.enableMagazyn')
                ->where('p.Nrklienta=' . $nrklienta)
                ->andWhere('p.locked=false')
                ->andWhere('p.roles LIKE :role')
                ->setParameter('role', '%"ROLE_ADMIN"%')
                ->add('orderBy', 'p.id DESC')
                ->getQuery();

        $magazyn = $query2->getOneOrNullResult();


        return $this->render('InfogoldAccountBundle:Baza:show.html.twig', array(
                    'entity' => $entity,
                    #pokaż kontakty klienta w ent
                    'edit_form' => $forms,
                    'formfaktura' => $forrmularzfaktury,
                    'fakturapdf' => $fakturapdf,
                    'faktury' => $faktury,
                    'zakupione' => $zakupione,
                    'ent' => $ent,
                    'edycja' => true,
                    'user' => $user,
                    'form' => $formkont->createView(),
                    'magazyn' => $magazyn['enableMagazyn'],
                    'form_produkt' => $formprodukt->createView(),
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing Klienci entity.
     *
     */
    public function editbazaAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }

        $editForm = $this->createForm(new KlienciType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldAccountBundle:Baza:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    public function editfirmowybazaAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }

        $editForm = $this->createForm(new KlienciFirmowiType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldAccountBundle:Baza:editfirmowy.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Klienci entity.
     *
     */
    public function updatebazaAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Klienci entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        if ($entity->getNipklienta()) {
            $editForm = $this->createForm(new KlienciFirmowiType(), $entity, array(
                'validation_groups' => array('editfirma')));
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)));
            }
            return $this->render('InfogoldAccountBundle:Baza:editfirmowy.html.twig', array(
                        'entity' => $entity,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
            ));
        } else {
            $editForm = $this->createForm(new KlienciType(), $entity, array(
                'validation_groups' => array('editprywatni')
            ));
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)));
            }
            return $this->render('InfogoldAccountBundle:Baza:edit.html.twig', array(
                        'entity' => $entity,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
            ));
        }
    }

    public function updatekontaktbazaAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKlienciBundle:Kontakty')->find($id);
        $user = $this->get('my.main.admin')->getMainAdmin();
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

        $editForm = $this->createForm(new KontaktyBazaType($user->getNrklienta()), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $wartosc)) . '#tab_content2');
        }
        return $this->render('InfogoldAccountBundle:Baza:editkontakty.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Klienci entity.
     *
     */
    public function deletebazaAction(Request $request, $id) {


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

        return $this->redirect($this->generateUrl('baza_klientow'));
    }

    public function deletekontaktbazaAction(Request $request, $id) {

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
            $entity->setKontaktzamknietyprzez('administratora');
            $entity->setDatazamkniecia(new \DateTime('today'));
            $em->flush();
        }

        return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $klientaid)) . '#tab_content2');
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

    public function cenaajaxAction(Request $request) {

        if ($request->getMethod() == 'POST') {

            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);
            $cena = $entity->getCenaProduktu();
            $vat = $entity->getVat();
            $magazyn = $entity->getMagazyn();
            $jednostkamiary = $entity->getJednostkamiary();
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            return $response->setContent(json_encode(array(
                        'cena' => $cena,
                        'vat' => $vat,
                        'magazyn' => $magazyn,
                        'jednostkamiary' => $jednostkamiary
            )));
        }
    }

    public function fakturaorgnrAction(Request $request) {

        if ($request->getMethod() == 'POST') {

            $id = $request->get('id');

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldKlienciBundle:Faktura');
            $qb = $entity->createQueryBuilder('p');
            $query = $qb
                    ->select('p')
                    ->leftJoin('p.userfaktury', 'c')
                    ->where('c=' . $id)
                    ->andwhere('p.rodzaj= :rodzaj')
                    ->setParameter('rodzaj', 1)
                    ->orderBy('p.id', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery();


            $numer = $query->getSingleResult();
            if ($numer) {
                $numerostatniejfaktury = $numer->getNrfaktury();
                $nowy = preg_split('/([A-Za-z]+)|\\/|\\-/', $numerostatniejfaktury, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                $nowynumer = array();

                foreach ($nowy as $element) {


                    if (is_numeric($element) && end($nowy) == $element) {

                        if (preg_match('/(^[0]+)/', $element) === 1) {
                            // Starts with http (case insensitive).

                            $zera = preg_split('/(^[0]+)/', $element, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                            var_export($zera);
                            $pierwsza = reset($zera);

                            $druga = end($zera) + 1;
                            $nowynumer[] = $pierwsza . $druga;
                        } else {

                            $nowynumer[] = $element + 1;
                        }
                    } else {
                        $nowynumer[] = $element;
                    }
                }
                $comma_separated = implode("/", $nowynumer);

                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                return $response->setContent(json_encode(array(
                            'nrfaktury' => $comma_separated
                )));
            } else {
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                return $response->setContent(json_encode(array(
                            'nrfaktury' => false
                )));
            }
        }
    }

    public function fakturapronrAction(Request $request) {


        if ($request->getMethod() == 'POST') {

            $id = $request->get('id');


            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldKlienciBundle:Faktura');
            $qb = $entity->createQueryBuilder('p');
            $query = $qb
                    ->select('p')
                    ->leftJoin('p.userfaktury', 'c')
                    ->where('c=' . $id)
                    ->andwhere('p.rodzaj= :rodzaj')
                    ->setParameter('rodzaj', 2)
                    ->orderBy('p.id', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery();


            $numer = $query->getSingleResult();
            if ($numer) {
                $numerostatniejfaktury = $numer->getNrfaktury();
                $nowy = preg_split('/([A-Za-z]+)|\\/|\\-/', $numerostatniejfaktury, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                $nowynumer = array();

                foreach ($nowy as $element) {


                    if (is_numeric($element) && end($nowy) == $element) {

                        if (preg_match('/(^[0]+)/', $element) === 1) {
                            // Starts with http (case insensitive).

                            $zera = preg_split('/(^[0]+)/', $element, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                            var_export($zera);
                            $pierwsza = reset($zera);

                            $druga = end($zera) + 1;
                            $nowynumer[] = $pierwsza . $druga;
                        } else {

                            $nowynumer[] = $element + 1;
                        }
                    } else {
                        $nowynumer[] = $element;
                    }
                }
                $comma_separated = implode("/", $nowynumer);

                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                return $response->setContent(json_encode(array(
                            'nrfaktury' => $comma_separated
                )));
            } else {
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
                return $response->setContent(json_encode(array(
                            'nrfaktury' => false
                )));
            }
        }
    }

    #PDF faktura

    public function usunsprzedazAction($id, $idzakup) {

        $ed = $this->getDoctrine()->getManager();
        $usun = $ed->getRepository('InfogoldKlienciBundle:ProduktyKlienta')->find($idzakup);

        $fakturaid = $usun->getZakup();
        $fakturaproformaid = $usun->getProforma();

        if ($fakturaid) {
            if ($usun->getProdukty()) {
                $usun->getProdukty()->setMagazyn($usun->getProdukty()->getMagazyn() + $usun->getIlosc());
            }
        }
        $ed->remove($usun);
        $ed->flush();

        if ($fakturaid) {
            $produkty = $ed->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
            $qb = $produkty->createQueryBuilder('p');
            $query = $qb
                    ->select('p')
                    ->leftJoin('p.zakup', 'c')
                    ->where('c.id = :idfaktury')
                    ->setParameter('idfaktury', $fakturaid)
                    ->getQuery();
            $produktyfaktura = $query->getResult();


            $ile = count($produktyfaktura);
            if ($ile == 0) {

                $fakturadousuniecia = $ed->getRepository('InfogoldKlienciBundle:Faktura')->find($fakturaid);

                $ed->remove($fakturadousuniecia);
                $ed->flush();
            } else {

                $wartoscnetto = 0;
                $vat = 0;
                foreach ($produktyfaktura as $value) {
                    //  $value->getProdukty()->setMagazyn($value->getProdukty()->getMagazyn() + $value->getIlosc());
                    $wartoscnetto = $wartoscnetto + $value->getCenaProduktu() * $value->getIlosc();
                    $vat = round($vat, 2) + ($value->getCenaProduktu() * $value->getIlosc() * $value->getProdukty()->getVat() / 100);
                }
                $em = $this->getDoctrine()->getManager();
                $wartoscbrutto = $wartoscnetto + $vat;
                $wartoscbruttoround = round($wartoscbrutto, 2);
                $wartoscbrutto2 = str_replace(".", ",", $wartoscbruttoround);

                $slownie = $this->kwotaslownie($wartoscbrutto2);

                $fakturaupdate = $em->getRepository('InfogoldKlienciBundle:Faktura')->find($fakturaid);
                $fakturaupdate->setSlownie($slownie);

                $fakturaupdate->setWartoscnetto($wartoscnetto);
                $fakturaupdate->setWartoscbrutto($wartoscbruttoround);
                $em->flush();
            }
        }
        if ($fakturaproformaid) {
            $produkty = $ed->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
            $qb = $produkty->createQueryBuilder('p');
            $query = $qb
                    ->select('p')
                    ->leftJoin('p.proforma', 'c')
                    ->where('c.id = :idfaktury')
                    ->setParameter('idfaktury', $fakturaproformaid)
                    ->getQuery();
            $produktyproforma = $query->getResult();
            $ile = count($produktyproforma);
            if ($ile == 0) {
                $fakturadousuniecia = $ed->getRepository('InfogoldKlienciBundle:Faktura')->find($fakturaproformaid);
                $ed->remove($fakturadousuniecia);
                $ed->flush();
            } else {

                $wartoscnetto = 0;
                $vat = 0;
                foreach ($produktyproforma as $value) {
                    $wartoscnetto = $wartoscnetto + $value->getCenaProduktu() * $value->getIlosc();
                    $vat = round($vat, 2) + ($value->getCenaProduktu() * $value->getIlosc() * $value->getProdukty()->getVat() / 100);
                }
                $em = $this->getDoctrine()->getManager();
                $wartoscbrutto = $wartoscnetto + $vat;
                $wartoscbruttoround = round($wartoscbrutto, 2);
                $wartoscbrutto2 = str_replace(".", ",", $wartoscbruttoround);

                $slownie = $this->kwotaslownie($wartoscbrutto2);

                $fakturaupdate = $em->getRepository('InfogoldKlienciBundle:Faktura')->find($fakturaproformaid);
                $fakturaupdate->setSlownie($slownie);

                $fakturaupdate->setWartoscnetto($wartoscnetto);
                $fakturaupdate->setWartoscbrutto($wartoscbruttoround);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)));
    }

    public function fakturaAction(Request $request, $id) {

        $loggedUser = $this->get('my.main.admin')->getMainAdmin();

        if ($request->getMethod() == 'POST') {

            $faktura = new Faktura;
            $formfaktura = $this->createForm(new FakturaType(), $faktura);
            $formfaktura->bind($request);

            $checklist = $request->get('check_list');
            $platnosc = $formfaktura->get('platnosc')->getData();
            $nrfaktury = $formfaktura->get('nrfaktury')->getData();

            $datafaktury = $formfaktura->get('datafaktury')->getData();
            $termin = $formfaktura->get('terminplatnosci')->getData();
            $rodzaj = $formfaktura->get('rodzaj')->getData();

            $em = $this->getDoctrine()->getManager();
            $edi = $this->getDoctrine()->getManager();
            $en = $this->getDoctrine()->getManager();

            $check = array();

            $entity = $em->getRepository('InfogoldKlienciBundle:Klienci')->find($id);
            if (!$checklist) {
                $formfaktura->addError(new FormError('- Proszę zaznaczyć przynajmniej jeden sprzedany produkt'));
            }

            if ($formfaktura->isValid()) {
                foreach ($checklist as $value) {

                    $check[] = $edi->getRepository('InfogoldKlienciBundle:ProduktyKlienta')->find($value);
                }
                $wartoscnetto = 0;
                $vat = 0;

                $update = array();
                $proforma = array();

                foreach ($check as $value) {
                    if ($value->getZakup()) {
                        $update[] = $value->getZakup()->getId();
                    } else if ($value->getProforma()) {
                        $proforma[] = $value->getProforma()->getId();
                    }
                    $wartoscnetto = $wartoscnetto + $value->getCenaProduktu() * $value->getIlosc();
                    $vat = round($vat, 2) + ($value->getCenaProduktu() * $value->getIlosc() * $value->getProdukty()->getVat() / 100);
                }
                if ($update) {
                    $result = array_filter(array_unique($update));
                    $updateoryginal = array_map("trim", $result);
                } else if ($proforma) {
                    $result2 = array_filter(array_unique($proforma));
                    $updateproforma = array_map("trim", $result2);
                }
                $wartoscbrutto = $wartoscnetto + $vat;
                $wartoscbruttoround = round($wartoscbrutto, 2);
                $wartoscbrutto2 = str_replace(".", ",", $wartoscbruttoround);

                $slownie = $this->kwotaslownie($wartoscbrutto2);

                foreach ($check as $value) {
                    $faktura->setSlownie($slownie);
                    $faktura->setPlatnosc($platnosc);
                    $faktura->setWartoscnetto($wartoscnetto);
                    $faktura->setWartoscbrutto($wartoscbruttoround);
                    $faktura->setDatafaktury($datafaktury);
                    $faktura->setTerminplatnosci($termin);
                    $faktura->setNrfaktury($nrfaktury);
                    $faktura->setUserfaktury($loggedUser);
                    $faktura->setDlaklienta($entity);
                   // if ($rodzaj == "Oryginał") {
                    if ($rodzaj == 1) {
                        $value->setZakup($faktura);
                        $magazyn = $value->getProdukty()->getMagazyn() - $value->getIlosc();
                        $check_magazyn = $magazyn < 0 ? 0 : $magazyn;
                        $value->getProdukty()->setMagazyn($check_magazyn);
                    } else {
                        $value->setProforma($faktura);
                        // $value->setZakup(null);
                    }
                    $em->persist($faktura);
                }
                $em->flush();

                // nadpisywanie oryginałów faktury i aktualizacja starych 
                if ($update) {

                    $ero = $this->getDoctrine()->getManager();
                    $eri = $this->getDoctrine()->getManager();
                    $nettoupdate = 0;
                    $vatupdate = 0;
                    $upd = array();

                    foreach ($updateoryginal as $valueupdate) {

                        $produkty2 = $ero->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
                        $qb = $produkty2->createQueryBuilder('p');
                        $query = $qb
                                ->select('p')
                                ->leftJoin('p.zakup', 'c')
                                ->where('c.id = :faktura')
                                ->setParameter('faktura', $valueupdate)
                                ->getQuery();
                        $upd[] = $query->getResult();
                    }

                    foreach ($upd as $valupd) {

                        foreach ($valupd as $val) {
                            $nettoupdate = $nettoupdate + $val->getCenaProduktu() * $val->getIlosc();
                            $vatupdate = $vatupdate + ($val->getCenaProduktu() * $val->getIlosc() * $val->getProdukty()->getVat() / 100);
                        }
                    }
                    $wartoscbruttoupd = $nettoupdate + $vatupdate;

                    $wartoscbruttoroundupd = round($wartoscbruttoupd, 2);
                    $wartoscbruttoupdate = str_replace(".", ",", $wartoscbruttoroundupd);
                    $slownieupdate = $this->kwotaslownie($wartoscbruttoupdate);
                    $updfakture = array();
                    foreach ($updateoryginal as $valuefaktura) {
                        $updfakture[] = $eri->getRepository('InfogoldKlienciBundle:Faktura')->find($valuefaktura);
                    }
                    foreach ($updfakture as $valupdate) {
                        $valupdate->setSlownie($slownieupdate);
                        $valupdate->setWartoscnetto($nettoupdate);
                        $valupdate->setWartoscbrutto($wartoscbruttoroundupd);
                    }
                    $eri->flush();
                }
                // nadpisywanie faktur proforma i aktualizacja starych 
                else if ($proforma) {

                    $eroproforma = $this->getDoctrine()->getManager();
                    $eriproforma = $this->getDoctrine()->getManager();
                    $nettoupdate = 0;
                    $vatupdate = 0;
                    $updproforma = array();

                    foreach ($updateproforma as $valueupdate) {

                        $produkty2 = $eroproforma->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
                        $qb = $produkty2->createQueryBuilder('p');
                        $query = $qb
                                ->select('p')
                                ->leftJoin('p.proforma', 'c')
                                ->where('c.id = :faktura')
                                ->setParameter('faktura', $valueupdate)
                                ->getQuery();
                        $updproforma[] = $query->getResult();
                    }

                    foreach ($updproforma as $valupd) {

                        foreach ($valupd as $val) {
                            $nettoupdate = $nettoupdate + $val->getCenaProduktu() * $val->getIlosc();
                            $vatupdate = $vatupdate + ($val->getCenaProduktu() * $val->getIlosc() * $val->getProdukty()->getVat() / 100);
                        }
                    }
                    $wartoscbruttoupd = $nettoupdate + $vatupdate;

                    $wartoscbruttoroundupd = round($wartoscbruttoupd, 2);
                    $wartoscbruttoupdate = str_replace(".", ",", $wartoscbruttoroundupd);
                    $slownieupdate = $this->kwotaslownie($wartoscbruttoupdate);
                    $updfakture = array();
                    foreach ($updateproforma as $valuefaktura) {
                        $updfakture[] = $eriproforma->getRepository('InfogoldKlienciBundle:Faktura')->find($valuefaktura);
                    }
                    foreach ($updfakture as $valupdate) {
                        $valupdate->setSlownie($slownieupdate);
                        $valupdate->setWartoscnetto($nettoupdate);
                        $valupdate->setWartoscbrutto($wartoscbruttoroundupd);
                    }
                    $eriproforma->flush();
                }

                // usuwanie faktur do której był przypisany tylko jedna sprzedaż a ona została usunięta
                $fakturyklienta = $en->getRepository('InfogoldKlienciBundle:Faktura')->findByDlaklienta($id);
                $edek = $this->getDoctrine()->getManager();
                //   $produkt = array();
                $produkty = $edek->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
                $qb = $produkty->createQueryBuilder('p');

                $query = $qb
                        ->select('p')
                        ->leftJoin('p.ProduktyKlienta', 'c')
                        ->where('c.id = :klient')
                        ->setParameter('klient', $id)
                        ->getQuery();
                $produkt = $query->getResult();
                $arr0 = array();
                $arr1 = array();
                $arr2 = array();

                foreach ($produkt as $value) {
                    if ($value->getProforma()) {
                        $arr0[] = $value->getProforma()->getId();
                    }
                    if ($value->getZakup()) {
                        $arr1[] = $value->getZakup()->getId();
                    }
                }

                $faktury = array_merge($arr0, $arr1);

                foreach ($fakturyklienta as $value) {
                    $arr2[] = $value->getId();
                }
                $fakturyduusuniecia = array_diff($arr2, $faktury);
                if ($fakturyduusuniecia) {
                    foreach ($fakturyduusuniecia as $usun) {

                        $usuntefaktury = $en->getRepository('InfogoldKlienciBundle:Faktura')->find($usun);
                        $en->remove($usuntefaktury);
                    }
                    $en->flush();
                }
            } else {
                $this->get('session')->getFlashBag()->add('error', $this->getErrorMessages($formfaktura));
            }
        }
        return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)));
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
        if ($request->getMethod() == 'POST') {
            $radioid = $request->get('faktury');
            if ($radioid) {
                $em = $this->getDoctrine()->getManager();
                $faktura = $em->getRepository('InfogoldKlienciBundle:Faktura')->find($radioid);

                if ($faktura) {
                    $trvat = $this->tablevat($faktura);
                    $html = $this->renderView('InfogoldAccountBundle:Baza:pdfdoc.html.twig', array(
                        'faktura' => $faktura,
                        'trvat' => $trvat
                    ));
                    return new Response(
                            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="filename.pdf"'
                            )
                    );
                }
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Zaznacz fakturę do pobrania');
            }
        }
        return $this->redirect($this->generateUrl('klienci_baza_show', array('id' => $id)));
    }

    protected function kwotaslownie($kwot) {

        if (!function_exists('str_split')) {

            function str_split($string, $len = 1) {
                if ($len < 1)
                    return false;
                for ($i = 0, $rt = Array(); $i < ceil(strlen($string) / $len); $i++)
                    $rt[$i] = substr($string, $len * $i, $len);
                return($rt);
            }

        }

        $kwota = explode(',', $kwot);

        $zl = preg_replace('/[^-\d]+/', '', $kwota[0]);
        $gr = preg_replace('/[^\d]+/', '', substr(isset($kwota[1]) ? $kwota[1] : 0, 0, 2));
        while (strlen($gr) < 2)
            $gr .= '0';

        return $this->slownie($zl) . ' ' . $this->odmiana(Array('złoty', 'złote', 'złotych'), $zl) .
                (intval($gr) == 100 ? '' :
                        ' i ' . $this->slownie($gr) . ' ' . $this->odmiana(Array('grosz', 'grosze', 'groszy'), $gr));
    }

    protected function slownie($int) {

        $slowa = Array(
            'minus',
            Array(
                'zero',
                'jeden',
                'dwa',
                'trzy',
                'cztery',
                'pięć',
                'sześć',
                'siedem',
                'osiem',
                'dziewięć'),
            Array(
                'dziesięć',
                'jedenaście',
                'dwanaście',
                'trzynaście',
                'czternaście',
                'piętnaście',
                'szesnaście',
                'siedemnaście',
                'osiemnaście',
                'dziewiętnaście'),
            Array(
                'dziesięć',
                'dwadzieścia',
                'trzydzieści',
                'czterdzieści',
                'pięćdziesiąt',
                'sześćdziesiąt',
                'siedemdziesiąt',
                'osiemdziesiąt',
                'dziewięćdziesiąt'),
            Array(
                'sto',
                'dwieście',
                'trzysta',
                'czterysta',
                'pięćset',
                'sześćset',
                'siedemset',
                'osiemset',
                'dziewięćset'),
            Array(
                'tysiąc',
                'tysiące',
                'tysiące',
                'tysiące',
                'tysięcy',
                'tysięcy',
                'tysięcy',
                'tysięcy',
                'tysięcy'),
        );

        $in = preg_replace('/[^-\d]+/', '', $int);
        $out = '';

        if ($in{0} == '-') {
            $in = substr($in, 1);
            $out = $slowa[0] . ' ';
        }

        $txt = str_split(strrev($in), 3);

        if ($in == 0)
            $out = $slowa[1][0] . ' ';

        for ($i = count($txt) - 1; $i >= 0; $i--) {
            $liczba = (int) strrev($txt[$i]);
            if ($liczba > 0)
                if ($i == 0)
                    $out .= $this->liczba($liczba) . ' ';
                else
                    $out .= ($liczba > 1 ? $this->liczba($liczba) . ' ' : '')
                            . $this->odmiana($slowa[4 + $i], $liczba) . ' ';
        }
        return trim($out);
    }

    protected function liczba($int) { // odmiana dla liczb < 1000
        $slowa = Array(
            'minus',
            Array(
                'zero',
                'jeden',
                'dwa',
                'trzy',
                'cztery',
                'pięć',
                'sześć',
                'siedem',
                'osiem',
                'dziewięć'),
            Array(
                'dziesięć',
                'jedenaście',
                'dwanaście',
                'trzynaście',
                'czternaście',
                'piętnaście',
                'szesnaście',
                'siedemnaście',
                'osiemnaście',
                'dziewiętnaście'),
            Array(
                'dziesięć',
                'dwadzieścia',
                'trzydzieści',
                'czterdzieści',
                'pięćdziesiąt',
                'sześćdziesiąt',
                'siedemdziesiąt',
                'osiemdziesiąt',
                'dziewięćdziesiąt'),
            Array(
                'sto',
                'dwieście',
                'trzysta',
                'czterysta',
                'pięćset',
                'sześćset',
                'siedemset',
                'osiemset',
                'dziewięćset'),
            Array(
                'tysiąc',
                'tysiące',
                'tysiące',
                'tysiące',
                'tysięcy',
                'tysięcy',
                'tysięcy',
                'tysięcy',
                'tysięcy'),
        );
        $wynik = '';
        $j = abs((int) $int);

        if ($j == 0)
            return $slowa[1][0];
        $jednosci = $j % 10;
        $dziesiatki = ($j % 100 - $jednosci) / 10;
        $setki = ($j - $dziesiatki * 10 - $jednosci) / 100;

        if ($setki > 0)
            $wynik .= $slowa[4][$setki - 1] . ' ';

        if ($dziesiatki > 0)
            if ($dziesiatki == 1)
                $wynik .= $slowa[2][$jednosci] . ' ';
            else
                $wynik .= $slowa[3][$dziesiatki - 1] . ' ';

        if ($jednosci > 0 && $dziesiatki != 1)
            $wynik .= $slowa[1][$jednosci] . ' ';
        return $wynik;
    }

    protected function odmiana($odmiany, $int) { // $odmiany = Array('jeden','dwa','pięć')
        $txt = $odmiany[2];
        if ($int == 1)
            $txt = $odmiany[0];
        $jednosci = (int) substr($int, -1);
        $reszta = $int % 100;
        if (($jednosci > 1 && $jednosci < 5) & !($reszta > 10 && $reszta < 20))
            $txt = $odmiany[1];
        return $txt;
    }

    protected function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $retval = array();
        foreach ($form->getErrors() as $key => $error) {
            if ($error->getMessagePluralization() !== null) {
                $retval['message'] = $this->get('translator')->transChoice(
                        $error->getMessage(), $error->getMessagePluralization(), $error->getMessageParameters(), 'validators'
                );
            } else {
                $retval['message'] = $this->get('translator')->trans($error->getMessage(), array(), 'validators');
            }
        }
        foreach ($form->all() as $name => $child) {
            $errors = $this->getErrorMessages($child);
            if (!empty($errors)) {
                $retval[$name] = $errors;
            }
        }
        $blad = "";


        foreach ($retval as $value)
            if ($value === end($retval)) {
                $blad .= $value;
            } else {
                $blad .= $value . PHP_EOL;
            }

        return $blad;
    }

}
