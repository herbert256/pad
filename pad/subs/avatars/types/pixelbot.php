<?php
$color=$palette[hexdec($hash[0])%count($palette)];
$svg='<rect width="100" height="100" fill="#f5f1e3"/><path d="M45 8h10v12H45zM20 25h60v55H20zM10 40h10v25H10zM80 40h10v25H80z" fill="'.$color.'"/>';
$eye=6+hexdec($hash[1])%7;
$svg.='<rect x="30" y="40" width="'.$eye.'" height="10" fill="#111111"/><rect x="'.(70-$eye).'" y="40" width="'.$eye.'" height="10" fill="#111111"/>';
for($i=0;$i<4;$i++) if(hexdec($hash[$i+2])%2 || $i===0) $svg.='<rect x="'.(30+$i*10).'" y="65" width="8" height="5" fill="#111111"/>';
return $svg;
?>
