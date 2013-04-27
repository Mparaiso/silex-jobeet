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
        $limit = $app['mp.jobb.params.max_jobs_on_category'];
        $offset = $req->query->get("offset", 0);
        $category = $app['mp.jobb.service.category']->find($id);
        $jobs = $app['mp.jobb.service.job']->findBy(array('isActivated' => TRUE, "category" => $category), array(), $limit, $limit * $offset);
        $total = $app["mp.jobb.service.category"]->countActiveJobsByCategory($category);
        return $app['twig']->render("mp.jobb.category.read.$format.twig", array(
            "category" => $category,
            "jobs"     => $jobs,
            "total"    => $total,
            "limit"    => $limit,
            "offset"   => $offset
        ));
    }
}