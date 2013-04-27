<?php

namespace Mparaiso\JobBoard\Service;

use Silex\WebTestCase;
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