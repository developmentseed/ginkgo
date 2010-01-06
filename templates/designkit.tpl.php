<style type='text/css'>
#global,
#branding,
#navigation,
div.pager li.pager-current { background-color:<?php print $background ?>; }

div.page-region div.block h2.block-title {
  background-color:<?php print designkit_colorshift($background, '#eeeeee', .8) ?>;
  border-color:<?php print designkit_colorshift($background, '#dddddd', .8) ?>;
  border-bottom-color:<?php print designkit_colorshift($background, '#cccccc', .8) ?>;
  }

#branding form input.form-text { border-color:<?php print designkit_colorshift($background, '#ffffff', .25) ?>; }

#branding form input.form-text { background-color:<?php print designkit_colorshift($background, '#000000', .2) ?>; }

#header div.block-widget,
#header div.toggle-blocks,
#header h2.block-title { border-color:<?php print designkit_colorshift($background, '#000000', .2) ?>; }

#global,
#header h2.block-title,
#header div.block-widget a,
#global div.breadcrumb a { color:<?php print designkit_colorshift($background, '#ffffff', .5) ?>; }
</style>
