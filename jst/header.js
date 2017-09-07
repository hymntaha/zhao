
var header = {

  i: function() {

    this.handlers();

  },
  handlers: function() {
    $('.menu-places').hover(
        function() {$('.menu-places ul').addClass('menu-show');},
        function() {$('.menu-places ul').removeClass('menu-show');}
     );
    $('.menu-stories').hover(
        function() {$('.menu-stories ul').addClass('menu-show');},
        function() {$('.menu-stories ul').removeClass('menu-show');}
     );
    $('.menu-about').hover(
        function() {$('.menu-about ul').addClass('menu-show');},
        function() {$('.menu-about ul').removeClass('menu-show');}
     );

  },

  link: {
    faq: function() { location.href = '/static/faq'; },
    about: function() { location.href = '/static/about'; },
    fifty: function() { location.href = '/static/fifty'; },
    terms: function() { location.href = '/static/terms'; },
    privacy: function() { location.href = '/static/privacy'; }
  }

}
