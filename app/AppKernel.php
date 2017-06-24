<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
         //   new AppBundle\AppBundle(),
            new Infogold\UserBundle\InfogoldUserBundle(),
            new Infogold\StronaBundle\InfogoldStronaBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Infogold\AjaxBundle\InfogoldAjaxBundle(),
            new Infogold\AccountBundle\InfogoldAccountBundle(),
            new Infogold\KonsultantBundle\InfogoldKonsultantBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Infogold\KlienciBundle\InfogoldKlienciBundle(),
            new Infogold\AdminBundle\InfogoldAdminBundle(),
            new Infogold\SearchBundle\InfogoldSearchBundle(),         
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Ddeboer\DataImportBundle\DdeboerDataImportBundle()
            //ddeboer/data-import suggests installing phpoffice/phpexcel (If you want to use the ExcelReader)
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
