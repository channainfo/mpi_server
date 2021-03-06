<?php
// reduce n+1 loop + join query
class DynamicValue {
  var $patients = array();
  function __construct($field_codes=array()) {
    $this->field_codes = $field_codes; //filter access scope not used yet
    $this->field_mappers = Field::mapper();
  }

  function result($records, $type='patient') {

    // ILog::d("records", $records);
    if(count($records) == 0)
      return $records;

    $this->records = $records;
    $this->type = $type;

    $mapper = $this->map_field_values_by_record();
    $result = array();

    foreach($this->records as $record) {
      $id = $this->id_value($record);
      $dynamic_values = isset($mapper[$id]) ? $mapper[$id] : array();
      $result[] = $this->merge_values($record, $dynamic_values);
    }
    return $result;
  }

  private function id_value($record) {
    $id = $this->is_patient_type() ? "id" : "visit_id";
    return  is_object($record) ? $record->$id : $record[$id];
  }

  private function is_patient_type(){
    return strtolower($this->type) == "patient";
  }

  private function is_virutal_field($field_name) {
    $virtuals = $this->is_patient_type() ? Patient::virtual_fields() : Visit::virtual_fields();
    foreach($virtuals as $virtual_field_name){
      if($virtual_field_name == $field_name)
        return true;
    }
    return false;
  }

  private function merge_values($record, $dynamic_values) {
    $merged_result = array();

    foreach($record as $field_name => $value) {
      if(!$this->is_virutal_field($field_name))
        $merged_result[$field_name] = $value;
    }

    foreach($dynamic_values as $dynamic_value) {
      $field_code = $this->field_mappers[$dynamic_value->field_id];
      $merged_result[$field_code] = $dynamic_value->get_value();
    }
    return $merged_result;
  }

  //array("pat1"=> array(fieldvalueobje1, fieldvalueobje2), "pat2" => array(fieldval3, fieldvalobj4))
  private function map_field_values_by_record() {
    $ids = array();

    foreach($this->records as $record){
      $ids[] = $this->id_value($record);
    }


    $ids = implode(",", $ids);

    $field_values = FieldValue::all(array("field_owner_id IN ({$ids}) " => null));
    $result = array();

    foreach($field_values as $field_value_object) {
      $field_owner_id = $field_value_object->field_owner_id;

      if(isset($result[$field_owner_id]))
        $result[$field_owner_id][] = $field_value_object;
      else
        $result[$field_owner_id] = array($field_value_object);
    }

    return $result;
  }



}
