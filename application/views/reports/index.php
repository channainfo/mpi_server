<?php
  $menus = array(
    array("name" => "Fingerprint", "url" => "fingerprints/index", "description" => "Fingerprint report"),
    array("name" => "OI/ART", "url" => "", "description" => "OI/ART report"),
    array("name" => "VCCT", "url" => "", "description" => "VCCT report"),
    array("name" => "STD", "url" => "", "description" => "STD report"),
    array("name" => "Duplication", "url" => "", "description" => "Duplication report"),
    array("name" => "Routine Monitoring", "url" => "", "description" => "Routine Monitoring report"));
?>

<ul class="breadcrumb">
	<li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
	<li class="active">Report Menu</li>
</ul>
<h3>Report Menu</h3>

<table  class="table table-striped" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th>Name</th>
      <th>Description</th>
   </tr>

   <?php foreach($menus as $item): ?>
   <tr>
      <td><a href="<?= site_url($item['url'])?>"> <?=$item['name']?> </a></td>
      <td><?=$item['description']?></td>
   </tr>
   <?php endforeach;?>
</table>
