window.onload = init;

function init(){
	
	const map = new ol.Map({
			
		layers:[
			
			new ol.layer.Tile({
				
				source: new ol.source.OSM()
				
			})
			
		],
		target:'js-map',
		
		view: new ol.View({
			
			center:[0,0],
			zoom:2
			
		})
	});

	var style1 = [
		new ol.style.Style({
			image: new ol.style.Icon(({
				scale: 0.7,
				rotateWithView: false,
				anchor: [0.5, 1],
				anchorXUnits: 'fraction',
				anchorYUnits: 'fraction',
				opacity: 1,
				src: 'marker.png'
			})),
			zIndex: 5
		}),
		new ol.style.Style({
			image: new ol.style.Circle({
				radius: 5,
				fill: new ol.style.Fill({
					color: 'rgba(255,0,0,1)'
				}),
				stroke: new ol.style.Stroke({
					color: 'rgba(0,0,0,1)'
				})
			})
		})
	];



	fetch('./data.json').then(response => {
		return response.json();
	}).then(data => {

		var len = data.length;

		for(a=0;a<len;a++){

			var layer = new ol.layer.Vector({
				source: new ol.source.Vector({
					features: [
						new ol.Feature({
							id:data[a]['id'],
							geometry: new ol.geom.Point(ol.proj.fromLonLat([data[a]['longitude'],data[a]['latitude']])),				
						})
					]
				})
			});

			layer.setStyle(style1);
			map.addLayer(layer);
		}

	}).catch(err => {
		
	});

	map.on("click", function(e) {
		map.forEachFeatureAtPixel(e.pixel, function (feature, layer) {

			var selected_feature = feature.values_.id;

			fetch('./data.json').then(response => {
				return response.json();

			}).then(data => {
		
				var len = data.length;
		
				for(a=0;a<len;a++){
				
					var company_id = data[a]['id']

					if(company_id == selected_feature){

						document.getElementById("info_box").style.display="block";
						document.getElementById("info_box_heading").innerHTML = data[a]['company_name']
						document.getElementById("info_box_text").innerHTML = data[a]['description']
						document.getElementById("info_box_logo").src = data[a]['logo']
						document.getElementById("info_box_link").href = data[a]['website_link']
						document.getElementById("info_box_link").innerHTML = data[a]['website_link']
					}
				 
				}
		
			}).catch(err => {
				
			});
		
		})
	});
	
	var tooltip = document.getElementById('tooltip_1');

	var overlay = new ol.Overlay({
		element: tooltip,
		offset: [10, 0],
		positioning: 'bottom-left'
	});

	map.addOverlay(overlay);

	function displayTooltip(evt) {

		var selected_feature = "";

		map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {

			selected_feature = feature.values_.id;
			
		})
		
		var pixel = evt.pixel;
		var feature = map.forEachFeatureAtPixel(pixel, function(feature) {
			return feature;
		});
	
		tooltip.style.display = feature ? '' : 'none';

		if (feature) {

			overlay.setPosition(evt.coordinate);

			fetch('./data.json').then(response => {
				return response.json();

			}).then(data => {
		
				var len = data.length;
				
				for(b=0;b<len;b++){

					var company_id = data[b]['id'];

					if(company_id == selected_feature){

						document.getElementById("tooltip_heading").innerHTML = data[b]['company_name']
						document.getElementById("tooltip_logo").src = data[b]['logo']

					}

					
				}
		
			}).catch(err => {
				
			});
			
			//tooltip.innerHTML = "guna";
		}

		var pixel = map.getEventPixel(evt.originalEvent);
		var hit = map.hasFeatureAtPixel(pixel);
		map.getViewport().style.cursor = hit ? 'pointer' : '';

		
	};

	map.on('pointermove', displayTooltip);






}