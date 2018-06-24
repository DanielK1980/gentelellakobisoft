<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\KonsultantBundle\Entity\Konsultant;
use Infogold\AccountBundle\Form\KonsultantType;
use DateTime;

/**
 * Konsultant controller.
 *
 */
class KonsultantController extends Controller {

    /**
     * Lists all Konsultant entities.
     *
     */
    public function indexAction(Request $request) {

        $User = $this->get('my.main.admin')->getMainAdmin();
        $userId = $User->getNrklienta();



        $em = $this->getDoctrine()->getManager();
        $req = $em->getRepository('InfogoldKonsultantBundle:Konsultant');

        $qb = $req->createQueryBuilder('p');
        $entities = $qb
                ->leftJoin('p.firma', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->getQuery();


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $entities, $request->query->get('page', 1)/* page number */, 15 /* limit per page */);

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


        return $this->render('InfogoldKonsultantBundle:Konsultant:index.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView()
        ));
    }

    /**
     * Creates a new Konsultant entity.
     *
     */
    public function createAction(Request $request, $date) {

        $em = $this->getDoctrine()->getManager();
        $rola = $em->getRepository('InfogoldKonsultantBundle:Role')->find(1);

        $user = $this->get('my.main.admin')->getMainAdmin();

        $entity = new Konsultant();
        $form = $this->createForm(new KonsultantType($user->getNrklienta()), $entity);
        $form->bind($request);


        $entity->setFirma($user);

        $pass = 'konsultant' . rand(1000, 9999);
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($entity);
        $password = $encoder->encodePassword($pass, $entity->getSalt());
        $entity->setIsActive(false);
        $entity->setPassword($password);
        $entity->setUpdatePassword(new \DateTime('now'));
        $entity->setKonsultantRoles($rola);
        $entity->setOldpassword($entity->getPassword());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            $message = (new \Swift_Message())
                    ->setSubject('Dodanie nowego konsultanta na kobisoft.pl')                  
                    ->setFrom('testowymdk@gmail.com')
                    ->setTo($entity->getEmail())                 
                    ->setSender('testowymdk@gmail.com')                  
                    ->setBody('
                        Witaj ' . $entity->getImie() . '<br><br>
                            Dodano nowego konsultanta na kobisoft.pl<br>
                            Prosimy się zalogować <br>
                              LOGIN: <b>' . $entity->getUsername() . '<b><br>
                              HASŁO: <b>' . $pass . '</b>
                              <br><br>
                              Pozdrawiamy<br>
                              Zespół kobisoft.pl', 'text/html');
            $this->get('mailer')->send($message);

            
            /*
             *  $message = (new \Swift_Message('Hello Email'))
        ->setFrom($this->container->getParameter('mailer_user'))
        ->setTo($entity->getEmail())
        ->setBody('Zmieniłeś hasło dla loginu ' . $entity->getUsername() . '.<br/>Twoje nowe hasło to ' . $Password, 'text/html');
             * 
             * 
             */
            
            return $this->redirect($this->generateUrl('konsultant_show', array('id' => $entity->getId(), 'date' => $date)));
        }

        return $this->render('InfogoldKonsultantBundle:Konsultant:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Konsultant entity.
     *
     */
    public function newAction() {

        $User = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $User->getNrklienta();

        $entity = new Konsultant();

        $form = $this->createForm(new KonsultantType($nrklienta), $entity);

        return $this->render('InfogoldKonsultantBundle:Konsultant:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Konsultant entity.
     *
     */
    public function showAction($id, $date) {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Konsultant entity.');
        }

        $grafik = $this->grafik($id, $date);


        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldKonsultantBundle:Konsultant:show.html.twig', array(
                    'konsultant' => $grafik['konsultant'],
                    'rok' => $grafik['rok'],
                    'miesiac' => $grafik['miesiac'],
                    'nazwydni' => $grafik['nazwydni'],
                    'ilosc' => $grafik['ilosc'],
                    'pustedni' => $grafik['pustedni'],
                    'nazwamiesiaca' => $grafik['nazwamiesiaca'],
                    'grafiktabela' => $grafik['grafiktabela'],
                    'czaspracy' => $grafik['czaspracy'],
                    'id' => $id,
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing Konsultant entity.
     *
     */
    public function editAction($id) {

        $User = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $User->getNrklienta();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Konsultant entity.');
        }

        $editForm = $this->createForm(new KonsultantType($nrklienta, true), $entity);

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldKonsultantBundle:Konsultant:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Konsultant entity.
     *
     */
    public function updateAction(Request $request, $id, $date) {

        $User = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $User->getNrklienta();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);
        $haslo = $entity->getPassword();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Konsultant entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new KonsultantType($nrklienta, true), $entity);


        $editForm->bind($request);

        $entity->setPassword($haslo);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('konsultant_show', array('id' => $id, 'date' => $date)));
        }

        return $this->render('InfogoldKonsultantBundle:Konsultant:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Konsultant entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        // 
        //  wszystkie kontakty klientów ustawiamy na null, grafik usuwamy $entity->setKlientKonsultanta(null);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Konsultant entity.');
            }

            $entity2 = $em->getRepository('InfogoldKlienciBundle:Klienci');
            $qb = $entity2->createQueryBuilder('p');
            $query2 = $qb
                    ->select('p')
                    ->leftJoin('p.KlientKonsultanta', 'c')
                    ->where('c=' . $id)
                    ->getQuery();
            $klienci = $query2->getResult();
            foreach ($klienci as $value) {
                $value->setKlientKonsultanta(null);
            }


            $entity3 = $em->getRepository('InfogoldKlienciBundle:Kontakty');
            $qb2 = $entity3->createQueryBuilder('p');
            $query3 = $qb2
                    ->select('p')
                    ->leftJoin('p.PrzypisanyDo', 'c')
                    ->where('c=' . $id)
                    ->getQuery();
            $kontakty = $query3->getResult();
            foreach ($kontakty as $value) {
                $value->setPrzypisanyDo(null);
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('konsultant'));
    }

    /**
     * Creates a form to delete a Konsultant entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('konsultant_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Usuń Konsultanta', 'attr' => array('class' => 'btn btn-danger', 'widget_col' => 4, 'col_size' => 'md')))
                        ->getForm()
        ;
    }

    public function ResethaslaAction($id, $date) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);
        $Password = 'konsultant' . rand(1000, 9999);

        //encode the password   
        $encoder = $this->get('security.encoder_factory')->getEncoder($entity); //get encoder for hashing pwd later
        $tempPassword = $encoder->encodePassword($Password, $entity->getSalt());
        $entity->setPassword($tempPassword);
        $entity->setOldPassword($tempPassword);
        $entity->setisActive(false);
        $em->flush()
                ;
        
      
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom($this->container->getParameter('mailer_user'))
        ->setTo($entity->getEmail())
        ->setBody('Zmieniłeś hasło dla loginu ' . $entity->getUsername() . '.<br/>Twoje nowe hasło to ' . $Password, 'text/html');
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'Emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
    

    //$this->get('mailer')->send($message);
        /*
        
        $transport = new \Swift_SmtpTransport('danielk1980.nazwa.pl', 465);
                    
        $message = \Swift_Message($transport)
                ->setSubject('Hello Email')
                ->setFrom($this->container->getParameter('mailer_user'))
                ->setTo($entity->getEmail())
                ->setBody('Zmieniłeś hasło dla loginu ' . $entity->getUsername() . '.<br/>Twoje nowe hasło to ' . $Password);
        */
        /*
        $this_is = 'this is';
    $the_message = ' the message of the email';
    $mailer = $this->get('mailer');

    $message = \Swift_Message::newInstance()
        ->setSubject('The Subject for this Message')
        ->setFrom($this->container->getParameter('mailer_user'))
        ->setTo('any_account_name@any_domain.whatever')
        ->setBody($this->renderView('default/email.html.twig', ['this'=>$this_is, 'message'=>$the_message]))
    ;
    $mailer->send($message);
        */
        
        
        $this->get('mailer')->send($message);

        return $this->redirect($this->generateUrl('konsultant_show', array('id' => $id, 'date' => $date, $this->get('session')->getFlashBag()->add('info', 'Wysłano e-mail z nowym hasłem do konultanta'))));
    }
    private function strftimeV($format, $timestamp) {
            return iconv("ISO-8859-2", "UTF-8", ucfirst(strftime($format, $timestamp)));
        }

    public function grafik($id, $date) {

        $em = $this->getDoctrine()->getManager();
        $konsultant = $em->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);


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
         $monthName = $this->strftimeV("%B", $firstDayOfMonth)." ".$this->strftimeV("%G", $firstDayOfMonth) ;

        // What is the index value (0-6) of the first day of the
        // month in question.
        $dayOfWeek = $dateComponents['wday'];

        $nazwydniarray = array_merge($daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek, $daysOfWeek);
        $nazwydni = array_slice($nazwydniarray, $dayOfWeek, $numberDays);

        $em2 = $this->getDoctrine()->getManager();

        $request1 = $em2->getRepository('InfogoldAccountBundle:grafik');
        $qb1 = $request1->createQueryBuilder('p');
        $query1 = $qb1
                ->leftJoin('p.GrafikKonsultanta', 'c')
                ->andwhere('c.id= :konsultantid')
                ->setParameter('konsultantid', $id)
                ->getQuery();


        $grafiktabela = $query1->getResult();

        $em3 = $this->getDoctrine()->getManager();
        $request2 = $em3->getRepository('InfogoldKonsultantBundle:czaspracy');
        $qb2 = $request2->createQueryBuilder('p');
        $query2 = $qb2
                ->leftJoin('p.KonsultantaCzasy', 'c')
                ->andwhere('c.id= :konsultantid')
                ->setParameter('konsultantid', $id)
                ->getQuery();


        $czaspracy = $query2->getResult();

        return array(
            'konsultant' => $konsultant,
            'rok' => $rok,
            'miesiac' => $miesiac,
            'nazwydni' => $nazwydni,
            'ilosc' => $numberDays,
            'pustedni' => $dayOfWeek,
            'nazwamiesiaca' => $monthName,
            'grafiktabela' => $grafiktabela,
            'id' => $id,
            'czaspracy' => $czaspracy
        );
    }

    public function raportAction(Request $request) {


        if ($request->getMethod() == 'POST') {

            $date = $request->get('date');
            $day = new DateTime($date);
            $id = $request->get('id');


            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('InfogoldKonsultantBundle:CzasPracy');
            $q = $repository->createQueryBuilder('p')
                    ->where('SUBSTRING(p.zalogowanie,1,10)= :zalogowanie')
                    ->leftJoin('p.KonsultantaCzasy', 'c')
                    ->andwhere('c.id= :konsultantid')
                    ->setParameter('konsultantid', $id)
                    ->setParameter('zalogowanie', $day->format("Y-m-d"))
                    ->getQuery();

            $konsultantczaspracy = $q->getArrayResult();

            $em4 = $this->getDoctrine()->getManager();
            $request4 = $em4->getRepository('InfogoldKonsultantBundle:Konsultant')->find($id);
            $konsultantlogin = $request4->getUsername();

            $sekundy = 0;
            foreach ($konsultantczaspracy as $czaspracy) {
                $from_time = strtotime($czaspracy["zalogowanie"]->format("Y-m-d H:i:s"));
                $to_time = strtotime($czaspracy["wylogowanie"]->format("Y-m-d H:i:s"));
                $sekundy = $sekundy + (integer) (abs($to_time - $from_time));
            }
            $czas = gmdate("H:i:s", $sekundy);

            $em5 = $this->getDoctrine()->getManager();

            $repository5 = $em5->getRepository('InfogoldAccountBundle:RaportyPrzerw');
            $q5 = $repository5->createQueryBuilder('p')
                    ->where('p.data= :data')
                    ->leftJoin('p.KonsultantRaportPrzerw', 'c')
                    ->andwhere('c.id= :konsultantid')
                    ->setParameter('konsultantid', $id)
                    ->setParameter('data', $day->format("Y-m-d"))
                    ->getQuery();

            $konsultantaprzerwy = $q5->getArrayResult();

            $sekundyprzerw = 0;
            foreach ($konsultantaprzerwy as $czaspzerw) {
                $start_break = strtotime($czaspzerw["czasrozpoczecia"]->format("Y-m-d H:i:s"));
                $end_break = strtotime($czaspzerw["czaszakonczenia"]->format("Y-m-d H:i:s"));
                $sekundyprzerw = $sekundyprzerw + (integer) (abs($end_break - $start_break));
            }
            $czasprzerw = gmdate("H:i:s", $sekundyprzerw);

            $em2 = $this->getDoctrine()->getManager();
            $repository2 = $em2->getRepository('InfogoldKlienciBundle:Klienci');
            $q2 = $repository2->createQueryBuilder('p')
                    ->where('SUBSTRING(p.created,1,10)= :datautworzenia')
                    ->leftJoin('p.KlientKonsultanta', 'c')
                    ->andwhere('c.username= :utworzonyprzezlogin')
                    ->setParameter('utworzonyprzezlogin', $konsultantlogin)
                    ->setParameter('datautworzenia', $day->format("Y-m-d"))
                    ->getQuery();
            $konsultantklienci = $q2->getArrayResult();

            $em3 = $this->getDoctrine()->getManager();
            $repository3 = $em3->getRepository('InfogoldKlienciBundle:Kontakty');
            $q3 = $repository3->createQueryBuilder('p')
                    ->select('p.opiskontaktu, c.id, c.nazwaklienta, c.imie, c.nazwisko')
                    ->where('SUBSTRING(p.created,1,10)= :datautworzenia')
                    ->leftJoin('p.Klient', 'c')
                    ->andwhere('p.utworzonyprzez= :utworzonyprzezlogin')
                    ->setParameter('utworzonyprzezlogin', $konsultantlogin)
                    ->setParameter('datautworzenia', $day->format("Y-m-d"))
                    ->getQuery();
            $konsultantkontakty = $q3->getArrayResult();

            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            return $response->setContent(json_encode(array(
                        'date' => $date,
                        'id' => $id,
                        'konsultantczaspracy' => $konsultantczaspracy,
                        'konsultantkontakty' => $konsultantkontakty,
                        'konsultantklienci' => $konsultantklienci,
                        'przepracowal' => new DateTime($czas)
                       // 'konsultantaprzerwy' => $konsultantaprzerwy
                       // 'czasprzerw' => new DateTime($czasprzerw)
            )));
        }
    }

}
