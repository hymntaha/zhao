var rich = {

  toolbar: {},
  input: {},
  selection: false,

  i: function(toolbar, input) {

    rich.toolbar = toolbar;
    rich.input = input;
    rich.handlers();

  },

  handlers: function() {
    rich.toolbar.find('li').click(rich.choose);
    rich.input.keyup(rich.select.handler);
    rich.input.mouseup(rich.select.handler);
  },

  choose: function() {

    switch ($(this).text()) {

      case 'B' :
      case 'U' :
      case 'I' :
        rich.bui($(this).text().toLowerCase());
        break;

      case 'link' :
        rich.link();
        break;

      default :
        alert('unknown button: ' + $(this).text());
        break;

    }

  },

  bui: function(chr) {

    if (rich.input.data('tip') && rich.input.val() == rich.input.data('tip')) {
      return false;
    }

    if (rich.selection && rich.selection.length > 0) {
      rich.select.replace(rich.input, '[' + chr + ']' + rich.selection.text + '[/' + chr + ']');
      return true;
    } 
    


    var val = rich.input.val() + '[' + chr + '][/' + chr + ']';
    rich.input.val(val).selectRange(val.length-4,val.length-4);

  },

  link: function() {

    var url = prompt('please enter your link');

    if (rich.selection && rich.selection.length > 0) {
      rich.select.replace(rich.input, '[url=' + url + ']' + rich.selection.text + '[/url]');
      return true;
    } 
    
    var val = rich.input.val() + '[url=' + url + '][/url]';
    rich.input.val(val).selectRange(val.length-6,val.length-6);

  },

  select: {

    handler: function() {

      if (rich.select.get(document.getElementById(rich.input.attr('id')))) {
        rich.selection = rich.select.get(document.getElementById(rich.input.attr('id')));
      }

    },

    get: function(obj) {

      var e = obj;

      if (!e.value) {
        return false;
      }

      //Mozilla and DOM 3.0
      if('selectionStart' in obj) {
          var l = e.selectionEnd - e.selectionStart;
          return { start: e.selectionStart, end: e.selectionEnd, length: l, text: e.value.substr(e.selectionStart, l) };
      }

      //IE
      else if(document.selection) {
        e.focus();
        var r = document.selection.createRange();
        var tr = e.createTextRange();
        var tr2 = tr.duplicate();
        tr2.moveToBookmark(r.getBookmark());
        tr.setEndPoint('EndToStart',tr2);
        if (r == null || tr == null) return { start: e.value.length, end: e.value.length, length: 0, text: '' };
        var text_part = r.text.replace(/[\r\n]/g,'.'); //for some reason IE doesn't always count the \n and \r in the length
        var text_whole = e.value.replace(/[\r\n]/g,'.');
        var the_start = text_whole.indexOf(text_part,tr.text.length);
        return { start: the_start, end: the_start + text_part.length, length: text_part.length, text: r.text };
      }
      //Browser not supported
      else return { start: e.value.length, end: e.value.length, length: 0, text: '' };

    },

    replace: function(obj,replace_str) {

      var val = obj.val();

      selection = rich.selection;
      var start_pos = selection.start;
      var end_pos = start_pos + replace_str.length;
      obj.val(val.substr(0, start_pos) + replace_str + val.substr(selection.end));
      return {start: start_pos, end: end_pos, length: replace_str.length, text: replace_str};
    },

    set: function(obj, start_pos, end_pos) {

      var e = obj;

      //Mozilla and DOM 3.0
      if( 'selectionStart' in e) {
          e.focus();
          e.selectionStart = start_pos;
          e.selectionEnd = end_pos;
      } else if (document.selection) {

        e.focus();
        var tr = e.createTextRange();

        //Fix IE from counting the newline characters as two seperate characters
        var stop_it = start_pos;
        for (i=0; i < stop_it; i++) if( e.value[i].search(/[\r\n]/) != -1 ) start_pos = start_pos - .5;
        stop_it = end_pos;
        for (i=0; i < stop_it; i++) if( e.value[i].search(/[\r\n]/) != -1 ) end_pos = end_pos - .5;

        tr.moveEnd('textedit',-1);
        tr.moveStart('character',start_pos);
        tr.moveEnd('character',end_pos - start_pos);
        tr.select();
      }

      return rich.select.get(obj);

    },

    wrap: function(obj, left_str, right_str, sel_offset, sel_length) {

      var the_sel_text = rich.select.get(obj).text;
      var selection =  rich.select.replcae(obj, left_str + the_sel_text + right_str );

      if (sel_offset !== undefined && sel_length !== undefined) {
        selection = rich.select.set(obj, selection.start +  sel_offset, selection.start +  sel_offset + sel_length);
      } else if (the_sel_text == '') {
        selection = rich.select.set(obj, selection.start + left_str.length, selection.start + left_str.length);
      }
      return selection;

    }

  }

}

