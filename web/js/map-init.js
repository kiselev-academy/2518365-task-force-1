function initMap(latitude, longitude) {
    ymaps.ready(function () {
        var myMap = new ymaps.Map("map", {
            center: [latitude, longitude],
            zoom: 14
        });
        myMap.geoObjects.add(new ymaps.Placemark([latitude, longitude], {
            hintContent: 'Метка'
        }, {
            preset: 'islands#redIcon'
        }));
    });
}