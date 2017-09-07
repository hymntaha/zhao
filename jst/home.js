var home = {

  featuredMicroguides: [],
  navTimeout: null,
  scrollerResizeTimeout: null,
  sixColumnWidth: 1568,
  sevenColumnWidth: 1792,

  i: function() {
    home.handlers();
  },

  onBeforeScroll: function() {

    var id = $(this).attr('id').split('-')[2];
    var i;

    $('.nav-item').removeClass('nav-highlighted');
    $('.nav-item-' + id ).addClass('nav-highlighted');

    var fields = ["question", "microguideTitle", "microguideAuthor"];
    for (i = 0; i < fields.length; i++) {
      $('.' + fields[i]).html((home.featuredMicroguides[id][fields[i]]));
    }
    $('.microguideAuthor').attr('data-slug', home.featuredMicroguides[id]['microguideAuthorSlug']);
    
    $('.featured-microguide-title').data('slug', home.featuredMicroguides[id]['microguideSlug']);

  },
  
  onAfterScroll: function() {
    
    if(home.windowWidth === undefined)
      home.windowWidth = window.outerWidth;
    
    var id = $(this).attr('id').split('-')[2];
      
    id = parseInt(id);
    
    for (var i = 1; i < 5; i++) {
      $('.container' + i + ' .microguide-box').data('slug', home.featuredMicroguides[id]['microguideSlug']);
      $('.container' + i + ' .microguide-box').data('title', home.featuredMicroguides[id]['microguideTitle']);
      $('.container' + i + ' .microguide-box').data('story_title', home.featuredMicroguides[id].stories[i].title);
      $('.container' + i + ' .microguide-box').data('story_slug', home.featuredMicroguides[id].stories[i].slug);
      $('.container' + i + ' .microguide-box').data('index', i + 1);
      $('.container' + i + ' .microguide-num').html('<hr/> ' + (i + 1) + '<hr/>');
      $('.container' + i + ' .microguide-title').text(home.featuredMicroguides[id].stories[i].title);
      $('.container' + i + ' .microguide-box img').attr('src', home.featuredMicroguides[id].stories[i].photo);
    }

    $('.container-home-row .microguide-container .microguide-box').off('click');
    $('.container-home-row .microguide-container .microguide-box').on('click', microguide.select);
  
  },

  bindScroller: function() {
    home.scrollerResizeTimeout = null;
    $('#featured-microguides-scroller').cycle({
      fx: 'scrollLeft',
      timeout: 5000,
      before: home.onBeforeScroll,
      after: home.onAfterScroll,
      fastOnEvent: 500
    });
  },

  initScroller: function() {

    /* PROTOTYPE-ONLY HACK */
    if ( $('.nav-items').css('display') == 'none' ) return;

    home.windowWidth = window.outerWidth;
    
    $('#featured-microguides-scroller').cycle("destroy");
    
    if (home.windowWidth > home.sevenColumnWidth) {
      $('#featured-microguides-scroller').css(
        {
          'width': '30%',
          'height': ''
        }
      );
      
    } else if (home.windowWidth > home.sixColumnWidth) {
      $('#featured-microguides-scroller').css(
        {
          'width': '40.5%',
          'height': ''
        }
      );
      
    } else {
      $('#featured-microguides-scroller').css(
        {
          'width': '47%',
          'height': ''
        }
      );
    }
    
    
    $('#featured-microguides-scroller .featured-microguides-image-nav').css(
      {
        'width': '100%',
        'height': ''
      }
    );
    $('#featured-microguides-scroller .featured-microguides-image-nav > img').css(
      {
        'width': '100%',
        'height': ''
      }
    );

    if (home.scrollerResizeTimeout) {
      clearTimeout(home.scrollerResizeTimeout);
    }
    home.scrollerResizeTimeout = setTimeout(home.bindScroller, 500);
  },

  handlers: function() {

    $(window).resize(home.initScroller);

    $('.nav-item').on({

      mouseenter: function() {

	var re = /nav-item-(\d+)/;
	var matches = re.exec($(this).attr('class'));
	if (!matches) return;
	var id = parseInt(matches[1]);
	$('#featured-microguides-scroller').cycle(id);
	$('#featured-microguides-scroller').cycle('pause');

      },

      mouseleave: function() {
	if (home.navTimeout) clearTimeout(home.navTimeout);
	home.navTimeout = setTimeout(function() {
	  $('#featured-microguides-scroller').cycle('resume');
	  home.navTimeout = null;
	}, 15000);
      }

    });
    
    $('.container-home-row .featured-microguide-title').on('click', microguide.select);
    $('.container-home-row .slide-show-link').on('click', microguide.select);
    $('.container-home-row .nav-item').on('click', microguide.select);

  }

};
