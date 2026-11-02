# geospatial: simplify

Douglas–Peucker on a sphere of radius 6371008.8 m, using minimum surface distance from each input vertex to the minor great-circle arc between retained endpoints. Tolerance is explicit metres (0..20000000). Preserves endpoint coordinates; handles antimeridian/poles in 3-D and rejects ambiguous antipodal candidate arcs. Does not preserve topology or bound every point of an interpolated original curve. Input LineString 2..5000 vertices; maxWork defaults 2000000 (≤5000000 distance evaluations), exceeding it fails rather than returning a partial geometry.

Runnable example: `apps/tags/examples/geo/simplify.pad`. Independent fixture: `php tests/subsystems/expansion-geospatial-simplify.php`.
