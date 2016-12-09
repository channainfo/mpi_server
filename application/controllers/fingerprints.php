<?php
class Fingerprints extends MpiController {
  function index() {
    $params = $this->filter_params(array("date_from", "date_to", "type", "pro_code"));
    $provinces = Province::all();
    $patients = DuplicateFingerprint::patients();
    return $this->render_view(array(
      "patients" => $patients,
      "provinces" => $provinces,
      "params" => $params
    ));
  }
}
