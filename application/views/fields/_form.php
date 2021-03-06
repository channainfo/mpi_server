<? if(count($field->get_errors()) > 0): ?>
  <? require dirname(dirname(__FILE__)). "/shared/validation_error.php" ?>
<? endif ?>

<div class="row-fluid input-row <?= array_key_exists('name', $field->get_errors()) ? " error" : '' ?>" >
  <div class="span2">Name*</div>
  <div class="span10">
    <?= form_input(array( "name" => "name",
                          "id" => "name",
                          "value" => $field->name
                        ) ) ?>
  </div>
</div>

<div class="row-fluid input-row <?= array_key_exists('code', $field->get_errors()) ? "error" : '' ?>" >
  <div class="span2">Code*</div>
  <div class="span10">
    <?= form_input(array( "name" => "code", "id" => "code", "value" => $field->code )) ?>
  </div>
</div>



<div class="row-fluid input-row">
  <div class="span2">Data Type*</div>
  <div class="span10">
    <?= form_dropdown('type', Field::types(), $field->type, 'id="type" class="tokenizer tokenizer-short"' ) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Table*</div>
  <div class="span10">
    <?= form_dropdown('table_type', Field::table_types(), $field->table_type, 'id="type" class="tokenizer tokenizer-short"' ) ?>
  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2">Encrypted*</div>
  <div class="span10">
    <?= form_checkbox(array("name"=> "is_encrypted",
                            "id"=>"is_encrypted",
                            "value" => 0,
                            "checked" => true,
                            "class" => "hidden") )?>

    <?= form_checkbox(array("name"=> "is_encrypted",
                            "id"=>"is_encrypted",
                            "value" => 1,
                            "checked" => $field->is_encrypted == "1" ? true : false) )?>
    <p class='field-hint'>
      If checked, Value only can be searched by exact match.
    </p>

  </div>
</div>

<div class="row-fluid input-row">
  <div class="span2"></div>
  <div class="span10">
    <button class="btn"> Save </button>
    <a href='<?=site_url("fields/index")?>' class="btn btn-danger"> Cancel </a>
  </div>
</div>
