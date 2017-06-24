<?php

namespace Infogold\KlienciBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityRepository;

class ProduktyKlientaType extends AbstractType
{
    protected $user;

    public function __construct(SecurityContext $user) {
        
        $this->user = $user;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $zalogowany = $this->user->getToken()->getUser();
        
        
        $userId = $zalogowany->getNrklienta();
        
        $builder
            ->add('produkty','entity', array(
                    'class' => 'InfogoldAccountBundle:Produkt',
                    'query_builder' => function(EntityRepository $er) use ($userId) {

                return $er->createQueryBuilder('u')
                        ->leftJoin('u.userproduktu', 'c')
                        ->where('c.Nrklienta=' . $userId);
            },
                   'empty_value' => 'Wybierz',
                    'required' => true  ))       
            ->add('cenaProduktu', 'money', array(
                    'currency'=>'PLN',
                    'label' => 'Cena netto',
                    'grouping' => false
                   ))
            ->add('ilosc', 'integer', array('attr' => array('min' => 1, 'value'=> 1)))
             ->add('vat', 'hidden', array(
                    'mapped' => false
                   ))
               ->add('jednostkamiary', 'hidden', array(
                    'mapped' => false
                   )      
                     );
            
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\KlienciBundle\Entity\ProduktyKlienta'
        ));
    }

    public function getName()
    {
        return 'infogold_produkty_klienta';
    }
}
