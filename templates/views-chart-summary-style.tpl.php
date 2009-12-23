<?php
// $Id: views-cloud-summary-style.tpl.php,v 1.2 2008/09/15 23:02:37 eaton Exp $
/**
 * @file views-cloud-summary-style.tpl.php
 * View template to display a list summaries as a weighted cloud
 *
 * - $rows contains a nested array of rows. Each row contains an array of
 *   columns.
 *
 * @ingroup views_templates
 */
?>
<div class="views-chart <?php print $chart_type ?>">
  <?php print chart_render($chart); ?>
</div>
