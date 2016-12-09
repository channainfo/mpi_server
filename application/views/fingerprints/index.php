<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("reports/index")?>">Report Menu</a> <span class="divider">&gt;</span></li>
  <li class="active">Fingerprint</li>
</ul>
<h3>Fingerprint Report</h3>

<?= require_once(dirname(__FILE__)."/_search_form.php") ?>

<table class="table table-striped" cellspacing="0" cellpadding="0" width="100%">
   <tr valign="middle">
      <th>No.</th>
      <th>Number of fingeprints</th>
      <th>Patient List</th>
   </tr>
   <?php $row_nb = 1 ?>
   <?php foreach($reports as $sitecode => $row)?>
   <tr>
      <td align="center"><?=$row_nb?></td>
      <td align="center"><?=count($pat_id_list)?></td>
      <td align="left"><?=implode(":", $pat_id_list) ?></td>

   </tr>
   <?php $row_nb += 1; ?>
   <?php endforeach;?>
</table>
<br/>
<input class="btn" type="button" value="Export to CSV" onclick="window.location='<?=site_url("reports/exportfingerprint")?>'"/>
<?php endif; ?>
<div id="form_patient_detail" title="Paitient Detail"></div>
