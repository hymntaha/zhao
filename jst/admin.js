var admin = {

  microguideEdit : {

    tooltips : [],

    i: function() {
      this.handlers();
      this.sortable();
    },

    handlers: function() {

      this.authorHandler();
      this.addStoryHandler();
      this.removeStoryHandler();
      this.viewMicroguideHandler();

      $("#save").on("click", this.saveMicroguide);
      $("#delete").on("click", this.deleteMicroguide);

    },

    authorHandler: function() {

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

    addStoryHandler: function() {

      var exclude = null;

      $( "#new-story" ).autocomplete({
        source: function( request, response ) {
          var term = request.term;
          if (exclude === null) {
            exclude = admin.microguideEdit.storyIds();
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
          $("#new-story").data('storyid', ui.item.storyId);
          $("#new-story").data('slug', ui.item.slug);

          setTimeout(function() {
            var item = $('<li>').html('<a class="remove-story">[x]</a> <a style="color: black;" target="_blank" href="/story/' + ui.item.slug + '">' + $('#new-story').val() + '</a>').data('storyid', ui.item.storyId);
            exclude = null;
            $('#story-list').append(item);
            $('#new-story').val('');
            $("#new-story").data('storyid','');
            $("#new-story").data('slug','');

            admin.microguideEdit.removeStoryHandler();
          },1);
        }
      });
    },

    clearErrors: function() {
      if (admin.microguideEdit.tooltips.length) {
        tooltip.destroy(admin.microguideEdit.tooltips);
        admin.microguideEdit.tooltips = [];
      }
    },

    removeStoryHandler: function() {
      $(".remove-story").off("click");
      $(".remove-story").on("click", function() {
        $(this).parent().remove();
        admin.microguideEdit.clearErrors();
      });
    },

    viewMicroguideHandler: function() {
      var slug = $("#microguideid").data('slug');
      if (slug) {
        $(".view-microguide a").attr("href", document.location.origin + '/microguide/' + slug);
        $(".view-microguide").show();
      }
    },

    storyIds: function() {
      var ids = [];
      $('#story-list li').each(function(i,el) { 
        if ($(el).data('storyid')) {
          ids.push($(el).data('storyid'));
        }
      });
      return ids;
    },

    saveMicroguide: function() {

      admin.microguideEdit.clearErrors();
      params = {
        microguideId: $("#microguideid").val(),
        title: $("#title").val(),
        slug: $("#slug").val(),
        author: $("#author").val(),
        authorSlug: $("#author").data("slug"),
        storyIds: admin.microguideEdit.storyIds(),
        status: $('input:radio[name=status]:checked').val()
      }

      $.post('/microguide/edit',params, function(response) {
        if (response.success) {

          loader.create('success...');
          if (response.operation == 'created') {
	    $("#microguideid").val(response.id);
	    $("#microguideid").data('slug', response.slug);
	  }
          admin.microguideEdit.viewMicroguideHandler();

        } else {

          var scrolled = false;
          for (var i in response.errors) {

            if (i.charAt(0) == '#') {
              admin.microguideEdit.tooltips.push(
                tooltip.create($(i), response.errors[i], {type: 'error', pos: 'r'})
              );
            } else {
              switch (i) {
              case 'storyIds':
                $success = false;
                $('#story-list li').each(function(ind,el) { 
                  if (response.errors[i].indexOf($(el).data('storyid')) > -1) {
                    admin.microguideEdit.tooltips.push(
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
          loader.create('problem editing microguide.');
	}

      }, 'json');

    },

    deleteMicroguide: function() {

      if(confirm('Really delete this microguide? This cannot be undone.')) {
        admin.microguideEdit._deleteMicroguide();
      }

    },

    _deleteMicroguide: function() {

      admin.microguideEdit.clearErrors();

      params = {
        microguideId: $("#microguideid").val()
      };

      $.post('/microguide/delete', params, function(response) {
        if (response.success) {
          loader.create('success...');
          document.location.href = document.location.origin + '/admin/microguides/';
        } else {

          var scrolled = false;
          for (var i in response.errors) {

            if (i.charAt(0) == '#') {
              admin.microguideEdit.tooltips.push(
                tooltip.create($(i), response.errors[i], {type: 'error', pos: 'r'})
              );
            }

	  }
          loader.create('problem editing microguide.');
	}

      }, 'json');

    },

    sortable: function() {

      $( "#story-list" ).sortable({
        revert: true
      });
      $( "#story-list" ).draggable({
        connectToSortable: "#story-list",
        helper: "clone",
        revert: "invalid"
      });

    }

  },
  
  exportEbook: {
    
    tooltips : [],

    i: function() {
      
      /* Clear the form of any potential old data - especially images */
      $('form').each(function() { this.reset() });
      $(':checkbox').prop('disabled', false);
      
      this.handlers();
    },

    handlers: function() {
  
      $("#real-upload-cover").on("change", admin.exportEbook.uploadImages);
      $("#upload-cover").on("click", function() {
        $("#real-upload-cover").trigger("click");
        $('#real-upload-cover').off('change');
        $('#real-upload-cover').on("change", admin.exportEbook.uploadImages);
      });

      $("#real-upload-front-ad").on("change", admin.exportEbook.uploadImages);
      $("#upload-front-ad").on("click", function() {
        $("#real-upload-front-ad").trigger("click");
        $('#real-upload-front-ad').off('change');
        $('#real-upload-front-ad').on("change", admin.exportEbook.uploadImages);
      });

      $("#real-upload-rear-ad").on("change", admin.exportEbook.uploadImages);
      $("#upload-rear-ad").on("click", function() {
        $("#real-upload-rear-ad").trigger("click");
        $('#real-upload-rear-ad').off('change');
        $('#real-upload-rear-ad').on("change", admin.exportEbook.uploadImages);
      });
      
      $("#slider-compression").slider({
        orientation: "horizontal",
        range: "min",
        min: 0,
        max: 100,
        value: 80,
        slide: function( event, ui ) {
          $("#amount-compression").val(ui.value);
        }
      });
      
      $("#amount-compression").val($("#slider-compression").slider("value"));
      
      $("#amount-compression").on('change', function(evt) {
        var value = $(evt.currentTarget).val();
        if (value < 100 && value > 0) {
          $('#slider-compression').slider('value', value);
        } else {
          $("#amount-compression").val($("#slider-compression").slider("value"));
        }
        
      });
      
      $('.ad-link-type').on('change', function(evt) {
        var value = $(evt.currentTarget).val();
        $(evt.currentTarget).parent().find('.input-links').addClass('hidden');
        $(evt.currentTarget).parent().find('.input-links.' + value + '-link').removeClass('hidden');
        
      });
      
    },
    
    uploadImages: function(evt) {
      
      var target = $(evt.target).data('target');
      
      var file = evt.target.files[0];
      // make sure we're all images of supported types
      if (file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'image/gif') {

        tooltip.create($('.upload_button'), 'invalid image type(s)', 
          {type: 'error', pos: 'tl', timeout: 4000});

        return false;
      }
      
      var reader = new FileReader();
      
      reader.onloadend = function(evt) {

        loader.create('loading images.. ', {progress:  0, color: 'orange', timeout: false});

        if (evt.target.readyState == FileReader.DONE) {
        
          var image = new Image();
          image.src = evt.target.result;
          
          image.onload = function() {

            var width = image.width, height = image.height;

            if (admin.exportEbook.resize(width, height) != false) {

              var tmp = admin.exportEbook.resize(image.width, image.height);
              width = tmp[0], height = tmp[1];

              var canvas = document.createElement('canvas');
              canvas.width = width;
              canvas.height = height;

              var ctx = canvas.getContext("2d");
              ctx.drawImage(this, 0, 0, width, height);

              var data = canvas.toDataURL(file.type);

            } else {

              var data = evt.target.result;

            }
            
            $('.' + target + ' .content').prepend('<img src="' + data + '" title="' + escape(file.name) + '" />');
            
            $('.' + target + ' .content').removeClass('hidden');
            
            $('.input-text').off('focus').off('blur');
            $('.input-text')
                .focus(admin.exportEbook.help.focus)
                .blur(admin.exportEbook.help.blur);
          };
            
          loader.create('load complete', {progress:  100, color: 'orange'});
        }      
      };
      
      reader.readAsDataURL(file);
    
    },
    
    help: {
      tooltip: {},
  
      focus: function(obj) {
  
        share.help.tooltip = tooltip.create($(this), $(this).data('help'), 
          {type: 'comment', pos:'tl'}
        );
  
      },
  
      blur: function(obj) {
        tooltip.destroy(share.help.tooltip);
      }
  
    },
    
    submit: function() {
      if (admin.exportEbook.working) {
        return false;
      }
   
      admin.exportEbook.working = true;
       
      var returnUrl = $('#returnurl').length ? $('#returnurl').val() : '';
      
      var data = {
        submit: submit
      };
      
      data.files = [];
      for (var i = 0; i < order.length; i++) {
        data.files.push(share.img.data[order[i]]);
        data.files[i].data = $('.image_' + order[i] + ' img').attr('src');
        data.files[i].caption= $('#caption_' + order[i]).val();
        if (data.files[i].src) {
          delete data.files[i].src;
        }
      }
      
    },
    
    resize: function(width, height) {
      // We can specify what we want our max height and max width to be

      var maxWidth = 960, maxHeight = 3000;

      if (width <= maxWidth && height <= maxHeight) {
        return false;
      }

      var ratio = Math.min(maxWidth/width, maxHeight/height);

      return [Math.round(ratio*width), Math.round(ratio*height)];

    },
    
    
  }

}
