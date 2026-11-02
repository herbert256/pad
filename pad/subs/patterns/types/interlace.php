<?php
// interlace - two continuous diagonal strand families, alternating over/under at
// every crossing; the 2*size tile joins on all four edges.
return ['size'=>[40,8,400],'colors'=>['#f4eee0','#335c67','#ae6048'],
 'draw'=>function($p){
  $s=$p['size'];$tile=2*$s;$a=padPatternAttr($p['ink'][0]);$b=padPatternAttr($p['ink'][1%count($p['ink'])]);$bg=padPatternAttr($p['bg']);$d='';$line=function($x1,$y1,$x2,$y2,$color,$width){return '<path d="M'.padPatternNum($x1).' '.padPatternNum($y1).'L'.padPatternNum($x2).' '.padPatternNum($y2).'" fill="none" stroke="'.$color.'" stroke-width="'.padPatternNum($width).'"/>';};
  for($j=-2;$j<=4;$j++){$d.=$line($j*$s,0,$j*$s+$tile,$tile,$a,$s*.22);$d.=$line($j*$s,0,$j*$s-$tile,$tile,$b,$s*.22);}
  // Crossings lie on the half-size lattice, with parity choosing the upper strand.
  for($j=0;$j<=4;$j++)for($i=0;$i<=4;$i++)if(($i+$j)%2===0){$x=$i*$s/2;$y=$j*$s/2;$sign=$i%2===0?1:-1;$d.=$line($x-$s*.24,$y-$sign*$s*.24,$x+$s*.24,$y+$sign*$s*.24,$bg,$s*.36);$d.=$line($x-$s*.24,$y-$sign*$s*.24,$x+$s*.24,$y+$sign*$s*.24,$sign===1?$a:$b,$s*.22);}
  return ['<pattern id="'.$p['id'].'" width="'.padPatternNum($tile).'" height="'.padPatternNum($tile).'" patternUnits="userSpaceOnUse">'.$d.'</pattern>',padPatternFill($p,$p['id'])];
 }];
