
var modal = {

  modalOpen: false,
  History: null,
  windowHeight: null,
  modalHeight: null,
  scrolling: null,
  lastScrollTop: null,
  lastScrollTime: null,
  clickOutsideHandler: null,

  i: function() {

    modal.History = window.History;

    /* Little piece of code from stackoverflow:
     * http://stackoverflow.com/questions/2124684/jquery-how-click-anywhere-outside-of-the-div-the-div-fades-out#3234741
     * Allows you to use $().clickOutside to add an event handler for someone clicking outside of a selected div.
     */
    $.fn.extend({
      // Calls the handler function if the user has clicked outside the object (and not on any of the exceptions)
      clickOutside: function(handler, exceptions) {
        var $this = this;
        var unbind;

        if (typeof(handler) == 'string' && handler == 'off') {
          $("body").off("click", modal.clickOutsideHandler);
          modal.clickOutsideHandler = null;
          return;
        }

        if (!modal.clickOutsideHandler) {

          modal.clickOutsideHandler = function(event) {
            if (exceptions && $.inArray(event.target, exceptions) > -1) {
              return;
            } else if ($.contains($this[0], event.target)) {
              return;
            } else {
              unbind = handler(event, $this);
              if (unbind) {
                $("body").off("click", modal.clickOutsideHandler);
                modal.clickOutsideHandler = null;
              }
            }
          }

          $("body").on("click", modal.clickOutsideHandler);

        }

        return this;
      }
    });

    this.handlers();

  },

  handlers: function() {

    modal.History.Adapter.bind(window,'statechange',function(){ 
      var State = modal.History.getState(); 

      if (State.data.microguideSlug !== undefined && State.data.storySlug === undefined && !modal.modalOpen) {
        /* This means that someone has clicked on the icon and we need to open the modal for the first time*/

        modal.microguideModalSlug = State.data.microguideSlug;
        modal.microguideModalTitle = State.data.microguideTitle;

        modal.openMicroguideModal();

      } else if(State.data.microguideSlug !== undefined && State.data.storySlug !== undefined && State.data.index !== undefined && !modal.modalOpen) {
        /* Opening up a specific story from a microguide. */
        modal.microguideModalSlug = State.data.microguideSlug;
        modal.microguideModalTitle = State.data.microguideTitle;

        modal.openMicroguideModal(State.data.index);


      } else if(State.data.microguideSlug !== undefined && State.data.storySlug === undefined && modal.modalOpen) {
        /* Slightly weird case where we're navigating back to the original microguide page*/
        modal.displayMicroguideModal(1);

      } else if (State.data.microguideSlug !== undefined && State.data.storySlug !== undefined && State.data.index !== undefined && !modal.modalOpen) {
        /* If someone opens a modal, then closes it, then wants to go back we'll be here  */
        modal.openMicroguideModal(State.data.index);

      } else if (State.data.microguideSlug !== undefined && State.data.storySlug !== undefined && State.data.index !== undefined && modal.modalOpen) {

        /*This means that the modal is open and navigating between pages. */
        modal.displayMicroguideModal(State.data.index);

      } else if (State.data.closeModal || (State.data.microguideSlug === undefined && modal.modalOpen)) {
        /*We're being told here to close the modal */
        modal.close(true);

      }

    });


  },

  microguideSelect: function() {

    modal.originalUrl = new String(window.location.pathname);

    var microguideModalSlug = $(this).data('slug');
    var microguideModalTitle = $(this).data('title');

    if ($(this).data('story_slug') !== undefined && $(this).data('index') !== undefined && $(this).data('story_title') !== undefined) {
      var storySlug = $(this).data('story_slug');
      var storyTitle = $(this).data('story-title');
      var index = $(this).data('index');
      var stateObj = { microguideSlug: microguideModalSlug, microguideTitle: microguideModalTitle, index: index, storySlug: storySlug};
      modal.History.pushState(stateObj, storyTitle, "/microguide/" + microguideModalSlug  + "/" + storySlug);

    } else{
      var stateObj = { microguideSlug: microguideModalSlug, microguideTitle: microguideModalSlug};
      modal.History.pushState(stateObj, microguideModalTitle, "/microguide/" + microguideModalSlug );

    }

    if ($(this).attr('id') !== undefined) {
      _gaq.push(['_trackEvent', 'Navigation', 'Microguide Modal', $(this).attr('id')]);
    }
  },

  openMicroguideModal: function(index) {

    index = typeof index !== undefined ? index : 1;

    loader.create('loading...');
    br.overlay();

    $('.modal-microguide.hidden').removeClass('hidden');
    $('.modal-microguide').fadeIn();

    $('body').addClass('modal-open');

    modal.displayMicroguideModal(index);

    if (br.isMobileLayout()) {
      window.scrollTo(0,0);
      $(window).on('scroll', modal.scroll);
    }

  },

  displayMicroguideModal: function(index) {

    var slug = modal.microguideModalSlug;
    var title = modal.microguideModalTitle;

    modal.microguideModalPhoto = 0;
    modal.storyIndex = index;

    var ajaxLink = '/microguide/microguideModal/';

    $.get(ajaxLink, {slug:slug, story:index}, function(response) {

      if(response.success) {

        /*var stateObj = { microSlug: response.microSlug, microTitle: response.microTitle, storySlug: response.storySlug, storyTitle: response.storyTitle };
        modal.History.pushState(stateObj, response.storyTitle, "/microguide/" + response.microSlug + "/" + response.storySlug);*/

        modal.modalOpen = true;

        $('.modal-microguide .microguide-container').html(response.html);

        modal.modalHandlers();

      } else {

        loader.create("Microguide not found");
        modal.close(true);
      }

    }, 'json');

  },

  modalHandlers: function() {
    bravo.d();
    bravo.handlers();

    modal.windowHeight = $(window).height();
    setTimeout(modal.setHeight, 300);
    $('.story-image img').on('load', modal.imageLoad);

    if (!br.isMobileLayout()) {
      $('.modal-microguide .text-container').niceScroll({cursorwidth: 15, cursoropacitymax: '.4', cursorborder: '0'});
    }

    $('.modal-microguide .close').on('click', function(){

      modal.History.pushState({closeModal: true},'',modal.originalUrl);

    });

    $('.modal-microguide .nav-arrows .left-arrow').on('click', modal.navStory);
    $('.modal-microguide .nav-arrows .right-arrow').on('click', modal.navStory);

    $('.author a').on('click', function() {

      location.href = br.G_URL + '#authors=' + $(this).data('slug');

    });

    $('.modal-microguide .thumb-column img').on('mouseover', function() {
      if ($(this).data('num') != modal.microguideModalPhoto) {

        $('.modal-microguide .thumb-column img[data-num="' + modal.microguideModalPhoto + '"]').removeClass('faded');
        modal.microguideModalPhoto = $(this).data('num');

        $('.modal-microguide .image-container .story-image.main img').attr('src', $(this).data('fullsize'));
        $('.modal-microguide .thumb-column img[data-num="' + modal.microguideModalPhoto + '"]').addClass('faded');

        if ($('.modal-microguide .thumb-column img[data-num="' + modal.microguideModalPhoto + '"]').data('caption') == '' ) {
          $('.modal-microguide .image-caption-container.main .image-caption').html('&nbsp;');

        } else {
          $('.modal-microguide .image-caption-container.main .image-caption').text($('.modal-microguide .thumb-column img[data-num="' + modal.microguideModalPhoto + '"]').data('caption'));

        }
      }
    });


    $('.modal-microguide .modal-share a').on('click', function() {
      var url = $(this).attr('href');

      newwindow=window.open(url,'Share Your BYC Story','height=400,width=500');
      if (window.focus) {newwindow.focus()}

      return false;

    });

    $(document).on('keydown', function(event){
      if(event.keyCode == 27)
        modal.History.pushState({closeModal: true},'',modal.originalUrl);
      else if (event.keyCode == 39){
        var slug = $('.nav-arrows .right-arrow').data('slug');
        var title = $('.nav-arrows .right-arrow').data('title');
        var index = $('.nav-arrows .right-arrow').data('index');
        modal.navStory(slug, title, index);

      }
      else if (event.keyCode == 37){
        var slug = $('.nav-arrows .left-arrow').data('slug');
        var title = $('.nav-arrows .left-arrow').data('title');
        var index = $('.nav-arrows .left-arrow').data('index');
        modal.navStory(slug, title, index);

      }
    });

    /* For making it so that when you click outside the modal, it closes the 
     * modal. 
     */
    $('.modal-microguide .microguide-container').clickOutside(function(event, obj) { modal.History.pushState({closeModal: true},'',modal.originalUrl); });

  },

  navStory: function(slug, title, index) {

    if(slug === undefined || title === undefined || index === undefined ) {
      var slug = $(this).data('slug');
      var title = $(this).data('title');
      var index = $(this).data('index');
    }

    var stateObj = {microguideSlug: modal.microguideModalSlug, microguideTitle: modal.microguideModalTitle, storySlug: slug, storyTitle: title, index: index}
    modal.History.pushState(stateObj, title, "/microguide/" + modal.microguideModalSlug + '/' + slug);

  },

  close: function(removeOverlay) {

    // Remove Handlers
    $(document).off('keydown'); 
    $('.modal-microguide .modal-share a').off('click');
    $('.modal-microguide .thumb-column img').off('mouseover');
    $('.modal-microguide .close').off('click');
    $('.modal-microguide .nav-arrows .left-arrow').off('click');
    $('.modal-microguide .nav-arrows .right-arrow').off('click');
    $('.author span').off('click');
    $('body').unbind('click');

    // Remove Scrollbar
    $('.modal-microguide .text-container').getNiceScroll().hide();

    modal.modalOpen = false;
    $('body').removeClass('modal-open');
    $('.modal-microguide').fadeOut();

    if(removeOverlay)
      br.overlay(true);

    $(window).off('scroll', modal.scroll);

  },

  scroll: function(e) {
    if (modal.modalHeight - $(window).scrollTop() < modal.windowHeight - 200) {
      if (!modal.scrolling) {
        modal.scrolling = setInterval(modal.scrollBack, 300);
      }
    }
  },

  scrollBack: function() {
    if (modal.lastScrollTop === null) {
      modal.lastScrollTop = $(window).scrollTop();
      modal.lastScrollTime = (new Date()).getTime();
    }
    var thisTop = $(window).scrollTop();
    var thisTime = (new Date()).getTime();
    if (thisTime <= modal.lastScrollTime + 100) return;

    var velocity = (thisTop - modal.lastScrollTop) / (thisTime - modal.lastScrollTime);
    if (velocity < .1) {
      clearInterval(modal.scrolling);
      modal.scrolling = null;
      modal.lastScrollTop = null;
      $('html, body').animate({ scrollTop: modal.modalHeight - modal.windowHeight }, 200);
    }

    modal.lastScrollTop = thisTop;
    modal.lastScrollTime = thisTime;
    return;

  },

  setHeight: function() {
    modal.modalHeight = ($('.modal-microguide').height() - $('.modal-footer').height());
  },

  imageLoad: function() {
    var presetHeight = $(this).css('height');
    // Uncomment to have variable height and no stretching
    //  $(this).css({ 'height' : 'auto', 'max-height' : presetHeight});
    modal.setHeight();
  }

}
