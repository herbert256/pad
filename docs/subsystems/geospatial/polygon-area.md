# geospatial: polygon-area

Sphere radius 6371008.8 m; result includes model, radius and boundary semantics. Computes the exact surface integral for edges linear in unwrapped longitude/latitude (parallels/meridians for boxes), rather than geodesic great-circle boundaries. Ring orientation is ignored; holes subtract. Reuses Polygon validation (≤1000 vertices, closed, nonintersecting rings, strict holes); excludes pole vertices, pole-enclosing and ≥180° longitude-span rings. Narrow antimeridian crossing works via unwrapping. Units m2, km2 or ha.

Runnable example: `apps/tags/examples/geo/polygon-area.pad`. Independent fixture: `php tests/subsystems/expansion-geospatial-polygon-area.php`.
