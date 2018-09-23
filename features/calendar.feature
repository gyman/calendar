Feature: I can modify calendars

  Dictionary
    - Calendar
    - Event
    - Occurrence
    - Date Expression

  * You can add event to calendar
  * You can remove event from calendar
  * You can update events: name, calendarId, endDate, timespan
  * You can't change events: id, startDate, weekdays

  Background:
    Given calendar repository is empty

  Scenario: Create a calendar
    Given there is 0 calendars in calendar repository
    When I add new calendar with data:
    | id                                   | name |
    | 00000000-0000-0000-0000-000000000001 | test |
    Then there is 1 calendars in calendar repository
    And calendar '00000000-0000-0000-0000-000000000001' has 0 events

  Scenario: Get calendar by id
    Given I add new calendar with data:
    | id                                   | name |
    | 00000000-0000-0000-0000-000000000001 | test |
    Then there is 1 calendars in calendar repository
    And calendar '00000000-0000-0000-0000-000000000001' has data:
    | id                                   | name |
    | 00000000-0000-0000-0000-000000000001 | test |