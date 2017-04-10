<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class HomePageContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given that the home page exists
     */
    public function thatTheHomePageExists()
    {
        ///throw new PendingException();
    }

    /**
     * @When I visit rath3r.com
     */
    public function iVisitRathrCom()
    {
        $this->visit('rath3r.com');
        //throw new PendingException();
    }

    /**
     * @Then I should see a listing of blog posts
     */
    public function iShouldSeeAListingOfBlogPosts()
    {
        throw new PendingException();
    }

}
