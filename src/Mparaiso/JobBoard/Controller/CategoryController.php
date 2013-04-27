<?php
namespace Mparaiso\JobBoard\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CategoryController
{

    function index(Request $req, Application $app, $format)
    {
        return $app['twig']->render("mp.jobb.category.index.$format.twig",
            array(
                "categories" => $app['mp.jobb.service.category']->getWithActiveJob($app['mp.jobb.params.max_jobs_on_homepage']),
            ));
    }

    function read(Request $req, Application $app, $format, $name, $id)
    {
        $category = $app['mp.jobb.service.category']->getOneWithActiveJob($id);
        return $app['twig']->render("mp.jobb.category.read.$format.twig", array(
        "category" => $category,
    ));
    }
}