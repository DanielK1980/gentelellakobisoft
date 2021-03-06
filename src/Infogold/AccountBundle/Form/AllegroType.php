<?php

namespace Infogold\AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllegroType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    
    public $edit;
  
    public function __construct($edit = false) {  
        $this->edit = $edit;      
    }  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('AllegroClientID',null, array('label' => 'Client ID / klucz WebAPI'));
        $builder->add('AllegroClientSecret',null, array('label' => 'Client Secret'));
        /*
       if ($this->edit) {
            $builder->add('hiddennrklienta', 'hidden', array(
                'data' => $this->edit,
                'mapped' => false
            ));
        }
        */
        $buttontext = ($this->edit) ? " Edytuj" : " Zapisz";
        $builder->add('submit', 'submit', array('label' => $buttontext, 'attr' => array('class' => 'btn-success btn-lg', 'icon' => 'ok')));
                  
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\AccountBundle\Entity\Allegro'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'infogold_userbundle_allegro';
    }


}
