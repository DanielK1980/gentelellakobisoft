<?php

namespace Infogold\KlienciBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\SecurityContext;

class UniqueKlientValidator extends ConstraintValidator {

    protected $user;
    protected $entityManager;

    public function __construct(EntityManager $entityManager, SecurityContext $user) {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public function validate($value, Constraint $constraint) {
        if ($value){
            
        $zalogowany = $this->user->getToken()->getUser();
        $nrklienta = $zalogowany->getNrklienta();
         
      
        if ($this->context->getPropertyName() == "nipklienta" ){
            $string = "NIP";
        }
        else if ($this->context->getPropertyName() == "peselklienta" ){
            $string =  "PESEL";
        }
        else {
             $string =  "REGON";
        }
        //dla zalogowanego administratora
        if (get_class($zalogowany) == "Infogold\UserBundle\Entity\User") {

           $username = $this->entityManager->getRepository('InfogoldKlienciBundle:Klienci');
                     
            $useradmin = $this->entityManager->getRepository('InfogoldUserBundle:User');

            $getadmin = $useradmin->createQueryBuilder('p');
                    $query2 = $getadmin
                    ->select('p')
                    ->where('p.Nrklienta=' . $nrklienta)
                    ->andWhere('p.locked=false')
                    ->andWhere('p.roles LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%')
                    ->add('orderBy', 'p.id DESC')
                    ->getQuery();
            $admin = $query2->getOneOrNullResult();
                        
            $item = $username->findOneBy(array($this->context->getPropertyName() => $value, 'user' => $admin->getId()));

        //dla zalogowanego konsultanta
        } else {
            $user = $this->entityManager->getRepository('InfogoldKlienciBundle:Klienci');                               
            $item = $user->findOneBy(array($this->context->getPropertyName() => $value, 'user' => $zalogowany->getFirma()->getId()));
        }

        if ($item != null) {
        // the constraint does not pass
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => $string));
            return false;
        }
        }
// the constraint passes
        return true;
    }           

}
