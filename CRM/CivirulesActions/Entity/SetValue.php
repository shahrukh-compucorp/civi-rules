<?php
/**
 * @author Alain Benbassat (CiviCooP) <alain.benbassat@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_CivirulesActions_Entity_SetValue extends CRM_CivirulesActions_Generic_Api {

  /**
   * Method to get the api entity to process in this CiviRule action
   *
   * @access protected
   * @abstract
   */
  protected function getApiEntity() {
    $action_params = $this->getActionParameters();
    return $action_params['entity'];
  }

  /**
   * Method to get the api action to process in this CiviRule action
   *
   * @access protected
   * @abstract
   */
  protected function getApiAction() {
    return 'Create';
  }

  /**
   * Returns an array with parameters used for processing an action
   *
   * @param array $parameters
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return array
   * @access protected
   */
  protected function alterApiParameters($parameters, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $action_params = $this->getActionParameters();

    $entity = $action_params['entity'];
    $field = $action_params['field'];
    $value = $action_params['value'];

    $activityData = $triggerData->getEntityData($entity);
    $parameters['id'] = $activityData['id'];
    $parameters[$field] = $value;
    return $parameters;
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
    return CRM_Utils_System::url('civicrm/civirule/form/action/entity_set_value', 'rule_action_id=' . $ruleActionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   * @throws \CiviCRM_API3_Exception
   */
  public function userFriendlyConditionParams() {
    $friendlyName = '';
    $params = $this->getActionParameters();

    $result = civicrm_api3($params['entity'], 'getfield', [
      'name' => $params['field'],
      'action' => "get",
    ]);
    if ($result['count'] > 0) {
      $friendlyName .= ts("Set %1::%2 to: ", [1 => $params['entity'], 2 => $result['values']['title']]) . $params['value'];
    }

    return $friendlyName;
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
    $entities = [];
    foreach ($this->triggerClass->getProvidedEntities() as $entity) {
      $entities[$entity->entity] = $entity->entity;
    }
    // set value is valid for all entities, no check needed
    return TRUE;
  }
}
