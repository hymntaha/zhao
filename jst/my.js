var my = {

  i: function(statuses) {
    my.handlers();
  },

  handlers: function() {
    $('#my-selector').on('change', function() {
      document.location.href = '/my/' + $(this).val();
    });      
  },

  account : {

    i: function() {

      my.account.handlers();

    },

    handlers: function() {

      br.inputfocus($('.share .input-text'));

      $('.button.submit').on('click', my.account.submit);

      $('.button.cancel').on('click', my.account.returnToReferrer);

    },

    submit: function() {

      var data = {
        username: $('#username').val(),
        email: $('#email').val()
      };

      $.post('/my/accountUpdate', data, function(response){
        if (response.success) {
          loader.create("Account updated");
          setTimeout(my.account.returnToReferrer, 2000);
        } else {

          for (var i in response.errors) {
            if (response.errors.hasOwnProperty(i)) {
              share.tooltips.push(
                tooltip.create($('#' + i), response.errors[i], {type: 'error', pos: 'r'})
              );
            }
          }
        }
      }, 'json');

    },

    returnToReferrer: function() {
      if (document.referrer) {
        location.href = document.referrer;
      } else {
        location.href = '/';
      }

    }
  },

  stories : {

    scrollStatus: 'accepted',
    loading: false,
    infiniteScroll: false,
    appending: false,
    nomore: false,
    pageSize: { 'draft' : -1, 'pending' : -1, 'rejected' : -1, 'accepted' : 12 },
    pageOffset: { 'draft' : 0, 'pending' : 0, 'rejected' : 0, 'accepted' : 0 },

    i: function(statuses) {
      my.i();
      my.stories.handlers();
      $(window).load(function() {
        my.stories.loadStatuses(statuses);
      });
    
    },

    handlers: function() {

      br.addScrollToTop('.submissions');

      if (my.stories.infiniteScroll) {
        $(window).scroll(my.stories.scrollAppend);
      }

      $('.navlink-draft').on('click', function(e) {
        br.scrollTo($('.draft-stories-container').offset().top);
        e.preventDefault()
      });

      $('.navlink-pending').on('click', function(e) {
        br.scrollTo($('.pending-stories-container').offset().top);
        e.preventDefault()
      });

      $('.navlink-rejected').on('click', function(e) {
        br.scrollTo($('.rejected-stories-container').offset().top);
        e.preventDefault()
      });

      $('.navlink-accepted').on('click', function(e) {
        br.scrollTo($('.accepted-stories-container').offset().top);
        e.preventDefault()
      });

    },
  
    loadStatuses: function(statuses) {

      if (my.stories.loading) {
        return true;
      }

      my.stories.loading = true;

      $.get('/index/submissions', {
        statuses: statuses,
        ajax: 1,
        offset: my.stories.pageOffset,
        limit: my.stories.pageSize,
        columns: display.countColumns(),
        filters: search.filters
      }, function(response) {

        for (var i = 0; i < statuses.length; i++) {
          $('.' + statuses[i] + '-stories').html(response.stories[statuses[i]].html);
          if (response.stories[statuses[i]].countReturned > 0) {
            $('.no-' + statuses[i] + '-stories').hide();
          } else {
            $('.no-' + statuses[i] + '-stories').show();
          }
          $('.' + statuses[i] + '-stories-container').show();

          var storyLabel = response.stories[statuses[i]].countTotal == 1 ? 'story' : 'stories';
          $('.submissions-stats .number-' + statuses[i]).html(response.stories[statuses[i]].countTotal + ' ' + storyLabel);
        }
        my.stories.selectHandlers();
        $('.submissions-header').show();
        my.stories.loading = false;
      }, 'json');
    },

    appendStatus: function() {

      if (my.stories.appending) {
        return true;
      }

      if (my.stories.nomore) {
        loader.create('no more available stories');
        return false;
      }

      // CREATE LOADER
      // CREATE GUI LOADING FEEDBACK

      my.stories.appending = true;
      my.stories.pageOffset[my.stories.scrollStatus]++;

      $.get('/index/submissions', {
        statuses: [my.stories.scrollStatus],
        ajax: 1,
        append: 1,
        offset: my.stories.pageOffset,
        limit: my.stories.pageSize,
        columns: display.countColumns(),
        filters: search.filters
      }, function(response) {

        // detect when there are no more left

        if (response.stories[my.stories.scrollStatus]['countReturned'] == 0) {
          loader.create('no more available stories');
          my.stories.nomore = true;
          return false;
        }

        // build html

        for (var i in response.stories[my.stories.scrollStatus]['html']) {
          var html = $('.' + my.stories.scrollStatus + '-stories .column_' + i).html();
          if (html != null) {
            $('.' + my.stories.scrollStatus + '-stories .column_' + i).html(html.replace(br.spinner, ''));
          }
          $('.' + my.stories.scrollStatus + '-stories .column_' + i).append(response.stories[my.stories.scrollStatus]['html'][i]);
        }

        // turn on click handlers

        my.stories.appending = false;
        my.stories.selectHandlers();

      },'json');

    },

    selectHandlers: function() {

      $('.stories .thumbnail, .stories .title').off('click', my.stories.select);
      $('.stories .thumbnail, .stories .title').on('click', my.stories.select);

      $('.stories .delete').off('click', my.stories.deleteConfirm);
      $('.stories .delete').on('click', my.stories.deleteConfirm);

    },

    select: function() {

      loader.create('loading...');

      var linkBase = '/share/';

      location.href = '/share/' + $(this).data('slug');

    },

    _delete: function(storyId, refreshStatus) {

      if (my.stories.deleting) {
        return false;
      }

      my.stories.deleting = true;

      $.ajax({
        url: '/share/' + storyId,
        type: 'DELETE'
      }).done(function(d) {
        my.stories.deleting = false;
        var result = JSON.parse(d);
        if (!result.success) {
          alert(result.errors.join("\n"));
        } else {
          loader.create("Story deleted");
          my.stories.loadStatuses([refreshStatus]);
        }
      });

    },

    deleteConfirm: function() {
      if(confirm('Really delete this story? This cannot be undone.')) {
        my.stories._delete($(this).data('story-id'), $(this).data('story-status'));
      }
    },

    scrollAppend: function() {

      var scroll = $(window).scrollTop() + $(window).height();
      var offset = $(document).height() - scroll;

      if (offset < 500) {
        my.stories.appendStatus();
      }

    }

  },

  microguides : {

    scrollStatus: 'accepted',
    loading: false,
    infiniteScroll: false,
    appending: false,
    columns: 3,
    nomore: false,
    pageSize: { 'draft' : 100, 'pending' : 100, 'rejected' : 100, 'accepted' : 100 },
    pageOffset: { 'draft' : 0, 'pending' : 0, 'rejected' : 0, 'accepted' : 0 },

    i: function(statuses) {
      my.i();
      my.microguides.handlers();
      $(window).load(function() {
        my.microguides.loadStatuses(statuses);
      });
    
    },

    handlers: function() {

      br.addScrollToTop('.submissions');

      if (my.microguides.infiniteScroll) {
        $(window).scroll(my.microguides.scrollAppend);
      }

      $('.navlink-draft').on('click', function(e) {
        br.scrollTo($('.draft-microguides-container').offset().top);
        e.preventDefault()
      });

      $('.navlink-pending').on('click', function(e) {
        br.scrollTo($('.pending-microguides-container').offset().top);
        e.preventDefault()
      });

      $('.navlink-rejected').on('click', function(e) {
        br.scrollTo($('.rejected-microguides-container').offset().top);
        e.preventDefault()
      });

      $('.navlink-accepted').on('click', function(e) {
        br.scrollTo($('.accepted-microguides-container').offset().top);
        e.preventDefault()
      });

    },
  
    loadStatuses: function(statuses) {

      if (my.microguides.loading) {
        return true;
      }

      my.microguides.loading = true;

      $.get('/index/mymicroguides', {
        statuses: statuses,
        ajax: 1,
        offset: my.microguides.pageOffset,
        limit: my.microguides.pageSize,
        columns: my.microguides.columns,
        filters: search.filters
      }, function(response) {

        for (var i = 0; i < statuses.length; i++) {
          $('.' + statuses[i] + '-microguides').html(response.microguides[statuses[i]].html);
          if (response.microguides[statuses[i]].countReturned > 0) {
            $('.no-' + statuses[i] + '-microguides').hide();
          } else {
            $('.no-' + statuses[i] + '-microguides').show();
          }
          $('.' + statuses[i] + '-microguides-container').show();

          var storyLabel = response.microguides[statuses[i]].countTotal == 1 ? 'microguide' : 'microguides';
          $('.submissions-stats .number-' + statuses[i]).html(response.microguides[statuses[i]].countTotal + ' ' + storyLabel);
        }
        my.microguides.selectHandlers();
        $('.submissions-header').show();
        my.microguides.loading = false;
      }, 'json');
    },

    appendStatus: function() {

      return;

      if (my.microguides.appending) {
        return true;
      }

      if (my.microguides.nomore) {
        loader.create('no more available microguides');
        return false;
      }

      // CREATE LOADER
      // CREATE GUI LOADING FEEDBACK

      my.microguides.appending = true;
      my.microguides.pageOffset[my.microguides.scrollStatus]++;

      $.get('/index/mymicroguides', {
        statuses: [my.microguides.scrollStatus],
        ajax: 1,
        append: 1,
        offset: my.microguides.pageOffset,
        limit: my.microguides.pageSize,
        columns: stories.countColumns(),
        filters: search.filters
      }, function(response) {

        // detect when there are no more left

        if (response.stories[my.microguides.scrollStatus]['countReturned'] == 0) {
          loader.create('no more available stories');
          my.microguides.nomore = true;
          return false;
        }

        // build html

        for (var i in response.stories[my.microguides.scrollStatus]['html']) {
          var html = $('.' + my.microguides.scrollStatus + '-stories .column_' + i).html();
          if (html != null) {
            $('.' + my.microguides.scrollStatus + '-stories .column_' + i).html(html.replace(br.spinner, ''));
          }
          $('.' + my.microguides.scrollStatus + '-stories .column_' + i).append(response.stories[my.microguides.scrollStatus]['html'][i]);
        }

        // turn on click handlers

        my.microguides.appending = false;
        my.microguides.selectHandlers();

      },'json');

    },

    selectHandlers: function() {

      $('.microguides .img-container, .microguides .microguide-title').off('click', my.microguides.select);
      $('.microguides .img-container, .microguides .microguide-title').on('click', my.microguides.select);

      $('.microguides .delete').off('click', my.microguides.deleteConfirm);
      $('.microguides .delete').on('click', my.microguides.deleteConfirm);

    },

    select: function() {

      loader.create('loading...');

      location.href = '/microguide/create/' + $(this).data('slug');

    },

    _delete: function(microguideId, refreshStatus) {

      if (my.microguides.deleting) {
        return false;
      }

      my.microguides.deleting = true;

      $.ajax({
        url: '/microguide/delete?microguideId=' + microguideId
      }).done(function(d) {
        my.microguides.deleting = false;
        var result = JSON.parse(d);
        if (!result.success) {
          alert(result.errors.join("\n"));
        } else {
          loader.create("Microguide deleted");
          my.microguides.loadStatuses([refreshStatus]);
        }
      });

    },

    deleteConfirm: function() {
      if(confirm('Really delete this microguide? This cannot be undone.')) {
        my.microguides._delete($(this).data('microguide-id'), $(this).data('microguide-status'));
      }
    },

    scrollAppend: function() {

      var scroll = $(window).scrollTop() + $(window).height();
      var offset = $(document).height() - scroll;

      if (offset < 500) {
        my.microguides.appendStatus();
      }

    }

  }

};
