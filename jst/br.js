// general overall bravo functionality

var br = {

  // site URL
  G_URL: false,

  // parameters/state
  _p: false,

  // session hash
  hash: false,

  // is mobile
  _isMobileLayout: null,

  // spinner html
  spinner: '<div class="spinner-back"><div class="spinner red"></div></div>',

  // center an absolute div
  center: function(e, params) {

    var middle = ($(window).width() / 2) - (e.outerWidth() / 2);
    var top = ($(window).scrollTop() + 100*1);

    if (params && params.noTop) {
      $(e).css({left: middle + 'px'});
    } else {
      $(e).css({top: top + 'px', left: middle + 'px'});
    }

    return true;

  },

  // equivalent to php's in_array
  inArray: function(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
      if (haystack[i] == needle) {
        return true;
      }
    }

    return false;
  },

  stripTags: function(html) {
    return html.replace(/(<([^>]+)>)/ig,"");
  },

  countProperties: function(obj) {
    var count = 0;

    for(var prop in obj) {
      if(obj.hasOwnProperty(prop))
      ++count;
    }

    return count;
  },

  isNumeric: function(input) {
        return (input - 0) == input && input.length > 0;
  },

  // grab variables passed into GET
  getParam: function(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
  },

  // toggle the transparent overlay
  overlay: function(off) {

    if (off) {
      $('.fade').transition({opacity: 0}, 100,  function() { $('.fade').hide(); });
    } else {
      $('.fade').show().transition({opacity: 0.7}, 100);
      $('.fade').css({height: $(document).height() + 'px'});
    }

  },

  // object hovering
  hover: function(obj, cls, params) {

    var p = {tooltip: false};

    if (params) {
      for (var param in params) {
        p[param] = params[param];
      }
    }

    $(obj).hover(function() {

      if (cls) {
        $(this).addClass(cls + '_hover');
      }

      if (p.tooltip) {
        if (p.tooltip.data) {
          this.tooltip = tooltip.create($(this), $(this).data(p.tooltip.data), {fade: 10});
        } else if (p.tooltip.attr) {
          this.tooltip = tooltip.create($(this), $(this).attr(p.tooltip.attr), {fade: 10});
        } else {
          this.tooltip = tooltip.create($(this), p.tooltip, {fade: 10});
        }
      }

    }, function() {

      if (cls) {
        $(this).removeClass(cls + '_hover');
      }

      if (p.tooltip) {
        tooltip.destroy(this.tooltip);
      }
    });

  },

  // input focus/blur tip handling
  inputfocus: function(obj) {

   // initial scan for inputs starting off w/ data
   $(obj).each(function(index, value) {
      if ($(value).val() != '' && $(value).val() != $(value).data('tip')) {
        $(value).css({color: '#333'});
        if ($(value).hasClass('input-password')) {
          this.type = 'password';
        }
      }
    });

    obj.unbind('focus');
    obj.unbind('blur');

    obj.focus(function() {
      if ($(this).val() == $(this).data('tip')) {
        $(this).val('');
        $(this).css({color: '#333'});
      }
      if ($(this).hasClass('input-password')) {
        this.type = 'password';
      }
    });

    obj.blur(function() {
      if ($(this).val() == '') {
        $(this).val($(this).data('tip'));
        $(this).css({color: '#777'});
        if ($(this).hasClass('input-password')) {
          this.type = 'text';
        }
      }
    });

  },

  inputEnter: function(obj, callback) {

    obj.unbind('keydown');

    obj.keydown(function(event) {

      if (event.keyCode == 13) {
        callback(this);
        return false;
      }

    });

  },

  scrollTo: function(position) { 
    $('html, body').animate({ scrollTop: position}, 1000);
  },

  addScrollToTop: function(el) {

    $('body').append('<div class="scroll-to-top" style="display: none;"><input type="button" value="Scroll To Top" class="button"></div>');

    $('.scroll-to-top').on('click', function() {
      br.scrollTo($('.topOfPage').offset().top)
    });

    var tripLine = 300;
    switch (typeof el) {
    case 'number':
      tripLine = el;
      break;
    case 'string':
      var j = $(el);
      if (j instanceof jQuery && j.length > 0) {
	tripLine = j.offset().top;
      }
      break;
    case 'object':
      if (el instanceof jQuery && el.length > 0) {
	tripLine = el.offset().top;
      }
    }

    var scrollToTopWatcher = function() {

      if ($(window).scrollTop() < tripLine ) {
	if($('.scroll-to-top').is(":visible")){
          $('.scroll-to-top').fadeOut('fast', function(){});
	}
      } else {
	if(!$('.scroll-to-top').is(":visible")){
          $('.scroll-to-top').fadeIn('fast', function(){});
	}
      }

    }

    $(window).scroll(scrollToTopWatcher);

  },

  setMobileLayout: function() {
    $('body').append('<div class="test-mobile-layout"></div>');
    br._isMobileLayout = ($('.test-mobile-layout').css('width') === '10px');
  },

  isMobileLayout: function() {
    if (br._isMobileLayout === null) {
      br.setMobileLayout();
    }
    return br._isMobileLayout;
  },

  handleSeeAllMicroguides: function(link, label) {
    _gaq.push(['_trackEvent', 'Navigation', 'See All Microguides Click', label]);
    setTimeout('document.location = "' + link.href + '"', 100);
  },

  handleHouseAdGetStarted: function(link, label) {
    _gaq.push(['_trackEvent', 'Navigation', 'House Ad Get Started Click', label]);
    setTimeout('document.location = "' + link.href + '"', 100);
  },

  extractUrlBase: function(url) {
    var i,
        j,
        s,
        chars = [ '?', '#', '.' ];
    s = url;
    i = s.lastIndexOf('/');
    if (i > -1) {
      s = s.substring(i + 1);
    }
    for (j = 0; j < chars.length; j++) {
      i = s.indexOf(chars[j]);
      if (i > 0) {
        s = s.substring(0, i);
      }
    }
    return s;
  },

  countWords: function(source, target, limit) {

    // Initialize html    
    var wordCount;
    if ($(source).val() == $(source).data('tip')) {
      wordCount = 0;
    } else {
      wordCount = $(source).val().split(/\s+/).length;
    }
    $(target).html((limit - wordCount).toString());

    // Register event handler
    $(source).on('keyup', function() {
      var wordCount = $(this).val().split(/\s+/).length;
      $(target).html((limit - wordCount).toString());
      if (typeof(limit) != 'undefined') {
        if (wordCount > limit) {
          $(target).addClass('word-count-exceeded');
        } else {
          $(target).removeClass('word-count-exceeded');
        }
      }
    });
  }

}
