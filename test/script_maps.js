document.addEventListener("DOMContentLoaded", function () {

    var map = L.map("map").setView([46.75, 1.7], 6);

    var Stadia_OSMBright = L.tileLayer(
        "https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png",
        {
            maxZoom: 20,
            attribution:
                '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, ' +
                '&copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> ' +
                '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        }
    );

    Stadia_OSMBright.addTo(map);

    var geofence = L.circle([50.32077, 3.5134], {
        color: "blue",
        fillColor: "#2a5298",
        fillOpacity: 0.2,
        radius: 50000
    }).addTo(map);

    function isInZone(lat, lng) {
        var point = L.latLng(lat, lng);
        var center = geofence.getLatLng();
        return point.distanceTo(center) <= geofence.getRadius();
    }

    var marker = L.marker([currentLat, currentLng]).addTo(map);

    var zoneIcon = L.divIcon({
        className: "zone-marker-icon",
        iconSize: [26, 26]
    });

    var centerMarker = L.marker(geofence.getLatLng(), {
        draggable: true,
        icon: zoneIcon
    }).addTo(map);

    let alreadyAlerted = false;

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

            alreadyAlerted = false;

        } else {

            geofence.setStyle({
                color: "red",
                fillColor: "#e74c3c",
                fillOpacity: 0.3
            });

            if (markerEl) markerEl.style.animation = "blink 1s infinite";
            map.getContainer().style.animation = "shake 0.5s";
            alertBox.classList.remove("hidden");

            if (!alreadyAlerted) {
                alert("ALERTE : Capteur hors de la zone !");
                alreadyAlerted = true;
            }
        }
    }

    map.whenReady(updateZoneStatus);

    centerMarker.on("drag", function (e) {
        geofence.setLatLng(e.target.getLatLng());
        updateZoneStatus();
    });

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

        .radius-btn button {
            background: #1e3c72;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .radius-btn button:hover {
            background: #16325c;
        }
    `;
    document.head.appendChild(style);

    var radiusControl = L.control({ position: "topright" });

    radiusControl.onAdd = function () {
        var div = L.DomUtil.create("div", "radius-btn");
        div.innerHTML = '<button id="btnRadius">Changer rayon</button>';
        return div;
    };

    radiusControl.addTo(map);

    L.DomEvent.disableClickPropagation(document.querySelector(".radius-btn"));

    document.getElementById("btnRadius").onclick = function () {
        var newRadius = prompt("Nouveau rayon en mètres :", geofence.getRadius());
        if (!isNaN(newRadius) && newRadius > 0) {
            geofence.setRadius(Number(newRadius));
            updateZoneStatus();
        }
    };

});