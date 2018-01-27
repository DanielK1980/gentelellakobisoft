<?php

namespace Infogold\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use DateTime;

/**
 * Description of SzukajproduktuController
 *
 * @author Magda
 */
class SzukajfakturController extends Controller {

    public function indexAction(Request $request, $type = 1) {

        $loggedUser = $this->get('security.context')->getToken()->getUser();

        if (get_class($loggedUser) == "Infogold\UserBundle\Entity\User") {

            $user = $this->get('my.main.admin')->getMainAdmin();
            $userId = $user->getId();

            $konsultantlogin = false;
        } else {
            $user = $loggedUser->getFirma();
            $userId = $user->getNrklienta();
            $konsultantlogin = true;
        }
        $ar = array(
            'nrfaktury' => 'nr faktury',
            'nazwisko' => 'nazwisko',
            'nip' => 'NIP',
        );

        $data = array();
        $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('szukajfaktur',array("type"=>$type)))
                ->setMethod('GET')
                ->add('szukaj', 'text', array('required' => FALSE))
                ->add('wedlug', 'choice', array(
                    'choices' => $ar,
                    'multiple' => false,
                    'required' => false
                ))
                ->add('datazakonczenia', 'text', array(
                    'required' => false,
                    'mapped' => true,
                    'read_only' => true
                ))
                ->add('datarozpoczecia', 'text', array(
                    'required' => false,
                    'mapped' => true,
                    'read_only' => true
                ))
                ->getForm();

        if ($request->isMethod('GET')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $szukaj = trim($data['szukaj']);
                $nrfaktury = ($data['wedlug'] == 'nrfaktury' ? true : false);
                $nazwisko = ($data['wedlug'] == 'nazwisko' ? true : false);
                $nip = ($data['wedlug'] == 'nip' ? true : false);
                $databegin = (isset($data['datarozpoczecia']) && (!empty($data['datarozpoczecia'])) ? new DateTime($data['datarozpoczecia']) : new DateTime("2010-01-01"));
                $dataend = (isset($data['datazakonczenia']) && (!empty($data['datazakonczenia'])) ? new DateTime($data['datazakonczenia']) : new DateTime("now"));

                if ($nrfaktury) {
                    return $this->Nrfaktury($szukaj, $userId, $konsultantlogin, $form, $type);
                } else if ($nazwisko) {
                    return $this->Nazwisko($szukaj, $userId, $konsultantlogin, $form, $type, $databegin, $dataend);
                } else if ($nip) {
                    return $this->Nip($szukaj, $userId, $konsultantlogin, $form, $type, $databegin, $dataend);
                }else if ($databegin) {
                    return $this->Data($szukaj, $userId, $konsultantlogin, $form, $type, $databegin, $dataend);
                }
            }
            if ($type == 1) {
                return $this->redirect($this->generateUrl('faktura_oryginal', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
                )));
            } else {
                return $this->redirect($this->generateUrl('faktura_proforma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
                )));
            }
        }
    }

    protected function Nrfaktury($szukaj, $userId, $konsultantlogin, $form, $type) {
        $em2 = $this->getDoctrine()->getManager();
        $req = $em2->getRepository('InfogoldKlienciBundle:Faktura');
        $qb = $req->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userfaktury', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nrfaktury LIKE :nr')
                ->andWhere('p.rodzaj= :rodzaj')
                ->setParameter('rodzaj', $type)
                ->setParameter('nr', '%' . $szukaj . '%')
                ->add('orderBy', 'p.datafaktury DESC')
                ->getQuery();

        $request = $this->get('request_stack');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query2, $request->getCurrentRequest()->query->get('page', 1)/* page number */, 25/* limit per page */
        );
        if (count($pagination) > 0) {
            if ($type == 1) {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            } else {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            }
        } else {
            if ($type == 1) {
                return $this->redirect($this->generateUrl('faktura_oryginal', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury o takim numerze')
                )));
            } else {
                return $this->redirect($this->generateUrl('faktura_proforma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury o takim numerze')
                )));
            }
        }
    }

        protected function Data($szukaj, $userId, $konsultantlogin, $form, $type, $datebegin, $dateend) {

        $em2 = $this->getDoctrine()->getManager();
        $req = $em2->getRepository('InfogoldKlienciBundle:Faktura');
        $qb = $req->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userfaktury', 'c')
                ->leftJoin('p.dlaklienta', 'd')
                ->where('c.Nrklienta=' . $userId)
                
                ->andWhere('p.rodzaj= :rodzaj')
                ->andWhere('p.datafaktury>= :dataod')
                ->andWhere('p.datafaktury<= :datado')
                ->setParameter('rodzaj', $type)
                ->setParameter('dataod', $datebegin->format("Y-m-d"))
                ->setParameter('datado', $dateend->format("Y-m-d"))             
                ->add('orderBy', 'p.datafaktury DESC')
                ->getQuery();

        $paginator = $this->get('knp_paginator');
        $request = $this->get('request_stack');

        $pagination = $paginator->paginate(
                $query2, $request->getCurrentRequest()->query->get('page', 1), 25);

        if (count($pagination) > 0) {
            if ($type == 1) {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            } else {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            }
        } else {
            if ($type == 1) {
                return $this->redirect($this->generateUrl('faktura_oryginal', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury dla zakresu dat')
                )));
            } else {
                return $this->redirect($this->generateUrl('faktura_proforma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury dla zakresu dat')
                )));
            }
        }
    }
    
    protected function Nazwisko($szukaj, $userId, $konsultantlogin, $form, $type, $datebegin, $dateend) {

        $em2 = $this->getDoctrine()->getManager();
        $req = $em2->getRepository('InfogoldKlienciBundle:Faktura');
        $qb = $req->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userfaktury', 'c')
                ->leftJoin('p.dlaklienta', 'd')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('d.nazwisko LIKE :nazwisko')
                ->andWhere('p.rodzaj= :rodzaj')
                ->andWhere('p.datafaktury>= :dataod')
                ->andWhere('p.datafaktury<= :datado')
                ->setParameter('rodzaj', $type)
                ->setParameter('dataod', $datebegin->format("Y-m-d"))
                ->setParameter('datado', $dateend->format("Y-m-d"))
                ->setParameter('nazwisko', '%' . $szukaj . '%')
                ->add('orderBy', 'p.datafaktury DESC')
                ->getQuery();

        $paginator = $this->get('knp_paginator');
        $request = $this->get('request_stack');

        $pagination = $paginator->paginate(
                $query2, $request->getCurrentRequest()->query->get('page', 1), 3);

        if (count($pagination) > 0) {
            if ($type == 1) {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            } else {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            }
        } else {
            if ($type == 1) {
                return $this->redirect($this->generateUrl('faktura_oryginal', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury dla takiego nazwiska')
                )));
            } else {
                return $this->redirect($this->generateUrl('faktura_proforma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury dla takiego nazwiska')
                )));
            }
        }
    }

    protected function Nip($szukaj, $userId, $konsultantlogin, $form, $type, $datebegin, $dateend) {
        $em2 = $this->getDoctrine()->getManager();
        $req = $em2->getRepository('InfogoldKlienciBundle:Faktura');
        $qb = $req->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userfaktury', 'c')
                ->leftJoin('p.dlaklienta', 'd')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('d.nipklienta = :nip')
                ->andWhere('p.rodzaj= :rodzaj')
                ->andWhere('p.datafaktury>= :dataod')
                ->andWhere('p.datafaktury<= :datado')
                ->setParameter('rodzaj', $type)
                ->setParameter('dataod', $datebegin->format("Y-m-d"))
                ->setParameter('datado', $dateend->format("Y-m-d"))
                ->setParameter('nip', $szukaj)
                ->add('orderBy', 'p.datafaktury DESC')
                ->getQuery();

        $request = $this->get('request_stack');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query2, $request->getCurrentRequest()->query->get('page', 1)/* page number */,5/* limit per page */
        );

          if (count($pagination) > 0) {
            if ($type == 1) {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            } else {
                return $this->render('InfogoldKlienciBundle:Faktura:indexoryginal.html.twig', array(
                            'pagination' => $pagination,
                            'form' => $form->createView()
                ));
            }
        } else {
            if ($type == 1) {
                return $this->redirect($this->generateUrl('faktura_oryginal', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury dla takiego NIP')
                )));
            } else {
                return $this->redirect($this->generateUrl('faktura_proforma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak faktury pro forma dla takiego NIP')
                )));
            }
        }
    }
}
