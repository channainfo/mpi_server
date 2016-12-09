<?php
class Test extends MpiController {
  var $skip_before_action = "*";


  function patient_with_fingerprint() {
    $params = $this->filter_params(array("date_from", "date_to"));
    $patients = Patient::with_fingerprints($params);
    ILog::d("patients ", $patients, 1);
  }

  function console(){

    $attrs = array("member_login" => "yourins", "site_code" => "0202", "member_pwd" => "123456");
    // $member = new Member($attrs);
    // $member->save();
    // ILog::debug_message("errors", $member->get_errors());

    $member = Member::find(30);
    $member = new Member($attrs);
    $member->set_attributes($attrs);
    $member->save();

    // $member->update_attributes($attrs);

    ILog::debug_message("errors", $member->get_errors(), 1, 1);
  }

  //filter out pat_id not allow to update
  function update_patient() {
    $patient = Patient::find_by(array("pat_id" => "KH001100000001"));
    $params = array(
      "pat_register_site" => "0101",
      "pat_age" => 60,
      "pat_gender" => 1,
      "is_referred" => false,

      "p_is_referral" => "False",
      "p_dynamic_field1" => "1000",
      "p_dynamic_field2" => "70",
      "p_Fdafdafdafds" => "300"
    );
    $patient->update_attributes($params);

  }

  function patients() {
    $ids = array( 3683,3684,3685,3686,3687 );
    $records = Patient::all(array("id" => $ids));
    $dynamic_value = new DynamicValue($records);
    $result = $dynamic_value->result();
    ILog::debug_message("Patients", $result);

    $ids = array(2961,2962,2963,2964,2965,2966 );
    $records = Visit::all(array("visit_id" => $ids));
    $dynamic_value = new DynamicValue($records);
    $result = $dynamic_value->result();
    ILog::debug_message("Visits", $result, 1, 1);

  }

  function enroll_patient() {
    $params = array(
      "pat_register_site" => "0101",
      "pat_age" => 60,
      "pat_gender" => 1,
      "is_referred" => false,

      "p_is_referral" => "False",
      "v_tb" => "1111",
      "p_dynamic_field1" => "10",
      "p_dynamic_field2" => "30",
      "pat_id" => "KH002100003000", //override
    );
    $patient = PatientModule::enroll($params);
    ILog::debug_message("Patient", $patient);
  }


  function enroll_visit() {
    $field = new Field(array("type" => "DateTime"));

    $params = array(
      "pat_id"=> "KH002100000002",

      "pat_age"=> "80", "serv_id"=> "2", "visit_date"=> "2016-12-06",
      "site_code"=> "0202", "ext_code"=> "ex1", "ext_code_2"=> "ex2",
      "refer_to_vcct"=> "2", "refer_to_oiart"=> "1", "refer_to_std"=> "",
      "info"=> "positive", "vcctnumber"=> "1000", "vcctsite" => "V01-02",

      "v_dynamic_field1" => "False",
      "v_dynamic_field2" => "hello",
      "v_dynamic_field3" => "12dfdsa",
      "v_dynamic_field4" => "12.34",
      "v_dynamic_field5" => "2016-09-10",
      "v_dynamic_field6" => "2016-10-10 12:10:10",
      "v_dynamic_field7" => "zzzzz",

    );

    $_POST = $params;

    $visit = new Visit($params);

    if($visit->save())
      return $this->render_json($visit->to_array());
    else
      return $this->render_bad_request($visit->get_errors());
  }

  //(patient_id => "", patient: json_format}
  //jsonformat = array("visits" => array(visit, ...))
  // serv_id, site_code, ext_code, ext_code_2, info, age, refer_to_vcct, refer_to_oiart
  // refer_to_std, date_create, visit_date
  function sync_update(){
    $visit = array(
      "pat_age"=> 30, "serv_id"=> "2", "visit_date"=> "2016-12-06",
      "site_code"=> "0202", "ext_code"=> "ex1", "ext_code_2"=> "ex2",
      "refer_to_vcct"=> "2", "refer_to_oiart"=> "1", "refer_to_std"=> "",
      "info"=> "positive", "vcctnumber"=> "1000", "vcctsite" => "V01-02",

      "v_dynamic_field1" => "False",
      "v_dynamic_field2" => "hello",
      "v_dynamic_field3" => "12dfdsa",
      "v_dynamic_field4" => "12.34",
      "v_dynamic_field5" => "2016-09-10",
      "v_dynamic_field6" => "2016-10-10 12:10:10",
      "v_dynamic_field7" => "zzzzz",
    );
    $pat_id = "KH001100000001";
    $visit_params = array();
    $services = array(1,2,3);

    $patient = Patient::find_by(array("pat_id" => $pat_id));
    if(!$patient)
      return false;

    for($i=0; $i< 5; $i++) {
      shuffle($services);
      $visit["serv_id"] = $services[0];
      $visit["pat_age"] = $visit["pat_age"] + $i * 2;
      $visit["pat_id"] = $pat_id;
      $visit_params[] = $visit;
    }
    Visit::bulk_insert($visit_params);  //raise rollback_exception
    $visits = Visit::all(array("pat_id" => $pat_id));
    $result = array("pat_id" => $pat_id, "visits" => array());
    foreach($visits as $visit) {
      $result["visits"][] = (new VisitDetailSerializer($visit))->attributes();
    }
    return $this->render_json($result);
  }

  function search(){
    $params = array(
      "pat_age"=> 30, "serv_id"=> "2", "visit_date"=> "2016-12-06",
      "site_code"=> "0202", "ext_code"=> "ex1", "ext_code_2"=> "ex2",
      "refer_to_vcct"=> "2", "refer_to_oiart"=> "1", "refer_to_std"=> "",
      "info"=> "positive", "vcctnumber"=> "1000", "vcctsite" => "V01-02",

      "v_dynamic_field1" => "False",
      "v_dynamic_field2" => "hello",
      "v_dynamic_field3" => "12dfdsa",
      "v_dynamic_field4" => "12.34",
      "v_dynamic_field5" => "2016-09-10",
      "v_dynamic_field6" => "2016-10-10 12:10:10",
      "v_dynamic_field7" => "zzzzz",

      "pat_register_site" => "0101",
      "pat_age" => 60,
      "pat_gender" => 1,
      "is_referred" => false,

      "p_is_referral" => "False",
      "v_tb" => "1111",
      "p_dynamic_field1" => "10",
      "p_dynamic_field2" => "30",
      "pat_id" => "KH002100003000",
      "fingerprint_r1" => "r1",
      "fingerprint_r2" => "r2",
      "fingerprint_r3" => "r3"
    );

    PatientModule::search($params);
  }

  function  patient_where(){
    $active_record = new Patient();
    $criterias = array(
      "pat_age"=> 30, "serv_id"=> "2", "visit_date"=> "2016-12-06",
      "site_code"=> "0202", "ext_code"=> "ex1", "ext_code_2"=> "ex2",
      "refer_to_vcct"=> "2", "refer_to_oiart"=> "1", "refer_to_std"=> "",
      "info"=> "positive", "vcctnumber"=> "1000", "vcctsite" => "V01-02",

      "v_dynamic_field1" => "False",
      "v_dynamic_field2" => "hello",
      "v_dynamic_field3" => "12dfdsa",
      "v_dynamic_field4" => "12.34",
      "v_dynamic_field5" => "2016-09-10",
      "v_dynamic_field6" => "2016-10-10 12:10:10",
      "v_dynamic_field7" => "zzzzz",

      "pat_register_site" => "0101",
      "pat_age" => 60,
      "pat_gender" => 1,
      "is_referred" => false,

      "p_is_referral" => "False",
      "v_tb" => "1111",
      "p_dynamic_field1" => "10",
      "p_dynamic_field2" => "30",
      "pat_id" => "KH002100003000",
      "fingerprint_r1" => "r1",
      "fingerprint_r2" => "r2",
      "fingerprint_r3" => "r3",
      "site_code" => "sitecode1",
      "date_from" => "2016-10-10",
      "date_to" => "2016-10-16",
      "fake_field" => "Fdafda"
    );
    $exclude_pat_ids = array("'p1'","'p2'","'p3'","'p4'","'p5'" );
    ILog::debug_message("Array",$criterias);
    Patient::where_filter($active_record, $criterias, $exclude_pat_ids);
  }

  function allow_query_field_patients(){
    $criterias = array(
      "pat_age"=> 30, "serv_id"=> "2", "visit_date"=> "2016-12-06",
      "site_code"=> "0202", "ext_code"=> "ex1", "ext_code_2"=> "ex2",
      "refer_to_vcct"=> "2", "refer_to_oiart"=> "1", "refer_to_std"=> "",
      "info"=> "positive", "vcctnumber"=> "1000", "vcctsite" => "V01-02",

      "v_dynamic_field1" => "False",
      "v_dynamic_field2" => "hello",
      "v_dynamic_field3" => "12dfdsa",
      "v_dynamic_field4" => "12.34",
      "v_dynamic_field5" => "2016-09-10",
      "v_dynamic_field6" => "2016-10-10 12:10:10",
      "v_dynamic_field7" => "zzzzz",

      "pat_register_site" => "0101",
      "pat_age" => 60,
      "pat_gender" => 1,
      "is_referred" => false,

      "p_is_referral" => "False",
      "v_tb" => "1111",
      "p_dynamic_field1" => "10",
      "p_dynamic_field2" => "30",
      "pat_id" => "KH002100003000",
      "fingerprint_r1" => "r1",
      "fingerprint_r2" => "r2",
      "fingerprint_r3" => "r3",
      "site_code" => "sitecode1",
      "date_from" => "2016-10-10",
      "date_to" => "2016-10-16",
      "fake_field" => "Fdafda",
      "info" => "positive"
    );

    $fields = Patient::allow_query_fields($criterias);
    ILog::d("Fields ", $fields,1,1);
  }

  function has_field(){
    // $patient = new Patient();
    // ILog::debug_message("is a patient field", $patient->is_field("pat_id"));
    // ILog::debug_message("is not patient field", Patient::has_field("pat_idfdsaf"));

    ILog::debug_message("is visit field", Visit::has_field("pat_id"));
    ILog::debug_message("is not a visit field", Visit::has_field("pat_idfdsaf"));
    ILog::debug_message("must be a visit field", Patient::$visit->is_field("pat_id"));
  }

}
