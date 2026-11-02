<?php

$map = padUnprotect ( padUnescape ( padCode ( '{map:europe mode="bubbles", latitude="lat", longitude="lon", label="city", value="amount"}lat,lon,city,amount
52.37,4.9,Amsterdam,12{/map:europe}' ) ) );
  $holidays = padUnprotect ( padUnescape ( padCode ( '{holidays:auto countries="nl,de-by", from="2026-01-01", to="2026-01-01"}{$place}:{$date};{/holidays:auto}' ) ) );
  $answer = ( str_contains ( $map, 'pm-marker' ) && str_contains ( $map, 'Amsterdam: 12' ) ? 'bubble map' : 'FAIL' ) . '; ' . trim ( $holidays );

?>
