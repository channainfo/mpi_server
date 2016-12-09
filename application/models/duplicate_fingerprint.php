<?php
class DuplicateFingerprint{

  static function patients() {
    $patients = Patient::all_mapper("pat_id");
    $results = array();

    while(count($patients) > 1) {
      $fingerprint_sdk = GrFingerService::get_instance();
      $patient = array_shift($patients);
      $identified_name = $patient->identified_name();

      $fingerprint_options = array(
        "name" => $identified_name,
        "value" => $patient->$identified_name
      );

      $matched_patients = FingerprintMatcher::patients($fingerprint_options, $patients);
      if(count($matched_patients)) {
        $results[$patient->pat_id] = $matched_patients;
      }
      foreach($matched_patients as $matched_patient) {
        unset($patients[$matched_patient->pat_id]);
      }
    }
    return $results;
  }

}
