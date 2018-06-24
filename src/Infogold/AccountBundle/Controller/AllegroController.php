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
    public function indexAction() {
                            
        $wynik = $this->get("my.allegro.login")->SessionAllegro();
        
        if(isset($wynik['allegro'])){
            
            $id= $wynik['allegro']; 
            
            return $this->redirectToRoute('allegro_edit',array('id'=>$id,
                $this->get('session')->getFlashBag()->add($wynik['alert'], $wynik['message'])));
        }
        if(isset($wynik['login'])){
           return $this->redirectToRoute('allegro_new',array('login'=>$wynik['login']));           
        }
         if(isset($wynik['disable'])){
            return $this->render('InfogoldAccountBundle:Allegro:index.html.twig',array(
                $this->get('session')->getFlashBag()->add('error', $wynik['disable'])));         
        }

        return $this->render('InfogoldAccountBundle:Allegro:index.html.twig');
    }

    public function newAction(Request $request) {
              
        $allegro = new Allegro();
        $form = $this->createForm('Infogold\AccountBundle\Form\AllegroType', $allegro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $loggedUser = $this->get('my.main.admin')->getMainAdmin();
            $encryption_key = '$eRw!$';
            $secret_iv = substr(md5(microtime()),rand(0,26),20);            
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $haslo = $form->get('PasswordAllegro')->getData();
            $pass = openssl_encrypt($haslo, 'aes-128-cfb', $encryption_key, 0, $iv);
            
            $allegro->setPasswordAllegro($pass);
            $allegro->setIv($iv);
            $allegro->setUserAllegro($loggedUser);
            $em = $this->getDoctrine()->getManager();
            $em->persist($allegro);
            $em->flush();

            return $this->redirectToRoute('allegro_index');
        }
        return $this->render('InfogoldAccountBundle:Allegro:new.html.twig', array('form'=>  $form->createView()));
    }
    
   public function listAction() {
       
       $user = $this->get('my.main.admin')->getMainAdmin();
       
        $this->get("my.allegro.login")->SessionAllegro();
        $err = null;
        $items = null;
        $session = new Session();
        $id = $session->get('sessionHandlePart');
      //  var_dump($id);
       // exit();
             try {
                $client = new \SoapClient(LINK);
                /*
                $items = $client->doGetCatsData(array(
                    'countryId' => 1,                 
                    'webapiKey' => $user->getAllegro()->getAllegroKeyWebApi(),
                     'onlyLeaf'=> true
                 ));
                */
                
                 $items = $client->doGetSellFormFieldsForCategory(array(
                 'webapiKey' => $user->getAllegro()->getAllegroKeyWebApi(), 'countryId' => 1,'categoryId' => 486 )); 
                 echo '<pre>';
             var_export($items);
             echo '</pre>';
             exit();
                //$test = $this->get("my.allegro.login")->LoginToAllegro();
            } catch (\SoapFault $error){
               /* var_dump($error);
                exit();
                */
                $err = $this->get('session')->getFlashBag()->add('error', "błąd wyświtlenia listy wystawionych aukcji: $error->faultstring");               
            }
        
        return $this->render('InfogoldAccountBundle:Allegro:list.html.twig', array(
                    'list' => $items, $err       
                                  
        ));
    } 

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
              $encryption_key = '$eRw!$';
            $ivlen = openssl_cipher_iv_length('aes-128-cfb');
            $iv = openssl_random_pseudo_bytes($ivlen); 
             $haslo = $editForm->get('PasswordAllegro')->getData();
            $pass = openssl_encrypt($haslo, 'aes-128-cfb', $encryption_key, 0, $iv);          
            $allegro->setPasswordAllegro($pass);
            $allegro->setIv($iv);                        
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('allegro_index');
        }
        
        return $this->render('InfogoldAccountBundle:Allegro:edit.html.twig', array(
                    'id' => $allegro->getId(),
                    'edit_form' => $editForm->createView()      
        ));
    }
    

   

}
