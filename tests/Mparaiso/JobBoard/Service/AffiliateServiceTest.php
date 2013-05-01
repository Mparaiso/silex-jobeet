<?php
namespace Mparaiso\JobBoard\Service;

use Silex\WebTestCase;
use Mparaiso\JobBoard\Entity\Affiliate;
use Bootstrap;

class AffiliateServiceTest extends WebTestCase
{

    /**
     * Creates the application.
     *
     * @return HttpKernel
     */
    public function createApplication()
    {
        $app = Bootstrap::getApp();
        return $app;
    }

    public function testSave()
    {
        $ts = function ($affilate) {
            return 'token';
        };
        $loader = Bootstrap::getFixtureLoader();
        $loader->parse();
        $loader->persistFixtures($this->app['orm.em']);
        $as = new AffiliateService($this->app['orm.em'],
            'Mparaiso\JobBoard\Entity\Affiliate',
            'Mparaiso\JobBoard\Entity\Job',
            'Mparaiso\JobBoard\Entity\Category');
        $as->setTokenStrategy($ts);
        $cat = $this->app['mp.jobb.service.category']->findOneBy(array());
        $af = new Affiliate();
        $af->setEmail('foo@free.fr');
        $af->setUrl('http://free.fr');
        $af->addCategory($cat);
        $as->save($af);
        $this->assertEquals("token", $af->getToken());
    }

    function testForToken()
    {
        $loader = Bootstrap::getFixtureLoader();
        $loader->parse();
        $loader->persistFixtures($this->app['orm.em']);
        $as = $this->app['mp.jobb.service.affiliate'];
        /* @var $as AffiliateService */
        $cat = $this->app['mp.jobb.service.category']->findOneBy(array('name' => 'Design'));
        $af = new Affiliate();
        $af->setEmail('bar@free.fr');
        $af->setUrl('htpp://google.com');
        $af->setIsActive(TRUE);
        $af->addCategory($cat);
        $as->save($af);
        $this->assertNotNull($af->getId());
        $this->assertTrue($af->getIsActive());
        $jobs = $as->getForToken($af->getToken());
        $this->assertCount(1, $jobs);

    }
}
