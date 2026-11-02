<?php
// squircle - a grid of superellipses: exponent=2 gives circles, larger exponents approach
// rounded squares; density= sets each shape's share of the repeat cell.
//
//   {pattern:squircle size=48, exponent=4, density=0.8, colors='#f6f3ef, #568b8c'/}
return [ 'size'=>[48,4,400], 'colors'=>['#f6f3ef','#568b8c'],
 'options'=>['exponent'=>[4,2,10], 'density'=>[0.8,0.1,1]],
 'draw'=>function($p) {
   $s=$p['size']; $a=$s*$p['density']/2; $d='';
   for($i=0;$i<96;$i++) {
     $t=2*M_PI*$i/96; $c=round(cos($t),10); $v=round(sin($t),10);
     $x=$s/2+$a*($c<=>0)*abs($c)**(2/$p['exponent']);
     $y=$s/2+$a*($v<=>0)*abs($v)**(2/$p['exponent']);
     $d.=($i?'L':'M').padPatternNum($x).','.padPatternNum($y);
   }
   $s=padPatternNum($s);
   return ['<pattern id="'.$p['id'].'" width="'.$s.'" height="'.$s.'" patternUnits="userSpaceOnUse"><path d="'.$d.'Z" fill="'.padPatternAttr($p['ink'][0]).'"/></pattern>',padPatternFill($p,$p['id'])];
 } ];
