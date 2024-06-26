(function ($, Drupal) {

    Drupal.behaviors.shortener = {
      attach: function (context, settings) {
        // Ensure context is jQuery object
        var ctxt = $(context);
        ctxt.find('.shortener-link').not('.shortener-strict').each(function () {
          $(this).after('<a class="shortener-toggle">' + ($(this).hasClass('shortener-short') ? Drupal.t('expand') : Drupal.t('shorten')) + '</a>');
        });
        ctxt.find('.shortener-toggle').click(function () {
          var $a = $(this).prev();
          var newURL = $a.attr('title');
          var oldURL = $a.attr('href');
          $a.attr('title', oldURL);
          $a.attr('href', newURL);
          var urlLen = $a.attr('class').split(' ')[0].split('-')[2];
          if (urlLen && newURL.length > urlLen) {
            newURL = newURL.substring(0, urlLen - 1) + '&hellip;';
          }
          $a.html(newURL);
          if ($a.hasClass('shortener-short')) {
            $(this).html(Drupal.t('shorten'));
          } else {
            $(this).html(Drupal.t('expand'));
          }
          $a.toggleClass('shortener-short').toggleClass('shortener-long');
        });
      }
    };
    
    })(jQuery, Drupal);
    