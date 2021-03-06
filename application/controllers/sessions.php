<?php

class Sessions extends MpiController {
  var $skip_before_action = array("add", "create");

  function layout(){
    return parent::layout();
  }

  function add() {
    $current_user = $this->auth->current_user();

    if($this->auth->current_user())
      return redirect(site_url("homes/index"));
    else
      $this->render_view("add");
  }

  function create() {
    $params = $this->filter_params(array("login", "password"));
    $user = User::authenticate($params["login"], $params["password"]);
    if ($user == null){
      Isession::setFlash("error", "Login or Password is not correct");
      $this->render_view("add");
    }
    else{
      $this->auth->sign_in($user);
      redirect("homes/index");
    }
  }

  function filter_auth_params(){
    return array("login" => isset($_POST["login"]) ?  $_POST["login"] : "",
                 "password" => isset($_POST["password"]) ? $_POST["password"] : "" );
  }

  function destroy(){
    $this->auth->sign_out();
    Isession::setFlash("success", "You have successfully signed out");
    redirect("sessions/add");
  }

}
