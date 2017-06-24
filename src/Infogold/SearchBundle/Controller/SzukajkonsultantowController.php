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
class SzukajkonsultantowController extends Controller {

    public function indexAction(Request $request) {

        $loggedUser = $this->get('security.context')->getToken()->getUser();

        $userId =  $loggedUser->getNrklienta();

        $data = array();
        $form = $this->createFormBuilder()
                ->add('szukaj', 'text', array('required' => true))
                ->add('wedlug', 'choice', array(
                    'choices' => array(
                        'nazwiska' => 'nazwiska',
                        'imieniainazwiska' => 'imienia i nazwiska',
                        'login' => 'login',
                        'email' => 'adres e-mail'
                    ),
                    'multiple' => false,
                ))
                ->getForm();

        if ($request->isMethod('POST')) {

            $form->bind($request);


            $data = $form->getData();

            $szukaj = $data['szukaj'];
            $nazwiska = ($data['wedlug'] == 'nazwiska' ? true : false);
            $imieniainazwiska = ($data['wedlug'] == 'imieniainazwiska' ? true : false);
            $login = ($data['wedlug'] == 'login' ? true : false);
            $email = ($data['wedlug'] == 'email' ? true : false);

            //  $dlugosc = strlen($szukaj);

            if ($nazwiska) {
                //szukaj po nazwie produktu

                return $this->NazwiskaAction($szukaj, $userId, $form);
            } else if ($imieniainazwiska) {

                $arrnazwiska = explode(" ", $szukaj);
               // var_export($arrnazwiska);
              //  exit();
                if (array_key_exists(1, $arrnazwiska)) {
                    $imie = $arrnazwiska[0];
                    $nazwisko = $arrnazwiska[1];
                    return $this->ImieniainazwiskaAction($imie,$nazwisko, $userId, $form);
                } else {
                    return $this->redirect($this->generateUrl('konsultant', array(
                  $this->get('session')->getFlashBag()->add('error', 'Nie podano nazwiska')
                    )));
                }
            } else if ($login) {
                //szukaj po cenie
                return $this->LoginAction($szukaj, $userId, $form);
            } else if ($email) {
                //szukaj po nr klienta
                return $this->EmailAction($szukaj, $userId, $form);
            } else {
                return $this->redirect($this->generateUrl('konultant', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
                )));
            }
        }
        return $this->redirect($this->generateUrl('konultant', array(
                            $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
        )));
    }

    public function NazwiskaAction($szukaj, $userId, $form){
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKonsultantBundle:Konsultant');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.firma', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nazwisko LIKE :konsultant')
                ->setParameter('konsultant', '%'.$szukaj.'%')
                ->getQuery();

        $entities2 = $query2->getResult();

        if ($entities2) {


            return $this->render('InfogoldKonsultantBundle:Konsultant:index.html.twig', array(
                        'pagination' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('konsultant', array(
                                $this->get('session')->getFlashBag()->add('error', 'Brak konsultanta o podanym nazwisku')
            )));
        }
    }

    public function ImieniainazwiskaAction($imie, $nazwisko, $userId, $form) {
        
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKonsultantBundle:Konsultant');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.firma', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.imie = :konsultantimie')
                ->andWhere('p.nazwisko = :konsultantnazwisko')
                ->setParameter('konsultantimie', $imie)
                ->setParameter('konsultantnazwisko', $nazwisko)
                ->getQuery();

        $entities2 = $query2->getResult();

        if ($entities2) {


            return $this->render('InfogoldKonsultantBundle:Konsultant:index.html.twig', array(
                        'pagination' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('konsultant', array(
                                $this->get('session')->getFlashBag()->add('error', 'Brak konsultanta o podanym imieniu i nazwisku')
            )));
        }
    }

    public function LoginAction($szukaj, $userId, $form) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKonsultantBundle:Konsultant');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.firma', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.username = :konsultant')
                ->setParameter('konsultant', $szukaj)
                ->getQuery();

        $entities2 = $query2->getResult();

        if ($entities2) {
      return $this->render('InfogoldKonsultantBundle:Konsultant:index.html.twig', array(
                        'pagination' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('konsultant', array(
                                $this->get('session')->getFlashBag()->add('error', 'Brak konsultanta o podanym loginie')
            )));
        }
    }

    public function EmailAction($szukaj, $userId, $form) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKonsultantBundle:Konsultant');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.firma', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.email = :konsultant')
                ->setParameter('konsultant', $szukaj)
                ->getQuery();

        $entities2 = $query2->getResult();

        if ($entities2) {


            return $this->render('InfogoldKonsultantBundle:Konsultant:index.html.twig', array(
                        'pagination' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('konsultant', array(
                                $this->get('session')->getFlashBag()->add('error', 'Brak konsultanta o podanym emailu')
            )));
        }
    }

    //put your code here
}
