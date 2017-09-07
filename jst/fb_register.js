
var fb_register = {

  tooltips: [],
  loader: {},

  i: function() {
    this.handlers();
  },

  handlers: function() {

    br.inputfocus($('.fb-register .input-text, .fb-input-wrapper .input-text'));
    br.inputEnter($('.fb-register .input-text, .fb-input-wrapper .input-text'), fb_register.submit);

    $('.fb-register .input-text, .fb-input-wrapper .input-text').focus(fb_register.help.focus);
    $('.fb-register .input-text, .fb-input-wrapper .input-text').blur(fb_register.help.blur);
    $('.fb-register-button').click(fb_register.submit);

    $('.fb-input-wrapper .input-text').focus(function() {
      $('.fb_submit_link_accounts').removeClass('inactive');
    });
  },

  help: {
    tooltip: {},

    focus: function(obj) {
     fb_register.help.tooltip = tooltip.create($(this), $(this).data('help'), {type: 'comment', pos:'bl'});
    },
    blur: function(obj) {
      tooltip.destroy(fb_register.help.tooltip);
    }
  },

  submit: function(obj) {

    var params = {
      redirect: br.getParam('redirect'),
      code: $('#fb_code').val()
    };


    if ($(obj).hasClass('fb-connecting') || $(this).hasClass('fb-connecting')) {
      var controller = 'connect';
      params.connecting = 1;
      params.useremail = $('#fb_useremail').val();
      params.password = $('#fb_password').val();
    } else {
      var controller = 'register';
      params.connecting = 0;
      params.username = $('#fb_username').val();
      params.email = $('#fb_email').val();
    }

    tooltip.destroy(fb_register.tooltips);

    fb_register.loader = loader.create('submitting information..', {timeout: 0});

    $.get('/user/facebook_json_' + controller, params, function(response) {

      if (!response.success) {
        for (var i in response.errors) {
          fb_register.tooltips.push(
            tooltip.create($('#' + i), response.errors[i], {type: 'error', pos: 'r'})
          );
          loader.destroy(fb_register.loader);

        }
        _gaq.push(['_trackEvent', 'Social', 'Facebook Connect', 'Error']);
  
      } else {
        if (controller == 'connect') {
          loader.create('connection successful..', {timeout: 0});
        } else {
          loader.create('registration successful..', {timeout: 0});
        }
        location.href = document.referrer ? document.referrer : br.G_URL;

        _gaq.push(['_trackEvent', 'Social', 'Facebook Connect', 'Success']);
      }

    }, 'json');

  }

}
