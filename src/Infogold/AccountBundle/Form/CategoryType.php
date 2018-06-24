<?php

namespace Infogold\AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class CategoryType extends AbstractType
{
    public $user;
    public $edit;
    public $allegro;

    public function __construct($user, $edit = false, $allegro = false) {
        $this->user = $user;
         $this->edit = $edit;
         $this->allegro = $allegro;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $buttontext = ($this->edit) ? " Edytuj" : " Zapisz";
        $nrklienta = $this->user;
        $builder
            ->add('name', null, array('label' => 'Nazwa'))
            ->add('department', 'entity', array(
                    'class' => 'InfogoldAccountBundle:Dzialy',
                    'query_builder' => function(EntityRepository $er) use ($nrklienta) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.DzialyFirmy', 'c')
                                ->where('c.Nrklienta=' . $nrklienta);
                    },
                    'required' => true,
                    'label' => 'Dział'
                ));
              if($this->allegro){
                 $builder->add('itemCategoryIdAllegro', null, array('label' => 'Nazwa kategorii w Allegro'));  
               };
                            
             $builder->add('submit', 'submit', array('label' => $buttontext, 'attr' => array('class' => 'btn-success btn-lg', 'icon' => 'check fa-fw')));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\AccountBundle\Entity\Category'
        ));
    }
}
