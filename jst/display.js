
var display = {
  
  loading: false,
  moreing: false,
  bio: false,
  sort: 'newest',
  status: 0,
  columns: 1,
  rows: 5,
  columnWidth: 260,
  supportsMediaQuery: null,
  needsRedirect: false, 
  type: [],
  
  i: function() {

    display.columns = display.countColumns();
    display.load(true);
    this.handlers();

  },

  handlers: function() {
    $(window).resize(display.load);
    $(window).scroll(display.scroll);
    display.clickHandlers();
    
  },
    
  clickHandlers: function(reload) {

    $('.tags .tag').click(function() {
      location.href = br.G_URL + '#tags=' + $(this).html();
    
    });

    $('.author a').click(function() {
      
      location.href = br.G_URL + '#authors=' + $(this).data('slug');
      
      if (reload == true) {
        location.reload(true);
      }
    });
    

  },
  
  load: function(overload) {

    if (overload != true) {
      if (display.columns == display.countColumns() || display.countColumns() < 1) {
        return true;
      }

      if (display.loading) {
        return true;
      }

    }
    
    /* If we have any filters we want to load stories and microguides together
     * Only if we haven't already set the type in tpl/footer.php */
    if (display.type.length < 1) {
      if (search.content()) {
          display.type = ['story', 'microguide'];
          search.stories = true;
      }
    }
   
    if (display.bio) {
      display.type = ['story'];
      search.stories = false;
    } 
    
    display.loading = true;
    display.offset = 0;
    
    if (!window.location.hash) $(window).scrollTop(0);

    if (search.content()) {
      $('.content_main').html('<div class="spinner-back"><div class="spinner red"></div></div>');
      $('.message-microguide-wrapper').hide();
      $('.message-microguides-separator').hide();
    }
    
    var forceSmall = 0;
    if ($.inArray('microguide', display.type) != -1 && $.inArray('story', display.type) != -1) {
      forceSmall = 1;  
    }
    
    /* Want to separate call depending on whether its for microguides or stories or both. 
       How about just using a variable to let the server side logic take care of it? 
       Pass the variables that determine what we get back. 
    */
    $.get('/index/display', {
        status: display.status, ajax: 1, sort: display.sort, 
        bio: display.bio,
        columns: display.countColumns(),
        forceSmall: forceSmall,
        type: display.type,
        filters: search.filters }, function(response) {
  
        if (response.count < 1) {
          loader.create('no stories found');
          var img = response.img;
          var url = response.url;
          $('.content_main').html('<div class="no-stories"><h1>We are just getting started... <a href = "/share"> Please Share Your City!</a><br/></h1>Take a trip to someplace new by clicking on the Bravo spinner! <div class = "spinner-container"><a href = "' + url + '"><div class="spinner-back"><div class="spinner red"></div></div></a></div><br/><img src = "' +img+ '"/></div>');
          
        } else {
          //The below makes sure that if you're searching for an author AND 
          //another tag, you'll get the combination of the two and not 
          //just the bio page
          if (response.bio && !stories.bio && 
              search.filters.keywords.length == 0 &&
              search.filters.tags.length == 0 &&
              search.filters.bodytext.length == 0 &&
              search.filters.query.length == 0) {
            var author = search.filters.authors[0];
            search.filters.authors.length = 0;
            location.href = response.bio;
            
            return true;
          }
          
          $('.content_main').html(response.html);
  
        }

        if (response.microguidesTimeout || response.storiesTimeout) {
          var logText = (response.storiesTimeout) ? 'Stories Timeout' : '';
          logText += ((response.storiesTimeout && response.microguidesTimeout) ? ' & ' : '');
          logText += ((response.microguidesTimeout) ? 'Microguides Timeout' : '');
          _gaq.push(['_trackEvent', 'Search', 'Error', 'Max Query Time', logText ]);
        }

        display.columns = display.countColumns();
        
        $('.more-microguides.button').remove();
        if (typeof response.moreMicroguidesAvailable !== undefined && response.moreMicroguidesAvailable && $.inArray('story', display.type) !== -1) {
          $('.microguides-column').last().after("<div class='more-microguides button'>More Microguides</div>");
        }
        
        if ($.inArray('story'), display.type) { 
          stories.selectHandlers();
          stories.offset = 1;
        }

        if ($.inArray('microguide'), display.type) { 
          microguides.selectHandlers();
          microguides.offset = 1;
        }

        display.clickHandlers(search.stories);

        display.loading = false;
  
      }, 'json');

  },
  
  more: function(type, fromButton) {
    var type = (typeof type === undefined) ? display.type : type;
    
    if (display.moreing || display.loading) {
      return false;
    } 

    if ($.inArray('microguide', type) != -1) {
      /* Handle more microguide stuff here */
     
      if (microguides.noMore) {
        loader.create('no more available microguides');
      }
      
      var forceSmall = 0;
      if ($.inArray('microguide', display.type) != -1 && $.inArray('story', display.type) != -1) {
        forceSmall = 1;  
      }
      
      data = {
        type: type,
        status: display.status,
        ajax: 1,
        more: 1,
        smallColumns: display.countColumns(),
        filters: search.filters,
        offset: microguides.offset,
        forceSmall: forceSmall
      }

      if (typeof fromButton !== undefined && fromButton) {
        data.rowLimit = 1;
      }

      display.moreing = true;

      $.get('/index/display', data, function(response) {
  
        if (response.count < 1) {
          loader.create('no more available microguides'); //Shouldn't really come up      
          microguides.noMore = true;
          display.moreing = false;
          
          if (response.microguidesTimeout) {
            var logText = "Microguide Timeout"; 
            _gaq.push(['_trackEvent', 'Search', 'Error', 'Max Query Time', logText ]);
          }

          return false;
            
        } else {
    
          $('.microguides-column').last().after(response.html);
          microguides.offset++;
          
          display.columns = display.countColumns();
        
          $('.more-microguides.button').remove();
          if (typeof response.moreMicroguidesAvailable !== undefined && response.moreMicroguidesAvailable && $.inArray('story', display.type) !== -1) {
            $('.microguides-column').last().after("<div class='more-microguides button'>More Microguides</div>");
          }
          
          display.moreing = false;
    
          microguides.selectHandlers();
            
        }
  
      }, 'json');

    } else {
      /* Add more stories to the bottom */
      if (stories.noMore) {
        loader.create('no more available stories');
        return false;
      }
      
      display.moreing = true;
      
      $.get('/index/stories', {
        status: display.status, ajax: 1, more: 1, bio: display.bio, sort: display.sort, offset: stories.offset, 
        limit: display.countColumns()*stories.rows,
        columns: display.countColumns(), 
        filters: search.filters,
        type: type }, function(response) {
  
          if (response.count < 1) {
            loader.create('no more available stories');
            stories.noMore = true;
            display.moreing = false;
             
            if (response.searchTimeout) {
              var logText = "Story Timeout"; 
              _gaq.push(['_trackEvent', 'Search', 'Error', 'Max Query Time', logText ]);
            }

            return false;
          } else {
	    stories.offset++;
	  }
    
          for (var i in response.html) {
            var html = $('.content_main .column_' + i).html();
            if (html != null) {
              $('.content_main .column_' + i).append(response.html[i]);
            }
    
          }
          
          stories.selectHandlers();
          display.moreing = false;

      }, 'json');
    }
  },
  
  scroll: function() {

    var scroll = $(window).scrollTop() + $(window).height();
    var offset = $(document).height() - scroll;

    if (offset < 500) {
      if ($.inArray('story', display.type) === -1) {
        display.more(['microguide']);
      } else {
        display.more(['story']);
      }
    }

  },
  
  countColumns: function(width) {

    var bounds = [0,320,672,896,1184,1568,1792];
    var i;

    if (!width) {
      width = $(window).width();
    }

    if (display.supportsMediaQuery === null) {
      display.testMediaQuery();
    }

    if (display.supportsMediaQuery) {
      for (i = bounds.length-1; i >= 0; i--) {
        if (width >= bounds[i]) {
          return i+1;
        }
      }
      return bounds.length;
    } else {
      return Math.floor(width/display.columnWidth);
    }
  },

  testMediaQuery: function() {
    $('body').append('<div class="test-media-query"></div>');
    display.supportsMediaQuery = ($('.test-media-query').css('width') === '10px');
  }
}
