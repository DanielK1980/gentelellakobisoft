<?php

namespace Infogold\AccountBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface {

    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'id' => 'menu',
            ),
        ));

        $menu->addChild('Home', array('route' => 'infogold_strona_homepage', 'attributes' => array('id' => 'back_to_homepage')));
        $menu->addChild('Zarejestruj się', array('route' => 'fos_user_registration_register'));


        // ... add more children

        return $menu;
    }

    public function accountMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'id' => 'menuadmin',
            ),
        ));

        $menu->addChild('Konsultanci', array('route' => 'konsultant', 'attributes' => array('id' => 'back_to_homepage')));
        $menu->addChild('Baza klientów', array('route' => 'baza_klientow'));
        $menu->addChild('Grafik', array('route' => 'grafik', 'routeParameters' => array('date' => date('Y-m'))));
        $menu->addChild('Działy', array('route' => 'dzialy'));
        $menu->addChild('Kategorie', array('route' => 'category_index'));
        $menu->addChild('Produkty', array('route' => 'produkty'));
        $menu->addChild('Faktury', array('route' => 'faktura_oryginal'));
       // $menu->addChild('Przerwy', array('route' => 'administrator_przerw'));
        //   administrator_przerw
        //   $menu->addChild('Statystyki i raporty', array('route' => 'statrap'));
        //  $menu->addChild('Wiadomości', array('route' => 'wiadomosci'));
        $menu->addChild('Ustawienia', array('route' => 'fos_user_profile_show'));

        // ... add more children

        return $menu;
    }

    public function konsultantMenu(FactoryInterface $factory, array $options) {
        
        $dzial = $this->container->get('security.token_storage')->getToken()->getUser()->getKonsultantDzialy()->getId();

        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'id' => 'menu',
            ),
        ));

        $menu->addChild('Strona główna', array('route' => 'kontakty_glowna'));
        $menu->addChild('Baza klientów', array('route' => 'klienci'));
        $menu->addChild('Produkty', array('route' => 'produkt_kons'));
        $menu->addChild('Przerwy', array('route' => 'przerwy'));
        $menu->addChild('Grafik', array('route' => 'grafikdzialukonsultanta', 'routeParameters' => array('id' => $dzial, 'date' => date('Y-m'))));


        // ... add more children

        return $menu;
    }

    public function ustawieniaMenu(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills nav-stacked',
            ),
        ));
        $request = $this->container->get('request');
        $routeName = $request->get('_route');

        $show = array('class' => '');
        if ($routeName == 'fos_user_profile_show') {
            $show = array('class' => 'active');
        }

        $edit = array('class' => '');
        if ($routeName == 'fos_user_profile_edit') {
            $edit = array('class' => 'active');
        }
        $pass = array('class' => '');
        if ($routeName == 'fos_user_change_password') {
            $pass = array('class' => 'active');
        }

        $set = array('class' => '');
        if ($routeName == 'ustawienia') {
            $set = array('class' => 'active');
        }

        $setmag = array('class' => '');
        if ($routeName == 'ustawienia_magazynu') {
            $setmag = array('class' => 'active');
        }

        $wgrajdane = ($routeName == 'wgraj_dane') ? array('class' => 'active') : array('class' => '');
        $rodzajekontaktow = ($routeName == 'rodzajekontaktow_index') ? array('class' => 'active') : array('class' => '');

        $menu->addChild('Twoje dane', array('route' => 'fos_user_profile_show', 'attributes' => $show));
        $menu->addChild('Edytuj swoje dane', array('route' => 'fos_user_profile_edit', 'attributes' => $edit));
        $menu->addChild('Zmień hasło', array('route' => 'fos_user_change_password', 'attributes' => $pass));

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Administratorzy', array('route' => 'ustawienia', 'attributes' => $set));
        }
        $menu->addChild('Ustawnienia magazynu', array('route' => 'ustawienia_magazynu', 'attributes' => $setmag));
        $menu->addChild('Wgraj dane', array('route' => 'wgraj_dane', 'attributes' => $wgrajdane));
        $menu->addChild('Rodzaje kontaktów', array('route' => 'rodzajekontaktow_index', 'attributes' => $rodzajekontaktow));

        // ... add more children

        return $menu;
    }

    public function Clients(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav side-menu',
            ),
        ));

        $menu->addChild('Baza klientów', array('uri' => '#', 'childrenAttributes' => array(
                'class' => 'nav child_menu',
    )))->setLabel('<i class="fa fa-users"></i>Baza klientów<span class="fa fa-chevron-down"></span>')->setExtra('safe_label', true);
        $menu['Baza klientów']->addChild('Klienci indywidualni', array('route' => 'baza_klientow'));
        $menu['Baza klientów']->addChild('Klienci firmowi', array('route' => 'klienci_baza_firma'));
        
        $menu->addChild('Faktury', array('uri' => '#', 'childrenAttributes' => array(
                'class' => 'nav child_menu',
    )))->setLabel('<i class="fa fa-file-text-o"></i>Faktury<span class="fa fa-chevron-down"></span>')->setExtra('safe_label', true);
        $menu['Faktury']->addChild('Faktury Oryginał', array('route' => 'faktura_oryginal'));
        $menu['Faktury']->addChild('Faktury Pro forma', array('route' => 'faktura_proforma'));
           

        return $menu;
    }
    public function Consultants(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav side-menu',
            ),
        ));

        $menu->addChild('Konsultanci', array('route' => 'konsultant'
        ))->setLabel('<i class="fa fa-address-book-o"></i>Konsultanci')->setExtra('safe_label', true);
        $menu->addChild('Grafik', array('route' => 'grafik', 'routeParameters' => array('date' => date('Y-m')
    )))->setLabel('<i class="fa fa-calendar"></i>Grafik')->setExtra('safe_label', true);
      /*  $menu->addChild('Przerwy', array('route' => 'administrator_przerw'
        ))->setLabel('<i class="fa fa-universal-access"></i>Przerwy')->setExtra('safe_label', true);
*/
        return $menu;
    }
    
      public function Products(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav side-menu',
            ),
        ));

        $menu->addChild('Działy', array('route' => 'dzialy'
        ))->setLabel('<i class="fa fa-building"></i>Działy')->setExtra('safe_label', true);
        $menu->addChild('Kategorie', array('route' => 'category_index'
        ))->setLabel('<i class="fa fa-sitemap"></i>Kategorie')->setExtra('safe_label', true);
        $menu->addChild('Produkty', array('route' => 'produkty'
        ))->setLabel('<i class="fa fa-database"></i>Produkty')->setExtra('safe_label', true);

        return $menu;
    }
    
     public function Settings(FactoryInterface $factory, array $options) {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav side-menu',
            ),
        ));

        $menu->addChild('Ustawienia', array('uri' => '#', 'childrenAttributes' => array(
                'class' => 'nav child_menu',
    )))->setLabel('<i class="fa fa-cogs"></i>Ustawienia<span class="fa fa-chevron-down"></span>')->setExtra('safe_label', true);

        $menu['Ustawienia']->addChild('Twoje dane', array('route' => 'fos_user_profile_show'));
        $menu['Ustawienia']->addChild('Edytuj swoje dane', array('route' => 'fos_user_profile_edit'));
        $menu['Ustawienia']->addChild('Zmień hasło', array('route' => 'fos_user_change_password'));

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $menu['Ustawienia']->addChild('Administratorzy', array('route' => 'ustawienia'));
        }
        $menu['Ustawienia']->addChild('Ustawnienia magazynu', array('route' => 'ustawienia_magazynu'));
        $menu['Ustawienia']->addChild('Wgraj dane', array('route' => 'wgraj_dane'));
        $menu['Ustawienia']->addChild('Rodzaje kontaktów', array('route' => 'rodzajekontaktow_index'));
        $menu['Ustawienia']->addChild('Allegro', array('route' => 'allegro_index'));


        return $menu;
    }


}
