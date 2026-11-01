// pack.php in the browser: circles in circles (core.js says how these files are written).
// needs: core, hierarchy

(function (C) {

  'use strict';

  // $x ** 2: C's pow ( x, 2 ), the square correctly rounded, which x * x is.

  function sq(x) { return x * x; }

  C.padChartPack = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var groups = C.padChartFields(C.padTagParm('levels'));
    var used   = groups.slice();
    var lField = C.padChartPick('label', names,   used);
    var vField = C.padChartPick('value', numbers, used);

    var title = C.str(C.padTagParm('title', C.ucfirst(vField !== '' ? vField : 'value') + (groups.length ? ' by ' + groups.join(' and ') : (lField !== '' ? ' by ' + lField : ''))));

    var root = C.padChartPackTree(rows, groups, lField, vField);

    if (!root.children.length || !isFinite(root.value))
      return '';

    var desc = [];
    C.padChartHierarchyDesc(root, [], desc);

    var open = C.padChartOpen('pack', root, desc, title, width, height);
    var svg  = open[1];

    var legendH = 0;

    if (groups.length && root.children.length > 1) {
      var legend = C.padChartLegend(root.children.map(function (c) { return c.name; }), 10, 6, width - 20);
      svg     += legend[0];
      legendH  = legend[1] + 4;
    }

    var size  = Math.max(10, Math.min(width, height - legendH) / 2 - 4);
    var first = C.padChartPackNode(root, 0);
    var pad   = 3 / (size / Math.max(1e-9, first.r));
    root      = C.padChartPackNode(root, pad);
    var k     = size / Math.max(1e-9, root.r);
    var cx    = width / 2;
    var cy    = legendH + (height - legendH) / 2;

    return svg + C.padChartPackDraw(root, cx, cy, k, 0, 'pc-c1', []) + '</svg>';

  };

  // The tree as padChartHierarchy makes it, then sorted: here children is a list.

  C.padChartPackTree = function (rows, groups, lField, vField) {

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

    return C.padChartPackSort(root);

  };

  // usort by [ value, name ] <=> : the larger value first, then the names in PHP's order.

  C.padChartPackSort = function (node) {

    var children = C.values(node.children).map(C.padChartPackSort);

    children.sort(function (a, b) { return C.cmp(b.value, a.value) || C.cmp(a.name, b.name); });

    return { name: node.name, value: node.value, children: children };

  };

  // A copy of the node with its radius r, and x and y on each child.

  C.padChartPackNode = function (node, pad) {

    var out = { name: node.name, value: node.value, children: node.children };

    if (!node.children.length) {
      out.r = Math.sqrt(node.value);
      return out;
    }

    var circles = [];

    out.children = node.children.map(function (child) {
      var placed = C.padChartPackNode(child, pad);
      circles.push([0, 0, placed.r + pad]);
      return placed;
    });

    var packed = C.padChartPackSiblings(circles);

    packed[0].forEach(function (circle, i) {
      out.children[i].x = circle[0];
      out.children[i].y = circle[1];
    });

    out.r = packed[1] + pad;

    return out;

  };

  C.padChartPackSiblings = function (c) {

    var n = c.length;

    c = c.map(function (circle) { return circle.slice(); });

    if (n === 1)
      return [[[0, 0, c[0][2]]], c[0][2]];

    c[0][0] = -c[1][2];
    c[1][0] =  c[0][2];
    c[0][1] = c[1][1] = 0;

    if (n === 2) {
      var mid = (c[0][0] + c[1][0]) / 2 + (c[1][2] - c[0][2]) / 2;
      c.forEach(function (circle) { circle[0] -= mid; });
      return [c, c[0][2] + c[1][2]];
    }

    c[2] = C.padChartPackPlace(c[1], c[0], c[2]);

    var next = [1, 2, 0];
    var prev = [2, 0, 1];
    var a    = 0;
    var b    = 1;

    var hits = function (p, q) { return (p[2] + q[2] - 1e-6 > 0) && (sq(p[2] + q[2] - 1e-6) > sq(q[0] - p[0]) + sq(q[1] - p[1])); };

    var score = function (i) {
      var p  = c[i];
      var q  = c[next[i]];
      var ab = p[2] + q[2];
      var dx = (p[0] * q[2] + q[0] * p[2]) / ab;
      var dy = (p[1] * q[2] + q[1] * p[2]) / ab;
      return dx * dx + dy * dy;
    };

    for (var i = 3; i < n; i++) {

      c[i] = C.padChartPackPlace(c[a], c[b], c[i]);

      var j  = next[b];
      var k  = prev[a];
      var sj = c[b][2];
      var sk = c[a][2];
      var redo = false;

      do {
        if (sj <= sk) {
          if (hits(c[j], c[i])) {
            b = j;
            next[a] = b;
            prev[b] = a;
            redo = true;
            break;
          }
          sj += c[j][2];
          j   = next[j];
        } else {
          if (hits(c[k], c[i])) {
            a = k;
            next[a] = b;
            prev[b] = a;
            redo = true;
            break;
          }
          sk += c[k][2];
          k   = prev[k];
        }
      } while (j !== next[k]);

      if (redo) {
        i--;
        continue;
      }

      prev[i] = a;
      next[i] = b;
      next[a] = i;
      prev[b] = i;
      b = i;

      var best = score(a);
      for (var m = next[b]; m !== b; m = next[m]) {
        var s = score(m);
        if (s < best) {
          a    = m;
          best = s;
        }
      }

      b = next[a];

    }

    var chain = [b];
    for (var at = next[b]; at !== b; at = next[at])
      chain.push(at);

    var enclose = C.padChartPackEnclose(chain.map(function (one) { return c[one]; }));

    c.forEach(function (circle) {
      circle[0] -= enclose[0];
      circle[1] -= enclose[1];
    });

    return [c, enclose[2]];

  };

  C.padChartPackPlace = function (b, a, c) {

    var dx = b[0] - a[0];
    var dy = b[1] - a[1];
    var d2 = dx * dx + dy * dy;

    if (!d2)
      return [a[0] + c[2], a[1], c[2]];

    var a2 = sq(a[2] + c[2]);
    var b2 = sq(b[2] + c[2]);
    var x, y;

    if (a2 > b2) {
      x = (d2 + b2 - a2) / (2 * d2);
      y = Math.sqrt(Math.max(0, b2 / d2 - x * x));
      return [b[0] - x * dx - y * dy, b[1] - x * dy + y * dx, c[2]];
    }

    x = (d2 + a2 - b2) / (2 * d2);
    y = Math.sqrt(Math.max(0, a2 / d2 - x * x));

    return [a[0] + x * dx - y * dy, a[1] + x * dy + y * dx, c[2]];

  };

  C.padChartPackEnclose = function (circles) {

    var reach = function (x, y, p) { return Math.sqrt(sq(p[0] - x) + sq(p[1] - y)) + p[2]; };

    var x = (C.lo(circles.map(function (p) { return p[0] - p[2]; })) + C.hi(circles.map(function (p) { return p[0] + p[2]; }))) / 2;
    var y = (C.lo(circles.map(function (p) { return p[1] - p[2]; })) + C.hi(circles.map(function (p) { return p[1] + p[2]; }))) / 2;

    for (var round = 1; round <= 200; round++) {

      var far = circles[0];
      var max = -Infinity;

      circles.forEach(function (p) {
        var r = reach(x, y, p);
        if (r > max) {
          max = r;
          far = p;
        }
      });

      var d  = Math.sqrt(sq(far[0] - x) + sq(far[1] - y));
      var px = (d > 0) ? far[0] + (far[0] - x) / d * far[2] : far[0] + far[2];
      var py = (d > 0) ? far[1] + (far[1] - y) / d * far[2] : far[1];

      x += (px - x) / (round + 1);
      y += (py - y) / (round + 1);

    }

    var r = 0;
    circles.forEach(function (p) { r = Math.max(r, reach(x, y, p)); });

    return [x, y, r];

  };

  C.padChartPackDraw = function (node, x, y, k, depth, slot, path) {

    var svg = '';

    node.children.forEach(function (child, i) {

      var cx    = x + child.x * k;
      var cy    = y + child.y * k;
      var r     = Math.max(0.5, child.r * k);
      var kids  = child.children.length > 0;
      var klass = depth ? slot : (kids ? C.padChartSlot(i) : 'pc-c1');
      var trail = path.concat([child.name]);
      var tip   = '<title>' + C.padChartAttr(trail.join(' › ') + ': ' + C.padChartNumber(child.value)) + '</title>';
      var at    = ' cx="' + C.padChartXY(cx) + '" cy="' + C.padChartXY(cy) + '" r="' + C.padChartXY(r) + '"';

      if (kids) {
        svg += '<circle class="' + klass + '" fill-opacity="0.14"' + at + '>' + tip + '</circle>'
             + C.padChartPackDraw(child, cx, cy, k, depth + 1, klass, trail);
        return;
      }

      svg += '<circle class="pc-mark pc-ring ' + klass + '"' + at + '>' + tip + '</circle>';

      var name = child.name;

      if (r >= 14 && C.padChartWidth(name) <= 2 * r - 6) {
        var two = (r >= 24);
        svg += '<text class="pc-in" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy + (two ? -1 : 4)) + '" text-anchor="middle" pointer-events="none">' + C.padChartAttr(name) + '</text>';
        if (two)
          svg += '<text class="pc-in" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy + 12) + '" text-anchor="middle" pointer-events="none">' + C.padChartAttr(C.padChartFit(C.padChartNumber(child.value), 2 * r - 6)) + '</text>';
      }

    });

    return svg;

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
