<?php

namespace Infogold\AccountBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Konsultant implements ContainerAwareInterface {

    use ContainerAwareTrait;

    public function Konsultant(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav side-menu',
            ),
        ));

         $dzial = $this->container->get('security.token_storage')->getToken()->getUser()->getKonsultantDzialy()->getId();
         
        $menu->addChild('kon', array('route' => 'kontakty_glowna'
        ))->setLabel('<i class="fa fa-address-card"></i>Kontakty')->setExtra('safe_label', true);
        $menu->addChild('kind', array('route' => 'klienci'
        ))->setLabel('<i class="fa fa-user"></i>Moi klienci ind.')->setExtra('safe_label', true);
        $menu->addChild('kfirm', array('route' => 'klienci_firmowi'
        ))->setLabel('<i class="fa fa-building"></i>Moi klienci firmowi')->setExtra('safe_label', true);
        $menu->addChild('bind', array('route' => 'klienci_all_ind'
        ))->setLabel('<i class="fa fa-users"></i>Baza klientów ind.')->setExtra('safe_label', true);
        $menu->addChild('bfirm', array('route' => 'klienci_all_firma'
        ))->setLabel('<i class="fa fa-industry"></i>Baza klientów firmowych')->setExtra('safe_label', true);
        $menu->addChild('produkty', array('route' => 'produkt_kons'
        ))->setLabel('<i class="fa fa-product-hunt"></i>Produkty')->setExtra('safe_label', true);
        $menu->addChild('grafik', array('route' => 'grafikdzialukonsultanta', 'routeParameters' => array('id' => $dzial, 'date' => date('Y-m'))
        ))->setLabel('<i class="fa fa-calendar"></i>Grafik')->setExtra('safe_label', true);
       

        return $menu;
    }


}
