<?php $title = 'Campus map';
require 'includes/header.php'; ?>
<h1 style="font-size:2.4rem">Campus map</h1>
<p class="lead">Type a number or pick one from the list. The map zooms in to that building or room. Drag the map to move
  around.</p>
<div class="map-layout">
  <div class="panel" style="padding:14px">
    <div class="map-view" id="mapView">
      <div class="map-stage" id="mapStage">
        <img src="assets/lvhs-map.png" alt="Lupon Vocational High School campus map" width="937" height="562">
      </div>
      <div class="map-tools">
        <button type="button" id="zin" aria-label="Zoom in">+</button>
        <button type="button" id="zout" aria-label="Zoom out">&minus;</button>
        <button type="button" id="zreset" aria-label="Show whole map" title="Show whole map">&#8962;</button>
      </div>
      <div class="map-info" id="mapInfo" hidden></div>
    </div>
  </div>
  <aside class="panel map-side" style="padding:20px">
    <div class="goto">
      <div><label for="num">Go to number</label><input id="num" type="number" min="1" max="41" inputmode="numeric"
          placeholder="1 - 41"></div>
      <button class="btn small" type="button" id="goBtn">Zoom</button>
    </div>
    <div class="legend" id="legend" role="list"></div>
  </aside>
</div>
<script src="assets/map.js"></script>
<?php require 'includes/footer.php'; ?>