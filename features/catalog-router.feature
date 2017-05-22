Feature: Catalog Router
  Catalog pages should be loaded based on url path dynamically

  Background:
    Given I have a store called 'UK Store'
    And I have a store called 'US Store'
    And I have a root category called 'First Root'
    And I have a root category called 'Second Root'

  Scenario Outline: View category pages
    Given The 'First Root' root category is assigned to the 'UK Store'
    And I have a category called 'Snacks' under 'First Root' with this configuration:
      | attribute_name | store_level | attribute_value       |
      | url_key        | Default     | <default_url_key>     |
      | url_key        | UK Store    | <store_level_url_key> |
    When I visit the '<page>' page in the 'UK Store'
    Then I should see the <expected_page> page

  Examples:
    | default_url_key | store_level_url_key | page       | expected_page     |
    | snacks          | use default         | /snacks    | 'Snacks' category |
    | snacks          | snacks              | /snacks    | 'Snacks' category |
    | snacks          | snacks-uk           | /snacks-uk | 'Snacks' category |
    | snacks          | snacks-uk           | /snacks    | 404               |
    | snacks          | snacks              | /iamwrong  | 404               |

  Scenario Outline: View subcategory pages
    Given The 'First Root' root category is assigned to the 'UK Store'
    And I have a category called 'Snacks' under 'First Root' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | snacks          |
    And I have a category called 'Bestsellers' under 'Snacks' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | bestsellers     |
    And I have a category called 'Healthy habits' under 'First Root' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | healthy-habits  |
    And I have a category called 'Health Stars' under 'Healthy habits' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | health-stars    |
    When I visit the '<page>' page in the 'UK Store'
    Then I should see the <expected_page> page

  Examples:
    | page                         | expected_page             |
    | /snacks                      | 'Snacks' category         |
    | /healthy-habits              | 'Healthy habits' category |
    | /snacks/bestsellers          | 'Bestsellers' category    |
    | /healthy-habits/health-stars | 'Health Stars' category   |
    | /bestsellers                 | 404                       |
    | /healthy-habits/bestsellers  | 404                       |
    | /iamwrong/bestsellers        | 404                       |
    | /snacks/iamwrong             | 404                       |
    | /snacks/health-stars         | 404                       |
    | /iam/wrong                   | 404                       |

  Scenario Outline: View product page directly
    Given The 'First Root' root category is assigned to the 'UK Store'
    And I have a product called 'Original fruity flapjack' with this configuration:
      | attribute_name | store_level | attribute_value       |
      | url_key        | Default     | <default_url_key>     |
      | url_key        | UK Store    | <store_level_url_key> |
    When I visit the '<page>' page in the 'UK Store'
    Then I should see the <expected_page> page

  Examples:
    | default_url_key          | store_level_url_key         | page                         | expected_page                      |
    | original-fruity-flapjack | use default                 | /original-fruity-flapjack    | 'Original fruity flapjack' product |
    | original-fruity-flapjack | original-fruity-flapjack    | /original-fruity-flapjack    | 'Original fruity flapjack' product |
    | original-fruity-flapjack | original-fruity-flapjack-uk | /original-fruity-flapjack-uk | 'Original fruity flapjack' product |
    | original-fruity-flapjack | original-fruity-flapjack-uk | /original-fruity-flapjack    | 404                                |
    | original-fruity-flapjack | original-fruity-flapjack    | /iamwrong                    | 404                                |

  Scenario Outline: View product page with category path
    Given The 'First Root' root category is assigned to the 'UK Store'
    And I have a category called 'Snacks' under 'First Root' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | snacks          |
    And I have a category called 'Bestsellers' under 'Snacks' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | bestsellers     |
    And I have a category called 'New' under 'Snacks' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | new             |
    And I have a category called 'Healthy habits' under 'First Root' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | healthy-habits  |
    And I have a category called 'Health Stars' under 'Healthy habits' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | health-stars    |
    And I have a product called 'Original fruity flapjack' assigned to ['Snacks, Bestsellers'] categories with this configuration:
      | attribute_name | store_level | attribute_value          |
      | url_key        | Default     | original-fruity-flapjack |
    When I visit the '<page>' page in the 'UK Store'
    Then I should see the <expected_page> page

  Examples:
    | page                                         | expected_page                      |
    | /original-fruity-flapjack                    | 'Original fruity flapjack' product |
    | /snacks/original-fruity-flapjack             | 'Original fruity flapjack' product |
    | /snacks/bestsellers/original-fruity-flapjack | 'Original fruity flapjack' product |
    | /bestsellers/original-fruity-flapjack        | 404                                |
    | /snacks/wrong/original-fruity-flapjack       | 404                                |
    | /snacks/bestsellers/wrongprodkey             | 404                                |
    | /snacks/new/original-fruity-flapjack         | 404                                |

  Scenario Outline: View different categories with the same url key in different stores
    Given The 'First Root' root category is assigned to the 'UK Store'
    And The 'Second Root' root category is assigned to the 'US Store'
    And I have a category called 'Snacks UK' under 'First Root' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | snacks          |
    And I have a category called 'Snacks US' under 'Second Root' with this configuration:
      | attribute_name | store_level | attribute_value |
      | url_key        | Default     | snacks          |
    When I visit the '<page>' page in the '<store>'
    Then I should see the <expected_page> page

  Examples:
    | page    | store    | expected_page        |
    | /snacks | UK Store | 'Snacks UK' category |
    | /snacks | US Store | 'Snacks US' category |

    
