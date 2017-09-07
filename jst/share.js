
var share = {

  tags: [],
  id: false,
  tooltips: [],
  shareing: false,
  deleting: false,
  maximages: 5,

  i: function() {
    this.handlers();
    this.sortable();
    this.browserCheck();
  },

  handlers: function() {

    $('#text').keydown(share.words);
    $('#tags').keyup(share.tagging);

    $('.tag').fadeIn();

    $('.tag_close').click(share.tagClose);

    $('.upload_button').click(share.img.select);
    $('.real_upload_button').change(share.img.change);

    $('.submit, .save').click(share.submit);

    rich.i($('.toolbar1'), $('#text'));

    br.inputfocus($('.share .input-text'));
 
    $('.share .input-text')
      .focus(share.help.focus)
      .blur(share.help.blur);

    $('.adminStatus li').click(share.adminStatus);

    $('.comment_submit').click(share.comment);

    $('.image_outer').on('click', '.delete', function(e) {
      if ($(e.delegateTarget).find('img').length > 0) {
        var num = $(e.delegateTarget).data('num')
        share.img.del(num);
      }
    });

    $('.buttons .delete-story').on('click', function() {
      if(confirm('Really delete this story? This cannot be undone.')) {
        share._delete();
      }
    });

    $('.buttons .cancel').on('click', function() {
      if (document.referrer) {
        location.href = document.referrer;
      } else {
        location.href = '/my/stories';
      }
    });

    $('.redirect .redirect-button').button();
    
    if (window.location.pathname.indexOf("redirect") != -1) {
      share.setUpClip();
    }

    $('.share-preview .top').on('click', function() {
      br.scrollTo($('.topOfPage').offset().top)
    });

    $('.button.goback').on('click', function() {
      document.location.href = $('#returnurl').val();
    });
            
    $('.share').on('change', function() {
      $('.share-preview').addClass('hidden');
    });

    $('.freshen').on('click', share.freshen);

    br.addScrollToTop('.buttons');

  },
  
  sortable: function() {
    s = $( ".images" ).sortable({
      revert: true
    });
    $( ".images" ).draggable({
      connectToSortable: ".images",
      helper: "clone",
      revert: "invalid"
    });
  },
  
  /*Test to see if browser supports filreader and datauri and if not display message about upgrading to a 
   * modern browser.*/
  browserCheck: function() {
    var data = new Image();
    
    // We attempt to load a 1x1 image, and if it doesn't load correctly, or if !window.FileReader, 
    // we show the browser message
    data.onload = data.onerror = function(){
      if (this.width != 1 || this.height != 1 || !window.FileReader) {
        $('.share .browser-message').html("We're sorry, but html5 photo uploading will not work with your web browser.  Please update to the latest version of your browser software, or try a different browser.  Thanks!");	
        $('.share .browser-message.hidden').removeClass('hidden');
      }      
    }
    
    data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
    
  },
  
  /*Show a dialog which asks the editor if they'd like to prompt the author of a story to send a facebook message
  to the owner of the facebook page.*/
  facebookMessage: function(){
    $('.facebook-share').empty();
    /*Start by initializing the dialog box if not already*/
    if (!share.fbSearchAgain) {
      share.dialogNode = $('.facebook-share');
      share.dialogNode.removeClass('hidden');
      share.fbDialog = share.dialogNode.dialog({
        modal: true,
        width:400,
        height:700,
        position:"center, center",
        autoOpen: false,
        buttons: {
            
            "Email without prompting": function() {
              var storyId = $('.adminStatus .active').data('id');
              var params = {
                  'prompt': false,
                  'story_id':storyId,
                  'preview': true
              };
              
              $.post('/share/message',params, function(response){
                if (response.success) {
                  share.dialogNode.dialog("close");
                  share.acceptedMessage(response.body, params);
                } else {
                  loader.create('problem sending mail.');
                }
              }, 'json');  
            },
            
            'Close': function() {
              $( this ).dialog( "close" );
            }
        },
        close: function(){share.fbSearchAgain = true;}
      });
    }
    share.dialogNode.dialog("open");
    $('.ui-dialog :button').blur();
    
    /*Right now what we've got to go with when searching the facebook graph api is the title of the story and the information
    stored on the page with regards to "location." We need better handling of crappy results from facebook.*/
    var title = $('.share #title').val();
    var locationName = $('.share #location_name').val().toLowerCase();
    var location = $('.share #location_formatted').val().toLowerCase();
    var lat = $('.share #location_latitude').val();
    var lng = $('.share #location_longitude').val();
    
    /*Sometimes the "location_name" field holds an actual valid title of the location, but sometimes it just mirrors the
    geographical location in "location_formatted" - ie "Paris, Paris France" vs "Mary's Confectioners, Paris, France" 
    We only want to use the "location_name" as a search term if its not just the same as the geographical location name.*/
    var searchTerm = (location.indexOf(locationName) == -1) ? locationName : title;
    
    var html = '<div class = "fb-query-div"><label for="name">Query:</label><input type="text" name="fb-query" id="fb-query" class="text ui-widget-content ui-corner-all" /> <div class="fb-check-div"><input type=checkbox class = "fb-check" id="fb-latlng" label="with lat lng"/>use Lat/Lng<br/><input type=checkbox class = "fb-check" id="fb-place-check" label="is a place"/>match places</div></div>';
    share.searchNode = $(html);
    share.searchNode.appendTo(share.dialogNode);
    $('#fb-latlng').attr('checked', true);
    $('#fb-place-check').attr('checked', true);
    
    var cache = {};
    $( "#fb-query" ).autocomplete({
      minLength: 2,
      source: function( request, response ) {
        var term = request.term;
        if (!$('#fb-place-check').prop('checked')) {
          var query = term +'&type=page';
        } else {
          if ($('#fb-latlng').prop('checked')) {
            var query = term + '&type=place&center='+lat+','+lng;
          } else {
            var query = term + '&type=place';
          }
        }
        $.get( '/share/fbProxy?query=' + query, function( data ) {
          share.displayFbResults(data);
        }, 'json');
      },
      search: function( event, ui){
        $('.fb-results').empty();
        $('.fb-results').html('<img src="/img/ajax-loader.gif" class="ajax-loader"/>');
      }
    });
    
    $('#fb-query').val(searchTerm);
    $('#fb-query').autocomplete('search');
    
    $('#fb-latlng').on('change', function(){
      var term = $('#fb-query').val();
      $('#fb-query').autocomplete('search', term);
    });
    
    $('#fb-place-check').on('change', function(){
      var term = $('#fb-query').val();
      $('#fb-query').autocomplete('search', term);
    });
    
  },
  
  displayFbResults: function(response){
    var fbPlaces = response.data;
    $('.fb-results').remove();
    share.resultsNode = $('<div class=fb-results></div>')
    share.resultsNode.appendTo(share.dialogNode);
    
    if (fbPlaces.length == 0) {
      var html = '<div class="no-results">No results found!</div>';
      var resultNode = $(html);
      resultNode.appendTo(share.resultsNode);
    }
    
    for (var i = 0; i < fbPlaces.length; i++) {      
      /*For each returned facebook place, we want to add that to the dialog, include a button for sending them a message, and set up a tooltip
        which will display more information about the facebook page.*/
      var place = fbPlaces[i];
      var html = '<div class="fb-place" data-id="'+place.id+'"><div class="fb-place-title"><a href="http://facebook.com/' + place.id + '" target="_blank">' + place.name + '</a></div></div>';
      var resultNode = $(html);
      
      $('<div class="fb-button-right"><div class="fb-button" data-id="'+place.id+'">Notify</div></div>').appendTo(resultNode);
      resultNode.appendTo(share.resultsNode);
      
      var url = 'https://graph.facebook.com/' + place.id;
      $.get('/share/fbProxy?page=true&id=' + place.id, share.showFacebookToolTip, 'json');
      
    }

    $( ".fb-results .fb-button" ).button().click(function( event ) {
      var storyId = $('.adminStatus .active').data('id');
      var params = {
          'prompt': true,
          'fb_id': $(this).data('id'),
          'story_id':storyId,
          'preview': true
      };
      
      $.post('/share/message',params, function(response){
        if (response.success) {
          share.dialogNode.dialog("close");
          share.acceptedMessage(response.body, params);
        } else {
          loader.create('problem sending mail.');
        }

      }, 'json');
    });
  },
  
  /*These tooltips are for showing the information about facebook pages as tooltips when an editor is 
  deciding which facebook page to prompt the author to send a message to.*/
  showFacebookToolTip: function(data){
    var responseObj = data;
    var id = responseObj.id;
    var titleNode = $('.fb-place[data-id="'+id+'"]');
    var selector = '.fb-place[data-id="'+id+'"]';
    
    /*We have to check what information was returned to us about the facebook place and display what's available.
    There are other properties that some places return, but we can deal with that later if we want.*/
    toolTipHTML = '<div class = "location-tooltip">' + responseObj.name + '<br/>' + 
        ((typeof responseObj.location.city === "undefined") ? " ": responseObj.location.city + ', ') + 
        ((typeof responseObj.location.state === "undefined") ? " ": responseObj.location.state + ', ') + 
        ((typeof responseObj.location.country === "undefined") ? " ": responseObj.location.country + ', ') +
        ((typeof responseObj.likes === "undefined") ? " ": '<br/>likes: ' + responseObj.likes + ',') + 
        ((typeof responseObj.checkins === "undefined") ? " ": '<br/>checkins: ' + responseObj.checkins + ',') +
        ((typeof responseObj.category === "undefined") ? " ": '<br/>category: ' + responseObj.category);
    
    titleNode.tooltip({
      items: selector,
      content: toolTipHTML,
      track:false
    });
    
  },
  acceptedMessage: function(message, params) {

    var storyId = $('.adminStatus .active').data('id');
    
    /*Start by initializing the dialog box if not already*/
    if (!share.acceptedAgain) {
      
      share.acceptedNode = $('.accepted-message');
      share.acceptedNode.show();
      
      share.acceptedDialog = share.acceptedNode.dialog({
        modal: true,
        width:800,
        height:700,
        position:"center, center",
        autoOpen: false,
        buttons: {
            
            "Send message": function() {
              var storyId = $('.adminStatus .active').data('id');
              params.preview = false;
              params.message = message;

              $.post('/share/message', params, function(response) {
                if (response.success) {
                  share.acceptedNode.dialog("close");
                  loader.create('message sent successfully.');
                } else {
                  loader.create('problem sending mail.');
                }
              }, 'json');
            },

            'Close': function() {
              $( this ).dialog( "close" );
            }
        },
        close: function(){share.acceptedAgain = true;}
	});
    }

    share.acceptedNode.dialog("open");
    
    $('.accepted-content .accepted-textarea').val(message);

  },
  /* Prompts editor with email to send to user notifying them that their story has been declined. */
  rejectedMessage: function(){

    var storyId = $('.adminStatus .active').data('id');
    
    /*Start by initializing the dialog box if not already*/
    if (!share.rejectedAgain) {
      
      share.rejectedNode = $('.rejected-message');
      share.rejectedNode.removeClass('hidden');
      
      share.rejectedDialog = share.rejectedNode.dialog({
        modal: true,
        width:800,
        height:700,
        position:"center, center",
        autoOpen: false,
        buttons: {
            
            "Send message": function() {
              var storyId = $('.adminStatus .active').data('id');
              var params = {
                  'rejected': true,
                  'story_id':storyId,
                  'message': $('.rejected-content .rejected-textarea').val()
              };
              
              $.post('/share/sendRejectedMessage',params, function(response){
                if (response.success) {
                  share.rejectedNode.dialog("close");
                  loader.create('message sent successfully.');
                } else {
                  loader.create('problem sending mail.');
                }
              }, 'json');  
            },
            
            'Close': function() {
              $( this ).dialog( "close" );
            }
        },
        close: function(){share.rejectedAgain = true;}
      });
    }

    share.rejectedNode.dialog("open");
    
    
    /*Ok now we'll query the server to get the information to put into the textarea and 
     * which will eventually end up in the email
     */
    var params = {
        'rejected': true,
        'story_id':storyId
    };
    
    $.get('/share/getRejectedMessage', params, function(response){
      if (response.sucess) {
        $('.rejected-content .rejected-textarea').val(response.message);
      } else {
        loader.create('Problem finding story and loading comments');
      }
    }, 'json');
  },
  
  adminStatus: function() {

    $('.adminStatus li').removeClass('active');
    $(this).addClass('active');

    var params = {
      'id': $(this).data('id'),
      'status': $(this).text()
    };

    $.get('/share/status', params, function(response) {
      
      if (response.success) {
        loader.create('status changed sucessfully');
        /*Check to see if editor is connected w/ facebook. If so, we'll go forward with checking which facebook page
        to send message to.*/
        if (!(typeof response.fb_access === "undefined")) {
          if (response.fb_access == false) {
            loader.create('you must be connected with facebook in order to prompt users to send messages to pages');
          } else {
            share.facebookMessage();
          }
        }
        else if (!(typeof response.rejected == "undefined")) {
          if (response.rejected) {
            share.rejectedMessage();
          }
        }
      } else {
        loader.create('error changing status');
      }
    }, 'json');

  },

  freshen: function() {

    var params = {
      'id': $(this).data('id')
    };

    if(confirm('This will set the story creation and update times to now.')) {

      $.get('/share/freshen', params, function(response) {
      
        if (response.success) {
          loader.create('This story is now at the top of the homepage');
        } else {
          loader.create('error updating timestamps');
        }

      }, 'json');

    }

  },

  help: {
    tooltip: {},

    focus: function(obj) {

      if (!user.loggedin) {
        user.loginRegister();
        return false;
      }

      share.help.tooltip = tooltip.create($(this), $(this).data('help'), 
        {type: 'comment', pos:'tl'}
      );

    },

    blur: function(obj) {
      tooltip.destroy(share.help.tooltip);
    }

  },

  words: function() {

    $('.words').html($(this).val().split(/\s+/).length + ' word(s)');

  },

  tagging: function(event) {

    if (event.keyCode == 188 || event.keyCode == 13) {

      var tag = $.trim($(this).val().split(',')[0]);

      if (tag == "") {
        return false;
      }

      tooltip.destroy(share.tooltips);

      if (share.tags.length >= 20) {

        share.tooltips.push(tooltip.create($('#tags'), 'You cannot have more than 20 tags', {type: 'error', pos: 'tl'}));
        $(this).val('');

      } else if (!br.inArray(tag, share.tags)) { 

        $('.tags').append('<div class="tag"><div class="tag_close"></div><p>' + tag + '</p></div>');
        $('.tag').fadeIn(200);
        share.tags.push(tag);
        loader.create('tag added');
        $('.tag_close').unbind('click');
        $('.tag_close').click(share.tagClose);
        $(this).val('');

      } else {

        share.tooltips.push(tooltip.create($('#tags'), 'tag "' + tag + '" already exists', {type: 'error', pos: 'tl'}));
        $(this).val('');

      }

    }

  },

  tagClose: function() {

    var el = $(this).parent();
    var tag = el.find('p').text();
    share.tags.splice(share.tags.indexOf(tag), 1);
    el.fadeOut(200, function() { el.remove(); });

  },

  img: {

    files: [],
    data: [],

    select: function() {

      $('.real_upload_button').trigger('click');
      $('.real_upload_button').unbind('change');
      $('.real_upload_button').change(share.img.change);

    },

    reset: function() {

      share.img.files = [];
      share.img.data = [];

      for (var i = 0; i != share.maximages; i++) {
        $('.image_' + i).html('photo ' + (i+1));
        $('#caption_' + i).val($('#caption_' + i).data('tip'));
      }

      for (var i = (share.maximages - 1); i >= 0; i--) { //resets the sortable
        $('.images').prepend($('.image_outer_'+i));
      }

      loader.create('images reset');

    },

    del: function(num) {

      var data = [];
      var order = share.img.sortedOrder();

      for (var j = 0; j < order.length; j++) {
        var i = order[j];
        if (i != num) {

          // grab our data if we havent yet
          share.img.data[i].caption = $('#caption_' + i).val();
          share.img.data[i].src = $('.image_' + i + ' img').attr('src');
        
          data.push(share.img.data[i]);

        }

      }

      share.img.reset();
      share.img.data = data;

      for (var i = 0; i < share.img.data.length; i++) {
        $('.image_' + i).html('<img src="' + share.img.data[i].src + '" />');
        $('#caption_' + i).val(share.img.data[i].caption);
        delete share.img.data[i].src;
      }

      loader.create('photo deleted');

    },

    change: function(evt) {

      if ((evt.target.files.length + share.img.data.length) > share.maximages) {
        loader.create('No more than ' + share.maximages + ' images');
        return false;
      }

      for (var i in evt.target.files) {
        if (evt.target.files.hasOwnProperty(i) && typeof evt.target.files[i] == 'object') {
          share.img.files.push(evt.target.files[i]);
        }
      }

      var total = evt.target.files.length;
      var completed = 0;

      Array.prototype.forEach.call(evt.target.files, function(f, i) {

        
        // make sure we're all images of supported types
        if (f.type != 'image/jpeg' && f.type != 'image/png' && f.type != 'image/gif') {

          tooltip.create($('.upload_button'), 'invalid image type(s)', 
            {type: 'error', pos: 'tl', timeout: 4000});

          return false;
        }

        // make our thumbnails / store the base64 data
        var reader = new FileReader();

        reader.onloadend = function(evt) {

          loader.create('loading images.. ', {progress:  0, color: 'orange', timeout: false});

          if (evt.target.readyState == FileReader.DONE) {

            var image = new Image();
            image.src = evt.target.result;


            image.onload = function() {

              var width = image.width, height = image.height;

              if (share.img.resize(width, height) != false) {

                var tmp = share.img.resize(image.width, image.height);
                width = tmp[0], height = tmp[1];

                var canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                var ctx = canvas.getContext("2d");
                ctx.drawImage(this, 0, 0, width, height);

                var data = canvas.toDataURL(f.type);

              } else {

                var data = evt.target.result;

              }

              var next = 0;
              for (var j = 0; j != share.maximages; j++) {
                if ($('.image_' + j).find('img').length < 1) {
                  next = j;
                  break;
                }
              }

              share.img.data[next] = {
               width: width,
               height: height,
               type: f.type
              };

              $('.image_' + next).html('<img src="' + data + '" title="' + escape(f.name) + '" />');
              $('.details_' + next).text('"' + f.name + '", ' + width + 'x' + height);

            }


            var complete =((++completed / total) * 100);

            if (complete == 100) {
              loader.create('load complete', {progress:  complete, color: 'orange'});
            } else {
              loader.create('loading images.. ', {progress:  complete, color: 'orange', timeout: false});
            }
          }

        };

        reader.readAsDataURL(f);

      });

    },

    resize: function(width, height) {

      var maxWidth = 960, maxHeight = 3000;

      if (width <= maxWidth && height <= maxHeight) {
        return false;
      }

      var ratio = Math.min(maxWidth/width, maxHeight/height);

      return [Math.round(ratio*width), Math.round(ratio*height)];

    },

    sortedOrder: function() {

      var order = [];

      $('.image_outer').each(function(i,o) { 
        var i = parseInt($(o).data('num'));
        if (typeof($('.image_' + i + ' img').attr('src')) !== "undefined") {
          order.push(i);
        }
      });

      return order;

    }

  },

  previewStory: function(slug) {

    $.get('/share/preview', {
      'story': slug
    }, function(data) {

      $('.share-preview').removeClass('hidden');
      $('.share-preview .share-preview-content').html(data);
              
      $('html, body').animate({
        scrollTop: $('.share-preview').offset().top - 20
      },1000);

      story.handleSliderClicks = true;
      story.sliderHandlers();
    });

  },

  submit: function() {

    if (share.shareing) {
      return false;
    }

    share.shareing = true;

    var showPreview = false;
    var returnUrl = $('#returnurl').length ? $('#returnurl').val() : '';

    var username = $('#username').length ? $('#username').val() : '';

    if($(this).attr('name') == 'save' ) {
      showPreview = true;
    } 
    
    var submit = 0;
    if ($(this).attr('name') == 'submit' || $(this).attr('name') == 'submit-lower') {
      submit = 1;
    }

    var isBio = $('#bio').val();

    var optional = ['phone','url'];

    var data = {
      submit: submit,
      id: share.id,
      bio: isBio,
      username: username,
      title: $('#title').val(),
      text: $('#text').val(),
      'location': {
        'name': $('#location_name').val(),
        'formatted': $('#location_formatted').val(),
        'latitude': $('#location_latitude').val(),
        'longitude': $('#location_longitude').val()
      },

      tags: share.tags,
      status: $('#status').length ? $('#status').val() : '',
      microguide: $('#microguide').length ? $('#microguide').val() : ''
    };

    for (var i = 0, max = optional.length; i != max; i++) {
      data[optional[i]] = $('#'+optional[i]).val();
    }


    var order = share.img.sortedOrder();

    data.files = [];
    for (var i = 0; i < order.length; i++) {
      data.files.push(share.img.data[order[i]]);
      data.files[i].data = $('.image_' + order[i] + ' img').attr('src');
      data.files[i].caption= $('#caption_' + order[i]).val();
      if (data.files[i].src) {
        delete data.files[i].src;
      }
    }

    var request = new XMLHttpRequest();

    request.upload.onprogress = function(evt) {
      var complete = Math.round((evt.loaded / evt.total)*100);
      loader.create('uploading.. ', {progress: complete, timeout: false});
    };

    request.onreadystatechange = function(evt) {

      if (this.readyState == 4 && this.status == 200) {

        var response = JSON.parse(request.responseText);
        var scrolled = false;

        tooltip.destroy(share.tooltips);

        if (response.success == true) {

          if (share.id === false) {
            loader.create('story created..');
          } else {
            loader.create('story saved..');
          }

          share.id = response.id;

          if (showPreview) {

            share.previewStory(response.slug.split('/')[2]);
            
          } else if (isBio == 1) {
            
            location.href = response.slug;

	  } else if (returnUrl) {

            location.href = returnUrl;

          } else {
            
            location.href = "/my/stories"; 
          
          }
          
        } else {

          loader.create('you have ' + br.countProperties(response.errors) + ' error(s)');

          for (var i in response.errors) {

            if (br.isNumeric(i)) {
              loader.create(response.errors[i]);
            } else {
              share.tooltips.push(
                tooltip.create($('#' + i), response.errors[i], {type: 'error', pos: 'r'})
              );
            }
            if (!scrolled) {
              br.scrollTo($('#' + i).offset().top - 20);
              scrolled = true;
            }

          }
        }

        share.shareing = false;

      }

    };

    request.open('POST', '/share/submit', true);
    request.send(JSON.stringify(data));

  },

  _delete: function() {

    if (share.deleting) {
      return false;
    }

    share.deleting = true;

    $.ajax({
      url: '/share/' + share.id,
      type: 'DELETE'
    }).done(function(d) {
      share.deleting = false;
      var result = JSON.parse(d);
      if (!result.success) {
        alert(result.errors.join("\n"));
      } else {
        alert("Story deleted");
        location.href = '/share/';
      }
    });

  },

  comment: function() {

    var data = {
      comment: $('#comment').val(),
      story: $('#story_id').val()
    };

    $.get('/share/comment', data, function(response) {

      if (response.success) {
        loader.create('comment successful');
        $('.comments').html(response.html);
        
        //Scrolls window in a smooth way to see comments
        var offset = $('.share .comments').offset();
        offset.top -= 20;
        $('html, body').animate({
                    scrollTop: offset.top
                },1000);
        $('#comment').val('');
      }

    }, 'json');

  },
  
  setUpClip: function(){
    
    $('.redirect-button').button();
    share.clip = new ZeroClipboard.Client();
    share.clip.addEventListener( 'onComplete', function(client, text) {
      window.location = $('#fb-link').data('link');
    });
    share.clip.addEventListener( 'onMouseDown', function(client) {
      // set text to copy here
      share.clip.setText( $('.redirect #story-message').val() );

    } );
    
    //For preselecting the textarea. Whether we choose to re-include or not.
    /*var inputSelect = document.getElementById('story-message');
    inputSelect.select();*/
    
    share.clip.setHandCursor( true );
    share.clip.setCSSEffects( false );
    share.clip.glue('redirect-button');
  
  }

}
