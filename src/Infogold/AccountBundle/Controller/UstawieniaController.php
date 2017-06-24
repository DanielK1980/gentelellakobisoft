<?php

//test

namespace Infogold\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ddeboer\DataImport\Workflow;
use Symfony\Component\Form\FormError;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ddeboer\DataImport\ValueConverter\StringToDateTimeValueConverter;

class UstawieniaController extends Controller {

    //put your code here
    public function indexAction(Request $request) {

        $user = $this->get('my.main.admin')->getAdminsPaginate();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $user, $request->query->get('page', 1)/* page number */, 3/* limit per page */
        );

        return $this->render('InfogoldAccountBundle:Ustawienia:admins.html.twig', array(
                    'pagination' => $pagination,
        ));
    }

    public function activateAction($id, Request $request) {

        $request1 = $this->container->get('request');
        $routeName = $request1->get('_route');

        if ($request->getMethod() == 'POST' && $routeName == 'activate_admin') {

            $em = $this->getDoctrine()->getManager();
            $admin = $em->getRepository('InfogoldUserBundle:User')->find($id);

            if (!$admin) {
                throw $this->createNotFoundException(
                        'No admin found for id ' . $id
                );
            }
            $admin->setEnabled(true);
            $em->flush();
        }
        return $this->redirect($this->generateUrl("ustawienia"));
    }

    public function deactivateAction($id, Request $request) {

        $request1 = $this->container->get('request');
        $routeName = $request1->get('_route');

        if ($request->getMethod() == 'POST' && $routeName == 'deactivate_admin') {

            $em = $this->getDoctrine()->getManager();
            $admin = $em->getRepository('InfogoldUserBundle:User')->find($id);

            if (!$admin) {
                throw $this->createNotFoundException(
                        'No admin found for id ' . $id
                );
            }

            $admin->setEnabled(false);
            $em->flush();
        }
        return $this->redirect($this->generateUrl("ustawienia"));
    }

    public function deleteadminAction($id, Request $request) {

        $request1 = $this->container->get('request');
        $routeName = $request1->get('_route');

        if ($request->getMethod() == 'POST' && $routeName == 'delete_admin') {

            $em = $this->getDoctrine()->getManager();
            $admin = $em->getRepository('InfogoldUserBundle:User')->find($id);

            if (!$admin) {
                throw $this->createNotFoundException(
                        'No admin found for id ' . $id
                );
            }

            $admin->setLocked(true);
            $em->flush();
        }
        return $this->redirect($this->generateUrl("ustawienia"));
    }

    public function magazynAction() {

        $user_admin = $this->get('my.main.admin')->getMainAdmin();

        return $this->render('InfogoldAccountBundle:Ustawienia:magazyn.html.twig', array(
                    'magazyn' => $user_admin,
        ));
    }

    public function activate_magazynAction($id, Request $request) {
        $request1 = $this->container->get('request');
        $routeName = $request1->get('_route');

        if ($request->getMethod() == 'POST' && $routeName == 'activate_magazyn') {

            $em = $this->getDoctrine()->getManager();
            $admin = $em->getRepository('InfogoldUserBundle:User')->find($id);

            if (!$admin) {
                throw $this->createNotFoundException(
                        'No admin found for id ' . $id
                );
            }

            $admin->setenableMagazyn(true);
            $em->flush();
        }
        return $this->redirect($this->generateUrl("ustawienia_magazynu"));
    }

    public function deactivate_magazynAction($id, Request $request) {
        $request1 = $this->container->get('request');
        $routeName = $request1->get('_route');

        if ($request->getMethod() == 'POST' && $routeName == 'deactivate_magazyn') {

            $em = $this->getDoctrine()->getManager();
            $admin = $em->getRepository('InfogoldUserBundle:User')->find($id);

            if (!$admin) {
                throw $this->createNotFoundException(
                        'No admin found for id ' . $id
                );
            }

            $admin->setenableMagazyn(false);
            $em->flush();
        }
        return $this->redirect($this->generateUrl("ustawienia_magazynu"));
    }

    protected function getFormProduct($form, $path, $user_admin) {

        
        $fileupload = $form->getData()['file'];
        $fileName = md5(uniqid()) . '.csv';
        $fileupload->move(
                $this->getParameter('csv_directory'), $fileName
        );

        $file = new \SplFileObject($path . "/" . $fileName, 'r');
        $csvReader = new CsvReader($file, ";");
        $csvReader->setHeaderRowNumber(0);

        $i = 2;

        if (!empty($csvReader->getColumnHeaders())) {

            $header = $csvReader->getColumnHeaders();

            if (in_array("cenaProduktu", $header) == false ||
                    in_array("nazwa", $header) == false ||
                    in_array("vat", $header) == false ||
                    in_array("jednostkaMiary", $header) == false
            ) {

                $form->addError(new FormError("Wymagagane dane w nazwach kolumn to 'nazwa', 'cenaProduktu', 'vat', 'jednostkaMiary'"));
            } else {

                $numeryproduktow = array();

                $error = 0;
                $loop = 0;
                 $kategorie = array();
                foreach ($csvReader as $row) {

                    if (!preg_match('/^[0-9]+(?:[\.\,][0-9]{0,2})?$/', trim($row['cenaProduktu']))) {

                        $form->addError(new FormError('Cena produktu nie jest prawidłowa w wierszu numer ' . $i));
                        $error++;
                        break;
                    }

                    if (empty(trim($row['cenaProduktu'])) || (int) $row['cenaProduktu'] == 0 || (int) $row['cenaProduktu'] < 0) {

                        $form->addError(new FormError('Brak ceny, niepoprawny format ceny lub cena równa "0" w wierszu numer ' . $i));
                        $error++;
                        break;
                    }

                    if (in_array("nrProduktu", $header)) {
                            
                        $numeryproduktow[] = $row['nrProduktu'];
                        $nrproduktu = $this->getDoctrine()->getManager()->getRepository('InfogoldAccountBundle:Produkt');
                        $item = $nrproduktu->findOneBy(array('nrproduktu' => $row['nrProduktu'], 'userproduktu' => $user_admin->getId()));

                        if ($item) {

                            $form->addError(new FormError('Produkt o nr ' .$row['nrProduktu'].' już istnieje w systemie - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                    }
                    if (in_array("kategoria", $header)) {

                    if (!empty($row['kategoria'])){
                         $category = $this->getDoctrine()->getManager()->getRepository('InfogoldAccountBundle:Category');
                         $item = $category->findOneBy(array('name' => $row['kategoria'], 'company' => $user_admin->getId()));

                         if ($item == null) {
                             
                             $form->addError(new FormError('Brak w systemie kategorii o nazwie "' .$row['kategoria'].'" - błąd w wierszu nr:' . $i));
                             $error++;
                             break;
                         }
                         else{
                             $kategorie[$loop] = $item->getId();
                         }
                     }
                    }                  
                    $i++;
                    $loop++;
                }

                if ($error == 0) {
                    if (in_array("nrProduktu", $header)) {
                        
                       if(count(array_filter($numeryproduktow))>1){
                        if (count(array_unique($numeryproduktow)) < count($numeryproduktow)) {

                            $form->addError(new FormError('Nr produktów w pliku csv się powtarzają'));
                             $error++;
                        }
                       }
                    }
                }
                if ($form->isSubmitted() && $form->isValid()) {

                    $em2 = $this->getDoctrine()->getManager();

                    $doctrineWriter = new DoctrineWriter($em2, 'InfogoldAccountBundle:Produkt');
                    $doctrineWriter->disableTruncate();
                    $doctrineWriter->prepare();

                    $loopcount = 0;
                    foreach ($csvReader as $row) {

                        $cena = trim($row['cenaProduktu']);

                        if (strpos($cena, ",") !== false) {
                            $cena = str_replace(",", ".", $cena);
                        }
                    
                        $doctrineWriter->writeItem(array(
                            'cenaProduktu' => $cena,
                            'nrproduktu' => isset($row['nrProduktu']) && !empty($row['nrProduktu']) ? $row['nrProduktu'] : null,
                            'cenabrutto' => $cena + ($cena * (int) $row['vat']) / 100,
                            'name' => $row['nazwa'],
                            'opis' => isset($row['opis']) && !empty($row['opis']) ? $row['opis'] : null,
                            'vat' => $row['vat'],
                            'magazyn' => isset($row['magazyn']) && !empty($row['magazyn']) ? $row['magazyn'] : 0,
                            'enableMagazyn' => isset($row['magazyn']) && !empty($row['magazyn']) ? 1 : null,
                            'jednostkamiary' => $row['jednostkaMiary'],
                            'userproduktu' => $user_admin,
                            'category' => isset($kategorie[$loopcount]) && !empty($kategorie[$loopcount]) ? $category->find($kategorie[$loopcount]) : null
                        ));
                        $loopcount++;
                    }
                    $doctrineWriter->finish();

                    $flash = $this->get('braincrafted_bootstrap.flash');
                    $flash->success('Zaimportowano produkty.');
                }
            }
        } else {
            $form->addError(new FormError("Plik CSV powinien zawierać nazwy kolumn: 'cenaProduktu', 'nazwa', 'vat', 'jednostkaMiary'"));
        }
    }

    protected function validatenip($nip) {
        $nip_bez_kresek = preg_replace("/-/", "", $nip);
        $reg = '/^[0-9]{10}$/';
        if (preg_match($reg, $nip_bez_kresek) == false)
            return false;
        else {
            $dig = str_split($nip_bez_kresek);
            $kontrola = (6 * intval($dig[0]) + 5 * intval($dig[1]) + 7 * intval($dig[2]) + 2 * intval($dig[3]) + 3 * intval($dig[4]) + 4 * intval($dig[5]) + 5 * intval($dig[6]) + 6 * intval($dig[7]) + 7 * intval($dig[8])) % 11;
            if (intval($dig[9]) == $kontrola)
                return true;
            else
                return false;
        }
    }

    protected function validatepesel($pesel) {
        $reg = '/^[0-9]{11}$/';
        if (preg_match($reg, $pesel) == false) {
            return false;
        } else {

            return true;
        }
    }

    protected function validateregon9($regon) {
        $reg = '/^[0-9]{9}$/';
        if (preg_match($reg, $regon) == false)
            return false;
        else {
            $dig = str_split($regon);
            $kontrola = (8 * intval($dig[0]) + 9 * intval($dig[1]) + 2 * intval($dig[2]) + 3 * intval($dig[3]) + 4 * intval($dig[4]) + 5 * intval($dig[5]) + 6 * intval($dig[6]) + 7 * intval($dig[7])) % 11;
            if ($kontrola == 10)
                $kontrola = 0;
            if (intval($dig[8]) == $kontrola)
                return true;
            else
                return false;
        }
    }

    protected function getFormKlienci($form, $path, $user_admin) {

        $noklient = false;
        $fileupload = $form->getData()['file'];
        $fileName = md5(uniqid()) . '.csv';
        $fileupload->move(
                $this->getParameter('csv_directory'), $fileName
        );

        $file = new \SplFileObject($path . "/" . $fileName, 'r');
        $csvReader = new CsvReader($file, ";");
        $csvReader->setHeaderRowNumber(0);

        $i = 2;

        if (!empty($csvReader->getColumnHeaders())) {

            $header = $csvReader->getColumnHeaders();

            if (in_array("imie", $header) == false ||
                    in_array("nazwisko", $header) == false
            ) {

                $form->addError(new FormError("Wymagagane dane w nazwach kolumn to 'imie', 'nazwisko'"));
            } else {

                $numerklienta = array();

                $error = 0;
                foreach ($csvReader as $row) {


                    if (in_array("nrTelefonu", $header)) {
                        if (isset($row['nrTelefonu']) && !empty($row['nrTelefonu'])) {
                            if (!preg_match('^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$', trim($row['nrTelefonu']))) {
                                $form->addError(new FormError('Nr telefon jest niprawidłowy - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        }
                    }
                    if (in_array("email", $header)) {
                        if (isset($row['email']) && !empty($row['email'])) {
                            if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                                $form->addError(new FormError('błędny adres email  - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        }
                    }
                    if (in_array("nip", $header)) {
                        if (isset($row['nip']) && !empty($row['nip'])) {
                            $nip_bez_kresek = preg_replace("/-/", "", $row['nip']);
                            $reg = '/^[0-9]{10}$/';
                            if (preg_match($reg, $nip_bez_kresek) == false) {
                                $form->addError(new FormError('błędny NIP  - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        }
                        if (in_array("nazwaFirmy", $header)) {
                            if (isset($row['nazwaFirmy']) && !empty($row['nazwaFirmy'])) {
                                if (strlen($row['nazwaFirmy']) > 80) {
                                    $form->addError(new FormError('Nazwa firmy przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            } else {
                                $form->addError(new FormError('Brak nazwy firmy przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        } else {
                            $form->addError(new FormError('Brak nazwy firmy przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                        if (in_array("kodPocztowy", $header)) {
                            if (isset($row['kodPocztowy']) && !empty($row['kodPocztowy'])) {
                                if (strlen($row['kodPocztowy']) > 80) {
                                    $form->addError(new FormError('kod pocztowy przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            } else {
                                $form->addError(new FormError('Brak w adresie kodu pocztowego przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        } else {
                            $form->addError(new FormError('Brak w adresie kodu pocztowego przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                        if (in_array("ulica", $header)) {
                            if (isset($row['ulica']) && !empty($row['ulica'])) {
                                if (strlen($row['ulica']) > 80) {
                                    $form->addError(new FormError('ulica przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            } else {
                                $form->addError(new FormError('Brak ulicy przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        } else {
                            $form->addError(new FormError('Brak ulicy przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                        if (in_array("miasto", $header)) {
                            if (isset($row['miasto']) && !empty($row['miasto'])) {
                                if (strlen($row['miasto']) > 80) {
                                    $form->addError(new FormError('miasto przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            } else {
                                $form->addError(new FormError('Brak miasta przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        } else {
                            $form->addError(new FormError('Brak miasta przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                        if (in_array("nrDomu", $header)) {
                            if (isset($row['nrDomu']) && !empty($row['nrDomu'])) {
                                if (strlen($row['nrDomu']) > 80) {
                                    $form->addError(new FormError('nr domu przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            } else {
                                $form->addError(new FormError('Brak w adresie nr domu przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        } else {
                            $form->addError(new FormError('Brak w adresie nr domu przy wsytępującym nr NIP - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                    }
                    if (in_array("regon", $header)) {
                        if (isset($row['regon']) && !empty($row['regon'])) {
                            $reg = '/^[0-9]{9}$/';
                            if (preg_match($reg, $row['regon']) == false) {
                                $form->addError(new FormError('błędny regon  - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        }
                    }
                    if (in_array("pesel", $header)) {
                        if (isset($row['pesel']) && !empty($row['pesel'])) {
                            if ($this->validatepesel($row['pesel']) == false) {
                                $form->addError(new FormError('błędny pesel  - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        }
                        if (in_array("kodPocztowy", $header)) {
                            if (isset($row['kodPocztowy']) && !empty($row['kodPocztowy'])) {
                                if (strlen($row['kodPocztowy']) > 80) {
                                    $form->addError(new FormError('kod pocztowy przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            }
                        }
                        if (in_array("ulica", $header)) {
                            if (isset($row['ulica']) && !empty($row['ulica'])) {
                                if (strlen($row['ulica']) > 80) {
                                    $form->addError(new FormError('ulica przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            }
                        }
                        if (in_array("miasto", $header)) {
                            if (isset($row['miasto']) && !empty($row['miasto'])) {
                                if (strlen($row['miasto']) > 80) {
                                    $form->addError(new FormError('miasto przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            }
                        }
                        if (in_array("nrDomu", $header)) {
                            if (isset($row['nrDomu']) && !empty($row['nrDomu'])) {
                                if (strlen($row['nrDomu']) > 80) {
                                    $form->addError(new FormError('nr domu przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                    $error++;
                                    break;
                                }
                            }
                        }
                    }
                    if (in_array("imie", $header)) {
                        if (strlen($row['imie']) > 80) {
                            $form->addError(new FormError('Imie przekracza 80 znaków - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                    }
                    if (in_array("nazwisko", $header)) {
                        if (strlen($row['nazwisko']) > 120) {
                            $form->addError(new FormError('Nazwisko przekracza 80 znaków - błąd w wierszu nr:' . $i));
                            $error++;
                            break;
                        }
                    }

                    if (in_array("nrMieszkania", $header)) {
                        if (isset($row['nrMieszkania']) && !empty($row['nrMieszkania'])) {
                            if (strlen($row['nrMieszkania']) > 80) {
                                $form->addError(new FormError('nr mieszkania przekracza 80 znaków - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        }
                    }

                    if (in_array("nrKlienta", $header)) {
                        if (isset($row['nrKlienta']) && !empty($row['nrKlienta'])) {
                            $numerklienta[] = $row['nrKlienta'];
                            $nrklienta = $this->getDoctrine()->getManager()->getRepository('InfogoldKlienciBundle:Klienci');
                            $item = $nrklienta->findOneBy(array('numerklienta' => $row['nrKlienta'], 'user' => $user_admin->getId()));

                            if ($item) {

                                $form->addError(new FormError('Klient o danym nr już istnieje w systemie - błąd w wierszu nr:' . $i));
                                $error++;
                                break;
                            }
                        }
                    }

                    $i++;
                }

                if ($error == 0) {
                    if (in_array("nrKlienta", $header)) {
                        if (isset($row['nrKlienta']) && !empty($row['nrKlienta'])) {
                            if (count(array_unique($numerklienta)) < count($numerklienta)) {

                                $form->addError(new FormError('Nr klienta w pliku csv się powtarzają'));
                                $error++;
                            }
                        }
                    }
                }

                if ($form->isSubmitted() && $form->isValid()) {
                    $today = new \DateTime("now");
                    $em = $this->getDoctrine()->getManager();
                    $doctrineWriter = new DoctrineWriter($em, 'InfogoldKlienciBundle:Klienci');
                    $doctrineWriter->disableTruncate();
                    $doctrineWriter->prepare();

                    foreach ($csvReader as $row) {

                        $doctrineWriter->writeItem(array(
                            'id' => 0,
                            'nazwaklienta' => isset($row['nazwaFirmy']) && !empty($row['nazwaFirmy']) ? $row['nazwaFirmy'] : null,
                            'telefonklienta' => isset($row['nrTelefonu']) && !empty($row['nrTelefonu']) ? $row['nrTelefonu'] : null,
                            'emailklienta' => isset($row['email']) && !empty($row['email']) ? $row['email'] : null,
                            'nipklienta' => isset($row['nip']) && !empty($row['nip']) ? $row['nip'] : null,
                            'regonklienta' => isset($row['regon']) && !empty($row['regon']) ? $row['regon'] : null,
                            'peselklienta' => isset($row['pesel']) && !empty($row['pesel']) ? $row['pesel'] : null,
                            'imie' => isset($row['imie']) && !empty($row['imie']) ? $row['imie'] : null,
                            'nazwisko' => isset($row['nazwisko']) && !empty($row['nazwisko']) ? $row['nazwisko'] : null,
                            'ulica' => isset($row['ulica']) && !empty($row['ulica']) ? $row['ulica'] : null,
                            'kodpocztowy' => isset($row['kodPocztowy']) && !empty($row['kodPocztowy']) ? $row['kodPocztowy'] : null,
                            'miasto' => isset($row['miasto']) && !empty($row['miasto']) ? $row['miasto'] : null,
                            'nrdomu' => isset($row['nrDomu']) && !empty($row['nrDomu']) ? $row['nrDomu'] : null,
                            'nrmieszkania' => isset($row['nrMieszkania']) && !empty($row['nrMieszkania']) ? $row['nrMieszkania'] : null,
                            'created' => $today,
                            'user' => $user_admin
                        ));
                    }
                    $doctrineWriter->finish();

                    $flash = $this->get('braincrafted_bootstrap.flash');
                    $flash->success('Zaimportowano klientów.');
                }
            }
        } else {
            $form->addError(new FormError("Plik CSV powinien zawierać nazwy kolumn: 'imie', 'nazwisko'"));
        }
    }

    protected function getFormKonsultant($form, $path, $user_admin) {
        
    }

    protected function getFormFaktury($form, $path, $user_admin) {
        
    }

    public function wgrajAction(Request $request) {

      $user_admin = $this->get('my.main.admin')->getMainAdmin();

        $form = $this->createFormBuilder()
                ->add('file', new FileType(), array('attr' => array('label_col' => 50, 'label' => 'Wgraj', 'data-preview-file-type' => 'text', 'data-allowed-file-extensions' => '["csv"]', 'class' => 'file')))
                ->add('produkty', new SubmitType(), array('label' => 'Wgraj produkty', 'attr' => array('class' => 'btn-success', 'icon' => 'check fa-lg')))
                ->add('konsultanci', new SubmitType(), array('label' => 'Wgraj konsultantów', 'attr' => array('class' => 'btn-success', 'icon' => 'user fa-lg')))
                ->add('faktury', new SubmitType(), array('label' => 'Wgraj faktury', 'attr' => array('class' => 'btn-success', 'icon' => 'file-text-o fa-lg')))
                ->add('klienci', new SubmitType(), array('label' => 'Wgraj klientów', 'attr' => array('class' => 'btn-success', 'icon' => 'address-card fa-lg')))
                ->getForm();


        if ($request->getMethod('post') == 'POST') {

            $form->handleRequest($request);
            $path = "E:/xampp/htdocs/" . $this->getRequest()->getBasePath() . "/uploads/csv";

            if ($form->isSubmitted()) {

                if ($form->get('produkty')->isClicked()) {

                    $this->getFormProduct($form, $path, $user_admin);
                } else if ($form->get('konsultanci')->isClicked()) {

                    $this->getFormKonsultant($form, $path, $user_admin);
                } else if ($form->get('faktury')->isClicked()) {

                    $this->getFormFaktury($form, $path, $user_admin);
                } else {

                    $this->getFormKlienci($form, $path, $user_admin);
                }
            }
            // var_export("jest");
        }
        return $this->render('InfogoldAccountBundle:Ustawienia:wgraj.html.twig', array(
                    'user' => $user_admin,
                    'form' => $form->createView()
        ));
    }

}
