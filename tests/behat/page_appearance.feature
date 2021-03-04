@mod @mod_pdf
Feature: Configure pdf appearance
  In order to change the appearance of the pdf resource
  As an admin
  I need to configure the pdf appearance settings

  Background:
    Given the following "courses" exist:
      | shortname | fullname   |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | pdf     | pdfName1  | pdfDesc1  | C1     | pdf1    |
    And I log in as "admin"

  @javascript
  Scenario: Hide and display the pdf name
    Given I am on "Course 1" course homepdf
    When I follow "pdfName1"
    Then I should see "pdfName1" in the "region-main" "region"
    And I navigate to "Edit settings" in current pdf administration
    And I follow "Appearance"
    When I click on "Display pdf name" "checkbox"
    And I press "Save and display"
    Then I should not see "pdfName1" in the "region-main" "region"
    And I navigate to "Edit settings" in current pdf administration
    And I follow "Appearance"
    When I click on "Display pdf name" "checkbox"
    And I press "Save and display"
    Then I should see "pdfName1" in the "region-main" "region"

  @javascript
  Scenario: Display and hide the pdf description
    Given I am on "Course 1" course homepdf
    When I follow "pdfName1"
    Then I should not see "pdfDesc1" in the "region-main" "region"
    And I navigate to "Edit settings" in current pdf administration
    And I follow "Appearance"
    When I click on "Display pdf description" "checkbox"
    And I press "Save and display"
    Then I should see "pdfDesc1" in the "region-main" "region"
    And I navigate to "Edit settings" in current pdf administration
    And I follow "Appearance"
    When I click on "Display pdf description" "checkbox"
    And I press "Save and display"
    Then I should not see "pdfDesc1" in the "region-main" "region"

  @javascript
  Scenario: Display and hide the last modified date
    Given I am on "Course 1" course homepdf
    When I follow "pdfName1"
    Then I should see "Last modified:" in the "region-main" "region"
    And I navigate to "Edit settings" in current pdf administration
    And I follow "Appearance"
    When I click on "Display last modified date" "checkbox"
    And I press "Save and display"
    Then I should not see "Last modified:" in the "region-main" "region"
    And I navigate to "Edit settings" in current pdf administration
    And I follow "Appearance"
    When I click on "Display last modified date" "checkbox"
    And I press "Save and display"
    Then I should see "Last modified:" in the "region-main" "region"
