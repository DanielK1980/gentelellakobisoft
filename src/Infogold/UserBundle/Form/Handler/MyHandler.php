<?php

namespace Infogold\UserBundle\Form\Handler;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Infogold\AccountBundle\Entity\Dzialy;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Form\Handler\RegistrationFormHandler;
use Doctrine\ORM\EntityManager;

class MyHandler extends RegistrationFormHandler {

    protected $em;

    public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, EntityManager $em) {
        parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
        $this->em = $em;
    }

    public function numer() {


        $request = $this->em->getRepository('InfogoldUserBundle:User');





        $qb = $request->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.Nrklienta) AS max_score')
                ->getQuery();


        $entities = $query->getSingleResult();

        foreach ($entities as $wartosc) {


            return $wartosc + 1;

        }
    }

    public function domena() {


        $key = '';

        $keys = range('A', 'Z');

        for ($i = 0; $i < 2; $i++) {

            $key .= $keys[array_rand($keys)];
        }
        $request = $this->em->getRepository('InfogoldUserBundle:User');

        $domena = $request->findOneBy(array('domena' => $key));

        if ($domena) {

            return domena();
        }


        return $key;
# Tu możesz z wartością zrobić, czego tylko dusza zapragnie;)
    }

    protected function onSuccess(UserInterface $user, $confirmation) {


        $user->setNrklienta($this->numer());
        $user->setDomena($this->domena());
        
        
        
        
        parent::onSuccess($user, $confirmation);
        // your code
        
        
        $pierwszydzial = new Dzialy();
        $pierwszydzial->setDzialyFirmy($user);
        $pierwszydzial->setLimityprzerw(4);
        $pierwszydzial->setName('Pierwszy dział');
        $em = $this->em;
        $em->persist($pierwszydzial);
        $em->flush();
    }

}
