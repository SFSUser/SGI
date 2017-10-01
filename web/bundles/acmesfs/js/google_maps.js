/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var GoogleMaps = {
    set_first_marker: true,
    set_marker_onclick: true,
    inputObject: null,
    map: null,
    geocoder: null,
    mainMarker: null,
    initializeGeocoder:function(){
        GoogleMaps.geocoder = new google.maps.Geocoder();
    },
    initialize: function(element) {
        var mapOptions = {
            zoom: 5, //17
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        GoogleMaps.initializeGeocoder();
        GoogleMaps.map = new google.maps.Map(document.getElementById(element), mapOptions);
        var position = new google.maps.LatLng(4.302591077119676, -74.11376953125);
        GoogleMaps.map.setCenter(position);

        if (GoogleMaps.set_first_marker) {
            GoogleMaps.mainMarker = new google.maps.Marker({
                map: GoogleMaps.map,
                draggable: false,
                position: position
            });
            google.maps.event.addListener(GoogleMaps.map, 'click', function(event) {
                if (GoogleMaps.set_marker_onclick) {
                    GoogleMaps.mainMarker.setPosition(event.latLng);
                    GoogleMaps.map.setCenter(event.latLng);
                }
            });
        }

    },
    codeAddress: function(adress) {
        var address = adress;
        GoogleMaps.geocoder.geocode({'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                GoogleMaps.setMapLocation(results[0].geometry.location);
                /*
                 * 
                 this.map.setCenter();
                 var marker = new google.maps.Marker({
                 map: map,
                 position: results[0].geometry.location
                 });
                 */
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    }, codeLatLng: function(pos) {
        var latlng = GoogleMaps.parsePosition(pos);
        GoogleMaps.geocoder.geocode({'latLng': latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    GoogleMaps.map.setZoom(11);
                    infowindow = new google.maps.InfoWindow({
                        position: latlng,
                        map: GoogleMaps.map,
                        content: results[0].formatted_address
                    });
                    //infowindow.setContent(results[1].formatted_address);
                    //infowindow.open(map, marker);
                }
            } else {
                alert("Geocoder failed due to: " + status);
            }
        });
    },
    fundCurrentGeolocationToTarget: function(target) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = new google.maps.LatLng(position.coords.latitude,
                        position.coords.longitude);
                target(pos);
            }, function() {
                handleNoGeolocation(true);
            });
        } else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }
    },
    findCurrentAddresToTarget: function(target) {
        var latlng = GoogleMaps.getCurrentPosition();
        GoogleMaps.geocoder.geocode({'latLng': latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    target(results);
                }
            } else {
                alert("Geocoder failed due to: " + status);
            }
        });

    },
    geolocateCurrentAddressToTarget: function(target) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = new google.maps.LatLng(position.coords.latitude,
                        position.coords.longitude);

                GoogleMaps.geocoder.geocode({'latLng': pos}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            target(results);
                        }
                    } else {
                        alert("Geocoder failed due to: " + status);
                    }
                });
            }, function() {
                handleNoGeolocation(true);
            });
        } else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }
    }, setNewMarker: function(options) {
        return new google.maps.Marker(options);
    }, setNewInfoBox: function(options) {
        return new google.maps.InfoWindow(options);
    }, saveInputLocation: function() {
        if (GoogleMaps.inputObject == null) {
            return;
        }
        var lat = this.getCurrentPosition();
        GoogleMaps.inputObject.value = lat.lat() + ', ' + lat.lng();
    }, loadInputLocation: function() {
        if (GoogleMaps.inputObject == null) {
            return;
        }
        var position = GoogleMaps.inputObject.value;
        if (position == null || position == "") {
            return;
        }
        //console.log(p2);
        GoogleMaps.setLocation(position);
    }, setInputElement: function(input) {
        GoogleMaps.inputObject = document.getElementById(input);
    }, parsePosition: function(position) {
        var pos = null;
        var p1 = parseFloat(position.split(",")[0]);
        var p2 = parseFloat(position.split(",")[1]);
        pos = new google.maps.LatLng(p1, p2);
        return pos;
    }, setLocation: function(position) {
        var pos = GoogleMaps.parsePosition(position);
        GoogleMaps.setMapLocation(pos);
        GoogleMaps.map.setZoom(15);
    }, setZoom: function(zoom) {
        GoogleMaps.map.setZoom(zoom);
    }, setCenter: function(pos) {
        GoogleMaps.map.setCenter(pos);
    },
    setMapLocation: function(pos) {
        GoogleMaps.map.setCenter(pos);
        GoogleMaps.mainMarker.setPosition(pos);
    }, setMarkInGeolocation: function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = new google.maps.LatLng(position.coords.latitude,
                        position.coords.longitude);

                var infowindow = new google.maps.InfoWindow({
                    map: GoogleMaps.map,
                    position: pos,
                    content: '<h3>Esta es su ubicación fisica</h3>'
                });

                GoogleMaps.setLocation(pos);
            }, function() {
                handleNoGeolocation(true);
            });
        } else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }
    }, getCurrentPosition: function() {
        return GoogleMaps.mainMarker.getPosition();
    }, getCurrentPositionString: function() {
        return (GoogleMaps.getCurrentPosition() + "").match(/\((.*)\)/)[1];
    },
    handleNoGeolocation: function(errorFlag) {
        if (errorFlag) {
            var content = 'Error: The Geolocation service failed.';
        } else {
            var content = 'Error: Your browser doesn\'t support geolocation.';
        }

        var options = {
            map: GoogleMaps.map,
            position: new google.maps.LatLng(60, 105),
            content: content
        };

        var infowindow = new google.maps.InfoWindow(options);
        GoogleMaps.map.setCenter(options.position);
    }
};
