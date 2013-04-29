<?php

namespace Mparaiso\Provider;

use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Mparaiso\JobBoard\Controller\CategoryController;
use Mparaiso\JobBoard\Service\CategoryService;
use Mparaiso\JobBoard\Controller\JobController;
use Mparaiso\JobBoard\Service\JobService;
use Mparaiso\CodeGeneration\Controller\CRUD;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Silex\Application;
use Mparaiso\JobBoard\Event\JobEvents;
use Mparaiso\JobBoard\Entity\Base\Job as BaseJob;

/**
 * FR : fournit un service de site recherche d'emploi
 * EN : provide a job search site
 * @author M.PARAISO <mparaiso@online.fr>
 */
class JobBoardServiceProvider implements ServiceProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function register(Application $app) {
        # controllers
        $app['mp.jobb.controller.job'] = $app->share(function ($app) {
                    return new JobController($app['mp.jobb.service.job']);
                });
        $app['mp.jobb.controller.category'] = $app->share(function ($app) {
                    return new CategoryController;
                });
        $app['mp.jobb.controller.admin.job'] = $app->share(function ($app) {
                    return new CRUD(array(
                        "entityClass" => $app['mp.jobb.entity.job'],
                        "formClass" => $app['mp.jobb.form.job'],
                        "service" => $app['mp.jobb.service.job'],
                        "resourceName" => "job",
                        "templateLayout" => $app['mp.jobb.templates.crud.layout'],
                        "propertyList" => array('id','category', 'position', 'location'),
                        "orderList"=>array('id','position',"location"),
                        "beforeCreateEvent" => JobEvents::BEFORE_CREATE,
                        "beforeUpdateEvent" => JobEvents::BEFORE_UPDATE,
                    ));
                });

        $app['mp.jobb.controller.admin.category'] = $app->share(function ($app) {
                    return new CRUD(array(
                        "entityClass" => $app['mp.jobb.entity.category'],
                        "formClass" => $app['mp.jobb.form.category'],
                        "service" => $app['mp.jobb.service.category'],
                        "resourceName" => "category",
                        "propertyList"=>array('id','name'),
                        "orderList"=>array('id','name'),
                        "templateLayout" => $app['mp.jobb.templates.crud.layout']
                    ));
                });
        # entity services
        $app['mp.jobb.service.job'] = $app->share(function ($app) {
                    $service = new JobService($app['mp.jobb.orm.em'], $app['mp.jobb.entity.job'], $app['mp.jobb.entity.category']);
                    $service->setTokenGen($app['mp.jobb.function.create_token']);
                    return $service;
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
        $app['mp.jobb.form.category'] = 'Mparaiso\JobBoard\Form\CategoryType';

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
        $app['mp.jobb.templates.layout'] = "mp.jobb.layout.html.twig";
        $app['mp.jobb.templates.crud.layout'] = "mp.jobb.crud.layout.html.twig";
        # params
        $app['mp.jobb.params.job_active_days'] = 30;
        $app['mp.jobb.params.max_jobs_on_homepage'] = 10;
        $app['mp.jobb.params.max_jobs_on_category'] = 20;
        $app['mp.jobb.params.upload_dir'] = __DIR__ . '/../../../web/upload/';
        $app['mp.jobb.params.upload_server_path'] = "/upload/";
        /**
         * TWIG CONFIGURATION
         */
        $app['twig'] = $app->share($app->extend("twig", function ($twig, $app) {
                            /* @var $twig \Twig_Environment */
                            #@note FR : créer un filter twig
                            $twig->addFilter(new \Twig_SimpleFilter("slugify", function ($string, $separator = "-") {
                                        return preg_replace('/[^\w\d]/i', $separator, $string);
                                    }));
                            $twig->addFilter(new \Twig_SimpleFilter("floor", function ($float) {
                                        if (!is_float($float))
                                            throw new \Exception("value must be a float");
                                        return floor($float);
                                    }));
                            $twig->addFunction(new \Twig_SimpleFunction("mp_jobb_get_path", function ($id) use ($app) {
                                        $realfile = $app['mp.jobb.params.upload_dir'] . $id;
                                        if (file_exists($realfile) && is_file($realfile)) {
                                            return $app['mp.jobb.params.upload_server_path'] . $id;
                                        } else {
                                            return NULL;
                                        }
                                    }));
                            return $twig;
                        }));
        /**
         * EVENT LISTENERS
         */
        $app['mp.jobb.listener.job.after_create'] = $app->protect(
                /**
                 * FR :  créer le token et ajoute un message flash avec la valeur du token
                 * EN :  create the token and add a flash message with the token value
                 */
                function(GenericEvent $event)use($app) {
                    if (isset($app["session"])) {
                        $job = $event->getSubject();
                        $app['session']->getFlashBag()
                                ->add('info', "Manage your job offer with the following url : \n" . $app['url_generator']
                                        ->generate('job_edit', array("token" => $job->getToken()),true));
                    }
                }
        );
        $app['mp.jobb.listener.job.upload_image_before_save'] = $app->protect(
                /**
                 * EN : upload the company logo picture
                 * FR : charge le logo de l'entreprise
                 */
                function (GenericEvent $event) use ($app) {
                    $form = $event->getArgument('form');
                    $logoFile = $form->get('logo_file')->getData();
                    if ($logoFile != NULL) {
                        $job = $event->getSubject();
                        /* @var $logoFile UploadedFile */
                        $logoFileName = uniqid() . "_" . $logoFile->getClientOriginalName();
                        $logoFile->move(
                                $app['mp.jobb.params.upload_dir'], $logoFileName
                        );
                        $job->setLogo($logoFileName);
                    }
                }
        );
        $app['mp.jobb.function.create_token'] = $app->protect(function(BaseJob $job) {
                    return sha1($job->getEmail() . md5(uniqid()));
                }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app) {

        $app["orm.chain_driver"]->addDriver(
                new YamlDriver($app['mp.jobb.doctrine.entity.base.path']), $app["mp.jobb.doctrine.entity.base.namespace"]);
        $app['orm.chain_driver']->addDriver(
                new YamlDriver($app['mp.jobb.doctrine.entity.path']), $app['mp.jobb.doctrine.entity.namespace']);
        $app['mp.route_loader']->append(array(
            array("type" => "yaml",
                "path" => __DIR__ . "/../JobBoard/Resources/routing/routes.yml",
                "prefix" => "/"))
        );
        $app['twig.loader.filesystem']->addPath($app["mp.jobb.templates.path"]);

        $app['dispatcher']->addListener(JobEvents::BEFORE_CREATE, $app['mp.jobb.listener.job.upload_image_before_save']);
        $app['dispatcher']->addListener(JobEvents::BEFORE_UPDATE, $app['mp.jobb.listener.job.upload_image_before_save']);
        $app['dispatcher']->addListener(JobEvents::AFTER_CREATE, $app['mp.jobb.listener.job.after_create']);
    }

}
