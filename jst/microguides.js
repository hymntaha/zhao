
var microguides = {
  
  noMore: false,
  status: 0,
  offset: 0,
  slideshowTimer: {
    blocking: false
  }, // To contain individual microguide timers,
  slideshowTransition: 500, // In ms

  i: function() {

    this.handlers();
    display.type = ['microguide'];
    display.i();

  },

  handlers: function() {
    
    this.selectHandlers();
  },

  slideshowHandlers: function(selector) {

    $(selector + ' .img-container').off('mouseenter');
    $(selector + ' .img-container').off('mouseleave');
    $(selector + ' .img-container').on('mouseenter', microguides.cyclePhotos.start)
    $(selector + ' .img-container').on('mouseleave', microguides.cyclePhotos.stop);
  },

  selectHandlers: function() {
    $('.story-box .img-container').off('click');
    $('.story-box .microguide-title').off('click');
    $('.microguide-info .author a').off('click');
    $('.more-microguides.button').off('click');

    $('.story-box .img-container').on('click', microguide.select);
    $('.story-box .microguide-title').on('click', microguide.select);
    
    $('.microguide-info .author a').on('click', function() {

      location.href = br.G_URL + '#authors=' + $(this).data('slug');
      
    });
    
    $('.more-microguides.button').on('click', function() {
      display.more(['microguide'], true);
    });

    this.slideshowHandlers('.microguides-column');
    
  },
  
  featuredHandlers: function() {
    $('.story-box .img-container').off('click');
    $('.story-box .microguide-title').off('click');

    $('.story-box .img-container').on('click', microguide.select);
    $('.story-box .microguide-title').on('click', microguide.select);
    
    this.slideshowHandlers('.featured-microguides-column');
  },
  
  relatedHandlers: function() {
    $('.related-microguides .img-container').off('click');
    $('.related-microguides .microguide-title').off('click');

    $('.related-microguides .img-container').on('click', microguide.select);
    $('.related-microguides .microguide-title').on('click', microguide.select);
    
    this.slideshowHandlers('.related-microguides-column');
  },

  cyclePhotos: {

    start: function(event) {
      var node = $(event.currentTarget);
      var slug = node.data('slug');

      var responseClosure = function(node) {
        return function(response, textStatus) {
          if (response.success) {
          
            if (node.parent().parent().hasClass('related-microguides-column')) {
              node.data('photos', response.photos.small);
            } else if (node.parent().hasClass('large-box')) {
              node.data('photos', response.photos.large);
            } else {
              node.data('photos', response.photos.small);
            }

            microguides.cyclePhotos.startCycle(node);
          }
        }
      }

      if (!microguides.slideshowTimer.blocking) {

        microguides.slideshowTimer.blocking = true;

        if (typeof node.data('photos') === 'undefined') {

          $.get('/microguide/slideshowPhotos/', {slug: slug}, responseClosure(node), 'json');
         
        } else {

          microguides.cyclePhotos.startCycle(node);

        }
      }
    },
      
    startCycle: function(node) {

      microguides.slideshowTimer.slug = node.data('slug');
      microguides.slideshowTimer.photos = node.data('photos');
      microguides.slideshowTimer.node = node;
 
      window.clearInterval(microguides.slideshowTimer.timer);
    
      microguides.slideshowTimer.timer = window.setInterval(function() {
        microguides.cyclePhotos.nextPhoto();
      }, microguides.slideshowTransition);

      microguides.slideshowTimer.index = 0;

    },

    stop: function() {
      var photo = microguides.slideshowTimer.photos[0];
      microguides.slideshowTimer.node.children('img').attr('src', photo);

      window.clearInterval(microguides.slideshowTimer.timer);
      microguides.slideshowTimer.blocking = false;
    },

    nextPhoto: function() {
      if (++microguides.slideshowTimer.index >= microguides.slideshowTimer.photos.length) microguides.slideshowTimer.index = 0;

      var photo = microguides.slideshowTimer.photos[microguides.slideshowTimer.index];

      microguides.slideshowTimer.node.children('img').attr('src', photo);
      
    }
  }

}
