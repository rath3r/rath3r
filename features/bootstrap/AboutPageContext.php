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
class AboutPageContext implements Context
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
     * @Given that the about page exists
     */
    public function thatTheAboutPageExists()
    {
        $this->session->start();
        $this->session->visit($this->url);
        $statusCode = $this->session->getStatusCode();

        Assert::eq($statusCode, 200, 'The statusCode must be %2$s. Got: %s');
    }

    /**
     * @When I visit rath3r.com\/about
     */
    public function iVisitRathrComAbout()
    {
        throw new PendingException();
    }

    /**
     * @Then I should see the about title
     */
    public function iShouldSeeTheAboutTitle()
    {
        throw new PendingException();
    }

}
