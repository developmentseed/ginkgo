<?php include 'page.header.inc'; ?>

<div id='content'><div class='page-region'>
  <?php if ($content): ?>
    <div class='page-content content-wrapper clear-block'><?php print $content ?></div>
  <?php endif; ?>
  <?php print $content_region ?>
</div></div>
<div id='right'><div class='page-region'>
  <?php if ($right) print $right ?>
</div></div>

<?php include 'page.footer.inc'; ?>
