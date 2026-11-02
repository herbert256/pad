<?php

  // contourdots - Stippled topographic contours reuse the exact topography noise and marching-squares engine. `levels` 2..60, `spacing` 2..30 pixels. This is a finite field, not a seamless tile. The conservative 100,000-dot budget includes cell diagonal length; zero-length round dashes render dots without expanding point markup.
  //
  // {pattern:contourdots seed=17, width=240, height=160, title="contourdots"/}
  //
  // `{pattern:contourdots seed=17, width=240, height=160/}` uses the shared deterministic seed, escaped colours, theme integration and optional accessible `title`.

// Reuse topography's seeded field and marching-squares paths. Round zero-length dashes
// make stippled contours; the conservative bound counts dots even at cell diagonals.
$spec=include __DIR__.'/topography.php';$draw=$spec['draw'];
$spec['options']['spacing']=[6,2,30];
$spec['count']=['contour dots',100000,fn($p)=>(int)(ceil($p['w']/max(3,min(12,$p['size']/12)))*ceil($p['h']/max(3,min(12,$p['size']/12)))*$p['levels']*(2+3*max(3,min(12,$p['size']/12))/$p['spacing'])),'larger size/spacing, fewer levels or a smaller picture'];
$spec['draw']=function($p)use($draw){[$defs,$body]=$draw($p);$body=str_replace('<path ', '<path stroke-dasharray="0 '.padPatternNum($p['spacing']).'" ', $body);return [$defs,$body];};return $spec;
