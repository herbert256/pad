<?php
// Robinson-triangle deflation of the ten-triangle sun. A finite crop, not a
// seamless repeated tile. Reference: Bettencourt, Penrose Aperiodic Tiling (2015).
function padPenroseDepth($p){return max(0,(int)ceil(log(max(1,hypot($p['w'],$p['h'])*.65/$p['size']))/log((1+sqrt(5))/2)));}
function padPenroseCount($depth){$thin=10;$thick=0;for($i=0;$i<$depth;$i++)[$thin,$thick]=[$thin+$thick,$thin+2*$thick];return $thin+$thick;}
function padPenroseTriangles($depth,$radius,$angle=0){
 $phi=(1+sqrt(5))/2;$triangles=[];$mix=fn($a,$b)=>[$a[0]+($b[0]-$a[0])/$phi,$a[1]+($b[1]-$a[1])/$phi];
 for($i=0;$i<10;$i++){$a=(2*$i-1)*M_PI/10+$angle;$b=(2*$i+1)*M_PI/10+$angle;$B=[$radius*round(cos($a),10),$radius*round(sin($a),10)];$C=[$radius*round(cos($b),10),$radius*round(sin($b),10)];if($i%2===0)[$B,$C]=[$C,$B];$triangles[]=[0,[0,0],$B,$C];}
 for($i=0;$i<$depth;$i++){$next=[];foreach($triangles as [$kind,$A,$B,$C])if($kind===0){$P=$mix($A,$B);$next[]=[0,$C,$P,$B];$next[]=[1,$P,$C,$A];}else{$Q=$mix($B,$A);$R=$mix($B,$C);$next[]=[1,$R,$C,$A];$next[]=[1,$Q,$R,$B];$next[]=[0,$R,$Q,$A];}$triangles=$next;}
 return $triangles;
}
