<?php

namespace Infogold\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;

class SzukajfirmowychController extends Controller {

    public function indexAction(Request $request) {

        $loggedUser = $this->get('security.context')->getToken()->getUser();
        //  var_export($loggedUser->getId());
        //  exit();                        

        if (get_class($loggedUser) == "Infogold\UserBundle\Entity\User") {

            $userId = $loggedUser->getNrklienta();

            $konsultantlogin = false;
        } else {

            $userId = $loggedUser->getFirma()->getNrklienta();
            $konsultantlogin = true;
        }

        $data = array();
        $form = $this->createFormBuilder($data)
                ->add('szukaj', 'text', array('required' => false))
                ->add('konsultant', 'entity', array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($userId) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.firma', 'c')
                                ->where('c.Nrklienta=' . $userId);
                    },
                    'required' => false,
                    'label' => 'Według Konsultanta',
                    'empty_value' => 'Wybierz',
                ))
                ->getForm();

        if ($request->isMethod('POST')) {

            $form->bind($request);

            // data is an array with "szukaj", "konsultant" keys
            $data = $form->getData();

            $szukaj = $data['szukaj'];
            $konsultant = $data['konsultant'];
            $dlugosc = strlen($szukaj);


            if (empty($konsultant)) {
                if (is_numeric($szukaj) === false) {
                    //szukaj po nazwie firmy

                    return $this->NazwaAction($szukaj, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 10) {
                    //szukaj po nip
                    return $this->NipAction($szukaj, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 9) {
                    //szukaj po nr regon
                    return $this->RegonklientaAction($szukaj, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 8) {
                    //szukaj po nr regon
                    return $this->NrklientaAction($szukaj, $userId, $form, $konsultantlogin);
                } else if ($konsultantlogin) {
                    return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                $this->get('session')->getFlashBag()->add('error', 'NIP składa się z 10 cyfr, REGON z 9 a NR KLIENTA z 8')
                    )));
                } else {

                    return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                        $this->get('session')->getFlashBag()->add('error', 'NIP składa się z 10 cyfr, REGON z 9 a NR KLIENTA z 8')
                    )));
                }
            } else {
                if (empty($szukaj)) {
                    //szukaj po konsultancie
                    return $this->TylkoKonsultantAction($konsultant, $userId, $form, $konsultantlogin);
                } else if (is_numeric($szukaj) === false) {
                    //szukaj po nazwisku
                    return $this->NazwaKonsultantAction($szukaj, $konsultant, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 10) {
                    //szukaj po nip
                    return $this->NipKonsultantAction($szukaj, $konsultant, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 9) {
                    //szukaj po nip
                    return $this->RegonKonsultantAction($szukaj, $konsultant, $userId, $form, $konsultantlogin);
                } else if ($dlugosc === 8) {
                    //szukaj po nr klienta
                    return $this->NrklientaKonsultantAction($szukaj, $konsultant, $userId, $form, $konsultantlogin);
                } else if ($konsultantlogin) {
                    return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                $this->get('session')->getFlashBag()->add('error', 'NIP składa się z 10 cyfr, REGON z 9 a NR KLIENTA z 8')
                    )));
                } else {

                    return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                        $this->get('session')->getFlashBag()->add('error', 'NIP składa się z 10 cyfr, REGON z 9 a NR KLIENTA z 8')
                    )));
                }
            }
        }
        if ($konsultantlogin) {
            return $this->forward('InfogoldKlienciBundle:Klienci:kliencifirmowi', array(
                        $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
            ));
        } else {
            return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                $this->get('session')->getFlashBag()->add('error', 'Nie wprowadzono danych')
            )));
        }
    }

    public function NazwaKonsultantAction($nazwa, $konsultant, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();
        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nazwaklienta LIKE ?1')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, '%'.$nazwa.'%')
                ->setParameter(2, $konsultant)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego  o podanej nazwie firmy dla konsultanta ' . $konsultant)
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {

                //  $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego  o podanej nazwie firmy dla konsultanta ' . $konsultant);
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego  o podanej nazwie firmy dla konsultanta ' . $konsultant)
                )));
            }
        }
    }

    public function NipKonsultantAction($nip, $konsultant, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();
        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta= ?1')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, $nip)
                ->setParameter(2, $konsultant)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze NIP dla konsultanta ' . $konsultant)
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze NIP dla konsultanta ' . $konsultant)
                )));
            }
        }
    }

    public function RegonKonsultantAction($regon, $konsultant, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();
        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.regonklienta= ?1')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, $regon)
                ->setParameter(2, $konsultant)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze REGON dla konsultanta ' . $konsultant)
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze REGON dla konsultanta ' . $konsultant)
                )));
            }
        }
    }

    public function NipAction($nip, $userId, $form, $konsultantlogin) {
        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nipklienta= ?1')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, $nip)
                ->getQuery();

        $entities2 = $query2->getResult();
        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze NIP')
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze NIP')
                )));
            }
        }
    }

    public function NrklientaAction($nrklienta, $userId, $form, $konsultantlogin) {

        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.numerklienta= ?1')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, $nrklienta)
                ->getQuery();

        $entities2 = $query2->getResult();

        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze klienta')
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze klenta')
                )));
            }
        }
    }

    public function NazwaAction($nazwa, $userId, $form, $konsultantlogin) {

        $em2 = $this->getDoctrine()->getManager();

        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');

        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.nazwaklienta LIKE ?1')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, '%'.$nazwa.'%')
                ->getQuery();


        $entities2 = $query2->getResult();

        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanej nazwie')
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanej nazwie')
                )));
            }
        }
    }

    public function RegonklientaAction($regon, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();



        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');

        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.regonklienta= ?1')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, $regon)
                ->getQuery();


        $entities2 = $query2->getResult();

        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze REGON')
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze REGON')
                )));
            }
        }
    }

    public function NrklientaKonsultantAction($nrklienta, $konsultant, $userId, $form, $konsultantlogin) {

        $em2 = $this->getDoctrine()->getManager();
        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request->createQueryBuilder('p');
        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.numerklienta= ?1')
                ->andWhere('p.KlientKonsultanta= ?2')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(1, $nrklienta)
                ->setParameter(2, $konsultant)
                ->getQuery();


        $entities2 = $query2->getResult();


        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze klienta od konsultanta ' . $konsultant)
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klienta firmowego o podanym numerze klenta od konsultanta ' . $konsultant)
                )));
            }
        }
    }

    public function TylkoKonsultantAction($konsultant, $userId, $form, $konsultantlogin) {


        $em2 = $this->getDoctrine()->getManager();
        $request = $em2->getRepository('InfogoldKlienciBundle:Klienci');
        $qb = $request->createQueryBuilder('p');


        $query2 = $qb
                ->select('p')
                ->leftJoin('p.user', 'c')
                ->where('c.Nrklienta=' . $userId)
                ->andWhere('p.KlientKonsultanta= ?2')
                ->andWhere('p.nipklienta IS NOT NULL')
                ->setParameter(2, $konsultant)
                ->getQuery();


        $entities2 = $query2->getResult();

        if ($konsultantlogin) {
            if ($entities2) {
                return $this->render('InfogoldKlienciBundle:Klienci:firmowi.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_firmowi', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klientów firmowych dla danego konsultanta')
                )));
            }
        } else {
            if ($entities2) {
                return $this->render('InfogoldAccountBundle:Baza:bazaklientowfirmowych.html.twig', array(
                            'entities' => $entities2,
                            'form' => $form->createView(),
                ));
            } else {
                return $this->redirect($this->generateUrl('klienci_baza_firma', array(
                                    $this->get('session')->getFlashBag()->add('error', 'Brak klientów firmowych dla danego konsultanta')
                )));
            }
        }
    }

}
