<?php
// basketweave - alternating over/under strand bundles in a seamless four-quadrant tile.
// strands= sets the bundle count; gap= leaves space between the strands.
//
//   {pattern:basketweave size=48, strands=3, gap=0.15, colors='#f6f0df, #9b7048, #c29666'/}
return [ 'size'=>[48,8,400], 'colors'=>['#f6f0df','#9b7048','#c29666'],
 'options'=>['strands'=>[3,1,12], 'gap'=>[0.15,0,0.8]],
 'draw'=>function($p) {
   $s=$p['size']; $h=$s/2; $n=(int)$p['strands']; $u=$h/$n; $d='';
   for($row=0;$row<2;$row++) for($col=0;$col<2;$col++) for($i=0;$i<$n;$i++) {
     $horizontal=($row+$col)%2===0;
     $x=$col*$h+($horizontal?0:($i+$p['gap']/2)*$u);
     $y=$row*$h+($horizontal?($i+$p['gap']/2)*$u:0);
     $d.='<rect x="'.padPatternNum($x).'" y="'.padPatternNum($y).'" width="'.padPatternNum($horizontal?$h:$u*(1-$p['gap'])).'" height="'.padPatternNum($horizontal?$u*(1-$p['gap']):$h).'" fill="'.padPatternAttr($p['ink'][($row+$col)%count($p['ink'])]).'"/>';
   }
   $s=padPatternNum($s);
   return ['<pattern id="'.$p['id'].'" width="'.$s.'" height="'.$s.'" patternUnits="userSpaceOnUse">'.$d.'</pattern>',padPatternFill($p,$p['id'])];
 } ];
