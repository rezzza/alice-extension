Feature: Load Fixtures
    In order to have data for test purposes
    As a Symfony feature writer
    I should be able to load fixtures directly in my feature

    Scenario: Loading fixtures with ORM adapter
        When I load "Rezzza\Bundle\Entity\User" fixtures where column "key" is the key:
            | key      | name  |
            | fixture1 | jean  |
            | fixture2 | marc  |
            | fixture3 | chuck |
        Then I should have 3 entity "Rezzza\Bundle\Entity\User" stored
        And entity "Rezzza\Bundle\Entity\User" with primary key 1 should have "getName" method equals to "jean"
        And entity "Rezzza\Bundle\Entity\User" with primary key 2 should have "getName" method equals to "marc"
        And entity "Rezzza\Bundle\Entity\User" with primary key 3 should have "getName" method equals to "chuck"
