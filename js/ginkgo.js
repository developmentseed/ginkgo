Drupal.behaviors.ginkgo = function (context) {
  // Click handler for toggling palette blocks.
  $('a.palette-toggle:not(.atrium-processed)')
    .addClass('atrium-processed')
    .each(function() {
      $(this).click(function() {
        var target = $('div#' + $(this).attr('href').split('#')[1]);
        if (target.css('display') == 'none') {
          target.show();

          // Trigger the first link with a click handler --
          // this will activate context editing or menu item
          // reordering for example.
          var runonce = false;
          $('a', target).each(function() {
            var events = $(this).data('events');
            if ($(this).css('display') != 'none' && events.click && !runonce) {
              $(this).click();
              runonce = true;
            }
          });
        }
        else {
          target.hide();
        }
        return false;
      });
    });

  // Growl-style system messages
  $('#growl > div.messages:not(.processed)')
    .addClass('processed')
    .each(function() {
      // If a message meets these criteria, we don't autoclose
      // - contains a link
      // - is an error or warning
      // - contains a lenghthy amount of text
      if ($('a', this).size() || $(this).is('.error') || $(this).is('.warning') || $(this).text().length > 100) {
        $(this).prepend("<span class='close'>X</span>");
        $('span.close', this).click(function() {
          $(this).parent().slideUp('fast');
        });
      }
      else {
        // This essentially adds a 3 second pause before hiding the message.
        $(this).animate({opacity:1}, 4000, 'linear', function() {
          $(this).slideUp('fast');
        });
      }
    });

  // Togglable blocks
  $('div.toggle-blocks:not(.processed)')
    .addClass('processed')
    .each(function() {
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
