function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
            c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
            }
        }
        return "";
    }
var id_z = getCookie('id_zone');


if (id_z){
var geofence = L.circle([50.32077, 3.5134], {
    color: "blue",
    fillColor: "#2a5298",
    fillOpacity: 0.2,
    radius: 5
}).addTo(map);

function isInZone(lat, lng) {
    var point = L.latLng(lat, lng);
    var center = geofence.getLatLng();
    return point.distanceTo(center) <= geofence.getRadius();
}


var zoneIcon = L.divIcon({
    className: "zone-marker-icon",
    iconSize: [26, 26]
});

var centerMarker = L.marker(geofence.getLatLng(), {
    draggable: false,
    icon: zoneIcon
}).addTo(map);
function loadZoneFromDatabase(id) {
    if(id){
        fetch('api_zones.php/?id=' + id, { method: 'GET' })
        .then(response => {
            if (!response.ok) {
                console.error('Réponse HTTP zones:', response.status);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if (Array.isArray(data) && data.length > 0) {
                var zone = data[0];
                var lat = parseFloat(zone.latitude);
                var lng = parseFloat(zone.longitude);
                var r = parseFloat(zone.radius);
                if (!isNaN(lat) && !isNaN(lng)) {
                    geofence.setLatLng([lat, lng]);
                    centerMarker.setLatLng([lat, lng]);
                }
                if (!isNaN(r)) geofence.setRadius(r);
                console.log('Zone view chargée:', zone);
            } else {
                console.log('Aucune zone trouvée (view)');
            }
        })
        .catch(err => console.error('Erreur fetch zones (view):', err));
    }
}

loadZoneFromDatabase(id_z);

function updateZoneStatus() {
    var alertBox = document.getElementById("zoneAlert");
    var markerEl = marker.getElement();

    if (isInZone(currentLat, currentLng)) {

        geofence.setStyle({
            color: "blue",
            fillColor: "#2a5298",
            fillOpacity: 0.2
        });

        if (markerEl) markerEl.style.animation = "";
        map.getContainer().style.animation = "";
        alertBox.classList.add("hidden");

    } else {

        geofence.setStyle({
            color: "red",
            fillColor: "#e74c3c",
            fillOpacity: 0.3
        });

        if (markerEl) markerEl.style.animation = "blink 1s infinite";
        map.getContainer().style.animation = "shake 0.5s";
        alertBox.classList.remove("hidden");
    }
}

map.whenReady(updateZoneStatus);

if (typeof polylinePoints !== "undefined") {
    L.polyline(polylinePoints).addTo(map);
}

var style = document.createElement("style");
style.innerHTML = `
    @keyframes blink { 50% { opacity: 0.3; } }
    @keyframes shake {
        10%,30%,50%,70%,90% { transform: translateX(-5px); }
        20%,40%,60%,80% { transform: translateX(5px); }
    }

    .zone-marker-icon {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #ffcc00;
        border: 3px solid #d49a00;
        box-shadow: 0 0 10px rgba(255, 204, 0, 0.7);
    }
    `;
    document.head.appendChild(style);
}
