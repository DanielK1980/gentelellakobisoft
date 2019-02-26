<?php

namespace Infogold\AccountBundle\Controller;

use Infogold\AccountBundle\Entity\Allegro;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Infogold\AccountBundle\Form\AllegroType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Allegro controller.
 *
 */
class AllegroController extends Controller {

    /**
     * Lists all allegro entities.
     *
     */
    public function indexAction(Request $request) {

        //  $wynik = $this->get("my.allegro.login")->SessionAllegro();
        $loggedUser = $this->get('my.main.admin')->getMainAdmin();

        if ($loggedUser->getEnableAllegro()) {

            $em = $this->getDoctrine()->getManager();
            $allegro = $em->getRepository('InfogoldAccountBundle:Allegro')->findOneBy(array('UserAllegro' => $loggedUser->getId()));

            if ($allegro) {
                $form = $this->createForm(new AllegroType(true), $allegro);

                return $this->render('InfogoldAccountBundle:Allegro:index.html.twig', array(
                            'edit_form' => $form->createView(),
                            'id' => $allegro->getId()
                ));
            } else {
                $allegro = new Allegro();
                $form = $this->createForm('Infogold\AccountBundle\Form\AllegroType', $allegro);
                return $this->render('InfogoldAccountBundle:Allegro:index.html.twig', array(
                            'form' => $form->createView()));
            }
        } else {
            return $this->render('InfogoldAccountBundle:Allegro:index.html.twig', array(
                        $this->get('session')->getFlashBag()->add('error', 'Brak modułu allegro')));
        }
    }
    
   

    public function newAction(Request $request) {

        $allegro = new Allegro();
        $form = $this->createForm('Infogold\AccountBundle\Form\AllegroType', $allegro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $loggedUser = $this->get('my.main.admin')->getMainAdmin();
            $allegro->setUserAllegro($loggedUser);
            $em = $this->getDoctrine()->getManager();
            $em->persist($allegro);
            $em->flush();

            return $this->redirectToRoute('allegro_index');
        }
        return $this->redirectToRoute('allegro_index');
    }
    
    public function getTokenAction() {
        
    }
    
    public function refreshTokenAction() {
        
    }
/*
    public function listAction() {

        $user = $this->get('my.main.admin')->getMainAdmin();

        $this->get("my.allegro.login")->SessionAllegro();
        $err = null;
        $items = null;
        $session = new Session();
        $id = $session->get('sessionHandlePart');
;
        try {
            $client = new \SoapClient(LINK);


            $items = $client->doGetSellFormFieldsForCategory(array(
                'webapiKey' => $user->getAllegro()->getAllegroKeyWebApi(), 'countryId' => 1, 'categoryId' => 486));
            echo '<pre>';
            var_export($items);
            echo '</pre>';
            exit();
            //$test = $this->get("my.allegro.login")->LoginToAllegro();
        } catch (\SoapFault $error) {

            $err = $this->get('session')->getFlashBag()->add('error', "błąd wyświtlenia listy wystawionych aukcji: $error->faultstring");
        }

        return $this->render('InfogoldAccountBundle:Allegro:list.html.twig', array(
                    'list' => $items, $err
        ));
    }
    */
    
    

    public function editAction(Request $request, $id) {

        // var_dump($alert);
        //  exit();     
        //$deleteForm = $this->createDeleteForm($allegro);
        $em = $this->getDoctrine()->getManager();

        $allegro = $em->getRepository('InfogoldAccountBundle:Allegro')->find($id);

        if (!$allegro) {
            throw $this->createNotFoundException('Unable to find Allegro entity.');
        }

        $editForm = $this->createForm(new AllegroType(true), $allegro);

        return $this->render('InfogoldAccountBundle:Allegro:edit.html.twig', array(
                    'id' => $allegro->getId(),
                    'edit_form' => $editForm->createView()
        ));
    }

    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $allegro = $em->getRepository('InfogoldAccountBundle:Allegro')->find($id);

        if (!$allegro) {
            throw $this->createNotFoundException('Unable to find Allegro entity.');
        }

        $editForm = $this->createForm(new AllegroType(true), $allegro);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
           
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('allegro_index');
        }

        return $this->redirectToRoute('allegro_index');
    }

}
