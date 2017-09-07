var search = {

    toggle: {searching: false},
    helpText: 'Tags, locations, text, people',
    interval: false,
    preload: false,
    stories: false,
    attachState: 'attached',
    filters: {keywords: [], tags: [], authors: [], bodytext: [], microguides: [], query: []},
    i: function() {

      $('.search_input').val(search.helpText);
      this.readhash();
      this.readImpliedHash();
      this.handlers();
      if (search.preload == true) {
        search.load();
      }

    },
    
    handlers: function() {
      search.openHandlers();
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

    readhash: function() {
      
      if (location.hash != '' && location.hash != '#') {

        var sections = location.hash.replace(/%20/g, ' ').substr(1).split('&');

        for (var i in sections) {

          for (var j in search.filters) {
            if (sections[i].indexOf(j + '=') !== -1) {
              search.preload = true;
              search.filters[j] = sections[i].split('=')[1].split(',');
            }
          }

        }

      }

    },

    readImpliedHash: function() {
      var authorRegex = /\/story\/bio\/([^\/]+)$/;
      var matches = authorRegex.exec(location.pathname);
      if (matches) {
        search.filters['authors'] = [matches[1]];
      }
    },

    hash: function() {

      var hash = [];

      for (var i in search.filters) {

        if (search.filters[i].length > 0) {
          hash.push(i + '=' + search.filters[i].join(','));
        }

      }

      if (hash.length < 1) {
        display.type = ['story'];
        search.stories = false;
        return '';
      } else {
        display.type = ['story', 'microguide'];
        search.stories = true;
      }

      return hash.join('&');

    },

    updateHash: function() {

      if (search.hash() == false) {
        location.hash = '';
      } else {
        location.hash = '#' + search.hash();
      }

    },



    openHandlers: function() {

      $('.search_input').keyup(search.search);
      $('.search_input').click(search.clear);
      $('.search_go').click(search.search);
      if(location.pathname === '/'){
        $(window).scroll(search.detach);
      }
      $('.tag_container .global_close').click(search.closeAll);
      $('.popular_on a').click(function(){
        if($(this).attr("class") === "authors"){
          search.oneSearch($(this).attr('id'), "authors");
        }else{
          search.oneSearch($(this).text(), "query");
        }
      });      
      $('.menu-places-search li').on('click', function() {
        var query = $(this).data('query');
        if (!query) return;
        search.multiSearch(query);
      });

    },

    clear: function() {
      if($('.search_input').val() === search.helpText) {
        $('.search_input').val('');
      }
    },

    content: function() {

      if (search.filters.keywords.length > 0 ||
          search.filters.tags.length > 0 ||
          search.filters.authors.length > 0 ||
          search.filters.bodytext.length > 0 ||
          search.filters.query.length > 0 ) {
        return true;
      }

      return false;
    },

    load: function() {

      //search.openHandlers();
      for (var i in search.filters.keywords) {
        search.addTag(search.filters.keywords[i], 'keywords');
      }
      for (var i in search.filters.tags) {
        search.addTag(search.filters.tags[i], 'tags');
      }
      for (var i in search.filters.authors) {
        search.addTag(search.filters.authors[i], 'authors');
      }
      for (var i in search.filters.bodytext) {
        search.addTag(search.filters.bodytext[i], 'bodytext');
      }
      for (var i in search.filters.query) {
        search.addTag(search.filters.query[i], 'query');
      } 
      search.transparent();
      search.updateClose();
    },


    detach: function() {

      if ($('.global_close').css('display') == "none") { return; } //hack disables detach for mobile browsers

      if ($(window).scrollTop() >= 30 && search.content() == true) {

        if (search.attachState == 'detached') return;

        search.attachState = 'detached';

        $('.search_wrapper').css({position: 'fixed', width: '25.5em', top: 0, 'box-shadow': '1px 1px 2px 2px rgba(0, 0, 0, 0.2)','background-color': 'rgba(242, 242, 242, 0.8)'});
     
      } 

      if (
          $(window).scrollTop() < 30 ) {

          if (search.attachState == 'attached') return;

          search.attachState = 'attached';

          $('.search_wrapper').css({position: '', width: '', top: '', 'box-shadow': '','background-color': 'transparent'});

      }
      search.transparent();

    },

    search: function(event) {

      if (event.keyCode == 13 || event.type == 'click') {

        var param = $.trim($('.search_input').val());
        var type = "query";

        if (param == '') {
          return false;
        }

        if (br.inArray(param, search.filters[type])) {
          loader.create('you are already searching for that');
          return true;
        }


        search.filters[type].push(param);
        search.addTag(param, type);
        search.transparent();
        search.updateClose();
        
        /* Do you want to reload the page? 
         * Necessary for when you're on a story page or a microguide TOC page
         */
        if (display.needsRedirect) {      
          location.href= '/#' + search.hash();
          return true;
        }

        search.updateHash();

        $('.search_input').val('');
        display.load(true, search.filters);
        $(window).trigger('search');

      }

    },
    oneSearch: function(param, type){
      param = $.trim(param);
      if ($.inArray(param, search.filters[type]) != -1) {
        loader.create('you are already searching for that');
        return true;
      }
      search.filters[type].push(param);
      search.addTag(param, type);
      search.transparent();
      search.updateClose();
      
      /* Do you want to reload the page? 
       * Necessary for when you're on a story page or a microguide TOC page
       */
      if (display.microguidePage || display.storyPage) {      
        location.href= '/#' + search.hash();
        return true;
      }
      
      search.updateHash();
      //$('.search_input').val('');
      display.load(true, search.filters);
      $(window).trigger('search');
    },

    multiSearch: function(searches) {
      var i, param, type, queryList;
      search.closeAllWithoutReload();
      if (typeof searches[0] == "string") {
        queryList = [searches];
      } else {
        queryList = searches;
      }
      for (i = 0; i < queryList.length; i++) {
        param = queryList[i][0];
        type = queryList[i][1];
        switch (type) {
        case 'sort':
          display.sort = param;
          break;
        default:
          search.filters[type].push(param);
          search.addTag(param, type);
        }
      }
      search.transparent();
      search.updateClose();
      
      /* Do you want to reload the page? 
       * Necessary for when you're on a story page or a microguide TOC page
       */
      if (display.needsRedirect) {      
        location.href= '/#' + search.hash();
        return true;
      }
      
      search.updateHash();
      display.load(true, search.filters);
      $(window).trigger('search');
    },

    addTag: function(tag, type) {
      
      var encoded = search.htmlEscape(tag);
      $('.tag_container').append('<div class="tag ' + type + '"><div class="tag_close"></div><p class = "tag_text" data-type=' + type + '>' + encoded + '</p></div>');
      $('.tag_container .tag').show();
      $('.tag_container .tag_close').unbind('click');
      $('.tag_container .tag_close').click(search.tagClose);
      
    },

    closeAllWithoutReload: function() {
      var type;
      $('.tag').remove();
      for (type in search.filters) {
        if (search.filters.hasOwnProperty(type)) {
          search.filters[type] = [];
        }
      }
    },

    closeAll: function(){
      $('.tag_container .tag_close').each(function(index){
        $(this).click();
      });
      $(window).trigger('nosearch');
    },
    tagClose: function() {

      var el = $(this).parent();
      var tag = el.find('p').text();
      var type = el.find('p').data('type');
      search.filters[type].splice(search.filters[type].indexOf(tag), 1);
      el.remove(); 

      if (search.stories == false) {
        location.href= '/#' + search.hash();
        location.reload();
      }

      search.updateHash();
      display.load(true, search.filters);
      search.transparent();
      search.updateClose();
      if (!search.content()) {
        $(window).trigger('nosearch');
      }
    },

    d: function() {
      clearInterval(search.interval);
    },
    transparent: function() {
      return;
      if($('.tag_container').height() > 90 && $(window).scrollTop() < 30){
        $('.search_wrapper').css({'box-shadow': '1px 1px 2px 2px rgba(0, 0, 0, 0.2)','background-color': 'rgba(242, 242, 242, 0.8)'});
      }
      else if($('.tag_container').height() <= 90  && $(window).scrollTop() <= 30){
        $('.search_wrapper').css({'box-shadow': '','background-color': 'transparent'});
      }

    },
    updateClose: function(){
      if(search.content()){
        $('.tag_container .global_close').css({'visibility':'visible'});
      }
      else{
        $('.tag_container .global_close').css({'visibility':'hidden'});
      }
    },

    htmlEscape: function (str) {
      return String(str)
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
    }

}

