<?php
/**
 * Class for CiviRule Condition Activity Date is .....
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 3 May 2018
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_CivirulesConditions_Activity_Date extends CRM_Civirules_Condition {

  private $_conditionParams = array();

  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/activity/date',
      'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->_conditionParams = array();
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->_conditionParams = unserialize($this->ruleCondition['condition_params']);
    }
  }

  /**
   * Method to check if the condition is valid, will check if the contact
   * has an activity of the selected type
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $activityData = $triggerData->getEntityData('Activity');
    if (isset($activityData['activity_date_time'])) {
      $activityDate = new DateTime($activityData['activity_date_time']);
      if ($activityDate) {
        if ($this->_conditionParams['operator'] == 6) {
          $fromDate = new DateTime($this->_conditionParams['activity_from_date']);
          $toDate = new DateTime($this->_conditionParams['activity_to_date']);
          $fromInterval = date_diff($fromDate, $activityDate);
          $toInterval = date_diff($toDate, $activityDate);
          if ($fromInterval->days >= 0 && $toInterval->days <= 0) {
            return TRUE;
          }
        }
        else {
          $compareDate = new DateTime($this->_conditionParams['activity_compare_date']);
          $interval = date_diff($compareDate, $activityDate);
          switch ($this->_conditionParams['operator']) {
            case 0:
              if ($interval->days == 0) {
                return TRUE;
              }
              break;
            case 1:
              if ($interval->days > 0) {
                return TRUE;
              }
              break;
            case 2:
              if ($interval->days >= 0) {
                return TRUE;
              }
              break;
            case 3:
              if ($interval->days < 0) {
                return TRUE;
              }
              break;
            case 4:
              if ($interval->days <= 0) {
                return TRUE;
              }
              break;
            case 5:
              if ($interval->days != 0) {
                return TRUE;
              }
              break;
          }
        }
      }
    }
    return FALSE;
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $operatorOptions = CRM_Civirules_Utils::getActivityDateOperatorOptions();
    $friendlyText = ts("Activity Date ") . ts($operatorOptions[$this->_conditionParams['operator']]);
    if ($this->_conditionParams['operator'] == 6) {
      $fromDate = new DateTime($this->_conditionParams['activity_from_date']);
      $toDate = new DateTime($this->_conditionParams['activity_to_date']);
      $friendlyText .= ' ' . $fromDate->format('j F Y') . ts(' and ') . $toDate->format('j F Y');
    }
    else {
      $compareDate = new DateTime($this->_conditionParams['activity_compare_date']);
      $friendlyText .= ' ' . $compareDate->format('j F Y');
    }
    return $friendlyText;
  }

  /**
   * This function validates whether this condition works with the selected trigger.
   *
   * This function could be overriden in child classes to provide additional validation
   * whether a condition is possible in the current setup. E.g. we could have a condition
   * which works on contribution or on contributionRecur then this function could do
   * this kind of validation and return false/true
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
   * @return bool
   */
  public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
    return $trigger->doesProvideEntity('Activity');
  }
}