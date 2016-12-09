<?php
  $cards = array(
    array("title" => "Patients", "image" => "patients.jpg", "url" => "patients/index", "admin" => false),
    array("title" => "Sites", "image" => "hospital_icon2.png", "url" => "sites/sitelist", "admin" => false),
    array("title" => "Reports", "image" => "reports_icon.png", "url" => "reports/index", "admin" => false),

    array("title" => "Users", "image" => "users.png", "url" => "users/index", "admin" => true),
    array("title" => "Members", "image" => "doctor.png", "url" => "members/index", "admin" => true),
    array("title" => "CSV Export", "image" => "csv.png", "url" => "datas/csvexport", "admin" => true),

    array("title" => "Dynamic field", "image" => "api-dynamic-field.png", "url" => "fields/index", "admin" => true),
    array("title" => "API Scope", "image" => "api-scope.png", "url" => "scopes/index", "admin" => true),
    array("title" => "Application", "image" => "api-application.png", "url" => "applications/index", "admin" => true),
    array("title" => "Access Log", "image" => "monitor-ok-icon.png", "url" => "access_logs/index", "admin" => true)
  );
?>
<h3> Admin Dashboard</h3>

<div class='row' style='margin: 0px;' >
  <ul class="thumbnails">
    <?php foreach($cards as $card) :?>
      <?php $shown = !$card["admin"] || ($card["admin"] && $current_user->is_admin()); ?>
      <?php if($shown) : ?>
        <li class='span2 dashboard-item'>
          <a class="thumbnail" href="<?= site_url($card["url"]); ?>">
            <img src="<?= base_url("img/{$card['image']}") ?>" alt="<?=$card["image"] ?>" style="width: 80px;"/>
            <div class="caption" style="text-align:center;">
              <h4 style="font-size: 120%;"><?= $card["title"] ?></h4>
            </div>
          </a>
        </li>
      <?php endif; ?>
  <?php endforeach ?>
  </ul>
</div>
