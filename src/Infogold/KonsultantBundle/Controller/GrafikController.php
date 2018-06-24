<?php

namespace Infogold\KonsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GrafikController extends Controller {

    private function strftimeV($format, $timestamp) {
        return iconv("ISO-8859-2", "UTF-8", ucfirst(strftime($format, $timestamp)));
    }
    public function dzialAction($id, $date) {

        $User = $this->get('security.context')->getToken()->getUser();
        $IdUser = $User->getId();

        $em = $this->getDoctrine()->getManager();
        $req = $em->getRepository('InfogoldKonsultantBundle:Konsultant');
        $qb = $req->createQueryBuilder('p');
        $query = $qb
                ->where('p.KonsultantDzialy=' . $id)
                ->getQuery();

        $konsultanci = $query->getResult();

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
        function strftimeV($format, $timestamp) {
            return iconv("ISO-8859-2", "UTF-8", ucfirst(strftime($format, $timestamp)));
        }

        // What is the name of the month in question?
        $monthName = $this->strftimeV("%B", $firstDayOfMonth)." ".$dateComponents['year'];

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
                ->setParameter('konsultantid', $IdUser)
                ->getQuery();


        $grafiktabela = $query1->getResult();


        return $this->render('InfogoldKonsultantBundle:Konsultant:grafik.html.twig', array(
                    'konsultanci' => $konsultanci,
                    'rok' => $rok,
                    'miesiac' => $miesiac,
                    'nazwydni' => $nazwydni,
                    'ilosc' => $numberDays,
                    'pustedni' => $dayOfWeek,
                    'nazwamiesiaca' => $monthName,
                    'grafiktabela' => $grafiktabela,
                    'id' => $id
        ));
    }

}
