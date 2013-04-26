<?php
use Silex\ServiceProviderInterface;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Mparaiso\Provider\RouteConfigServiceProvider;
use Mparaiso\Provider\CrudServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Mparaiso\Provider\JobBoardServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Mparaiso\Provider\DoctrineORMServiceProvider;
use Mparaiso\Provider\ConsoleServiceProvider;
use Silex\Application;

class Config implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app->register(new ConsoleServiceProvider());
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new  SessionServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new TranslationServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $app->register(new HttpCacheServiceProvider, array(
            'http_cache.cache_dir' => __DIR__ . '/../temp/cache-dir'
        ));
        $app->register(new TwigServiceProvider(), array(
            "twig.path" => array(__DIR__ . "/Resources/views/"),
        ));
        $app->register(new UrlGeneratorServiceProvider);
        $app->register(new RouteConfigServiceProvider);

        $app->register(new DoctrineServiceProvider(), array(
            "db.options" => array(
                "driver" => "pdo_sqlite",
                "path"   => __DIR__ . "/database.sqlite"
            )
        ));
        $app->register(new DoctrineORMServiceProvider(), array(
            "orm.proxy_dir" => __DIR__ . "/Proxy",
        ));
        $app->register(new JobBoardServiceProvider());
        $app->register(new CrudServiceProvider);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}