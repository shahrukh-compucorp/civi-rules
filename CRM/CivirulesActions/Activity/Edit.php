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

    // Retrieve triggering activity, Check if it has an id
    $triggeringActivity = $triggerData->getEntityData('Activity');
    if (empty($triggeringActivity['id'])) {
      $message = "Civirules activity edit action has no id.";
      \Civi::log()->error($message);
      throw new Exception($message);
    }

    // load activity from api
    try {
      $activity = civicrm_api3('Activity', 'getsingle', [
        'id' => $triggeringActivity['id'],
        'return' => [
          'activity_type_id',
          'status_id',
          'subject',
          'assignee_contact_id',
          'activity_date_time',
        ],
      ]);
    } catch (Exception $e) {
      $message = "Civirules activity edit action exception: {$e->getMessage()}.";
      \Civi::log()->error($message);
      throw new Exception($message);
    }
    
    $updateParams = [ 'id' => $activity['id'] ];
    
    if (!empty($params['activity_type_id']) && $params['activity_type_id']!=$activity['activity_type_id'])
      $updateParams['activity_type_id'] = $params['activity_type_id'];

    if (!empty($params['status_id']) && $params['status_id']!=$activity['status_id'])
      $updateParams['status_id'] = $params['status_id'];

    if (!empty($params['subject']) && $params['subject']!=$activity['subject'])
      $updateParams['subject'] = $params['subject'];


    if (!empty($params['assignee_contact_id'])) {

      $existingAssignees = (array)$activity['assignee_contact_id'];
      $newAssignees = (array)$params['assignee_contact_id'];

      // Is there anyone new is the params list
      $newlyAssignedContacts = array_diff($newAssignees,$existingAssignees);
      if (count($newlyAssignedContacts)>0){
        $updateParams['assignee_contact_id'] = array_merge($existingAssignees, $newlyAssignedContacts);
      }
    }

    // issue #127: no activity date time if set to null
    if ($params['activity_date_time'] == 'null') {
      unset($params['activity_date_time']);
    } else {
      if (!empty($action_params['activity_date_time'])) {
        $delayClass = unserialize($action_params['activity_date_time']);
        if ($delayClass instanceof CRM_Civirules_Delay_Delay) {
          $activityDate = $delayClass->delayTo(new DateTime(), $triggerData);
          if ($activityDate instanceof DateTime) {
            $updateParams['activity_date_time'] = $activityDate->format('Ymd His');
          }
        }
      }
    }

    return $updateParams;
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

  /**
   * This function validates whether this action works with the selected trigger.
   *
   * This function could be overriden in child classes to provide additional validation
   * whether an action is possible in the current setup.
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param CRM_Civirules_BAO_Rule $rule
   * @return bool
   */
  public function doesWorkWithTrigger(CRM_Civirules_Trigger $trigger, CRM_Civirules_BAO_Rule $rule) {
    $entities = $trigger->getProvidedEntities();
    if (isset($entities['Activity'])) {
      return true;
    }
    return false;
  }
}
