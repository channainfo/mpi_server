<?php
class BaseController extends CI_Controller {
  var $skip_before_action = array();

  function __construct() {
    parent::__construct();
    $CI =& get_instance();
    define("CHAR_SET", $CI->config->item("charset"));
    require_once APPPATH.'libraries/http_status_code.php';
    require_once APPPATH.'libraries/ILog.php';
    session_start();

    $action = $this->router->fetch_method();

    if(!in_array($action, $this->skip_before_action))
      $this->before_action();
  }

  function before_action(){
    return true;
  }

  function filter_params($keys){
    $result = array();
    foreach($keys as $key){
      if(isset($_REQUEST[$key]))
        $result[$key] = $_REQUEST[$key];
    }
    return $result;
  }

  function render_json($object) {
    header('Content-Type: application/json');
    echo json_encode($object);
    exit;
  }

  function render_error($errors, $status) {
    http_response_code($status);
    $this->render_json($errors);
  }

  function render_unauthorized($errors) {
    $this->render_error($errors, 401);
  }
}
