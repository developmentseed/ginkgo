Drupal.behaviors.ginkgo = function (context) {
  // Close handler for palette blocks.
  $('#palette div.block h2.block-title span.close:not(.atrium-processed)')
    .addClass('atrium-processed')
    .each(function() {
      $(this).click(function() {
        var target = $(this).parents('div.block');
        target.hide();
        // If the block contains a pageEditor form, trigger its end handler.
        if (jQuery().pageEditor && $('form', target).pageEditor) {
          $('form', target).pageEditor('end');
        }
      });
    });

  // Click handler for toggling palette blocks.
  $('a.palette-toggle:not(.atrium-processed)')
    .addClass('atrium-processed')
    .each(function() {
      $(this).click(function() {
        var target = $('div#' + $(this).attr('href').split('#')[1]);
        if (target.css('display') == 'none') {
          target.show();
          // If the block contains a pageEditor form, trigger its start handler.
          if (jQuery().pageEditor && $('form', target).pageEditor) {
            $('form', target).pageEditor('start');
          }
        }
        else {
          target.hide();
          // If the block contains a pageEditor form, trigger its end handler.
          if (jQuery().pageEditor && $('form', target).pageEditor) {
            $('form', target).pageEditor('end');
          }
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
};
