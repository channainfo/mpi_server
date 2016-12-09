<?php
class Patient extends Imodel {
  var $id = null;
  var $pat_id = null;
  var $pat_gender = null;
  var $pat_dob = null;
  var $pat_age = null;
  var $date_create = null;
  var $pat_version = null;
  var $pat_register_site = null;
  var $new_pat_id = null;

  var $fingerprint_r1 = "";
  var $fingerprint_r2 = "";
  var $fingerprint_r3 = "";
  var $fingerprint_r4 = "";
  var $fingerprint_r5 = "";

  var $fingerprint_l1 = "";
  var $fingerprint_l2 = "";
  var $fingerprint_l3 = "";
  var $fingerprint_l4 = "";
  var $fingerprint_l5 = "";

  var $visits_count = 0;
  var $visit_positives_count = 0;
  var $created_at = null;
  var $updated_at = null;

  //virtual attributes
  var $province = null;
  var $dynamic_fields = array();

  public static function virtual_fields() {
    return array("province", 'dynamic_fields');
  }

  public static function with_fingerprints($params=array()){
    $condtions = array();
    foreach(Patient::fingerprint_fields() as $fingerprint_name) {
      $key = "{$fingerprint_name} IS NOT NULL";
      $condtions[$key] = null;
    }

    if($params["date_from"])
      $condtions["date_from >= "] = Imodel::beginning_of_day($date_from);
    if($params["date_to"])
      $condtions["date_to <= "] = Imodel::end_of_day($date_to);

    return Patient::all_mapper("pat_id");
  }

  public static function is_fingerprint_field($field_name){
    foreach(Patient::fingerprint_fields() as $fingerprint_name){
      if($fingerprint_name == $field_name)
         return true;
    }
    return false;
  }

  function __construct($params = array()) {
    $patient_fields = Patient::field_params($params);
    $this->dynamic_fields = Patient::dynamic_field_params($params);
    parent::__construct($patient_fields);
  }

  function update_attributes($params){
    $patient_fields = Patient::field_params($params);
    $this->dynamic_fields = Patient::dynamic_field_params($params);

    parent::update_attributes($patient_fields);
    FieldValue::create_or_update_fields($this->dynamic_fields, $this);
  }

  function after_create() {
    FieldValue::create_or_update_fields($this->dynamic_fields, $this);
    $this->province->update_attributes(array("pro_pat_seq" => $this->province->pro_pat_seq + 1 ));
  }

  function before_create() {
    $site = Site::find_by(array("site_code" => $this->pat_register_site));
    $this->province = Province::find_by(array("pro_code" => $site->pro_code));

    $country = "KH";
    $version = 1;

    $sequence_fill_8_chars = str_pad($this->province->pro_pat_seq + 1, 8, "0", STR_PAD_LEFT);
    $province_fill_3_chars = str_pad($site->pro_code, 3, "0", STR_PAD_LEFT);
    $pat_id = $country.$province_fill_3_chars.$version.$sequence_fill_8_chars;
    $this->set_attribute('pat_id', $pat_id);
  }


  function to_json(){
    return array(
      "patientid" => $this->pat_id,
      "gender" => $this->pat_gender,
      "birthdate" => $this->pat_dob,
      "sitecode" => $this->pat_register_site,
      "createdate" => $this->date_create,
      "age" => $this->pat_age
    );
  }

  static function field_params($params){
    $result = array();
    foreach($params as $field_name => $field_value) {
      if(property_exists("Patient", $field_name))
        $result[$field_name] = $field_value;
    }
    return $result;
  }

  static function dynamic_field_params($params) {
    $result = array();
    $dynamic_fields = Field::dynamic_fields();
    foreach($params as $field_code => $field_value) {
      if(isset($dynamic_fields[$field_code]) && $dynamic_fields[$field_code]->is_patient_field())
        $result[$field_code] = $field_value;
    }
    return $result;
  }

  function identified_name(){
    foreach(Patient::fingerprint_fields() as $fingerprint_name) {
      if($this->has_fingerprint($fingerprint_name))
        return $fingerprint_name;
    }
    return null;
  }

  function has_fingerprint($fingerprint_name){
    if($this->$fingerprint_name)
      return true;
    return false;
  }
  static function fingerprint_fields(){
    return array(
      "fingerprint_r1",
      "fingerprint_r2",
      "fingerprint_r3",
      "fingerprint_r4",
      "fingerprint_r5",
      "fingerprint_l1",
      "fingerprint_l2",
      "fingerprint_l3",
      "fingerprint_l4",
      "fingerprint_l5"
    );
  }

  function gender(){
    $gender = $this->pat_gender == 2 ? "Female" : "Male";
    return $gender;
  }

  static function timestampable() {
    return true;
  }

  static function primary_key() {
    return "id";
  }

  static function table_name() {
    return "mpi_patient";
  }

  static function class_name(){
    return 'Patient';
  }

  static function migrate_counter_cache() {
    $limit=100;
    $count = Patient::count(array("visits_count" => 0, "visit_positives_count" => 0));

    $repeat = ceil($count/$limit);
    for($i=0; $i< $repeat; $i++) {
      Patient::foreach_migrate($limit);
    }
  }

  static function foreach_migrate($limit=100) {
    $order_by=null;
    $offset=null;
    $patients = Patient::all(array("visits_count" => 0, "visit_positives_count" => 0),
                    $order_by,
                    $offset,
                    $limit);
    foreach($patients as $patient){
      Visit::update_count_cache($patient);
    }
  }

  function search ($gender="") {
    $sql = "SELECT p.pat_id,
                   p.pat_gender,
                   p.pat_dob,
                   p.pat_age,
                   p.pat_register_site,
                   p.date_create,
                   p.fingerprint_r1,
                   p.fingerprint_r2,
                   p.fingerprint_r3,
                   p.fingerprint_r4,
                   p.fingerprint_r5,
                   p.fingerprint_l1,
                   p.fingerprint_l2,
                   p.fingerprint_l3,
                   p.fingerprint_l4,
                   p.fingerprint_l5
            FROM mpi_patient p";

    if ($gender != "")
      $sql.= " WHERE (p.pat_gender = ".$gender." OR p.pat_gender IS NULL)";

    $query = $this->db->query($sql);
    if (!$query)
      ILog::error(mysql_error());
    return $query;
  }

  static function count_filter($criterias, $exclude_pat_ids = array()){
    $active_record = new Patient();

    $active_record = Patient::where_filter($active_record, $criterias, $exclude_pat_ids);
    $count = $active_record->db->count_all_results();
    return $count;
  }

  static function all_filter($criterias, $exclude_pat_ids = array(), $order_by = null, $order_direction = null){
    $active_record = new Patient();
    $active_record->db->select("patient.pat_id, patient.pat_gender, patient.pat_age, patient.pat_dob,
                                patient.date_create, patient.pat_register_site, patient.visits_count,
                                patient.visit_positives_count,patient.new_pat_id");


    $active_record = Patient::where_filter($active_record, $criterias, $exclude_pat_ids);

    if($order_by && $order_direction )
      $active_record->db->order_by($criterias["order_by"], $criterias["order_direction"]);

    $active_record->db->limit(Paginator::per_page());
    $active_record->db->offset(Paginator::offset());
    $query = $active_record->db->get();
    $active_record = null;
    return $query->result();
  }

  // view tecnical and developer spec in docs/spec point 1
  static function allow_query_fields($params){
    $custom_fields = array("master_id", "date_from", "date_to", "site_code", "external_code", "ext_code_2");
    $allow_fields = array();

    $visit = new Visit();
    $patient = new Patient();

    foreach($params as $key => $value) {
      if(in_array($key, $custom_fields) || $patient->is_field($key) || $visit->is_field($key))
        $allow_fields[$key] = $value;
    }
    return $allow_fields;
  }

  static function where_filter($active_record, $criterias, $exclude_pat_ids = array()){
    $conditions = array("patients" => array(), "visits" => array());

    foreach($criterias as $field => $value ){
      if(!$value)
        continue;

      if($field == 'pat_gender'){
        $gender_key = "( patient.pat_gender = " . intval($value) . " OR " . " patient.pat_gender is NULL ) ";
        $conditions["patients"][$gender_key] = null;
      }

      else if ($field == "master_id"){
        $conditions["patients"]['patient.pat_id LIKE'] = "%". $value . "%";
      }

      else if($field == "date_from"){
        $conditions["patients"]["patient.date_create >="] = Imodel::beginning_of_day($value);
        $conditions["visits"]["visit.visit_date >="] = $value;
      }

      else if($field == "date_to"){
        $conditions["patients"]["patient.date_create <="] = Imodel::end_of_day($value);
        $conditions["visits"]["visit.visit_date <="] = $value;
      }
      else if($field == "site_code"){
        $conditions["patients"]['patient.pat_register_site'] = $value;
        $conditions["visits"]['visit.site_code = '] = $value;
      }

      else if($field == "external_code")
        $conditions["visits"]["visit.ext_code = "] = $value;

      else if($field == "external_code2")
        $conditions["visits"]["visit.ext_code_2 = "] = $value;

      else{
        if($active_record->is_field($field) && !Patient::is_fingerprint_field($field))
          $conditions["patients"]["patient.{$field} = "] = $value;
        else{
          // assumtion to be visit fields because we allow only custom field, patient and visit fields
          // view tecnical and developer spec in docs/spec point 1
          $conditions["visits"]["visit.{$field} = "] = $value;
        }
      }
    }

    if(count($exclude_pat_ids) > 0) { //[1,2,4,3,10]
      $wrap_pat_ids = array();
      foreach($exclude_pat_ids as $pat_id) {
        $wrap_pat_ids[] = "'{$pat_id}'";
      }
      $pat_ids = implode(",", $wrap_pat_ids);  // "(1,2,4,3,'10')"
      $key = "(patient.pat_id NOT IN ({$pat_ids}))";
      $conditions["patients"][$key] = null;
    }

    foreach($conditions["patients"] as $key => $value)
      $active_record->db->where($key, $value);

    if(count($conditions["visits"]) >0 ){
      $query_string = array();
      foreach($conditions["visits"] as $key => $value)
        $query_string[] = $key . "'" . mysql_real_escape_string($value) . "'";

      $visit_condition_sql = implode(" AND ", $query_string);

      $visit_query = "SELECT DISTINCT visit.pat_id FROM mpi_visit visit WHERE {$visit_condition_sql}";
      $active_record->db->where("patient.pat_id IN ($visit_query) ");
    }
    $active_record->db->from('mpi_patient patient');
    return $active_record;
  }

  static function paginate_filter($criterias, $exclude_pat_ids=array()) {
    $order_direction = $criterias["order_by"];
    $order_by = $criterias["order_by"];

    $criterias = Patient::allow_query_fields($criterias);
    $total_counts = Patient::count_filter($criterias, $exclude_pat_ids);
    $records = Patient::all_filter($criterias, $exclude_pat_ids, $order_by, $order_direction);
    $paginator = new Paginator($total_counts, $records);
    return $paginator;
  }

  function newPatientFingerprint($data) {
    $create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    $gender = isset($data["gender"])  && $data["gender"] != "" ? $data["gender"] : "NULL";
    $age = isset($data["age"]) && $data["age"] != "" ? $data["age"] : "NULL";
    $dob = isset($data["birthdate"]) && $data["birthdate"] != "" ? "'".$data["birthdate"]."'" : "NULL";
    $site = isset($data["sitecode"]) && $data["sitecode"] != "" ? "'".$data["sitecode"]."'" : "NULL";
    $sitecode = $site == "NULL" ?  "0201" : $data["sitecode"];

    $pat_id = $this->getPatientIdBySiteCode($sitecode);
    $field = "pat_id,
              pat_gender,
              pat_age,
              pat_dob,
              pat_register_site,
              date_create";
    $values = "'".$pat_id."',
               ".$gender.",
               ".$age.",
               ".$dob.",
               ".$site.",
               ".$create_date;

    foreach (Iconstant::$MPI_FINGERPRINT as $fingerprint){
      ${$fingerprint} = isset($data[$fingerprint]) &&  $data[$fingerprint] != "" ? "'".mysql_real_escape_string($data[$fingerprint])."'" : "NULL";
      $field .= ",".$fingerprint ;
      $values .= ",".${$fingerprint};
    }

    $sql = "INSERT INTO mpi_patient(".$field.") VALUES(".$values.")";

    $res = $this->db->query($sql);
    if (!$res)
      ILog::error(mysql_error());

    return $pat_id;
  }

  function newPatient($data) {
    $create_date = isset($var["date_create"]) ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    $gender = isset($data["gender"])  && $data["gender"] != "" ? $data["gender"] : "NULL";
    $age = isset($data["age"]) && $data["age"] != "" ? $data["age"] : "NULL";
    $dob = isset($data["birthdate"]) && $data["birthdate"] != "" ? "'".$data["birthdate"]."'" : "NULL";
    $site = isset($data["sitecode"]) && $data["sitecode"] != "" ? "'".$data["sitecode"]."'" : "NULL";
    $sitecode = $site == "NULL" ?  "0201" : $data["sitecode"];

    $pat_id = $this->getPatientIdBySiteCode($sitecode);
    $this->db->trans_start();

    $sql = "INSERT INTO mpi_patient(pat_id,
                                   pat_gender,
                                   pat_age,
                                   pat_dob,
                                   pat_register_site,
                                   date_create)
                            VALUES('".$pat_id."',
                                   ".$gender.",
                                   ".$age.",
                                   ".$dob.",
                                   ".$site.",
                                   ".$create_date.")";

    $this->db->query($sql);
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method newPatient without fingerprint of patient model. ".$this->db->_error_message());

    return $pat_id;
  }

  static function visits($patient_ids, $order_by, $order_direction) {
    $active_record = new Patient();
    $ids = implode(",", $patient_ids);
    $order_by = $order_by == "" ? "visit_date" : $order_by;
    $order_direction = $order_direction == "" ? "DESC" : $order_direction;

    $sql = "SELECT ps.visit_id,
                   ps.pat_id,
                   ps.serv_id,
                   s.serv_code,
                   ps.site_code,
                   site.site_name,
                   ps.ext_code,
                   ps.ext_code_2,
                   ps.visit_date,
                   ps.pat_age,
                   ps.info
              FROM mpi_visit ps
              LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
              LEFT JOIN mpi_site site ON (site.site_code = ps.site_code)
              WHERE pat_id IN ({$ids}) ORDER BY {$order_by} {$order_direction} ";

    $query = $active_record->db->query($sql);
    return $query->result();
  }

  function getVisitsByPID($pid) {
    $sql = "SELECT ps.visit_id,
                  ps.pat_id,
                  ps.serv_id,
                  s.serv_code,
                  ps.site_code,
                  site.site_name,
                  ps.ext_code,
                  ps.ext_code_2,
                  ps.visit_date,
                  ps.info,
                  ps.date_create,
                  ps.pat_age,
                  ps.refer_to_vcct,
                  ps.refer_to_oiart,
                  ps.refer_to_std
              FROM mpi_visit ps
              LEFT JOIN mpi_service s ON (s.serv_id = ps.serv_id)
              LEFT JOIN mpi_site site ON (site.site_code = ps.site_code)
              WHERE pat_id = '".mysql_real_escape_string($pid)."'
              ORDER BY visit_date DESC";
    return $this->db->query($sql);
  }

  function newVisit($var) {
    $visit_id = null;
    $create_date = isset($var["date_create"]) && $var["date_create"] != "" ? "'".$var["date_create"]."'" : "CURRENT_TIMESTAMP()";
    $age = isset($var["age"]) && $var["age"] != "" ? $var["age"] : "NULL";

    /** Control if visit already exist */
    $sql = "SELECT visit_id FROM mpi_visit WHERE  pat_id = '".mysql_real_escape_string($var["pat_id"])."' AND
                                                  serv_id = ".$var["serv_id"]." AND
                                                  site_code = '".mysql_real_escape_string($var["site_code"])."' AND
                                                  ext_code = '".mysql_real_escape_string($var["ext_code"])."' AND
                                                  visit_date = '".$var["visit_date"]."' AND
                                                  date_create = ".$create_date;
    $query = $this->db->query($sql);
    if ($query->num_rows() > 0){
        $row = $query->row_array();
        $visit_id = $row["visit_id"];
        return $visit_id;
    }

    $sql = "INSERT INTO mpi_visit(pat_id,
                                        serv_id,
                                        site_code,
                                        ext_code,
                                        ext_code_2,
                                        info,
                                        visit_date,
                                        pat_age,
                                        refer_to_vcct,
                                        refer_to_oiart,
                                        refer_to_std,
                                        date_create)
                                  VALUES('".mysql_real_escape_string($var["pat_id"])."',
                                         ".$var["serv_id"].",
                                         '".mysql_real_escape_string($var["site_code"])."',
                                         '".mysql_real_escape_string($var["ext_code"])."',
                                         '".mysql_real_escape_string($var["ext_code_2"])."',
                                         '".mysql_real_escape_string($var["info"])."',
                                         '".$var["visit_date"]."',
                                         ".$age.",
                                         ".$var["refer_to_vcct"].",
                                         ".$var["refer_to_oiart"].",
                                         ".$var["refer_to_std"].",
                                         ".$create_date."
                                       )";

    $this->db->query($sql);
    $visit_id = $this->db->insert_id();

    if ($age != "NULL"){
      $sql = "UPDATE mpi_patient SET pat_age = ".$age." WHERE pat_id = '".mysql_real_escape_string($var["pat_id"])."'";
      $this->db->query($sql);
    }

    return $visit_id;
  }

  private function getPatientSeqProId($province) {
    $seq = -1;
    $this->db->trans_start();

    $sql = "SELECT pro_pat_seq 	FROM mpi_province WHERE pro_id = ".$province;
    $query = $this->db->query($sql);

    if ($query->num_rows() > 1){
      $row = $query->row_array();
      $seq = $row["pro_pat_seq"];
    }

    if ($seq < 0)
      return -1;

    $sql = "UPDATE mpi_province SET pro_pat_seq = pro_pat_seq + 1 WHERE pro_id = ".$province;
    $query = $this->db->query($sql);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method getPatientSeqProId of patient model. ".$this->db->_error_message());

    return $seq;
  }

  private function getPatientSeqProCode($province) {
    $seq = -1;
    $this->db->trans_start();

    $sql = "SELECT pro_pat_seq FROM mpi_province WHERE pro_code = '".mysql_real_escape_string($province)."'";
    $query = $this->db->query($sql);

    if ($query->num_rows() > 0){
        $row = $query->row_array();
        $seq = $row["pro_pat_seq"];
    }

    if ($seq < 0)
      return -1;


    $seq++;
    $sql = "UPDATE mpi_province SET pro_pat_seq = pro_pat_seq + 1 WHERE pro_code = '".mysql_real_escape_string($province)."'";
    $query = $this->db->query($sql);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method getPatientSeqProId of patient model. ".$this->db->_error_message());
    return $seq;
  }

  private function getPatientIdBySiteCode ($sitecode, $country="KH") {
    $version = 1;
    $sitemodel = $this->load_other_model("site");
    $site = $sitemodel->getSiteByCode($sitecode);
    $seq = $this->getPatientSeqProCode($site["pro_code"]);
    $pid = $country.str_pad($site["pro_code"], 3, "0", STR_PAD_LEFT).$version.str_pad($seq, 8, "0", STR_PAD_LEFT);
    return $pid;
  }

  function updateReplacePatientId($old_masterid, $new_patientid) {
    $this->db->trans_start();
    $sql = "UPDATE mpi_visit SET pat_id = '".mysql_real_escape_string($new_patientid)."'
            WHERE pat_id = '".mysql_real_escape_string($old_masterid)."'";
    $query = $this->db->query($sql);

    $sql = "UPDATE mpi_vcct_from_oiart SET oiart_pat_id = '".mysql_real_escape_string($new_patientid)."'
           WHERE oiart_pat_id = '".mysql_real_escape_string($old_masterid)."'";
    $query = $this->db->query($sql);

    $sql = "UPDATE mpi_patient SET new_pat_id = '".mysql_real_escape_string($new_patientid)."'
            WHERE pat_id = '".mysql_real_escape_string($old_masterid)."'";
    $query = $this->db->query($sql);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
      throw new Exception("There is error during calling method updateReplacePatientId of patient model. ".$this->db->_error_message());
  }

  function manageVcctNoFpFromOiart($data) {
    $sql = " SELECT vcct_no_fp_id,
                    vcct_external_code,
                    vcct_site,
                    oiart_pat_id
              FROM mpi_vcct_from_oiart
              WHERE vcct_external_code = '".mysql_real_escape_string($data["ext_code"])."' AND
                    vcct_site = '".mysql_real_escape_string($data["site_code"])."'";
    $query = $this->db->query($sql);

    $record = null;
    if ($query->num_rows() > 0)
            $record = $query->row_array();
    if ($record == null){
      $sql = "INSERT INTO mpi_vcct_from_oiart( vcct_external_code, vcct_site, oiart_pat_id)
                    VALUES('".mysql_real_escape_string($data["ext_code"])."',
                    '".mysql_real_escape_string($data["site_code"])."',
                    '".mysql_real_escape_string($data["pat_id"])."')";
      $this->db->query($sql);

    }
  }
}
