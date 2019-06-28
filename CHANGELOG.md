# CHANGELOG

## Unreleased version
* Add a condition for contacts being within a specific domain. This is useful for multisite installations as it allows rules to only be executed on contacts that are within that domain's domain_group_id

## Version 2.8
* "Set Thank You Date for a Contribution" action now supports options for time as well as date.
* Added trigger for Event Date reached.
* Added option to compare with original value in Field Value Comparison condition

## Version 2.7
* Changed the ON DELETE NO ACTION to ON DELETE CASCADE for the constraints for tables civirule_rule_action, civirule_rule_condition, civirule_rule_tag which fixes #8
* Fixed notices and warnings on isRuleOnQueue method
* Add "show disabled rules" checkbox on filter for Manage Rules

## Version 2.6
REQUIRES MENU REBUILD! (/civicrm/clearcache)

* Added a trigger for membership end date
* Replaced the Find Rules custom search with a Manage Rules form
