<?php
// crosshatch - two perpendicular stroke families in rotated seamless tiles. angle= rotates
// the first family, weight= sets the stroke width, and cross=0 leaves one family.
//
//   {pattern:crosshatch size=16, angle=45, weight=1, colors='#fff, #444'/}
return [ 'size'=>[16,2,400], 'colors'=>['#fff','#444'],
 'options'=>['angle'=>[45,-360,360], 'cross'=>[1,0,1], 'weight'=>[1,0.1,10]],
 'draw'=>function($p) {
   $defs=$body=''; $s=padPatternNum($p['size']);
   for($i=0;$i<($p['cross']>=0.5?2:1);$i++) {
     $id=$p['id'].'-'.$i;
     $defs.='<pattern id="'.$id.'" width="'.$s.'" height="'.$s.'" patternUnits="userSpaceOnUse" patternTransform="rotate('.padPatternNum($p['angle']+90*$i).')"><path d="M'.padPatternNum($p['size']/2).' 0V'.$s.'" fill="none" stroke="'.padPatternAttr($p['ink'][$i%count($p['ink'])]).'" stroke-width="'.padPatternNum(min($p['size'],$p['weight'])).'"/></pattern>';
     $body.=padPatternFill($p,$id);
   }
   return [$defs,$body];
 } ];
