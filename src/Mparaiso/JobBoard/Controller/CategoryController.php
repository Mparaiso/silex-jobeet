<?php

namespace Mparaiso\JobBoard\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * EN : display category related pages
 * FR : affiche les pages relatives aux catégories 
 * @author M.Paraiso  <mparaiso@online.fr>
 */
class CategoryController {

    /**
     * FR : homepage, affiche les catégories possédant des jobs actifs , puis pour chaque catégorie liste les n premiers jobs de la catégorie
     * EN : homepage, display categories that have active jobs , and for each category display n jobs in category
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param \Silex\Application $app
     * @param type $format
     * @return type
     */
    function index(Request $req, Application $app, $format) {
        return $app['twig']->render("mp.jobb.category.index.$format.twig", array(
                    "categories" => $app['mp.jobb.service.category']->getWithActiveJob($app['mp.jobb.params.max_jobs_on_homepage']),
        ));
    }

    /**
     * FR : affiche les jobs dans une catégorie
     * EN : display the jobs in a category
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @param \Silex\Application $app
     * @param type $format
     * @param type $name
     * @param type $id
     * @return type
     */
    function read(Request $req, Application $app, $format, $name, $id) {
        $limit = $app['mp.jobb.params.max_jobs_on_category'];
        $offset = $req->query->get("offset", 0);
        $category = $app['mp.jobb.service.category']->find($id);
        $jobs = $app['mp.jobb.service.job']->findBy(array('isActivated' => TRUE, "category" => $category), array(), $limit, $limit * $offset);
        $total = $app["mp.jobb.service.category"]->countActiveJobsByCategory($category);
        return $app['twig']->render("mp.jobb.category.read.$format.twig", array(
                    "category" => $category,
                    "jobs" => $jobs,
                    "total" => $total,
                    "limit" => $limit,
                    "offset" => $offset
        ));
    }

}