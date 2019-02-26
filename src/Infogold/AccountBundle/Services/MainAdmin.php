<?php

namespace Infogold\AccountBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;

class MainAdmin {

    protected $user;
    protected $entityManager;  

    public function __construct(EntityManager $entityManager, SecurityContext $user) {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    // POBIERA ADMINA GŁÓWNEGO 
    public function getMainAdmin() {

        $user_admin = null;
        
        $zalogowany = $this->user->getToken()->getUser();
        $nrklienta = $zalogowany->getNrklienta();

        if (get_class($zalogowany) == "Infogold\UserBundle\Entity\User") {

            $req = $this->entityManager->getRepository('InfogoldUserBundle:User');

            $qb = $req->createQueryBuilder('p');

            $query = $qb
                    ->select('p')
                    ->where('p.Nrklienta=' . $nrklienta)
                    ->andWhere('p.locked=false')
                    ->andWhere('p.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                    ->add('orderBy', 'p.id DESC')
                    ->getQuery();
            $user_admin = $query->getOneOrNullResult();
        }
        return $user_admin;
    }

    // POBIERA WSZYSTKICH ADMINÓW OPRÓCZ GŁÓWNEGO 
    public function getAdminsPaginate() {

        $query = null;

        $zalogowany = $this->user->getToken()->getUser();
        $nrklienta = $zalogowany->getNrklienta();

        if (get_class($zalogowany) == "Infogold\UserBundle\Entity\User") {

            $req = $this->entityManager->getRepository('InfogoldUserBundle:User');

            $qb = $req->createQueryBuilder('p');

            $query = $qb
                    ->select('p')
                    ->where('p.Nrklienta=' . $nrklienta)
                    ->andWhere('p.locked=false')
                    ->andWhere('p.roles NOT LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                    ->add('orderBy', 'p.id DESC')
                    ->getQuery();
            //  $user_admin = $query->getOneOrNullResult();
        }
        return $query;
    }

}
