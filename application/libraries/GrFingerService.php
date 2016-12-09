<?php
class GrFingerService {
  public $GR_OK = 0;
  public $GR_MATCH = 1;
  public $GR_DEFAULT_CONTEXT = 0;
  public $GrFingerX;
  public $db;

  static $instance = null;

  static function instance() {
    if(GrFingerService::$instance) {
      $instance = GrFingerService::$instance;
    }
    GrFingerService::$instance = new GrFingerService();
    return GrFingerService::$instance;
  }

  public function __construct(){
    $this->create_com_object();
    $this->initialize();
  }

  public function reload() {
    $com_id = "{A9995C7C-77BF-4E27-B581-A4B5BBD90E50}";
    com_load_typelib($com_id);
  }

  public function create_com_object() {
    $com_name = "GrFingerX.GrFingerXCtrl.1";
    $this->GrFingerX = new COM($com_name) or die ('Could not initialise object.');
  }

  public function initialize(){
    try {
      return $this->GrFingerX->Initialize() == $this->GR_OK;
    }
    catch (Exception $exception) {
      $this->reload();
      $this->create_com_object();
      return $this->GrFingerX->Initialize() != $this->GR_OK ;
    }
  }

  function prepare($value) {
    $result = $this->GrFingerX->IdentifyPrepareBase64($value, $this->GR_DEFAULT_CONTEXT);
    return $result == $this->GR_OK;
  }

  function identify($value) {
    $score = 0;
    $result = $this->GrFingerX->IdentifyBase64($value, $score, $this->GR_DEFAULT_CONTEXT);
    return $result  == $this->GR_OK;
  }

  public function finalize() {
    $this->GrFingerX->Finalize();
  }
}

?>
