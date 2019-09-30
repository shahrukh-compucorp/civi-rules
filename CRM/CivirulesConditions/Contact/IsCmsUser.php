<?php
/**
 * Class for CiviRules AgeComparison (extending generic ValueComparison)
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 23 Sep 2019
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Contact_IsCmsUser extends CRM_Civirules_Condition {

  private $_conditionParams = [];

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->_conditionParams = [];
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->_conditionParams = unserialize($this->ruleCondition['condition_params']);
    }
  }

  /**
   * This method returns true or false when an condition is valid or not
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   * @abstract
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contactId = $triggerData->getContactId();
    if ($contactId) {
      try {
        $result = civicrm_api3('UFMatch', 'getcount', [
          'contact_id' => $contactId,
        ]);
        if ($result > 0) {
          return TRUE;
        }
      }
      catch (CiviCRM_API3_Exception $ex) {
      }
    }
    return FALSE;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   * @abstract
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return FALSE;
  }

}
