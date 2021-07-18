<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Alpdesk\AlpdeskCore\AlpdeskCoreBundle;
use Alpdesk\AlpdeskFootball\AlpdeskFootballBundle;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [BundleConfig::create(AlpdeskFootballBundle::class)->setLoadAfter([ContaoCoreBundle::class, AlpdeskCoreBundle::class])];
    }

    /**
     * @param LoaderResolverInterface $resolver
     * @param KernelInterface $kernel
     * @return mixed|\Symfony\Component\Routing\RouteCollection|null
     * @throws \Exception
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        $file = __DIR__ . '/../Resources/config/routes.yml';
        return $resolver->resolve($file)->load($file);
    }
}
