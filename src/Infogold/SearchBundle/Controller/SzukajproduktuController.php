<?php

namespace Infogold\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;

/**
 * Description of SzukajproduktuController
 *
 * @author Magda
 */
class SzukajproduktuController extends Controller {

    public function indexAction(Request $request) {

        $loggedUser = $this->get('security.context')->getToken()->getUser();

        if (get_class($loggedUser) == "Infogold\UserBundle\Entity\User") {

            $user = $this->get('my.main.admin')->getMainAdmin();
            $userId = $user->getId();
            $enablemagazyn = $user->getenableMagazyn();
            $konsultantlogin = false;
        } else {
            $user = $loggedUser->getFirma();
            $userId = $user->getNrklienta();
            $enablemagazyn = $user->getenableMagazyn();
            $konsultantlogin = true;
        }

        $ar = array(
            'nazwaproduktu' => 'nazwa produktu',
            'nrproduktu' => 'nr produktu',
            'ceny' => 'cena od'
                //'wmagazyniedo' => 'w magazynie do' // do poprawy
        );

        if ($enablemagazyn == true) {
            $ar['wmagazyniedo'] = "w magazynie do";
        }

        $data = array();
        $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => true))
                ->add('wedlug', 'choice', array(
                    'choices' => $ar,
                    'multiple' => false,
                ))
                ->getForm();

        if ($request->isMethod('POST')) {

            $form->bind($request);
            $data = $form->getData();
            $szukaj = $data['szukaj'];
            $nazwaproduktu = ($data['wedlug'] == 'nazwaproduktu' ? true : false);
            $nrproduktu = ($data['wedlug'] == 'nrproduktu' ? true : false);
            $ceny = ($data['wedlug'] == 'ceny' ? true : false);
            $wmagazyniedo = ($data['wedlug'] == 'wmagazyniedo' ? true : false);

            if ($nazwaproduktu) {
                //szukaj po nazwie produktu

                return $this->NazwaproduktuAction($szukaj, $userId, $form, $konsultantlogin);
            } else if ($nrproduktu) {

                //szukaj po nr produktu
                return $this->NrproduktuAction($szukaj, $userId, $form, $konsultantlogin);
            } else if ($ceny) {
                //szukaj po cenie
                return $this->CenyAction($szukaj, $userId, $form, $konsultantlogin);
            } else if ($wmagazyniedo) {
                //szukaj po nr klienta
                return $this->WmagazyniedoAction($szukaj, $userId, $form, $konsultantlogin);
            } else if ($konsultantlogin) {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Pesel składa się z 11 cyfr a nr klienta z 8 cyfr')
                )));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Pesel składa się z 11 cyfr a nr klienta z 8 cyfr')
                )));
            }
        }
        if ($konsultantlogin) {
            return $this->redirect($this->generateUrl('produkt_kons', array(
                                $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
            )));
        } else {
            return $this->redirect($this->generateUrl('produkty', array(
                                $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
            )));
        }
    }

    public function NazwaproduktuAction($szukaj, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.name LIKE :product')
                ->setParameter('product', '%' . $szukaj . '%')
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takiej nazwie')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $entities2[0]->getUserproduktu()->getenableMagazyn()
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takiej nazwie')
                )));
            }
        }
    }

    public function NrproduktuAction($szukaj, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nrproduktu = :product')
                ->setParameter('product', $szukaj)
                ->getQuery();

        $entities2 = $query2->getResult();

        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takim numerze')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $entities2[0]->getUserproduktu()->getenableMagazyn()  // $entities2[0]['userproduktu']->getenableMagazyn()//$this->get('my.main.admin')->getMainAdmin()->getenableMagazyn()//$entities2->getUserproduktu()->getenableMagazyn()//$this->get('my.main.admin')->getMainAdmin()->getenableMagazyn()
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takim numerze')
                )));
            }
        }
    }

    public function CenyAction($szukaj, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');
        $zmicena = str_replace(',', '.', $szukaj);
        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.cenabrutto >= :product')
                ->setParameter('product', $zmicena)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w cenie wyższej od podanej')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $entities2[0]->getUserproduktu()->getenableMagazyn()
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w cenie wyższej od podanej')
                )));
            }
        }
    }

    public function WmagazyniedoAction($szukaj, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.magazyn <= :product')
                ->setParameter('product', $szukaj)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w magazynie w ilosci mniejszej od podanej')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $entities2[0]->getUserproduktu()->getenableMagazyn()
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w magazynie w ilosci mniejszej od podanej')
                )));
            }
        }
    }

    //put your code here
}
