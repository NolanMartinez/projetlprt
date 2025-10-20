var map = L.map("map").setView([46.75, 2.3522219], 6);

var Stadia_OSMBright = L.tileLayer(
    "https://tiles.stadiamaps.com/tiles/osm_bright/{z}/{x}/{y}{r}.png",
    {
        maxZoom: 20,
        attribution:
        '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    }
);

Stadia_OSMBright.addTo(map);

var marker = L.marker([48.856614, 2.3522219]).addTo(map);
var marker = L.marker([50.36336728031363, 3.517169812090994]).addTo(map);