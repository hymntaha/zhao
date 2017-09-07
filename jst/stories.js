
var stories = {
  
  offset: 0,
  rows: 5, // This will tell us how many rows of stories to load on each "more" event
  noMore: false,

  i: function() {

  },

  selectHandlers: function() {
    bravo.d();
    bravo.handlers();
    $('.stories .thumbnail, .stories .title').off('click');
    $('.stories .thumbnail, .stories .title').on('click', stories.select);

  },
  
  menuHandlers: function() {
    
    /* depending on where this will ultimately go and what it will be connected to it 
     * might make more sense for it to be in display.js
     */
    
    $('.menu-stories li').click(function(){

      stories.sort = $(this).data('sort');
      stories.load(true);
      
      if($('.content_main .column')[0] !== undefined && $('.content_main .column')[0].title !== undefined){
        var offset = $('.content_main .column').offset();
        offset.top -= 20;
        $('html, body').animate({
            scrollTop: offset.top
            
        },1000);
      }
      $('.menu-stories ul').removeClass('menu-show');

      _gaq.push(['_trackEvent', 'Navigation', 'Stories Menu Click', stories.sort]);

    });
    
  },

  select: function() {
    loader.create('loading...');

    var linkBase = '/story/';
    if ($(this).parents().hasClass('microguide-slider')) {
	    linkBase = '/microguide/' + $('.microguide-slider').data('slug') + '/';
    }

    if (search.content() && !display.bio) {
      location.href = linkBase + $(this).data('slug') + '#' + search.hash();
    } else {
      location.href = linkBase + $(this).data('slug');
    }
  },

}
