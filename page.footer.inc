  </div></div>

  <?php if ($footer_message || $footer_links): ?>
  <div id="footer"><div class='limiter clear-block'>
    <?php if ($footer_message): ?><div class='footer-message'><?php print $footer_message ?></div><?php endif; ?>
    <?php if ($footer_links): ?><?php print theme('links', $footer_links) ?><?php endif; ?>
  </div></div>
  <?php endif; ?>

  <?php if ($palette): ?><div id='palette'><?php print $palette; ?></div><?php endif; ?>

  <?php print $closure ?>

  </body>
</html>
