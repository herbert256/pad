<?php
include_once PAD.'subs/patterns/_lib/penrose.php';
return ['size'=>[70,12,400],'colors'=>['#f7efe2','#3b6e76','#d8aa62'],
 'count'=>['triangles',50000,fn($p)=>padPenroseCount(padPenroseDepth($p)),'a larger size or smaller canvas'],
 'draw'=>function($p){$body='';$triangles=padPenroseTriangles(padPenroseDepth($p),hypot($p['w'],$p['h'])*.65,padPatternRand()*2*M_PI);
  foreach($triangles as [$kind,$A,$B,$C]){$points=[];foreach([$A,$B,$C] as [$x,$y])$points[]=padPatternNum($x+$p['w']/2).','.padPatternNum($y+$p['h']/2);$body.='<polygon points="'.implode(' ',$points).'" fill="'.padPatternAttr($p['ink'][$kind%count($p['ink'])]).'" stroke="'.padPatternAttr($p['bg']).'" stroke-width="0.5"/>';}
  return ['',$body];
 }];
