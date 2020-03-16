<?php

class CRM_CivirulesConditions_ContributionRecur_PaymentProcessor extends CRM_Civirules_Condition {

  private $conditionParams = [];

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->conditionParams = [];
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->conditionParams = unserialize($this->ruleCondition['condition_params']);
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
    // To do add condition checking
    $sqlParams = [];
    $whereClauses = [];

    switch ($triggerData->getEntity()) {
      case 'ContributionRecur':
        $whereClauses[] = "ccr.id = %1";
        $sql = "SELECT ccr.id FROM civicrm_contribution_recur ccr WHERE ";
        break;

      case 'Membership':
        $whereClauses[] = "cm.id = %1";
        $sql = "SELECT cm.id FROM civicrm_membership cm
LEFT JOIN civicrm_contribution_recur ccr ON ccr.id = cm.contribution_recur_id WHERE ";
        break;
    }

    $sqlParams[1] = [$triggerData->getEntityData($triggerData->getEntity())['id'], 'Integer'];
    if (count($this->conditionParams['payment_processor_id'])) {
      switch ($this->conditionParams['payment_processor_id_operator']) {
        case 'in':
          $whereClauses[] = 'payment_processor_id IN ('.implode($this->conditionParams['payment_processor_id'], ','). ')';
          break;
        case 'not in':
          $whereClauses[] = '(payment_processor_id NOT IN ('
            . implode($this->conditionParams['payment_processor_id'], ',')
            . ') OR payment_processor_id IS NULL)';
          break;
      }
    }

    $sql .= implode($whereClauses, ' AND ');
    $result = CRM_Core_DAO::singleValueQuery($sql, $sqlParams);
    if ($result) {
      return TRUE;
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/recurringpaymentprocessor', 'rule_condition_id=' .$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $label = '';
    $operator_options = self::getOperatorOptions();

    try {
      $params = [
        'is_test' => 0,
        'options' => ['limit' => 0, 'sort' => "name ASC"],
      ];
      $paymentProcessors = civicrm_api3('PaymentProcessor', 'Get', $params);
      if (isset($this->conditionParams['payment_processor_id']) && count($this->conditionParams['payment_processor_id'])) {
        $operator = $operator_options[$this->conditionParams['payment_processor_id_operator']];
        $values = '';
        foreach ($this->conditionParams['payment_processor_id'] as $paymentProcessorID) {
          if (!isset($paymentProcessors['values'][$paymentProcessorID])) {
            continue;
          }
          if (strlen($values)) {
            $values .= ', ';
          }
          $values .= $paymentProcessors['values'][$paymentProcessorID]['name'];
        }
        $label .= ts('Payment processor %1 %2', [
          1 => $operator,
          2 => $values,
        ]);
      }
    } catch (CiviCRM_API3_Exception $ex) {}

    return trim($label);
  }

  /**
   * Method to get operators
   *
   * @return array
   * @access protected
   */
  public static function getOperatorOptions() {
    return [
      'in' => ts('Is one of'),
      'not in' => ts('Is not one of'),
    ];
  }

}
