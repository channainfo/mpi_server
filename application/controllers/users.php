<?php
/**
 * Users controller
 * @author Sokha RUM
 */
class Users extends MpiController {

  function before_action(){
    $this->required_admin_user();
  }
	function userlist() {
		$user = Isession::getUser();
		if ($user["grp_id"] != Iconstant::USER_ADMIN) :
		    redirect(site_url("main/errorpage"));
		    return;
		endif;
		$data = array();
		$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	$this->load->model("usermodel");
    	$data["user_list"] = $this->usermodel->user_list();
        $this->load->template("templates/general", "users/user_list", Iconstant::MPI_APP_NAME, $data);
    }

    /**
     * Form of user creation
     * Enter description here ...
     */
    function usernew() {
    	$user = Isession::getUser();
		if ($user["grp_id"] != Iconstant::USER_ADMIN) :
		    redirect(site_url("main/errorpage"));
		    return;
		endif;
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	$this->load->model("usermodel");
    	$data["user_login"] = "";
    	$data["user_lname"] = "";
    	$data["user_fname"] = "";
    	$data["user_email"] = "";
    	$data["grp_id"] = 2;
    	$user_data = Isession::getFlash("user_data");
    	if (!is_null($user_data) && is_array($user_data)) :
    	    $data = array_merge($data, $user_data);
    	endif;
    	$this->load->model("usermodel");
    	$data["group_list"] = $this->usermodel->group_list();
        $this->load->template("templates/general", "users/user_new", Iconstant::MPI_APP_NAME, $data);
    }

    /**
     * Create a new user
     */
    function usersave() {
    	$cur_user = Isession::getUser();
    	//$user = Isession::getUser();
		if ($cur_user["grp_id"] != Iconstant::USER_ADMIN) :
		    redirect(site_url("main/errorpage"));
		    return;
		endif;
        $data = array();

    	$data["user_login"] = trim($_POST["user_login"]);
    	$data["user_lname"] = trim($_POST["user_lname"]);
    	$data["user_fname"] = trim($_POST["user_fname"]);
    	$data["user_email"] = trim($_POST["user_email"]);
    	$data["grp_id"] = $_POST["grp_id"];

    	$this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules("user_login", "Login", "trim|required|alpha_numeric");
        $this->form_validation->set_rules("user_lname", "Last name", "trim|required");
        $this->form_validation->set_rules("user_fname", "First name", "trim|required");
        $this->form_validation->set_rules("user_email", "Email", "trim|valid_email");
        $this->form_validation->set_rules("user_pwd", "Password", "required");

        $error = "";
        if ($this->form_validation->run() == FALSE) :
   	        $this->form_validation->set_error_delimiters("<li>", "</li>");
		    $error = validation_errors();
        endif;

	    if ($error != null) :
	    	Isession::setFlash("user_data", $data);
	        Isession::setFlash("error_list", "<ul>".$error."<ul>");
            redirect("users/usernew");
            return;
	    endif;

	    $this->load->model("usermodel");
	    $user_found = $this->usermodel->user_by_login($data["user_login"]);
	    if ($user_found != null) :
	    	Isession::setFlash("user_data", $data);
	    	Isession::setFlash("error_list", "<ul><li>User with login ".$data["user_login"]." already exist</li><ul>");
            redirect("users/usernew");
            return;
	    endif;

	    if ($_POST["user_pwd"] != $_POST["user_confirm_pwd"]) :
	    	Isession::setFlash("user_data", $data);
	        Isession::setFlash("error_list", "<ul><li>Confirm Password is not correct</li><ul>");
            redirect("users/usernew");
            return;
	    endif;

	    $data["user_pwd"] = $_POST["user_pwd"];
	    $data["user_create"] = $cur_user["user_id"];
	    $this->usermodel->user_new($data);
	    Isession::setFlash("success", "User was successfully created");
	    redirect(site_url("users/userlist"));
    }

    /**
     * User modification form
     * @param int $user_id
     */
    function useredit($user_id) {
    	$user = Isession::getUser();
		if ($user["grp_id"] != Iconstant::USER_ADMIN) :
		    redirect(site_url("main/errorpage"));
		    return;
		endif;

        if (!is_nint($user_id)) :
            redirect("users/userlist");
            return;
        endif;

        $this->load->model("usermodel");
        $user = $this->usermodel->user_by_id($user_id);
        if ($user == null) :
            Isession::setFlash("error", "User with id ".$user_id." was not found");
            redirect("users/userlist");
            return;
        endif;

        $data = array();
        $data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");

    	$data = array_merge($data, $user);
    	$user_data = Isession::getFlash("user_data");
    	if (!is_null($user_data) && is_array($user_data)) :
    	    $data = array_merge($data, $user_data);
    	endif;
    	$data["group_list"] = $this->usermodel->group_list();
        $this->load->template("templates/general", "users/user_edit", Iconstant::MPI_APP_NAME, $data);
    }

    /**
     * update user
     */
    function userupdate() {
        $cur_user = Isession::getUser();
		if ($cur_user["grp_id"] != Iconstant::USER_ADMIN) :
		    redirect(site_url("main/errorpage"));
		    return;
		endif;
        $data = array();

        $data["user_id"] = $_POST["user_id"];
    	$data["user_login"] = trim($_POST["user_login"]);
    	$data["user_lname"] = trim($_POST["user_lname"]);
    	$data["user_fname"] = trim($_POST["user_fname"]);
    	$data["user_email"] = trim($_POST["user_email"]);
    	$data["grp_id"] = $_POST["grp_id"];

    	$this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules("user_login", "Login", "trim|required|alpha_numeric");
        $this->form_validation->set_rules("user_lname", "Last name", "trim|required");
        $this->form_validation->set_rules("user_fname", "First name", "trim|required");
        $this->form_validation->set_rules("user_email", "Email", "trim|valid_email");

        $error = "";
        if ($this->form_validation->run() == FALSE) :
   	        $this->form_validation->set_error_delimiters("<li>", "</li>");
		    $error = validation_errors();
        endif;

	    if ($error != null) :
	    	Isession::setFlash("user_data", $data);
	        Isession::setFlash("error_list", "<ul>".$error."<ul>");
            redirect("users/useredit/".$data["user_id"]);
            return;
	    endif;

	    $this->load->model("usermodel");
	    $user_found = $this->usermodel->user_by_login($data["user_login"]);
	    if ($user_found != null && $data["user_id"] != $user_found["user_id"]) :
	    	Isession::setFlash("user_data", $data);
	    	Isession::setFlash("error_list", "<ul><li>User with login ".$data["user_login"]." already exist</li><ul>");
            redirect("users/useredit/".$data["user_id"]);
            return;
	    endif;

	    $data["user_update"] = $cur_user["user_id"];
	    $this->usermodel->user_update($data);
	    Isession::setFlash("success", "User was successfully updated");
	    redirect(site_url("users/useredit/".$data["user_id"]));
    }

    /**
     * Delete a user with specific user id
     * @param int $user_id
     */
    function userdelete($user_id) {
    	$user = Isession::getUser();
		if ($user["grp_id"] != Iconstant::USER_ADMIN) :
		    redirect(site_url("main/errorpage"));
		    return;
		endif;
        if (!is_nint($user_id)) :
            redirect("users/userlist");
            return;
        endif;

        $this->load->model("usermodel");
        $user_found = $this->usermodel->user_by_id($user_id);
        if ($user_found == null) :
            Isession::setFlash("error", "User with id ".$user_id." was not found");
            redirect("users/userlist");
            return;
        endif;

        if ($user_id == $user["user_id"]) :
            Isession::setFlash("error", "You cannot delete yourself");
            redirect("users/userlist");
            return;
        endif;

        $this->load->model("usermodel");
        $this->usermodel->delete_user($user_id);
        Isession::setFlash("success", "User ".$user_found["user_login"]." have been deleted");
        redirect(site_url("users/userlist"));
    }

    /**
     * Regenerate the password randonly
     * @param int $user_id
     */
    function generagepwd($user_id) {
    	$user = Isession::getUser();
		if ($user["grp_id"] != Iconstant::USER_ADMIN) :
		    redirect(site_url("main/errorpage"));
		    return;
		endif;
        if (!is_nint($user_id)) :
            redirect("users/userlist");
            return;
        endif;

        $this->load->model("usermodel");
        $user_found = $this->usermodel->user_by_id($user_id);
        if ($user_found == null) :
            Isession::setFlash("error", "User with id ".$user_id." was not found");
            redirect("users/userlist");
            return;
        endif;

        $new_pwd = uniqid();
        $this->usermodel->update_pwd($user_id, $new_pwd, $user["user_id"]);
        Isession::setFlash("success", "Password has beed regenerated to: ".$new_pwd);
        redirect("users/useredit/".$user_id);
    }
}
