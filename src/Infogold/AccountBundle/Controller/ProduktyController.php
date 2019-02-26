<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Infogold\AccountBundle\Entity\Produkt;
use Infogold\AccountBundle\Form\ProduktyType;
use Infogold\AccountBundle\Entity\AllegroInputs;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use GuzzleHttp\Client;
/**
 * Produkty controller.
 *
 */
class ProduktyController extends Controller {

    private $allegroFormRadio = array();
    private $datetimepicker = array();
    private $datepicker = array();

    /**
     * Lists all Produkty entities.
     *
     */
    public function indexAction(Request $request) {

        $User = $this->get('my.main.admin')->getMainAdmin();
        //  $userId = $User->getNrklienta();
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
                    'query_builder' => function(EntityRepository $er) use ($nrklienta) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.company', 'c')
                                ->where('c.Nrklienta=' . $nrklienta);
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

        $allegro = false;
        $code = '';
        if ($User->getEnableAllegro()) {
                    if(isset($_GET['code'])){
                     
            $allegroToken = $this->get('my.allegro')->getToken($_GET['code']);
                      var_export($allegroToken);
                       exit();
                   } 
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $entities, $request->query->get('page', 1)/* page number */, 15 /* limit per page */);
        return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                    'pagination' => $pagination,
                    'form' => $form->createView(),
                    'magazyn' => $User->getenableMagazyn(),
                    'client_id' => $User->getEnableAllegro() ?  $User->getAllegro()->getAllegroClientID() : null,
                    'code' => $code
            
            
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
                        ->where('p.id=' . $id)
                        ->andWhere('c.nipklienta is not null')
                        ->setMaxResults(100)
                        ->getQuery();
                $entities2 = $query2->getResult();

                $indexedOnly = array();

                foreach ($entities2 as $row) {

                    if ($row["created"]) {
                        $myDate = $row['created'];
                        $row["created"] = $myDate->format('Y-m-d H:i');
                    }
                    if ($row["id"]) {
                        $row["id"] = "<a class='btn btn-default btn-sm'  href='/symfony2_8_new/web/app_dev.php/user/baza/" . $row["id"] . "/show'><span class='glyphicon glyphicon-eye-open'></span>";
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
        $indexedOnly = array();
        if ($request->getMethod() == 'POST') {
            $id = $request->get('id');
            if ($id) {
                $em2 = $this->getDoctrine()->getManager();
                $req = $em2->getRepository('InfogoldAccountBundle:Produkt');
                $qb = $req->createQueryBuilder('p');
                $query2 = $qb
                        ->select('c.imie,c.nazwisko,d.cenaProduktu,d.ilosc,d.created,c.id')
                        ->leftJoin('p.produkt', 'd')
                        ->leftJoin('d.ProduktyKlienta', 'c')
                        ->where('p.id=' . $id)
                        ->andWhere('c.peselklienta is not null')
                        ->setMaxResults(100)
                        ->getQuery();
                $entities2 = $query2->getResult();



                foreach ($entities2 as $row) {

                    if ($row["created"]) {
                        $myDate = $row['created'];
                        $row["created"] = $myDate->format('Y-m-d H:i');
                    }
                    if ($row["id"]) {
                        $row["id"] = "<a class='btn btn-default btn-sm'  href='/symfony2_8_new/web/app_dev.php/user/baza/" . $row["id"] . "/show'><span class='glyphicon glyphicon-eye-open'></span>";
                    }
                    $indexedOnly[] = array_values($row);
                }

                $response = new Response();
                $response->headers->set('Content-Type', 'application/json');
            }
        }
        /*
          return $response->setContent(json_encode(array(
          'data' => $indexedOnly
          )));

         */

        return new JsonResponse(array(
            'data' => $indexedOnly
        ));
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
            if ($entity->getCategory()->getItemCategoryIdAllegro()) {
                $formAllegro = $this->createAllegroeForm($entity->getCategory()->getId(), $entity->getCategory()->getItemCategoryIdAllegro(), $id);
            }
        }



        return $this->render('InfogoldAccountBundle:Produkty:show.html.twig', array(
                    'entity' => $entity,
                    'klienci' => $entities2,
                    'allegro' => $entity->getCategory()->getItemCategoryIdAllegro() ? $formAllegro->createView() : null,
                    'kategoria' => $entity->getCategory()->getItemCategoryIdAllegro() ? $entity->getCategory()->getId() : null,
                    'kategoriaAllegro' => $entity->getCategory()->getItemCategoryIdAllegro() ? $entity->getCategory()->getItemCategoryIdAllegro() : null,
                    'delete_form' => $deleteForm->createView(),
                    'datetimepicker' => $this->datetimepicker,
                    'datepicker' => $this->datepicker,
        ));
    }

    public function SaveAllegroAction($kat = null, $katall = null, $id = null, Request $request) {

        $user = $this->get('my.main.admin')->getMainAdmin();

        $editForm = $this->createAllegroeForm($kat, $katall, $id);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            $dataFirst = $request->request->get('form');
            $em = $this->getDoctrine()->getManager();

            /*  var_export($dataFirst);
              exit();
             */
            $notemptyFirst = array_filter($dataFirst);

            foreach ($this->allegroFormRadio as $radio) {
                //Dodanie radio z 0
                if (isset($dataFirst[$radio]) && $dataFirst[$radio] == 0) {
                    $notemptyFirst[$radio] = '0';
                }
            }
            //PROBLRM Z USÓWANIEM 0
            unset($notemptyFirst["_token"]);

            $notempty = array_map(array($this, 'ChangeArrayToString'), $notemptyFirst);

            // $arrayIsIn = array_values(array_flip($notempty));

            $getAllForCategoryProducts = $em->getRepository('InfogoldAccountBundle:AllegroInputs')->findBy(
                    array(
                        'user' => $user,
                        'category' => $kat,
                        'produkt' => $id
                    )
            );

            $isInputBase = array();

            if ($getAllForCategoryProducts) {
                foreach ($getAllForCategoryProducts as $inputBase) {
                    $isInputBase[$inputBase->getFormId()] = $inputBase->getValue();
                }
            }
            /*
              $update = array();
              $remove = array();
              $insert = array();
             */
            foreach ($isInputBase as $key => $baseInput) {

                if (!isset($dataFirst[$key]) || (isset($dataFirst[$key]) && ($dataFirst[$key] == ""))) {
                    //REMOVE  
                    $em = $this->getDoctrine()->getManager();
                    $entityInputs = $em->getRepository('InfogoldAccountBundle:AllegroInputs')->findOneBy(
                            array(
                                'user' => $user,
                                'category' => $kat,
                                'produkt' => $id,
                                'formId' => $key
                            //  'value' => $notempty[$input]
                            )
                    );
                    $em->remove($entityInputs);
                    $em->flush();
                }

                //UPDATE
                if (isset($notempty[$key]) && $notempty[$key] != $baseInput) {
                    $em = $this->getDoctrine()->getManager();
                    $entityInputs = $em->getRepository('InfogoldAccountBundle:AllegroInputs')->findOneBy(
                            array(
                                'user' => $user,
                                'category' => $kat,
                                'produkt' => $id,
                                'formId' => $key
                            //  'value' => $notempty[$input]
                            )
                    );
                    if ($entityInputs) {

                        $entityInputs->setValue($notempty[$key]);
                        $em->flush();
                    }
                }
            }

            $newData = array_diff_key($notempty, $isInputBase);

            foreach ($newData as $key => $newValue) {
                //INSERT

                $em2 = $this->getDoctrine()->getManager();
                $product = $em2->getRepository('InfogoldAccountBundle:Produkt')->find($id);
                $newInput = new AllegroInputs();
                $newInput->setUser($user);
                $newInput->setProdukt($product);
                $newInput->setFormId($key);
                $newInput->setValue($newValue);
                $newInput->setCategory($product->getCategory());
                $em = $this->getDoctrine()->getManager();
                $em->persist($newInput);
                $em->flush();
            }
        }

        return $this->redirect($this->generateUrl('produkty_show', array(
                            'id' => $id,
                        )) . "#tab_content2");
    }

    protected function ChangeArrayToString($value) {
        if (is_array($value)) {
            if (isset($value[1])) {
                return implode("|", $value);
            } else {
                return $value[0] . "|";
            }
        } else {
            return $value;
        }
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

    public function fileUploadHandlerAction(Request $request) {
        $output = array('uploaded' => false);
        // get the file from the request object
        $file = $request->files->get('file');
        // generate a new filename (safer, better approach)
        // To use original filename, $fileName = $this->file->getClientOriginalName();
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        // set your uploads directory
        $uploadDir = $this->get('kernel')->getRootDir() . '/../web/uploads/images';
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        if ($file->move($uploadDir, $fileName)) {
            $output['uploaded'] = true;
            $output['fileName'] = $fileName;
        }
        return new JsonResponse($output);
    }

    private function createAllegroeForm($kat, $katAllegro, $id) {

        $em = $this->getDoctrine()->getManager();

        $entityInputs = $em->getRepository('InfogoldAccountBundle:AllegroInputs')->findBy(
                array(
                    'category' => $kat,
                    'produkt' => $id)
        );

        $checkboxes = array();
        foreach ($entityInputs as $value) {
            if (strpos($value->getValue(), '|')) {
                $arrchoices = explode("|", $value->getValue());
                $checkboxes[$value->getFormId()] = $arrchoices;
            }
        }
        // var_export($checkboxes);
        //  exit();
        $buildFormAllegro = $this->createFormBuilder();
        $allegomodule = $this->get('my.allegro.module')->getModuleAllegro("doGetSellFormFieldsForCategory", array('categoryId' => $katAllegro));

        foreach ($allegomodule->sellFormFieldsForCategory->sellFormFieldsList->item as $itemform) {

            // var_export($checkboxes);
            //  exit();

            if ($itemform->sellFormId != 2 && //kategoria
                    $itemform->sellFormId != 3 && //czas rozpoczęcia
                    // $itemform->sellFormId != 6 && //cena wywoławcza
                    // $itemform->sellFormId != 7 && //cena minimalna
                    // $itemform->sellFormId != 8 && //cena kup teraz
                    $itemform->sellFormId != 9// && //kraj
            //  $itemform->sellFormId != 341 // chyba prezentacja w html
            ) {

                /*
                  if($itemform->sellFormId == 341){
                  echo "<pre>";
                  var_export($itemform);
                  echo "</pre>";
                  exit();
                  }
                 */
                /*
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
                  'required' => FALSE, // $itemform->sellFormOpt == 1 ? true : false,
                  'choices' => $arrchoices,
                  'placeholder' => false,
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
                  'required' => false, //$itemform->sellFormOpt == 1 ? true : false,
                  'choices' => $arrchoices,
                  'choices_as_values' => false,
                  'placeholder' => false,
                  'max_length' => $itemform->sellFormLength,
                  'attr' => array(
                  'min' => $itemform->sellMinValue,
                  'max' => $itemform->sellMaxValue
                  )
                  ));

                  //pobieramy radio do dalszej obróki z 0 jest zapis
                  $this->allegroFormRadio[] = $itemform->sellFormId;
                  }
                  if ($itemform->sellFormType == 6) {
                  //chackbox
                  $arrchoices = explode("|", $itemform->sellFormDesc);

                  $arrchoices_without = array_diff($arrchoices, ['-']);
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
                  ),
                  'data' => isset($checkboxes[$itemform->sellFormId]) ? $checkboxes[$itemform->sellFormId] : array()
                  ));
                  }
                 */


                if ($itemform->sellFormType == 8) {
                    //textarea    
                    /*  if ($itemform->sellFormId == 341) {
                      $buildFormAllegro->add($itemform->sellFormId, "textarea", array(
                      'label' => 'Opis strony produktu',
                      'required' => $itemform->sellFormOpt == 1 ? true : false,
                      'max_length' => $itemform->sellFormLength,
                      'attr' => array(
                      'class' => 'tinymce',
                      'data-theme'=>'modern',
                      'min' => $itemform->sellMinValue,
                      'max' => $itemform->sellMaxValue,
                      )
                      ));
                      } else {
                     */
                    $buildFormAllegro->add($itemform->sellFormId, "textarea", array(
                        'label' => $itemform->sellFormTitle,
                        'required' => $itemform->sellFormOpt == 1 ? true : false,
                        'max_length' => $itemform->sellFormLength,
                        'attr' => array(
                            'min' => $itemform->sellMinValue,
                            'max' => $itemform->sellMaxValue
                        )
                    ));
                    // }
                }
                /*
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

                  $this->datetimepicker[] = $itemform->sellFormId;
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
                  $this->datepicker[] = $itemform->sellFormId;
                  }
                 */
            }
        }
        $buildFormAllegro->add('submit', 'submit', array('label' => "Zapisz", 'attr' => array('class' => 'btn-success btn-lg', 'icon' => 'check fa-fw')));
        $formAllegro = $buildFormAllegro->getForm();
        /*
          foreach ($entityInputs as $value) {
          if (!strpos($value->getValue(), '|')) {
          $formAllegro->get($value->getFormId())->setData($value->getValue());
          }
          }
         */
        return $formAllegro;
    }

}
