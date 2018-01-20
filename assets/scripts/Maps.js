//var $ = require("jquery");

function Maps() {};
Maps.prototype.init = function() {
  this.initialised = true;
  this.initMap();
};
Maps.prototype.initMap = function() {
  var centre = {
          lat: 51.485,
          lng: -0.065
      },
      zoomLevel = 11.5;

  mymap = L.map('map').setView([centre.lat, centre.lng], zoomLevel);

  $.getJSON('data/map-filenames.json', function( data ) {
    for (var i = 0; i < data.maps.length; i++) {
      $.getJSON('data/' + data.maps[i].filename, function( data ) {
        var myLayer = L.geoJSON().addTo(mymap);
        //console.log(data.features[0]);
        this.feature = data.features[0];
        myLayer.addData(this.feature);
      });
    }
  });
};
Maps.prototype.drawPoint = function(coords) {
  //this.drawPoint([51.508, -0.11]);
  var circle = L.circle(coords, {
    color: 'red',
    fillColor: '#f03',
    fillOpacity: 0.5,
    radius: 1
  }).addTo(mymap);

};
//module.exports = About;
