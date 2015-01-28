<?php
/**
 * Page showing list of existing CiviRule Rules
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
require_once 'CRM/Core/Page.php';

class CRM_Civirules_Page_Rule extends CRM_Core_Page {
  /**
   * Standard run function created when generating page with Civix
   * 
   * @access public
   */
  function run() {
    $this->setPageConfiguration();
    $this->assign('rules', $this->getRules());
    parent::run();
  }
  /**
   * Function to get the data 
   * 
   * @return array $rules
   * @access protected
   */
  protected function getRules() {
    $rules = CRM_Civirules_BAO_Rule::getValues(array());
    foreach ($rules as $ruleId => $rule) {
      $rules[$ruleId]['actions'] = $this->setRowActions($rule);
      $rules[$ruleId]['event_label'] = CRM_Civirules_BAO_Event::getEventLabelWithId($rule['event_id']);
      $rules[$ruleId]['created_contact_name'] = CRM_Civirules_Utils::getContactName($rule['created_contact_id']);
      $rules[$ruleId]['is_active'] = CRM_Civirules_Utils::formatIsActive($rule['is_active']);
    }
    return $rules;
  }
  /**
   * Function to set the row action urls and links for each row
   * 
   * @param array $rule
   * @return array $actions
   * @access protected
   */
  protected function setRowActions($rule) {
    $rowActions = array();
    $updateUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.
      $rule['id']);
    $deleteUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=delete&id='.
      $rule['id']);
    $disableUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=disable&id='.
      $rule['id']);
    $enableUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=enable&id='.
      $rule['id']);
    $rowActions[] = '<a class="action-item" title="Update" href="'.$updateUrl.'">Update</a>';
    $rowActions[] = '<a class="action-item" title="Delete" href="'.$deleteUrl.'">Delete</a>';
    if ($rule['is_active'] == 1) {
      $rowActions[] = '<a class="action-item" title="Disable" href="'.$disableUrl.'">Disable</a>';
    } else {
      $rowActions[] = '<a class="action-item" title="Enable" href="'.$enableUrl.'">Enable</a>';
    }
    return $rowActions;
  }
  /**
   * Function to set the page configuration
   * 
   * @access protected
   */
  protected function setPageConfiguration() {
    CRM_Utils_System::setTitle(ts('CiviRules'));
    $this->assign('add_url', CRM_Utils_System::url('civicrm/civirule/form/rule', 
      'action=add', true));  
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/civirules/page/rule', 'reset=1', true));
  }
}