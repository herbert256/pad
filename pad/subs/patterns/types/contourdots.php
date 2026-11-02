<?php
// Reuse topography's seeded field and marching-squares paths. Round zero-length dashes
// make stippled contours; the conservative bound counts dots even at cell diagonals.
$spec=include __DIR__.'/topography.php';$draw=$spec['draw'];
$spec['options']['spacing']=[6,2,30];
$spec['count']=['contour dots',100000,fn($p)=>(int)(ceil($p['w']/max(3,min(12,$p['size']/12)))*ceil($p['h']/max(3,min(12,$p['size']/12)))*$p['levels']*(2+3*max(3,min(12,$p['size']/12))/$p['spacing'])),'larger size/spacing, fewer levels or a smaller picture'];
$spec['draw']=function($p)use($draw){[$defs,$body]=$draw($p);$body=str_replace('<path ', '<path stroke-dasharray="0 '.padPatternNum($p['spacing']).'" ', $body);return [$defs,$body];};return $spec;
