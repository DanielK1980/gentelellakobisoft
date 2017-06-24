<?php

namespace Infogold\KlienciBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class KontaktyType extends AbstractType {

    protected $user;

    public function __construct(SecurityContext $user) {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $zalogowany = $this->user->getToken()->getUser();

        $userId = $zalogowany->getFirma()->getId();
        $nrklienta = $zalogowany->getFirma()->getNrklienta();
        $builder
                ->add('OpisKontaktu', 'textarea', array(
                    'required' => true
                ))
                ->add('id', 'hidden', array(
                    'mapped' => false
                ))
                ->add('DaneKontaktowe', null, array(
                    'required' => true
                ))
                ->add('RodzajKontaktu', 'entity', array(
                    'class' => 'InfogoldKlienciBundle:RodzajeKontaktow',
                    'query_builder' => function(EntityRepository $er) use ($nrklienta) {

                        return $er->createQueryBuilder('u')
                                ->leftJoin('u.company', 'c')
                                ->where('c.Nrklienta=' . $nrklienta)
                                ->orderby('u.name');
                    },
                    'required' => true,
                    'empty_value' => 'Wybierz',
                ))
                ->add('CzasKontaktu', 'datetime', array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd hh:mm',
                    'read_only' => true,
                    'required' => true
                ))
                ->add('PrzypisanyDo', EntityType::class, array(
                    'class' => 'InfogoldKonsultantBundle:Konsultant',
                    'query_builder' => function(EntityRepository $er) use ($userId) {

                        return $er->createQueryBuilder('u', 'c')
                                ->leftJoin('u.firma', 'c')
                                ->where('c=' . $userId)
                                ->select(array('u', 'c'));
                    },
                            'preferred_choices' => array($zalogowany),
                            'required' => false,
                            'empty_value' => 'Administrator'
                ));
            }

            public function setDefaultOptions(OptionsResolverInterface $resolver) {
                $resolver->setDefaults(array(
                    'data_class' => 'Infogold\KlienciBundle\Entity\Kontakty'
                ));
            }

            public function getName() {
                return 'infogold_kliencibundle_kontaktytype';
            }

        }
        