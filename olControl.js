<script type="text/javascript" src="olControl.js"></script>

var cont_rotation = new ol.control.Rotate({
    tipLabel: 'Réinitialiser la rotation - Maintenez alt+shift pour tourner la carte',
    autoHide: false,
})

var cont_echelle = new ol.control.ScaleLine({})

var cont_position_cuseur = new ol.control.MousePosition({	
	coordinateFormat: function(coordinate) {
		return ol.coordinate.format(coordinate, '<span><i class="fas fa-map-marker-alt"></i> {x} ° | {y} °</span>', 6);
	},
	projection: 'EPSG:4326',
})

var cont_plein_ecran = new ol.control.FullScreen({
    tipLabel: 'Passez en mode plein-écran',
})