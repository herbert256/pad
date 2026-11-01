// orgchart.php in the browser: who is under whom (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  // $nodes, $boss, $place and $slot are Maps keyed by the id as text: two ids are one PHP
  // key exactly when their texts are the same, and the Map keeps the order of the rows.

  C.padChartOrg = function (rows, width, height) {

    // padMakeSafe ( $id, 40 ) of the messages: control characters and runs of white space a
    // space, cut to 40 bytes of whole characters, trimmed. PCRE's \s under /u is JavaScript's
    // without U+FEFF.

    var safe = function (text) {
      text = C.str(text).replace(/[\x00-\x1F\x7F-\x9F]/g, ' ').replace(/[^\S﻿]+/gu, ' ');
      if (C.strlen(text) > 40) {
        var cut = '';
        Array.from(text).some(function (ch) {
          if (C.strlen(cut + ch) > 40) return true;
          cut += ch;
          return false;
        });
        text = cut;
      }
      return C.trim(text);
    };

    var guess = C.padChartGuess(rows);
    var names = guess[1];

    var all    = C.values(rows);
    var first  = C.padChartRow((all.length && all[0] !== null && all[0] !== undefined) ? all[0] : new Map());
    var iField = C.str(C.padTagParm('id', (first instanceof Map && first.has('id')) ? 'id' : ''));
    var pField = C.str(C.padTagParm('parent', 'parent'));
    var sField = C.str(C.padTagParm('sub'));
    var used   = [iField, pField, sField].filter(function (f) { return f !== '' && f !== '0'; });
    var lField = C.padChartPick('label', names, used);

    if (iField === '')
      iField = lField;

    if (lField === '') {
      if (C.spec.check)
        C.padError("the org chart has no field for the names - label='name'");
      return '';
    }

    var title = C.str(C.padTagParm('title', 'Organisation chart'));

    var nodes = new Map();

    all.forEach(function (row) {

      row = C.padChartRow(row);
      var id = C.padChartText(row, iField);

      if (id === '')
        return;

      if (nodes.has(id)) {
        if (C.spec.check)
          C.padError("the org chart has the id '" + safe(id) + "' twice");
        return;
      }

      nodes.set(id, { name: C.padChartText(row, lField), sub: C.padChartText(row, sField),
                      parent: C.padChartText(row, pField), kids: [] });

    });

    if (!nodes.size)
      return '';

    var boss = new Map();

    nodes.forEach(function (node, id) {
      boss.set(id, (node.parent !== '' && node.parent !== id && nodes.has(node.parent)) ? node.parent : null);
    });

    var roots = [];

    nodes.forEach(function (node, id) {
      if (boss.get(id) !== null)
        nodes.get(boss.get(id)).kids.push(id);
      else
        roots.push(id);
    });

    var seen  = new Map();
    var stack = roots.slice();
    var id;

    while (stack.length) {
      id = stack.pop();
      seen.set(id, true);
      nodes.get(id).kids.forEach(function (kid) { stack.push(kid); });
    }

    if (seen.size < nodes.size) {

      if (C.spec.check) {
        var at = null;
        nodes.forEach(function (node, one) { if (at === null && !seen.has(one)) at = one; });
        var walked = new Map();
        while (at !== null && !walked.has(at)) {
          walked.set(at, true);
          at = boss.get(at);
        }
        C.padError("the org chart goes round in a circle through '" + safe(at) + "'");
      }

      var kept = new Map();
      nodes.forEach(function (node, one) { if (seen.has(one)) kept.set(one, node); });
      nodes = kept;

    }

    if (!roots.length)
      return '';

    var desc = [];
    nodes.forEach(function (node, one) {
      desc.push(node.name + (node.sub !== '' ? ', ' + node.sub : '')
              + (boss.get(one) !== null ? ' - under ' + nodes.get(boss.get(one)).name : ''));
    });

    var leaves = function (one) {
      var kids = nodes.get(one).kids;
      return kids.length > 0 && !kids.some(function (kid) { return nodes.get(kid).kids.length > 0; });
    };

    var list    = false;
    var columns = C.padChartOrgColumns(nodes, roots, list, leaves);

    if ((width - 20) / Math.max(1, columns) < 96) {
      list    = true;
      columns = C.padChartOrgColumns(nodes, roots, list, leaves);
    }

    width = Math.max(width, 20 + 96 * columns);

    var place = new Map();
    var next  = { at: 0 };

    roots.forEach(function (root) { C.padChartOrgPlace(nodes, root, 0, next, list, leaves, place); });

    var two   = (sField !== '');
    var colW  = (width - 20) / Math.max(1, columns);
    var boxW  = Math.max(30, Math.min(170, colW - 12));
    var boxH  = two ? 38 : 26;
    var spots = Array.from(place.values());
    var depth = C.hi(spots.map(function (p) { return p[1]; }));
    var under = C.hi(spots.map(function (p) { return p[2]; }));

    var listStep  = boxH + 8;
    var spare     = height - 20 - boxH * (depth + 1) - under * listStep;
    var levelStep = boxH + Math.max(16, Math.min(48, depth ? spare / depth : 0));

    height = Math.max(height, C.int(Math.ceil(12 + depth * levelStep + under * listStep + boxH)));

    var open = C.padChartOpen('orgchart', nodes, desc, title, width, height);
    var svg  = open[1];

    var box = function (one) {
      var p = place.get(one);
      var x = 10 + (p[0] + 0.5) * colW - boxW / 2;
      var y = 10 + p[1] * levelStep + p[2] * listStep;
      return p[2] ? [x + 14, y, boxW - 14] : [x, y, boxW];
    };

    var slot = new Map();
    var tops = (roots.length === 1) ? nodes.get(roots[0]).kids : roots;

    tops.forEach(function (top, i) {
      var todo = [top];
      while (todo.length) {
        var one = todo.pop();
        slot.set(one, i);
        nodes.get(one).kids.forEach(function (kid) { todo.push(kid); });
      }
    });

    nodes.forEach(function (node, one) {

      if (!node.kids.length)
        return;

      var parent = box(one);
      var px = parent[0], py = parent[1], pw = parent[2];
      var bottom = py + boxH;
      var d = '';

      if (place.get(node.kids[0])[2]) {
        var lx = px + 7;
        node.kids.forEach(function (kid) {
          var k = box(kid);
          d += 'M' + C.padChartXY(lx) + ',' + C.padChartXY(bottom) + 'V' + C.padChartXY(k[1] + boxH / 2) + 'H' + C.padChartXY(k[0]);
        });
        svg += '<path class="pc-edge" stroke-width="1.5" d="' + d + '"/>';
        return;
      }

      var mid = bottom + (levelStep - boxH) / 2;

      node.kids.forEach(function (kid) {
        var k = box(kid);
        d += 'M' + C.padChartXY(px + pw / 2) + ',' + C.padChartXY(bottom) + 'V' + C.padChartXY(mid)
           + 'H' + C.padChartXY(k[0] + k[2] / 2) + 'V' + C.padChartXY(k[1]);
      });

      svg += '<path class="pc-edge" stroke-width="1.5" d="' + d + '"/>';

    });

    nodes.forEach(function (node, one) {

      var b = box(one);
      var x = b[0], y = b[1], w = b[2];

      var i     = slot.has(one) ? slot.get(one) : -1;
      var fill  = (i < 0) ? 'pc-co' : C.padChartSlot(i);
      var line  = (i < 0 || i >= 8) ? 'pc-edge' : 'pc-l' + (i + 1);
      var at    = ' x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y) + '" width="' + C.padChartXY(w) + '" height="' + boxH + '" rx="6"';
      var below = (boss.get(one) !== null) ? ' - under ' + nodes.get(boss.get(one)).name : '';

      svg += '<rect class="pc-mark ' + fill + '" fill-opacity="0.16"' + at + '><title>'
           + C.padChartAttr(node.name + (node.sub !== '' ? ', ' + node.sub : '') + below) + '</title></rect>'
           + '<rect class="' + line + '" style="stroke-width:1.5" pointer-events="none"' + at + '/>';

      var tx = x + w / 2;

      if (two)
        svg += '<text x="' + C.padChartXY(tx) + '" y="' + C.padChartXY(y + 16) + '" text-anchor="middle" font-weight="600" pointer-events="none">' + C.padChartAttr(C.padChartFit(node.name, w - 10)) + '</text>'
             + '<text x="' + C.padChartXY(tx) + '" y="' + C.padChartXY(y + 30) + '" text-anchor="middle" opacity="0.8" pointer-events="none">' + C.padChartAttr(C.padChartFit(node.sub, w - 10)) + '</text>';
      else
        svg += '<text x="' + C.padChartXY(tx) + '" y="' + C.padChartXY(y + 17) + '" text-anchor="middle" font-weight="600" pointer-events="none">' + C.padChartAttr(C.padChartFit(node.name, w - 10)) + '</text>';

    });

    return svg + '</svg>';

  };

  C.padChartOrgColumns = function (nodes, ids, list, leaves) {

    var count = 0;

    ids.forEach(function (id) {
      if (!nodes.get(id).kids.length || (list && leaves(id)))
        count++;
      else
        count += C.padChartOrgColumns(nodes, nodes.get(id).kids, list, leaves);
    });

    return count;

  };

  // &$at is next.at.

  C.padChartOrgPlace = function (nodes, id, level, next, list, leaves, place) {

    var kids = nodes.get(id).kids;

    if (!kids.length) {
      place.set(id, [next.at++, level, 0]);
      return;
    }

    if (list && leaves(id)) {
      place.set(id, [next.at, level, 0]);
      kids.forEach(function (kid, row) { place.set(kid, [next.at, level, row + 1]); });
      next.at++;
      return;
    }

    kids.forEach(function (kid) { C.padChartOrgPlace(nodes, kid, level + 1, next, list, leaves, place); });

    place.set(id, [(place.get(kids[0])[0] + place.get(kids[kids.length - 1])[0]) / 2, level, 0]);

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
