Feature: Homepage
  In order to be able to view and get info about the site
  As an anonymous user
  We need to be able to have access to the homepage

  @javascript
  Scenario: Visit the homepage
    Given I am an anonymous user
    When  I visit "products/3853859427"
    And   I add to cart
    Then  I should see the item added to the cart
