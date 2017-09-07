
var loader = {

  on: false,
  sObj: false,
  timeout: false,

  create: function(content, params) {

    // set and stack parameters w/ defaults
    var p = {timeout: 3000};
    if (params) {
      for (var param in params) {
        p[param] = params[param];
      }
    } else {
      params = p
    }

    if (!this.sObj) {
      this.sObj = $('.loader');
    }

    if (!this.on) {
      this.display();
      this.on = true;
    }

    if ($.isArray(content)) {
      for (var i = 0, len = content.length; i != len; ++i) {
        loader.create(content[i]);
      }
    } else {
      this.sObj.find('ul').html('<li>' + content + '</li>');
    }

    if (p.progress) {

      $('.meter').show();
      $('.meter').removeClass('red').removeClass('orange');

      if (p.color) {
        $('.meter').addClass(p.color);
      }

      $('.meter span').css({width: p.progress + '%'});
    } else {
      $('.meter').hide();
    }

    br.center(this.sObj, {noTop: true});

    if (p.timeout) {

      // reset if already counting down
      if (this.timeout) {
        clearTimeout(this.timeout);
        this.timeout = false;
      }

      this.timeout = setTimeout(function() { loader.destroy(); }, params.timeout); 

    }

  },

  display: function() {
    this.sObj.slideDown(20);
  },

  destroy: function() {

    this.sObj.fadeOut(400, function() {
      $('.meter').hide();
      loader.sObj.find('ul').html('');
      loader.on = false;
      loader.timeout = false;
      $(this).unbind('scroll');
    });

  }

}

