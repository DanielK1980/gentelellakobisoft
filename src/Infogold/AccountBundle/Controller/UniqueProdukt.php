<?php

namespace Infogold\AccountBundle\Controller;

use Symfony\Component\Validator\Constraint;

class UniqueProdukt extends Constraint {

    public $message = 'Produkt o podanym %string% już istnieje';

    public function validatedBy() {
        return 'validatorprodukt.unique';
    }

}
