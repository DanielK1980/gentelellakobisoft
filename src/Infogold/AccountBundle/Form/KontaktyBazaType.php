<?php

namespace Infogold\AccountBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityRepository;

class KontaktyBazaType extends AbstractType {

    public $nrklienta;

    public function __construct($nrklienta) {
        $this->nrklienta = $nrklienta;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $nr = $this->nrklienta;

        $builder
                ->add('OpisKontaktu', 'textarea', array(
                    'required' => true
                ))
                ->add('id', 'hidden', array(
                    'mapped' => false
                ))
                ->add('DaneKontaktowe')
                ->add('RodzajKontaktu', 'entity', array(
                    'class' => 'InfogoldKlienciBundle:RodzajeKontaktow',
                    'query_builder' => function(EntityRepository $er) use ($nr) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.company', 'c')
                                ->where('c.Nrklienta=' . $nr)
                                ->orderby('u.name');
                    },
                    'required' => true,
                    'empty_value' => 'Wybierz',
                ))
                ->add('CzasKontaktu', 'datetime', array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd hh:mm',
                    'read_only' => true
                ))
                ->add('PrzypisanyDo', 'entity', array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($nr) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.firma', 'c')
                                ->where('c.Nrklienta=' . $nr);
                    },
                    'required' => false,
                    'empty_value' => 'Administrator',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Infogold\KlienciBundle\Entity\Kontakty'
        ));
    }

    public function getName() {
        return 'infogold_accountbundle_kontaktytype';
    }

}
