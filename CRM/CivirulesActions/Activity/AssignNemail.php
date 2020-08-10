<?php
/**
 * @author Wil Colón <it@unidosnow.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_CivirulesActions_Activity_AssignNemail extends CRM_CivirulesActions_Generic_Api {

  private $param2 = [];

  /**
   * Method to get the api entity to process in this CiviRule action
   *
   * @access protected
   * @abstract
   */
  protected function getApiEntity() {
    return 'Activity';
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
    $this->param2['id'] = $parameters['id'] = ($triggerData->getEntityData('Activity'))['id'];
    $this->param2['assignee_id'] = $parameters['assignee_id'] = $triggerData->getContactId();
    return $parameters;
  }

  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    //Normal API execution
    parent::processAction($triggerData);
    //If no exception thrown, send email with Activity details to this one assignee
    //Mimicks how webform_civicrm sends email to an activity assignee
    $assigneeV = end(civicrm_api3('Contact', 'get', ['id' => $this->param2['assignee_id']])['values']);
    CRM_Case_BAO_Case::sendActivityCopy(NULL, $this->param2['id'], [$assigneeV['email'] => $assigneeV], NULL, NULL);
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
    return FALSE;
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