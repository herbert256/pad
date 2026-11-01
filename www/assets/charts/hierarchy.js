// hierarchy.php in the browser: sunburst and treemap, and the tree and the description pack
// uses too (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  // A node of the tree: { name, value, children } - children a Map, name => node, in the
  // order the rows made them; a name is text, which keys a Map as PHP keys the array.

  C.padChartHierarchy = function (kind, rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var groups = C.padChartFields(C.padTagParm('levels'));
    var used   = groups.slice();
    var lField = C.padChartPick('label', names,   used);
    var vField = C.padChartPick('value', numbers, used);

    var title = C.str(C.padTagParm('title', C.ucfirst(vField !== '' ? vField : 'value') + (groups.length ? ' by ' + groups.join(' and ') : (lField !== '' ? ' by ' + lField : ''))));

    var root  = { name: '', value: 0, children: new Map() };
    var index = 0;

    C.entries(rows).forEach(function (entry) {

      var key = entry[0];
      index++;
      var row = C.padChartRow(entry[1]);
      var v   = C.get(row, vField);

      if (!C.padChartFinite(v) || C.float(v) <= 0)
        return;

      v = C.num(v);

      var path = groups.map(function (group) { return C.padChartText(row, group); });

      if (lField !== '')
        path.push(C.padChartText(row, lField));
      else if (!groups.length)
        path.push(typeof key === 'string' ? key : String(index));

      var node = root;
      node.value += v;

      path.forEach(function (name) {
        if (!node.children.has(name))
          node.children.set(name, { name: C.str(name), value: 0, children: new Map() });
        node = node.children.get(name);
        node.value += v;
      });

    });

    if (!root.children.size || !isFinite(root.value))
      return '';

    var desc = [];
    C.padChartHierarchyDesc(root, [], desc);

    var open = C.padChartOpen(kind, root, desc, title, width, height);
    var svg  = open[1];

    if (kind === 'sunburst')
      return svg + C.padChartSunburst(root, groups.length + 1, width, height) + '</svg>';

    return svg + C.padChartTreemap(root, 0, 0, 0, width, height, 0) + '</svg>';

  };

  C.padChartHierarchyDesc = function (node, path, desc) {

    C.values(node.children).forEach(function (child) {
      if (C.count(child.children))
        C.padChartHierarchyDesc(child, path.concat([child.name]), desc);
      else
        desc.push(path.concat([child.name]).join(' › ') + ': ' + C.padChartNumber(child.value));
    });

  };

  C.padChartTreemap = function (node, depth, x, y, w, h, slot, path) {

    if (path === undefined) path = [];

    var svg      = '';
    var children = C.values(node.children);
    var rects    = C.padChartSquarify(children.map(function (c) { return c.value; }), x, y, w, h);

    children.forEach(function (child, i) {

      var cx = rects[i][0], cy = rects[i][1], cw = rects[i][2], ch = rects[i][3];

      var kids  = C.count(child.children) > 0;
      var klass = depth ? slot : (kids ? C.padChartSlot(i) : 'pc-c1');
      var trail = path.concat([child.name]);

      if (kids) {
        svg += C.padChartTreemap(child, depth + 1, cx + 1, cy + 1, Math.max(0, cw - 2), Math.max(0, ch - 2), klass, trail)
             + '<rect class="pc-frame" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy) + '" width="' + C.padChartXY(cw) + '" height="' + C.padChartXY(ch) + '"/>';
        return;
      }

      svg += '<rect class="pc-mark pc-gap ' + klass + '" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy) + '" width="' + C.padChartXY(cw) + '" height="' + C.padChartXY(ch) + '"><title>'
           + C.padChartAttr(trail.join(' › ') + ': ' + C.padChartNumber(child.value)) + '</title></rect>';

      if (cw >= 40 && ch >= 24) {
        svg += '<text class="pc-in" x="' + C.padChartXY(cx + 5) + '" y="' + C.padChartXY(cy + 15) + '" pointer-events="none">' + C.padChartAttr(C.padChartFit(child.name, cw - 10)) + '</text>';
        if (ch >= 38)
          svg += '<text class="pc-in" x="' + C.padChartXY(cx + 5) + '" y="' + C.padChartXY(cy + 29) + '" pointer-events="none">' + C.padChartAttr(C.padChartFit(C.padChartNumber(child.value), cw - 10)) + '</text>';
      }

    });

    return svg;

  };

  // Answers [ x, y, width, height ] per value, by its index in the list of values.

  C.padChartSquarify = function (values, x, y, w, h) {

    var total = values.reduce(function (s, v) { return s + v; }, 0);
    var rects = [];

    if (total <= 0 || w <= 0 || h <= 0) {
      values.forEach(function (value, i) { rects[i] = [x, y, 0, 0]; });
      return rects;
    }

    // arsort: largest first, equal areas in their order.

    var areas = values.map(function (value, i) { return [i, value / total * w * h]; });
    areas.sort(function (a, b) { return a[1] < b[1] ? 1 : (a[1] > b[1] ? -1 : 0); });

    var area = [];
    areas.forEach(function (e) { area[e[0]] = e[1]; });

    var sum = function (list) { return list.reduce(function (s, v) { return s + v; }, 0); };

    var worst = function (row, side) {
      var s = sum(row), most = C.hi(row), least = C.lo(row);
      if (s <= 0 || side <= 0 || least <= 0)
        return Infinity;
      return Math.max(side * side * most / (s * s), s * s / (side * side * least));
    };

    var row  = [];
    var keys = areas.map(function (e) { return e[0]; });

    var rowValues = function (more) { return row.map(function (i) { return area[i]; }).concat(more); };

    while (keys.length || row.length) {

      var side = Math.min(w, h);
      var key  = keys.length ? keys[0] : null;

      if (key !== null && (!row.length || worst(rowValues([area[key]]), side) <= worst(rowValues([]), side))) {
        row.push(key);
        keys.shift();
        continue;
      }

      var rowSum = sum(rowValues([]));
      var thick, at;

      if (w >= h) {
        thick = (h > 0) ? rowSum / h : 0;
        at    = y;
        row.forEach(function (i) {
          var length = (thick > 0) ? area[i] / thick : 0;
          rects[i] = [x, at, thick, length];
          at += length;
        });
        x += thick;
        w -= thick;
      } else {
        thick = (w > 0) ? rowSum / w : 0;
        at    = x;
        row.forEach(function (i) {
          var length = (thick > 0) ? area[i] / thick : 0;
          rects[i] = [at, y, length, thick];
          at += length;
        });
        y += thick;
        h -= thick;
      }

      row = [];

    }

    return rects;

  };

  C.padChartSunburst = function (root, levels, width, height) {

    var radius = Math.max(10, Math.min(width, height) / 2 - 6);
    var cx     = width / 2;
    var cy     = height / 2;
    var hole   = radius * 0.28;
    var ring   = (radius - hole) / levels;

    var svg   = '';
    var stack = [];
    var a     = 0;

    C.values(root.children).forEach(function (child, i) {
      var sweep = child.value / root.value * 2 * Math.PI;
      stack.push([child, 0, a, a + sweep, C.padChartSlot(i), [child.name]]);
      a += sweep;
    });

    while (stack.length) {

      var item = stack.shift();
      var node = item[0], depth = item[1], a0 = item[2], a1 = item[3], slot = item[4], path = item[5];

      var d     = C.padChartArc(cx, cy, hole + depth * ring, hole + (depth + 1) * ring, a0, a1);
      var light = (depth > 0) ? ' pc-dp' + Math.min(3, depth) : '';

      svg += '<path class="pc-mark pc-seg ' + slot + light + '" fill-rule="evenodd" d="' + d + '"><title>'
           + C.padChartAttr(path.join(' › ') + ': ' + C.padChartNumber(node.value)) + '</title></path>';

      var at = a0;

      C.values(node.children).forEach(function (child) {
        var sweep = (node.value > 0) ? child.value / node.value * (a1 - a0) : 0;
        stack.push([child, depth + 1, at, at + sweep, slot, path.concat([child.name])]);
        at += sweep;
      });

    }

    return svg + '<text class="pc-big" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy + 6) + '" text-anchor="middle">' + C.padChartNumber(root.value) + '</text>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
