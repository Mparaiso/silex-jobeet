<?php

namespace Mparaiso\JobBoard\Service;

use Silex\WebTestCase;
use Mparaiso\Doctrine\ORM\FixtureLoader;
use Mparaiso\JobBoard\Entity\Category;

class CategoryServiceTest extends WebTestCase
{
    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        return \Bootstrap::getApp();
    }

    function testFindAll()
    {
        // 4 categories are uploaded to the database;
        $loader = \Bootstrap::getFixtureLoader();
        $loader->parse();
        $loader->persistFixtures($this->app['orm.em']);
        $categoryService = $this->app['mp.jobb.service.category'];
        $categories = $categoryService->findAll();
        $this->assertCount(4, $categories);
        $loader->removeFixtures($this->app['orm.em']);
        # 103 categories are uploaded
        $fixtureLoader = new FixtureLoader(\Bootstrap::getRootDir() . "/../data/randomjobboardfixutes.yml");
        $fixtureLoader->parse();
        $fixtureLoader->persistFixtures($this->app['orm.em']);
        $cat2 = $categoryService->findAll();
        $this->assertCount(4, $categories);
        $fixtureLoader->removeFixtures($this->app['orm.em']);
    }

    function testSave()
    {
        // assert save category works
        $s = new CategoryService($this->app['orm.em'],
            'Mparaiso\JobBoard\Entity\Category', 'Mparaiso\JobBoard\Entity\Job');
        $c = new Category;
        $c->setName("Database Administrator");
        $s->save($c);
        $this->assertTrue(is_int($c->getId()), "Category is not properly saved");
        // assert category names are unique
        $this->getExpectedException();
        $c2 = new Category;
        $c->setName("Database Administrator");
        $s->save($c);
    }
}