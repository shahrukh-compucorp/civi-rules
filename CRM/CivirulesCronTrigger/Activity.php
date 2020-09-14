<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

abstract class CRM_CivirulesCronTrigger_Activity extends CRM_Civirules_Trigger_Cron {

  /**
   * @var \CRM_Activity_DAO_Activity
   */
  protected $activityDAO = NULL;

  /**
   * This function returns a CRM_Civirules_TriggerData_TriggerData this entity is used for triggering the rule
   *
   * Return false when no next entity is available
   *
   * @return CRM_Civirules_TriggerData_TriggerData|false
   */
  protected function getNextEntityTriggerData() {
    if (!$this->activityDAO) {
      if (!$this->queryForTriggerEntities()) {
        return FALSE;
      }
    }
    if ($this->activityDAO->fetch()) {
      $data = [];
      CRM_Core_DAO::storeValues($this->activityDAO, $data);
      unset($data['activity_contact_id']);
      unset($data['contact_id']);
      unset($data['record_type_id']);
      $triggerData = new CRM_Civirules_TriggerData_Cron($this->activityDAO->contact_id, 'Activity', $data);
      $activityContact = [];
      $activityContact['id'] = $this->activityDAO->activity_contact_id;
      $activityContact['activity_id'] = $this->activityDAO->id;
      $activityContact['contact_id'] = $this->activityDAO->contact_id;
      $activityContact['record_type_id'] = $this->activityDAO->record_type_id;
      $triggerData->setEntityData('ActivityContact', $activityContact);
      return $triggerData;
    }
    return FALSE;
  }

  /**
   * Returns an array of entities on which the trigger reacts
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function reactOnEntity() {
    return new CRM_Civirules_TriggerData_EntityDefinition('Activity', 'Activity', 'CRM_Activity_DAO_Activity', 'Activity');
  }

  /**
   * Method to query trigger entities
   *
   * @access private
   */
  private function queryForTriggerEntities() {
    if (empty($this->triggerParams['activity_type_id'])) {
      return false;
    }
    if (empty($this->triggerParams['activity_status_id'])) {
      return false;
    }

    $sql = "SELECT a.*, ac.contact_id as contact_id, ac.record_type_id as record_type_id, ac.id as activity_contact_id
            FROM `civicrm_activity` `a`
            INNER JOIN `civicrm_activity_contact` ac ON a.id = ac.activity_id
            LEFT JOIN `civirule_rule_log` `rule_log` ON `rule_log`.entity_table = 'civicrm_activity' AND `rule_log`.entity_id = a.id AND `rule_log`.`contact_id` = `ac`.`contact_id` AND DATE(`rule_log`.`log_date`) = DATE(NOW())  AND `rule_log`.`rule_id` = %3
            WHERE `a`.`activity_type_id` = %1 AND a.status_id = %2 AND a.activity_date_time <= NOW()
            AND `rule_log`.`id` IS NULL
            AND `ac`.`contact_id` NOT IN (
              SELECT `rule_log2`.`contact_id`
              FROM `civirule_rule_log` `rule_log2`
              WHERE `rule_log2`.`rule_id` = %3 AND DATE(`rule_log2`.`log_date`) = DATE(NOW()) and `rule_log2`.`entity_table` IS NULL AND `rule_log2`.`entity_id` IS NULL
            )";
    $params[1] = [$this->triggerParams['activity_type_id'], 'Integer'];
    $params[2] = [$this->triggerParams['activity_status_id'], 'Integer'];
    $params[3] = [$this->ruleId, 'Integer'];
    $this->activityDAO = CRM_Core_DAO::executeQuery($sql, $params, true, 'CRM_Activity_DAO_Activity');

    return true;
  }

  public function setTriggerParams($triggerParams) {
    $this->triggerParams = unserialize($triggerParams);
  }

  /**
   * Returns an array of additional entities provided in this trigger
   *
   * @return array of CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function getAdditionalEntities() {
    $entities = parent::getAdditionalEntities();
    $entities[] = new CRM_Civirules_TriggerData_EntityDefinition('ActivityContact', 'ActivityContact', 'CRM_Activity_DAO_ActivityContact' , 'ActivityContact');
    return $entities;
  }

}
