

    var geofence = L.circle([lat_cookie, lng_cookie], {
        color: "blue",
        fillColor: "#2a5298",
        fillOpacity: 0.2,
        radius: radius_cookie
    }).addTo(map);
    
    var currentZoneId = null;
    var isDragging = false;
    

    var zoneIcon = L.divIcon({
        className: "zone-marker-icon",
        iconSize: [26, 26]
    });

    var centerMarker = L.marker(geofence.getLatLng(), {
        draggable: true,
        icon: zoneIcon
    }).addTo(map);
    
    function saveZoneToDatabase(i) {
        var center = geofence.getLatLng();
        var idCapEl = document.getElementById('capteur');
        //var idCap = idCapEl ? idCapEl.value : null;
        var nom_zon = document.getElementById('nouveau_nom').value;
        if(!nom_zon && !currentZoneId){
            alert("Rentrer le nom de la zone");
            exit;
        }
        if(i == "nom"){
           var zoneData = {
                id_zone: currentZoneId || 0,
                nom:nom_zon,
                latitude: center.lat,
                longitude: center.lng,
                radius: geofence.getRadius()
            }; 
            if (currentZoneId != null){
                var sel = document.getElementById('zone');
                sel.options[sel.selectedIndex].label = nom_zon;
            }
        }else{
            var zoneData = {
                id_zone: currentZoneId || 0,
                latitude: center.lat,
                longitude: center.lng,
                radius: geofence.getRadius()
            }; 
        }
        
        
        console.log('Envoi zone:', zoneData);
        
        fetch('api_zones.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(zoneData)
        })
        .then(response => {
            if (!response.ok) {
                console.error('Réponse HTTP:', response.status);
                return response.text().then(text => {
                    console.error('Réponse:', text);
                });
            }
            if (currentZoneId){
                alert("La zone à été modifiée");
            }
            else{
                alert("La zone à été ajouté");
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success && data.id_zone) {
                if (currentZoneId == null){
                    opt = document.createElement("option");
                    opt.value = data.id_zone;
                    opt.text = nom_zon;
                    document.getElementById('zone').add(opt,null);
                    document.getElementById('zone').value = data.id_zone;
                    document.forms["info"].submit();
                }
                currentZoneId = data.id_zone;
                console.log('Zone sauvegardée avec l\'ID:', currentZoneId);
            } else if (data && data.error) {
                console.error('Erreur API:', data.error);
            }
        })
        .catch(error => console.error('Erreur lors de la sauvegarde:', error));
    }
    
    function loadZonesFromDatabase(id) {
        console.log('Chargement des zones...');
        fetch('api_zones.php/?id=' + id, {
            method: 'GET'
        })
        .then(response => {
            if (!response.ok) {
                console.error('Réponse HTTP:', response.status);
                return response.text().then(text => {
                    console.error('Réponse:', text);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            if (Array.isArray(data) && data.length > 0) {
                var zone = data[0];
                console.log('Zone trouvée, latitude:', zone.latitude, 'longitude:', zone.longitude, 'radius:', zone.radius);
                geofence.setLatLng([parseFloat(zone.latitude), parseFloat(zone.longitude)]);
                geofence.setRadius(parseFloat(zone.radius));
                centerMarker.setLatLng([parseFloat(zone.latitude), parseFloat(zone.longitude)]);
                currentZoneId = zone.id_zone;
                console.log('Zone chargée de la BD:', zone);
            } else {
                console.log('Aucune zone trouvée en BD');
                //saveZoneToDatabase();
            }
        })
        .catch(error => console.error('Erreur lors du chargement:', error));
    }

    let alreadyAlerted = false;
    
    loadZonesFromDatabase(id_z);

    centerMarker.on("dragstart", function (e) {
        isDragging = true;
    });

    centerMarker.on("drag", function (e) {
        geofence.setLatLng(e.target.getLatLng());
        $coord_mouse = e.latlng;
        
        document.getElementById("x").innerHTML = "x = <br>" + $coord_mouse.lat;
        document.getElementById("y").innerHTML = "y = <br>" +$coord_mouse.lng;

    });

    centerMarker.on("dragend", function (e) {
        isDragging = false;
        //saveZoneToDatabase();
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
            document.getElementById("r").innerHTML = "Radius = <br>" + newRadius;
            geofence.setRadius(Number(newRadius));
            //saveZoneToDatabase();
        }
    };

