document.addEventListener("DOMContentLoaded", function () {

    var map = L.map("map").setView([46.75, 1.7], 6);

    var Stadia_OSMBright = L.tileLayer(
        "https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png",
        {
            maxZoom: 20,
            attribution:
            '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        }
    );

    Stadia_OSMBright.addTo(map);

    var geofence = L.circle([50.32077, 3.5134], {
        color: 'blue',
        fillColor: '#2a5298',
        fillOpacity: 0.2,
        radius: 50000
    }).addTo(map);

    if (!isInZone(currentLat, currentLng)) {
    geofence.setStyle({
        color: 'red',
        fillColor: '#e74c3c',
        fillOpacity: 0.3
        });
    }

    function isInZone(lat, lng) {
        var point = L.latLng(lat, lng);
        var center = L.latLng(50.32077, 3.5134);
        return point.distanceTo(center) <= 50000;
    }

    var marker = L.marker([currentLat, currentLng]).addTo(map);

    if (!isInZone(currentLat, currentLng)) {
        alert("ALERTE : Capteur hors zone !");
        marker.getElement().style.animation = 'blink 1s infinite';
        map.getContainer().style.animation = 'shake 0.5s';
        setTimeout(function() {
            map.getContainer().style.animation = '';
        }, 500);
    }

    if (typeof polylinePoints !== 'undefined') {
        L.polyline(polylinePoints).addTo(map);
    }

    var style = document.createElement('style');
    style.innerHTML = `
    @keyframes blink { 50% { opacity: 0.3; } }
    @keyframes shake { 10%,30%,50%,70%,90% { transform: translateX(-5px); } 20%,40%,60%,80% { transform: translateX(5px); } }
    `;
    document.head.appendChild(style);

});