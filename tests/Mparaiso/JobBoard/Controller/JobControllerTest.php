<?php
namespace Mparaiso\JobBoard\Controller;

use Silex\WebTestCase;
use Mparaiso\JobBoard\Controller\JobController;

class JobControllerTest extends WebTestCase
{

    public function createApplication()
    {
        $app = \Bootstrap::getApp();

        return $app;
    }

    /**
     * FR : tester la création d'une offre d'emploi
     * EN : test job creation
     * @covers JobController::create
     */
    function testCreate()
    {
        // EN : user goes to the job post page
        // FR : l'utilisateur navigue la page de création d'une offre d'emploi
        $loader = \Bootstrap::getFixtureLoader();
        $loader->parse();
        $loader->persistFixtures($this->app['orm.em']);
        $client = $this->createClient();
        $crawler = $client->request("GET", "/job/post-a-job");
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter("form[name=job]"));
        $this->assertCount(2, $crawler->filter("input[type=submit]"));
        // EN:  user submit a incomplete form and is back to the form page
        // FR :  l'utilisateur envoie un formulaire incomplet
        $buttonCrawlerNode = $crawler->selectButton("Post a job");
        $form = $buttonCrawlerNode->form(array(), "POST");
        $crawler2 = $client->submit($form);
        $this->assertCount(1, $crawler2->filter("form[name=job]"));
        // EN : user submits a complete form and is redirected to the job detail page
        // FR : l'utilisateur envoie un formulaire de création complet
        $form2 = $crawler2->filter("form[name=job]")->form();
        $crawler3 = $client->submit($form2, array(
            "job[position]"    => "Front-end developper",
            "job[company]"     => "Google",
            "job[location]"    => "Paris, France",
            "job[description]" => "Write great applications",
            "job[howToApply]"  => "Just send us a mail",
            "job[email]"       => "google@acme.com",
            "job[logo_file]"   => \Bootstrap::getRootDir() . "/assets/image.jpg",
        ));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals(4, $this->app['mp.jobb.service.job']->count());
        $crawler = $client->followRedirect();
        $this->assertRegExp('/successfull/mi', $client->getResponse()->getContent());

    }

    /**
     * @covers JobController::read
     */
    function testRead()
    {
        // EN : User job history
        // FR : historique de consultation des offres d'empoi
        $loader = \Bootstrap::getFixtureLoader();
        $loader->parse();
        $loader->persistFixtures($this->app['orm.em']);
        // EN : When the user access a job, it is added to its history
        // FR : quand un utilisateur consulte un job ,
        //      ce job est ajouté à historique de consultation des offres d'emploi
        $client = $this->createClient();
        $crawler = $client->request("GET", "/");
        $link1 = $crawler->filter("a.job")->first()->link();
        $link2 = $crawler->filter('a.job')->eq(1)->link();
        $link3 = $crawler->filter('a.job')->eq(2)->link();
        $client->request("GET", $link1->getUri());
        $this->assertCount(1, $this->app['session']->get('job_history'));
        $client->restart();
        $client->request("GET", $link2->getUri());
        $this->assertCount(2, $this->app['session']->get('job_history'));
        $client->restart();
        $client->request("GET", $link3->getUri());
        $this->assertCount(3, $this->app['session']->get('job_history'));
        $client->restart();


    }
}
