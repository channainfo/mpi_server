<?php
class Tpatients extends MpiController {
  var $skip_before_action = "*";
  var $display_value;

  function init(){
    parent::init();
    $scope = Scope::find(2);
    $this->display_value = new DisplayValue($scope);
  }

  function index(){
    $params = array(
      "p_fingerprint_r3"=>"",
      "p_fingerprint_l5"=>"",
      "p_fingerprint_l1"=>"",
      "p_fingerprint_l2"=>"p/8BHrkAtgABVQCsALsAAU8AwgDBAAFVAKIAtwABTwDOAKoAAVoAkwANAQH+AMQATwABpACzANoAAgMBqQD2AAFPAOEAxgABWgDRAPcAAVUApQBlAAHAAMIAggABdwCOAOkAAf4AgwAeAQE+AJgAIwEC8gCNAN0AAUoAywAXAQEDAcUA/AABAwG6AAYBAU8AuwBhAAFYAdUAfgABggCGADMBAecA1gBJAAGeAKAAmgABSgCfAJEAATkAYQBzAALnALUAbgACpABxABYBAfIAZQDmAAFKAKIAGBkBAw0QEgoCABsUAQATEhsLBhcMFQ4cFAYWDg8OCxQDAA4FAgEPBRMIAAQWDxETDBsCBBMKERIDGAcCEggIBwgNCQIFCAcBEQoJBAwUAgMbBhwFARgUFwcAFhwVGwUNEAcZDAEEAxkLBhIHCBAAGBUUBwMNBwoIFgUFEwwLDR0ZGw8cHRAKBxgMBAwQAwkAGQsBGRMHABkEFQ8TEAEDBBEIBRAPCBgbHB0EGBUGGxcHCQwGDxEKCQIYBBkVFxgLBRIADBMNDQMJARwNFQsODQ0BBwQOCAoCCxcZFBkVEQUSDRICEAAPDQUHDBcQAg8SDhMFHRIJGBQcEBwIDh0EGwkDBQoRBxYTGRoaCxYIEBgCFQgdDhEJFRYNGBoWEQkMGQYdAx0HHQEYBhobEQkZFxoUAxodGAQXDBoBGhoGCRQdGgkX",
      "p_fingerprint_r1"=>"",
      "p_fingerprint_l4"=>"",
      "p_fingerprint_r4"=>"",
      "p_pat_gender"=>"1",
      "p_fingerprint_r2"=>"p/8BHpMADQEBJQHIAK8AATYBygDtAAErAdUAvwABggCDAMAAAWsA4wCeAAGHAKwAWAABngB4AMIAAWYA1wDWAAF8AGsAwAABWgCbAEsAAV0BwwAvAQI2AbQA/QACKwGZANQAAXcAfAAfAQElAaIA7QABKwGnALIAAXwArgDrAAF3AJsAfQABpACQAOQAASUBtgDhAAF3ALMANAABpADlANkAATABpwAYAAFYAcMAOAECRwHfAC4BATYBmQDpAAJxAOgAXgABRwGSAKkAAYIAbgD6AAFxAK0AGAsPGhoTBwQPEREUBwkWCBMNDBEPExoNBgoDAREaCAMPFBAcAhQECQwPAggMAg8NBBwLGQwUAhEOAA0EGBkVFxoUFgMRExENAQUUDRABChUMGgIWFAgADwAaAwUHHAAMDRAGFQ0HEwQTFAQQAg8dEw4dEgYAEwgBEwcAHQARDBMNHAkcHBIRCB0aFAMCAxoEFBYDEAwNFBACGhIKBxANCRoHFgEMCBMJChcLDBQBEBIBHB0PAg0TEA8EABQREAgFHQ0ADR0HERYdCREDCwAWBRMcDQEbBg4aFAQYDAwWGgkJEA4THQQQBQ4PGAAGFwUbGQwLAg4MARIZAhsVCxEEEg4RCw4LDxgCGA4SFQcSGwoFEhgRAAcUBQkSGA8ZABIbGREcBhkWGRQBGxkIAxIFBh0cEAYcChsXAxsZDhIXFBIQCgEKBRUWEhwVFhsBFQkKHBcFFxAXCRUJFw==",
      "p_pat_register_site"=>"0202",
      "p_fingerprint_l3"=>""
      ,"p_fingerprint_r5"=>"");

    $filter_patients = FieldTransformer::apply_to_all($params);
    $filter_patients = AppHelper::merge_array($filter_patients['visits'], $filter_patients['patients']);

    $paginate_patients = PatientModule::search_registersite_priority($filter_patients);
    $paginate_patients->records = $this->display_value->patients($paginate_patients->records);
    $this->render_json($paginate_patients);
  }



  function show() {
    $pat_id = "KH001100000005";
    $patient = Patient::ensure_find_by(array("pat_id" => $pat_id));
    $patient_json = PatientModule::embed_dynamic_value($patient);
    $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);
    $this->render_json($patient_json);
  }

  function sync() {
    $params = array(
      "p_fingerprint_l5"=>"",
      "p_fingerprint_r1"=>"",
      "p_pat_id"=>"KH002100003312",
      "p_pat_age"=>"",
      "p_fingerprint_r2"=>"p/8BHYYA8gABdwCwAMYAATsBxwCwAAGHAKUA/QABfADDAO4AAXwAnAAFAQF8AL8A1gABggCEAC0BASsBpAAVAQEwAdEA8AABNgHCAHMAAkcBjgAKAQIrAbcABAEBMAGEAEsAAakAiwDIAAGHAFgAMQEBdwBcAA8BAXEAhwBwAAGpAFQA4gABYAB8AJkAAa8AfAAxAAFjAXUAaAACAABVAKYAAS0AQAALAQJrAFAAhgABHQDpADUBAjsBbQDeAAFrAGAA2QABZgB7AMgAAYIAowAFAwkEGhsSGwsFDhwIBQwDERUGAQgDBAYICwsADAQSGggMCwMaHAUMDRQQFwUACQYbHAECABoWGAwJFQ0DAAMEDxAHCxoOEQ0OAQYCBwgLDBYTExEADgAcBAEPBw8XBQQAGwMJCAAbDhASFxIHBRIcHBMDBgwGGBUTGA4TExUQCwgEBxAQABwWGxYAEhAaDAAJARwBBg4QGwsaFRQDAQUJBwMICQoRBwAXGwUGGBEDDhIWCwQAARIOGhYCCgQCBQ4XGhEUDhYQBQkCBwwOAg8LAxwBExsTGQwWFQgQGQkXAAoNFhEHFxkIEBwKFQITFwsPABMNGA0PEhwYHAITCg8IGQQPBQMCGxgBCgwCDhEZAw4YABMGExkFEhgKFBwVGBQGChkLGQcWDRcWExQZBhAWAxMCDRYUCQoXGBkCGQ8ZEAIUGRMZCg==",
      "p_updated_at"=>"2017-01-09 16:00:43",
      "p_date_create"=>"2017-01-09 16:00:43",
      "p_fingerprint_r3"=>"",
      "p_fingerprint_l1"=>"",
      "v_site_code"=>"",
      "p_fingerprint_r4"=>"",
      "p_fingerprint_l2"=>"p/8BHr8ACQEBWgA9ACIBAT4AQACuAALhAM8A2QABawCsAO0AAWAA1wDzAAJmAI8AHgECAwGOAPIAAU8AZwAfAQFEAGsALQEC+ACgAI8AAaQA2wDbAAEfATUAtQABMwCgAMgAAXcA1ADJAAF3AEMAaAABKAB9AP0AAkQAQABeAALcAKAABwEBTwCUAP4AAk8AKAAnAQHyAIMAqgABwACyAMAAAXwAlwCkAAFjAbIAhgABpACRALYAAaQAgQDZAAJEAH0A5AABPgCEADgBAUoAiQDVAAJaAKsAGh0PEQsDGxoTBwwCCQgSEwMOGRULDhsdGRcKGBAHDRYVFxQBBxsNGRcKExAFCxAbHQ0FAxwJHAYHGhIHBx0SBAYSEwQHBAASBhMdGQAFAAQWGQ4WGg0TGxAaEhANFxoZAxYGEBwICQYWFwQNFxgVCgQDCAYIEBMaGQoEHRMdBQ4QHQ0VAQgFBB0VBgcAEwQWGw0dFgkBGhUEGxIbCxYDDQQLGxkQBAADHRcJEA4NBgAWFRYKAAcACwQOEh0IExwSGhcGBA0KGRgbFRYYHBAVGBIFHBMIBwQZCRMLDQgSBRYIGxQICRISAwkUAA4CFQ0YEwUCDw4ZHAcBEBILHQocARwADhgaAgwPDgoMFQIRGwIZAgEGHQIaDBsMAxgMEQEbHQwJABkMFQ8BBxwUFBAKDwIKFREQDBcPFAYKERQbAQwZDxcRDAoYDxQMGREYERoPCQwbDxYR",
      "p_fingerprint_l4"=>"",
      "p_fingerprint_r5"=>"",
      "p_pat_dob"=>"",
      "p_fingerprint_l3"=>"",
      "p_pat_gender"=>"1",
      "visits" => array(
        array(
          "v_pat_age"=> "80", "v_serv_id"=> "2", "v_visit_date"=> "2016-12-06",
          "v_site_code"=> "0202", "v_ext_code"=> "ex1", "v_ext_code_2"=> "ex2",
          "v_refer_to_vcct"=> "2", "v_refer_to_oiart"=> "1", "v_refer_to_std"=> "",
          "v_info"=> "positive", "v_vcctnumber"=> "1000", "v_vcctsite" => "V01-02",

          "v_dynamic_field1" => "False",
          "v_dynamic_field2" => "hello",
          "v_dynamic_field3" => "12dfdsa"
        ),
        array(
          "v_pat_age"=> "81", "v_serv_id"=> "2", "v_visit_date"=> "2016-12-06",
          "v_site_code"=> "0202", "v_ext_code"=> "ex1", "v_ext_code_2"=> "ex2",
          "v_refer_to_vcct"=> "2", "v_refer_to_oiart"=> "1", "v_refer_to_std"=> "",
          "v_info"=> "positive", "v_vcctnumber"=> "1000", "v_vcctsite" => "V01-02",

          "v_dynamic_field1" => "False",
          "v_dynamic_field2" => "hello",
          "v_dynamic_field3" => "12dfdsa"
        ),
        array(
          "v_pat_age"=> "83", "v_serv_id"=> "2", "v_visit_date"=> "2016-12-06",
          "v_site_code"=> "0202", "v_ext_code"=> "ex1", "v_ext_code_2"=> "ex2",
          "v_refer_to_vcct"=> "2", "v_refer_to_oiart"=> "1", "v_refer_to_std"=> "",
          "v_info"=> "positive", "v_vcctnumber"=> "1000", "v_vcctsite" => "V01-02",

          "v_dynamic_field1" => "False",
          "v_dynamic_field2" => "hello",
          "v_dynamic_field3" => "12dfdsa"
        )
      ));

    $patient = PatientModule::synchronize($params);

    if(!$patient){
      $errors = array("error" => "Validation errors",
                      "error_description" => PatientModule::$errors);
      $this->render_bad_request($errors);
    }
    else{
      $patient_json = PatientModule::embed_dynamic_value($patient);
      $patient_json["patient"] = $this->display_value->patient($patient_json["patient"]);

      $this->render_json($patient_json);
    }

  }
}
