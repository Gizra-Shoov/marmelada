<?php

use Drupal\DrupalExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

class FeatureContext extends MinkContext implements SnippetAcceptingContext {

  /**
   * @Given I am an anonymous user
   */
  public function iAmAnAnonymousUser() {
    // Just let this pass-through.
  }

  /**
   * @When I add to cart
   */
  public function iAddToCart() {

    // Close modal window.
    $this->iWaitForCssElement('#newsletterModal');
    $link = $this->getSession()->getPage()->find('css', '#newsletterModal .close');
    $link->click();

    // Add to cart.
      $element = $this->getSession()->getPage()->find('css', '.p_details .tobuy:nth-child(1) .btn_tobuy2');
      $element->press();
  }

  /**
   * @Then I should see the item added to the cart
   */
  public function iShouldSeeTheItemAddedToTheCart() {
    $this->waitFor(function($context) {
      try {
        $element = $context->getSession()->getPage()->find('css', '.btn_pay_paypal2');
        return TRUE;
      }
      catch (WebDriver\Exception $e) {
        if ($e->getCode() == WebDriver\Exception::NO_SUCH_ELEMENT) {
          return FALSE;
        }
        throw $e;
      }
    });
  }


  /**
   * @Then I should wait for the text :arg1 to :arg2
   */
  public function iShouldWaitForTheTextTo($text, $appear) {
    $this->waitForXpathNode(".//*[contains(normalize-space(string(text())), \"$text\")]", $appear == 'appear');
  }

  /**
   * @Then /^I wait for css element "([^"]*)" to "([^"]*)"$/
   */
  public function iWaitForCssElement($element, $appear = 'appear') {
    $xpath = $this->getSession()->getSelectorsHandler()->selectorToXpath('css', $element);
    $this->waitForXpathNode($xpath, $appear == 'appear');
  }

  /**
   * Helper function; Execute a function until it return TRUE or timeouts.
   *
   * @param $fn
   *   A callable to invoke.
   * @param int $timeout
   *   The timeout period. Defaults to 90 seconds.
   *
   * @throws Exception
   */
  private function waitFor($fn, $timeout = 90000) {
    $start = microtime(true);
    $end = $start + $timeout / 1000.0;
    while (microtime(true) < $end) {
      if ($fn($this)) {
        return;
      }
    }
    throw new \Exception('waitFor timed out.');
  }

  /**
   * Wait for an element by its XPath to appear or disappear.
   *
   * @param string $xpath
   *   The XPath string.
   * @param bool $appear
   *   Determine if element should appear. Defaults to TRUE.
   *
   * @throws Exception
   */
  private function waitForXpathNode($xpath, $appear = TRUE) {
    $this->waitFor(function($context) use ($xpath, $appear) {
      try {
        $nodes = $context->getSession()->getDriver()->find($xpath);
        if (count($nodes) > 0) {
          $visible = $nodes[0]->isVisible();
          return $appear ? $visible : !$visible;
        }
        return !$appear;
      }
      catch (WebDriver\Exception $e) {
        if ($e->getCode() == WebDriver\Exception::NO_SUCH_ELEMENT) {
          return !$appear;
        }
        throw $e;
      }
    });
  }
}

