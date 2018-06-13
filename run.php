<?php
/**
 * @author Klaas Eikelboom (klaas@kainuk.it)
 * @date 12-6-18
 * @license AGPL-3.0
 */

   $conditionParams = CRM_Core_DAO::singleValueQuery("select condition_params from civirule_rule_condition");
   $conditionParams = unserialize($conditionParams);
   print_r($conditionParams);

