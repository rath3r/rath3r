<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Exception;
use Behat\Testwork\Exception\Stringer\TestworkExceptionStringer;
use Behat\Mink\Session;
use Behat\Mink\Driver\GoutteDriver;
use Webmozart\Assert\Assert;

/**
 * Defines application features from the specific context.
 */
class HomePageContext implements Context
{
    private $driver;
    private $session;
    private $url;
    private $page;

    public function __construct($url)
    {
        $this->driver = new GoutteDriver();
        $this->session = new Session($this->driver);
        $protocol = 'http://';
        $this->url = $protocol . $url;

    }

    /**
     * @Given that the home page exists
     */
    public function thatTheHomePageExists()
    {

        $this->session->start();
        $this->session->visit($this->url);
        $statusCode = $this->session->getStatusCode();

        Assert::eq($statusCode, 200, 'The statusCode must be %2$s. Got: %s');
    }

    /**
     * @When I visit rath3r.com
     */
    public function iVisitRathrCom()
    {
        $this->page = $this->session->getPage();
    }

    /**
     * @Then I should see a listing of blog posts
     */
    public function iShouldSeeAListingOfBlogPosts()
    {
        $posts = $this->page->find('css', '#blog-posts');

        if (null === $posts) {
            throw new \Exception('The blog posts element is not found');
        }
    }

    /**
     * @Then I should see the title
     */
    public function iShouldSeeTheTitle()
    {
        $h1 = $this->page->find('css', 'h1');

        if (null === $h1) {
            throw new \Exception('The h1 element is not found');
        }
    }

    /**
     * @Then the tile should read rath3r
     */
    public function theTileShouldReadRathr()
    {
        $h1 = $this->page->find('css', 'h1');
        Assert::eq($h1->getHtml(), 'rath3r', 'The tile must be %2$s. Got: %s');
    }

}
