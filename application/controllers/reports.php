<?php
class Reports extends MpiController {
	/**
	 * Constructor of reports controller
	 */
	function __construct() {
		parent::__construct();
		
	}
    function index() {
    	$this->render_view();
    }
    
    private function sumbit_report() {
    	$criteria = $_POST;
    	$criteria["cri_pro_code"] = trim($criteria["cri_pro_code"]);
    	$criteria["date_from"] = trim($criteria["date_from"]);
    	$criteria["date_to"] = trim($criteria["date_to"]);

    	$error = "";
    	
    	if (!isset($criteria["report_type"])) :
	    	if ($error=="" && $criteria["cri_pro_code"] == "") :
	    		$error = "Please select a province"; 
	    	endif;
    	endif;
    	
    	if ($error=="" && $criteria["date_from"] != null && date_html_to_php($criteria["date_from"]) == null) :
    		$error = "Start date format is not correct"; 
    	endif;
    	
    	if ($error=="" && $criteria["date_to"] != null && date_html_to_php($criteria["date_to"]) == null) :
    		$error = "End date format is not correct"; 
    	endif;
    	
    	Isession::setFlash("error", $error);
    	
    	$session_data = Isession::getCriteria("reports");
    	if ($session_data != null) :
    		$criteria = array_merge($session_data, $criteria);
    	endif;
    	if (!isset($_POST["empty_exclude"])) :
    		unset($criteria["empty_exclude"]);
    	endif;
    	Isession::setCriteria("reports", $criteria);
    }
    
    /**
     * Display parameter for generate VCCT report
     * Enter description here ...
     */
    function oiart() {
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("site");
    	$data["provinces"] = $this->site->getProvinces();
    	
    	$session_data = Isession::getCriteria("reports");
    	$criteria = array(	"cri_pro_code" => "",
    						"date_from" => "",
    						"date_to" => ""
    					);
    	
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	if ($first_access || $data["error"] != null) :
    	    $data = array_merge($data, $criteria);
    	    $data["reports"] = null;
    	    Isession::setCriteria("report_oiart", $data["reports"]);
    	    $this->load->template("templates/general", "reports/oi_art", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	Isession::setCriteria("reports", $criteria);
    	
    	$this->load->model("report");
    	$data["reports"] = $this->report->get_oi_art_report_data($criteria);
    	Isession::setCriteria("report_oiart", $data["reports"]);
    	$data = array_merge($data, $criteria);
    	$this->load->template("templates/general", "reports/oi_art", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Generate OI/ART report
     * Enter description here ...
     */
	function submitoiart() {
    	$this->sumbit_report();
    	redirect(site_url("reports/oiart"));
    }
    
    /**
     * Display parameter for generate VCCT report
     */
    function vcct() {
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("site");
    	$data["provinces"] = $this->site->getProvinces();
    	
    	$session_data = Isession::getCriteria("reports");
    	$criteria = array(	"cri_pro_code" => "",
    						"date_from" => "",
    						"date_to" => ""
    					);
    	
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	if ($first_access || $data["error"] != null) :
    	    $data = array_merge($data, $criteria);
    	    $data["reports"] = null;
    	    Isession::setCriteria("report_vcct", $data["reports"]);
    	    $this->load->template("templates/general", "reports/vcct", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	Isession::setCriteria("reports", $criteria);
    	
    	$this->load->model("report");
    	$data["reports"] = $this->report->get_vcct_report_data($criteria);
    	$data = array_merge($data, $criteria);
    	Isession::setCriteria("report_vcct", $data["reports"]);
    	$this->load->template("templates/general", "reports/vcct", Iconstant::MPI_APP_NAME, $data);
    }
    
	 /**
     * Generate OI/ART report
     * Enter description here ...
     */
	function submitvcct() {
    	$this->sumbit_report();
    	redirect(site_url("reports/vcct"));
    }
    
    /**
     * Call STD report
     */
    function std() {
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("site");
    	$data["provinces"] = $this->site->getProvinces();
    	
    	$session_data = Isession::getCriteria("reports");
    	$criteria = array(	"cri_pro_code" => "",
    						"date_from" => "",
    						"date_to" => ""
    					);
    	
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	if ($first_access || $data["error"] != null) :
    	    $data = array_merge($data, $criteria);
    	    $data["reports"] = null;
    	    Isession::setCriteria("report_std", $data["reports"]);
    	    $this->load->template("templates/general", "reports/std", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	Isession::setCriteria("reports", $criteria);
    	
    	$this->load->model("report");
    	$data["reports"] = $this->report->get_std_report_data($criteria);
    	$data = array_merge($data, $criteria);
    	Isession::setCriteria("report_std", $data["reports"]);
    	$this->load->template("templates/general", "reports/std", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Submit STD
     */
    function submitstd() {
    	$this->sumbit_report();
    	redirect(site_url("reports/std"));
    }
    
    /**
     * Display duplicate report
     */
    function duplicate() {
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("site");
    	$data["provinces"] = $this->site->getProvinces();
    	
    	$session_data = Isession::getCriteria("reports");
    	$criteria = array(	"cri_pro_code" => "",
    						"date_from" => "",
    						"date_to" => "",
    						"report_type" => 0
    					);
    	
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	if ($first_access || $data["error"] != null) :
    	    $data = array_merge($data, $criteria);
    	    $data["reports"] = null;
    	    Isession::setCriteria("report_duplicate", $data["reports"]);
    	    $this->load->template("templates/general", "reports/duplicate", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	Isession::setCriteria("reports", $criteria);
    	
    	$this->load->model("report");
    	if ($criteria["report_type"] == 0) :
    		$data["reports"] = $this->report->duplicate_oiart_register($criteria);
    	else :
    		$data["reports"] = $this->report->duplicate_vcct_test($criteria);
    	endif;
    	$data = array_merge($data, $criteria);
    	Isession::setCriteria("report_duplicate", $data["reports"]);
    	$this->load->template("templates/general", "reports/duplicate", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Submit the duplicate report
     */
    function submitduplicate() {
    	$this->sumbit_report();
    	redirect(site_url("reports/duplicate"));
    }
    
    /**
     * Routine report
     */
    function routine() {
    	$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("site");
    	$data["provinces"] = $this->site->getProvinces();
    	
    	$session_data = Isession::getCriteria("reports");
    	$criteria = array(	"cri_pro_code" => "",
    						"date_from" => "",
    						"date_to" => ""
    					);
    	
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	if ($first_access || $data["error"] != null) :
    	    $data = array_merge($data, $criteria);
    	    $data["reports"] = null;
    	    $this->load->template("templates/general", "reports/routine", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	Isession::setCriteria("reports", $criteria);
    	
    	$this->load->model("report");
    	
    	$data["reports"] = $this->report->get_routine_report_data($criteria);
    	Isession::setCriteria("report_routine", $data["reports"]);
    	
    	$data = array_merge($data, $criteria);
    	$this->load->template("templates/general", "reports/routine", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Submit routine report
     */
	function submitroutine() {
    	$this->sumbit_report();
    	redirect(site_url("reports/routine"));
    }
    
    /**
     * Display fingerprint report
     */
    function fingerprint() {
   		$data = array();
    	$data["error"] = Isession::getFlash("error");
    	$data["error_list"] = Isession::getFlash("error_list");
    	$data["success"] = Isession::getFlash("success");
    	
    	$this->load->model("site");
    	$data["provinces"] = $this->site->getProvinces();
    	$old_criteria = Isession::getCriteria("old_reports");
    	$session_data = Isession::getCriteria("reports");
    	$criteria = array(	"cri_pro_code" => "",
    						"date_from" => "",
    						"date_to" => ""
    					);
    	
    	$first_access = false;
    	if ($session_data != null) :
    		$criteria = array_merge($criteria, $session_data);
    	else : 
    	    $first_access = true;
    	endif;
    	
    	
    	if ($first_access || $data["error"] != null) :
    	    $data = array_merge($data, $criteria);
    	    $data["reports"] = null;
    	    $this->load->template("templates/general", "reports/fingerprint", Iconstant::MPI_APP_NAME, $data);
    	    return;
    	endif;
    	
    	Isession::setCriteria("reports", $criteria);
    	
    	$this->load->model("report");
    	
    	if ($old_criteria != null && ($old_criteria["date_from"] == $criteria["date_from"] && $old_criteria["date_to"] == $criteria["date_to"])) :
    		$result = Isession::getCriteria("report_fingeprint");
    	else:
    		$patients = $this->report->get_fingerprint_report_data($criteria);
    	
	    	require FCPATH.'application/libraries/GrFingerService.php';
	    	$grFingerprint = new GrFingerService();
	        if (!$grFingerprint->initialize()) :
	        	$data = array_merge($data, $criteria);
	            $data["error"] = "SDK is busy with other service";
	            $data["reports"] = null;
	            Isession::setCriteria("report_fingeprint", $data["reports"]);
	    	    $this->load->template("templates/general", "reports/fingerprint", Iconstant::MPI_APP_NAME, $data);
	    	    return;
	        endif;
		    
	        $result = array();
	        $this->findSameFingerprint($patients, $grFingerprint, $result);
	        $grFingerprint->finalize();
        endif;
        $data["reports"] = $result;
    	Isession::setCriteria("report_fingeprint", $data["reports"]);
        
    	$data = array_merge($data, $criteria);
    	$this->load->template("templates/general", "reports/fingerprint", Iconstant::MPI_APP_NAME, $data);
    }
    
    /**
     * Submit fingeprint
     */
	function submitfingerprint() {
		$session_data = Isession::getCriteria("reports");
		Isession::setCriteria("old_reports", $session_data);
    	$this->sumbit_report();
    	redirect(site_url("reports/fingerprint"));
    }
    
    
 	private function findSameFingerprint($patients, $grFingerprint, &$result) {
    	if (count($patients) <= 1) :
    		return;
    	endif;
    	//$this->load->model("report");
    	while (count($patients) > 1) :
	    	$prepare_patient =  array_shift($patients);
	    	$fp = $this->get_valid_fingerprint($prepare_patient);
	    	$fp_name = $fp[0];
	    	
	    	$ret = $grFingerprint->GrFingerX->IdentifyPrepareBase64($prepare_patient[$fp_name], $grFingerprint->GR_DEFAULT_CONTEXT);
	        if ($ret != $grFingerprint->GR_OK) :
	            $result["error"] = "Fingerprint (".$fingerprint.") template is not correct";
		        return;
	        endif;
	        
	        $sub_result = array();
	        //array_push($sub_result, $prepare_patient["pat_id"]);
	        foreach ($patients as $row) :
	        	$score = 0;
	        	if ($row[$fp_name] == null || $row[$fp_name] == "") :
	                continue;
	            endif;
	            $ret = $grFingerprint->GrFingerX->IdentifyBase64($row[$fp_name],$score,$grFingerprint->GR_DEFAULT_CONTEXT);
	            if( $ret == $grFingerprint->GR_MATCH) : 
	            	array_push($sub_result, $row["pat_id"]);
	            endif;
	        endforeach;
	        
	        if (count($sub_result) > 0) :
		        foreach ($sub_result as $row) :
		        	unset($patients[$row]);
		        endforeach;
		        
		        array_push($sub_result, $prepare_patient["pat_id"]);
		        
		        $pat_id_list = implode("','", $sub_result);
		        $record = $this->report->number_visit($pat_id_list);
		        array_push($result, $record);
	        endif;
        endwhile;
        
    }
    
	/**
     * 
     * @param unknown_type $grFingerprint
     */
    private function get_valid_fingerprint($reference) {
    	$arr = array();
    	foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint) :
    	    if (isset($reference[$fingerprint]) && $reference[$fingerprint] != "") :
	            array_push($arr, $fingerprint);
        	endif;
    	endforeach;
        return $arr;
    }
    
    function exportfingerprint() {
    	$file_name = "fingerprint_".date("YmdHis").".csv";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		// Header
		echo "No.,Number of fingeprints,Patient List".PHP_EOL;
		$data = Isession::getCriteria("report_fingeprint");
		$session_data = Isession::getCriteria("reports");
		// write data
		$row_nb = 0; 
		if ($data != null && count($data) > 0) :
			foreach($data as $sitecode => $row) :
				$pat_id_list = array();
				foreach ($row as $pat_id=>$nb_row) :
		      		if (!isset($session_data["empty_exclude"]) || (isset($session_data["empty_exclude"]) && $nb_row > 0)) :
		      			array_push($pat_id_list, $pat_id);
		      		else :
		      		    continue;
		      		endif;
		      	endforeach;
		      	if (count($pat_id_list) <= 1) :
		      		continue;
		      	endif;
		  		$row_nb++;
				echo $row_nb.",".count($pat_id_list).",".implode(":", $pat_id_list).PHP_EOL;
			endforeach;
		endif;
    }
    
    function exportoiart() {
    	$file_name = "oi_art_".date("YmdHis").".csv";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		// Header
		echo "Site code,Site Name,Fingerprint patient,Behave of patient,Shown at STD".PHP_EOL;
		$data = Isession::getCriteria("report_oiart");
		// write data
		if ($data != null && count($data) > 0) :
			$row_nb = 0; 
	        foreach($data as $sitecode => $row) : 
	      		$row_nb++;
				echo $sitecode.",".$row["site_name"].",".
					 (!isset($row["nb_register"]) ? 0 : $row["nb_register"]).",".
					 (!isset($row["nb_on_behave"]) ? 0 : $row["nb_on_behave"]).",".
					 (!isset($row["nb_reach_std"]) ? 0 : $row["nb_reach_std"]).PHP_EOL;
			endforeach;
		endif;
    }
    
    function exportvcct() {
    	$file_name = "vcct_".date("YmdHis").".csv";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		// Header
		echo "Site code,Site Name,Fingerprint patient,Positive,Shown at OI/ART,Shown at STD".PHP_EOL;
		$data = Isession::getCriteria("report_vcct");
		// write data
		if ($data != null && count($data) > 0) :
			$row_nb = 0; 
	        foreach($data as $sitecode => $row) : 
	      		$row_nb++;
				echo $sitecode.",".$row["site_name"].",".
					 (!isset($row["nb_register"]) ? 0 : $row["nb_register"]).",".
					 (!isset($row["nb_positive"]) ? 0 : $row["nb_positive"]).",".
					 (!isset($row["nb_reach_oiart"]) ? 0 : $row["nb_reach_oiart"]).",".
					 (!isset($row["nb_reach_std"]) ? 0 : $row["nb_reach_std"]).PHP_EOL;
			endforeach;
		endif;
    }
    
    function exportstd() {
    	$file_name = "std_".date("YmdHis").".csv";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		// Header
		echo "Site code,Site Name,Fingerprint patient,Shown at STD".PHP_EOL;
		$data = Isession::getCriteria("report_std");
		// write data
		if ($data != null && count($data) > 0) :
			$row_nb = 0; 
	        foreach($data as $sitecode => $row) : 
	      		$row_nb++;
				echo $sitecode.",".$row["site_name"].",".
					 (!isset($row["nb_register"]) ? 0 : $row["nb_register"]).",".
					 (!isset($row["nb_reach_vcct"]) ? 0 : $row["nb_reach_vcct"]).PHP_EOL;
			endforeach;
		endif;
    }
    
    function exportduplicateoiart() {
    	//$this->load->model("report");
    	$file_name = "duplicate_oiart_".date("YmdHis").".csv";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		// Header
		echo "Patient id,Sex,Age,Nb. Register".PHP_EOL;
		$data = Isession::getCriteria("report_duplicate");
		// write data
		if ($data != null && count($data) > 0) :
			$row_nb = 0; 
	        foreach($data as $row) : 
	      		$row_nb++;
				echo $row["pat_id"].",".($row["pat_gender"] == "1" ? "Male" : "Female").",".
					 $row["pat_age"].",".
					 $row["nb_register"].PHP_EOL;
			endforeach;
		endif;
    }
    
    function exportroutine() {
    	//$this->load->model("report");
    	$file_name = "routine_".date("YmdHis").".csv";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		// Header
		echo "Site code,Site Name,Last sync date".PHP_EOL;
		$data = Isession::getCriteria("report_routine");
		// write data
		if ($data != null && count($data) > 0) :
			$row_nb = 0; 
	        foreach($data as $sitecode => $row) :
	      		$row_nb++;
				echo $row["site_code"].",".
					 $row["site_name"].",".
					 (!isset($row["last_sync_date"]) ? "" : datetime_mysql_to_html($row["last_sync_date"])).
					 PHP_EOL;
			endforeach;
		endif;
    }
    
    function exportduplicatevcct() {
    	$file_name = "duplicate_vcct_".date("YmdHis").".csv";
    	header("Content-disposition: attachment; filename=".$file_name);
		header("Content-type: text/plain");
		// Header
		echo "Patient id,Nb. of test,Positive,Negative".PHP_EOL;
		$data = Isession::getCriteria("report_duplicate");
		// write data
		if ($data != null && count($data) > 0) :
			$row_nb = 0; 
	        foreach($data as $pat_id=>$row) : 
	      		$row_nb++;
				echo $pat_id.",".
					 ($row["positive"] + $row["negative"]).",".
					 $row["positive"].",".
					 $row["negative"].",".
					 PHP_EOL;
			endforeach;
		endif;
    }
    
    function summary_list($type, $site_code) {
    	$criteria = Isession::getCriteria("reports");
    	$data = array();
    	$patients = array();
    	$data["report_type"] = $type;
    	$this->load->model("report");
    	switch ($type) {
    		case 1: // VCCT show at OI/ART
    			$patients = $this->report->vcct_show_at_oi($site_code, $criteria);
    			break;
    		case 2: // VCCT show at STD  
    			$patients = $this->report->vcct_show_at_std($site_code, $criteria);
    			break;
    		case 3: // STD show at VCCT  
    			$patients = $this->report->std_show_at_vcct($site_code, $criteria);
    			break;
    		case 4:
    			$patients = $this->report->oiart_show_at_std($site_code, $criteria);
    			break;
    	}
    	$data["patients"] = $patients;
    	
    	$this->load->template("templates/blank", "reports/summary_list", Iconstant::MPI_APP_NAME, $data);
    }
    
    function patient_detail($pat_id) {
    	$this->load->model("patient");
        $patient = $this->patient->getPatientById($pat_id);
        $var = array();
        array_push($var, $pat_id);
        $visit = $this->patient->getVisits($var);
        $data = array();
        $data["patient"] = $patient;
        $data["visit_list"] = $visit;
        $this->load->template("templates/blank", "reports/report_patient_detail", Iconstant::MPI_APP_NAME, $data);
    }
}
