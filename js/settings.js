Drupal.behaviors.ginkgo_settings = function (context) {
  // Add Farbtastic
  $('input.colorpicker:not(.processed)').each(function() {
    $(this).addClass('processed');
    var target = $(this);
    var id = $(this).attr('id') + '-colorpicker';
    var farb = $.farbtastic('div#'+id, target);
    if (target) {
      target
        .focus(function() {
          $('div#'+id).show('fast');
        })
        .blur(function() {
          $('div#'+id).hide('fast');
        });
    }
  });
};
