<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GrafikController
 *
 * @author Magda
 */

namespace Infogold\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Infogold\AccountBundle\Entity\Grafik;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormError;
use Doctrine\Common\Collections\ArrayCollection;
use DatePeriod;
use DateTime;
use DateInterval;

class GrafikController extends Controller {
    
    private function strftimeV($format, $timestamp) {
            return iconv("ISO-8859-2", "UTF-8", ucfirst(strftime($format, $timestamp)));
        }

    //put your code here
    public function indexAction($date) {

        $arrLocales = array('pl_PL', 'pl', 'Polish_Poland.28592');
        setlocale(LC_ALL, $arrLocales);


        // Create array containing abbreviations of days of week.
        $daysOfWeek = array('Nd', 'Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob');


        // What is the first day of the month in question?
        $firstDayOfMonth = strtotime(date('Y-m-01', strtotime($date)));
        $miesiac = date("m", $firstDayOfMonth);
        $rok = date("Y", $firstDayOfMonth);
        // How many days does this month contain?
        $numberDays = date('t', $firstDayOfMonth);

        // Retrieve some information about the first day of the
        // month in question.
        $dateComponents = getdate($firstDayOfMonth);

        // What is the name of the month in question?
       

        // What is the name of the month in question?
        $monthName = $this->strftimeV("%B", $firstDayOfMonth)." ".$this->strftimeV("%G", $firstDayOfMonth) ;

        // What is the index value (0-6) of the first day of the
        // month in question.
        $dayOfWeek = $dateComponents['wday'];

        $nazwydniarray = array_merge($daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek);
        $nazwydni = array_slice($nazwydniarray, $dayOfWeek, $numberDays);
      
       // $userdzialy = $this->get('my.main.admin')->getMainAdmin()->getDzialy();
        
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getNrklienta();
    
        $em5 = $this->getDoctrine()->getManager();
        
         $req = $em5->getRepository('InfogoldAccountBundle:Dzialy');

            $qb5 = $req->createQueryBuilder('p');

            $query2 = $qb5
                    ->select('p')
                    ->leftJoin('p.DzialyFirmy', 'c')
                    ->where('c.Nrklienta=' . $userId)                 
                    ->andWhere('c.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
               
                    ->getQuery();
        
        
            $userdzialy = new ArrayCollection($query2->getResult());
        
        
        $nazwydzialow = $userdzialy->getValues();
        $pierwszydzial = $userdzialy->first()->getId();

        $em = $this->getDoctrine()->getManager();
        $request = $em->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb = $request->createQueryBuilder('p');
        $query = $qb
                ->where('p.KonsultantDzialy=' . $pierwszydzial)
                ->getQuery();

        $konsultanci = $query->getResult();

        $em2 = $this->getDoctrine()->getManager();

        $req2 = $em2->getRepository('InfogoldAccountBundle:grafik');
        $grafiktabela = $req2->findBy(
                array('GrafikDzialy' => $pierwszydzial)
        );
        
        $form = $this->createGreafikForm($pierwszydzial);
    



        return $this->render('InfogoldAccountBundle:Grafik:grafik.html.twig', array(
                    'rok' => $rok,
                    'miesiac' => $miesiac,
                    'nazwydni' => $nazwydni,
                    'ilosc' => $numberDays,
                    'pustedni' => $dayOfWeek,
                    'nazwamiesiaca' => $monthName,
                    'nazwydzialow' => $nazwydzialow,
                    'konsultanci' => $konsultanci,
                    'grafiktabela' => $grafiktabela,
                    'id' => $pierwszydzial,
                    'form' => $form->createView(),
        ));
    }
      private function createGreafikForm($dzial) {
          
        $ustalgrafik = new Grafik();

        return $this->createFormBuilder($ustalgrafik)
                ->add('datarozpoczecia', 'text', array(
                    'mapped' => false,
                    'constraints' => array(
                        new NotBlank(array(
                            'message' => '- Zaznacz datę rozpoczęcia',
                             'groups'  => array('dodaj','usun'))
                        ),
                        new Date()),
                    'required' => true,
                    'read_only' => true
                   
                ))
                ->add('czasrozpoczecia', 'time', array(
                    'widget' => 'single_text',
                   // 'required' => true,
                    'read_only' => true,
                  //   'validation_groups'  => 'dodaj',
                    
                ))
                ->add('czaszakonczenia', 'time', array(
                    'widget' => 'single_text',
                   // 'required' => true,
                    'read_only' => true,
                //   'validation_groups'  => 'dodaj',
                ))
                ->add('datazakonczenia', 'text', array(
                    'mapped' => false,
                    'read_only' => true,
                   
                ))
                ->add('GrafikKonsultanta', 'entity', array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($dzial) {

                        return $er->createQueryBuilder('p')
                                ->where('p.KonsultantDzialy=' . $dzial);
                    },
                    'required' => true,
                    'expanded' => true,
                    'multiple' => true,
                    'label' => false,
                 //   'validation_groups'  => array('dodaj','usun'),
                ))
                ->add('komentarz', 'textarea', array(
                    'required' => false
                ))
                ->add('zapisz', 'submit', array(
                     'attr' => array('class' => 'btn btn-success btn-lg'),
                    'validation_groups'  => array('dodaj'),
                     ))
               ->add('usun', 'submit', array(
                    'attr' => array('class' => 'btn btn-danger btn-lg'),
                    'validation_groups'  => array('usun'),
                     ))
                ->getForm();
    }

    public function dzialAction(Request $request, $id, $date) {

        $arrLocales = array('pl_PL', 'pl', 'Polish_Poland.28592');
        setlocale(LC_ALL, $arrLocales);


        // Create array containing abbreviations of days of week.
        $daysOfWeek = array('Nd', 'Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob');


        // What is the first day of the month in question?
        $firstDayOfMonth = strtotime(date('Y-m-01', strtotime($date)));
        $miesiac = date("m", $firstDayOfMonth);
        $rok = date("Y", $firstDayOfMonth);
        // How many days does this month contain?
        $numberDays = date('t', $firstDayOfMonth);

        // Retrieve some information about the first day of the
        // month in question.
        $dateComponents = getdate($firstDayOfMonth);
   
        // What is the name of the month in question?
        $monthName = $this->strftimeV("%B", $firstDayOfMonth)." ".$dateComponents['year']; //$this->strftimeV("%B", $firstDayOfMonth)." ".$this->strftimeV("%G", $firstDayOfMonth-1) ;

        // What is the index value (0-6) of the first day of the
        // month in question.
        $dayOfWeek = $dateComponents['wday'];

        $nazwydniarray = array_merge($daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek);
        $nazwydni = array_slice($nazwydniarray, $dayOfWeek, $numberDays);
         $loggedUser = $this->get('my.main.admin')->getMainAdmin();
        $userId = $loggedUser->getNrklienta();
    
        $em5 = $this->getDoctrine()->getManager();
        
         $req5 = $em5->getRepository('InfogoldAccountBundle:Dzialy');

            $qb5 = $req5->createQueryBuilder('p');

            $query2 = $qb5
                    ->select('p')
                    ->leftJoin('p.DzialyFirmy', 'c')
                    ->where('c.Nrklienta=' . $userId)                 
                    ->andWhere('c.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                   
                    ->getQuery();
        
        
            $userdzialy = new ArrayCollection($query2->getResult());
        
        $nazwydzialow = $userdzialy->getValues();

        $em = $this->getDoctrine()->getManager();
        $req = $em->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->where('p.KonsultantDzialy=' . $id)
                ->getQuery();

        $konsultanci = $query->getResult();

        $em2 = $this->getDoctrine()->getManager();

        $req2 = $em2->getRepository('InfogoldAccountBundle:grafik');
        $grafiktabela = $req2->findBy(
                array('GrafikDzialy' => $id)
        );
         $form = $this->createGreafikForm($id);


        return $this->render('InfogoldAccountBundle:Grafik:grafik.html.twig', array(
                    'rok' => $rok,
                    'miesiac' => $miesiac,
                    'nazwydni' => $nazwydni,
                    'id' => $id,
                    'ilosc' => $numberDays,
                    'pustedni' => $dayOfWeek,
                    'nazwamiesiaca' => $monthName,
                    'konsultanci' => $konsultanci,
                    'nazwydzialow' => $nazwydzialow,
                    'grafiktabela' => $grafiktabela,
                    'form' => $form->createView(),
        ));
    }

    public function ustalgrafikAction(Request $request, $id, $date) {


        $em = $this->getDoctrine()->getManager();
        $dzial = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($id);

        $ustalgrafik = new Grafik();
        
         $form = $this->createGreafikForm($id);

        if ($request->isMethod('POST')) {
            
              $form->handleRequest($request);   
            $czasrozpoczecia = $form->get('czasrozpoczecia')->getData();
            $czaszakonczenia = $form->get('czaszakonczenia')->getData();
            $komentarz = $form->get('komentarz')->getData();
            $begin = $form->get('datarozpoczecia')->getData();
            $poczatek = new DateTime($begin);

            $grafikKonsultanta = $form->get('GrafikKonsultanta')->getData();

            $datazakonczenia = $form->get('datazakonczenia')->getData();
            if ($czasrozpoczecia && $czaszakonczenia) {
                if ($czasrozpoczecia > $czaszakonczenia) {
                    $to_time = strtotime($czaszakonczenia->modify('+1 day')->format("Y-m-d H:i"));
                } else {
                    $to_time = strtotime($czaszakonczenia->format("Y-m-d H:i"));
                }
                $from_time = strtotime($czasrozpoczecia->format("Y-m-d H:i"));

                $minutes = (integer) (round(abs($to_time - $from_time) / 60, 2));

                if ($minutes > 720) {
                    $form->addError(new FormError('- Czas pracy nie może przkroczyć 12 godzin'));
                }
            }
            if ($form->isValid()) {
                     $nextAction = $form->get('zapisz')->isClicked()
                        ? 'ok'
                         : 'usungrafik';

               if($nextAction == 'ok'){ 
                if ($datazakonczenia) {

                    $koniec = new DateTime($datazakonczenia);
                    $end = $koniec->modify('+1 day');
                    $interval = new DateInterval('P1D');
                    $daterange = new DatePeriod($poczatek, $interval, $end);


                    foreach ($grafikKonsultanta as $konsultanci) {

                        foreach ($daterange as $data) {

                            $request1 = $em->getRepository('InfogoldAccountBundle:Grafik');
                            $qb1 = $request1->createQueryBuilder('p');
                            $query1 = $qb1
                                    ->where('p.data= :data')
                                    ->leftJoin('p.GrafikKonsultanta', 'c')
                                    ->andwhere('c.id= :konsultantid')
                                    ->setParameter('data', $data->format("Y-m-d"))
                                    ->setParameter('konsultantid', $konsultanci->getId())
                                    ->getQuery();

                            $jestgrafik = $query1->getOneOrNullResult();

                            if ($jestgrafik) {
                                $jestgrafik->setCzasrozpoczecia($czasrozpoczecia);
                                $jestgrafik->setCzaszakonczenia($czaszakonczenia);
                                $jestgrafik->setKomentarz($komentarz);
                                $jestgrafik->setMinutypracy($minutes);
                            } else {
                                $ustalgrafik = new Grafik();
                                $ustalgrafik->setCzasrozpoczecia($czasrozpoczecia);
                                $ustalgrafik->setCzaszakonczenia($czaszakonczenia);
                                $ustalgrafik->addGrafikKonsultanta($konsultanci);
                                $ustalgrafik->setMinutypracy($minutes);
                                $ustalgrafik->setKomentarz($komentarz);
                                $ustalgrafik->setGrafikDzialy($dzial);
                                $ustalgrafik->setData($data);
                                $em->persist($ustalgrafik);
                            }
                        }
                    }
                } else {
                    $em = $this->getDoctrine()->getManager();
                    foreach ($grafikKonsultanta as $konsultanci) {

                        $request1 = $em->getRepository('InfogoldAccountBundle:Grafik');
                        $qb1 = $request1->createQueryBuilder('p');
                        $query1 = $qb1
                                ->where('p.data= :data')
                                ->leftJoin('p.GrafikKonsultanta', 'c')
                                ->andwhere('c.id= :konsultantid')
                                ->setParameter('data', $poczatek->format("Y-m-d"))
                                ->setParameter('konsultantid', $konsultanci->getId())
                                ->getQuery();


                        $jestgrafik = $query1->getOneOrNullResult();

                        if ($jestgrafik) {

                            $jestgrafik->setCzasrozpoczecia($czasrozpoczecia);
                            $jestgrafik->setCzaszakonczenia($czaszakonczenia);
                            $jestgrafik->setKomentarz($komentarz);
                            $jestgrafik->setMinutypracy($minutes);
                        } else {
                            $ustalgrafik = new Grafik();
                            $ustalgrafik->setCzasrozpoczecia($czasrozpoczecia);
                            $ustalgrafik->setCzaszakonczenia($czaszakonczenia);
                            $ustalgrafik->addGrafikKonsultanta($konsultanci);
                            $ustalgrafik->setKomentarz($komentarz);
                            $ustalgrafik->setMinutypracy($minutes);
                            $ustalgrafik->setGrafikDzialy($dzial);
                            $ustalgrafik->setData($poczatek);
                            $em->persist($ustalgrafik);
                        }
                    }
                }
                $em->flush();

                $response = array(
                    'id' => $id,
                    'date' => $date
                );
                return $this->redirect($this->generateUrl('grafikdzialu', $response));
                
            } else
                {
                return $this->usungrafikAction($request, $id, $date);
                }
            }
            $response = array(
                'id' => $id,
                'date' => $date,
                $this->get('session')->getFlashBag()->add('error', $this->getErrorMessages($form))
            );

            return $this->redirect($this->generateUrl('grafikdzialu', $response));
          
        }
    }

    public function usungrafikAction(Request $request, $id, $date) {

        $em = $this->getDoctrine()->getManager();
      

        $form = $this->createGreafikForm($id);

        if ($request->isMethod('POST')) {

              $form->handleRequest($request);   

            $begin = $form->get('datarozpoczecia')->getData();
            $poczatek = new DateTime($begin);

            $grafikKonsultanta = $form->get('GrafikKonsultanta')->getData();

            $datazakonczenia = $form->get('datazakonczenia')->getData();

            if ($form->isValid()) {

                if ($datazakonczenia) {

                    $koniec = new DateTime($datazakonczenia);
                    $end = $koniec->modify('+1 day');
                    $interval = new DateInterval('P1D');
                    $daterange = new DatePeriod($poczatek, $interval, $end);
                    
                foreach ($grafikKonsultanta as $konsultanci) {

                        foreach ($daterange as $data) {

                            $request1 = $em->getRepository('InfogoldAccountBundle:Grafik');
                            $qb1 = $request1->createQueryBuilder('p');
                            $query1 = $qb1
                                    ->where('p.data= :data')
                                    ->leftJoin('p.GrafikKonsultanta', 'c')
                                    ->andwhere('c.id= :konsultantid')
                                    ->setParameter('data', $data->format("Y-m-d"))
                                    ->setParameter('konsultantid', $konsultanci->getId())
                                    ->getQuery();

                            $jestgrafik = $query1->getOneOrNullResult();

                            if ($jestgrafik) {
                                $em->remove($jestgrafik);
                                //  $em->flush();
                            }
                        }
                    }
                } else {
                    $em = $this->getDoctrine()->getManager();
                    foreach ($grafikKonsultanta as $konsultanci) {

                        $request1 = $em->getRepository('InfogoldAccountBundle:Grafik');
                        $qb1 = $request1->createQueryBuilder('p');
                        $query1 = $qb1
                                ->where('p.data= :data')
                                ->leftJoin('p.GrafikKonsultanta', 'c')
                                ->andwhere('c.id= :konsultantid')
                                ->setParameter('data', $poczatek->format("Y-m-d"))
                                ->setParameter('konsultantid', $konsultanci->getId())
                                ->getQuery();


                        $jestgrafik = $query1->getOneOrNullResult();

                        if ($jestgrafik) {

                            $em->remove($jestgrafik);
                        }
                    }
                }
                $em->flush();

                $response = array(
                    'id' => $id,
                    'date' => $date
                );
                return $this->redirect($this->generateUrl('grafikdzialu', $response));
            }
            $response = array(
                'id' => $id,
                'date' => $date,
                $this->get('session')->getFlashBag()->add('error', $this->getErrorMessages($form))
            );

            return $this->redirect($this->generateUrl('grafikdzialu', $response));
        }
    }

    protected function getErrorMessages($form) {
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
