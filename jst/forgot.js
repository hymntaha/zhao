
var forgot = {

  forgoting: false,
  hash: false,

  i: function() {

    this.handlers();

  },

  handlers: function() {

    $('.forgot_button').click(forgot.submit);
    $('.reset_button').click(forgot.reset);
//    $('.forgot .input-text').keyup(forgot.enter);
    br.inputfocus($('.forgot .input-text'));

  },

  enter: function(event) {

    if (event.keyCode == 13) {
      if (forgot.hash != false) {
        forgot.reset();
      } else {
        forgot.submit();
      }
    }

  },

  submit: function() {

    if (forgot.forgoting) {
      return true;
    }
    
    forgot.forgoting = false;

    loader.create('submiting..');

    $.get('/forgot/submit', {email: $('.forgot .input-text').val()}, function(response) {

      if (response.success == true) {
        loader.create('an e-mail has been sent to you with a link to reset your password', 
          {timeout: 10000});
        setTimeout(function() {
          location.href = document.referrer ? document.referrer : br.G_URL;
        }, 7000);
      } else {
        loader.create(response.error);
      }

    forgot.forgoting = false;

    }, 'json');

  },

  reset: function() {

    if (forgot.forgoting) {
      return true;
    }
      
    forgot.forgoting = false;

    loader.create('submiting..');

    var params = {
      hash: forgot.hash,
      password: $('.input-pass1').val(),
      confirm: $('.input-pass2').val()
    };

    $.get('/forgot/reset', {params: JSON.stringify(params)}, function(response) {

      if (response.success == true) {
        loader.create('your password has been reset');
        setTimeout(function() {
          location.href = br.G_URL;
        }, 5000);
      } else {
        loader.create(response.error);
      }

    forgot.forgoting = false;

    }, 'json');

  }

}
