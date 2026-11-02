<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/graphs.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$nodes=['a','b','c'];$possible=[[0,1],[0,2],[1,0],[1,2],[2,0],[2,1]];for($mask=0;$mask<64;$mask++){$reach=[[true,false,false],[false,true,false],[false,false,true]];$edges=[];foreach($possible as $bit=>[$a,$b])if($mask&(1<<$bit)){$reach[$a][$b]=true;$edges[]=[$nodes[$a],$nodes[$b]];}for($k=0;$k<3;$k++)for($i=0;$i<3;$i++)for($j=0;$j<3;$j++)$reach[$i][$j]=$reach[$i][$j]||($reach[$i][$k]&&$reach[$k][$j]);$r=padGraph('strongcomponents',$nodes,$edges);$owner=[];foreach($r['components'] as $c=>$group)foreach($group as $id)$owner[$id]=$c;for($i=0;$i<3;$i++)for($j=0;$j<3;$j++)check(($owner[$nodes[$i]]===$owner[$nodes[$j]])===($reach[$i][$j]&&$reach[$j][$i]));$dag=$r['condensation'];check(padGraph('toposort',array_map('strval',$dag['nodes']),array_map(fn($e)=>array_map('strval',$e),$dag['edges']))['status']==='ok');}
echo "graphs:strongcomponents fixtures passed\n";
