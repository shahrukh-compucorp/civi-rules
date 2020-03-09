<?php
/**
 * Class for CiviRules Condition Membership recurring form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_ContributionRecur_PaymentProcessor extends CRM_CivirulesConditions_Form_Form {

  /**
   * Method to get operators
   *
   * @return array
   * @access protected
   */
  protected function getOperators() {
    return CRM_CivirulesConditions_ContributionRecur_PaymentProcessor::getOperatorOptions();
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');

    $paymentProcessors = CRM_Civirules_Utils::getPaymentProcessors();
    $paymentProcessorID = $this->add('select', 'payment_processor_id', ts('Payment Processor'), $paymentProcessors, TRUE);
    $paymentProcessorID->setMultiple(TRUE);
    $this->add('select', 'payment_processor_id_operator', ts('Operator'), $this->getOperators(), TRUE);

    $this->addButtons([
      ['type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,],
      ['type' => 'cancel', 'name' => ts('Cancel')]
    ]);
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->ruleCondition->condition_params);
    if (!empty($data['payment_processor_id'])) {
      $defaultValues['payment_processor_id'] = $data['payment_processor_id'];
    }
    if (!empty($data['payment_processor_id_operator'])) {
      $defaultValues['payment_processor_id_operator'] = $data['payment_processor_id_operator'];
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    $data['payment_processor_id'] = $this->_submitValues['payment_processor_id'];
    $data['payment_processor_id_operator'] = $this->_submitValues['payment_processor_id_operator'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();
    parent::postProcess();
  }
}
