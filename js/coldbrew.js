// $Id$

Drupal.behaviors.coldbrew = function (context) {
  // Growl-style system messages
  $('#growl > div.messages:not(.processed)').each(function() {
    $(this).addClass('processed');
    // If a message meets these criteria, we don't autoclose
    // - contains a link
    // - is an error or warning
    // - contains a lenghthy amount of text
    if ($('a', this).size() || $(this).is('.error') || $(this).is('.warning') || $(this).text().length > 100) {
      $(this).prepend("<span class='close'>X</span>");
      $('span.close', this).click(function() {
        $(this).parent().hide('fast');
      });
    }
    else {
      // This essentially adds a 3 second pause before hiding the message.
      $(this).animate({opacity:1}, 4000, 'linear', function() {
        $(this).hide('fast');
      });
    }
  });

  /**
   * Palette links/block management.
   */
  $('div.toggle-blocks:not(.processed)').each(function() {
    $(this).addClass('processed');
    $('h2.block-title', this).click(function() {
      var realm = $(this).parents('div.toggle-blocks');

      if (!$(this).is('.toggle-active')) {
        $('div.toggle-blocks h2.block-title').removeClass('toggle-active');
        $('div.toggle-blocks div.block-toggle div.block-content').hide();
        $(this).addClass('toggle-active').siblings('div.block-content').show();
      }
      else {
        $(this).removeClass('toggle-active').siblings('div.block-content').hide();
      }
      return false;
    });
  });
}
