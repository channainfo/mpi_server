<?php
class Tfingerprints extends MpiController {
  var $skip_before_action = "*";
  var $display_value;

  function init(){
    parent::init();
    $scope = Scope::find(2);
    $this->display_value = new DisplayValue($scope);
  }

  function match(){
    $test_fp =   "p/8BHrkAtgABVQCsALsAAU8AwgDBAAFVAKIAtwABTwDOAKoAAVoAkwANAQH+AMQATwABpACzANoAAgMBqQD2AAFPAOEAxgABWgDRAPcAAVUApQBlAAHAAMIAggABdwCOAOkAAf4AgwAeAQE+AJgAIwEC8gCNAN0AAUoAywAXAQEDAcUA/AABAwG6AAYBAU8AuwBhAAFYAdUAfgABggCGADMBAecA1gBJAAGeAKAAmgABSgCfAJEAATkAYQBzAALnALUAbgACpABxABYBAfIAZQDmAAFKAKIAGBkBAw0QEgoCABsUAQATEhsLBhcMFQ4cFAYWDg8OCxQDAA4FAgEPBRMIAAQWDxETDBsCBBMKERIDGAcCEggIBwgNCQIFCAcBEQoJBAwUAgMbBhwFARgUFwcAFhwVGwUNEAcZDAEEAxkLBhIHCBAAGBUUBwMNBwoIFgUFEwwLDR0ZGw8cHRAKBxgMBAwQAwkAGQsBGRMHABkEFQ8TEAEDBBEIBRAPCBgbHB0EGBUGGxcHCQwGDxEKCQIYBBkVFxgLBRIADBMNDQMJARwNFQsODQ0BBwQOCAoCCxcZFBkVEQUSDRICEAAPDQUHDBcQAg8SDhMFHRIJGBQcEBwIDh0EGwkDBQoRBxYTGRoaCxYIEBgCFQgdDhEJFRYNGBoWEQkMGQYdAx0HHQEYBhobEQkZFxoUAxodGAQXDBoBGhoGCRQdGgkX";

    $fps = array(
      "kjkjkj",
      "fdafdafdafdafda",
      "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",
    );
    $sdk = GrFingerService::get_instance();

    if(!$sdk->prepare($test_fp))
      die("failed");

    foreach($fps as $index => $fp){
      if(!$sdk->prepare($fp))
        ILog::d("can not prepare ", $index);
    }
    $sdk->prepare($test_fp);

    foreach($fps as $index => $fp){
      if($sdk->identify($fp))
        ILog::d("Index ", $index);
    }

  }
}