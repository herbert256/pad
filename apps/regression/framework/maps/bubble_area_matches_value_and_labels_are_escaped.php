<?php

include_once PAD . 'subs/maps/_lib/map.php';
$points = padMapMarkers ( [ [ 'latitude' => 0, 'longitude' => 0, 'label' => '<script>Large</script>', 'n' => 4 ], [ 'latitude' => 10, 'longitude' => 10, 'label' => 'Small', 'n' => 1 ], [ 'latitude' => 20, 'longitude' => 20, 'label' => 'Zero', 'n' => 0 ] ], 'world', 'latitude', 'longitude', 'label', 'n' );
[ $points ] = padMapMarkerView ( $points, 'world' );
$svg = padMapMarkerSvg ( $points, 1 );
$answer = count ( $points ) === 2 && str_contains ( $svg, 'r="18"' ) && str_contains ( $svg, 'r="9"' )
  && str_contains ( $svg, '&lt;script&gt;Large&lt;/script&gt;: 4' ) && ! str_contains ( $svg, '<script>' )
  ? 'bubble areas scale with value and labels stay text' : 'FAIL';

?>
