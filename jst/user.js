
var user = {

  tooltips: [],
  loader: {},
  loggedin: false,
  returnToModal: false,

  i: function(loggedin) {
    this.loggedin = loggedin;
    this.handlers();
  },

  handlers: function(loggedin) {

    if (this.loggedin) {

      $('.user-logout').click(user.logout);
      user.bar.i();

    } else {

      $('.user-sign-in').click(user.loginRegister);

    }

    if (br.isMobileLayout()) {
      $('.auth-toggle-login').on('click', function() {
        $('.modal .register').hide();
        $('.modal .login').show();
        return false;
      });
      $('.auth-toggle-register').on('click', function() {
        $('.modal .register').show();
        $('.modal .login').hide();
        return false;
      });
    }

    $('.auth-step-2').on('click', function() { user.showAuthStep(2); });
    $('.auth-step-3').on('click', function() { user.showAuthStep(3); });
  },

  logout: function() {

    loader.create('logging out..');
    $.get('/user/logout', {}, function() {
      location.href = location.href.substr(0, location.href.indexOf('#'));
    });

  },

  loginRegister: function(login) {
    br.overlay();
    user.center();
    $('.modal-auth').show();
    user.showAuthStep(1);

    if (!br.isMobileLayout()) {
      $('.modal-auth').css({top: '-500px'}).transition({top: '50px'});
    } else {
      $('.modal .register').hide();
      $('.modal .login').show();
      $('.modal-auth .auth-toggle').show();
    }

    br.inputfocus($('.modal-auth .input-text'));

    $('.modal-auth .input-text').focus(user.help.focus);
    $('.modal-auth .input-text').blur(user.help.blur);

//    setTimeout(function() { $('#login_username').focus(); }, 500);

    br.inputEnter($('.modal-auth .input-text'), function(obj) {

      if ($(obj).attr('id').match(/register/)) {
        user.submit.register();
      } else {
        user.submit.login();
      }
      
    });

    $(window).resize(user.center);
    $('.modal-close').click(user.close);
    $('.button-register').click(user.submit.register);
    $('.button-login').click(user.submit.login);

  },

  showAuthStep: function(n) {
    $('.error-messages').html('').css({visibility: 'hidden'});
    $('.modal-auth .modal-inner').hide();
    $('.modal-auth .modal-inner-'+n).show();
  },

  close: function() {
    user.d();
  },

  center: function() {
    br.center($('.modal-auth'), {noTop: br.isMobileLayout()});
  },

  submit: {

    register: function() {

      var params = {
        username: $('#register_username').val(),
        email: $('#register_email').val(),
        password: $('#register_password').val(),
        verify: $('#register_verify').val()
      };

      $('.modal-inner-3 .error-messages').html('').css({visibility: 'hidden'});
      user.loader = loader.create('submitting information..', {timeout: 0});

      $.post('/user/register', params, function(response) {

        if (!response.success) {
          var messages = '';
          for (var i in response.errors) {
            if (response.errors.hasOwnProperty(i)) {
              messages = messages + '<div>' + response.errors[i] + '</div>';
              loader.destroy(user.loader);
	    }
          }
          $('.modal-inner-3 .error-messages').html(messages).css({visibility:'visible'});
        } else {
          loader.create('registration successful..');
          location.href = location.href.substr(0, location.href.indexOf('#'));
        }


      }, 'json');


    },

    login: function() {

      var params = {
        username: $('#login_username').val(),
        password: $('#login_password').val(),
        remember: $('#login_remember').attr('checked')
      };

      $('.modal-inner-2 .error-messages').html('').css({visibility: 'hidden'});
      user.loader = loader.create('submitting information..', {timeout: 0});

      $.post('/user/login', params, function(response) {

        if (!response.success) {
          var messages = '';
          for (var i in response.errors) {
            if (response.errors.hasOwnProperty(i)) {
              messages = messages + '<div>' + response.errors[i] + '</div>';
              loader.destroy(user.loader);
            }
          }
          $('.modal-inner-2 .error-messages').html(messages).css({visibility:'visible'});
        } else {
          loader.create('login successful..');
          if (user.returnToModal) {
            location.href = user.returnToModalUrl; 
            window.reload();
            user.returnToModal = false;
            user.returnToModalUrl = null;
          } else {
            location.href = location.href.substr(0, location.href.indexOf('#'));
          }
        }

      }, 'json');

    }

  },

  help: {
    tooltip: {},

    focus: function(obj) {
     user.help.tooltip = tooltip.create($(this), $(this).data('help'), {type: 'comment', pos:'bl'});
    },

    blur: function(obj) {
      tooltip.destroy(user.help.tooltip);
    }

  },

  bar: {

    loading: false,
    deleteing: false,

    i: function() {
      user.bar.handlers();
    },

    handlers: function() {
      $('.profilebar-control').click(user.bar.control);
    },

    loadhandlers: function() {
      $('.profilebar .view').hover(user.bar.hover);
      $('.profilebar .delete').click(user.bar.del);
    },

    d: function() {
      $('.profilebar .view').unbind('hover');
      $('.profilebar .delete').unbind('click');
    },

    hover: function() {

      $('.hidden').hide();
      $(this).next('.hidden').show().css({top: ($(this).position().top+30) + 'px'});

    },

    del: function() {

      loader.create('delete is coming soon');
      return true;

      if (user.bar.deleteing) {
        return false;
      }

      user.bar.deleteing = true;

      loader.create('deleteing story..', {timeout: false});

      $.get('/story/delete', {id: $(this).data('id')}, function(response) {

        user.bar.deleteing = false;

      });

    },

    control: function() {

      if ($('.profilebar').css('opacity') == 1) {
        user.bar.close($(this));
      } else {
        user.bar.open($(this));
        user.bar.load();
      }

    },

    open: function(obj) {
      $('.profilebar').transition({opacity: 1});
      obj.find('.arrow').removeClass('arrow-up-css').addClass('arrow-down-css');
      $(window).scroll(user.bar.detach);
    },

    close: function(obj) {

      $('.profilebar').transition({opacity: 0}, function() { 
        $('.profilebar').html(br.spinner);
        user.bar.d();
      });

      obj.find('.arrow').removeClass('arrow-down-css').addClass('arrow-up-css');
      $(window).unbind('scroll', user.bar.detach);
    },

    detach: function() {

      if ($(window).scrollTop() >= $('.profilebar').position().top) {
        if ($('.profilebar').css('position') != 'fixed') {
          $('.profilebar').css({position: 'fixed', top: '0px', margin: '0px'});
        }
      } 

      if (
          $(window).scrollTop() < 128 && 
          $('.profilebar').css('position') == 'fixed') {

          $('.profilebar').css({position: 'absolute', top: '', margin: '18px 0 0 0'});

      }

    },

    load: function() {

      if (user.bar.loading) {
        return true;
      }

      user.bar.loading = true;

      $.get('/user/profilebar', {}, function(response) {

        if (response.success) {
          $('.profilebar').html(response.html);
          user.bar.loadhandlers();

        } else {

          if (response.error == 'not logged in') {
            user.loginRegister(true);
            user.bar.close();
          } else {
            loader.create('error loading profile menu');
          }
 
        }
        user.bar.loading = false;

      }, 'json');

    }

  },

  d: function() {

    if (!user.returnToModal)
    br.overlay(true);

    if (br.isMobileLayout()) {
      $('.modal-auth').hide();
    } else {
      $('.modal-auth').show().transition({top: '-500px'}, function() { $('.modal-auth').hide(); });
    }

    $(window).unbind('resize', user.center);
    $('.modal-close').unbind('click', user.close);

    tooltip.destroy(user.tooltips);

    if (user.returnToModal) {
      modal.openMicroguideModal(modal.storyIndex);
      br.overlay();
      user.returnToModal = false;
    }

    return true;

  }

}
