<?php $buttons = !empty($buttons) ? drupal_render($buttons) : ''; ?>

<div class='form clear-block <?php print $form_classes ?>'>

<?php if (!empty($form_message)) print $form_message ?>

<div id='content'>
  <div class='main clear-block'>
  <?php
    $form_settings = variable_get('seed_forms', array());
    print !empty($form_settings['numbered']) ? seed_number_form($form) : drupal_render($form)
  ?>
  </div>
  <?php if ($buttons): ?><div class='buttons clear-block'><?php print $buttons ?></div><?php endif; ?>
</div>

<?php if ($sidebar): ?>
  <div id='right'><div class='sidebar clear-block'><?php print drupal_render($sidebar) ?></div></div>
<?php endif; ?>

</div>
