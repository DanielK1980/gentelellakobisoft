<?php

namespace Infogold\KlienciBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KlienciFirmowiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numerklienta', 'text', array('label' => 'Nr klienta'))
            ->add('imie', 'text', array('label' => 'Imie'))         
            ->add('nazwisko', 'text', array('label' => 'Nazwisko')) 
            ->add('nazwaklienta', 'text', array('label' => 'Nazwa firmy'))         
            ->add('telefonklienta', 'text', array('label' => 'Nr telefonu'))
            ->add('emailklienta', 'text', array('label' => 'Email'))
            ->add('nipklienta', 'text', array('label' => 'NIP'))
            ->add('regonklienta', 'text', array('label' => 'REGON'))                                          
            ->add('ulica', 'text', array('label' => 'Ulica'))          
            ->add('nrdomu', 'text', array('label' => 'Nr domu'))          
            ->add('nrmieszkania', 'text', array('label' => 'Nr lokalu'))           
            ->add('kodpocztowy', 'text', array('label' => 'Kod pocztowy'))          
            ->add('miasto', 'text', array('label' => 'Miasto'))           
            ->add('wojewodztwo', 'text', array('label' => 'Województwo'))
            ->add('stronawwwklienta', 'text', array('label' => 'Strona www'))
            ->add('submit', 'submit', array('label' =>'Zapisz','attr' => array('class' => 'btn-success btn-lg col-md-offset-3', 'icon' => 'check fa-lg')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\KlienciBundle\Entity\Klienci',
            'validation_groups' => array('firma'),
        ));
    }

    public function getName()
    {
        return 'infogold_kliencibundle_kliencifirmowitype';
    }
}
