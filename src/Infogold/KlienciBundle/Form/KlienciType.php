<?php

namespace Infogold\KlienciBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KlienciType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numerklienta', 'text', array('label' => 'Nr klienta'))          
            ->add('telefonklienta', 'text', array('label' => 'Telefon'))
            ->add('emailklienta', 'email', array('label' => 'Email'))                 
            ->add('peselklienta', 'text', array('label' => 'Pesel'))
            ->add('imie', 'text', array('label' => 'Imie'))
            ->add('nazwisko', 'text', array('label' => 'Nazwisko'))
            ->add('ulica', 'text', array('label' => 'Ulica'))
            ->add('nrdomu', 'text', array('label' => 'Nr domu'))
            ->add('nrmieszkania', 'text', array('label' => 'Nr mieszkania','required' => false))
            ->add('kodpocztowy', 'text', array('label' => 'Kod pocztowy'))
            ->add('miasto', 'text', array('label' => 'Miasto'))
            ->add('wojewodztwo', 'text', array('label' => 'Województwo'))
             ->add('submit', 'submit', array('label' =>'Zapisz','attr' => array('class' => 'btn-success btn-lg col-md-offset-3', 'icon' => 'check fa-lg')));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\KlienciBundle\Entity\Klienci',
            'validation_groups' => array('prywatni'),
        ));
    }

    public function getName()
    {
        return 'infogold_kliencibundle_kliencitype';
    }
}
