<style type='text/css'>
#global,
#navigation,
div.pager li.pager-current { background-color:<?php print $background ?>; }

body #space-tools h2.block-title { background-color:<?php print designkit_colorshift($background, '#000000', .3) ?>; }

body #header div.block h2.block-title { background-color:<?php print designkit_colorshift($background, '#000000', .15) ?>; }

body div.page-region div.block h2.block-title {
  background-color:<?php print designkit_colorshift($background, '#eeeeee', .8) ?>;
  border-color:<?php print designkit_colorshift($background, '#dddddd', .8) ?>;
  border-bottom-color:<?php print designkit_colorshift($background, '#cccccc', .8) ?>;
  }
</style>
