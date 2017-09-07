var tooltip = {

  tooltips: {},
  params: {},
  id: 1,

  create: function(obj, content, params) {

    // grab our unique id
    var id = tooltip.id++;

    // set and stack parameters w/ defaults
    var p = { type: 'fs', timeout: false, fade: 100, pos: 'tl'};
    if (params) {
      for (var param in params) {
        p[param] = params[param];
      }
    } else {
      params = p;
    }

    tooltip.params[id] = p;

    $('#tpl-tooltip-' + p.type + ' .tooltip').attr('id', 'tooltip-' + id);
    $('#tpl-tooltip-' + p.type + ' .tooltip').attr('tooltip-id', id);

    $('.tooltips').append($('#tpl-tooltip-' + p.type).html());
    $('#tpl-tooltip-' + p.type + ' .tooltip').attr('id', '');

    if ($.isArray(content)) {
      for (var i = 0, len = content.length; i != len; i++) {
        tooltip.addline(id, content[i]);
      }
    } else {
      tooltip.addline(id, content);
    }

    var tooltip_obj = $('#tooltip-' + id);

    tooltip.position(p.pos, tooltip_obj, obj);

    $(tooltip_obj).fadeIn(p.fade);

    if (p.timeout) {
      setTimeout(function() { tooltip.destroy(id); }, p.timeout); 
    }

    return id;
  },

  addline: function(id, line) {
    $('#tooltip-' + id + ' ul').append('<li>' + line + '</li>');
  },

  position: function(pos, tooltip, obj) {

    var xy = obj.offset();

    // topright w/ bottomright pointer
    if (pos == 'tr') {
      tooltip.find(' .tooltip-pointer-br').show();
      var y = xy.top - tooltip.height();
      var x = xy.left - (tooltip.outerWidth() - obj.outerWidth());
    }

    if (pos == 'tl') {
      tooltip.find(' .tooltip-pointer-bl').show();
      var y = xy.top - tooltip.outerHeight();
      var x = xy.left
    }
    if (pos == 'tm') {
      var y = xy.top - tooltip.outerHeight();
      var x = xy.left;
      tooltip.find(' .tooltip-pointer-bl').show().css({'margin-left': (tooltip.width() / 2) + 'px'});
    }

    if (pos == 'l') {
      var y = xy.top - (tooltip.height() / 2) + (obj.outerHeight() / 2);
      var x = xy.left - tooltip.width();
      tooltip.find('.tooltip-pointer-r').show().css({marginTop: tooltip.height() / 2 - 4});
    }

    if (pos == 'r') {
      var y = xy.top - (tooltip.height() / 2) + (obj.outerHeight() / 2);
      var x = xy.left + obj.outerWidth();
      tooltip.find('.tooltip-pointer-l').show().css({marginTop: tooltip.height() / 2 - 4});
    }

    // topright w/ bottomright pointer
    if (pos == 'br') {
      tooltip.find(' .tooltip-pointer-tr').show();
      var y = xy.top + obj.outerHeight() + 4;
      var x = xy.left - (tooltip.outerWidth() - obj.outerWidth());
    }

    if (pos == 'bl') {
      tooltip.find(' .tooltip-pointer-tl').show();
      var y = xy.top + obj.outerHeight() + 4;
      var x = xy.left
    }



    tooltip.css({top: y + 'px', left: x + 'px'});

  },

  destroy: function(id) {

    if (id == undefined) {
      return true;
    }

    if ($.isArray(id)) {
      for (var i = 0, len = id.length; i != len; i++) {
        tooltip.destroy(id[i]);
      }
    } else {


      var p = tooltip.params[id];
      if ($('#tooltip-' + id).length > 0) {
        $('#tooltip-' + id).fadeOut(p.fade, function() { 
          $(this).remove(); 
          $('#tooltip-' + id + '.tooltip-pointer').hide();
        });
      }
    }

    return true;

  }

}

