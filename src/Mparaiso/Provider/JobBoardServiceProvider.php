<?php

namespace Mparaiso\Provider;

use Silex\ServiceProviderInterface;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Silex\Application;

class JobBoardServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app["mp.jobb.console"] = $app->share(function ($app) {
            return $app['console'];
        });
        # doctrine mapped super classes
        $app["mp.jobb.doctrine.entity.base.path"] =
            __DIR__ . "/../JobBoard/Resources/doctrine-base/";
        $app["mp.jobb.doctrine.entity.base.namespace"] = 'Mparaiso\JobBoard\Entity\Base';
        # doctrine entities
        $app["mp.jobb.doctrine.entity.path"] =
            __DIR__ . "/../JobBoard/Resources/doctrine/";
        $app["mp.jobb.doctrine.entity.namespace"] = 'Mparaiso\JobBoard\Entity';
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        $app["orm.chain_driver"]->addDriver(new YamlDriver($app['mp.jobb.doctrine.entity.base.path']), $app["mp.jobb.doctrine.entity.base.namespace"]);
        $app['orm.chain_driver']->addDriver(new YamlDriver($app['mp.jobb.doctrine.entity.path']), $app['mp.jobb.doctrine.entity.namespace']);
    }
}