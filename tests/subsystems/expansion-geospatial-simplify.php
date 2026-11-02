<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/geospatial.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$p=['type'=>'LineString','coordinates'=>[[179,0],[180,0],[-179,0]]];$r=padGeo('simplify',$p,NULL,['tolerance'=>1]);check(count($r['coordinates'])===2&&$r['coordinates'][0]===[179.0,0.0]&&$r['coordinates'][1]===[-179.0,0.0]);$p=['type'=>'LineString','coordinates'=>[[0,0],[1,1],[2,0]]];check(count(padGeo('simplify',$p,NULL,['tolerance'=>1000])['coordinates'])===3);check(count(padGeo('simplify',$p,NULL,['tolerance'=>120000])['coordinates'])===2);$p=['type'=>'LineString','coordinates'=>[[0,89],[0,90],[180,89]]];check(count(padGeo('simplify',$p,NULL,['tolerance'=>1])['coordinates'])===2);rejects(fn()=>padGeo('simplify',['type'=>'LineString','coordinates'=>[[0,0],[90,0],[180,0]]],NULL,['tolerance'=>1]));
echo "geospatial:simplify fixtures passed\n";
