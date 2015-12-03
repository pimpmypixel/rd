<!DOCTYPE HTML>
<html>
<head>
	<title>RD</title>
	<link href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" rel="stylesheet"/>
	<link href="../libs/vis/dist/vis.css" rel="stylesheet"/>
	<link href="../libs/Metro-UI-CSS/build/css/metro.css" rel="stylesheet">
	<link href="../libs/Metro-UI-CSS/build/css/metro-icons.css" rel="stylesheet">
	<link href="../libs/Metro-UI-CSS/build/css/metro-responsive.css" rel="stylesheet">
	<link href="../libs/Metro-UI-CSS/build/css/metro-schemes.css" rel="stylesheet">
	<link href="lib/rd.css" rel="stylesheet">
</head>
<body>
<div id="logo"><img src="lib/logo-filled.svg" width="5%" style="float: right"></div>
<div id="map"></div>
<div id="controls">
	<ul id="programmer">
		<li id="resetMap">
			<button class="button mini-button">Nulstil kort</button>
		</li>
		<li id="resetTimeline">
			<button class="button mini-button">Nulstil tidslinje</button>
		</li>
		<li>
			<label class="input-control checkbox small-check">
				<input id="farver" type="checkbox" checked data-show="indeterminate">
				<span class="check"></span>
				<span class="caption">Farvede programmer</span>
			</label>
		</li>
		<li>
			<label class="input-control checkbox small-check">
				<input id="cluster" type="checkbox" checked data-show="indeterminate">
				<span class="check"></span>
				<span class="caption">Gruppér projekter</span>
			</label>
		</li>
		<li id="search">
			<div class="input-control text" data-role="input">
				<input id="searchproject" type="text" placeholder="Søg projekt">
				<button class="button helper-button clear"><span class="mif-cross"></span></button>
			</div>
			<div id="results">
				<dl></dl>
			</div>
		</li>
		<li>

			<label>Radius:</label>
			<input type="number" id="radius" class="span1"/>
			<br/>
			<label>Max Zoom Change:</label>
			<input type="number" id="maxZoomChange" class="span1"/>
			<br/>
			<label>Max Zoom Level:</label>
			<input type="number" id="maxZoomLevel" class="span1"/>
			<br/>
			<label>Auto Enable:</label>
			<input type="checkbox" id="autoEnable"/>
			<br/>
			<label>Single Piles:</label>
			<input type="checkbox" id="singlePiles"/>
			<br/>

			<br/><br/>
			<button id="addrand">Add</button>
			<button id="rmvrand">Remove</button>
			<button id="enable">Enable</button>
			<button id="disable">Disable</button>
			<button id="popup">Popup</button>
		</li>
	</ul>
</div>
<div id="info" style="float: right; vertical-align: top"></div>
<div style="clear:both"></div>
<div id="street"></div>


<div style="clear:both"></div>
<div id="timeline"></div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/leaflet.js"></script>
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/pouchdb/5.1.0/pouchdb.min.js"></script>
<script src="../libs/L.TileLayer.PouchDBCached.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/0.4.0/leaflet.markercluster.js"></script>
-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.10.0/vis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js"></script>
<script src="http://maps.google.com/maps/api/js?v=3.2&sensor=false"></script>
<script src="http://maps.stamen.com/js/tile.stamen.js?v1.3.0"></script>
<script src="../libs/Metro-UI-CSS/build/js/metro.js"></script>
<script src="../libs/leafpile.js"></script>
<script id="info-template" type="text/x-handlebars-template">
	<table>
		<tr>
			<td width="30%">Program</td>
			<td>
				<div
					style="height:10px; width:10px; background-color:#{{color}}; display: inline; float: left; padding-right: 5px"></div>
				<div style="display: inline;margin-left: 10px;">{{group}}</div>
			</td>
		</tr>
		<tr>
			<td>Navn</td>
			<td>{{navn}}</td>
		</tr>
		<tr>
			<td>Start</td>
			<td>{{start}}</td>
		</tr>
		<tr>
			<td>Slut</td>
			<td>{{end}}</td>
		</tr>
		<tr>
			<td>Adresse</td>
			<td>{{adresse}}</td>
		</tr>
		<tr>
			<td>Bevilling</td>
			<td>{{bevilget}}</td>
		</tr>
		<tr>
			<td colspan="2"></td>
		</tr>
	</table>
</script>
<script src="lib/rd.js"></script>
<script>$.RD.init()</script>
</html>