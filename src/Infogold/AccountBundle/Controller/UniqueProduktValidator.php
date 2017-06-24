<?php

namespace Infogold\AccountBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RequestStack;

class UniqueProduktValidator extends ConstraintValidator {

    protected $user;
    protected $entityManager;
    protected $requestStack;

    public function __construct(EntityManager $entityManager, SecurityContext $user, RequestStack $requestStack) {
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->requestStack = $requestStack;
    }

    public function validate($value, Constraint $constraint) {
        if ($value) {
            $zalogowany = $this->user->getToken()->getUser();


            if ($this->context->getPropertyName() == "nrproduktu") {
                $string = "numerze";
            }
            //dla zalogowanego administratora
            if (get_class($zalogowany) == "Infogold\UserBundle\Entity\User") {

                $nrklienta = $zalogowany->getNrklienta();

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

                $hiddennrproduktu = null;
                
                $request = $this->requestStack->getCurrentRequest();
                if (isset($request->request->get('infogold_accountbundle_produktytype')['hiddennrproduktu'])){
                    $hiddennrproduktu = $request->request->get('infogold_accountbundle_produktytype')['hiddennrproduktu'];
                }

                if ($hiddennrproduktu == $value) {
                    $item = null;
                } else {
                    $nrproduktu = $this->entityManager->getRepository('InfogoldAccountBundle:Produkt');
                    $qb2 = $nrproduktu->createQueryBuilder('p');
                    $query2 = $qb2
                            ->select('p')
                            ->where('p.nrproduktu=:value')
                            ->andWhere('p.userproduktu=:admin')
                            ->setParameter('value', $value)
                            ->setParameter('admin', $user_admin->getId())
                            ->getQuery();
                    $item = $query2->getOneOrNullResult();
                }
            }

            if ($item != null) {
                // the constraint does not pass
                $this->context->addViolation(
                        $constraint->message, array('%string%' => $string));
                return false;
            }
        }
// the constraint passes
        return true;
    }

}
