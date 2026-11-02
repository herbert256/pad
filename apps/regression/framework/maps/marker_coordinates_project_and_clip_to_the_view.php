<?php

include_once PAD . 'subs/maps/_lib/map.php';
$rows = [ [ 'lat' => 0, 'lon' => 0, 'city' => '<Origin>' ], [ 'lat' => 52.37, 'lon' => 4.9, 'city' => 'Amsterdam' ], [ 'lat' => -33.87, 'lon' => 151.21, 'city' => 'Sydney' ] ];
$points = padMapMarkers ( $rows, 'world', 'lat', 'lon', 'city' );
[ $world ] = padMapMarkerView ( $points, 'world' );
[ $europe, $outside ] = padMapMarkerView ( $points, 'europe' );
$html = padMap ( 'europe', [], 'Cities', 720, 0, 'linear', $points );
$origin = padMapProject ( 0, 0 );
$answer = abs ( $world [0] ['x'] - 2735.4 ) < 0.000001 && abs ( $world [0] ['y'] - 1422.4 ) < 0.000001
  && count ( $europe ) === 1 && $outside === 2 && $europe [0] ['label'] === 'Amsterdam'
  && substr_count ( $html, 'class="pc-dot pm-marker"' ) === 1 && str_contains ( $html, '2 markers outside this view' )
  ? 'projected coordinates and outside markers accounted for' : 'FAIL';

?>
