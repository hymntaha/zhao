
var menu = {

  obj: false,
  options: false,
  mObj: false,
  p: {d: false},

  i: function(obj, options, params) {


    menu.obj = obj;
    menu.options = options;

    // set and stack parameters w/ defaults
    if (params) {
      for (var param in params) {
        menu.p[param] = params[param];
      }
    } 

    if (menu.mObj == false) {
      menu.mObj = $('.menu');
    }

    menu.populate();
    menu.handlers();
    menu.position();

  },

  populate: function() {

    var ul = menu.mObj.find('ul');
    ul.html('');

    for (var i in menu.options) {
      if (i != '-') {
        ul.append('<li class="menu-option">' + i + '</li>');
      } else {
        ul.append('<div class="menu-separator"></div>');
      }
    }

  },

  handlers: function() {

    menu.mObj.find('li').hover(menu.hover);
    menu.mObj.find('li').click(menu.click);

    setTimeout(function() { $('html').click(menu.d); }, 100);

    menu.mObj.click(function(event) {
      event.stopPropagation(); 
    });

  },

  hover: function() {
    $(this).toggleClass('menu-hover');
  },

  click: function() {
    $(this).removeClass('menu-hover').addClass('menu-active');
    menu.options[$(this).html()](this);
    setTimeout(menu.d, 200);
  },

  position: function() {

    var pos = menu.obj.offset();

    menu.mObj.css({
      top: (pos.top + menu.obj.outerHeight()) + 'px', 
      left: (pos.left - ( menu.mObj.outerWidth() - menu.obj.outerWidth() ) ) + 'px'
    });

    menu.mObj.show();

  },

  d: function() {

    $('html').unbind('click', menu.d);

    menu.mObj.unbind();
   
   if (menu.p.d) {
     menu.p.d();
   }

    menu.mObj.find('li').unbind('hover', menu.hover);
    menu.mObj.hide();

  }

}
