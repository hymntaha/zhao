
var microguide = {

  loading: false,
  moreing: false,
  nomore: false,
  bio: false,
  sort: 'created',
  status: 0,
  offset: 0,
  columns: 1,
  slug: null,
  liwrap: 'microguide',
  slider_start: 1,
  supportsMediaQuery: null,

  i: function() {

    this.handlers();
    microguide.columns = microguide.countColumns();
    microguide.selectHandlers();
  },

  handlers: function() {
  },
  
  countColumns: function(width) {

    var bounds = [0,432,672,896,1184,1568,1792];
    var i;

    if (!width) {
      width = $(window).width();
    }

    if (microguide.supportsMediaQuery === null) {
      microguide.testMediaQuery();
    }

    if (microguide.supportsMediaQuery) {
      for (i = bounds.length-1; i >= 0; i--) {
        if (width >= bounds[i]) {
          return i+1;
        }
      }
      return bounds.length;
      
    } else {
      return Math.floor(width/microguide.columnWidth);
    }

  },

  selectHandlers: function() {
    
    $('.microguide-landing .first-story').on('click',modal.microguideSelect);
    $('.microguide-landing .microguide-story:not(.invited,.pending,.draft,.new) .story-box').on('click', modal.microguideSelect);
    $('.microguide-landing .microguide-story.invited .story-box').on('click', function() {
      location.href = br.G_URL + 'share/' + $(this).data('story_slug');
    });
    $('.microguide-landing .microguide-story.new .story-box').on('click', function() {
      document.location.href = br.G_URL + 'share/?microguide=' + microguide.slug + '&returnurl=' + encodeURIComponent(document.URL);
    });
  },
  
  select: function() {
    document.location.href = '/microguide/' + $(this).data('slug');
  },

  testMediaQuery: function() {
    $('body').append('<div class="test-media-query"></div>');
    microguide.supportsMediaQuery = ($('.test-media-query').css('width') === '10px');
  },

  edit: {

    tags : [],
    tooltips : [],
    changed : false,

    i: function() {
      this.sortable();
      this.handlers();
    },

    confirmChanged: function() {
      if (microguide.edit.changed) {
        return confirm('You have unsaved changes. Are you sure you want to leave this page?');
      }
      return true;
    },

    handlers: function() {
      $(".button.save").on("click", this.submit);
      $('#tags').keyup(microguide.edit.tagging);
      $('.tag').fadeIn();
      $('.tag_close').click(microguide.edit.tagClose);
      $('.add-story').on('click', microguide.edit.storyEntry);
      $('.top-navigation').on('click', function() { if (microguide.edit.confirmChanged()) { document.location.href = '/my/microguides'}});
      this.initAddStoryHandler();
      this.refreshRemoveStoryHandler();
      this.authorHandler();
      br.inputfocus($('.input-text'));
      $('input, textarea').on('change', function() { microguide.edit.changed = true; });
      $('.create-new-story').on('click', microguide.edit.createStoryHandler);
      br.countWords('#description', '.word-count', 40);
      $('.microguide-landing .microguide-story:not(.add-story, .invited) .story-box').on('click', microguide.edit.redirectToStory);

      $('.input-text').on('focus', function() {
        if (!user.loggedin) {
          user.loginRegister();
          return false;
        }
      });
 
    },
    
    redirectToStory: function(event) {
      var slug = $(event.currentTarget).data('story-slug');
      document.location.href = '/share/' + slug;
      
    },

    createStoryHandler: function(event) {
      if (!$("#microguideid").val() || microguide.edit.changed) {
        loader.create('saving your work...');
        microguide.edit.submit(event);
      } else {
        microguide.edit.createStoryPostSaveHandler($(event.srcElement));
      }
    },

    createStoryPostSaveHandler: function(src) {
      var slug,
          invitedArg = '';

      slug = $('#microguideid').data('slug');
      if (src.data('invited') == 1) {
        invitedArg = '&invited=1';
      }
      document.location.href = '/share/?microguide=' + slug + invitedArg + '&returnurl=' + br.G_URL + 'microguide/create/' + slug;
    },

    authorHandler: function() {

      if ($('#author').length == 0) return;

      var selected = null,
      initialValue = $('#author').val();

      $( "#author" ).autocomplete({
        source: function( request, response ) {
          var term = request.term;
          if (term.indexOf('http://') > -1) {
            term = br.extractUrlBase(term);
          }
          $.get( '/user/search/' + encodeURIComponent(term), function(data) {
            response( $.map( data, function( user ) {
              return {
                label: user.username + " (" + user.slug + ")",
                value: user.username,
                slug: user.slug
              }
            } 
            )) }, 'json');
        },
        close: function( event, ui ) {
          if (!selected) {
            $("#author").val(initialValue);
          }
          initialValue = $("#author").val();
          selected = true;
        },
        search: function() {
          selected = false;
        },
        select: function( event, ui ) {
          selected = true;
          $('#author').data('slug',ui.item.slug);
        }
      });

    },

    clearErrors: function() {
      if (microguide.edit.tooltips.length) {
        tooltip.destroy(microguide.edit.tooltips);
        microguide.edit.tooltips = [];
      }
    },

    storyEntry: function() {

      if ($("#new-story").is(":visible")) {
	return;
      }

      var xy = $(this).offset();
      var windowWidth = $(window).width();
      var addStoryWidth = $(this).width()
      var leftMargin = xy.left/windowWidth;
      var rightMargin = (windowWidth - xy.left - addStoryWidth )/windowWidth;
      var leftPosition = 0;
      var carrotPosition = 0;

      if (leftMargin < .1) {
        leftPosition = 25;
        carrotPosition = 50;
      } else if (rightMargin < .1) {
        leftPosition = -150;
        carrotPosition = 300;
      } else {
        leftPosition = -55;
        carrotPosition = 175;
      }

      $("#new-story").css({ display: 'inline-block', top: (xy.top + 150) + 'px', left: (xy.left + leftPosition) + 'px' });
      $(".new-story-carrot").css({ left: carrotPosition + 'px' });

      setTimeout(
        function() {
          $("#new-story").clickOutside(
            function(event) {
              $('#new-story').hide();
	      return true;
            });
        },1);
    },

    initAddStoryHandler: function() {

      var exclude = null;

      $( "#new-story-search" ).autocomplete({
        source: function( request, response ) {
          var term = request.term;
          if (exclude === null) {
            exclude = microguide.edit.storyIds();
          }
          if (term.indexOf('http://') > -1) {
            term = br.extractUrlBase(term);
          }
          $.get( '/story/search/'
            + encodeURIComponent(term)
            + '?exclude=' + encodeURIComponent(JSON.stringify(exclude)),
            function(data) {
              response( $.map( data, function( story ) {
                return {
                  label: story.title + " (" + story.slug + ")",
                  value: story.title,
                  slug: story.slug,
                  storyId: story.id
                }
              })) }, 'json');
        },
        select: function( event, ui ) {
          $("#new-story-search").data('story-id', ui.item.storyId);
          $("#new-story-search").data('slug', ui.item.slug);
          $("#new-story").hide();
          $("#new-story").clickOutside('off');

          $.get( '/story/previewfragment/'
            + ui.item.slug
            + '?offset=' + microguide.edit.storyIds().length,
            function(response) {
              var item = $(response.html);
	      item.addClass('microguide-story');
              item.appendTo($('#story-list'));
              $( "#story-list" ).sortable("refresh");
              $('#new-story-search').val('');
              $("#new-story-search").data('story-id','');
              $("#new-story-search").data('slug','');
              exclude = null;
              microguide.edit.refreshRemoveStoryHandler();
              microguide.edit.changed = true;
	    }, 'json');

        }
      });
    },

    refreshRemoveStoryHandler: function() {
      $(".delete").off("click");
      $(".delete").on("click", function() {
        $(this).parent().parent().remove();
        microguide.edit.clearErrors();
      });
    },

    submit: function(event) {
      microguide.edit.clearErrors();
      
      var action,
          postAction;

      action = $(event.srcElement).data('action');
      postAction = $(event.srcElement).data('post-action');
      
      var drafts = false;
      $('.microguide-landing .microguide-story').each(function() {
        if ($(this).hasClass('draft')) {
          drafts = true;
        }
      });
      
      if (drafts && action === 'submit') {
        loader.create('There are unsubmitted draft stories. <br/>Please complete them before publishing your microguide. ',
                      {timeout: 10000});
        return false;
      }
      
      params = {
        microguideId: $("#microguideid").val(),
        access: $('input:radio[name=access]:checked').val(),
        title: $("#title").val(),
        description: $("#description").val(),
        storyIds: microguide.edit.storyIds(),
        action: action,
        tags: microguide.edit.tags,
        status: $('input:radio[name=status]:checked').length ? $('input:radio[name=status]:checked').val() : '',
        authorSlug: $("#author").length ? $("#author").data('slug') : '',
        kindle: $('#kindle').val(),
        itunes: $('#itunes').val()
      }

      if ($('#featureid').length) {
        params.featureid = $('#featureid').val();
        params.featurestatus = $('input:radio[name=featurestatus]:checked').val();
        params.featurequestion = $('#featurequestion').val();
        params.featurequestionverb = $('#featurequestionverb').val();
        params.featurequestionplace = $('#featurequestionplace').val();
      }

      $.post('/microguide/edit',params, function(response) {
        if (response.success) {

          loader.create('success...');
          if (response.operation == 'created') {
	    $("#microguideid").val(response.id);
	    $("#microguideid").data('slug', response.slug);
	  }
	  microguide.edit.changed = true;
          switch (params.action) {
          case 'save':
            switch (postAction) {
            case 'viewAllMicroguides':
              document.location.href = '/microguide/';
              break;
            case 'createStory':
	      microguide.edit.createStoryPostSaveHandler($(event.srcElement));
              break;
            case 'viewMyMicroguides':
              document.location.href = '/my/microguides';
              break;
            default:
              br.scrollTo($('.microguide-create-header').offset().top - 20);
            }
            break;
          case 'submit':
            document.location.href = '/my/microguides';
            break;
	  }

        } else {

          var scrolled = false;
          for (var i in response.errors) {

            if (i.charAt(0) == '#') {
              microguide.edit.tooltips.push(
                tooltip.create($(i), response.errors[i], {type: 'error', pos: 'r'})
              );
            } else {
              switch (i) {
              case 'storyIds':
                $success = false;
                $('#story-list li').each(function(ind,el) { 
                  if (response.errors[i].indexOf($(el).data('storyid')) > -1) {
                    microguide.edit.tooltips.push(
                      tooltip.create($(el), 'We could not find this story', {type: 'error', pos: 'r'})
                    );
		  }
                });

		break;
              }
	    }
            if (!scrolled) {
              br.scrollTo($(i).offset().top - 20);
              scrolled = true;
            }
	  }
          loader.create('error editing microguide.');
	}

      }, 'json');

    },

    storyIds: function() {
      var ids = [];
      $('.story-box').each(function(i,el) { 
        if ($(el).data('story-id')) {
          ids.push($(el).data('story-id'));
        }
      });
      return ids;

    },

    tagging: function(event) {

      if (event.keyCode == 188 || event.keyCode == 13) {

        var tag = $.trim($(this).val().split(',')[0]);

        if (tag == "") {
          return false;
        }

        tooltip.destroy(microguide.edit.tooltips);

        if (microguide.edit.tags.length >= 20) {

          microguide.edit.tooltips.push(tooltip.create($('#tags'), 'You cannot have more than 20 tags', {type: 'error', pos: 'tl'}));
          $(this).val('');

        } else if (!br.inArray(tag, microguide.edit.tags)) { 

          $('.tags').append('<div class="tag"><div class="tag_close"></div><p>' + tag + '</p></div>');
          $('.tag').fadeIn(200);
          microguide.edit.tags.push(tag);
          loader.create('tag added');
          $('.tag_close').unbind('click');
          $('.tag_close').click(microguide.edit.tagClose);
          $(this).val('');

        } else {

          microguide.edit.tooltips.push(tooltip.create($('#tags'), 'tag "' + tag + '" already exists', {type: 'error', pos: 'tl'}));
          $(this).val('');

        }

      }

    },

    tagClose: function() {

      var el = $(this).parent();
      var tag = el.find('p').text();
      microguide.edit.tags.splice(share.tags.indexOf(tag), 1);
      el.fadeOut(200, function() { el.remove(); });

    },

    sortable: function() {

      $( "#story-list" ).sortable({
        revert: true,
        start: function (event, ui) {
          $( event.toElement ).one('click', function(e){ e.stopImmediatePropagation(); } );
        }
      });
      $( "#story-list" ).draggable({
        connectToSortable: "#story-list",
        helper: "clone",
        revert: "invalid"
      });
      $( "#story-list" ).on("sortupdate", function() {
        $('.microguide-container .story-number').each(function(ind,el) { $(el).html(ind+1); })
        microguide.edit.changed = true;
      });

    }

  }

}

