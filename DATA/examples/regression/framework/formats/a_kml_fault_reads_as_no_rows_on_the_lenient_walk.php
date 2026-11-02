<?php

  // With the strict check off a KML text that cannot be read - a DOCTYPE, a position that is
  // none, a root that is no <kml> - is no rows and no error; a row whose geometry has no
  // positions is written as a placemark without one, without a word.

  $padCheckSyntax = FALSE;

  $kmlTexts = [
    "<!DOCTYPE kml [ <!ENTITY a \"aaaa\"> ]>\n<kml xmlns=\"http://www.opengis.net/kml/2.2\"><Placemark><name>&a;</name></Placemark></kml>",
    "<kml xmlns=\"http://www.opengis.net/kml/2.2\"><Placemark><Point><coordinates>a,b</coordinates></Point></Placemark></kml>",
    "<Document><Placemark><name>x</name></Placemark></Document>"
  ];

  $kmlCounts = implode ( ' ', array_map ( fn ( $text ) => count ( padFormatRead ( 'kml', $text ) ) . ' rows', $kmlTexts ) );
  $kmlLeft   = substr_count ( padFormatWrite ( 'kml', [ [ 'name' => 'Leie', 'geometry' => 'LineString', 'coordinates' => [ [ 3.71 ] ] ] ] ), '<LineString>' ) . ' lines';

?>
