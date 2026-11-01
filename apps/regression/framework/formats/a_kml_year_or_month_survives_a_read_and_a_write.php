<?php

  // KML's dateTime is a year, a year and a month, a day or a moment: a TimeStamp or a TimeSpan
  // of a year or a month read and written again is the year or the month it was - not a Data
  // field time, nor the first moment of the month.

  $kmlText = '<kml xmlns="http://www.opengis.net/kml/2.2"><Document>'
           . '<Placemark><name>Year</name><TimeStamp><when>1997</when></TimeStamp></Placemark>'
           . '<Placemark><name>Month</name><TimeStamp><when>1997-07</when></TimeStamp></Placemark>'
           . '<Placemark><name>Span</name><TimeSpan><begin>1941-09</begin><end>1943</end></TimeSpan></Placemark>'
           . '<Placemark><name>Day</name><TimeStamp><when>1997-07-16</when></TimeStamp></Placemark>'
           . '</Document></kml>';

  $kmlAgain = padFormatWrite ( 'kml', padFormatRead ( 'kml', $kmlText ) );

  preg_match_all ( '#<(when|begin|end)>([^<]*)|<Data name="([^"]*)"#', $kmlAgain, $kmlMatch, PREG_SET_ORDER );

  $kmlTimes = implode ( ' ', array_map ( fn ( $m ) => ( $m [3] ?? '' ) !== '' ? "data:{$m[3]}" : "{$m[1]}={$m[2]}", $kmlMatch ) );

?>
