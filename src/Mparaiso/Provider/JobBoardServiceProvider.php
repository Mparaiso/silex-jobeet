<?php

namespace Mparaiso\Provider;

use Silex\ServiceProviderInterface;
use Mparaiso\JobBoard\Controller\CategoryController;
use Mparaiso\JobBoard\Service\CategoryService;
use Gedmo\Sluggable\SluggableListener;
use Mparaiso\JobBoard\Controller\JobController;
use Mparaiso\JobBoard\Service\JobService;
use Mparaiso\CodeGeneration\Controller\CRUD;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Silex\Application;

class JobBoardServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        # controllers
        $app['mp.jobb.controller.job'] = $app->share(function ($app) {
            return new JobController($app['mp.jobb.service.job']);
        });
        $app['mp.jobb.controller.category']= $app->share(function($app){
        return new CategoryController;
        });
        $app['mp.jobb.controller.admin.job'] = $app->share(function ($app) {
            return new CRUD(array(
                "entityClass"  => $app['mp.jobb.entity.job'],
                "formClass"    => $app['mp.jobb.form.job'],
                "serviceName"  => 'mp.jobb.service.job',
                "resourceName" => "job",
            ));
        });
        # entity services
        $app['mp.jobb.service.job'] = $app->share(function ($app) {
            return new JobService($app['mp.jobb.orm.em'], $app['mp.jobb.entity.job'], $app['mp.jobb.entity.category']);
        });
        $app['mp.jobb.service.category'] = $app->share(function ($app) {
            return new CategoryService($app['mp.jobb.orm.em'], $app['mp.jobb.entity.category'], $app['mp.jobb.entity.job']);
        });
        $app["mp.jobb.console"] = $app->share(function ($app) {
            return $app['console'];
        });
        # orm
        $app['mp.jobb.orm.em'] = $app->share(function ($app) {
            return $app['orm.em'];
        });
        $app['mp.jobb.entity.job'] = 'Mparaiso\JobBoard\Entity\Job';
        $app['mp.jobb.form.job'] = 'Mparaiso\JobBoard\Form\JobType';

        $app['mp.jobb.entity.category'] = 'Mparaiso\JobBoard\Entity\Category';
        $app['mp.jobb.entity.affiliate'] = 'Mparaiso\JobBoard\Entity\Affiliate';

        # doctrine mapped super classes
        $app["mp.jobb.doctrine.entity.base.path"] =
            __DIR__ . "/../JobBoard/Resources/doctrine-base/";
        $app["mp.jobb.doctrine.entity.base.namespace"] = 'Mparaiso\JobBoard\Entity\Base';
        # doctrine entities
        $app["mp.jobb.doctrine.entity.path"] =
            __DIR__ . "/../JobBoard/Resources/doctrine/";
        $app["mp.jobb.doctrine.entity.namespace"] = 'Mparaiso\JobBoard\Entity';
        # templates
        $app['mp.jobb.templates.path'] = __DIR__ . '/../JobBoard/Resources/views/';
        $app['mp.jobb.templates.admin_path'] = __DIR__ . '/../JobBoard/Resources/views/admin';
        $app['mp.jobb.templates.layout'] = "mp.jobb.layout.html.twig";

        # params
        $app['mp.jobb.params.max_jobs_on_homepage'] = 10;

        $app['twig'] = $app->share($app->extend("twig", function ($twig, $app) {
            /* @var $twig \Twig_Environment */
            #@note FR : créer un filter twig
            $twig->addFilter(new \Twig_SimpleFilter("slugify", function ($string, $separator = "-") {
                return preg_replace('/[^\w\d]/i', $separator, $string);
            }));
            return $twig;
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {

        $app["orm.chain_driver"]->addDriver(
            new YamlDriver($app['mp.jobb.doctrine.entity.base.path']),
            $app["mp.jobb.doctrine.entity.base.namespace"]);
        $app['orm.chain_driver']->addDriver(
            new YamlDriver($app['mp.jobb.doctrine.entity.path']),
            $app['mp.jobb.doctrine.entity.namespace']);
        $app['mp.route_loader']->append(array(
                array("type"   => "yaml",
                      "path"   => __DIR__ . "/../JobBoard/Resources/routing/routes.yml",
                      "prefix" => "/"))
        );
        $app['twig.loader.filesystem']->addPath($app["mp.jobb.templates.path"]);
        $app['twig.loader.filesystem']->addPath($app["mp.jobb.templates.admin_path"]);
    }
}
