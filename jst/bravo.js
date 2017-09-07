var bravo = {

  bravoing: [],

  i: function() {

    bravo.handlers();
  }, 

  handlers: function() {

    $('.bravoSmall, .bravoLarge').hover(bravo.hover, bravo.out);
    $('.bravoSmall, .bravoLarge').click(bravo.click);

  },

  hover: function() {
    if(!$(this).hasClass('no-hover')) {
      if ($(this).hasClass('bravoOn')) {
        bravo.tooltip = tooltip.create($(this), $(this).data('tip-after'),{type: 'comment', pos: 'l'});
      } else{
        bravo.tooltip = tooltip.create($(this), $(this).data('tip-before'),{type: 'comment', pos: 'l'});
      }
    }
  },

  out: function() {
    tooltip.destroy(bravo.tooltip);
  },

  click: function() {
    var datas = $(this).data('datas');

    if (br.inArray(datas, bravo.bravoing)) {
      return true;
    }

    if (!user.loggedin) {
      if ($(this).parent().hasClass('modal-bravo')) { //if this is a modal bravo button we have to close modal
        user.returnToModal = true;
        user.returnToModalUrl = location.href;
        modal.close();
      }
      user.loginRegister();
      return false;
    }

    // bravoing
    $(this).removeClass('bravoOff').addClass('bravoOn');
    bravo.bravo(true, datas, $(this));

  },

  bravo: function(bit, datas, clicked) {

    if (br.inArray(datas, bravo.bravoing)) {
      return false;
    }

    bravo.bravoing.push(datas);
    datas.bit = bit;


    if (bit) {
      loader.create('bravoing "' + datas.title + '"..', {progress: 20, timeout: false});

      var action = 'Bravo Story';
      var label = datas.slug;
      if ($(clicked).parents().hasClass('microguide')) {
        action = 'Bravo Story From Microguide';
        label = $('.microguide').data('slug') + '/' + datas.slug;
      }
      _gaq.push(['_trackEvent', 'Social', action, label]);

    } else {
      loader.create('unbravoing "' + datas.title + '"..', {progress: 20, timeout: false});
    }

    $.get('/bravo/bravo', {data: JSON.stringify(datas)}, function(responsejson) {

      var response = $.parseJSON(responsejson);
      
      if (response.success == false) {
        loader.create(response.errors[0]);
        bravo.bravoing.splice(bravo.bravoing.indexOf(datas), 1);
        return false;
      }
      
      // We need slightly different cases depending on the context of where a story is being
      // bravoed.
      if(clicked.hasClass('bravoLarge') && clicked.hasClass('story')) 
        bravoNode = $('.story .bravo_count');
      else if(clicked.hasClass('bravoLarge') && clicked.hasClass('modal-microguide'))
        bravoNode = $('.modal-microguide .bravo_count');
      else
        bravoNode = clicked.siblings().children('.bravo-count-middle').children('span');
      
      bravoCount = parseInt(bravoNode.text());
      
      if(bravoCount == 0)
        bravoNode.parent().parent().removeClass('hidden').addClass('visible');

      if (bit) {
        loader.create('bravoed "' + datas.title + '"', {progress: 100});
        bravoCount++;
        bravoNode.text(bravoCount);
      } else {
        loader.create('unbravoed "' + datas.title + '"', {progress: 100});
        bravoCount--;
        bravoNode.text(bravoCount);
      }

      bravo.bravoing.splice(bravo.bravoing.indexOf(datas), 1);

    });

  },
  
  shareOnFacebook: function(slug) {

    var params = {
        'slug': slug
    }

    loader.create("sharing...", {progress: 20, timeout: false});
    
    $.post('/user/fbShare', params, function(response) {

      if(response.success) {
        loader.create("shared on facebook", {progress: 100, timeout: 1000});

      } else {
        if(response.loggedIn !== undefined && !response.loggedIn) {
          loader.create("please register with facebook", {progress: 100, timeout: 1000});

        } else {
          loader.create("error attempting to share on facebook", {progress: 100, timeout: 1000});

        }
      }
    }, 'json');

  },

  d: function() {

    $('.bravoSmall, .bravoLarge').unbind('hover', bravo.hover);
    $('.bravoSmall, .bravoLarge').unbind('click', bravo.click);

  }

}
