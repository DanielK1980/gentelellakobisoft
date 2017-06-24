<?php

namespace Infogold\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Infogold\AccountBundle\Entity\Dzialy;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RegisterListener implements EventSubscriberInterface {

    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function numer() {

        $er = $this->em->getRepository('InfogoldUserBundle:User');

        $qb = $er->createQueryBuilder('s');
        $query = $qb
                ->select('MAX(s.Nrklienta) AS max_score')
                ->getQuery();


        $entities = $query->getSingleResult();

        foreach ($entities as $wartosc) {


            return $wartosc + 1;
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegisterCompleted',
        );
    }

    public function onRegistrationSuccess(FormEvent $event) {

        $user = $event->getForm()->getData();
        $nip = $user->getNip();

        $nipbase = $this->em->getRepository('InfogoldUserBundle:User')->findOneByNip($nip);

        if ($nipbase) {
            $user->setNrklienta($nipbase->getNrklienta());
            //  $user->addRole('ROLE_USER');
            $user->setEnabled(false);
            $user->setLocked(true);
            $user->setenableMagazyn($nipbase->getMagazyn());
        } else {
            $user->setNrklienta($this->numer());
            $user->setEnabled(false);
            $user->addRole('ROLE_ADMIN');
            $user->setenableMagazyn(false);
        }
    }

    public function onRegisterCompleted(FilterUserResponseEvent $event) {


        $user = $event->getUser();

        $role = $user->getRoles();

        if (isset($role[0]) && in_array("ROLE_ADMIN", $role)) {
            $pierwszydzial = new Dzialy();
            $pierwszydzial->setDzialyFirmy($user);
            $pierwszydzial->setLimityprzerw(4);
            $pierwszydzial->setName('Pierwszy dział');
            $er = $this->em;
            $er->persist($pierwszydzial);
            $er->flush();
        }
    }

}
