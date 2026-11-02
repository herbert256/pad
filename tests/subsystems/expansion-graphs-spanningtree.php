<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/graphs.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$nodes=['a','b','c','d'];$edges=[['a','b',5],['a','c',2],['b','c',1],['b','d',4],['c','d',3]];$best=INF;for($mask=0;$mask<32;$mask++){$set=[];$weight=0;foreach($edges as $i=>$e)if($mask&(1<<$i)){$set[]=array_slice($e,0,2);$weight+=$e[2];}if(count($set)===3&&count(padGraph('components',$nodes,$set,['directed'=>false])['components'])===1)$best=min($best,$weight);}$r=padGraph('spanningtree',$nodes,$edges);near($r['weight'],$best);check(count($r['edges'])===3&&$r['connected']);$r=padGraph('spanningtree',$nodes,[['a','b',2]]);check(count($r['components'])===3&&!$r['connected']);rejects(fn()=>padGraph('spanningtree',$nodes,$edges,['directed'=>true]));
echo "graphs:spanningtree fixtures passed\n";
