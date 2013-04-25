<?php
use Silex\ServiceProviderInterface;
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
        $app->register(new DoctrineServiceProvider(), array(
            "db.options" => array(
                "driver" => "pdo_sqlite",
                "path"   => __DIR__ . "/database.sqlite"
            )
        ));
        $app->register(new DoctrineORMServiceProvider());
        $app->register(new JobBoardServiceProvider());
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}