<?php
if (empty($args)) return;

$thead = $args['thead'] ?? [];
$data = $args['data'] ?? [];
$notice = $args['notice'] ?? '';
?>
<table class="table table-data">
  <?php if (!empty($thead)): ?>
    <tr>
      <?php foreach ($thead as $row): ?>
        <th><?= $row ?></th>
      <?php endforeach; ?>
    </tr>
  <?php endif; ?>
  <?php foreach ($data as $row):
    $emphasis = $row['emphasis'] ?? '';
    $emphasis_class = $emphasis ? '--emphasis' : '';
  ?>
    <tr class="<?= $emphasis_class ?>">
      <td><?= $row['label'] ?? '' ?></td>
      <td><?= $row['text'] ?? '' ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php if ($notice): ?>
  <p class="remark"><?= $notice ?></p>
<?php endif; ?>