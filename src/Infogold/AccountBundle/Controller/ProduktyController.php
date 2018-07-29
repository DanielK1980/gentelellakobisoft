<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\AccountBundle\Entity\Produkt;
use Infogold\AccountBundle\Form\ProduktyType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Produkty controller.
 *
 */
class ProduktyController extends Controller {

    /**
     * Lists all Produkty entities.
     *
     */
    public function indexAction(Request $request) {



        $User = $this->get('my.main.admin')->getMainAdmin();
        $userId = $User->getNrklienta();
        $nrklienta = $User->getNrklienta();

        $ar = array(
            'nazwaproduktu' => 'nazwa produktu',
            'nrproduktu' => 'nr produktu',
            'ceny' => 'cena od'
                //'wmagazyniedo' => 'w magazynie do' // do poprawy
        );

        if ($User->getenableMagazyn() == true) {
            $ar['wmagazyniedo'] = "w magazynie do";
        }

        $form = $this->createFormBuilder()
                ->add('wedlug', 'choice', array(
                    'required' => false,
                    'choices' => $ar,
                    'multiple' => false,
                    'empty_value' => 'Wybierz'
                ))
                ->add('kategorie', 'entity', array(
                    'class' => 'InfogoldAccountBundle:Category',
                    'query_builder' => function(EntityRepository $er) use ($userId) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.company', 'c')
                                ->where('c.Nrklienta=' . $userId);
                    },
                    'required' => false,
                    'label' => false,
                    'empty_value' => 'Wybierz kategorie',
                    'attr' => ['data-select' => 'true']
                ))
                ->add('szukaj', 'text', array('required' => false))
                ->getForm();

        $em = $this->getDoctrine()->getManager();
        $req = $em->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $req->createQueryBuilder('p');
        $entities = $qb
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $nrklienta)
                ->getQuery();


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $entities, $request->query->get('page', 1)/* page number */, 15 /* limit per page */);
        return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView(),
                    'magazyn' => $User->getenableMagazyn()
        ));
    }

    /**
     * Creates a new Produkty entity.
     *
     */
    public function createAction(Request $request) {

        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $entity = new Produkt();
        $form = $this->createForm(new ProduktyType($nrklienta, $user->getenableMagazyn()), $entity);
        $form->bind($request);
        $cenabrutto = $form->get('cenaProduktu')->getData() + $form->get('cenaProduktu')->getData() * ( $form->get('vat')->getData() / 100);

        if ($form->isValid()) {
            $entity->setUserproduktu($user); // produkt firmy  $user
            $entity->setCenabrutto($cenabrutto);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('produkty_show', array('id' => $entity->getId())));
        }

        return $this->render('InfogoldAccountBundle:Produkty:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Produkty entity.
     *
     */
    public function newAction() {

        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $entity = new Produkt();
        $form = $this->createForm(new ProduktyType($nrklienta, $user->getenableMagazyn()), $entity);

        return $this->render('InfogoldAccountBundle:Produkty:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Produkty entity.
     *
     */
    public function getpkfAction(Request $request) {
        $entities2 = null;
        if ($request->getMethod() == 'POST') {
            $id = $request->get('id');
            if ($id) {
                $em2 = $this->getDoctrine()->getManager();
                $request = $em2->getRepository('InfogoldAccountBundle:Produkt');
                $qb = $request->createQueryBuilder('p');
                $query2 = $qb
                        ->select('c.nazwaklienta,c.nipklienta,d.cenaProduktu,d.ilosc,d.created,c.id')
                        ->leftJoin('p.produkt', 'd')
                        ->leftJoin('d.ProduktyKlienta', 'c')
                        ->where('p.id='.$id)
                        ->andWhere('c.nipklienta is not null')
                        ->setMaxResults(100)
                        ->getQuery();
                $entities2 = $query2->getResult();
                
                $indexedOnly = array();

                foreach ($entities2 as $row) {

                    if($row["created"]){
                        $myDate = $row['created'];
                        $row["created"] = $myDate->format('Y-m-d H:i');                     
                    }
                    if($row["id"]){                      
                        $row["id"] = "<a class='btn btn-default btn-sm'  href='/symfony2_8_new/web/app_dev.php/user/baza/".$row["id"]."/show'><span class='glyphicon glyphicon-eye-open'></span>";                  
                    }                   
                    $indexedOnly[] = array_values($row);             
                }
                
              //  var_export($indexedOnly);
              //  exit();
                
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
            }
        }
        return $response->setContent(json_encode(array(
                    'data' => $indexedOnly
        )));
    }

    public function getpkiAction(Request $request) {
        $entities2 = null;
        if ($request->getMethod() == 'POST') {
            $id = $request->get('id');
            if ($id) {
                $em2 = $this->getDoctrine()->getManager();
                $request = $em2->getRepository('InfogoldAccountBundle:Produkt');
                $qb = $request->createQueryBuilder('p');
                $query2 = $qb
                        ->select('c.imie,c.nazwisko,d.cenaProduktu,d.ilosc,d.created,c.id')
                        ->leftJoin('p.produkt', 'd')
                        ->leftJoin('d.ProduktyKlienta', 'c')
                        ->where('p.id='.$id)
                        ->andWhere('c.peselklienta is not null')
                        ->setMaxResults(100)
                        ->getQuery();
                $entities2 = $query2->getResult();
                
                $indexedOnly = array();

                foreach ($entities2 as $row) {

                    if($row["created"]){
                        $myDate = $row['created'];
                        $row["created"] = $myDate->format('Y-m-d H:i');                     
                    }
                    if($row["id"]){                      
                        $row["id"] = "<a class='btn btn-default btn-sm'  href='/symfony2_8_new/web/app_dev.php/user/baza/".$row["id"]."/show'><span class='glyphicon glyphicon-eye-open'></span>";                  
                    }                   
                    $indexedOnly[] = array_values($row);             
                }
 
                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
            }
        }
        return $response->setContent(json_encode(array(
                    'data' => $indexedOnly
        )));
    }

    public function showAction($id) {

        $user = $this->get('my.main.admin')->getMainAdmin();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);



        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Produkty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');
        $query2 = $qb
                ->select('p.id, c.id, c.imie, c.nazwisko, c.peselklienta, c.nazwaklienta, c.nipklienta, c.miasto, c.ulica, d.cenaProduktu,d.ilosc, d.created ')
                ->leftJoin('p.produkt', 'd')
                ->leftJoin('d.ProduktyKlienta', 'c')
                ->where('p.id=' . $id)
                ->getQuery();


        $entities2 = $query2->getResult();

        //Allegro

        if ($user->getAllegro()) {

            $datetimepicker = array();
            $datepicker = array();

            if ($entity->getCategory()->getItemCategoryIdAllegro()) {

                $buildFormAllegro = $this->createFormBuilder();

                $allegomodule = $this->get('my.allegro.module')->getModuleAllegro("doGetSellFormFieldsForCategory", array('categoryId' => $entity->getCategory()->getItemCategoryIdAllegro()));

                foreach ($allegomodule->sellFormFieldsForCategory->sellFormFieldsList->item as $itemform) {

                    if ($itemform->sellFormId > 2 &&
                            $itemform->sellFormId != 6 &&
                            $itemform->sellFormId != 7 &&
                            $itemform->sellFormId != 8 &&
                            $itemform->sellFormId != 9 &&
                            $itemform->sellFormId != 341
                    ) {
                        /*
                          if($itemform->sellFormId == 13){
                          echo "<pre>";
                          var_export($itemform);
                          echo "</pre>";
                          exit();
                          }
                         */
                        if ($itemform->sellFormType == 1) {
                            //$nazwapola = str_replace(" ","_", $itemform->sellFormTitle);
                            $buildFormAllegro->add($itemform->sellFormId, "text", array(
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'max_length' => $itemform->sellFormLength
                            ));
                        }
                        if ($itemform->sellFormType == 2) {
                            //$nazwapola = str_replace(" ","_", $itemform->sellFormTitle);
                            $buildFormAllegro->add($itemform->sellFormId, "integer", array(
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'max_length' => $itemform->sellFormLength,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue
                                )
                            ));
                        }
                        if ($itemform->sellFormType == 3) {
                            //$nazwapola = str_replace(" ","_", $itemform->sellFormTitle);
                            $buildFormAllegro->add($itemform->sellFormId, "number", array(
                                'scale' => 2,
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'max_length' => $itemform->sellFormLength,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue
                                )
                            ));
                        }
                        if ($itemform->sellFormType == 3) {
                            //$nazwapola = str_replace(" ","_", $itemform->sellFormTitle);
                            $buildFormAllegro->add($itemform->sellFormId, "number", array(
                                'scale' => 2,
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'max_length' => $itemform->sellFormLength,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue
                                )
                            ));
                        }
                        if ($itemform->sellFormType == 4) {
                            //select            
                            $arrchoices = explode("|", $itemform->sellFormDesc);

                            $buildFormAllegro->add($itemform->sellFormId, "choice", array(
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'choices' => $arrchoices,
                                'choices_as_values' => false,
                                'max_length' => $itemform->sellFormLength,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue
                                )
                            ));
                        }
                        if ($itemform->sellFormType == 5) {
                            //radio
                            $arrchoices = explode("|", $itemform->sellFormDesc);

                            $arrchoices_without = array_diff($arrchoices, ['-']);


                            //$nazwapola = str_replace(" ","_", $itemform->sellFormTitle);
                            $buildFormAllegro->add($itemform->sellFormId, "choice", array(
                                'expanded' => true,
                                'multiple' => false,
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'choices' => $arrchoices_without,
                                'choices_as_values' => false,
                                'max_length' => $itemform->sellFormLength,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue
                                )
                            ));
                        }
                        if ($itemform->sellFormType == 6) {
                            //chackbox
                            $arrchoices = explode("|", $itemform->sellFormDesc);

                            $arrchoices_without = array_diff($arrchoices, ['-']);
                            /*
                              if (($key = array_search("-", $arrchoices_trimed)) != false) {
                              unset($arrchoices_trimed[$key]);
                              }
                             */
                            /*
                              echo "<pre>";
                              var_export($arrchoices_trimed);
                              echo "</pre>";
                             */
                            //$nazwapola = str_replace(" ","_", $itemform->sellFormTitle);
                            $buildFormAllegro->add($itemform->sellFormId, "choice", array(
                                'expanded' => true,
                                'multiple' => true,
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'choices' => $arrchoices_without,
                                'choices_as_values' => false,
                                'max_length' => $itemform->sellFormLength,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue
                                )
                            ));
                        }
                        if ($itemform->sellFormType == 8) {
                            //textarea                    
                            $buildFormAllegro->add($itemform->sellFormId, "textarea", array(
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'max_length' => $itemform->sellFormLength,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue
                                )
                            ));
                        }
                        if ($itemform->sellFormType == 9) {
                            //textarea                    
                            $buildFormAllegro->add($itemform->sellFormId, "datetime", array(
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'max_length' => $itemform->sellFormLength,
                                //'input'  => 'timestamp',
                                'html5' => false,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue,
                                    'class' => 'js-datepicker-datetime' . $itemform->sellFormId,
                                    'input_group' => array(
                                        'class' => 'date',
                                        'prepend' => '.icon-calendar',
                                    )
                                ),
                                'widget' => 'single_text',
                                'format' => 'yyyy-MM-dd hh:mm',
                                'read_only' => true,
                                'placeholder' => 'Wybierz datę i czas'
                            ));

                            $datetimepicker[] = $itemform->sellFormId;
                        }
                        if ($itemform->sellFormType == 13) {
                            //textarea                    
                            $buildFormAllegro->add($itemform->sellFormId, "date", array(
                                'label' => $itemform->sellFormTitle,
                                'required' => $itemform->sellFormOpt == 1 ? true : false,
                                'max_length' => $itemform->sellFormLength,
                                'html5' => false,
                                'attr' => array(
                                    'min' => $itemform->sellMinValue,
                                    'max' => $itemform->sellMaxValue,
                                    'class' => 'js-datepicker-date' . $itemform->sellFormId,
                                    'input_group' => array(
                                        'data-date-format' => 'YYYY-MM-DD',
                                        'class' => 'date',
                                        'prepend' => '.icon-calendar'
                                    )
                                ),
                                'date_widget' => 'single_text',
                                'format' => 'yyyy-MM-dd',
                                'read_only' => true,
                                'placeholder' => 'Wybierz datę'
                            ));
                            $datepicker[] = $itemform->sellFormId;
                        }
                    }
                }
                $formAllegro = $buildFormAllegro->getForm();
            }
        }

        return $this->render('InfogoldAccountBundle:Produkty:show.html.twig', array(
                    'entity' => $entity,
                    'klienci' => $entities2,
                    'allegro' => $entity->getCategory()->getItemCategoryIdAllegro() ? $formAllegro->createView() : null,
                    'delete_form' => $deleteForm->createView(),));
    }

    public function editAction($id) {

        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Produkty entity.');
        }

        $editForm = $this->createForm(new ProduktyType($nrklienta, $user->getenableMagazyn(), $entity->getNrproduktu()), $entity, array(
            'validation_groups' => array('edit')));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('InfogoldAccountBundle:Produkty:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Produkty entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('my.main.admin')->getMainAdmin();
        $nrklienta = $user->getNrklienta();
        $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Produkty entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProduktyType($nrklienta, $user->getenableMagazyn(), $entity->getNrproduktu()), $entity, array(
            'validation_groups' => array('edit')));
        $editForm->bind($request);
        $cenabrutto = $editForm->get('cenaProduktu')->getData() + $editForm->get('cenaProduktu')->getData() * ( $editForm->get('vat')->getData() / 100);
        if ($editForm->isValid()) {
            $entity->setCenabrutto($cenabrutto);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('produkty_show', array('id' => $id)));
        }

        return $this->render('InfogoldAccountBundle:Produkty:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Produkty entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('InfogoldAccountBundle:Produkt')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Produkty entity.');
            }
            $entity2 = $em->getRepository('InfogoldKlienciBundle:ProduktyKlienta');
            $qb = $entity2->createQueryBuilder('p');
            $query2 = $qb
                    ->select('p')
                    ->leftJoin('p.produkty', 'c')
                    ->where('c=' . $id)
                    ->getQuery();
            $produktyklienta = $query2->getResult();
            foreach ($produktyklienta as $value) {
                $value->setProdukty(null);
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('produkty'));
    }

    /**
     * Creates a form to delete a Produkty entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
