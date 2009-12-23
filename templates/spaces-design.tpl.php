<?php if ($color): ?>
<style type='text/css'>
body.spaces-design #global,
body.spaces-design #branding,
body.spaces-design #navigation,
body.spaces-design #content h2.block-title { background-color:<?php print $color ?>; }

body.spaces-design #branding form input.form-text { border-color:<?php print $upshift ?>; }

body.spaces-design #branding form input.form-text { background-color:<?php print $downshift ?>; }

body.spaces-design #header div.block-widget,
body.spaces-design #header div.toggle-blocks,
body.spaces-design #header h2.block-title { border-color:<?php print $downshift ?>; }

body.spaces-design #global,
body.spaces-design #header h2.block-title,
body.spaces-design #header div.block-widget a,
body.spaces-design #global div.breadcrumb a { color:<?php print $upshiftplus ?>; }
</style>
<?php endif; ?>
