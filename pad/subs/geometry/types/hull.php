<?php

  // hull - `padGeometry(kind, points, options=[])` works on `[x,y]` native finite numbers,
  // with absolute coordinate magnitude at most 1e100. Units belong to the caller;
  // longitude/latitude and earth models remain in `geospatial`. No SVG is generated.
  //
  // * `hull`: up to 10,000 points, duplicates removed, Graham-Andrew monotone chain.
  //   Returns counterclockwise `points` without a repeated closing vertex, starting
  //   lexicographically first. Collinear boundary points are omitted. Empty, singleton
  //   and collinear results have `status='degenerate'`.
  // * `area`: up to 2,000 ring vertices, returning `area`, `signedArea`, `orientation`,
  //   `vertices`, `status` and tolerance. A repeated final closing vertex is accepted.
  // * `centroid`: the same simple-ring contract, also returning the area centroid.
  //   Zero/near-zero area has `status='degenerate'` and a null centroid.
  // * `clip`: simple **convex** polygon against `box=[minX,minY,maxX,maxY]`, using
  //   four half-plane clips. Returns `status='ok'|'empty'` and points. Lower-dimensional
  //   intersections are empty. Concave polygons, holes and multipolygons are rejected
  //   explicitly; disconnected clipping results are not silently bridged.
  //
  // Area/centroid validate simplicity with bounded all-pairs edge tests. Nonadjacent
  // touches, crossings and consecutive duplicate vertices fail. Tolerance defaults
  // to 1e-12 (0..0.001), applied to coordinates translated to the first point and
  // scaled by the largest coordinate difference. It controls near-collinearity and
  // degeneracy; this floating-point implementation is not an exact-predicate geometry
  // kernel. Large translations are removed before moments accumulate. Overflow or
  // nonzero area underflow fails rather than returning a plausible zero.
  //
  // Algorithm/contract references: [CGAL 2D hull manual](https://doc.cgal.org/latest/Convex_hull_2/index.html)
  // and [simple polygon definitions](https://doc.cgal.org/latest/Polygon/index.html).
  // PAD has no CGAL runtime dependency. Tests use analytic shapes, translation/rotation
  // invariance and all subsets of a 3×3 integer grid checked by independent half planes.

  return ['call'=>fn($points,$options)=>padGeometryCompute('hull',$points,$options)];
?>
