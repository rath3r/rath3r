Feature: About Page
  As a web surfer
  In order to view the about page of the Rath3r.com site
  I should be able to see the about page

  Scenario: View the about page
    Given that the about page exists
    When I visit rath3r.com/about
    Then I should see the about title

