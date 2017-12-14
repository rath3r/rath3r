var $ = require("jquery");

function About() {
}
About.prototype.init = function() {

  this.initialised = true;
  this.jsonData = this.loadJson();
};
About.prototype.loadJson = function() {
  $.getJSON("data/rath3r-size.json", function( data ) {
    var items = [];
    $.each( data, function( key, val ) {
      items.push( "<li id='" + key + "'>" + val + "</li>" );
    });

    $( "<ul/>", {
      "class": "my-new-list",
      html: items.join( "" )
    }).appendTo( "body" );
  });
};

module.exports = About;
