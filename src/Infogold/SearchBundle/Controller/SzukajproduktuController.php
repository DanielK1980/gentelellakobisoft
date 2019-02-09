<?php

namespace Infogold\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Infogold\AccountBundle\Entity\AllegroInputs;

/**
 * Description of SzukajproduktuController
 *
 * @author Magda
 */
class SzukajproduktuController extends Controller {

    private $allegroFormRadio = array();
    private $datetimepicker = array();
    private $datepicker = array();

    public function indexAction(Request $request) {

        $loggedUser = $this->get('security.context')->getToken()->getUser();

        if (get_class($loggedUser) == "Infogold\UserBundle\Entity\User") {

            $user = $this->get('my.main.admin')->getMainAdmin();
            $userId = $user->getNrklienta();
            $allegro = $user->getAllegro();
            $enablemagazyn = $user->getenableMagazyn();
            $konsultantlogin = false;
        } else {
            $user = $loggedUser->getFirma();
            $userId = $user->getNrklienta();
            $allegro = $user->getAllegro();
            $enablemagazyn = $user->getenableMagazyn();
            $konsultantlogin = true;
        }

        $ar = $this->enableMagazyn($enablemagazyn);

        $data = array();

        $form = $this->createSearchForm($userId, $ar);

        if ($request->isMethod('POST')) {

            $form->bind($request);
            $data = $form->getData();
   
            $szukaj = $data['szukaj'];
            $nazwaproduktu = ($data['wedlug'] == 'nazwaproduktu' ? true : false);
            $nrproduktu = ($data['wedlug'] == 'nrproduktu' ? true : false);
            $ceny = ($data['wedlug'] == 'ceny' ? true : false);
            $wmagazyniedo = ($data['wedlug'] == 'wmagazyniedo' ? true : false);
            $kategoriaId = $data['kategorie'] ? $data['kategorie']->getId() : false;

            /*
              var_dump($kategoriaId);
              exit();
             */
            if ($nazwaproduktu) {
                //szukaj po nazwie produktu

                return $this->NazwaproduktuAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn);
            } else if ($nrproduktu) {

                //szukaj po nr produktu
                return $this->NrproduktuAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn);
            } else if ($ceny) {
                //szukaj po cenie
                return $this->CenyAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn);
            } else if ($wmagazyniedo) {
                //szukaj po nr klienta
                return $this->WmagazyniedoAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn);
            } else if ($kategoriaId && $data['wedlug'] == false) {
                //szukaj po Kategorii
                return $this->KategoriaAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn, $kategoriaId, $allegro);
            } else if ($konsultantlogin) {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
                )));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
                )));
            }
        }
        /*
          if ($konsultantlogin) {
          return $this->redirect($this->generateUrl('produkt_kons', array(
          $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
          )));
          } else {
          return $this->redirect($this->generateUrl('produkty', array(
          $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
          )));
          }
         */
    }

    protected function enableMagazyn($enablemagazyn) {
        $ar = array(
            'nazwaproduktu' => 'nazwa produktu',
            'nrproduktu' => 'nr produktu',
            'ceny' => 'cena od'
                //'wmagazyniedo' => 'w magazynie do' // do poprawy
        );
        if ($enablemagazyn == true) {
            $ar['wmagazyniedo'] = "w magazynie do";
        }
        return $ar;
    }

    public function NazwaproduktuAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.name LIKE :product')
                ->setParameter('product', '%' . $szukaj . '%')
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takiej nazwie')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takiej nazwie')
                )));
            }
        }
    }

    public function NrproduktuAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nrproduktu = :product')
                ->setParameter('product', $szukaj)
                ->getQuery();

        $entities2 = $query2->getResult();

        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takim numerze')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn//'magazyn' => $entities2[0]->getUserproduktu()->getenableMagazyn()  // $entities2[0]['userproduktu']->getenableMagazyn()//$this->get('my.main.admin')->getMainAdmin()->getenableMagazyn()//$entities2->getUserproduktu()->getenableMagazyn()//$this->get('my.main.admin')->getMainAdmin()->getenableMagazyn()
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu o takim numerze')
                )));
            }
        }
    }

    public function CenyAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');
        $zmicena = str_replace(',', '.', $szukaj);
        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.cenabrutto >= :product')
                ->setParameter('product', $zmicena)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w cenie wyższej od podanej')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w cenie wyższej od podanej')
                )));
            }
        }
    }

    public function WmagazyniedoAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.magazyn <= :product')
                ->setParameter('product', $szukaj)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {


                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w magazynie w ilosci mniejszej od podanej')
                )));
            }
        } else {
            if ($entities2) {


                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu w magazynie w ilosci mniejszej od podanej')
                )));
            }
        }
    }

    public function KategoriaAction($szukaj, $userId, $form, $konsultantlogin, $enablemagazyn, $kategoriaId, $allegro) {

        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldAccountBundle:Produkt');

        $qb = $request->createQueryBuilder('p');

        $qb->select('p')
                ->leftJoin('p.userproduktu', 'c')
                ->leftJoin('p.category', 'd')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('d.id =' . $kategoriaId);
        //->setParameter('kategoriaId', $kategoriaId)
        // ->getQuery();

        if (!empty($szukaj)) {
            $qb->andWhere('p.name LIKE :product')
                    ->setParameter('product', '%' . $szukaj . '%');
        }

        $query2 = $qb->getQuery();

        $entities2 = $query2->getResult();

        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKonsultantBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn
                ));
            } else {
                return $this->redirect($this->generateUrl('produkt_kons', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu')
                )));
            }
        } else {
            if ($entities2) {
                if ($allegro) {

                    $kategorie = $this->getDoctrine()->getRepository('InfogoldAccountBundle:Category')->find($kategoriaId);

                    if ($kategorie->getItemCategoryIdAllegro()) {

                        $formAllegro = $this->createAllegroeForm($kategorie->getId(), $kategorie->getItemCategoryIdAllegro());
                    }
                }
                return $this->render('InfogoldAccountBundle:Produkty:index.html.twig', array(
                            'pagination' => $entities2,
                            'form' => $form->createView(),
                            'magazyn' => $enablemagazyn,
                            'allegro' => $kategorie->getItemCategoryIdAllegro() ? $formAllegro->createView() : null,
                            'kategoria' => $kategorie->getItemCategoryIdAllegro() ? $kategorie->getId() : null,
                            'kategoriaAllegro' => $kategorie->getItemCategoryIdAllegro() ? $kategorie->getItemCategoryIdAllegro() : null,
                            'datetimepicker' => $this->datetimepicker,
                            'datepicker' => $this->datepicker
                ));
            } else {
                return $this->redirect($this->generateUrl('produkty', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak produktu')
                )));
            }
        }
    }

    private function createSearchForm($userId, $ar) {
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
                ->add('szukaj', 'text', array('required' => FALSE))
                ->getForm();

        return $form;
    }

    private function createAllegroeForm($kat, $katAllegro) {
        /*
          $datetimepicker = array();
          $datepicker = array();
         */
        $em = $this->getDoctrine()->getManager();

        $entityInputs = $em->getRepository('InfogoldAccountBundle:AllegroInputs')->findBy(
                array(
                    'category' => $kat,
                    'produkt' => NULL)
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
                    $itemform->sellFormId != 1 && //nazwa produktu
                    // $itemform->sellFormId != 7 && //cena minimalna
                    // $itemform->sellFormId != 8 && //cena kup teraz
                    $itemform->sellFormId != 9 && //kraj
                    $itemform->sellFormId != 341 // chyba prezentacja w html
            ) {

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
            }
        }
        $buildFormAllegro->add('submit', 'submit', array('label' => "Zapisz", 'attr' => array('class' => 'btn-success btn-lg', 'icon' => 'check fa-fw')));
        $formAllegro = $buildFormAllegro->getForm();

        foreach ($entityInputs as $value) {
            if (!strpos($value->getValue(), '|')) {
                $formAllegro->get($value->getFormId())->setData($value->getValue());
            }
        }

        return $formAllegro;
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

    public function saveAllegroCategoryAction($kat = null, $katall = null, Request $request) {
        
 

        $user = $this->get('my.main.admin')->getMainAdmin();

        $editForm = $this->createAllegroeForm($kat, $katall);
        $editForm->bind($request);

        if ($editForm->isValid()) {

            $dataFirst = $request->request->get('form');
            $em = $this->getDoctrine()->getManager();

            /*  var_export($dataFirst);
              exit();
             */
            $notemptyFirst = array_filter($dataFirst);

            foreach ($this->allegroFormRadio as $radio) {
                ////PROBLRM Z USÓWANIEM 0 - Dodanie radio z 0
                if (isset($dataFirst[$radio]) && $dataFirst[$radio] == 0) {
                    $notemptyFirst[$radio] = '0';
                }
            }

            unset($notemptyFirst["_token"]);

            $notempty = array_map(array($this, 'ChangeArrayToString'), $notemptyFirst);

            // $arrayIsIn = array_values(array_flip($notempty));

            $getAllForCategoryProducts = $em->getRepository('InfogoldAccountBundle:AllegroInputs')->findBy(
                    array(
                        'user' => $user,
                        'category' => $kat,
                        'produkt' => NULL
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
                                // 'produkt' => $id,
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
                                //'produkt' => $id,
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
                $category = $em2->getRepository('InfogoldAccountBundle:category')->find($kat);
                $newInput = new AllegroInputs();
                $newInput->setUser($user);
                //  $newInput->setProdukt($product);
                $newInput->setFormId($key);
                $newInput->setValue($newValue);
                $newInput->setCategory($category);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newInput);
                $em->flush();
            }
        }

        $ar = $this->enableMagazyn($user->getenableMagazyn());
        $form = $this->createSearchForm($user->getId(), $ar);
        /*
          return $this->redirect($this->generateUrl('produkty_show', array(
          'id' => $id,
          )) . "#tab_content2");
         * */
        return $this->KategoriaAction(NULL, $user->getId(), $form, NULL, $user->getenableMagazyn(), $kat, TRUE); //TU SKOŃCZYŁEM
    }

    //public function SaveAllegroAction() { // przenisione do Produkty COntroller
    //put your code here
}
