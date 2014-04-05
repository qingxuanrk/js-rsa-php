$(function(){

    var obj = $( "#input-main" ) ;
    function write_link(link) {obj.attr('now', link)};
    function read_link() {return obj.attr('now')};
    function goto_link() {window.location=read_link()};

    var projects = [
      {
        value: "jquery",
        label: "jQuery",
        desc: "the write less, do more, JavaScript library",
      },
      {
        value: "jquery-ui",
        label: "jQuery UI",
        desc: "the official user interface library for jQuery",
      },
      {
        value: "sizzlejs",
        label: "Sizzle JS",
        desc: "a pure-JavaScript CSS selector engine",
      }
    ];

    obj.autocomplete({
      minLength: 0,
      source: projects,
      focus: function( event, ui ) {
        return false;
      },
      select: function( event, ui ) {
        obj.val( ui.item.label );
        return false;
      }
    })
    .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
     /* return $( "<li>" )
        .append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
        .appendTo( ul );*/
    };

    $(window).keydown(function(event){
        if (event.keyCode == 13) {
            gotofile($( "#filename" ).val());
        }
    });


})
