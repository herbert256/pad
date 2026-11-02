# Maps

Map kinds are the geographic views in `types/`. Country outlines, regional outlines, palette and linear/quantile choropleth scales share `_lib/map.php`.

Geographic marker and bubble overlays use the same Natural Earth projection as the world and regional country views. `_lib/markers.php` normalizes rows with `padMapMarkers($rows, $view, $latitude, $longitude, $label, $value)`; fields default to `latitude`, `longitude`, `label`, with no value field. Pass the normalized result as the optional final argument of `padMap($view, $values, $title, $width, $height, $scale, $markers)`.

Markers have a five-pixel radius. With a value field, bubble area is proportional to the value and the largest visible bubble has an 18-pixel radius. Zero values have no area; negative, nonnumeric and nonfinite values are rejected. Larger bubbles are drawn first so smaller neighbours stay visible. Labels appear as escaped SVG titles and in the accessible map description. Points outside the view's geographic/projected bounds are omitted and counted in the title; outside points do not affect bubble scaling.

Coordinates must be finite latitude −90…90 and longitude −180…180. A map accepts at most 500 marker rows. State/province views use separately projected outlines and sometimes insets; geographic marker overlays are rejected for those views rather than placing points with an unrelated projection. Invalid data raises the normal PAD syntax error; the lenient renderer skips invalid rows.

The template entrypoint can select marker/bubble mode while retaining the existing view, data, width, height and title options. The PHP functions support markers alongside choropleth values when both datasets are supplied programmatically.
