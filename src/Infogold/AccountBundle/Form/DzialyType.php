<?php

namespace Infogold\AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DzialyType extends AbstractType
{
     
    public $edit;
    
    public function __construct($edit = false) {
       
        $this->edit = $edit;
      
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $buttontext = ($this->edit) ? " Edytuj" : " Zapisz";
        
        $builder
            ->add('name')
            ->add('limityprzerw', 'integer', array('attr' => array('min' => 1, 'value'=> 1),
                'label' => 'Limit przerw'))
            ->add('submit', 'submit', array('label' => $buttontext, 'attr' => array('class' => 'btn-success btn-lg', 'icon' => 'check fa-fw')));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\AccountBundle\Entity\Dzialy'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'infogold_accountbundle_dzialy';
    }
}
