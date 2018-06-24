<?php

namespace Infogold\KlienciBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FakturaType extends AbstractType {

    public $kondultant;

    public function __construct($kondultant = false) {

        $this->kondultant = $kondultant;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        if ($this->kondultant) {
            $arrtypess = array(2 => 'Pro Forma');
        } else {
            $arrtypess = array(2 => 'Pro Forma', 1 => 'Oryginał');
        }

        $builder
                ->add('rodzaj', 'choice', array(
                    'empty_value' => 'Wybierz',
                    'choices' => $arrtypess))
                ->add('nrfaktury')
                ->add('datafaktury', 'datetime', array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'read_only' => true
                ))
                ->add('terminplatnosci', 'datetime', array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'read_only' => true
                ))
                ->add('platnosc');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\KlienciBundle\Entity\Faktura'
        ));
    }

    public function getName() {
        return 'infogold_kliencibundle_fakturatype';
    }

}
