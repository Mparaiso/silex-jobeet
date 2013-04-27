<?php
use Silex\Application;
use Mparaiso\Doctrine\ORM\FixtureLoader;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;

/**
 * @author M.Paraiso <mparaiso@online.fr>
 * test bootstrap file
 */

 $autoload = require __DIR__."/../vendor/autoload.php";

class Bootstrap
{
    /**
     * instanciate the application , create a in memory database
     * @return Application
     */
    static function getApp()
    {
        $app = new App(['debug' => TRUE]);
        $app["db.options"] = [
            "driver" => "pdo_sqlite",
            "memory" => TRUE
        ];
        $app->boot();
        self::createDatabase($app);
        return $app;
    }

    /**
     * EN : get fixture loader
     * @return Mparaiso\Doctrine\ORM\FixtureLoader
     */
    static function getFixtureLoader()
    {
        $path = __DIR__ . "/../data/jobboardfixtures.yml";
        $loader = new FixtureLoader($path);
        return $loader;
    }

    /**
     * EN : create database
     * @param Silex\Application $app
     */
    static function createDatabase(Application $app)
    {
        $em = $app['orm.em'];
        /* @var $em EntityManager */
        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

    }
}