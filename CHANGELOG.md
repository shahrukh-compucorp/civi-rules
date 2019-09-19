# CHANGELOG

## Version 2.10 (not yet released)

* Added clone butten to the edit rule screen, so you can copy and change only what needs changing (#29)
* Added configuration for the record type for Activity and Case Activity trigger.
* Fixed bug in Activity and Case Activity trigger with an empty contact id.
* Added action to set Case Role

## Version 2.9

* Adds new action: update participant status
* Refactored the way triggers, actions and conditions are inserted in the database upon installation (#24).  
* Fixed the fatal error after copying a profile (#19).  
* Fixed php warning in CRM_CivirulesConditions_Contact_AgeComparison
* Fixed Cancel button on Rule form returns to "random" page (now it returns to rule overview)
* Fixed uncorrect behavior of isConditionValid with empty value (now returns FALSE)
* Fixed issue 40 (https://lab.civicrm.org/extensions/civirules/issues/40) where the fresh install SQL scripts still create tables with CONSTRAINT ON DELETE RESTRICT rather than ON DELETE CASCADE. There is an upgrade action (2025) linked to this fix which will remove the current constraints on tables civirule_rule_action, civirule_rule_condition and civirule_rule_tag and replace them with CONSTRAINT ON DELETE CASCADE and ON UPDATE RESTRICT.
* Introduces the option to take child groups into consideration for the condition 'contact is (not) in group'.


## Version 2.8
* "Set Thank You Date for a Contribution" action now supports options for time as well as date.
* Added trigger for Event Date reached.
* Added option to compare with original value in Field Value Comparison condition
* Add a condition for contacts being within a specific domain. This is useful for multisite installations as it allows rules to only be executed on contacts that are within that domain's domain_group_id

## Version 2.7
* Changed the ON DELETE NO ACTION to ON DELETE CASCADE for the constraints for tables civirule_rule_action, civirule_rule_condition, civirule_rule_tag which fixes #8
* Fixed notices and warnings on isRuleOnQueue method
* Add "show disabled rules" checkbox on filter for Manage Rules

## Version 2.6
REQUIRES MENU REBUILD! (/civicrm/clearcache)

* Added a trigger for membership end date
* Replaced the Find Rules custom search with a Manage Rules form
