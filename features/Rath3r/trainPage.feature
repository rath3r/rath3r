Feature: Trains Page
  As a web surfer
  In order to view the trains page of the Rath3r.com site
  I should be able to see the train page

  Scenario: View the trains page
    Given that the trains page exists
    When I visit rath3r.com/trains
    Then I should see the trains title

  Scenario: View the trains animation
    Given that the trains page exists
    When I visit rath3r.com/trains
    Then I should see the trains title
