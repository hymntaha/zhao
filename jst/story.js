var story = {
  
  imageList: [],
  hasSlider: true,
  handleSliderClicks: false,
  slideCount: 0,
  currentSlide: 0,
  sliderBaseLeft: 0,

  i: function() {

    search.stories = false;
    this.handlers();
    story.imageList = $('.photo');
  },

  handlers: function() {

    $('.photo').hover(story.imgover, story.imgout);
    $('.photo').click(story.click);
    if (story.hasSlider) {
      story.sliderHandlers();
    }
    if (typeof $('.edit-bio.button') != undefined) {
      $('.edit-bio.button').on('click', function() {
        document.location.href = '/share/bio';
      });
    }

  },

  openhandlers: function() {
    if(story.imageList.length > 1){
      $('.fade').click(story.close);
      $(document).keydown(function(event){
        if(event.keyCode == 27)
          story.close();
        else if (event.keyCode == 39)
          story.nextImage();
        else if (event.keyCode == 37)
          story.previousImage();
        });
      $('.zoom .zoom-left-nav').click(story.previousImage);
      $('.zoom .zoom-right-nav').click(story.nextImage);
      $('.zoom .fade').click(story.close);
      $('.zoom img').click(story.nextImage);
      $('.zoom').click(story.close);
    }
    else if(story.imageList.length == 1){
      $('.fade, .zoom').click(story.close);
      $(document).keydown(function(event){
        if(event.keyCode == 27)
          story.close();
        });
    }

  },

  imgover: function() {
    $('.zoomicon', this).removeClass('zoomhidden');
  },

  imgout: function() {
    $('.zoomicon', this).addClass('zoomhidden');
  },

  click: function() {
    $('.zoom').removeClass('zoomhidden').css({display: 'block'});
    br.overlay();
    story.showZoom($(this));
    story.openhandlers();
    
  },
  showZoom: function(photo){
    $('.zoom img').fadeOut('fast', function(){
      $('.zoom img').attr('src', photo.data('path'));
    });
    $('.zoom label').fadeOut('fast', function(){
      $('.zoom label').text(photo.data('label'));
    });
    $('.zoom .zoom-left-nav').fadeOut('fast', function(){});
    $('.zoom .zoom-right-nav').fadeOut('fast', function(){});
    story.currentPhoto = photo[0];
    $('.zoom img').load(function(){
      $('.zoom img').fadeIn('fast', function(){});
      $('.zoom label').fadeIn('fast', function(){});
      height = $('.zoom img').height();
      if(story.imageList.length > 1){
        $('.zoom .zoom-left-nav').css({ height: height});
        $('.zoom .zoom-right-nav').css({ height: height});
        $('.zoom .zoom-left-nav').fadeIn('fast', function(){});
        $('.zoom .zoom-right-nav').fadeIn('fast', function(){});
      }
    });
    var offsetTop = $('.zoom img').offset().top;
    var height = ( $(window).height()/2 - photo.data('height')/2 ) + $(window).scrollTop();
    $('.zoom').css({top: height + 'px'});
  },
  close: function() {
    $('.zoom').addClass('zoomhidden').css({display: 'none'});
    $('.zoom img').attr('src', '');
    $('.zoom label').text('');
    br.overlay(true);
    $('.zoom, .fade').unbind('click');
    $(document).unbind("keypress");
    $('.zoom .zoom-left-nav .left-nav-arrow').unbind('click');
    $('.zoom .zoom-right-nav .right-nav-arrow').unbind('click');
    $('.zoom .zoom-left-nav').unbind('click');
    $('.zoom .zoom-right-nav').unbind('click');
  },
  nextImage: function(){
    i = 0;
    $.each(story.imageList, function(index,value){
      i = index;
      if(value === story.currentPhoto){
        //story.close();
        if(i + 1 >= story.imageList.length)
          i = 0;
        else
          i++;
        story.showZoom($(story.imageList[i]));
        return false;
      }
    });
    return false;
  },

  previousImage: function(){
    i = 0;
    $.each(story.imageList, function(index,value){
      i = index;
      if(value === story.currentPhoto){
        //story.close();
        if(i == 0 >= story.imageList.length)
          i = story.imageList.length - 1;
        else
          i--;
        story.showZoom($(story.imageList[i]));
        return false;
      }
    });
    return false;
  },

  sliderPosition: function(id, duration) {

    story.currentSlide = parseInt(id.split('-')[1],10);

    var left = $('#'+id).offset().left;
    var move = story.sliderBaseLeft - left;

    $('.slides').animate({marginLeft: '+='+move}, duration);

  },

  sliderNext: function() {
    if (story.currentSlide < story.slideCount - 1) {
      story.sliderPosition('photo-' + (story.currentSlide + 1), 200);
    } else {
      story.sliderPosition('photo-' + (0), 200);
    }
    _gaq.push(['_trackEvent', 'Navigation', 'Story Slider Click', 'next arrow']);
  },

  sliderPrev: function() {
    if (story.currentSlide > 0) {
      story.sliderPosition('photo-' + (story.currentSlide - 1), 200);
    } else {
      story.sliderPosition('photo-' + (story.slideCount - 1), 200);
    }
    _gaq.push(['_trackEvent', 'Navigation', 'Story Slider Click', 'prev arrow']);
  },

  sliderHandlers: function() {

    $('.slides-outer-container .slides-arrow-right a').on('click', story.sliderNext);
    $('.slides-outer-container .slides-arrow-left a').on('click', story.sliderPrev);

    if (story.handleSliderClicks) {

      $('.slides .slide').off('click');
      $('.slides').off('click');

      $('.slides .slide').on('click', function(e) {

        var left = $(this).offset().left;
        var baseLeft = $('.title').offset().left;
        var move = baseLeft - left;
        var duration = 200;
        var el = $(this);

        if (Math.abs(move) < 50) {
          if ($(this).attr('id') == $('.slides .slide:last').attr('id')) {
            el = $('.slides .slide:first');
            duration = 300;
          } else {
            el = $(this).next('.slide');
          }
        }

        story.sliderPosition(el.attr("id"), duration);

	_gaq.push(['_trackEvent', 'Navigation', 'Story Slider Click', 'image' ]);

        e.stopPropagation();
      });

      $('.slides').on('click', story.sliderNext);

    }

  }

}
