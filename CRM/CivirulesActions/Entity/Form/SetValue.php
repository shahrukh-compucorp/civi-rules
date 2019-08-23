<?php
/**
 * Class for CiviRules SetValue Form
 *
 * @author Alain Benbassat (CiviCooP) <alain.benbassat@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesActions_Entity_Form_SetValue extends CRM_CivirulesActions_Form_Form {

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->setFormTitle();

    // store current action id in hidden field
    $this->add('hidden', 'rule_action_id');

    // create a list of available entities
    $entities = [];
    foreach ($this->triggerClass->getProvidedEntities() as $entity) {
      $entities[$entity->entity] = $entity->entity;
    }
    $this->add('select', 'rule_action_entity', ts('For'), $entities, true);

    // list of fields for the selected entity (will be populated dynamically)
    $currentField = $this->getCurrentField();
    $this->add('select', 'rule_action_field', ts('Set'), $currentField, true);

    // new value
    $this->add('text', 'rule_action_value', ts('To'), true);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $data = [];
    $defaultValues = [];
    $defaultValues['rule_action_id'] = $this->ruleActionId;
    $ruleAction = new CRM_Civirules_BAO_RuleAction();
    $ruleAction->id = $this->ruleActionId;
    if ($ruleAction->find(true)) {
      $data = unserialize($ruleAction->action_params);
    }
    if (!empty($data['entity'])) {
      $defaultValues['rule_action_entity'] = $data['entity'];
    }
    if (!empty($data['field'])) {
      $defaultValues['rule_action_field'] = $data['field'];
    }
    if (!empty($data['value'])) {
      $defaultValues['rule_action_value'] = $data['value'];
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule action not found
   * @access public
   */
  public function postProcess() {
    $data = unserialize($this->ruleAction->action_params);
    $data['entity'] = $this->_submitValues['rule_action_entity'];
    $data['field'] = $this->_submitValues['rule_action_field'];
    $data['value'] = $this->_submitValues['rule_action_value'];

    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();

    $session = CRM_Core_Session::singleton();
    $session->setStatus('Action '.$this->action->label.' parameters updated to CiviRule '
      .CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleAction->rule_id),
      'Action parameters updated', 'success');

    $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->ruleAction->rule_id, TRUE);
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Method to set the form title
   *
   * @access protected
   */
  protected function setFormTitle() {
    $actionLabel = '';
    $ruleAction = new CRM_Civirules_BAO_RuleAction();
    $ruleAction->id = $this->ruleActionId;
    if ($ruleAction->find(true)) {
      $action = new CRM_Civirules_BAO_Action();
      $action->id = $ruleAction->action_id;
      if ($action->find(true)) {
        $actionLabel = $action->label;
      }
    }

    $title = 'CiviRules Set Action Parameters';
    $this->assign('ruleActionHeader', 'Edit Action '. $actionLabel.' of CiviRule '
      . CRM_Civirules_BAO_Rule::getRuleLabelWithId($ruleAction->rule_id));
    CRM_Utils_System::setTitle($title);
  }

  private function getCurrentField() {
    // get the current selected field from the default values
    $defaultValues = $this->setDefaultValues();
    if (array_key_exists('rule_action_field', $defaultValues)) {
      return [$defaultValues['rule_action_field'] => $defaultValues['rule_action_field']];
    }
    else {
      return [];
    }
  }
}
