<?php

  // A row whose latitude lies past the poles is written without its geometry - on the lenient
  // walk without a word - as the reader refuses such a position: written, it made the whole
  // document unreadable, the good rows lost with it. A longitude of 152 is one.

  $padCheckSyntax = FALSE;

  $kmlText = padFormatWrite ( 'kml', [ [ 'name' => 'Far east', 'lat' => 5.12, 'lon' => 152.09 ],
                                       [ 'name' => 'Bad', 'lat' => 152.09, 'lon' => 5.12 ],
                                       [ 'name' => 'Line', 'geometry' => 'LineString', 'coordinates' => [ [ 5.1, 52.0 ], [ 190.0, 52.1 ] ] ],
                                       [ 'name' => 'Good', 'lat' => 52.09, 'lon' => 5.12 ] ] );

  $kmlBack = implode ( ' ', array_map ( fn ( $row ) => "{$row['name']}={$row['geometry']}", padFormatRead ( 'kml', $kmlText ) ) );

?>
