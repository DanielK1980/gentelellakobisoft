<?php

namespace Infogold\UserBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;

class User  {
    
    protected $em;

    public function __construct(SecurityContext $em) {
                      
        $this->em = $em;
            
    }
    
    public function zalogowany() {

        $user = $this->em->getToken()->getUser();

        return $user;
    }

}
