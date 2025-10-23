

var map = L.map("map").setView([46.75, 1.7], 6);


document.getElementById("reset").addEventListener("click", (event) => {
    window.location.reload();
});


var Stadia_OSMBright = L.tileLayer(
    "https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png",
    {
        maxZoom: 20,
        attribution:
        '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    }
);

Stadia_OSMBright.addTo(map);

var marker = false;

function onMapClick(e) {
    if (marker){
        marker.remove(map);
    }
    $coord_mouse = String(e.latlng);
    $virgulation = 0
    for (var i = 0; i < $coord_mouse.length; i++) {
        var input = $coord_mouse[i];
        if (input == ","){
            $virgulation = i;
        }
    }
    $x_mouse = $coord_mouse.slice(7, $virgulation);
    $y_mouse = $coord_mouse.slice($virgulation + 1, -1);
    document.getElementById("x").innerHTML = "x = "+ $x_mouse ;
    document.getElementById("y").innerHTML = "y = "+ $y_mouse ;
    marker = L.marker([$x_mouse,$y_mouse]);
    marker.addTo(map);
}
map.on('click', onMapClick);