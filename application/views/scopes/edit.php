<ul class="breadcrumb">
  <li><a href="<?=site_url("main")?>">Home</a> <span class="divider">&gt;</span></li>
  <li><a href="<?=site_url("scopes/index")?>">Scope List</a> <span class="divider">&gt;</span></li>
  <li class="active">Edit</li>
</ul>

<h3>Edit Field Permission</h3>

<?= form_open("scopes/update/{$scope->id}", '') ?>
  <? require dirname(__FILE__). "/_form.php" ?>
</form>
