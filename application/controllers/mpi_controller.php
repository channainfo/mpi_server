<?php
class MpiController extends BaseController {
  var $view_params = null;
  var $auth = null;
  var $_raw_datas= array();

	/**
	 * The construction of this controller
	 * @param boolean $load_fingerprint: if we need to reload the fingerprint SDK
	 * @param boolean $init_session: if we need to create session
	 */
  function __construct($load_fingerprint=false, $init_session=true) {
    parent::__construct();
    if ($load_fingerprint)
      require FCPATH.'application/libraries/GrFingerService.php';
  }

  function before_action() {
    parent::before_action();
    $this->require_user_signed_in();
  }

  function init(){
    parent::init();
    $this->load->helper("html");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->library('form_validation');

    require_once BASEPATH.'core/Model.php';
    require_once APPPATH.'libraries/Imodel.php';
    require_once APPPATH.'libraries/Isession.php';
    require_once APPPATH.'libraries/Iencryption.php';
    require_once APPPATH.'libraries/Iconstant.php';
    require_once APPPATH.'libraries/ILog.php';
    require_once APPPATH.'libraries/mpi_auth_helper.php';
    require_once APPPATH.'libraries/params_helper.php';
    require_once APPPATH.'libraries/app_helper.php';
    require_once APPPATH.'libraries/paginator.php';
    require_once APPPATH.'helpers/mpi_helper.php';

    require_once APPPATH.'models/user.php';
    require_once APPPATH.'models/api_access_log.php';
    require_once APPPATH.'models/scope.php';
    require_once APPPATH.'models/field.php';
    require_once APPPATH.'models/field_value.php';
    require_once APPPATH.'models/application.php';
    require_once APPPATH.'models/patient.php';
    require_once APPPATH.'models/patient.php';
    require_once APPPATH.'models/visit.php';
    require_once APPPATH.'models/site.php';
    require_once APPPATH.'models/province.php';
    require_once APPPATH.'models/patient_module.php';
    require_once APPPATH.'models/dynamic_value.php';
    require_once APPPATH.'models/vcct_from_oiart.php';

    ILog::getInstance();
    $session_status = Isession::initializeSession();

    $this->auth = new MpiAuthHelper();
    $this->view_params = new ParamsHelper();
  }

  function require_user_signed_in(){
    if(!$this->auth->current_user()) {
      Isession::setFlash("error", "Your need to sign in to access the page");
      redirect(site_url("login"));
      exit;
    }
  }

  function current_user(){
    return $this->auth->current_user();
  }

  function required_admin_user(){
    $current_user = $this->auth->current_user();
    if(!$current_user || !$current_user->is_admin()){
      Isession::setFlash("error", "Your don't have access to this resource");
      redirect(site_url("errors/index"));
      exit;
    }
  }

  public function layout() {
    return "general";
  }

  function set_view_variables($datas=array()){
    foreach($datas as $key => $value){
      $this->_raw_datas[$key] = $value;
    }
  }

  function render_view($action=null, $status=200) {
    $template_name = "templates/".$this->layout();

    $controller_name = $this->router->fetch_class();
    $action_name = $action ? $action : $this->router->fetch_method();
    $view_name = "{$controller_name}/{$action_name}";

    $this->set_view_variables(array( "view_params" => $this->view_params,
                                     "current_user" => $this->auth->current_user()));

    $this->http_response_code($status);
    $this->load->template($template_name, $view_name, Iconstant::MPI_APP_NAME, $this->_raw_datas);
    $this->after_action();
  }
}
