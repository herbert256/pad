<?php
$svg='';
for($y=0;$y<5;$y++) for($x=0;$x<5;$x++) {
 $n=$y*5+$x;$a=$palette[hexdec($hash[$n])%count($palette)];$b=$palette[hexdec($hash[$n+25])%count($palette)];
 $px=$x*20;$py=$y*20;
 $svg.='<rect x="'.$px.'" y="'.$py.'" width="20" height="20" fill="'.$a.'"/><path d="M'.$px.','.$py.'h20v20z" fill="'.$b.'"/>';
}
return $svg;
?>
