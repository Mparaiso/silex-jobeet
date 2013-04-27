<?php
    use Silex\WebTestCase;
use Silex\Application;

class AppTest extends WebTestCase{
    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        return Bootstrap::getApp();
    }

    function testCreate(){
        $this->assertTrue($this->app!=NULL);
        $this->assertTrue($this->app instanceof Application);
    }
}