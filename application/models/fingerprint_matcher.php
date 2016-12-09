<?php
class FingerprintMatcher {
  static function patients($fingerprint_options=array(), $patients) {
    if(count($fingerprint_options) == 0)
      return $patients;

    $fingerprint_name = $fingerprint_options["name"] ; // fingerprint_r1, fingerprint_r2 , ...
    $fingerprint_value = $fingerprint_options["value"]; // data of fingerprint_r1

    $results = array();
    $fingerprint_sdk = GrFingerService::get_instance();
    $fingerprint_sdk->prepare($fingerprint_value);
    foreach($patients as $patient) {
      if($patient->has_fingerprint($fingerprint_name)){
        $indentify = $fingerprint_sdk->identify($patient->$fingerprint_name);
        $results[] = $patient;
      }
    }
    return $results;
  }
}
