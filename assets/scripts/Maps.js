//var $ = require("jquery");

function Maps() {};
Maps.prototype.init = function() {
  this.initialised = true;
  this.initMap();
  //this.jsonData = this.loadJson();

};
Maps.prototype.loadJson = function() {
  $.getJSON("data/rath3r-size.json", function( data ) {
    var items = [];
    $.each( data, function( key, val ) {
      items.push( "<li id='" + key.version + "'>" + val + "</li>" );
    });


    // $( "<ul/>", {
    //   "class": "my-new-list",
    //   html: items.join( "" )
    // }).appendTo( "body" );
  });
};
Maps.prototype.initMap = function() {
  var centre = {
    lat: 53.346769,
    lng: -6.262939
  },
  zoomLevel = 12;
  //this.myLayer = L.geoJSON().addTo(this.mymap);
  console.log('inst Maps');
  mymap = L.map('map').setView([51.485, -0.065], 12);

  $.getJSON('data/20160919083114.geojson', function( data ) {
    var myLayer = L.geoJSON().addTo(mymap);
    //console.log(data.features[0]);
    this.feature = data.features[0];
    myLayer.addData(this.feature);
  });
  //L.geoJSON(this.feature).addTo(this.mymap);

  this.drawPoint([51.508, -0.11]);
};
Maps.prototype.drawPoint = function(coords) {

  var circle = L.circle(coords, {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 1
  }).addTo(mymap);

};
//module.exports = About;
