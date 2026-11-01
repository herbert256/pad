<?php

  // A <!DOCTYPE in a placemark's CDATA - the HTML page of its balloon - and in a comment of
  // the prolog is text, and the document is read: only a DOCTYPE the document holds is
  // refused.

  $kmlText = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<!-- written without a <!DOCTYPE kml> -->\n"
           . "<kml xmlns=\"http://www.opengis.net/kml/2.2\"><Placemark><name>Balloon</name>"
           . "<description><![CDATA[<!DOCTYPE html><html><body><b>Hi</b></body></html>]]></description>"
           . "<Point><coordinates>5.12,52.09</coordinates></Point></Placemark></kml>";

  $kmlRow  = padFormatRead ( 'kml', $kmlText ) [0] ?? [];
  $kmlShow = ( $kmlRow ['name'] ?? '' ) . ' at ' . ( $kmlRow ['lat'] ?? '' ) . ',' . ( $kmlRow ['lon'] ?? '' ) . ': ' . ( $kmlRow ['description'] ?? '' );

?>
