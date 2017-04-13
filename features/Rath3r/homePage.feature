Feature: Home Page
  As a web surfer
  In order to view the Rath3r.com site
  I should be able to see the index page

  Scenario: View the home page
    Given that the home page exists
    When I visit rath3r.com
    Then I should see the title
    And the tile should read rath3r

  Scenario: View blog post listing on the home page
    Given that the home page exists
    When I visit rath3r.com
    Then I should see a listing of blog posts
