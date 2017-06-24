<?php

namespace Infogold\KlienciBundle\Controller;

use Symfony\Component\Validator\Constraint;

class UniqueKlient extends Constraint
{
    
public $message = 'Klient o podanym %string% już istnieje';


public function validatedBy()
{
return 'validator.unique';
}

}