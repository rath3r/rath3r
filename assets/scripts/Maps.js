//var $ = require("jquery");
var myMap;
function Maps() {};
Maps.prototype.init = function() {
  var centre = {
        lat: 51.485,
        lng: -0.065
      },
      zoomLevel = 11.5;
  this.initialised = true;
  myMap = L.map('map').setView([centre.lat, centre.lng], zoomLevel);
  //this.initMap();

  this.addRoutes();
  this.addPoint([51.44845115,-0.03590759,131]);
};
Maps.prototype.addRoutes = function() {
  $.getJSON('data/map-filenames.json', function( data ) {
    for (var i = 0; i < data.mapNames.length; i++) {
      $.getJSON('data/' + data.mapNames[i].filename, function( data ) {
        var myLayer = L.geoJSON().addTo(myMap);
        //console.log(data.features[0]);
        this.feature = data.features[0];
        myLayer.addData(this.feature);
      });
    }
  });
};
Maps.prototype.addPoint = function(coords) {
  var circle = L.circle(coords, {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 1
  }).addTo(myMap);
  circle.bringToFront();
};
Maps.prototype.addMarker = function(coords) {
  var myIcon = L.icon({
    iconUrl: 'images/map-marker.png',
    iconSize: [16, 16],
    iconAnchor: [7.5, 18]
  });
  L.marker(coords, {icon: myIcon}).addTo(myMap);
};
//module.exports = About;
