<?php
namespace Mparaiso\Provider;


use Silex\WebTestCase;

class JobBoardServiceProviderTest extends WebTestCase
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

    function testCreate()
    {
        $this->assertTrue($this->app['mp.jobb.controller.job'] != NULL);
    }
}