<?php

namespace Infogold\AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProduktyType extends AbstractType {

    public $pass;
    public $edit;
    public $user;

    public function __construct($user, $bypass = false, $edit = false) {
        $this->pass = $bypass;
        $this->edit = $edit;
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $nrklienta = $this->user;
        $builder
                ->add('category', 'entity', array(
                    'class' => 'InfogoldAccountBundle:Category',
                    'query_builder' => function(EntityRepository $er) use ($nrklienta) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.company', 'c')
                                ->where('c.Nrklienta=' . $nrklienta)
                                ->orderBy('u.name');
                    },
                    'required' => true,
                    'attr' => array('widget_col' => 4, 'col_size' => 'md'),
                    'label' => 'Kategorie'
                ))
                ->add('nrproduktu', null, array('attr' => array('widget_col' => 4, 'col_size' => 'md'),
                    'label' => 'Nr produktu'))
                ->add('name', null, array('attr' => array('widget_col' => 4, 'col_size' => 'md'),
                    'label' => 'Nazwa'))
                ->add('cenaProduktu', null, array(
                    'label' => 'Cena (netto) ',
                    'attr' => array('widget_col' => 4, 'input_group' => array(
                            'prepend' => 'PLN'
                        ))
                ))
                ->add('vat', null, array('attr' => array('widget_col' => 4, 'col_size' => 'md'),
                    'label' => 'VAT'))
                ->add('jednostkamiary', null, array('attr' => array('widget_col' => 4, 'col_size' => 'md'),
                    'label' => 'J.M'))
                ->add('opis', null, array('attr' => array('widget_col' => 4, 'col_size' => 'md'),
                    'label' => 'Opis'));


        if ($this->pass) {
            $builder->add('magazyn', 'integer', array('attr' => array('min' => 1, 'widget_col' => 4, 'col_size' => 'md')));
        }
        if ($this->edit) {
            $builder->add('hiddennrproduktu', 'hidden', array(
                'data' => $this->edit,
                'mapped' => false
            ));
        }
        $buttontext = ($this->edit) ? " Edytuj" : " Dodaj";

        $builder->add('submit', 'submit', array('label' => $buttontext, 'attr' => array('class' => 'btn-success btn-lg', 'icon' => 'ok')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\AccountBundle\Entity\Produkt',
            'validation_groups' => array('new', 'edit')
        ));
    }

    public function getName() {
        return 'infogold_accountbundle_produktytype';
    }

}
