<?php

namespace Infogold\KonsultantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChangePasswordType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('oldpassword', 'password', array('always_empty' => true, 'label' => 'Stare hasło'))
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Powtórzone hasło nie zgadza się z nowym',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options' => array('label' => 'Nowe hasło'),
                    'second_options' => array('label' => 'Powtórz nowe hasło'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\KonsultantBundle\Entity\Konsultant'
        ));
    }

    public function getName() {
        return 'infogold_konsultantbundle_changepasswordtype';
    }

}
