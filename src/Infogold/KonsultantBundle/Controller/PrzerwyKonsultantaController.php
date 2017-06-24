<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Infogold\KonsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Infogold\KonsultantBundle\Entity\Kolejki;
use Infogold\KonsultantBundle\Entity\Przerwy;
use Infogold\AccountBundle\Entity\RaportyPrzerw;
use DateTime;

class PrzerwyKonsultantaController extends Controller {

    public function indexAction() {

        $User = $this->get('security.context')->getToken()->getUser();
        $IdUser = $User->getId();
        $today = new DateTime('today');


        $em1 = $this->getDoctrine()->getManager();
        $request1 = $em1->getRepository('InfogoldAccountBundle:RaportyPrzerw');
        $qb1 = $request1->createQueryBuilder('p');
        $query1 = $qb1
                ->leftJoin('p.KonsultantRaportPrzerw', 'c')
                ->where('c=' . $IdUser)
                ->andWhere('p.data= :data')
                ->setParameter('data', $today->format("Y-m-d"))
                ->orderBy('p.czasrozpoczecia', 'ASC')
                ->getQuery();

        $raport = $query1->getResult();
         $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('InfogoldKonsultantBundle:przerwy');
        $konsultant = $repository->findOneBy(array('KonsultantPrzerwa' => $IdUser));
        
        $czasprzerw = false;
        $suma = 0;
        if ($raport) {
            foreach ($raport as $value) {

                $suma = $suma + (integer) (strtotime("1970-01-01 " . $value->getCzasprzerwy()->format('H:i:s') . " UTC"));
            }
            $czasprzerw = gmdate("H:i:s", $suma);
        }

        if ($konsultant) {
            $czasrozpoczecia = $konsultant->getCzasrozpoczecia();
            $teraz = new DateTime('now');
            $from_time = strtotime($czasrozpoczecia->format("Y-m-d H:i:s"));
            $to_time = strtotime($teraz->format("Y-m-d H:i:s"));
            $sekundy = (integer) (abs($to_time - $from_time));
            $suma = $suma + $sekundy;
        }

        //  ------------------ PRZERWY  ---------------
        $dzialkonsultanta = $User->getKonsultantDzialy()->getId();
        $limitprzerw = $User->getKonsultantDzialy()->getLimityprzerw();
        $kolejka = $this->aktualnakolejkaAction($dzialkonsultanta);
        $przerwa = $this->aktualnaprzerwaAction($dzialkonsultanta);
        $iloscnaprzerwie = count($przerwa);
        $ostatniklucz = count($kolejka);
        $kluczkonsultanta = $this->kluczkonsultantaAction();
        $jestemwkolejce = $this->jestemwkolejceAction($IdUser);
        $jestemnaprzerwie = $this->jestemnaprzerwieAction($IdUser);

        //  ------------------ GRAFIK ---------------

        if ($jestemwkolejce) {

            if ($ostatniklucz - $kluczkonsultanta > 1 && $limitprzerw - $iloscnaprzerwie > $kluczkonsultanta) {

                return $this->render('InfogoldKonsultantBundle:Konsultant:przerwy.html.twig', array(
                            'raport' => $raport,
                            'czasprzerw' => $czasprzerw,
                            'sekund' => $suma,
                            'przerwaid' => $IdUser,
                            'limit' => $limitprzerw,
                            'kolejka' => $kolejka,
                            'przerwa' => $przerwa,
                            'przepusc' => true,
                            'usunzkolejki' => true,
                ));
            } else if ($ostatniklucz - $kluczkonsultanta > 1 && $limitprzerw - $iloscnaprzerwie <= $kluczkonsultanta) {
                return $this->render('InfogoldKonsultantBundle:Konsultant:przerwy.html.twig', array(
                            'raport' => $raport,
                            'czasprzerw' => $czasprzerw,
                            'sekund' => $suma,
                            'limit' => $limitprzerw,
                            'kolejka' => $kolejka,
                            'przerwa' => $przerwa,
                            'przepusc' => true,
                            'usunzkolejki' => true,
                ));
            } else if ($limitprzerw - $iloscnaprzerwie > $kluczkonsultanta) {
                return $this->render('InfogoldKonsultantBundle:Konsultant:przerwy.html.twig', array(
                            'raport' => $raport,
                            'sekund' => $suma,
                            'czasprzerw' => $czasprzerw,
                            'przerwaid' => $IdUser,
                            'limit' => $limitprzerw,
                            'kolejka' => $kolejka,
                            'przerwa' => $przerwa,
                            'usunzkolejki' => true,
                ));
            } else {
                return $this->render('InfogoldKonsultantBundle:Konsultant:przerwy.html.twig', array(
                            'raport' => $raport,
                            'sekund' => $suma,
                            'czasprzerw' => $czasprzerw,
                            'limit' => $limitprzerw,
                            'kolejka' => $kolejka,
                            'przerwa' => $przerwa,
                            'usunzkolejki' => true,
                ));
            }
        } else if ($jestemnaprzerwie) {
            return $this->render('InfogoldKonsultantBundle:Konsultant:przerwy.html.twig', array(
                        'raport' => $raport,
                        'sekund' => $suma,
                        'czasprzerw' => $czasprzerw,
                        'usunprzerwy' => $IdUser,
                        'limit' => $limitprzerw,
                        'kolejka' => $kolejka,
                        'przerwa' => $przerwa,
            ));
        } else {
            return $this->render('InfogoldKonsultantBundle:Konsultant:przerwy.html.twig', array(
                        'raport' => $raport,
                        'sekund' => $suma,
                        'czasprzerw' => $czasprzerw,
                        'kolejkaid' => $IdUser,
                        'limit' => $limitprzerw,
                        'kolejka' => $kolejka,
                        'przerwa' => $przerwa,
            ));
        }

        return $this->render('InfogoldKonsultantBundle:Konsultant:przerwy.html.twig', array(
                    'limit' => $limitprzerw,
        ));
    }

    public function aktualnakolejkaAction($dzialkonsultanta) {

        $em1 = $this->getDoctrine()->getManager();
        $request1 = $em1->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb1 = $request1->createQueryBuilder('p');
        $query1 = $qb1
                ->leftJoin('p.kolejka', 'c')
                ->where('c.KolejkaDzialu=' . $dzialkonsultanta)
                ->orderBy('c.created', 'ASC')
                ->getQuery();

        $kolejka = $query1->getResult();

        return $kolejka;
    }

    public function kluczkonsultantaAction() {

        $User = $this->get('security.context')->getToken()->getUser();

        $dzialkonsultanta = $User->getKonsultantDzialy()->getId();

        $em1 = $this->getDoctrine()->getManager();
        $request1 = $em1->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb1 = $request1->createQueryBuilder('p');
        $query1 = $qb1
                ->leftJoin('p.kolejka', 'c')
                ->where('c.KolejkaDzialu=' . $dzialkonsultanta)
                ->orderBy('c.created', 'ASC')
                ->getQuery();

        $kolejka = $query1->getResult();
        $klucz = new ArrayCollection($kolejka);
        $mojklucz = $klucz->indexOf($User);
        return $mojklucz;
    }

    public function aktualnaprzerwaAction($dzialkonsultanta) {

        $em2 = $this->getDoctrine()->getManager();
        $request2 = $em2->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb2 = $request2->createQueryBuilder('p');
        $query2 = $qb2
                ->leftJoin('p.przerwa', 'c')
                ->where('c.PrzerwaDzialu=' . $dzialkonsultanta)
                ->orderBy('c.id', 'ASC')
                ->getQuery();

        $przerwa = $query2->getResult();

        return $przerwa;
    }

    public function jestemwkolejceAction($IdUser) {
        // sprawdzenie czy jest się w kolejce na przerwę true false
        $em = $this->getDoctrine()->getManager();
        $request = $em->getRepository('InfogoldKonsultantBundle:kolejki');
        $qb = $request->createQueryBuilder('p');
        $query = $qb
                ->leftJoin('p.KonsultantKolejka', 'c')
                ->where('c=' . $IdUser)
                ->getQuery();


        $jestemwkolejce = $query->getResult();

        return $jestemwkolejce;
    }

    public function jestemnaprzerwieAction($IdUser) {
        // sprawdzenie czy jest się w kolejce na przerwę true false
        $em3 = $this->getDoctrine()->getManager();
        $request3 = $em3->getRepository('InfogoldKonsultantBundle:przerwy');
        $qb3 = $request3->createQueryBuilder('p');
        $query3 = $qb3
                ->leftJoin('p.KonsultantPrzerwa', 'c')
                ->where('c=' . $IdUser)
                ->getQuery();


        $jestemnaprzerwie = $query3->getResult();

        return $jestemnaprzerwie;
    }

    public function nakolejkeAction() {

        $User = $this->get('security.context')->getToken()->getUser();


        $kol = new Kolejki();
        $kol->setKonsultantKolejka($User);
        $kol->setKolejkaDzialu($User->getKonsultantDzialy());
        $em = $this->getDoctrine()->getManager();
        $em->persist($kol);
        $em->flush();

        return $this->redirect($this->generateUrl('przerwy'));
    }

    public function usunzkolejkiAction() {

        $User = $this->get('security.context')->getToken()->getUser();
        $IdUser = $User->getId();


        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('InfogoldKonsultantBundle:kolejki');
        $konsultant = $repository->findOneBy(array('KonsultantKolejka' => $IdUser));

        $em->remove($konsultant);
        $em->flush();

        return $this->redirect($this->generateUrl('przerwy'));
    }

    public function naprzerweAction() {


        $User = $this->get('security.context')->getToken()->getUser();


        $kol = new Przerwy();
        $kol->setKonsultantPrzerwa($User);
        $kol->setPrzerwaDzialu($User->getKonsultantDzialy());
        $kol->setCzasrozpoczecia(new DateTime('now'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($kol);
        $em->flush();

        return $this->usunzkolejkiAction();
    }

    public function usunzprzerwyAction() {

        $User = $this->get('security.context')->getToken()->getUser();
        $IdUser = $User->getId();


        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('InfogoldKonsultantBundle:przerwy');
        $konsultant = $repository->findOneBy(array('KonsultantPrzerwa' => $IdUser));
        $czasrozpoczecia = $konsultant->getCzasrozpoczecia();
        $teraz = new DateTime('now');

        $from_time = strtotime($czasrozpoczecia->format("Y-m-d H:i:s"));
        $to_time = strtotime($teraz->format("Y-m-d H:i:s"));
        $sekundy = (integer) (abs($to_time - $from_time));
        $czas = gmdate("H:i:s", $sekundy);
        $raportprzerw = new RaportyPrzerw();
        $raportprzerw->setCzasrozpoczecia($czasrozpoczecia);
        $raportprzerw->setCzaszakonczenia(new DateTime('now'));
        $raportprzerw->setData(new DateTime('today'));
        $raportprzerw->setCzasprzerwy(new DateTime($czas));
        $raportprzerw->setKonsultantRaportPrzerw($User);
        $er = $this->getDoctrine()->getManager();
        $er->persist($raportprzerw);
        $er->flush();

        $em->remove($konsultant);
        $em->flush();




        return $this->redirect($this->generateUrl('przerwy'));
    }

    public function przepuscAction() {

        $User = $this->get('security.context')->getToken()->getUser();
        $IdUser = $User->getId();

        $dzialkonsultanta = $User->getKonsultantDzialy()->getId();
        $em = $this->getDoctrine()->getManager();
        $request = $em->getRepository('InfogoldKonsultantBundle:kolejki');
        $qb = $request->createQueryBuilder('p');
        $query = $qb
                ->leftJoin('p.KolejkaDzialu', 'c')
                ->where('c=' . $dzialkonsultanta)
                ->orderBy('p.created', 'ASC')
                ->getQuery();

        $kolejka = $query->getResult();
        if ($kolejka) {
            $em2 = $this->getDoctrine()->getManager();
            $request2 = $em2->getRepository('InfogoldKonsultantBundle:kolejki');
            $qb = $request2->createQueryBuilder('p');
            $query2 = $qb
                    ->leftJoin('p.KonsultantKolejka', 'c')
                    ->where('c=' . $IdUser)
                    ->getQuery();
            $jestemwkolejce = $query2->getOneOrNullResult();
            $czasdodania = $jestemwkolejce->getCreated();

            foreach ($kolejka as $key => $line) {

                if ($line->getKonsultantKolejka()->getId() == $IdUser) {


                    $jestemwkolejce->setCreated($kolejka[$key + 1]->getCreated());
                    $kolejka[$key + 1]->setCreated($czasdodania);
                }
            }
            $em2->flush();
        }
        return $this->redirect($this->generateUrl('przerwy'));
    }

    public function iloscnaprzerwie() {
        $User = $this->get('security.context')->getToken()->getUser();
        $dzialkonsultanta = $User->getKonsultantDzialy()->getId();
        $entities1 = aktualnaprzerwaAction($dzialkonsultanta);
        $przerwa = count($entities1);
        return $przerwa;
    }

    public function ajaxprzerwaAction() {
        $User = $this->get('security.context')->getToken()->getUser();
        $IdUser = $User->getId();

        $dzialkonsultanta = $User->getKonsultantDzialy()->getId();
        $limitprzerw = $User->getKonsultantDzialy()->getLimityprzerw();

        $przerwa = $this->aktualnaprzerwaAction($dzialkonsultanta);
        $iloscnaprzerwie = count($przerwa);

        $kluczkonsultanta = $this->kluczkonsultantaAction();
        $jestemwkolejce = $this->jestemwkolejceAction($IdUser);
        if ($jestemwkolejce) {
            if ($limitprzerw - $iloscnaprzerwie > $kluczkonsultanta) {
                return new Response('ok');
            } else {
                return new Response('no');
            }
        }
        return new Response('no');
    }

    #AJAX
}
