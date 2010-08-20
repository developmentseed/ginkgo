Drupal.behaviors.ginkgo = function (context) {
  // Close handler for palette blocks.
  $('#palette div.block:not(.gingko-palette-block)')
    .addClass('gingko-palette-block')
    .each(function() {
      var target = $(this);

      // Grr... jQuery.cookie() is registered solely under the $.
      // Retrieve initial position of each block and set them.
      if (jQuery().draggable && $.cookie) {
        var initial = {};
        try { initial = JSON.parse($.cookie('DrupalGinkgo')); } catch (error) { }
        initial = initial ? initial : {};
        var id = target.attr('id');
        // Check that coordinates are in the window viewport before setting.
        if (initial[id]) {
          if (initial[id].left >= 0 && initial[id].left < $(window).width()) {
            target.css('left', initial[id].left);
          }
          if (initial[id].top >= 0 && initial[id].top < $(window).height()) {
            target.css('top', initial[id].top);
          }
        }

        // Make block draggable and set position of each block on drag end.
        var options = {
          handle: '.block-title',
          containment: 'document',
          stop: function(event, ui) {
            var state = {};
            try { state = JSON.parse($.cookie('DrupalGinkgo')); } catch (error) { }
            state = state ? state : {};
            state[$(ui.helper).attr('id')] = ui.position;
            $.cookie('DrupalGinkgo', JSON.stringify(state), {expires: 14, path: '/'});
          }
        };
        target.draggable(options);
      }

      // If the end event is triggered from within a pageEditor form,
      // hide the palette block as well.
      if (jQuery().pageEditor && $('form', target).pageEditor) {
        var editor = $('form', target);
        editor.bind('end.pageEditor', function() { target.hide(); });
      }

      // Add a click handler for closing the block.
      $('.block-title span.close', this).click(function() {
        target.hide();
        if (editor) {
          editor.pageEditor('end');
        }
      });
    });

  // Click handler for toggling palette blocks.
  $('a.palette-toggle:not(.ginkgo-palette-toggler)')
    .addClass('ginkgo-palette-toggler')
    .each(function() {
      $(this).click(function() {
        var target = $('#palette div#' + $(this).attr('href').split('#')[1]);
        if (target.size() > 0) {
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
        }
        return false;
      });
    });

  // Growl-style system messages
  $('#messages > div.messages:not(.processed)')
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
      $('.block-title', this).click(function() {
        if (!$(this).is('.toggle-active')) {
          $('div.toggle-blocks .block-title').removeClass('toggle-active');
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
