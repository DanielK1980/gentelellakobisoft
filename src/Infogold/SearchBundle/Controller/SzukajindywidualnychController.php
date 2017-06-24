<?php

namespace Infogold\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;

class SzukajindywidualnychController extends Controller {

    public function indexAction(Request $request) {

        $loggedUser = $this->get('security.context')->getToken()->getUser();   
      //  var_export($loggedUser->getId());
      //  exit();                        
        
         if (get_class($loggedUser) == "Infogold\UserBundle\Entity\User") {
             
            $userId = $loggedUser->getNrklienta();
            
            $konsultantlogin = false;
         }
         else {
             
             $userId = $loggedUser->getFirma()->getNrklienta();  
             $konsultantlogin = true;
         }       

        $data = array();
        $form = $this->createFormBuilder($data)
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
                    'empty_value' => 'Wybierz',
                ))
                ->getForm();

        if ($request->isMethod('POST')) {

            $form->bind($request);

            // data is an array with "szukaj", "konsultant" keys
            $data = $form->getData();

            $szukaj = $data['szukaj'];
            $konsultant = $data['konsultant'];
            $dlugosc = strlen($szukaj);
            $imieinazwisko = explode(" ", $szukaj);

            if (empty($konsultant)) {
                if ((is_numeric($szukaj) === false ) && (isset($imieinazwisko[1]) === false)) {
                    //szukaj po nazwisku
                    
                    return $this->NazwiskoAction($szukaj, $userId, $form, $konsultantlogin);
                } else if ((is_numeric($szukaj) === false ) && ($imieinazwisko[1])) {
                    //szukaj po imieniu i nazwisku
                    return $this->ImieNazwiskoAction($imieinazwisko[0], $imieinazwisko[1], $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 11) {
                    //szukaj po peselu
                    return $this->PeselAction($szukaj, $userId, $form,$konsultantlogin);
                } else if ($dlugosc === 8) {
                    //szukaj po nr klienta
                    return $this->NrklientaAction($szukaj, $userId, $form, $konsultantlogin);
                } else if ($konsultantlogin){
                    return $this->redirect($this->generateUrl('klienci', array(
                                $this->get('session')->getFlashBag()->add('error', 'Pesel składa się z 11 cyfr a nr klienta z 8 cyfr')
                    )));
                }
                else {
                     return $this->redirect($this->generateUrl('baza_klientow', array(
                                $this->get('session')->getFlashBag()->add('error', 'Pesel składa się z 11 cyfr a nr klienta z 8 cyfr')
                    )));
                }
            }
            else {
                if (empty($szukaj)) {
                    //szukaj po konsultancie
                    return $this->TylkoKonsultantAction($konsultant, $userId, $form, $konsultantlogin);
                }
                else if ((is_numeric($szukaj) === false ) && (isset($imieinazwisko[1]) === false)) {
                    //szukaj po nazwisku
                    return $this->NazwiskoKonsultantAction($szukaj, $konsultant, $userId, $form, $konsultantlogin);
                } else if ((is_numeric($szukaj) === false ) && ($imieinazwisko[1])) {
                    //szukaj po imieniu i nazwisku
                    return $this->ImieNazwiskoKonsultantAction($imieinazwisko[0], $imieinazwisko[1], $konsultant, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 11) {
                    //szukaj po peselu
                    return $this->PeselKonsultantAction($szukaj, $konsultant, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 8) {
                    //szukaj po nr klienta
                    return $this->NrklientaKonsultantAction($szukaj, $konsultant, $userId, $form, $konsultantlogin);
                } else {
                    return $this->redirect($this->generateUrl('baza_klientow', array(
                                $this->get('session')->getFlashBag()->add('error', 'Pesel składa się z 11 cyfr a nr klienta z 8 cyfr')
                   )));
                }
            }
        }
        if ($konsultantlogin){    
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
           )));
        }        
        else { 
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
           )));
        
        }
        
    }

    public function PeselAction($pesel, $userId, $form, $konsultantlogin) {

        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.peselklienta= ?1')
                
                ->setParameter(1, $pesel)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin){
            
             if ($entities2) {
                 
         return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze pesel')
            )));
        }

        }
        else {
        if ($entities2) {


            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze pesel')
           )));
        }
        }
    }

    public function NrklientaAction($nrklienta, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.numerklienta= ?1')
                ->setParameter(1, $nrklienta)
                ->getQuery();

        $entities2 = $query2->getResult();
        
     if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze klienta')
            )));
        }
        }
        else {
        if ($entities2) {

            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze klienta')
           )));
        }
        }
    }

    public function NazwiskoAction($nazwisko, $userId, $form, $konsultantlogin) {

        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.nazwisko LIKE ?1')
                ->setParameter(1, '%'.$nazwisko.'%')
                ->getQuery();


        $entities2 = $query2->getResult();

if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym nazwisku')
            )));
        }

        }
        else {
        if ($entities2) {
            
                      
            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym nazwisku')
           )));
        }
        }
    }

    public function ImieNazwiskoAction($imie, $nazwisko, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.imie= ?1')
                ->andWhere('p.nazwisko= ?2')
                ->setParameter(1, $imie)
                ->setParameter(2, $nazwisko)
                ->getQuery();


        $entities2 = $query2->getResult();

if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym imieniu i nazwisku')
            )));
        }

        }
        else {
        if ($entities2) {


            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym imieniu i nazwisku')
            )));
        }
        }
    }

    public function PeselKonsultantAction($pesel, $konsultant, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.peselklienta= ?1')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->setParameter(1, $pesel)
                ->setParameter(2, $konsultant)
                ->getQuery();


        $entities2 = $query2->getResult();

      if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze pesel należącego do konsultanta '.$konsultant)
           )));
        }

        }
        else {
        if ($entities2) {


            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze pesel należącego do konsultanta '.$konsultant)
            )));
        }
        }
    }

    public function NrklientaKonsultantAction($nrklienta, $konsultant, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.numerklienta= ?1')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->setParameter(1, $nrklienta)
                ->setParameter(2, $konsultant)
                ->getQuery();


        $entities2 = $query2->getResult();


if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze klienta należącego do konsultanta '.$konsultant)
           )));
        }

        }
        else {
        if ($entities2) {


            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym numerze klenta należącego do konsultanta '.$konsultant)
            )));
        }
        }
    }

    public function NazwiskoKonsultantAction($nazwisko, $konsultant, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.nazwisko= ?1')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->setParameter(1, $nazwisko)
                ->setParameter(2, $konsultant)
                ->getQuery();


        $entities2 = $query2->getResult();




       if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym nazwisku należącego do konsultanta '.$konsultant)
           )));
        }

        }
        else {
        if ($entities2) {


            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym nazwisku należącego do konsultanta '.$konsultant)
            )));
        }
        }
    }

    public function ImieNazwiskoKonsultantAction($imie, $nazwisko, $konsultant, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.imie= ?1')
                ->andWhere('p.nazwisko= ?2')
                ->andWhere('p.KlientKonsultanta= ?3')
                ->setParameter(1, $imie)
                ->setParameter(2, $nazwisko)
                ->setParameter(3, $konsultant)
                ->getQuery();


        $entities2 = $query2->getResult();

if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym imieniu i nazwisku należącego do konsultanta '.$konsultant)
           )));
        }

        }
        else {
        if ($entities2) {


            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klienta o podanym imieniu i nazwisku należącego do konsultanta '.$konsultant)
            )));
        }
        }
    }

    public function TylkoKonsultantAction($konsultant, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta IS NULL')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->setParameter(2, $konsultant)
                ->getQuery();


        $entities2 = $query2->getResult();




       if ($konsultantlogin){
             if ($entities2) {


            return $this->render('InfogoldKlienciBundle:Klienci:index.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klientów dla danego konsultanta')
            )));
        }

        }
        else {
        if ($entities2) {


            return $this->render('InfogoldAccountBundle:Baza:bazaklientow.html.twig', array(
                        'entities' => $entities2,
                        'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('baza_klientow', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak klientów dla danego konsultanta')
            )));
        }
        }
    }

}
