<?php
define('PAD',dirname(__DIR__,2).'/pad/');
function padDirList($p){return scandir($p);}
function padError($m){throw new InvalidArgumentException($m);}
require PAD.'lib/subsystem.php';
require PAD.'lib/statistics.php';
function check($v){if(!$v)throw new RuntimeException('fixture failed');}
function near($a,$b,$e=1e-10){check(abs($a-$b)<=$e*max(1,abs($b)));}
function rejects($f){try{$f();}catch(InvalidArgumentException $e){return;}throw new RuntimeException('invalid input accepted');}
$rows=[['value'=>1,'weight'=>2],['value'=>4,'weight'=>1],['value'=>100,'weight'=>0]];$r=padStats('weighted',$rows);near($r['mean'],2);near($r['variance'],3);check($r['zeroWeights']===1);$r=padStats('weighted',array_slice($rows,0,2),['semantics'=>'reliability']);near($r['variance'],4.5);near($r['effectiveCount'],1.8);check(padStats('weighted',[['value'=>3,'weight'=>0]])['mean']===NULL);rejects(fn()=>padStats('weighted',[['value'=>3,'weight'=>-.5]]));rejects(fn()=>padStats('weighted',[['value'=>3,'weight'=>.5]]));near(padStats('weighted',[['value'=>1e12+1,'weight'=>2],['value'=>1e12+4,'weight'=>1]])['variance'],3);
echo "statistics:weighted fixtures passed\n";
