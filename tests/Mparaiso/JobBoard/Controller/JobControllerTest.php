<?php
namespace Mparaiso\JobBoard\Controller;

use Silex\WebTestCase;

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
            "job[logo_file]"    => \Bootstrap::getRootDir()."/assets/image.jpg",
        ));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals(4, $this->app['mp.jobb.service.job']->count());
        $crawler = $client->followRedirect();
        $this->assertRegExp('/successfull/mi',$client->getResponse()->getContent());

    }
}
