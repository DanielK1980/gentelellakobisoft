<?php

namespace Infogold\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nrklienta')
            ->add('imie')
            ->add('nazwisko')
            ->add('telefon')
            ->add('nazwafirmy')
            ->add('kodpocztowy')
            ->add('miejscowosc')
            ->add('ulica')
            ->add('nip')
            ->add('nrkonta')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'infogold_userbundle_user';
    }
}
