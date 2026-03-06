<?php
  $text   = return_array_field($args, 0, '');
  $remark = return_array_field($args, 1, '');
?>
<h2 class="title-wrap">
  <span class="title-wrap__text"><?= $text ?></span>
  <?php if($remark): ?>
    <span class="title-wrap__remark"><?= $remark ?></span>
  <?php endif; ?>
</h2>