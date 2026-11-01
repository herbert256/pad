<?php

  // The DOCTYPE of a KML text in UTF-16 - which a search of its bytes for <!DOCTYPE never saw
  // between their NULs - is refused as one in UTF-8 is, its entities never read.

  $kmlText = "\xFE\xFF" . mb_convert_encoding ( "<?xml version=\"1.0\" encoding=\"UTF-16\"?>\n<!DOCTYPE kml [ <!ENTITY a \"aaaa\"> ]>\n"
                                               . '<kml xmlns="http://www.opengis.net/kml/2.2"><Placemark><name>&a;</name></Placemark></kml>', 'UTF-16BE', 'UTF-8' );

  $kmlRows = count ( padFormatRead ( 'kml', $kmlText ) );

?>
