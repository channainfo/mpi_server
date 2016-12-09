<div class="well">
  <h4>Access Logs filter</h4>
  <?= form_open('fingerprints/index', array("method"=> "GET")) ?>
  <?= form_input(array("name" => "type",
                       "id" => "type",
                       "type" => "hidden",
                       "value" => $params["type"])) ?>
                      <div class="row-fluid input-row">

    <span class="label-inline"> </span>
    <?= form_input(array("name" => "from",
                         "id" => "from",
                         "value" => $params["from"],
                         "class" => "date-picker",
                         "style" => "margin-top: 10px; margin-left: 10px;",
                         "placeholder" => "From Date(YYYY-MM-DD)")) ?>

    <span class="label-inline"> </span>
    <?= form_input(array("name" => "to",
                         "id" => "to",
                         "value" => $params["to"],
                         "class" => "date-picker",
                         "style" => "margin-top: 10px; margin-left: 10px;",
                         "placeholder" => "To Date(YYYY-MM-DD)")) ?>
                         <span class="label-inline"> </span>
                       </div>
<div class="row-fluid input-row">
    <?= form_input(array("name" => "to",
                        "id" => "to",
                        "value" => $params["to"],
                        "class" => "date-picker",
                        "style" => "margin-top: 10px; margin-left: 10px;",
                        "placeholder" => "To Date(YYYY-MM-DD)")) ?>
                      </div>

    <button class='btn btn-primary' style="margin-left: 20px;"> Show </button>
  </form>
</div>
