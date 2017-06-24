<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Infogold\AccountBundle\Entity\RaportyPrzerw;

class PrzerwyController extends Controller {

    public function indexAction(Request $request) {

        $userdzialy = $this->get('my.main.admin')->getMainAdmin()->getDzialy();
        $nazwydzialow = $userdzialy->getValues();
        $pierwszydzial = $userdzialy->first()->getId();

        $kolejka = $this->aktualnakolejkaAction($pierwszydzial);
        $przerwa = $this->aktualnaprzerwaAction($pierwszydzial);

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($pierwszydzial);

        $form = $this->createFormBuilder($entities)
                ->add('limityprzerw', 'choice', array(
                    'choices' => array(
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                        6 => 6,
                    ),
                    'required' => false
                ))
                ->getForm();
        
        
           if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $data = $form->getData();

                $em->persist($data);
                $em->flush();

                $response = array(
                    'id' => $pierwszydzial,
                );


                return $this->redirect($this->generateUrl('dzialy_przerwy', $response));
            }
        }


        $limity = $userdzialy->first()->getLimityprzerw();

        return $this->render('InfogoldAccountBundle:Przerwy:przerwy.html.twig', array(
                    'limity' => $limity,
                    'kolejka' => $kolejka,
                    'przerwa' => $przerwa,
                    'nazwydzialow' => $nazwydzialow,
                    'id' => $pierwszydzial,
                    'form' => $form->createView()));
    }

    public function dzialAction(Request $request, $id) {

        $userdzialy = $this->get('my.main.admin')->getMainAdmin()->getDzialy();
        $nazwydzialow = $userdzialy->getValues();

        $kolejka = $this->aktualnakolejkaAction($id);
        $przerwa = $this->aktualnaprzerwaAction($id);

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($id);

        $form = $this->createFormBuilder($entities)
                ->add('limityprzerw', 'choice', array(
                    'choices' => array(
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                        6 => 6,
                    ),
                    'required' => false
                ))
                ->getForm();

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {

                $data = $form->getData();

                $em->persist($data);
                $em->flush();

                $response = array(
                    'id' => $id,
                );


                return $this->redirect($this->generateUrl('dzialy_przerwy', $response));
            }
        }
        $dzial = $em->getRepository('InfogoldAccountBundle:Dzialy')->find($id);
        $limity = $dzial->getLimityprzerw();

        return $this->render('InfogoldAccountBundle:Przerwy:przerwy.html.twig', array(
                    'limity' => $limity,
                    'id' => $id,
                    'kolejka' => $kolejka,
                    'przerwa' => $przerwa,
                    'nazwydzialow' => $nazwydzialow,
                    'form' => $form->createView()));
    }

    public function usunzkolejkiAction($id, $kolusun) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('InfogoldKonsultantBundle:kolejki');
        $konsultant = $repository->findOneBy(array('KonsultantKolejka' => $kolusun));

        $em->remove($konsultant);
        $em->flush();

        $response = array(
            'id' => $id,
        );


        return $this->redirect($this->generateUrl('dzialy_przerwy', $response));
    }
    
       public function usunzprzerwyAction($id, $przerusun) {

        $em = $this->getDoctrine()->getManager();
       
        $repository = $em->getRepository('InfogoldKonsultantBundle:przerwy');
        $konsultant = $repository->findOneBy(array('KonsultantPrzerwa' => $przerusun));
        $czasrozpoczecia = $konsultant->getCzasrozpoczecia();
        
        
        
        
        $em->remove($konsultant);
        $em->flush();
        

        $response = array(
            'id' => $id,
        );


        return $this->redirect($this->generateUrl('dzialy_przerwy', $response));
    }

    public function aktualnakolejkaAction($id) {
        $em1 = $this->getDoctrine()->getManager();
        $request1 = $em1->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb1 = $request1->createQueryBuilder('p');
        $query1 = $qb1
                ->leftJoin('p.kolejka', 'c')
                ->where('c.KolejkaDzialu=' . $id)
                ->getQuery();

        $kolejka = $query1->getResult();

        return $kolejka;
    }

    public function aktualnaprzerwaAction($id) {

        $em2 = $this->getDoctrine()->getManager();
        $request2 = $em2->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb2 = $request2->createQueryBuilder('p');
        $query2 = $qb2
                ->leftJoin('p.przerwa', 'c')
                ->where('c.PrzerwaDzialu=' . $id)
                ->getQuery();

        $przerwa = $query2->getResult();

        return $przerwa;
    }
    
   
}
