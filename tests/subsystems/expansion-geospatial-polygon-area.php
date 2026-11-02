<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/geospatial.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$p=['type'=>'Polygon','coordinates'=>[[[0,0],[1,0],[1,1],[0,1],[0,0]]]];$want=6371008.8**2*deg2rad(1)*sin(deg2rad(1));near(padGeo('polygon-area',$p)['area'],$want);$p['coordinates'][0]=array_reverse($p['coordinates'][0]);near(padGeo('polygon-area',$p)['area'],$want);$p['coordinates'][]=[[.2,.2],[.8,.2],[.8,.8],[.2,.8],[.2,.2]];near(padGeo('polygon-area',$p)['area'],$want-6371008.8**2*deg2rad(.6)*(sin(deg2rad(.8))-sin(deg2rad(.2))));$p=['type'=>'Polygon','coordinates'=>[[[179,0],[-179,0],[-179,1],[179,1],[179,0]]]];near(padGeo('polygon-area',$p,NULL,['unit'=>'km2'])['area'],2*$want/1e6);rejects(fn()=>padGeo('polygon-area',['type'=>'MultiPolygon','coordinates'=>[]]));
echo "geospatial:polygon-area fixtures passed\n";
