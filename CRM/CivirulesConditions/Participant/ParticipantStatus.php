<?php

class CRM_CivirulesConditions_Participant_ParticipantStatus extends CRM_CivirulesConditions_Generic_Status {

  /**
   * The entity name (eg. Membership)
   * @return string
   */
  protected function getEntity() {
    return 'Participant';
  }

  /**
   * The entity status field (eg. membership_status_id)
   * @return string
   */
  public function getEntityStatusFieldName() {
    return 'participant_status_id';
  }

  /**
   * Returns an array of statuses as [ id => label ]
   * @param bool $active
   * @param bool $inactive
   *
   * @return array
   */
  public static function getEntityStatusList($active = TRUE, $inactive = FALSE) {
    return parent::getEntityStatusListFromOptionGroup('participant_status', $active, $inactive);
  }

}
