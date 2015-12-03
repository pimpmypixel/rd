<!DOCTYPE HTML>
<html>
<head>
	<title>RD</title>
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css"/>
	<link rel="stylesheet" href="../../libs/vis/dist/vis.css"/>

	<link href="../../libs/Metro-UI-CSS/build/css/metro.css" rel="stylesheet">
	<link href="../../libs/Metro-UI-CSS/build/css/metro-icons.css" rel="stylesheet">
	<link href="../../libs/Metro-UI-CSS/build/css/metro-responsive.css" rel="stylesheet">
	<link href="../../libs/Metro-UI-CSS/build/css/metro-schemes.css" rel="stylesheet">
	<style type="text/css">
		.puls8 {
			border: 3px solid #999;
			-webkit-border-radius: 30px;
			height: 18px;
			width: 18px;
			-webkit-animation: pulsate 1s ease-out;
			-webkit-animation-iteration-count: infinite;
		}

		@-webkit-keyframes pulsate {
			0% {
				-webkit-transform: scale(1, 1);
				opacity: 1.0;
			}
			/*
						50% {opacity: 1.0;}*/
			100% {
				-webkit-transform: scale(2, 2);
				opacity: 0.0;
			}
		}

		.s_result {
			cursor: pointer;
		}

		.s_result:hover {
			color: red;
		}

		dd {
			color: silver
		}

		#map {
			height: 550px;
			width: 50%;
			float: left;
		}

		#controls {
			width: 50%;
			float: right;
			padding: 20px;
		}

		body {
			font-family: Arial, Tahoma;
			font-size: 14px;
		}

		ul {
			list-style: none;
		}

		.input-control.text {
			width: 12rem;
		}

		.caption {
			font-size: 14px;
			padding-left: 20px;
		}

		.vis-inner, #reset {
			cursor: pointer;
		}

		.vis-inner:hover, #reset:hover {
			color: blue
		}
	</style>
</head>
<body>
<div id="map"></div>
<div id="controls">
	<ul id="programmer">
		<li id="reset">Nulstil kort</li>
		<li id="search">
			<div class="input-control text" data-role="input">
				<input id="searchproject" type="text" placeholder="Søg projekt">
				<button class="button helper-button clear"><span class="mif-cross"></span></button>
			</div>
			<div id="results">
				<dl></dl>
			</div>
		</li>
	</ul>
</div>


<div style="clear:both"></div>
<div id="timeline"></div>
</body>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
<script src="http://maps.stamen.com/js/tile.stamen.js?v1.3.0"></script>
<script src="../../libs/Metro-UI-CSS/build/js/metro.js"></script>
<script src="../../libs/vis/dist/vis.js"></script>
<script>
	(function ($, vis) {
		$.REALDANIAPROJECTS = {
			items: new vis.DataSet(),
			groups: new vis.DataSet(),
			timeline: {},
			colors: ['ff9200', '06942b', '1b999b', '0070c2', '860085', 'b2a16b', 'e90013'],
			container: document.getElementById('timeline'),
			selectedId: 0,
			windowRange: {},
			options: {
				start: '2010-01-01',
				end: '2016-12-31',
				height: '280px',
				stack: false,
				showCurrentTime: false,
				min: new Date(2010, 0, 1),
				max: new Date(2018, 0, 1),
				dataAttributes: 'all',
				zoomMin: 1000 * 60 * 60 * 24 * 31 * 3
			},
			init: function () {
				this.initTimeline();
				this.initMap();
				this.initDashboard();
			},
			initTimeline: function () {

			},
			initMap: function () {

			},
			initDashboard: function () {

			}
			/*
			 */
		}
	})(jQuery, vis);

	var items, groups, timeline, colors, map, selectedId, windowRange, options, container;
	$(document).ready(function () {
		$.getJSON('lib/Generate.php', {refresh: false, antal: 100}, function (data) {
			//console.dir(data);
			items = new vis.DataSet(data.projects);
			groups = new vis.DataSet(data.groups);
			initTimeline();
			initMap();
			initDashboard();
		});
	});

	function initTimeline() {
		container = document.getElementById('timeline');
		options = {
			start: '2010-01-01',
			end: '2016-12-31',
			height: '280px',
			stack: false,
			showCurrentTime: false,
			min: new Date(2010, 0, 1),
			max: new Date(2018, 0, 1),
			dataAttributes: 'all',
			zoomMin: 1000 * 60 * 60 * 24 * 31 * 3 /* ,
			 showMajorLabels: false,
			 zoomMax: 1000 * 60 * 60 * 24 * 31 * 12
			 */
		};

		$.each(items["_data"], function (i, o) {
			o.content = '';
			//o.style = 'background-color: #' + colors[o.group];
			//delete o.end;
			//delete o.type;
		});

		timeline = new vis.Timeline(container, items, options);
		timeline.setGroups(groups);
		timeline.on('select', function (e) {
			selectedId = e.items[0];
			renderProject();
		});
	}

	function initMap() {
		var type = "toner";
		map = L.map('map', {
			//zoomControl: false
		});
		//map.dragging.disable();
		//map.touchZoom.disable();
		//map.doubleClickZoom.disable();
		//map.scrollWheelZoom.disable();
		resetMap();

		map.addLayer(new L.StamenTileLayer(type, {detectRetina: true}));

		$.each(items["_data"], function (i, o) {
			if (typeof o.latlon != 'undefined' && o.latlon != null) {
				var r = Math.pow(Math.log10(o.bevilget), 2) - 30;
				//var c = colors[o.group];
				marker = new L.circleMarker(
					[o.latlon.lat, o.latlon.lng],
					{
						//fillColor: '#' + c,
						radius: 5,
						/*
						 color: "#000",
						 weight: 0,
						 */
						opacity: 0,
						fillOpacity: 1,
						className: 'circle' + o.id + ' g' + o.group
					}
				)/*.bindPopup(o.content)*/
					.addTo(map)
					.on('click', function (e) {
						console.dir(e);
						selectedId = e.target.options.className.replace('circle', '');
						renderProject();
					});
			}
		});
		$(".leaflet-control-attribution").remove();
		/*
		 map.on('popupopen', function (centerMarker) {
		 var cM = map.project(centerMarker.popup._latlng);
		 cM.y -= centerMarker.popup._container.clientHeight / 2
		 map.setView(map.unproject(cM), 16, {animate: true});
		 });*/
	}

	function renderProject(i) {
		timeline.focus(selectedId, {animation: {duration: 1000, easingFunction: 'easeOutQuad'}});

		windowRange = timeline.getWindow();
		console.dir(windowRange);
		var interval = windowRange.end - windowRange.start;
		console.dir(interval);

		timeline.setWindow({
			start: windowRange.start.valueOf() - interval * -.2,
			end: windowRange.end.valueOf() + interval * -.2
		});
		/*
		 map.setZoomAround([items["_data"][i].latlng.lat, items["_data"][i].latlng.lng], 12, {
		 animate: true
		 });*/
	}

	function resetMap() {
		map.setView([56.2, 10.82], 7);
		windowRange = timeline.getWindow();
		timeline.setWindow({
			start: windowRange.start.valueOf(),
			end: windowRange.end.valueOf()
		});
	}

	function initDashboard() {
		$(".vis-inner").each(function (i, o) {
			o.on = true;
			$(o).click(function (e) {
				if (o.on) {
					$(o).css({'color': 'red'});
					o.on = false;
					$(".vis-group").eq(15 + i).find(".vis-item").css('border-color', 'red');
					$(".g" + i).hide();
				} else {
					$(".vis-group").eq(15 + i).find(".vis-item").css('border-color', '#97B0F8');
					$(o).css({'color': 'black'});
					o.on = true;
					$(".g" + i).show();
				}
			});
		});
		$("#reset").click(function () {
			resetMap();
		});
		/*$.each(groups["_data"], function (i, o) {
		 $("#programmer").append('<li><label class="input-control checkbox small-check"><input type="checkbox" checked><span class="check"></span><span class="caption">' + o.content + '</span></label></li>')
		 });*/
	}

	$("#searchproject").keyup(function () {
		$("#results").find("dl").empty();
		var searchResults = [];
		var q = $(this).val();
		if (q.length > 3) {
			$('#results').fadeIn("fast");
			for (var p = 0; p < Object.keys(items["_data"]).length; p++) {
				var o = items["_data"][p];
				if (typeof o !== 'undefined') {
					if (o.adresse.toLowerCase().indexOf(q) > -1 || o.navn.toLowerCase().indexOf(q) > -1) {
						searchResults.push(p);
						$("#results").find("dl").append("<dt class='s_result' id='res" + p + "'>" + o.navn + "</dt><dd>" + o.adresse + "</dd>");
						//$("#map path#" + id).css({"opacity": "1"});
					} else {
						//$("#map path#" + id).css({"opacity": "0.5"});
					}
				}
			}
		} else {
			$("#results").find("ul").empty();
			/*
			 $("#map path").css({"opacity": "1"});*/
		}
		console.log(searchResults);
	});
	$("dl").on('click', '.s_result', function () {
		$("#results").find("dl").empty();
		var i = this.id.replace('res', '');
		renderProject(i)

	})


</script
</html>