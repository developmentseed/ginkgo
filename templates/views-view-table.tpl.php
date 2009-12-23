<?php
// Add a caption class to captioned tables
if (!empty($title)) {
  $class .= ' captioned';
}

// Don't render header if empty
$test = implode('', $header);
$test = trim($test);
if (empty($test)) {
  $header = NULL;
}
?>
<table class="<?php print $class; ?>">
  <?php if (!empty($title)) : ?>
    <caption><?php print $title; ?></caption>
  <?php endif; ?>
  <?php if ($header): ?>
    <thead>
      <tr>
        <?php foreach ($header as $field => $label): ?>
          <th class="views-field <?php print $fields[$field]; ?>">
            <?php print $label; ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
  <?php endif; ?>
  <tbody>
    <?php $count = 0; ?>
    <?php foreach ($rows as $class => $row): ?>
      <tr class="<?php if (!is_numeric($class)) print $class ?> <?php print is_numeric($count) && ($count % 2 == 0) ? 'even' : 'odd';?>">
        <?php foreach ($row as $field => $content): ?>
          <td class="views-field <?php print $fields[$field]; ?>">
            <?php print $content; ?>
          </td>
        <?php endforeach; ?>
      </tr>
      <?php $count++; ?>
    <?php endforeach; ?>
  </tbody>
</table>
