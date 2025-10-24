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

function onMapClick(e) {
    document.getElementById("x").innerHTML = "x = "+ e.latlng ;
}
map.on('click', onMapClick);
