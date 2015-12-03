/**
 * Created by andreas on 14/11/15.
 */
(function ($, vis, L, Handlebars, google, TweenMax) {
	$.RD = {
		jsonData: {},
		antal: 100,
		farver: {
			'rd': 'f9001f'
		},
		maptype: 'toner-lite',
		center: L.latLng([56.2, 11.65]),
		tileLayer: {},
		streetView: {},
		zoom: 7,
		map: {},
		items: new vis.DataSet(),
		groups: new vis.DataSet(),
		colors: ['ff9200', '06942b', '1b999b', '0070c2', '860085', 'b2a16b', 'e90013'],
		container: document.getElementById('timeline'),
		streetViewContainer: document.getElementById('street'),
		searchResults: [],
		selectedId: 0,
		interval: 0,
		windowRange: {},
		timeline: {},
		infoTemplate: $("#info-template").html(),
		compiledTemplate: {},
		compiledHtml: '{}',
		markerArray: {},
		markerOptions: {
			icon: L.icon({
				iconUrl: 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+Cjxzdmcgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogPCEtLSBDcmVhdGVkIHdpdGggTWV0aG9kIERyYXcgLSBodHRwOi8vZ2l0aHViLmNvbS9kdW9waXhlbC9NZXRob2QtRHJhdy8gLS0+CiA8Zz4KICA8dGl0bGU+YmFja2dyb3VuZDwvdGl0bGU+CiAgPHJlY3QgZmlsbD0iI2ZmZiIgaWQ9ImNhbnZhc19iYWNrZ3JvdW5kIiBoZWlnaHQ9IjIyIiB3aWR0aD0iMjIiIHk9Ii0xIiB4PSItMSIvPgogIDxnIGRpc3BsYXk9Im5vbmUiIG92ZXJmbG93PSJ2aXNpYmxlIiB5PSIwIiB4PSIwIiBoZWlnaHQ9IjEwMCUiIHdpZHRoPSIxMDAlIiBpZD0iY2FudmFzR3JpZCI+CiAgIDxyZWN0IGZpbGw9InVybCgjZ3JpZHBhdHRlcm4pIiBzdHJva2Utd2lkdGg9IjAiIHk9IjAiIHg9IjAiIGhlaWdodD0iMTAwJSIgd2lkdGg9IjEwMCUiLz4KICA8L2c+CiA8L2c+CiA8Zz4KICA8dGl0bGU+TGF5ZXIgMTwvdGl0bGU+CiAgPGVsbGlwc2Ugcnk9IjEwIiByeD0iMTAiIGlkPSJzdmdfMSIgY3k9IjEwIiBjeD0iMTAiIHN0cm9rZS13aWR0aD0iMCIgZmlsbD0iIzAwMDAwMCIvPgogPC9nPgo8L3N2Zz4=',
				iconSize: [10, 10],
				iconAnchor: [10, 10]
			})
		},
		projectPile: new L.Leafpile(),
		timelineOptions: {
			type: 'point',
			start: '2010-01-01',
			end: '2016-12-31',
			height: '280px',
			stack: false,
			showCurrentTime: false,
			min: new Date(2010, 0, 1),
			max: new Date(2018, 0, 1),
			dataAttributes: 'all',
			zoomMin: 1000 * 60 * 60 * 24 * 31 * 3	// 3 months
		},
		Map: {
			init: function () {
				$.RD.map = L.map('map');
				$.RD.tileLayer = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/' + $.RD.maptype + '/{z}/{x}/{y}.png', {
					detectRetina: true,
					useCache: true
				}).addTo($.RD.map);
				$(".leaflet-control-attribution").remove();
				this.reset();
				this.addProjects();
			},
			addProjects: function () {
				$.each($.RD.items["_data"], function (i, o) {
					if (typeof o.latlon != 'undefined' && o.latlon != null) {
						var r = Math.pow(Math.log10(o.bevilget), 2) - 30;
						var c = $.RD.colors[o.group];
						$.RD.map.addLayer($.RD.projectPile);

						var marker = new L.Marker([o.latlon.lat, o.latlon.lng], $.RD.markerOptions)
							//.addTo($.RD.map)
							.on('click', function (e) {
								$.RD.selectedId = e.target.options.className.replace('circle', '');
								$.RD.selectedId = $.RD.selectedId.split(" ")[0];
								$.RD.Project.render();
							});

						$.RD.projectPile.addMarker(marker);
					}
				});
				$.RD.Map.colorProjects(false);
			},
			colorProjects: function (red) {
				$.each($.RD.groups, function (i, o) {
					var c = red === true ? $.RD.farver['rd'] : $.RD.colors[i];
					$(".g" + i).attr("fill", "#" + c);
				})
			},
			reset: function () {
				$.RD.map.setView($.RD.center, $.RD.zoom);
			},
			zoomPoint: function () {
				TweenMax.to(".circle" + $.RD.selectedId, 3,
					{
						attr: {
							"stroke-width": 200,
							"stroke-opacity": 0
						},
						delay: 2,
						ease: Expo.easeOut,
						onComplete: function () {
							$(".circle" + $.RD.selectedId).attr({
								"stroke-width": 0,
								"stroke-opacity": 1
							});
						}
					});
				if ($.RD.items._data[$.RD.selectedId].latlon) {
					$.RD.map.setView([$.RD.items._data[$.RD.selectedId].latlon.lat, $.RD.items._data[$.RD.selectedId].latlon.lng], 10, {
						pan: {animate: true, duration: 1.0},
						zoom: {animate: true}
					});
				}
			}
		},
		Timeline: {
			init: function () {
				$.RD.timeline = new vis.Timeline($.RD.container);
				$.RD.timeline.setItems($.RD.items);
				$.RD.timeline.setGroups($.RD.groups);
				$.RD.timeline.setOptions($.RD.timelineOptions);

				$.each($.RD.items["_data"], function (i, o) {
					$.RD.items["_data"][i].content = '';
					o.content = null;
					o.style = 'background-color: #' + $.RD.colors[o.group];
					//delete o.end;
					//delete o.type;
				});
				$(".vis-item-content").each(function (i, o) {
					$(o).empty();
				});

				$('.vis-item').hover(function () {
					var $hovered = $(this).parent();
					if ($($hovered)[0].lastElementChild.dataset.navn !== 'undefined') {
						var title = $($hovered)[0].lastElementChild.dataset.navn;
						$('<p class="tooltip"></p>').text(title).appendTo('body').fadeIn('slow');
					}
				}, function () {
					$('.tooltip').remove();
				}).mousemove(function (e) {
					var mousex = e.pageX + 20;
					var mousey = e.pageY + 10;
					$('.tooltip')
						.css({top: mousey, left: mousex})
				});
				$.RD.timeline.on('select', function (e) {
					$.RD.selectedId = e.items[0];
					//console.log($.RD.selectedId);
					$.RD.Project.render();
				})
			},
			focus: function () {
				$.RD.timeline.setSelection($.RD.selectedId/*, {
				 focus: true
				 }*/);
				timeline.focus($.RD.selectedId, {animation: {duration: 1000, easingFunction: 'easeOutQuad'}});
			},
			reset: function () {
				$.RD.timeline.setSelection();
				$.RD.timeline.fit();
			}
		},
		Dashboard: {
			init: function () {
				$.RD.compiledTemplate = Handlebars.compile($.RD.infoTemplate);
				$(".vis-inner").each(function (i, o) {
					o.on = true;
					$(o).click(function (e) {
						if (o.on) {
							$(o).css({'color': 'red'});
							o.on = false;
							$(".vis-group").eq(15 + i).find(".vis-item").css('border-color', 'red');
							$(".g" + i).hide();
						} else {
							$(".vis-group").eq(15 + i).find(".vis-item").css('border-color', 'black');
							$(o).css({'color': 'black'});
							o.on = true;
							$(".g" + i).show();
						}
					});
				});
				$(".vis-label").css({'height': '25px'});

				$("#resetMap").click(function () {
					$.RD.Map.reset();
				});
				$("#resetTimeline").click(function () {
					$.RD.Timeline.reset();
				});
			},
			steetView: function () {
				$.RD.streetView = new google.maps.StreetViewPanorama(
					$.RD.streetViewContainer, {
						position: {
							lat: $.RD.items._data[$.RD.selectedId].latlon.lat,
							lng: $.RD.items._data[$.RD.selectedId].latlon.lng
						},
						pov: {
							heading: 0,
							pitch: 0
						},
						streetViewControl: true,
						panControl: false,
						scaleControl: false,
						zoomControl: false,
						linksControl: false,
						//mapTypeId: "roadmap",
						visible: true
					});
				/*
				 setTimeout(function () {
				 $("#street .gmoprint, #street > div > div:nth-child(5)").remove();
				 }, 1000);*/
			}
		},
		Project: {
			render: function () {
				$.RD.Map.zoomPoint();
				$.RD.Dashboard.steetView();
				$.RD.Timeline.focus();
				$.RD.windowRange = $.RD.timeline.getWindow();
				$.RD.interval = $.RD.windowRange.end - $.RD.windowRange.start;
				$.RD.timeline.setWindow({
					start: $.RD.windowRange.start.valueOf() - $.RD.interval * -.2,
					end: $.RD.windowRange.end.valueOf() + $.RD.interval * -.2
				});
				$.RD.compiledHtml = $.RD.items._data[$.RD.selectedId];
				$.RD.compiledHtml.color = $.RD.colors[$.RD.items._data[$.RD.selectedId].group];
				/*	console.log($.RD.items._data[$.RD.selectedId].group);
				 console.log($.RD.colors[$.RD.items._data[$.RD.selectedId].group]);
				 console.dir($.RD.compiledHtml);
				 */
				$.RD.compiledHtml.group = $.RD.groups[$.RD.items._data[$.RD.selectedId].group].content;
				$.RD.compiledHtml = $.RD.compiledTemplate($.RD.compiledHtml);
				$("#info").html($.RD.compiledHtml);
			}
		},
		Interactions: {
			init: function () {
				$("#searchproject").keyup(function () {
					$("#results").find("dl").empty();
					var searchResults = [];
					var q = $(this).val();
					var count = 1;
					if (q.length > 3) {
						$('#results').fadeIn("fast");
						for (var p = 0; p < Object.keys($.RD.items["_data"]).length; p++) {
							var o = $.RD.items["_data"][p];
							if (typeof o !== 'undefined') {
								if (o.adresse.toLowerCase().indexOf(q) > -1 || o.navn.toLowerCase().indexOf(q) > -1) {
									searchResults.push(p);
									$("#results").find("dl").append("<dt class='s_result' id='res" + p + "'>" + count + ") " + o.navn + "</dt><dd>" + o.adresse + "</dd>");
									//$("#map path#" + id).css({"opacity": "1"});
									count++;
								} else {
									//$("#map path#" + id).css({"opacity": "0.5"});
								}
							}
						}
						console.log(searchResults);
					} else {
						$("#results").find("ul").empty();
						$("#map path").css({"opacity": "1"});
					}
				});

				$("dl").on('click', '.s_result', function () {
					$("#results").find("dl").empty();
					$.RD.selectedId = this.id.replace('res', '');
					$.RD.Project.render();
				})

				$('#farver').on("change", function () {
					if ($(this).is(":checked")) {
						$.RD.Map.colorProjects(false);
					} else {
						$.RD.Map.colorProjects(true);
					}
				});

				$('#cluster').on("change", function () {
					if ($(this).is(":checked")) {
						console.log("awuifh");
						$.RD.projectPile.enable();
					} else {
						$.RD.projectPile.disable();
					}
				});

			}
		},
		init: function () {
			$.getJSON('lib/Generate.php', {refresh: false, antal: this.antal}, function (data) {
				$.RD.items.add(data.projects);
				$.RD.groups = data.groups;
				$.RD.Map.init();
				$.RD.Timeline.init();
				$.RD.Interactions.init();
				$.RD.Dashboard.init();
				/*
				 */
			})
		}
	}
})
(jQuery, vis, L, Handlebars, google, TweenMax);
