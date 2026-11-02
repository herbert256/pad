<?php

  // rings - Concentric coloured arcs derived deterministically from an identity.
  //

$svg = '<rect width="100" height="100" rx="50" fill="'.$palette[0].'"/>';
for ($i=0;$i<5;$i++) {
  $r=44-$i*8; $color=$palette[hexdec($hash[$i])%count($palette)];
  $gap=15+hexdec($hash[$i+8])*5; $angle=hexdec(substr($hash,$i*2+16,2));
  $svg.='<circle cx="50" cy="50" r="'.$r.'" fill="none" stroke="'.$color.'" stroke-width="5" pathLength="100" stroke-dasharray="'.(100-$gap).' '.$gap.'" transform="rotate('.$angle.' 50 50)"/>';
}
return $svg;
?>
