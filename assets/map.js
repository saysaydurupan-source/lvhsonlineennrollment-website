// Interactive LVHS campus map. x/y are pixel positions on the 937 x 562 map image.
const MAP_W = 937, MAP_H = 562, ZOOM_ON_SELECT = 3.2, ZOOM_MAX = 6;
const SPOTS = {
  1:[500,311,'Guard House'], 2:[381,297,'ALS Building'], 3:[500,294,'Parking Area'],
  4:[428,237,'SNED Room'], 5:[398,237,'SNED Room'], 6:[370,237,'ICT Room Junior'], 7:[338,237,'Library'],
  8:[310,237,'School Paper Office'], 9:[286,237,'Accounting Office'], 10:[246,237,'Administration Office'],
  11:[193,291,'Storage Room'], 12:[193,276,'Reading Center'], 13:[193,262,'Guidance Office'], 14:[193,247,'SSG Office'],
  15:[192,224,'Canteen 1'], 16:[192,207,'H.E. Building'], 17:[192,191,'Clinic'], 18:[195,130,'TVE Building'],
  19:[226,125,'TVE Building Senior'], 20:[146,167,'Electricity Building'], 21:[124,207,'Foods Building'],
  22:[107,272,'Garments Building'], 23:[158,273,'Automotive Building'], 24:[226,202,'ICT Building Senior'],
  25:[298,85,'SMAW Building Senior'], 26:[463,130,'Senior Building'], 27:[499,115,'Grade 9'],
  28:[658,124,'Grade 8'], 29:[536,157,'Grade 8'], 30:[622,157,'Grade 8'], 31:[585,175,'Grade 8 & 9'],
  32:[532,210,'Grade 10'], 33:[581,210,'Faculty Grade 10'], 34:[636,210,'Grade 10'], 35:[698,203,'Canteen 2'],
  36:[519,239,'Animation Room Junior'], 37:[604,239,'Grade 7'], 38:[779,225,'Canteen 3'], 39:[830,265,'Grade 7'],
  40:[604,291,'Grade 7'], 41:[422,117,'Temporary Room']
};

const view = document.getElementById('mapView'), stage = document.getElementById('mapStage');
const info = document.getElementById('mapInfo'), legend = document.getElementById('legend'), numInput = document.getElementById('num');
let s = 1, tx = 0, ty = 0, current = null;

const clamp = (v, lo, hi) => Math.min(hi, Math.max(lo, v));
function apply(){
  const w = view.clientWidth, h = view.clientHeight;
  tx = clamp(tx, w - w * s, 0); ty = clamp(ty, h - h * s, 0);
  stage.style.transform = `translate(${tx}px,${ty}px) scale(${s})`;
}
function zoomAt(cx, cy, ns){          // keep the point (cx,cy) of the view fixed while zooming
  ns = clamp(ns, 1, ZOOM_MAX);
  tx = cx - (cx - tx) * ns / s; ty = cy - (cy - ty) * ns / s; s = ns; apply();
}
function select(n, zoom = true){
  const sp = SPOTS[n]; if (!sp) return;
  current = n;
  document.querySelectorAll('.hs.on,.legend button.on').forEach(el => el.classList.remove('on'));
  document.querySelector(`.hs[data-n="${n}"]`).classList.add('on');
  const lb = document.querySelector(`.legend button[data-n="${n}"]`); lb.classList.add('on');
  lb.scrollIntoView({block:'nearest'});
  info.hidden = false; info.innerHTML = `<b>${n}</b>${sp[2]}`; numInput.value = n;
  if (zoom){
    const w = view.clientWidth, h = view.clientHeight;
    s = ZOOM_ON_SELECT; tx = w / 2 - (sp[0] / MAP_W * w) * s; ty = h / 2 - (sp[1] / MAP_H * h) * s; apply();
  }
}
function reset(){ s = 1; tx = ty = 0; apply(); }

// hotspots over every number on the map + the legend list
for (const [n, [x, y, name]] of Object.entries(SPOTS)){
  const hs = document.createElement('button');
  hs.className = 'hs'; hs.type = 'button'; hs.dataset.n = n; hs.title = `${n} - ${name}`; hs.setAttribute('aria-label', `${n} ${name}`);
  hs.style.left = (x / MAP_W * 100) + '%'; hs.style.top = (y / MAP_H * 100) + '%';
  hs.addEventListener('click', () => { if (!moved) select(+n); });
  stage.appendChild(hs);
  const b = document.createElement('button');
  b.type = 'button'; b.dataset.n = n; b.setAttribute('role', 'listitem'); b.innerHTML = `<b>${n}</b>${name}`;
  b.addEventListener('click', () => select(+n));
  legend.appendChild(b);
}

// drag to pan
let drag = null, moved = false;
view.addEventListener('pointerdown', e => { if (e.target.closest('.map-tools')) return; drag = {x: e.clientX, y: e.clientY, tx, ty}; moved = false; });
addEventListener('pointermove', e => {
  if (!drag) return;
  const dx = e.clientX - drag.x, dy = e.clientY - drag.y;
  if (!moved && Math.hypot(dx, dy) > 5){ moved = true; view.classList.add('dragging'); }
  if (moved){ tx = drag.tx + dx; ty = drag.ty + dy; apply(); }
});
addEventListener('pointerup', () => { drag = null; view.classList.remove('dragging'); setTimeout(() => moved = false); });

document.getElementById('zin').onclick = () => zoomAt(view.clientWidth / 2, view.clientHeight / 2, s * 1.5);
document.getElementById('zout').onclick = () => zoomAt(view.clientWidth / 2, view.clientHeight / 2, s / 1.5);
document.getElementById('zreset').onclick = () => { reset(); };
const go = () => { const n = parseInt(numInput.value, 10); if (SPOTS[n]) select(n); else numInput.focus(); };
document.getElementById('goBtn').onclick = go;
numInput.addEventListener('input', () => { if (SPOTS[parseInt(numInput.value, 10)]) go(); });
numInput.addEventListener('keydown', e => { if (e.key === 'Enter') go(); });
addEventListener('resize', () => current ? select(current) : apply());

const q = parseInt(new URLSearchParams(location.search).get('n'), 10);
if (SPOTS[q]) setTimeout(() => select(q), 300);
