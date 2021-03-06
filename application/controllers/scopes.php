<?php
class Scopes extends MpiController {

  function before_action() {
    parent::before_action();
    $this->required_admin_user();
    $this->load->model("scope");
    $this->load->model("field");
  }

  function index() {
    $paginate_scopes = Scope::paginate(array(), "name ASC");
    $this->set_view_variables(array(
      "paginate_scopes" => $paginate_scopes,
      "fields" => Field::mapper(),
    ));
    $this->render_view();
  }

  function add() {
    $scope = new Scope();
    $this->set_view_variables(array("scope" => $scope));
    $this->render_view();
  }

  function create(){
    $scope = new Scope();
    $scope->set_attributes($this->scope_params());

    if($scope->save()) {
      Isession::setFlash("success", "Scope has been successfully created");
      redirect(site_url("scopes/index"));
      return;
    }
    else{
      $this->set_view_variables(array("scope" => $scope));
      $this->render_view("add");
    }
  }

  function edit($id) {
    $scope = Scope::find($id);
    $this->set_view_variables(array("scope" => $scope));
    $this->render_view();
  }

  function update($id){
    $scope = Scope::find($id);
    if($scope->update_attributes($this->scope_params())){
      Isession::setFlash("success", "Scope has been successfully updated");
      redirect(site_url("scopes/index"));
    }
    else{
      $this->set_view_variables(array("scope" => $scope));
      $this->render_view("edit");
    }
  }

  public function scope_params(){
    $params =  $this->filter_params(array("name", "searchable_fields", "display_fields", "updatable_fields"));

    if(!isset($params['updatable_fields']))
      $params['updatable_fields'] = array();
    if(!isset($params['searchable_fields']))
      $params['searchable_fields'] = array();
    if(!isset($params['display_fields']))
      $params['display_fields'] = array();
    return $params;
  }

  function delete($id) {
    $scope = Scope::find($id);

    if($scope && $scope->delete())
      Isession::setFlash("success", "Scope has been removed");
    else
      Isession::setFlash("error", "Failed to remove scope");
    redirect(site_url("scopes/index"));
  }
}
