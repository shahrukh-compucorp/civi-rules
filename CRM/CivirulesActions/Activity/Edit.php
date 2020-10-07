<?php
/**
 * Class for CiviRules editing an triggering activity
 *
 * @author David Hayes (Black Brick Software) <david@blackbrick.software>
 * @license AGPL-3.0
 */

class CRM_CivirulesActions_Activity_Edit extends CRM_CivirulesActions_Activity_Add {

  /**
   * Returns an array with parameters used for processing an action
   *
   * @param array $params
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return array $params
   * @access protected
   */
  protected function alterApiParameters($params, CRM_Civirules_TriggerData_TriggerData $triggerData) {

    $action_params = $this->getActionParameters();

    $params['id'] = ($triggerData->getEntityData('Activity'))['id'];
    
    if (!empty($params['activity_type_id']))
      $params['activity_type_id'] = $action_params['activity_type_id'];

    if (!empty($params['status_id']))
      $params['status_id'] = $action_params['status_id'];

    if (!empty($params['subject']))
      $params['subject'] = $action_params['subject'];
    
    if (!empty($action_params['assignee_contact_id'])) {
      $assignee = array();
      if (is_array($action_params['assignee_contact_id'])) {
        foreach($action_params['assignee_contact_id'] as $contact_id) {
          if($contact_id) {
            $assignee[] = $contact_id;
          }
        }
      } else {
        $assignee[] = $action_params['assignee_contact_id'];
      }
      if (count($assignee)) {
        $params['assignee_contact_id'] = $action_params['assignee_contact_id'];
      } else {
        $params['assignee_contact_id'] = '';
      }
    }

    // issue #127: no activity date time if set to null
    if ($action_params['activity_date_time'] == 'null') {
      unset($params['activity_date_time']);
    } else {
      if (!empty($action_params['activity_date_time'])) {
        $delayClass = unserialize($action_params['activity_date_time']);
        if ($delayClass instanceof CRM_Civirules_Delay_Delay) {
          $activityDate = $delayClass->delayTo(new DateTime(), $triggerData);
          if ($activityDate instanceof DateTime) {
            $params['activity_date_time'] = $activityDate->format('Ymd His');
          }
        }
      }
    }

    // Issue #152: when a rule is trigger from a public page then source contact id
    // is empty and that in turn creates a fatal error.
    // So the solution is to check whether we have a logged in user and if not use
    // the contact from the trigger as the source contact.
    if (CRM_Core_Session::getLoggedInContactID()) {
      $params['source_contact_id'] = CRM_Core_Session::getLoggedInContactID();
    } else {
      $params['source_contact_id'] = $triggerData->getContactId();
    }
    return $params;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a action
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleActionId
   * @return bool|string
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/action/activity/edit', 'rule_action_id='.$ruleActionId);
  }
}
