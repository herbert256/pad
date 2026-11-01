<?php

  // A .kml file of _data/ read by its extension: the placemarks of Google's KML reference
  // examples as rows, in the order of the document, whatever folder they stand in - the
  // folder's path, the name, the description (its CDATA the HTML it is), the Data and
  // SimpleData of ExtendedData first, the id, the style, the TimeStamp and TimeSpan, and the
  // geometry as GeoJSON's reader has it: lat, lon and ele of a Point - a space after its comma
  // read - the coordinates of a LineString, of a Polygon (the inner ring not closed in the file
  // closed) and of a gx:Track; a placemark without a geometry has none, and the style, the
  // schema, the NetworkLink - never fetched - and the overlay are no rows.

  $kmlRows = json_encode ( padFormatRead ( 'kml', padFileGet ( __DIR__ . '/_data/trails.kml' ) ), JSON_UNESCAPED_SLASHES );

?>
