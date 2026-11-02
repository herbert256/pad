<?php
// Orthogonal traces inside a seamless square tile. Endpoints join at each tile edge.
return ['size'=>[80,16,400],'colors'=>['#0f2626','#6bd5c8'],'options'=>['weight'=>[1.5,.5,6]],
 'count'=>['trace segments',10000,fn($p)=>8,'a smaller output'],
 'draw'=>function($p){$s=$p['size'];$id=$p['id'].'-circuit';$n=fn($v)=>padPatternNum($v);$ink=padPatternAttr($p['ink'][0]);$a=$s*(.2+.1*padPatternRand());$b=$s*(.7+.1*padPatternRand());
 $d='M0 '.$n($s/2).'H'.$n($a).'V'.$n($a).'H'.$n($s/2).'V0M'.$n($s).' '.$n($s/2).'H'.$n($b).'V'.$n($b).'H'.$n($s/2).'V'.$n($s);
 $defs='<pattern id="'.$id.'" width="'.$n($s).'" height="'.$n($s).'" patternUnits="userSpaceOnUse"><path d="'.$d.'" fill="none" stroke="'.$ink.'" stroke-width="'.$n($p['weight']).'"/><g fill="'.$ink.'"><circle cx="'.$n($a).'" cy="'.$n($a).'" r="'.$n($p['weight']*1.5).'"/><circle cx="'.$n($b).'" cy="'.$n($b).'" r="'.$n($p['weight']*1.5).'"/></g></pattern>';
 return [$defs,padPatternFill($p,$id)];}];
