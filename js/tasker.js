
$(document).ready( function()
{

   // html_button("Create table", '#menu',     function (){ $('code').html(ajax_call("func=create"))  }); // Create MySQL table
   // html_button("Drop table",   '#menu',     function (){ $('code').html(ajax_call("func=drop"))    }); // Delete MySQL table
    html_button("Update table", '#menu',     function (){ $('code').html(ajax_call("func=update"))  }); // Redraw table

    html_button("Priorities",   '#menu',     function (){ $('code').html(ajax_call("func=prior"))   }); // Get partial priorities
    html_button("Options",      '#menu',     function (){ $('code').html(ajax_call("func=options")) }); // Get partial priorities
    html_button("Add the task",      '#menu',     function (){ $('code').html(ajax_call("func=addForm")) }); // Get html form
    html_button("Add",      '#menu',     function (){ $('code').html(ajax_call("func=add")) }); // Add the task from form
});

// html_button (null|string) =  function ( value, id, [event], [selector] )
// If event         is specified - bind event to button
// If selector  is specified - appent button to selector
html_button = function ()
{
    var id = 0; // closure counter

    return function (value, selector, event)
    {
        // Nothing passed - Button n
        if (!value)
            value = "Button " + id;

        // @value passed — Name to @value
        button_code = '<div class = "button" id = "bt' + id + '">' + value + '</div>';

        if (selector)
            $( selector ).append( button_code );

        if (event)
            $( '#bt' + id ).on( "click", event );
        id++;

        if (!selector)
            return (button_code);

    }
}();



// Ajax call, func = "func=" + "create"|"drop"|"add"|"update"
// Takes $_POST when required
function ajax_call(func)
{
    func = func.split("=");
    myData = {};
    myData[func[0]] = func[1];

    myData = $.extend(myData, $("form").serializeObject());
    myData = JSON.stringify(myData);

    console.log(myData);

    aj = $.ajax(
    {
        type: "POST",
        url: 'src/Key.php',
        async: false,
        dataType: 'json',
        data: {myjson:myData},

        success: function(){
            console.log('Load was performed.');
        },
        error: function(e){
      console.log(e.message);
    }
    });

    return (aj.responseText);
    console.log (aj);
}

// Transform table into object array
function table2array(){
    var data = $("#taskTable tr").map(function(){
      return {
        "y0" : $("td",this).eq(0).text(),
        "y1" : $("td",this).eq(1).text(),
        "y2" : $("td",this).eq(2).text(),
        "y3" : $("td",this).eq(3).text(),
        "y4" : $("td",this).eq(4).text(),
      };
    }).get();
    console.table(data);
}