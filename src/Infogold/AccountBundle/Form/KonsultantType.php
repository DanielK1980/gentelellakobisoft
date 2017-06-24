<?php

namespace Infogold\AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class KonsultantType extends AbstractType {

    protected $nrklienta;
    protected $edit;

    public function __construct($nrklienta = false,$edit = false) {

        $this->user = $nrklienta;
        $this->edit = $edit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

       // $zalogowany = $this->user->getToken()->getUser();
           $nrklienta = $this->user;
           
           $buttontext = ($this->edit) ? " Edytuj": " Dodaj";
           
        $builder
                ->add('imie',null, array(
                    'label' => 'Imie'))
                ->add('nazwisko')
                ->add('username',null, array(
                    'label' => 'Login'))
                ->add('email')
                ->add('KonsultantDzialy', 'entity', array(
                    'class' => 'InfogoldAccountBundle:Dzialy',
                    'query_builder' => function(EntityRepository $er) use ($nrklienta) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.DzialyFirmy', 'c')
                                ->where('c.Nrklienta=' . $nrklienta);
                    },
                    'required' => true,
                    'label' => 'Dział'
                ))
                ->add('submit', 'submit', array('label' =>$buttontext,'attr' => array('class' => 'btn-success btn-lg', 'icon' => 'check fa-lg')));
             //  ->add('KonsultantRoles', null, array('label' => 'Dostępy konsultanta: ', 'required' => true)
               
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\KonsultantBundle\Entity\Konsultant'
        ));
    }

    public function getName() {
        return 'konsultanci_form';
    }

}
