// Builds an outlines file of pad/subs/maps/_lib/ from Natural Earth's admin-1 layer.
//
//   node build.mjs <view-config.json> <out.json>
//
// The config: { "shp": path, "filter": js expression (mapshaper -filter), "key": js expression
// giving the key of a feature (iso_3166_2 for a region, "~ADM0" for land around),
// "explodeFilter": optional js expression over exploded parts (cx, cy = centroid lon/lat),
// "proj": proj string, "clip": optional [ marginFraction ] around the regions' extent,
// "width": units of the widest side, "detail": grid, "plain": grid, meta... }

const {default: mapshaper} = await import(process.env.MAPSHAPER_MODULE || 'mapshaper');
import { readFileSync, writeFileSync } from 'node:fs';

const cfg = JSON.parse(readFileSync(process.argv[2], 'utf8'));
const out = process.argv[3];
cfg.shp = process.argv[4] || cfg.shp;

async function run(cmd, input) {
  const files = await mapshaper.applyCommands(cmd, input);
  return files;
}

// 1. Selected, keyed, exploded/filtered, dissolved, projected - one GeoJSON in projected metres.

let cmd = `-i ${cfg.shp} -filter '${cfg.filter}' -each 'key = ${cfg.key}' -filter-fields key`;
if (cfg.explodeFilter)
  cmd += ` -explode -each 'cx = this.centroidX, cy = this.centroidY' -filter '${cfg.explodeFilter}'`;
cmd += ` -dissolve key -proj ${cfg.proj} -o format=geojson base.json`;
let files = await run(cmd);
const base = JSON.parse(files['base.json']);

// 2. The extent of the regions (not the land around), with a margin; the land around is clipped to it.

function bounds(features, test) {
  let b = [Infinity, Infinity, -Infinity, -Infinity];
  for (const f of features) {
    if (!test(f)) continue;
    const walk = (c) => { if (typeof c[0] === 'number') { b[0] = Math.min(b[0], c[0]); b[1] = Math.min(b[1], c[1]); b[2] = Math.max(b[2], c[0]); b[3] = Math.max(b[3], c[1]); } else c.forEach(walk); };
    walk(f.geometry.coordinates);
  }
  return b;
}

const region = (f) => !String(f.properties.key).startsWith('~');
let ext = bounds(base.features, region);
if (cfg.margin) {
  const m = cfg.margin * Math.max(ext[2] - ext[0], ext[3] - ext[1]);
  ext = [ext[0] - m, ext[1] - m, ext[2] + m, ext[3] + m];
}

// 3. Simplified twice - full detail and plain - topology kept (one layer, shared arcs).

async function level(grid) {
  let c = `-i base.json -clip bbox=${ext.join(',')} -simplify ${cfg.method || ''} resolution=${grid} keep-shapes -o format=geojson precision=0.01 lvl.json`;
  const f = await run(c, { 'base.json': JSON.stringify(base) });
  return JSON.parse(f['lvl.json']);
}

const detail = await level(cfg.detail);
const plain  = await level(cfg.plain);

// 4. Into units: the widest side of the extent is cfg.width units, y downwards, rounded; relative
//    paths in the compact form of world.json.

const scale = cfg.width / Math.max(ext[2] - ext[0], ext[3] - ext[1]);
const X = (x) => Math.round((x - ext[0]) * scale);
const Y = (y) => Math.round((ext[3] - y) * scale);

function rings(geom) {
  if (!geom) return [];
  if (geom.type === 'Polygon') return geom.coordinates;
  if (geom.type === 'MultiPolygon') return geom.coordinates.flat();
  return [];
}

function num(n, first) { return (n < 0 || first) ? String(n) : ',' + n; }

function path(geom) {
  let d = '';
  let box = [Infinity, Infinity, -Infinity, -Infinity];
  for (const ring of rings(geom)) {
    const pts = [];
    for (const [x, y] of ring) {
      const p = [X(x), Y(y)];
      const last = pts[pts.length - 1];
      if (!last || last[0] !== p[0] || last[1] !== p[1]) pts.push(p);
    }
    if (pts.length > 1 && pts[0][0] === pts[pts.length - 1][0] && pts[0][1] === pts[pts.length - 1][1]) pts.pop();
    // area after rounding
    let a = 0;
    for (let i = 0; i < pts.length; i++) { const p = pts[i], q = pts[(i + 1) % pts.length]; a += p[0] * q[1] - q[0] * p[1]; }
    if (pts.length < 3 || a === 0) continue;
    let s = 'M' + pts[0][0] + ',' + pts[0][1] + 'l';
    for (let i = 1; i < pts.length; i++) {
      const dx = pts[i][0] - pts[i - 1][0], dy = pts[i][1] - pts[i - 1][1];
      s += (i === 1 ? String(dx) : (dx < 0 ? String(dx) : ' ' + dx)) + (dy < 0 ? String(dy) : ',' + dy);
    }
    d += s + 'z';
    for (const p of pts) { box[0] = Math.min(box[0], p[0]); box[1] = Math.min(box[1], p[1]); box[2] = Math.max(box[2], p[0]); box[3] = Math.max(box[3], p[1]); }
  }
  return [d, box];
}

function centroidBox(geom) {
  const b = bounds([{ geometry: geom }], () => true);
  return [X((b[0] + b[2]) / 2), Y((b[1] + b[3]) / 2)];
}

function diamond([cx, cy]) { return `M${cx},${cy - 3}l3,3-3,3-3-3z`; }

const regions = {}, context = {};
const plainBy = Object.fromEntries(plain.features.map((f) => [f.properties.key, f.geometry]));
const order = cfg.order || [];
const feats = detail.features.slice().sort((a, b) => String(a.properties.key).localeCompare(String(b.properties.key)));

for (const f of feats) {
  const key = f.properties.key;
  let [d, box] = path(f.geometry);
  let [p] = path(plainBy[key]);
  if (d === '') { const c = centroidBox(f.geometry); d = diamond(c); box = [c[0] - 3, c[1] - 3, c[0] + 3, c[1] + 3]; }
  if (p === '') p = diamond([Math.round((box[0] + box[2]) / 2), Math.round((box[1] + box[3]) / 2)]);
  if (region(f)) {
    const meta = cfg.regions[key];
    if (!meta) { console.error('no meta for', key); continue; }
    regions[key] = [meta[0], meta[1], meta[2] || [], box, d, p];
  } else {
    const code = key.slice(1);
    context[key] = ['', cfg.context?.[code] || code, [], box, d, p];
  }
}

for (const [key, meta] of Object.entries(cfg.regions))
  if (!regions[key]) {
    if (!meta[3]) console.error('missing outline', key);
    regions[key] = [meta[0], meta[1], meta[2] || [], [0, 0, 0, 0], '', ''];
  }

const W = X(ext[2]), H = Y(ext[1]);

const json = {
  _source: cfg.source,
  _projection: cfg.projection,
  _format: 'regions: ISO 3166-2 code => [ the code without its country, name, other names, [ x0, y0, x1, y1 ], path, path simplified for a small map ] - paths in relative coordinates of whole units, y downwards, a region too small to keep its outline a diamond of 3 units round its middle, one without an outline in this view an empty path; _box: the box the view shows; context: the land around the regions, the same, under ~ and its ISO alpha-3 code, drawn without values',
  _box: [0, 0, W, H],
  regions: Object.fromEntries(Object.keys(cfg.regions).sort().map((k) => [k, regions[k]])),
  context,
};

writeFileSync(out, JSON.stringify(json));
const size = readFileSync(out).length;
console.log(out, size, 'bytes', 'box', W, H, 'regions', Object.keys(regions).length, 'context', Object.keys(context).length);
